<?php
/**
 * @package    Eshoper/LPShipping/Model/ResourceModel
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model\ResourceModel;

class LPTableRates extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Magento\Framework\Filesystem $_fileSystem
     */
    private $_fileSystem;

    /**
     * @var \Eshoper\LPShipping\Model\LPTableRatesFactory $_LPTableRatesFactory;
     */
    private $_LPTableRatesFactory;

    /**
     * LPTableRates constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Eshoper\LPShipping\Model\LPTableRatesFactory $LPTableRatesFactory
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Eshoper\LPShipping\Model\LPTableRatesFactory $LPTableRatesFactory,
        $connectionName = null
    ) {
        $this->_fileSystem = $filesystem;
        $this->_LPTableRatesFactory = $LPTableRatesFactory;

        parent::__construct($context, $connectionName);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init ( 'lp_table_rates', 'id' );
    }

    /**
     * Upload csv file and import to main table
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function uploadAndImport ( \Magento\Framework\DataObject $object )
    {
        $tmpPath = $_FILES [ 'groups' ][ 'tmp_name' ][ 'lpcarrier' ]['groups']['lpcarriershipping_lp']
                        [ 'fields' ][ 'import_rates' ][ 'value' ];
        /**
         * @var \Magento\Framework\App\Config\Value $object
         */
        if ( empty ( $tmpPath ) ) {
            return $this;
        }

        $file = $this->getCsvFile ( $tmpPath );
        $data = $this->getData ( $file );

        if ( ! empty ( $data ) && $this->validate ( $data ) ) {
            // Truncate data
            $this->deleteByCondition ( [] );

            foreach ( $data as $rate ) {
                /** @var \Eshoper\LPShipping\Model\LPTableRates $rates */
                $this->_LPTableRatesFactory->create ()
                    ->setWeightTo ( $rate [ 'weight_to' ] )
                    ->setPostOfficePrice ( $rate [ 'lp_postoffice_price' ] )
                    ->save ();
            }
        }
    }

    /**
     * Delete from main table by condition
     *
     * @param array $condition
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function deleteByCondition ( array $condition )
    {
        $connection = $this->getConnection();
        $connection->beginTransaction();
        $connection->delete($this->getMainTable(), $condition);
        $connection->commit();
        return $this;
    }

    /**
     * Open for reading csv file
     *
     * @param $filePath
     * @return \Magento\Framework\Filesystem\File\ReadInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function getCsvFile ( $filePath )
    {
        $pathInfo = pathinfo ( $filePath );
        $dirName = isset ( $pathInfo['dirname'] ) ? $pathInfo [ 'dirname' ] : '';
        $fileName = isset ( $pathInfo['basename'] ) ? $pathInfo [ 'basename' ] : '';

        $directoryRead = $this->_fileSystem->getDirectoryReadByPath ( $dirName );

        return $directoryRead->openFile ( $fileName );
    }

    /**
     * Get shipping rates by presented weight and method
     *
     * @param string $quoteWeight
     * @param string $shippingMethod
     * @return string | bool
     */
    public function getRate ( $quoteWeight, $shippingMethod )
    {
        $weights = $result = [];
        $rates = $this->_LPTableRatesFactory->create ()->getCollection ()->getData ();

        for ( $i = count ( $rates ) - 1; $i >= 0; $i-- ) {
            // Search for weight that fits
            if ( $rates [ $i ]['weight_to'] >= $quoteWeight  ) {
                $weights [ $i ] = $rates [ $i ][ 'weight_to' ];
            }
        }

        if ( ! empty ( $weights ) ) {
            // Result is the minimum weight index
            $result = $rates [ array_search ( min ( $weights ), $weights ) ];
            return $result [ $shippingMethod . '_price' ];
        }

        return false;
    }

    /**
     * Validate CSV file format
     *
     * @param $data
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function validate ( $data )
    {
        $values = [];

        foreach ( $data as $rate ) {
            if ( $rate [ 'weight_to' ] === null || $rate [ 'weight_to' ] === '' ) {
                throw new \Magento\Framework\Exception\LocalizedException (
                    __( 'Weight cell in the csv file cannot be empty.' )
                );
            }
            array_push ( $values, $rate [ 'weight_to' ] );
        }

        // Check if there is any duplicates
        if ( count ( array_unique ( $values ) ) < count ( $data ) ) {
            throw new \Magento\Framework\Exception\LocalizedException (
                __( 'Please check for weight duplicates in the csv file.' )
            );
        }

        return true;
    }

    /**
     * Get data from csv file
     * @param \Magento\Framework\Filesystem\File\ReadInterface $file
     * @return array
     */
    private function getData ( \Magento\Framework\Filesystem\File\ReadInterface $file )
    {
        $data = [];

        while ( false !== ( $csvLine = $file->readCsv() ) ) {
            if ( @$line++ == 0 ) continue; // Skip first line

            /**
             * Columns
             * 0 - weight_to
             * 1 - lp_postoffice_price
             * 2 - lp_overseas_price
             * 3 - lpexpress_courier_price
             * 4 - lpexpress_terminal_price
             * 5 - lpexpress_postoffice_price
             */
            array_push (
                $data,
                [
                    'weight_to' => $csvLine [ 0 ],
                    'lp_postoffice_price' => $csvLine [ 1 ],
                ]
            );
        }

        return $data;
    }
}

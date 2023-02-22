<?php
/**
 * @package    Eshoper/LPShipping/Model/ResourceModel
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model\ResourceModel;

class LPExpressCountryRatesWeight extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;

    /**
     * @var \Eshoper\LPShipping\Model\LPCountryRates
     */
    protected $_LPExpressCountryRatesWeightFactory;

    /**
     * Rates constructor.
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Eshoper\LPShipping\Model\LPCountryRates $LPExpressCountryRatesWeightFactory
     * @param null $connectionName
     */
    public function __construct (
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Eshoper\LPShipping\Model\LPExpressCountryRatesWeightFactory $LPExpressCountryRatesWeightFactory,
        $connectionName = null
    ) {
        $this->_fileSystem = $filesystem;
        $this->_LPExpressCountryRatesWeightFactory = $LPExpressCountryRatesWeightFactory;

        parent::__construct($context, $connectionName);
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init ( 'lpexpress_overseas_rates_weight', 'id' );
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
        $tmpPath = $_FILES [ 'groups' ][ 'tmp_name' ][ 'lpcarrier' ]['groups']['lpcarriershipping_lpexpress']
                        [ 'fields' ][ 'import_country_weight' ][ 'value' ];
        /**
         * @var \Magento\Framework\App\Config\Value $object
         */
        if ( empty ( $tmpPath ) ) {
            return $this;
        }

        $file = $this->getCsvFile ( $tmpPath );
        $data = $this->getData ( $file );

        if ( ! empty ( $data ) ) {
            // Truncate data
            $this->deleteByCondition ( [] );

            foreach ( $data as $rate ) {
                /** @var \Eshoper\LPShipping\Model\LPExpressCountryRatesWeight $rates */
                $this->_LPExpressCountryRatesWeightFactory->create ()
                    ->setCountryId ( $rate [ 'country_id' ] )
                    ->setPrice ( $rate [ 'price' ] )
                    ->setWeight ( $rate [ 'weight' ] )
                    ->save ();
            }
        }
    }

    /**
     * Get shipping rates by presented country and price
     *
     * @param string $countryId
     * @return string | bool
     */
    public function getRate ( $countryId, $weight )
    {
        $weights   = [];
        $rates     = $this->_LPExpressCountryRatesWeightFactory->create ()->getCollection ()
            ->addFieldToFilter ( 'country_id', $countryId );

        $ratesArray = $rates->getData ();

        for ( $i = count ( $ratesArray ) - 1; $i >= 0; $i-- ) {
            // Search for weight that fits
            if ( $ratesArray [ $i ]['weight'] >= $weight  ) {
                $weights [ $i ] = $ratesArray [ $i ][ 'weight' ];
            }
        }

        if ( ! empty ( $weights ) ) {
            // Result is the minimum weight index
            $result = $ratesArray [ array_search ( min ( $weights ), $weights ) ];
            return $result [ 'price' ];
        }

        return false;
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
     * Get data from csv file
     *
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
             * 0 - country_id
             * 1 - price
             */
            array_push (
                $data,
                [
                    'country_id' => $csvLine [ 0 ],
                    'price' => $csvLine [ 1 ],
                    'weight' => $csvLine [ 2 ]
                ]
            );
        }

        return $data;
    }
}

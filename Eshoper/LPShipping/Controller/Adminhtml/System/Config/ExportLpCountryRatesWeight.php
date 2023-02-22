<?php
/**
 * @package    Eshoper/LPShipping/Controller/Adminhtml/System/Config
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Controller\Adminhtml\System\Config;

class ExportLpCountryRatesWeight extends \Magento\Config\Controller\Adminhtml\System\AbstractConfig
{
    /**
     * File response
     *
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * ExportLptables constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Config\Model\Config\Structure $configStructure
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Config\Model\Config\Structure $configStructure,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->_fileFactory = $fileFactory;

        parent::__construct ( $context, $configStructure, null );
    }

    /**
     * Export shipping table rates in csv format
     *
     * @return \Magento\Framework\App\ResponseInterface | \Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $fileName = 'lp-country-rates-weight.csv';

        /** @var $gridBlock \Eshoper\LPShipping\Block\Adminhtml\Carrier\LPCountryRatesWeight\Grid */
        $gridBlock = $this->_view->getLayout ()->createBlock (
            \Eshoper\LPShipping\Block\Adminhtml\Carrier\LPCountryRatesWeight\Grid::class
        );

        /**
         * Return CSV file from grid
         */
        return $this->_fileFactory->create ( $fileName, $gridBlock->getCsvFile () ,
            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR );
    }
}

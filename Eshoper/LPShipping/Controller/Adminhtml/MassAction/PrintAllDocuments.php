<?php
/**
 * @package    Eshoper/LPShipping/Controller/Adminhtml/MassAction
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Controller\Adminhtml\MassAction;

class PrintAllDocuments extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Ui\Component\MassAction\Filter $_filter
     */
    protected $_filter;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $_orderCollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var \Eshoper\LPShipping\Model\Config $_config
     */
    protected $_config;

    /**
     * @var \Eshoper\LPShipping\Helper\ApiHelper
     */
    protected $_apiHelper;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface $_orderRepository
     */
    protected $_orderRepository;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory $_fileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory $_resultRawFactory
     */
    protected $_resultRawFactory;

    /**
     * PrintManifests constructor.
     * @param \Eshoper\LPShipping\Model\Config $config
     * @param \Eshoper\LPShipping\Helper\ApiHelper $apiHelper
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct (
        \Eshoper\LPShipping\Model\Config $config,
        \Eshoper\LPShipping\Helper\ApiHelper $apiHelper,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_filter                  = $filter;
        $this->_orderCollectionFactory  = $orderCollectionFactory;
        $this->_config                  = $config;
        $this->_apiHelper               = $apiHelper;
        $this->_orderRepository         = $orderRepository;
        $this->_fileFactory             = $fileFactory;
        $this->_resultRawFactory        = $resultRawFactory;
        parent::__construct ( $context );
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $orderCollection = $this->_filter->getCollection ( $this->_orderCollectionFactory->create () );
            $merged = new \Zend_Pdf ();

            /** @var \Magento\Sales\Model\Order $order */
            foreach ( $orderCollection->getItems () as $order ) {
                // Sticker
                try {
                    if ( $stickerData = $this->_apiHelper->createSticker ( $order->getLpShippingItemId () ) ) {
                        $loadDoc = new \Zend_Pdf ( base64_decode ( $stickerData ), null, false );

                        foreach ( $loadDoc->pages as $page ) {
                            $clonedPage = clone $page;
                            $merged->pages [] = $clonedPage;
                        }
                    }
                } catch ( \Exception $e ) {}

                // Manifest
                try {
                    if ( $manifestData = $this->_apiHelper->getManifest ( $order->getLpCartId () ) ) {
                        $loadDoc = new \Zend_Pdf ( base64_decode ( $manifestData ), null, false );

                        foreach ( $loadDoc->pages as $page ) {
                            $clonedPage = clone $page;
                            $merged->pages [] = $clonedPage;
                        }
                    }
                } catch ( \Exception $e ) {}

                // CN23Form
                try {
                    if ( $CN23FormData = $this->_apiHelper->getCN23Form ( $order->getLpCartId () ) ) {
                        $loadDoc = new \Zend_Pdf ( base64_decode ( $CN23FormData ), null, false );

                        foreach ( $loadDoc->pages as $page ) {
                            $clonedPage = clone $page;
                            $merged->pages [] = $clonedPage;
                        }
                    }
                } catch ( \Exception $e ) {}
            }

            $this->_fileFactory->create (
                sprintf ('LP_Documents_%s.pdf', date ( 'Ymd' ) ),
                $merged->render (),
                \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                'application/pdf',
                ''
            );
        } catch ( \Exception $e ) {
            $this->messageManager->addErrorMessage ( $e->getMessage () );
            return $this->_redirect ( $this->_redirect->getRefererUrl () );
        }

        return $this->_resultRawFactory->create ();
    }
}

<?php
/**
 * @package    Eshoper/LPShipping/Controller/Adminhtml/Action
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Controller\Adminhtml\Action;

class PrintAllDocuments extends \Magento\Backend\App\Action
{
    /**
     * @var \Eshoper\LPShipping\Helper\ApiHelper $_apiHelper
     */
    protected $_apiHelper;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory $_resultRawFactory
     */
    protected $_resultRawFactory;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory $_fileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface $_orderRepository
     */
    protected $_orderRepository;

    /**
     * PrintAllDocuments constructor.
     * @param \Eshoper\LPShipping\Helper\ApiHelper $apiHelper
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct (
        \Eshoper\LPShipping\Helper\ApiHelper $apiHelper,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_apiHelper           = $apiHelper;
        $this->_resultRawFactory    = $resultRawFactory;
        $this->_fileFactory         = $fileFactory;
        $this->_orderRepository     = $orderRepository;

        parent::__construct ( $context );
    }

    /**
     * @inheritDoc
     */
    public function execute ()
    {
        $merged = new \Zend_Pdf ();
        $order = $this->_orderRepository->get ( $this->getRequest ()->getParam ( 'order' ) );

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


        $this->_fileFactory->create (
            sprintf ('LP_Documents_%s_%s.pdf', $order->getIncrementId (), date ( 'Ymd' ) ),
            $merged->render (),
            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
            'application/pdf',
            ''
        );
    }
}

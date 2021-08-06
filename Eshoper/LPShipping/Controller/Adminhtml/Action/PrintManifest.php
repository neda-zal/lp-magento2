<?php
/**
 * @package    Eshoper/LPShipping/Controller/Adminhtml/Action
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Controller\Adminhtml\Action;

class PrintManifest extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory $_resultRawFactory
     */
    protected $_resultRawFactory;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory $_fileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Eshoper\LPShipping\Helper\ApiHelper $_apiHelper
     */
    protected $_apiHelper;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface $_orderRepository
     */
    protected $_orderRepository;

    /**
     * PrintManifest constructor.
     * @param \Eshoper\LPShipping\Helper\ApiHelper $apiHelper
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Eshoper\LPShipping\Helper\ApiHelper $apiHelper,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_resultRawFactory = $resultRawFactory;
        $this->_fileFactory = $fileFactory;
        $this->_apiHelper = $apiHelper;
        $this->_orderRepository = $orderRepository;

        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute ()
    {
        $order = $this->_orderRepository->get ( $this->getRequest ()->getParam ( 'order' ) );
        $fileName = sprintf ( 'LP_Manifest_%s.pdf', $order->getIncrementId () );

        if ( $manifestData = $this->_apiHelper->getManifest ( $order->getLpCartId () ) ) {
            $this->_fileFactory->create (
                $fileName,
                base64_decode ( $manifestData ),
                \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                'application/pdf',
                ''
            );
        } else {
            $this->messageManager->addErrorMessage(
                'Manifest does not exist.'
            );

            return $this->_redirect ( $this->_redirect->getRefererUrl () );
        }

        return $this->_resultRawFactory->create ();
    }
}

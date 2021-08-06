<?php
/**
 * @package    Eshoper/LPShipping/Controller/Adminhtml/Action
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Controller\Adminhtml\Action;


use Magento\Framework\App\ResponseInterface;

class PrintCN23Form extends \Magento\Backend\App\Action
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
     * LpPrintCN23Form constructor.
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Eshoper\LPShipping\Helper\ApiHelper $apiHelper
     */
    public function __construct(
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Backend\App\Action\Context $context,
        \Eshoper\LPShipping\Helper\ApiHelper $apiHelper
    ) {
        parent::__construct($context);
        $this->_resultRawFactory = $resultRawFactory;
        $this->_fileFactory = $fileFactory;
        $this->_apiHelper = $apiHelper;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $orderRepository = $objectManager->create('Magento\Sales\Api\OrderRepositoryInterface');

            /** @var \Magento\Sales\Model\Order $order */
            $order = $orderRepository->get ( $this->getRequest ()->getParam ( 'order' ) );

            $fileName = sprintf ( 'LP_CN23_Form_%s.pdf', $order->getIncrementId () );

            if ( $CN23FormData = $this->_apiHelper->getCN23Form ( $order->getLpCartId () ) ) {
                $this->_fileFactory->create (
                    $fileName,
                    base64_decode ( $CN23FormData ),
                    \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                    'application/pdf',
                    ''
                );
            } else {
                return $this->_redirect ( $this->_redirect->getRefererUrl () );
            }
        } catch ( \Exception $e ) {
            $this->messageManager->addErrorMessage ( $e->getMessage () );
            return $this->_redirect ( $this->_redirect->getRefererUrl () );
        }


        $resultRaw = $this->_resultRawFactory->create ();
        return $resultRaw;
    }
}

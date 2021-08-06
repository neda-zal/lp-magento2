<?php
/**
 * @package    Eshoper/LPShipping/Controller/Adminhtml/MassAction
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Controller\Adminhtml\MassAction;

class CallCourier extends \Magento\Backend\App\Action
{
    /**
     * @var \Eshoper\LPShipping\Helper\ApiHelper $_apiHelper
     */
    protected $_apiHelper;

    /**
     * @var \Magento\Ui\Component\MassAction\Filter $_filter
     */
    protected $_filter;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $_orderCollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface $_orderRepository
     */
    protected $_orderRepository;

    /**
     * @var \Eshoper\LPShipping\Model\Config $_config
     */
    protected $_config;

    /**
     * CallCourier constructor.
     * @param \Eshoper\LPShipping\Helper\ApiHelper $apiHelper
     * @param \Eshoper\LPShipping\Model\Config $config
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct (
        \Eshoper\LPShipping\Helper\ApiHelper $apiHelper,
        \Eshoper\LPShipping\Model\Config  $config,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_apiHelper               = $apiHelper;
        $this->_config                  = $config;
        $this->_filter                  = $filter;
        $this->_orderCollectionFactory  = $orderCollectionFactory;
        $this->_orderRepository         = $orderRepository;
        parent::__construct ( $context );
    }

    /**
     * @inheritDoc
     */
    public function execute ()
    {
        try {
            $shippingItemIds = [];
            $orderCollection = $this->_filter->getCollection ( $this->_orderCollectionFactory->create () );

            /** @var \Magento\Sales\Model\Order $order */
            foreach ( $orderCollection->getItems () as $order ) {
                $shippingItemIds [] = $order->getLpShippingItemId ();
            }

            if ( !$this->_config->isCallCourierAllowed ( $order )
                || !$this->_apiHelper->callCourier ( $shippingItemIds ) ) {
                $this->messageManager->addErrorMessage (
                    sprintf ( '%s %s', __( 'Could not call courier for order' ),
                        $order->getIncrementId () )
                );
            } else {
                foreach ( $orderCollection->getItems () as $order ) {
                    $order->setStatus ( $this->_config::COURIER_CALLED_STATUS );
                    $this->_orderRepository->save ( $order );
                }
            }

            $this->messageManager->addSuccessMessage (
                __( 'Call Courier action complete' )
            );
        } catch ( \Exception $e ) {
            $this->messageManager->addErrorMessage (
                $e->getMessage ()
            );
        }

        $resultRedirect = $this->resultFactory->create ( $this->resultFactory::TYPE_REDIRECT );
        return $resultRedirect->setPath ( $this->_redirect->getRefererUrl () );
    }
}

<?php
/**
 * @package    Eshoper/LPShipping/Controller/Adminhtml/MassAction
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Controller\Adminhtml\MassAction;

class CancelShippingLabels extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Sales\Api\ShipmentTrackRepositoryInterface $_shipmentTrackRepository
     */
    protected $_shipmentTrackRepository;

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
     * CancelShippingLabels constructor.
     * @param \Eshoper\LPShipping\Model\Config $config
     * @param \Eshoper\LPShipping\Helper\ApiHelper $apiHelper
     * @param \Magento\Sales\Api\ShipmentTrackRepositoryInterface $shipmentTrackRepository
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct (
        \Eshoper\LPShipping\Model\Config $config,
        \Eshoper\LPShipping\Helper\ApiHelper $apiHelper,
        \Magento\Sales\Api\ShipmentTrackRepositoryInterface $shipmentTrackRepository,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_shipmentTrackRepository = $shipmentTrackRepository;
        $this->_filter                  = $filter;
        $this->_orderCollectionFactory  = $orderCollectionFactory;
        $this->_config                  = $config;
        $this->_apiHelper               = $apiHelper;
        $this->_orderRepository         = $orderRepository;
        parent::__construct ( $context );
    }

    /**
     * Delete tracking info
     *
     * @param \Magento\Sales\Model\Order\Shipment $orderShipment
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    protected function deleteTracks ( \Magento\Sales\Model\Order\Shipment $orderShipment )
    {
        foreach ( $orderShipment->getTracks () as $track )
        {
            $this->_shipmentTrackRepository->delete ( $track );
        }
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute ()
    {
        try {
            $orderCollection = $this->_filter->getCollection ( $this->_orderCollectionFactory->create () );

            /** @var \Magento\Sales\Model\Order $order */
            foreach ( $orderCollection->getItems () as $order ) {
                if ( $shippingItemId = $order->getLpShippingItemId () ) {
                    if ( $this->_apiHelper->cancelLabel ( $shippingItemId ) ) {
                        $this->deleteTracks ( $order->getShipmentsCollection ()->getFirstItem () );
                        $order->setLpCartId ( null );
                        $order->setLpShippingItemId ( null );
                        $order->setStatus ( 'processing' );
                        $this->_orderRepository->save ( $order );
                    } else {
                        $this->messageManager->addErrorMessage (
                            sprintf ( '%s %s', __( 'Could not cancel label for order' ), $order->getIncrementId () )
                        );
                    }
                }
            }

            $this->messageManager->addSuccessMessage ( __( 'You have canceled the shipping labels.' ) );
        } catch ( \Exception $e ) {
            $this->messageManager->addErrorMessage (
                $e->getMessage ()
            );
        }

        $resultRedirect = $this->resultFactory->create ( $this->resultFactory::TYPE_REDIRECT );
        return $resultRedirect->setPath ( $this->_redirect->getRefererUrl() );
    }
}

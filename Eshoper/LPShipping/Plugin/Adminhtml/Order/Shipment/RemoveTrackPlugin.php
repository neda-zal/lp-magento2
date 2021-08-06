<?php
/**
 * @package    Eshoper/LPShipping/Plugin/Adminhtml/Order/Shipment
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Plugin\Adminhtml\Order\Shipment;

class RemoveTrackPlugin
{
    /**
     * @var \Eshoper\LPShipping\Helper\ApiHelper $_apiHelper
     */
    protected $_apiHelper;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface $_orderRepository
     */
    protected $_orderRepository;

    /**
     * @var \Magento\Sales\Api\ShipmentRepositoryInterface $_shipmentRepository
     */
    protected $_shipmentRepository;

    /**
     * RemoveTrackPlugin constructor.
     * @param \Eshoper\LPShipping\Helper\ApiHelper $apiHelper
     * @param \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        \Eshoper\LPShipping\Helper\ApiHelper $apiHelper,
        \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->_apiHelper           = $apiHelper;
        $this->_shipmentRepository  = $shipmentRepository;
        $this->_orderRepository     = $orderRepository;
    }

    /**
     * Cancel label on track delete
     * @param \Magento\Shipping\Controller\Adminhtml\Order\Shipment\RemoveTrack $subject
     */
    public function beforeExecute (
        \Magento\Shipping\Controller\Adminhtml\Order\Shipment\RemoveTrack $subject
    ) {
        $shipment = $this->_shipmentRepository->get ( $subject->getRequest ()->getParam ( 'shipment_id' ) );
        $order = $this->_orderRepository->get ( $shipment->getOrderId () );

        if ( $shippingItemId = $order->getLpShippingItemId () ) {
            if ( $this->_apiHelper->cancelLabel ( $shippingItemId ) ) {
                $order->setLpCartId ( null );
                $order->setLpShippingItemId ( null );
                $order->setStatus ( 'processing' );
                $this->_orderRepository->save ( $order );
            } else {
                die ( '<b>Could not cancel shipping label. Please try to refresh this page and try again.</b>' );
            }
        }
    }
}

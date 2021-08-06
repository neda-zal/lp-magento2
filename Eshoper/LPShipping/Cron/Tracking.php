<?php
/**
 * @package    Eshoper/LPShipping/Cron
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Cron;


use Magento\Sales\Api\ShipmentTrackRepositoryInterface;

class Tracking
{
    /**
     * @var \Eshoper\LPShipping\Helper\ApiHelper $_apiHelper
     */
    protected $_apiHelper;

    /**
     * @var \Eshoper\LPShipping\Api\Data\TrackingInterfaceFactory $_tracking
     */
    protected $_tracking;

    /**
     * @var \Eshoper\LPShipping\Api\TrackingRepositoryInterface $_trackingRepository
     */
    protected $_trackingRepository;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface $_orderRepository
     */
    protected $_orderRepository;

    /**
     * @var \Magento\Sales\Api\ShipmentRepositoryInterface $_shipmentRepository
     */
    protected $_shipmentRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder $_searchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * Tracking constructor.
     * @param \Eshoper\LPShipping\Helper\ApiHelper $apiHelper
     * @param \Eshoper\LPShipping\Api\Data\TrackingInterfaceFactory $tracking
     * @param \Eshoper\LPShipping\Api\TrackingRepositoryInterface $trackingRepository
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct (
        \Eshoper\LPShipping\Helper\ApiHelper $apiHelper,
        \Eshoper\LPShipping\Api\Data\TrackingInterfaceFactory $tracking,
        \Eshoper\LPShipping\Api\TrackingRepositoryInterface $trackingRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->_apiHelper               = $apiHelper;
        $this->_tracking                = $tracking;
        $this->_trackingRepository      = $trackingRepository;
        $this->_orderRepository         = $orderRepository;
        $this->_searchCriteriaBuilder   = $searchCriteriaBuilder;
        $this->_shipmentRepository      = $shipmentRepository;
    }

    /**
     * Get all orders that has lp_shipping_item_id not null
     * So it will get only those shipments that are initiated
     * and not updated last 8 hours
     * @return \Magento\Sales\Api\Data\OrderInterface[]
     */
    protected function getOrders ()
    {
        $criteria = $this->_searchCriteriaBuilder
            ->addFilter ( 'lp_shipping_item_id', null, 'neq' )
            ->addFilter ( 'lp_shipment_tracking_updated',
                date ( 'Y-m-d H:i:s', strtotime ( '-8 hours' ) ), 'lteq'
            )
            ->create ();

        return $this->_orderRepository->getList ( $criteria )->getItems ();
    }

    /**
     * Get shipment by order id
     * @param int $orderId
     * @return \Magento\Sales\Api\Data\ShipmentInterface[]
     */
    protected function getShipmentByOrderId ( $orderId )
    {
        $criteria = $this->_searchCriteriaBuilder
            ->addFilter ( 'order_id', $orderId )
            ->create ();

        return $this->_shipmentRepository->getList ( $criteria )->getItems ();
    }

    public function execute ()
    {
        foreach ( $this->getOrders () as $order ) {
            foreach ( $this->getShipmentByOrderId ( $order->getEntityId () ) as $shipment ) {
                foreach ( $shipment->getTracks () as $track ) {
                    if ( $trackInfo = $this->_apiHelper->getTracking ( $track->getTrackNumber () ) ) {
                        /** @var \Eshoper\LPShipping\Api\Data\TrackingInterface $tracking */
                        $tracking = $this->_tracking->create ();

                        // If tracking exists
                        if ( $trackingRepository = $this->_trackingRepository
                                ->getByTrackingCode ( $track->getTrackNumber () ) ) {
                            $tracking = $trackingRepository;
                        }

                        // Add or update tracking info
                        if ( property_exists ( $trackInfo, 'events' ) ) {
                            $tracking->setTrackingCode ( $track->getTrackNumber () );
                            $tracking->setStateCode ( $trackInfo->state );
                            $tracking->setEvents ( json_encode ( $trackInfo->events ) );

                            $this->_trackingRepository->save ( $tracking );
                        }

                        $order->setLpShipmentTrackingUpdated ( date ( 'Y-m-d H:i:s' ) );
                        $this->_orderRepository->save ( $order );
                    }
                }
            }
        }
    }
}

<?php
/**
 * @package    Eshoper/LPShipping/Controller/Adminhtml/MassAction
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Controller\Adminhtml\MassAction;

class CreateShippingLabels extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $_orderCollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var \Magento\Ui\Component\MassAction\Filter $_filter
     */
    protected $_filter;

    /**
     * @var \Magento\Sales\Model\Convert\Order $_convertOrder
     */
    protected $_convertOrder;

    /**
     * @var \Magento\Shipping\Model\Shipping\LabelGenerator $_labelGenerator
     */
    protected $_labelGenerator;

    /**
     * @var \Magento\Sales\Api\ShipmentRepositoryInterface $_shipmentRepository
     */
    protected $_shipmentRepository;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface $_orderRepository
     */
    protected $_orderRepository;

    /**
     * @var \Eshoper\LPShipping\Model\Config $_config
     */
    protected $_config;

    /**
     * CreateShippingLabels constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Sales\Model\Convert\Order $convertOrder
     * @param \Magento\Shipping\Model\Shipping\LabelGenerator $labelGenerator
     * @param \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Eshoper\LPShipping\Model\Config $config
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Model\Convert\Order $convertOrder,
        \Magento\Shipping\Model\Shipping\LabelGenerator $labelGenerator,
        \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Eshoper\LPShipping\Model\Config $config
    ) {
        $this->_filter = $filter;
        $this->_orderCollectionFactory  = $orderCollectionFactory;
        $this->_convertOrder            = $convertOrder;
        $this->_labelGenerator          = $labelGenerator;
        $this->_shipmentRepository      = $shipmentRepository;
        $this->_orderRepository         = $orderRepository;
        $this->_config                  = $config;
        parent::__construct ( $context );
    }

    protected function createShippingLabel (
        \Magento\Sales\Model\Order\Shipment $shipment
    ) {
        if ( $shipment ) {
            $items = [];
            $weight = $price = 0;
            /** @var \Magento\Framework\App\RequestInterface $request */
            $request = $this->_objectManager->create ( 'Magento\Framework\App\RequestInterface' );

            // Format packages
            foreach ( $shipment->getAllItems () as $item ) {
                $items [ $item->getOrderItemId () ] = [
                    'qty' => $item->getQty (),
                    'price' => $item->getPrice (),
                    'name' => $item->getName (),
                    'weight' => $item->getWeight (),
                    'product_id' => $item->getId (),
                    'order_item_id' => $item->getOrderItemId ()
                ];

                $weight += $item->getWeight ();
                $price += $item->getPrice ();
            }

            // Set packages
            $request->setParams ([
                'packages' => [
                    '1' => [
                        'params' => [
                            'container' => '',
                            'weight' => $weight,
                            'customs_value' => $price,
                            'length' => 0,
                            'width' => 0,
                            'height' => 0,
                            'weight_units' => 'KILOGRAM',
                            'dimension_units' => 'CENTIMETER',
                            'content_type' => '',
                            'content_type_other' => ''
                        ],
                        'items' => $items
                    ]
                ]
            ]);

            // Create the shipping label
            $this->_labelGenerator->create ( $shipment, $request );
            $this->_shipmentRepository->save ( $shipment );
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
            foreach ( $orderCollection as $order ) {
                if ( !$order->getLpShippingItemId () ) {
                    // Create shipment if order doesn't have one
                    if ( !$order->hasShipments () ) {
                        if ( !$order->canShip () ) {
                            $this->messageManager->addErrorMessage (
                                sprintf ( '%s %s', __( 'You can\'t create shipping labels for order' ),
                                    $order->getIncrementId () )
                            );
                            continue;
                        }

                        // Convert order to shipment
                        $orderShipment = $this->_convertOrder->toShipment ( $order );

                        foreach ( $order->getAllItems () as $orderItem ) {
                            // Check if virtual item and item Quantity
                            if ( !$orderItem->getQtyToShip () || $orderItem->getIsVirtual () ) {
                                continue;
                            }

                            // Convert to shipment item
                            $orderShipment->addItem ( $this->_convertOrder->itemToShipmentItem ( $orderItem )
                                ->setQty ( $orderItem->getQtyToShip () ) );

                            if ( !$orderShipment->getId () ) {
                                $orderShipment->register ();
                            }

                            $this->_shipmentRepository->save ( $orderShipment );
                            $this->_orderRepository->save ( $orderShipment->getOrder () );
                        }

                        $this->createShippingLabel ( $orderShipment );
                    } else {
                        // Create label if order has shipment
                        $orderShipment = $this->_convertOrder->toShipment ( $order );

                        if ( !$orderShipment->getId () ) {
                            $orderShipment->register ();
                        }

                        $this->createShippingLabel ( $order->getShipmentsCollection ()->getFirstItem () );
                    }
                } else {
                    $this->messageManager->addErrorMessage (
                        sprintf ( '%s %s', __( 'You have already created label for order' ),
                            $order->getIncrementId () )
                    );
                }
            }
            $this->messageManager->addSuccessMessage( __( 'You created the shipping labels.' ) );
        } catch ( \Exception $e ) {
            $this->messageManager->addErrorMessage ( $e->getMessage () );
        }

        $resultRedirect = $this->resultFactory->create ( $this->resultFactory::TYPE_REDIRECT );
        return $resultRedirect->setPath ( $this->_redirect->getRefererUrl() );
    }
}

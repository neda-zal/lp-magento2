<?php
/**
 * @package    Eshoper/LPShipping/Model/Carrier
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model\Carrier;

use Eshoper\LPShipping\Api\SenderRepositoryInterface;
use Eshoper\LPShipping\Model\ResourceModel\LPCountries;
use Magento\Framework\Xml\Security;
use Magento\Quote\Model\Quote\Address\RateRequest;

class LPCarrier extends \Magento\Shipping\Model\Carrier\AbstractCarrierOnline implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'lpcarrier';

    /**
     * @var \Psr\Log\LoggerInterface $_logger
     */
	protected $_logger;

    /**
     * @var bool
     */
    protected $_isFixed = false;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory $_rateResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $_rateMethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @var \Eshoper\LPShipping\Model\ResourceModel\LPTableRates $_LPTableRates
     */
    protected $_LPTableRates;

    /**
     * @var \Eshoper\LPShipping\Model\ResourceModel\LPExpressTableRates $_LPExpressTableRates
     */
    protected $_LPExpressTableRates;

    /**
     * @var \Eshoper\LPShipping\Model\ResourceModel\LPCountryRates $_LPCountryRates
     */
    protected $_LPCountryRates;

    /**
     * @var \Eshoper\LPShipping\Model\ResourceModel\LPExpressCountryRates $_LPExpressCountryRates
     */
    protected $_LPExpressCountryRates;

    /**]
     * @var \Eshoper\LPShipping\Model\ResourceModel\LPCountries\Collection $_LPCountries
     */
    protected $_LPCountries;

    /**
     * @var \Eshoper\LPShipping\Helper\ApiHelper $_apiHelper
     */
    protected $_apiHelper;

    /**
     * @var \Eshoper\LPShipping\Helper\ShippingTemplate $_shippingTemplateHelper
     */
    protected $_shippingTemplateHelper;

    /**
     * @var \Magento\Shipping\Model\Tracking\ResultFactory $_trackFactory
     */
    protected $_trackFactory;

    /**
     * @var \Magento\Shipping\Model\Tracking\Result\StatusFactory $_trackStatusFactory
     */
    protected $_trackStatusFactory;

    /**
     * @var \Eshoper\LPShipping\Api\TrackingRepositoryInterface $_trackingRepository
     */
    protected $_trackingRepository;

    /**
     * @var \Eshoper\LPShipping\Api\SenderRepositoryInterface $_senderRepository
     */
    protected $_senderRepository;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface $_orderRepository
     */
    protected $_orderRepository;

    /**
     * @var \Eshoper\LPShipping\Model\Config $_config
     */
    protected $_config;

    /**
     * @var array $_labels
     */
    protected $_labels;

    protected $_test;

    /**
     * LPCarrier constructor.
     * @param \Eshoper\LPShipping\Model\ResourceModel\LPTableRates $LPTableRates
     * @param \Eshoper\LPShipping\Model\ResourceModel\LPExpressTableRates $LPExpressTableRates
     * @param \Eshoper\LPShipping\Model\ResourceModel\LPCountryRates $LPCountryRates
     * @param \Eshoper\LPShipping\Model\ResourceModel\LPExpressCountryRates $LPExpressCountryRates
     * @param LPCountries\Collection $LPCountries
     * @param \Eshoper\LPShipping\Helper\ApiHelper $apiHelper
     * @param \Eshoper\LPShipping\Helper\ShippingTemplate $shippingTemplateHelper
     * @param \Eshoper\LPShipping\Api\TrackingRepositoryInterface $trackingRepository
     * @param \Eshoper\LPShipping\Api\SenderRepositoryInterface $senderRepository
     * @param \Eshoper\LPShipping\Model\Config $config
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param Security $xmlSecurity
     * @param \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory
     * @param \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory
     * @param \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Directory\Helper\Data $directoryData
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param array $data
     */
    public function __construct(
        \Eshoper\LPShipping\Model\ResourceModel\LPTableRates $LPTableRates,
        \Eshoper\LPShipping\Model\ResourceModel\LPExpressTableRates $LPExpressTableRates,
        \Eshoper\LPShipping\Model\ResourceModel\LPCountryRates $LPCountryRates,
        \Eshoper\LPShipping\Model\ResourceModel\LPExpressCountryRates $LPExpressCountryRates,
        \Eshoper\LPShipping\Model\ResourceModel\LPCountries\Collection $LPCountries,
        \Eshoper\LPShipping\Helper\ApiHelper $apiHelper,
        \Eshoper\LPShipping\Helper\ShippingTemplate $shippingTemplateHelper,
        \Eshoper\LPShipping\Api\TrackingRepositoryInterface $trackingRepository,
        \Eshoper\LPShipping\Api\SenderRepositoryInterface $senderRepository,
        \Eshoper\LPShipping\Model\Config $config,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger, Security $xmlSecurity,
        \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory,
        \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        array $data = []
    ) {
        $this->_rateResultFactory       = $rateResultFactory;
        $this->_rateMethodFactory       = $rateMethodFactory;
        $this->_LPTableRates            = $LPTableRates;
        $this->_LPExpressTableRates     = $LPExpressTableRates;
        $this->_LPCountryRates          = $LPCountryRates;
        $this->_LPExpressCountryRates   = $LPExpressCountryRates;
        $this->_LPCountries             = $LPCountries;
		$this->_logger                  = $logger;
		$this->_apiHelper               = $apiHelper;
		$this->_shippingTemplateHelper  = $shippingTemplateHelper;
		$this->_trackFactory            = $trackFactory;
		$this->_trackStatusFactory      = $trackStatusFactory;
		$this->_trackingRepository      = $trackingRepository;
		$this->_senderRepository        = $senderRepository;
		$this->_config                  = $config;
		$this->_orderRepository         = $orderRepository;
		$this->_labels                  = [];
		$this->_test                    = [];

        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $xmlSecurity,
            $xmlElFactory,
            $rateResultFactory,
            $rateMethodFactory,
            $trackFactory,
            $trackErrorFactory,
            $trackStatusFactory,
            $regionFactory,
            $countryFactory,
            $currencyFactory,
            $directoryData,
            $stockRegistry,
            $data
        );
    }

    /**
     * Available services by contract
     * @return array
     */
    protected function getAvailableServices ()
    {
        return [
            0 => [ 'lpcarriershipping_lp', 'lpcarriershipping_lpexpress' ],
            1 => [ 'lpcarriershipping_lp' ],
            2 => [ 'lpcarriershipping_lpexpress' ]
        ];
    }

    /**
     * @param $key
     * @return mixed
     */
    protected function getCarrierTitle ( $key )
    {
        $services = [
            'lpcarriershipping_lp' => __( 'Lithuanian Post' ),
            'lpcarriershipping_lpexpress' => __( 'LP EXPRESS' )
        ];

        return $services [ $key ];
    }

    /**
     * Get enabled services
     * @return array
     */
    protected function getEnabledServices ()
    {
        return $this->getAvailableServices () [
            $this->getConfigData ( 'available_services' )
        ];
    }

    /**
     * Create methods structure code => label
     * @return array
     */
    protected function getAllMethods ()
    {
        return [
            'lp_postoffice'        => __( 'Delivery To Home, Office Or Post Office' ),
            'lp_overseas'          => __( 'Delivery To Overseas' ),
            'lpexpress_courier'    => __( 'Delivery To Home Or Office By Courier' ),
            'lpexpress_terminal'   => __( 'Delivery To Terminal' ),
            'lpexpress_postoffice' => __( 'Delivery To Post Office' ),
            'lpexpress_overseas'   => __( 'Delivery To Overseas' ),
        ];
    }

    /**
     * Get formatted allowed methods
     * @return array
     */
    protected function getEnabledMethods ()
    {
        // Filtered array
        $allowedMethods = [];

        // Cycle through allowed methods config values
        foreach ( $this->getEnabledServices () as $availableService ) {
            // Filter only allowed methods
            $allowedMethods [ $availableService ] = array_filter ( $this->getAllMethods (),
                function ( $method, $key ) use ( $availableService ) {
                    return in_array ( $key,
                        explode ( ',', $this->getConfigData ( $availableService )
                        [ 'allowedmethods' ] )
                    );
            }, ARRAY_FILTER_USE_BOTH );
        }

        // Convert Phrase Object to assoc array
        return json_decode ( json_encode ( $allowedMethods ), true );
    }

    /**
     * Allowed methods not in lithuania
     * @return array
     */
    protected function getForeignMethods ()
    {
        return [ 'lp_overseas', 'lpexpress_overseas' ];
    }

    /**
     * Get service config
     * @param $service
     * @param $field
     * @return mixed
     */
    protected function getServiceConfigData ( $service, $field )
    {
        return $this->getConfigData ( $service ) [ $field ];
    }

    /**
     * Get total weight of items in cart
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return integer
     */
    public function getQuoteWeight ( \Magento\Quote\Model\Quote\Address\RateRequest $request )
    {
        $weight = 0;
        if ( $items = $this->getAllItems ( $request ) ) {
            foreach ( $items as $item ) {
                $weight += ( $item->getWeight () * $item->getQty () );
            }
        }

        return $weight;
    }

    /**
     * @param RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result|bool
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag ('active') || !$this->getConfigFlag ( 'status' ) ) {
            return false;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create ();

        foreach ( $this->getEnabledMethods () as $service => $methods ) {
            // Only allowed countries
            if ( $this->getServiceConfigData ( $service, 'sallowspecific' ) ) {
                if ( !in_array ( $request->getDestCountryId (),
                    explode (',', $this->getServiceConfigData ( $service, 'specificcountry' ) ) ) ) {
                    continue; // Skip service
                }
            }

            foreach ( $methods as $method => $label ) {
                // Restrict post office from foreign countries
                if ( !in_array ( $method, $this->getForeignMethods () )
                    && $request->getDestCountryId () !== 'LT' ) {
                    continue;
                }

                // Only foreign countries
                if ( in_array ( $method, $this->getForeignMethods () )
                    && $request->getDestCountryId () === 'LT' ) {
                    continue;
                }

                /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
                $methodFactory = $this->_rateMethodFactory->create();

                $methodFactory->setCarrier ( $this->_code );
                $methodFactory->setCarrierTitle( $this->getCarrierTitle ( $service ) );
                $methodFactory->setMethod ( $this->_code . $method );
                $methodFactory->setMethodTitle ( $label );

                // Fixed rates
                if ( $shippingPrice = $this->getConfigData ( $service )
                        [ sprintf ( '%s_price', $method ) ] ) {
                    $methodFactory->setPrice ( $request->getFreeShipping () ? 0 : $shippingPrice );
                    $methodFactory->setCost ( $request->getFreeShipping () ? 0 : $shippingPrice );
                } else {
                    // Table rates
                    if ( $request->getDestCountryId () === 'LT' ) {
                        if ( $service == 'lpcarriershipping_lp' ) {
                            $rate = $this->_LPTableRates->getRate($this->getQuoteWeight ( $request ),
                                $method);
                        } else if ( $service == 'lpcarriershipping_lpexpress' ) {
                            $rate = $this->_LPExpressTableRates->getRate ( $this->getQuoteWeight ( $request ),
                                $method );
                        }

                        if ( $rate != null && $rate != -1 ) {
                            $methodFactory->setPrice ( $request->getFreeShipping () ? 0 : $rate );
                            $methodFactory->setCost ( $request->getFreeShipping () ? 0 : $rate );
                        } else {
                            continue;
                        }
                    }

                    // Overseas rates
                    if ( $request->getDestCountryId () !== 'LT' ) {
                        if ( $service == 'lpcarriershipping_lp' ) {
                            $rate = $this->_LPCountryRates->getRate ( $request->getDestCountryId () );
                        } else if ( $service == 'lpcarriershipping_lpexpress' ) {
                            $rate = $this->_LPExpressCountryRates->getRate ( $request->getDestCountryId () );
                        }

                        if ( $rate != null && $rate != -1 ) {
                            $methodFactory->setPrice ( $request->getFreeShipping () ? 0 : $rate );
                            $methodFactory->setCost ( $request->getFreeShipping () ? 0 : $rate );
                        } else {
                            continue;
                        }
                    }
                }


                $result->append ( $methodFactory );
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getAllowedMethods ()
    {
        return $this->getEnabledMethods ();
    }

    /**
     * Get shipment data
     *
     * @param $request
     * @param $order
     * @param $parcelWeight
     * @return array
     */
    protected function getShipmentData ( $request, $order, $parcelWeight )
    {
        // Get shipping template according to shipping type and shipping size
        $template = $this->_shippingTemplateHelper->getTemplate (
            $order->getLpShippingType (),
            $order->getLpShippingSize ()
        );

        // Sender info
        $sender = $this->_senderRepository->getByOrderId ( $order->getEntityId () );

        // Get sender data from module configuration or from shipment details
        $sender_name  = $sender->getName () ??
            $this->getConfigData ( 'lpcarriersender/sender_name' );
        $sender_phone = $sender->getPhone () ??
            $this->getConfigData ( 'lpcarriersender/sender_phone' );
        $sender_email = $sender->getEmail () ??
            $this->getConfigData ( 'lpcarriersender/sender_email' );

        // Sender country code by id
        $country = $this->_LPCountries->addFieldToFilter (
            'country_id',
            $sender->getCountryId () ??
            $this->getConfigData ( 'lpcarriersender/sender_country' )
        );

        $sender_country     = $country->getData () [ 0 ][ 'country_code' ];
        $sender_city        = $sender->getCity () ??
            $this->getConfigData ( 'lpcarriersender/sender_city' );
        $sender_street      = $sender->getStreet () ??
            $this->getConfigData ( 'lpcarriersender/sender_street' );
        $sender_building    = $sender->getBuildingNumber () ??
            $this->getConfigData ( 'lpcarriersender/sender_building_number' );
        $sender_apartment   = $sender->getApartment () ??
            $this->getConfigData ( 'lpcarriersender/sender_apartment_number' );
        $sender_postcode    = $sender->getPostcode () ??
            $this->getConfigData ( 'lpcarriersender/sender_postcode' );


        // Receiver info
        $shippingAddress = $order->getShippingAddress ();
        $receiver_phone  = $shippingAddress->getTelephone ();

        // Convert to 370xxxxxxx
        if ( substr ( $receiver_phone, 0, 1 ) === "8" ) {
            $receiver_phone = substr_replace ( $receiver_phone, "370", 0, 1 );
        }

        $shipmentData = [
            'template' => $template [ 'id' ],
            'receiver' => [
                'name'    => sprintf ('%s %s',
                    $shippingAddress->getFirstName (),
                    $shippingAddress->getLastName ()
                ),
                'address' => [
                    'locality'          => $shippingAddress->getCity (),
                    $template [ 'id' ] == 73 ? 'address1' : 'freeformAddress' => implode ( ',', $shippingAddress->getStreet () ),
                    'postalCode'        => $shippingAddress->getPostcode (),
                    'country'           => $shippingAddress->getCountryId ()
                ],
                'phone' => $receiver_phone,
                'email' => $shippingAddress->getEmail ()
            ],
            'sender' => [ // TODO: Get value from shipment correction,
                'companyName'   => $sender_name,
                'name'          => " ",
                'email'         => $sender_email,
                'phone'         => $sender_phone,
                'address' => [
                    'locality'   => $sender_city,
                    'street'     => $sender_street,
                    'building'   => $sender_building,
                    'apartment'  => $sender_apartment,
                    'postalCode' => $sender_postcode,
                    'country'    => $sender_country
                ]
            ],
            'partCount' => $packageCount = count ( $request->getPackages () ),
            'weight'    => $template [ 'weight' ] > 0 ? $parcelWeight * 1000 : null,
            // CN Form data
            'documents' => $this->_shippingTemplateHelper->getCnData (
                $order,
                $template [ 'id' ]
            ), // TODO: Get value from shipment correction
            'additionalServices' => $this->_shippingTemplateHelper->getAdditionalServices (
                $template [ 'id' ],
                $order->getLpCod () == null ? $order->getGrandTotal () : $order->getLpCod (),
                $order->getPayment ()->getMethod () == 'cashondelivery',
                !$this->_config->isLpExpressMethod ( $order->getShippingMethod () )
                && in_array ( 'priority', explode ( ',', $this->_config->getConsigmentFormation () ) ), // Is priority
                !$this->_config->isLpExpressMethod ( $order->getShippingMethod () )
                && in_array ( 'registered', explode ( ',', $this->_config->getConsigmentFormation () ) ) // Is registered
            )
        ];

        // Terminal
        if ( $order->getShippingMethod () == 'lpcarrier_lpcarrierlpexpress_terminal' ) {
            $shipmentData [ 'receiver' ][ 'terminalId' ] = $order->getLpexpressTerminal ();
        }

        return $shipmentData;
    }

    /**
     * @inheritDoc
     */
    protected function _doShipmentRequest ( \Magento\Framework\DataObject $request )
    {
        // Add labels pdf and tracking number
        $result = new \Magento\Framework\DataObject ();

        // Mass action
        if ( key_exists ( 'selected', $_REQUEST ) ) {
            $shippingItemIds = [];

            try {
                foreach ( $_REQUEST [ 'selected' ] as $orderId ) {
                    $order = $this->_orderRepository->get ( $orderId );
                    $shipmentData = $this->getShipmentData ( $request, $order, $order->getWeight () );

                    if ( !$order->getLpShippingItemId () ) {
                        if ( $shippingItemId = $this->_apiHelper->createShippingItem ( $shipmentData ) ) {
                            // Push shipping item ids
                            $shippingItemIds [] = $shippingItemId;

                            // Set shipping item id
                            $order->setLpShippingItemId ( $shippingItemId );
                            $this->_orderRepository->save ( $order );
                        }
                    }
                }


                if ( !empty ( $shippingItemIds )
                    && $cartId = $this->_apiHelper->initiateShipping ( $shippingItemIds ) ) {
                    foreach ( $_REQUEST [ 'selected' ] as $orderId ) {
                        $order = $this->_orderRepository->get ( $orderId );
                        if ( !$order->getLpCartId () ) {
                            $order->setLpCartId ( $cartId );
                            $this->_orderRepository->save ( $order );
                        }
                    }
                }

                /** @var \Magento\Sales\Model\Order $order */
                $order = $request->getOrderShipment ()->getOrder ();

                if ( $label = $this->_apiHelper->createSticker ( $order->getLpShippingItemId () ) ) {
                    $result->setShippingLabelContent (
                        base64_decode ( $label )
                    );
                    $result->setTrackingNumber ( $this->_apiHelper->getBarcode ( $order->getLpShippingItemId () ) );
                    // Set custom order status
                    $this->_config->setOrderStatus ( $order );

                    $order->setLpShipmentTrackingUpdated ( date ( 'Y-m-d H:i:s' ) );
                    $this->_orderRepository->save ( $order );
                }
            } catch ( \Exception $e ) {
                throw new \Magento\Framework\Exception\LocalizedException (
                    __( $e->getMessage () )
                );
            }
        } else {
            // Check if empty labels because this method calls multiple times per package
            if ( empty ( $this->_labels ) ) {
                /** @var \Magento\Sales\Model\Order $order */
                $order = $request->getOrderShipment ()->getOrder ();

                // Calculate parcel weight
                $parcelWeight = 0;

                foreach ( $request->getPackages () as $package ) {
                    $parcelWeight += $package [ 'params' ] [ 'weight' ];
                }

                $shipmentData = $this->getShipmentData ( $request, $order, $parcelWeight );

                try {
                    if ( $shippingItemId = $this->_apiHelper->createShippingItem ( $shipmentData ) ) {
                        if ( $cartId = $this->_apiHelper->initiateShipping ( [ $shippingItemId ] ) ) {
                            if ( $label = $this->_apiHelper->createSticker ( $shippingItemId ) ) {
                                $result->setShippingLabelContent (
                                    base64_decode ( $label )
                                );

                                $result->setTrackingNumber ( $this->_apiHelper->getBarcode ( $shippingItemId ) );

                                // Save cart id and shipping item id to order
                                $order->setLpCartId ( $cartId );
                                $order->setLpShippingItemId ( $shippingItemId );

                                // Set custom order status
                                $this->_config->setOrderStatus ( $order );

                                $order->setLpShipmentTrackingUpdated ( date ( 'Y-m-d H:i:s' ) );
                                $this->_orderRepository->save ( $order );
                            }
                        }
                    }
                } catch ( \Exception $e ) {
                    throw new \Magento\Framework\Exception\LocalizedException (
                        __( $e->getMessage () )
                    );
                }
            }

            // Fill labels so second time call won't work
            $this->_labels [] = $result;
        }

        return $result;
    }

    /**
     * @param $trackings
     * @return mixed
     * @throws \Exception
     */
    public function getTracking ( $trackings )
    {
        if ( !is_array ( $trackings ) ) {
            $trackings = [ $trackings ];
        }

        $result = $this->_trackFactory->create ();
        $tracking = $this->_trackStatusFactory->create ();

        foreach ( $trackings as $trackingCode ) {
            $packageEvents = [];
            $trackItem = $this->_trackingRepository->getByTrackingCode ( $trackingCode );
            $tracking->setTracking ( $trackingCode );

            $resultArr = [];
            $resultArr [ 'carrier_title' ] = __( 'Lithuania Post' );

            if ( $trackItem && $trackItem->getEvents () ) {
                foreach ( json_decode ( $trackItem->getEvents () ) as $event ) {
                    array_push ( $packageEvents, [
                        'deliverydate' => explode ( ' ', $event->eventDate )[ 0 ],
                        'deliverytime' => explode ( ' ', $event->eventDate )[ 1 ],
                        'activity' => $event->eventTitle
                    ] );
                }

                $resultArr [ 'status' ] = $this->_trackingRepository
                    ->getEventDescriptionByCode ( $trackItem->getStateCode () );
                $resultArr [ 'progressdetail' ] = $packageEvents;
            }

            $tracking->addData ( $resultArr );
            $result->append ( $tracking );
        }

        return $result;
    }

    public function isShippingLabelsAvailable()
    {
        return true;
    }

    public function processAdditionalValidation ( \Magento\Framework\DataObject $request )
    {
        return true;
    }
}

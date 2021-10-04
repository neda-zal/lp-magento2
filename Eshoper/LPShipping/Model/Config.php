<?php
/**
 * @package    Eshoper/LPShipping/Model
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model;

class Config
{
    /**
     * Path constants
     */
    const CONFIG_PATH_STATUS                                = 'carriers/lpcarrier/status';
    const CONFIG_PATH_ENABLED                               = 'carriers/lpcarrier/active';
    const CONFIG_PATH_TEST_MODE                             = 'carriers/lpcarrier/lpcarrierapi/test_mode';
    const CONFIG_PATH_API_USERNAME                          = 'carriers/lpcarrier/lpcarrierapi/api_login';
    const CONFIG_PATH_API_PASSWORD                          = 'carriers/lpcarrier/lpcarrierapi/api_password';
    const CONFIG_PATH_SENDER_NAME                           = 'carriers/lpcarrier/lpcarriersender/sender_name';
    const CONFIG_PATH_SENDER_PHONE                          = 'carriers/lpcarrier/lpcarriersender/sender_phone';
    const CONFIG_PATH_SENDER_EMAIL                          = 'carriers/lpcarrier/lpcarriersender/sender_email';
    const CONFIG_PATH_SENDER_COUNTRY                        = 'carriers/lpcarrier/lpcarriersender/sender_country';
    const CONFIG_PATH_SENDER_CITY                           = 'carriers/lpcarrier/lpcarriersender/sender_city';
    const CONFIG_PATH_SENDER_STREET                         = 'carriers/lpcarrier/lpcarriersender/sender_street';
    const CONFIG_PATH_SENDER_BUILDING                       = 'carriers/lpcarrier/lpcarriersender/sender_building_number';
    const CONFIG_PATH_SENDER_APARTMENT                      = 'carriers/lpcarrier/lpcarriersender/sender_apartment_number';
    const CONFIG_PATH_SENDER_POSTCODE                       = 'carriers/lpcarrier/lpcarriersender/sender_postcode';
    const CONFIG_PATH_LPEXPRESS_CALL_COURIER_AUTOMATICALLY  = 'carriers/lpcarrier/lpcarriershipping_lpexpress/call_courier_automatically';
    const CONFIG_PATH_LPEXPRESS_COURIER_METHOD              = 'carriers/lpcarrier/lpcarriershipping_lpexpress/lpexpress_shipping_courier_method';
    const CONFIG_PATH_LPEXPRESS_COURIER_SIZE                = 'carriers/lpcarrier/lpcarriershipping_lpexpress/lpexpress_shipping_courier_size';
    const CONFIG_PATH_LPEXPRESS_COURIER_DELIVERY            = 'carriers/lpcarrier/lpcarriershipping_lpexpress/lpexpress_shipping_courier_delivery_time';
    const CONFIG_PATH_LPEXPRESS_TERMINAL_METHOD             = 'carriers/lpcarrier/lpcarriershipping_lpexpress/lpexpress_shipping_terminal_method';
    const CONFIG_PATH_LPEXPRESS_TERMINAL_SIZE               = 'carriers/lpcarrier/lpcarriershipping_lpexpress/lpexpress_shipping_terminal_size';
    const CONFIG_PATH_LPEXPRESS_TERMINAL_DELIVERY           = 'carriers/lpcarrier/lpcarriershipping_lpexpress/lpexpress_shipping_terminal_delivery_time';
    const CONFIG_PATH_LPEXPRESS_POSTOFFICE_METHOD           = 'carriers/lpcarrier/lpcarriershipping_lpexpress/lpexpress_shipping_postoffice_method';
    const CONFIG_PATH_LPEXPRESS_POSTOFFICE_DELIVERY         = 'carriers/lpcarrier/lpcarriershipping_lpexpress/lpexpress_postoffice_delivery_time';
    const CONFIG_PATH_LPEXPRESS_OVERSEAS_METHOD             = 'carriers/lpcarrier/lpcarriershipping_lpexpress/lpexpress_shipping_overseas_method';
    const CONFIG_PATH_LPEXPRESS_OVERSEAS_SIZE               = 'carriers/lpcarrier/lpcarriershipping_lpexpress/lpexpress_shipping_overseas_courier_size';
    const CONFIG_PATH_LPEXPRESS_OVERSEAS_DELIVERY           = 'carriers/lpcarrier/lpcarriershipping_lpexpress/lpexpress_overseas_delivery_time';
    const CONFIG_PATH_LP_POSTOFFICE_SIZE                    = 'carriers/lpcarrier/lpcarriershipping_lp/lp_shipping_postoffice_size';
    const CONFIG_PATH_LP_POSTOFFICE_TYPE_SM                 = 'carriers/lpcarrier/lpcarriershipping_lp/lp_shipping_postoffice_type_sm';
    const CONFIG_PATH_LP_POSTOFFICE_TYPE_L                  = 'carriers/lpcarrier/lpcarriershipping_lp/lp_shipping_postoffice_type_l';
    const CONFIG_PATH_LP_POSTOFFICE_DELIVERY                = 'carriers/lpcarrier/lpcarriershipping_lp/lp_postoffice_delivery_time';
    const CONFIG_PATH_LP_OVERSEAS_SIZE                      = 'carriers/lpcarrier/lpcarriershipping_lp/lp_shipping_overseas_size';
    const CONFIG_PATH_LP_OVERSEAS_TYPE_SM                   = 'carriers/lpcarrier/lpcarriershipping_lp/lp_shipping_overseas_type_sm';
    const CONFIG_PATH_LP_OVERSEAS_TYPE_L                    = 'carriers/lpcarrier/lpcarriershipping_lp/lp_shipping_overseas_type_l';
    const CONFIG_PATH_LP_OVERSEAS_DELIVERY                  = 'carriers/lpcarrier/lpcarriershipping_lp/lp_shipping_overseas_delivery_time';
    const CONFIG_PATH_LABEL_SIZE                            = 'carriers/lpcarrier/lpcarriershipping_other_settings/label_format';
    const CONFIG_PATH_CONSIGMENT_FORMATION                  = 'carriers/lpcarrier/lpcarriershipping_other_settings/consigment_formation';

    /**
     * Database tables
     */
    const CONFIG_DB_TERMINAL_TABLE_NAME                 = 'lpexpress_terminal_list';
    const CONFIG_DB_API_TABLE_NAME                      = 'lp_api_token';

    /**
     * Order statuses
     */
    const COURIER_CALLED_STATUS         = 'lp_courier_called';
    const COURIER_NOT_CALLED_STATUS     = 'lp_courier_not_called';
    const SHIPMENT_CREATED_STATUS       = 'lp_shipment_created';
    const SHIPMENT_NOT_CREATED_STATUS   = 'lp_shipment_not_created';


    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $_config
     */
    protected $_config;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface $_configWriter
     */
    protected $_configWriter;

    /**
     * Config constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
    ) {
        $this->_config = $config;
        $this->_configWriter = $configWriter;
    }

    /**
     * Get is module enabled
     * @return mixed
     */
    public function isEnabled ()
    {
        return $this->_config->getValue (
            self::CONFIG_PATH_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Get is test mode enabled
     * @return mixed
     */
    public function getIsTestMode ()
    {
        return $this->_config->getValue (
            self::CONFIG_PATH_TEST_MODE,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Get terminal db name
     * @return string
     */
    public function getTerminalDbTableName ()
    {
        return self::CONFIG_DB_TERMINAL_TABLE_NAME;
    }

    /**
     * Get api_token db name
     * @return string
     */
    public function getApiTokenDbTableName ()
    {
        return self::CONFIG_DB_API_TABLE_NAME;
    }

    /**
     * Get API credentials username
     * @return mixed
     */
    public function getApiUsername ()
    {
        return $this->_config->getValue (
            self::CONFIG_PATH_API_USERNAME,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Get API credentials password
     * @return mixed
     */
    public function getApiPassword ()
    {
        return $this->_config->getValue (
            self::CONFIG_PATH_API_PASSWORD,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Get sender name
     * @return mixed
     */
    public function getSenderName ()
    {
        return $this->_config->getValue (
            self::CONFIG_PATH_SENDER_NAME,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Get sender phone
     * @return mixed
     */
    public function getSenderPhone ()
    {
        return $this->_config->getValue (
            self::CONFIG_PATH_SENDER_PHONE,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Get sender email
     * @return mixed
     */
    public function getSenderEmail ()
    {
        return $this->_config->getValue (
            self::CONFIG_PATH_SENDER_EMAIL,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Get sender country
     * @return mixed
     */
    public function getSenderCountryId ()
    {
        return $this->_config->getValue (
            self::CONFIG_PATH_SENDER_COUNTRY,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Get sender city
     * @return mixed
     */
    public function getSenderCity ()
    {
        return $this->_config->getValue (
            self::CONFIG_PATH_SENDER_CITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Get sender street
     * @return mixed
     */
    public function getSenderStreet ()
    {
        return $this->_config->getValue (
            self::CONFIG_PATH_SENDER_STREET,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Get sender building
     * @return mixed
     */
    public function getSenderBuilding ()
    {
        return $this->_config->getValue (
            self::CONFIG_PATH_SENDER_BUILDING,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Get sender apartment
     * @return mixed
     */
    public function getSenderApartment ()
    {
        return $this->_config->getValue (
            self::CONFIG_PATH_SENDER_APARTMENT,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Get sender postcode
     * @return mixed
     */
    public function getSenderPostCode ()
    {
        return $this->_config->getValue (
            self::CONFIG_PATH_SENDER_POSTCODE,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Get if call courier automatically for LP Express
     * @return mixed
     */
    public function getCallCourierAutomatically ()
    {
        return $this->_config->getValue (
            self::CONFIG_PATH_LPEXPRESS_CALL_COURIER_AUTOMATICALLY,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Set module status
     * @param bool $status
     */
    public function setStatus ( $status )
    {
        $this->_configWriter->save ( self::CONFIG_PATH_STATUS, $status );
    }

    /**
     * Set module status according to lp method
     * @param \Magento\Sales\Model\Order|\Magento\Sales\API\Data\OrderInterface $order
     */
    public function setOrderStatus ( $order )
    {
        // If shipping method is LPEXPRESS and not CHCA
        if ( $this->isLpExpressMethod ( $order->getShippingMethod () )
                && $order->getLpShippingType () !== 'CHCA' ) {
            $order->setStatus (
                $this->getCallCourierAutomatically () ?
                    self::COURIER_CALLED_STATUS :
                    self::COURIER_NOT_CALLED_STATUS
            );
        } else {
            // Method is LP
            $order->setStatus (
                self::SHIPMENT_CREATED_STATUS
            );
        }
    }

    /**
     * Get module status
     * @return mixed
     */
    public function getStatus ()
    {
        return $this->_config->getValue (
            self::CONFIG_PATH_STATUS,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Get shipping type by shipping method
     * @param string $method
     * @return mixed|null
     */
    public function getShippingType ( $method )
    {
        $shippingType = null;

        switch ( $method ) {
            case 'lpcarrier_lpcarrierlpexpress_courier':
                $shippingType = $this->_config->getValue ( self::CONFIG_PATH_LPEXPRESS_COURIER_METHOD );
                break;
            case 'lpcarrier_lpcarrierlpexpress_overseas':
                $shippingType = $this->_config->getValue ( self::CONFIG_PATH_LPEXPRESS_OVERSEAS_METHOD );
                break;
            case 'lpcarrier_lpcarrierlpexpress_terminal':
                $shippingType = $this->_config->getValue ( self::CONFIG_PATH_LPEXPRESS_TERMINAL_METHOD );
                break;
            case 'lpcarrier_lpcarrierlpexpress_postoffice':
                $shippingType = $this->_config->getValue ( self::CONFIG_PATH_LPEXPRESS_POSTOFFICE_METHOD );
                break;
            case 'lpcarrier_lpcarrierlp_postoffice':
                $shippingType = $this->_config->getValue ( self::CONFIG_PATH_LP_POSTOFFICE_SIZE );

                if ( $shippingType == 'S' ) {
                    $shippingType = $this->_config->getValue ( self::CONFIG_PATH_LP_POSTOFFICE_TYPE_SM );
                    $shippingType = 'SMALL_' . $shippingType;
                }

                if ( $shippingType == 'M' ) {
                    $shippingType = $this->_config->getValue ( self::CONFIG_PATH_LP_POSTOFFICE_TYPE_SM );
                    $shippingType = 'BIG_' . $shippingType;
                }

                if ( $shippingType == 'L' ) {
                    $shippingType = $this->_config->getValue ( self::CONFIG_PATH_LP_POSTOFFICE_TYPE_L );
                }
                break;
            case 'lpcarrier_lpcarrierlp_overseas':
                $shippingType = $this->_config->getValue ( self::CONFIG_PATH_LP_OVERSEAS_SIZE );

                if ( $shippingType == 'S' ) {
                    $shippingType = $this->_config->getValue ( self::CONFIG_PATH_LP_OVERSEAS_TYPE_SM );
                    if ( $shippingType == 'CORESPONDENCE' ) {
                        $shippingType = 'SMALL_CORESPONDENCE';
                    }

                    if ( $shippingType == 'CORESPONDENCE_TRACKED' ) {
                        $shippingType = 'SMALL_CORESPONDENCE_TRACKED';
                    }
                }

                if ( $shippingType == 'M' ) {
                    $shippingType = $this->_config->getValue ( self::CONFIG_PATH_LP_OVERSEAS_TYPE_SM );

                    if ( $shippingType == 'CORESPONDENCE' ) {
                        $shippingType = 'BIG_CORESPONDENCE';
                    }

                    if ( $shippingType == 'CORESPONDENCE_TRACKED' ) {
                        $shippingType = 'MEDIUM_CORESPONDENCE_TRACKED';
                    }
                }

                if ( $shippingType == 'L' ) {
                    $shippingType = $this->_config->getValue ( self::CONFIG_PATH_LP_OVERSEAS_TYPE_L );
                }
                break;
        }

        return $shippingType;
    }

    /**
     * Check if is any LP method
     * @param string $method
     * @return bool
     */
    public function isLpMethod ( $method )
    {
        return in_array ( $method,
            [
                'lpcarrier_lpcarrierlpexpress_courier',
                'lpcarrier_lpcarrierlpexpress_overseas',
                'lpcarrier_lpcarrierlpexpress_terminal',
                'lpcarrier_lpcarrierlpexpress_postoffice',
                'lpcarrier_lpcarrierlp_postoffice',
                'lpcarrier_lpcarrierlp_overseas'
            ]
        );
    }

    /**
     * Check if lpexpress method
     * @param $method
     * @return bool
     */
    public function isLpExpressMethod ( $method )
    {
        return in_array ( $method, [
            'lpcarrier_lpcarrierlpexpress_courier',
            'lpcarrier_lpcarrierlpexpress_overseas',
            'lpcarrier_lpcarrierlpexpress_terminal',
            'lpcarrier_lpcarrierlpexpress_postoffice'
        ] );
    }

    /**
     * Check if manual courier call alowwed and is lpExpress method
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    public function isCallCourierAllowed ( $order )
    {
        // If LP Express method but is CHCA
        if ( $order->getLpShippingType () === 'CHCA' )
            return false;

        return $this->isLpExpressMethod ( $order->getShippingMethod () ) &&
            !$this->getCallCourierAutomatically ();
    }

    /**
     * Get shipping size by method
     * @param $method
     * @return mixed|null
     */
    public function getShippingSize ( $method )
    {
        $shippingSize = null;

        switch ( $method ) {
            case 'lpcarrier_lpcarrierlpexpress_courier':
                // EBIN does not have size
                if ( $this->getShippingType ( $method ) !== 'EBIN' ) {
                    $shippingSize = $this->_config->getValue ( self::CONFIG_PATH_LPEXPRESS_COURIER_SIZE );
                }
                break;
            case 'lpcarrier_lpcarrierlpexpress_overseas':
                // EBIN does not have size
                if ( $this->getShippingType ( $method ) !== 'EBIN' ) {
                    $shippingSize = $this->_config->getValue ( self::CONFIG_PATH_LPEXPRESS_OVERSEAS_SIZE );
                }
                break;
            case 'lpcarrier_lpcarrierlpexpress_terminal':
                $shippingSize = $this->_config->getValue ( self::CONFIG_PATH_LPEXPRESS_TERMINAL_SIZE );
                break;
        }

        return $shippingSize;
    }

    /**
     * Get label size
     * @return mixed
     */
    public function getLabelSize ()
    {
        return $this->_config->getValue (
            self::CONFIG_PATH_LABEL_SIZE,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return string
     */
    public function getConsigmentFormation ()
    {
        return $this->_config->getValue (
            self::CONFIG_PATH_CONSIGMENT_FORMATION,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Get delivery time
     * @return mixed
     */
    public function getLPPostOfficeDeliveryTime ()
    {
        return $this->_config->getValue (
            self::CONFIG_PATH_LP_POSTOFFICE_DELIVERY,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Get delivery time
     * @return mixed
     */
    public function getLPExpressCourierDeliveryTime ()
    {
        return $this->_config->getValue (
            self::CONFIG_PATH_LPEXPRESS_COURIER_DELIVERY,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return mixed
     */
    public function getLPExpressTerminalDeliveryTime ()
    {
        return $this->_config->getValue (
            self::CONFIG_PATH_LPEXPRESS_TERMINAL_DELIVERY,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return mixed
     */
    public function getLPExpressPostOfficeDeliveryTime ()
    {
        return $this->_config->getValue (
            self::CONFIG_PATH_LPEXPRESS_POSTOFFICE_DELIVERY,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return mixed
     */
    public function getLPOverseasDeliveryTime ()
    {
        return $this->_config->getValue (
            self::CONFIG_PATH_LP_OVERSEAS_DELIVERY,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return mixed
     */
    public function getLPExpressOverseasDeliveryTime ()
    {
        return $this->_config->getValue (
            self::CONFIG_PATH_LPEXPRESS_OVERSEAS_DELIVERY,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }
}

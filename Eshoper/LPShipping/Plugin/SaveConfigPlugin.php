<?php
/**
 * @package    Eshoper/LPShipping/Plugin/Checkout
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Plugin;

use Eshoper\LPShipping\Api\Data\LPExpressTerminalsInterface;

class SaveConfigPlugin
{
    /**
     * @var \Eshoper\LPShipping\Helper\ApiHelper $_apiHelper
     */
    protected $_apiHelper;

    /**
     * @var \Eshoper\LPShipping\Model\Config $_config
     */
    protected $_config;

    /**
     * @var \Eshoper\LPShipping\Model\ApiTokenFactory $_apiTokenFactory
     */
    protected $_apiTokenFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface $_messageManager
     */
    protected $_messageManager;

    /**
     * @var \Eshoper\LPShipping\Api\Data\LPExpressTerminalsInterfaceFactory $_terminalsFactory
     */
    protected $_terminalsFactory;

    /**
     * @var \Eshoper\LPShipping\Api\LPExpressTerminalRepositoryInterface $_terminalRepository
     */
    protected $_terminalRepository;

    /**
     * @var \Eshoper\LPShipping\Model\LPCountriesFactory $_countriesFactory
     */
    protected $_countriesFactory;


    /**
     * @var \Eshoper\LPShipping\Api\Data\ShippingTemplatesInterfaceFactory $_shippingTemplatesFactory
     */
    protected $_shippingTemplatesFactory;

    /**
     * @var \Eshoper\LPShipping\Api\ShippingTemplateRepositoryInterface $_shippingTemplatesRepository
     */
    protected $_shippingTemplatesRepository;

    /**
     * @var \Magento\Framework\App\ResourceConnection $_resourceConnection
     */
    protected $_resourceConnection;

    /**
     * SaveConfigPlugin constructor.
     * @param \Eshoper\LPShipping\Helper\ApiHelper $apiHelper
     * @param \Eshoper\LPShipping\Model\Config $config
     * @param \Eshoper\LPShipping\Model\ApiTokenFactory $apiTokenFactory
     * @param \Eshoper\LPShipping\Api\Data\LPExpressTerminalsInterfaceFactory $terminalsFactory
     * @param \Eshoper\LPShipping\Api\LPExpressTerminalRepositoryInterface $terminalRepository
     * @param \Eshoper\LPShipping\Model\LPCountriesFactory $countriesFactory
     * @param \Eshoper\LPShipping\Api\Data\ShippingTemplatesInterfaceFactory $shippingTemplatesFactory
     * @param \Eshoper\LPShipping\Api\ShippingTemplateRepositoryInterface $shippingTemplatesRepository
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(
        \Eshoper\LPShipping\Helper\ApiHelper $apiHelper,
        \Eshoper\LPShipping\Model\Config $config,
        \Eshoper\LPShipping\Model\ApiTokenFactory $apiTokenFactory,
        \Eshoper\LPShipping\Api\Data\LPExpressTerminalsInterfaceFactory $terminalsFactory,
        \Eshoper\LPShipping\Api\LPExpressTerminalRepositoryInterface $terminalRepository,
        \Eshoper\LPShipping\Model\LPCountriesFactory $countriesFactory,
        \Eshoper\LPShipping\Api\Data\ShippingTemplatesInterfaceFactory $shippingTemplatesFactory,
        \Eshoper\LPShipping\Api\ShippingTemplateRepositoryInterface $shippingTemplatesRepository,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->_apiHelper                   = $apiHelper;
        $this->_config                      = $config;
        $this->_apiTokenFactory             = $apiTokenFactory;
        $this->_messageManager              = $messageManager;
        $this->_terminalsFactory            = $terminalsFactory;
        $this->_terminalRepository          = $terminalRepository;
        $this->_countriesFactory            = $countriesFactory;
        $this->_shippingTemplatesFactory    = $shippingTemplatesFactory;
        $this->_shippingTemplatesRepository = $shippingTemplatesRepository;
        $this->_resourceConnection          = $resourceConnection;
    }

    /**
     * Truncate table to clear old data
     * @param $tableName
     */
    private function truncateTable ( $tableName )
    {
        $connection = $this->_resourceConnection->getConnection ();
        $connection->query (
            sprintf ( 'TRUNCATE %s',
                $this->_resourceConnection->getTableName ( $tableName )
            )
        );
    }

    /**
     * Save access token from API
     * @param $username
     * @param $password
     * @return bool
     * @throws \Zend_Http_Client_Exception
     */
    private function saveAccessToken ( $username, $password )
    {
        if ( $accessTokenObject = $this->_apiHelper->requestAccessToken (
            $username, $password
        ) ) {
            // Truncate api credentials
            $this->truncateTable ( $this->_config->getApiTokenDbTableName () );

            // Set timezone
            date_default_timezone_set ( 'Europe/Vilnius' );

            /** @var \Eshoper\LPShipping\Model\ApiToken $apiTokenModel */
            $apiTokenModel = $this->_apiTokenFactory->create ();
            $apiTokenModel
                ->setAccessToken ( $accessTokenObject->access_token )
                ->setRefreshToken ( $accessTokenObject->refresh_token )
                ->setExpires ( date ( 'Y-m-d H:i:s',
                    time () + $accessTokenObject->expires_in ) )
                ->save ();

            return true;
        }

        return false;
    }

    /**
     * Save LP EXPRESS terminals from API
     * @throws \Zend_Http_Client_Exception
     */
    private function saveLPExpressTerminalList ()
    {
        if ( $terminalList = $this->_apiHelper->getLPExpressTerminalList () ) {
            // Truncate LP EXPRESS terminal list
            $this->truncateTable ( $this->_config->getTerminalDbTableName () );

            foreach ( $terminalList as $terminalAPI ) {
                /** @var \Eshoper\LPShipping\Model\LPExpressTerminals $terminal */
                $terminal = $this->_terminalsFactory->create ();
                $terminal->setTerminalId ( $terminalAPI->id )
                    ->setName ( $terminalAPI->name )
                    ->setAddress ( $terminalAPI->address )
                    ->setCity ( $terminalAPI->city );

                $this->_terminalRepository->save ( $terminal );
            }
        }
    }

    /**
     * Save LP available countries list from API
     * @throws \Zend_Http_Client_Exception
     */
    private function saveAvailableCountryList ()
    {
        if ( $countryList = $this->_apiHelper->getAvailableCountryList () ) {
            // Truncate LP available country list
            $this->truncateTable ( 'lp_country_list' );

            foreach ( $countryList as $country ) {
                /** @var \Eshoper\LPShipping\Model\LPCountries $countryListModel */
                $countryListModel = $this->_countriesFactory->create ();
                $countryListModel->setCountryId ( $country->id )
                    ->setCountryCode ( $country->code )
                    ->setCountry ( $country->country )
                    ->save ();
            }
        }
    }

    /**
     * Save shipping templates JSON
     * @throws \Zend_Http_Client_Exception
     */
    public function saveShippingTemplates ()
    {
        if ( $shippingTemplates = $this->_apiHelper->getShippingTemplates () ) {
            $this->truncateTable ( 'lp_shipping_template' );

            /** @var \Eshoper\LPShipping\Api\Data\ShippingTemplatesInterface $shippingTemplatesFactory */
            $shippingTemplatesFactory = $this->_shippingTemplatesFactory->create ();
            $shippingTemplatesFactory->setShippingTemplates ( $shippingTemplates );

            $this->_shippingTemplatesRepository->save ( $shippingTemplatesFactory );
        }
    }

    /**
     * Before saving module settings check if credentials
     * Are correct and try to fetch access token. If it is successful
     * Then fetch terminal list and available countries from API
     * Also set module status to active so it can proceed it's purpose
     * @param \Magento\Config\Model\Config $subject
     * @throws \Zend_Http_Client_Exception
     */
    public function beforeSave (
        \Magento\Config\Model\Config $subject
    ) {
        if ( key_exists ( 'lpcarrier', $subject->getData ()[ 'groups' ] ) ) {
            // Is NEW value enabled
            $enabled = $subject->getData()[ 'groups' ][ 'lpcarrier' ]
            [ 'fields' ][ 'active' ][ 'value' ];

            if ( $subject->getSection () === 'carriers' && $enabled ) {
                // API username - new value ( not saved )
                $apiUsername = $subject->getData()[ 'groups' ][ 'lpcarrier' ][ 'groups' ][ 'lpcarrierapi' ]
                [ 'fields' ][ 'api_login' ][ 'value' ];

                // API password - new value ( not saved )
                $apiPassword = $subject->getData()[ 'groups' ][ 'lpcarrier' ][ 'groups' ][ 'lpcarrierapi' ]
                [ 'fields' ][ 'api_password' ][ 'value' ];

                // Test mode - new value ( not saved )
                $testMode = $subject->getData()[ 'groups' ][ 'lpcarrier' ][ 'groups' ][ 'lpcarrierapi' ]
                [ 'fields' ][ 'test_mode' ][ 'value' ];

                // Only if new API values presented
                if ( $this->_config->getApiUsername () != $apiUsername
                    || $this->_config->getApiPassword () != $apiPassword
                    || $this->_config->getIsTestMode () != $testMode ) {
                    // Fetch and save API access token
                    if ( $this->saveAccessToken ( $apiUsername, $apiPassword ) ) {
                        // Fetch and save terminal list from API
                        $this->saveLPExpressTerminalList ();

                        // Fetch and save available country list from API
                        $this->saveAvailableCountryList ();

                        // Fetch and save shipping templates
                        $this->saveShippingTemplates ();

                        // Set module status to active
                        $this->_config->setStatus ( true );
                    } else {
                        // Set module status to inactive
                        $this->_config->setStatus ( false );
                    }
                }
            }
        }
    }

    /**
     * After successful module configuration save
     * Verify sender postcode and city
     * @param \Magento\Config\Model\Config $subject
     * @param \Magento\Config\Model\Config\Interceptor $interceptor
     * @throws \Zend_Http_Client_Exception
     */
    public function afterSave (
        \Magento\Config\Model\Config $subject,
        \Magento\Config\Model\Config\Interceptor $interceptor
    ) {
        if ( $subject->getSection() === 'carriers' && $this->_config->isEnabled () ) {
            if ( $this->_config->getStatus () ) {
                // Validate sender postcode
                if ( $this->_config->getSenderPostCode () != null ) {
                    $this->_apiHelper->verifySenderPostCode ();
                }

                // Validate sender city
                if ( $this->_config->getSenderPostCode () != null ) {
                    $this->_apiHelper->verifySenderCity ();
                }
            }
        }
    }
}

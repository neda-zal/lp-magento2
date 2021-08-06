<?php
/**
 * @package    Eshoper/LPShipping/Helper
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Helper;


class ApiHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Authentication gateways
     */
    const AUTH_TEST_GATEWAY = 'https://api-manosiuntostst.post.lt/oauth/token';
    const AUTH_DEFAULT_GATEWAY = 'https://api-manosiuntos.post.lt/oauth/token';

    /**
     * Api gateways
     */
    const TEST_GATEWAY = 'https://api-manosiuntostst.post.lt/api/v1';
    const DEFAULT_GATEWAY = 'https://api-manosiuntos.post.lt/api/v1';

    /**
     * @var \Magento\Framework\HTTP\Client\Curl $_clientFactory
     */
    protected $_client;

    /**
     * @var \Eshoper\LPShipping\Model\Config $_config
     */
    protected $_config;

    /**
     * @var \Magento\Framework\Message\ManagerInterface $_messageManger
     */
    protected $_messageManger;

    /**
     * @var \Eshoper\LPShipping\Model\ApiTokenFactory $_apiTokenFactory
     */
    protected $_apiTokenFactory;

    /**
     * ApiHelper constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\HTTP\Client\Curl $client
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Eshoper\LPShipping\Model\Config $config
     * @param \Eshoper\LPShipping\Model\ApiTokenFactory $apiTokenFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\HTTP\Client\Curl $client,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Eshoper\LPShipping\Model\Config $config,
        \Eshoper\LPShipping\Model\ApiTokenFactory $apiTokenFactory
    ) {
        $this->_client = $client;
        $this->_config = $config;
        $this->_messageManger = $messageManager;
        $this->_apiTokenFactory = $apiTokenFactory;

        parent::__construct($context);
    }

    /**
     * Get API gateway depending on test mode setting
     * @return string
     */
    private function getApiGateway ()
    {
        return $this->_config->getIsTestMode () ? self::TEST_GATEWAY : self::DEFAULT_GATEWAY;
    }

    /**
     * Get API authorization gateway depending on test mode setting
     * @return string
     */
    private function getAuthGateway ()
    {
        return $this->_config->getIsTestMode () ? self::AUTH_TEST_GATEWAY : self::AUTH_DEFAULT_GATEWAY;
    }

    /**
     * @param $params
     * @return \Magento\Framework\HTTP\Client\Curl
     */
    private function tokenRequest ( $params )
    {
        $this->_client->setOption (CURLOPT_HEADER, 0 );
        $this->_client->setOption (CURLOPT_TIMEOUT, 120 );
        $this->_client->setOption (CURLOPT_RETURNTRANSFER, true );

        $this->_client->addHeader ( 'Accept', 'application/json' );
        $this->_client->post ( $this->getAuthGateway (), $params );

        return $this->_client;
    }


    /**
     * Send authorized request to API endpoint
     *
     * @param $endpoint
     * @param $params
     * @param string $requestMethod
     * @return \Magento\Framework\HTTP\Client\Curl
     */
    private function doRequest ( $endpoint, $params = [], $requestMethod = \Zend_Http_Client::GET )
    {
        try {
            $this->_client->setOption (CURLOPT_HEADER, 0 );
            $this->_client->setOption (CURLOPT_TIMEOUT, 120 );
            $this->_client->setOption (CURLOPT_RETURNTRANSFER, true );
            $this->_client->setOption (CURLOPT_CUSTOMREQUEST, $requestMethod );

            $this->_client->addHeader ( 'Content-Type', 'application/json' );
            $this->_client->addHeader ( 'Accept', 'application/json' );
            $this->_client->addHeader ('Authorization',
                sprintf ( '%s %s', 'Bearer', $this->getAccessToken () )
            );

            if ( $requestMethod !== \Zend_Http_Client::POST && $requestMethod !== \Zend_Http_Client::DELETE ) {
                $this->_client->get ( sprintf ( '%s/%s/?%s', $this->getApiGateway (), $endpoint,
                    http_build_query ( $params ) ) );
            } else {
                $this->_client->post ( sprintf ( '%s/%s', $this->getApiGateway (), $endpoint ),
                    json_encode ( $params ) );
            }

            return $this->_client;
        } catch ( \Exception $e ) {
            $this->_messageManger->addErrorMessage ( $e->getMessage () );
        }
    }

    /**
     * Get LP EXPRESS full terminal list
     * @return mixed
     */
    public function getLPExpressTerminalList ()
    {
        $request = $this->doRequest ( 'address/terminals', [ 'size' => 999 ] );

        if ( $request ) {
            $requestResult = json_decode ( $request->getBody () );
            if ( $request->getStatus() === 200 ) {
                return $requestResult;
            } else {
                // Throw error if occurs
                $this->_messageManger->addErrorMessage ( $requestResult->error_description );
            }
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getShippingTemplates ()
    {
        $request = $this->doRequest ( 'shipping/shippingItemTemplates', [],
            \Zend_Http_Client::OPTIONS );

        if ( $request ) {
            if ( $request->getStatus () === 200 ) {
                return $request->getBody ();
            } else {
                $this->_messageManger->addErrorMessage ( __( 'Something wen\'t wrong.' ) );
            }
        }

        return null;
    }

    /**
     * Get available country list
     * @return mixed|null
     */
    public function getAvailableCountryList ()
    {
        $request = $this->doRequest ( 'address/country', [ 'size' => 999 ] );

        if ( $request ) {
            $requestResult = json_decode ( $request->getBody () );
            if ( $request->getStatus() === 200 ) {
                return $requestResult;
            } else {
                // Throw error if occurs
                $this->_messageManger->addErrorMessage ( $requestResult->error_description );
            }
        }

        return null;
    }

    /**
     * @param $requestData
     * @return int|null
     * @throws \Exception
     */
    public function createShippingItem ( $requestData )
    {
        $request = $this->doRequest ( 'shipping', $requestData,
            \Zend_Http_Client::POST );

        if ( $request ) {
            if ( $request->getStatus () === 200 ) {
                $response = json_decode ( $request->getBody () );

                if ( !$response || !property_exists ( $response, 'id' ) ) {
                    throw new \Exception (
                      __( 'Something wen\'t wrong. Please try again later.' )
                    );
                }

                return $response->id;
            } else {
                throw new \Exception (
                    __( $request->getBody () )
                );
            }
        }

        return null;
    }

    /**
     * Get barcode
     * @param $shippingItemId
     * @return string|null
     */
    public function getBarcode ( $shippingItemId )
    {
        $request = $this->doRequest ( sprintf ( '%s/%s', 'shipping', $shippingItemId ), [],
            \Zend_Http_Client::GET );

        if ( $request ) {
            if ( $request->getStatus () === 200 ) {
                $response = json_decode ( $request->getBody () );
                return $response->barcode;
            } else {
                $this->_messageManger->addErrorMessage ( __( $request->getBody () ) );
            }
        }

        return null;
    }

    /**
     * @param array $shippingItemIds
     * @return string
     */
    public function initiateShipping ( $shippingItemIds )
    {
        $request = $this->doRequest ('shipping/initiate', $shippingItemIds,
                \Zend_Http_Client::POST );

        if ( $request ) {
            if ( $request->getStatus () === 200 ) {
                $cartId = json_decode ( $request->getBody (), true );
                return $cartId [ 0 ];
            } else {
                $this->_messageManger->addErrorMessage ( $request->getBody () );
            }
        }

        return null;
    }

    /**
     * @param $itemId
     * @return string|null
     */
    public function createSticker ( $itemId )
    {
        $request = $this->doRequest ( 'documents/item/sticker', [
                'itemId' => $itemId,
                'layout' => $this->_config->getLabelSize ()
            ] );

        if ( $request ) {
            if ( $request->getStatus () === 200 ) {
                $labelContent = json_decode ( $request->getBody (), true );

                return $labelContent != null && !empty ( $labelContent )
                    ? $labelContent [ 0 ][ 'label' ] : null;
            }
        }

        return null;
    }

    /**
     * @param $itemId
     * @return bool
     */
    public function cancelLabel ( $itemId )
    {
        $request = $this->doRequest ( sprintf ( '%s/%s', 'shipping', $itemId ), [],
            \Zend_Http_Client::DELETE );

        return $request && $request->getStatus () == 200;
    }

    /**
     * @param $cartId
     * @return mixed
     */
    public function getManifest ( $cartId )
    {
        $request = $this->doRequest ( sprintf ( 'documents/cart/%s/manifest', $cartId ),
            [] );

        if ( $request ) {
            if ( $request->getStatus () === 200 ) {
                $manifestContent = json_decode ( $request->getBody () );

                if ( !$manifestContent ) {
                    return null;
                }

                return $manifestContent->document;
            }
        }

        return null;
    }

    /**
     * @param $cartId
     * @throws \Exception
     * @return mixed
     */
    public function getCN23Form ( $cartId )
    {
        $request = $this->doRequest ( sprintf ( 'documents/cart/%s/cn23', $cartId ),
            [] );

        if ( $request ) {
            if ( $request->getStatus () === 200 ) {
                $CN23FormContent = json_decode ( $request->getBody () );
                return $CN23FormContent != null &&
                    property_exists ( $CN23FormContent, 'document' ) ?
                        $CN23FormContent->document : null;
            } else {
                throw new \Exception (
                    __( $request->getBody () )
                );
            }
        }

        return null;
    }

    /**
     * @param array $shippingItemId
     * @return bool
     */
    public function callCourier ( $shippingItemIds )
    {
        $request = $this->doRequest ( 'shipping/courier/call', $shippingItemIds,
            \Zend_Http_Client::POST );

        if ( $request ) {
            if ( $request->getStatus () === 200 ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Used for tracking cronjob
     * @param $barcode
     * @return mixed
     */
    public function getTracking ( $barcode )
    {
        $request = $this->doRequest ( sprintf ( '%s/%s', 'tracking/byBarcode', $barcode ),
            [] );

        if ( $request ) {
            if ( $request->getStatus () === 200 ) {
                if ( $tracking = json_decode ( $request->getBody () ) ) {
                    return $tracking != null && property_exists ( $tracking, 'state' ) &&
                        $tracking->state != 'STATE_NOT_FOUND' ? $tracking : null;
                }

                return null;
            }
        }

        return null;
    }

    /**
     * Verify sender postcode
     * @param $postalCode
     * @param $countryId
     * @return bool
     */
    public function verifySenderPostCode ()
    {
        // Request countryId should be captured from API
        $request = $this->doRequest ( 'address/verification', [
            'countryId' => $this->_config->getSenderCountryId (),
            'locality' => '-',
            'street' => '-',
            'building' => '-',
            'postalCode' => $this->_config->getSenderPostCode ()
        ], \Zend_Http_Client::POST );

        if ( $request ) {
            // Post code invalid
            if ( $request->getStatus () !== 200 ) {
                // Error thrown
                $this->_messageManger->addErrorMessage (
                    $request->getBody ()
                );
            }
        }
    }


    public function verifySenderCity ()
    {
        $request = $this->doRequest ( sprintf ( 'address/country/%s/locality',
            $this->_config->getSenderCountryId()
        ), [ 'keyword' => $this->_config->getSenderCity () ] );

        if ( $request ) {
            $response = json_decode ( $request->getBody () );

            if ( $request->getStatus () == 200 ) {
                // If empty response means city is not valid
                if ( empty ( $response ) ) {
                    $this->_messageManger->addErrorMessage ( __( 'Sender city is not valid.' ) );
                }
            } else {
                // Some exception thrown
                $this->_messageManger->addErrorMessage ( __( 'Something wen\'t wrong. Please try again.' ) );
            }
        }
    }

    /**
     * Refresh token
     */
    public function requestRefreshToken ()
    {
        /** @var \Eshoper\LPShipping\Model\ApiToken $apiTokenModel */
        $apiTokenModel = $this->_apiTokenFactory->create ()->load ( 1 );

        // If token expires
        date_default_timezone_set ( 'Europe/Vilnius' );

        if ( strtotime ( $apiTokenModel->getExpires () ) - 1000 < strtotime ( date ( 'Y-m-d H:i:s' ) ) ) {
            // Send refresh token request
            $authResponse = $this->tokenRequest ( [
                'scope' => 'read+write',
                'grant_type' => 'refresh_token',
                'clientSystem' => 'PUBLIC',
                'refresh_token' => $apiTokenModel->getRefreshToken ()
            ] );

            if ( $authResponse->getStatus () === 200 ) {
                $accessTokenObject = json_decode ( $authResponse->getBody () );

                if ( property_exists ( $accessTokenObject, 'access_token' ) ) {
                    // Save refreshed token
                    $apiTokenModel->setAccessToken ( $accessTokenObject->access_token )
                        ->setRefreshToken ( $accessTokenObject->refresh_token )
                        ->setExpires ( date('Y-m-d H:i:s',
                            time() + $accessTokenObject->expires_in ) )
                        ->setUpdated ( date ( 'Y-m-d H:i:s' ) )
                        ->save ();
                } else {
                    // Throw error if no access token received
                    $this->_messageManger->addErrorMessage ( __( 'Error authorization' ) );
                }
            } else {
                // Throw error if occurs
                $this->_messageManger->addErrorMessage ( __( 'Error authorization' ) );
            }
        }
    }

    /**
     * @param $username
     * @param $password
     * @return mixed|null
     * @throws \Zend_Http_Client_Exception
     */
    public function requestAccessToken ( $username, $password )
    {
        $response = $this->tokenRequest ( [
            'scope' => 'read+write',
            'grant_type' => 'password',
            'clientSystem' => 'PUBLIC',
            'username' => $username,
            'password' => $password
        ] );

        if ( $response->getStatus () === 200 ) {
            $authResponse = json_decode ( $response->getBody () );

            if ( property_exists ( $authResponse, 'access_token' ) ) {
                // Return access token object
                return $authResponse;
            }
        } else {
            // Throw error if occurs
            $this->_messageManger->addErrorMessage ( __( 'Error authorization' ) );
        }

        return null;
    }

    /**
     * Get API access token from database
     * @return mixed
     */
    public function getAccessToken ()
    {
        /** @var \Eshoper\LPShipping\Model\ApiToken $apiTokenModel */
        $apiTokenModel = $this->_apiTokenFactory->create ()->load ( 1 );

        return $apiTokenModel->getAccessToken ();
    }
}

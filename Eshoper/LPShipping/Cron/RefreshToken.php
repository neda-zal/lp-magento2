<?php
/**
 * @package    Eshoper/LPShipping/Cron
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Cron;


class RefreshToken
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
     * RefreshToken constructor.
     * @param \Eshoper\LPShipping\Helper\ApiHelper $apiHelper
     * @param \Eshoper\LPShipping\Model\Config $config
     */
    public function __construct(
        \Eshoper\LPShipping\Helper\ApiHelper $apiHelper,
        \Eshoper\LPShipping\Model\Config $config
    ) {
        $this->_apiHelper = $apiHelper;
        $this->_config = $config;
    }

    /**
     * Execute refresh token
     * @throws \Zend_Http_Client_Exception
     */
    public function execute ()
    {
        if ( $this->_config->isEnabled () ) {
            $this->_apiHelper->requestRefreshToken ();
        }
    }
}

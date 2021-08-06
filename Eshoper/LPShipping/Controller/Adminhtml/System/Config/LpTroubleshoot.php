<?php
/**
 * @package    Eshoper/LPShipping/Controller/Adminhtml/System/Config
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Controller\Adminhtml\System\Config;

class LpTroubleshoot extends \Magento\Config\Controller\Adminhtml\System\AbstractConfig
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
     * @var \Magento\Framework\App\ResourceConnection $_resourceConnection
     */
    protected $_resourceConnection;

    /**
     * @var \Magento\Framework\Message\ManagerInterface $_messageManager
     */
    protected $_messageManager;

    /**
     * @var \Eshoper\LPShipping\Model\ApiTokenFactory $_apiTokenFactory
     */
    protected $_apiTokenFactory;

    /**
     * Troubleshoot constructor.
     * @param \Eshoper\LPShipping\Helper\ApiHelper $apiHelper
     * @param \Eshoper\LPShipping\Model\ApiTokenFactory $apiTokenFactory
     * @param \Eshoper\LPShipping\Model\Config $config
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Config\Model\Config\Structure $configStructure
     * @param $sectionChecker
     */
    public function __construct (
        \Eshoper\LPShipping\Helper\ApiHelper $apiHelper,
        \Eshoper\LPShipping\Model\ApiTokenFactory $apiTokenFactory,
        \Eshoper\LPShipping\Model\Config $config,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Config\Model\Config\Structure $configStructure
    ) {
        $this->_apiHelper           = $apiHelper;
        $this->_config              = $config;
        $this->_resourceConnection  = $resourceConnection;
        $this->_messageManager      = $messageManager;
        $this->_apiTokenFactory     = $apiTokenFactory;
        parent::__construct( $context, $configStructure, null );
    }

    /**
     * Truncate table
     * @param $tableName
     */
    protected function truncateTable ( $tableName )
    {
        $connection = $this->_resourceConnection->getConnection ();
        $connection->query (
            sprintf ( 'TRUNCATE %s',
                $this->_resourceConnection->getTableName ( $tableName )
            )
        );
    }

    /**
     * First recreate API token
     * If system goes down API token won't work
     * In API and Module tokens won't be the same
     * @throws \Zend_Http_Client_Exception
     */
    protected function recreateToken ()
    {
        if ( $accessTokenObject = $this->_apiHelper->requestAccessToken (
            $this->_config->getApiUsername (), $this->_config->getApiPassword ()
        ) ) {
            // Truncate api credentials
            $this->truncateTable ( $this->_config->getApiTokenDbTableName () );

            // If token expires
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
     * @inheritDoc
     * @throws \Zend_Http_Client_Exception
     */
    public function execute()
    {
        if ( $this->recreateToken () ) {
            $this->_messageManager->addSuccessMessage (
                __( 'Troubleshoot complete.' )
            );
        } else {
            $this->messageManager->addErrorMessage (
                __( 'Error. Please check your credentials.' )
            );
        }

        $resultRedirect = $this->resultRedirectFactory->create ();
        $resultRedirect->setRefererUrl ();

        return $resultRedirect;
    }
}

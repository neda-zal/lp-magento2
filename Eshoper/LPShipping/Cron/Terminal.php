<?php
/**
 * @package    Eshoper/LPShipping/Cron
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Cron;


class Terminal
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
     * @var \Eshoper\LPShipping\Model\LPExpressTerminalsFactory $_terminalsFactory
     */
    protected $_terminalsFactory;

    /**
     * Terminal constructor.
     * @param \Eshoper\LPShipping\Helper\ApiHelper $apiHelper
     * @param \Eshoper\LPShipping\Model\Config $config
     * @param \Eshoper\LPShipping\Model\LPExpressTerminalsFactory $terminalsFactory
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct (
        \Eshoper\LPShipping\Helper\ApiHelper $apiHelper,
        \Eshoper\LPShipping\Model\Config $config,
        \Eshoper\LPShipping\Model\LPExpressTerminalsFactory $terminalsFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->_apiHelper = $apiHelper;
        $this->_config = $config;
        $this->_terminalsFactory = $terminalsFactory;
        $this->_resourceConnection = $resourceConnection;
    }

    /**
     * Delete all terminals from database
     */
    protected function truncateTerminals ()
    {
        $connection = $this->_resourceConnection->getConnection ();
        $connection->query ( 'TRUNCATE ' . $this->_resourceConnection->getTableName (
                $this->_config->getTerminalDbTableName ()
        ) );
    }

    /**
     * @throws \Exception
     */
    public function execute ()
    {
        if ( $this->_config->isEnabled () && $terminalList = $this->_apiHelper->getLPExpressTerminalList () ) {
            // Truncate LP EXPRESS terminal list
            $this->truncateTerminals ();

            foreach ( $terminalList as $terminal ) {
                /** @var \Eshoper\LPShipping\Model\LPExpressTerminals $terminalListModel */
                $terminalListModel = $this->_terminalsFactory->create ();
                $terminalListModel->setTerminalId ( $terminal->id )
                    ->setName ( $terminal->name )
                    ->setAddress ( $terminal->address )
                    ->setCity ( $terminal->city )
                    ->save ();
            }
        }
    }
}

<?php
/**
 * @package    Eshoper/LPShipping/Plugin/Adminhtml/Order/Shipment
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Plugin\Adminhtml\Order\Shipment;

class SavePlugin
{
    /**
     * @var \Eshoper\LPShipping\Model\Config $_config
     */
    protected $_config;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface $_orderRepository
     */
    protected $_orderRepository;

    /**
     * SavePlugin constructor.
     * @param \Eshoper\LPShipping\Model\Config $config
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     */
    public function __construct (
        \Eshoper\LPShipping\Model\Config $config,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->_config                  = $config;
        $this->_orderRepository         = $orderRepository;
    }

    public function afterExecute (
        \Magento\Shipping\Controller\Adminhtml\Order\Shipment\Save $subject,
        $result
    ) {
        $order_id = $subject->getRequest ()->getParam ( 'order_id' );
        $order = $this->_orderRepository->get ( $order_id );

        // Set custom order status
        $this->_config->setOrderStatus ( $order );
        $this->_orderRepository->save ( $order );

        return $result;
    }
}

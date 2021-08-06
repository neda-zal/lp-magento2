<?php
/**
 * @package    Eshoper/LPShipping/Model/Observer
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model\Observer;

class SaveOrderObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface $_objectManager
     */
    protected $_objectManager;

    /**
     * @var \Eshoper\LPShipping\Model\Config $_config
     */
    protected $_config;

    /**
     * SaveOrderObserver constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Eshoper\LPShipping\Model\Config $config
     */
    public function __construct (
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Eshoper\LPShipping\Model\Config $config
    ) {
        $this->_objectManager = $objectManager;
        $this->_config = $config;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute ( \Magento\Framework\Event\Observer $observer )
    {
        // Save LP EXPRESS terminal to order
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getOrder ();

        if ( $order->getShippingMethod() === 'lpcarrier_lpcarrierlpexpress_terminal' ) {
            /** @var \Magento\Quote\Model\QuoteRepository $quoteRepository */
            $quoteRepository = $this->_objectManager
                ->create ( 'Magento\Quote\Model\QuoteRepository' );
            $quote = $quoteRepository->get ( $order->getQuoteId () );
            $order->setLpexpressTerminal ( $quote->getLpexpressTerminal ()
                ?? $_POST [ 'order' ][ 'lpexpress_terminal' ] );
        }

//        if ( !$this->_config->isLpExpressMethod ( $order->getShippingMethod () ) ) {
//            $order->setStatus (
//                $this->_config::SHIPMENT_NOT_CREATED_STATUS
//            );
//        }

        // Save method size and type
        $order->setLpShippingType ( $this->_config->getShippingType ( $order->getShippingMethod () ) );
        $order->setLpShippingSize ( $this->_config->getShippingSize ( $order->getShippingMethod () ) );

        return $this;
    }
}

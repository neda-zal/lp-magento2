<?php
/**
 * @package    Eshoper/LPShipping/Model/Observer
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model\Observer;

class PaymentMethodObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Eshoper\LPShipping\Model\Config $_config
     */
    protected $_config;

    /**
     * @var \Eshoper\LPShipping\Helper\ShippingTemplate $_shippingTemplateHelper
     */
    protected $_shippingTemplateHelper;

    /**
     * PaymentPlugin constructor.
     * @param \Eshoper\LPShipping\Model\Config $config
     * @param \Eshoper\LPShipping\Helper\ShippingTemplate $shippingTemplateHelper
     */
    public function __construct (
        \Eshoper\LPShipping\Model\Config $config,
        \Eshoper\LPShipping\Helper\ShippingTemplate $shippingTemplateHelper
    ) {
        $this->_config                  = $config;
        $this->_shippingTemplateHelper  = $shippingTemplateHelper;
    }


    /**
     * @inheritDoc
     */
    public function execute ( \Magento\Framework\Event\Observer $observer )
    {
        $result          = $observer->getEvent ()->getResult ();
        $method_instance = $observer->getEvent ()->getMethodInstance ();

        $quote           = $observer->getEvent ()->getQuote ();

        if ( $quote ) {
            $shippingAddress  = $quote->getShippingAddress ();

            // Logic only if LP method selected
            if ( $this->_config->isLpMethod ( $shippingAddress->getShippingMethod () ) ) {
                // Get shipping template
                $template        = $this->_shippingTemplateHelper->getTemplate (
                    $this->_config->getShippingType ( $shippingAddress->getShippingMethod () ),
                    $this->_config->getShippingSize ( $shippingAddress->getShippingMethod () )
                );

                if ( $quote && $template ) {
                    if ( !$this->_shippingTemplateHelper->isCODAvailable ( $template [ 'id' ] ) ||
                        $shippingAddress->getCountryId () !== 'LT' ) {
                        // If COD not available disable
                        if ( $method_instance->getCode () == 'cashondelivery' ) {
                            $result->setData ( 'is_available', false );
                        }
                    }
                }
            }
        }
    }
}

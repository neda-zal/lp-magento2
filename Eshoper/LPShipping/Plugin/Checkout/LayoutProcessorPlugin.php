<?php
/**
 * @package    Eshoper/LPShipping/Plugin/Checkout
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Plugin\Checkout;

class LayoutProcessorPlugin
{
    /**
     * Call method after layout process
     *
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess (
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array  $jsLayout
    ) {
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shippingAdditional']['component'] = 'uiComponent';

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shippingAdditional']['displayArea'] = 'shippingAdditional';

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shippingAdditional']['children']['lpexpress_terminal'] = [
            'component' => 'Eshoper_LPShipping/js/methods/lpexpress',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'Eshoper_LPShipping/lpexpress',
                'options' => [],
            ],
            'dataScope' => 'shippingAddress.lpexpress_terminal',
            'label' => '',
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => [ 'select-terminal-required' => true ],
            'sortOrder' => 200,
        ];

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shippingAdditional']['children']['lp_delivery_time'] = [
            'component' => 'Eshoper_LPShipping/js/methods/lpexpress',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'Eshoper_LPShipping/delivery_time',
                'options' => [],
            ],
            'dataScope' => 'shippingAddress.lp_delivery_time',
            'label' => '',
            'provider' => 'checkoutProvider',
            'visible' => true,
            'sortOrder' => 300,
        ];

        return $jsLayout;
    }
}

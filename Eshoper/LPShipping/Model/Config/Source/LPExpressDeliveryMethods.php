<?php
/**
 * @package    Eshoper/LPShipping/Model/Config/Source
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model\Config\Source;


class LPExpressDeliveryMethods implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            [ 'value' => 'lpexpress_courier', 'label'       => __( 'Delivery To Home Or Office By Courier' ) ],
            [ 'value' => 'lpexpress_terminal', 'label'      => __( 'Delivery To Terminal' ) ],
            [ 'value' => 'lpexpress_postoffice', 'label'    => __( 'Delivery To Post Office' ) ],
            [ 'value' => 'lpexpress_overseas', 'label'      => __( 'Delivery To Overseas' ) ]
        ];
    }
}

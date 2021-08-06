<?php
/**
 * @package    Eshoper/LPShipping/Model/Config/Source
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model\Config\Source;

class AvailableServices implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray ()
    {
        return [
            [ 'value' => '0', 'label' => __( 'All' ) ],
            [ 'value' => '1', 'label' => __( 'Lithuanian Post' ) ],
            [ 'value' => '2', 'label' => __( 'LP EXPRESS' ) ],
        ];
    }
}

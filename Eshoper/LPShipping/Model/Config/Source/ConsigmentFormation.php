<?php
/**
 * @package    Eshoper/LPShipping/Model/Config/Source
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model\Config\Source;


class ConsigmentFormation implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            [ 'value' => 'priority', 'label' => __( 'Priority' ) ],
            [ 'value' => 'registered', 'label' => __( 'Registerd' ) ]
        ];
    }
}

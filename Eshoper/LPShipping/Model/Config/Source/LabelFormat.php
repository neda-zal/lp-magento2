<?php
/**
 * @package    Eshoper/LPShipping/Model/Config/Source
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model\Config\Source;


class LabelFormat implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            [ 'value' => 'LAYOUT_A4', 'label' => 'LAYOUT_A4' ],
            [ 'value' => 'LAYOUT_MAX', 'label' => 'LAYOUT_MAX' ],
            [ 'value' => 'LAYOUT_10x15', 'label' => 'LAYOUT_10x15' ]
        ];
    }
}

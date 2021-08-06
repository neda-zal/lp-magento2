<?php
/**
 * @package    Eshoper/LPShipping/Model/Config/Source
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model\Config\Source;


class LPShippingSizes implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            [ 'value' => 'SMALL_CORESPONDENCE', 'label' => 'S' ],
            [ 'value' => 'BIG_CORESPONDENCE', 'label' => 'M' ],
            [ 'value' => 'PACKAGE', 'label' => 'L' ],
        ];
    }

    public function toOverseasOptionArray ()
    {
        return [
            [ 'value' => 'SMALL_CORESPONDENCE', 'label' => 'S' ],
            [ 'value' => 'BIG_CORESPONDENCE', 'label' => 'M' ],
            [ 'value' => 'PACKAGE', 'label' => 'L' ],
            [ 'value' => 'SMALL_CORESPONDENCE_TRACKED', 'label' => 'SMALL_CORESPONDENCE_TRACKED - S' ],
            [ 'value' => 'MEDIUM_CORESPONDENCE_TRACKED', 'label' => 'MEDIUM_CORESPONDENCE_TRACKED - M' ]
        ];
    }
}

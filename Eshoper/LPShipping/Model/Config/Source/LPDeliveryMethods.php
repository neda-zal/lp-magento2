<?php
/**
 * @package    Eshoper/LPShipping/Model/Config/Source
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model\Config\Source;


class LPDeliveryMethods implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            [ 'value' => 'lp_postoffice', 'label' => __( 'Delivery To Home, Office Or Post Office' ) ],
            [ 'value' => 'lp_overseas', 'label' => __( 'Delivery To Overseas' ) ]
        ];
    }
  
    /**
     * @return array
     */
    public function toPostOfficeArraySM ()
    {
        return [
            [ 'value' => 'CORESPONDENCE', 'label' => __( 'Corespondence' ) ],
        ];
    }

    /**
     * @return array
     */
    public function toPostOfficeArrayL ()
    {
        return [
            [ 'value' => 'PACKAGE', 'label' => __( 'Package' ) ],
        ];
    }

    /**
     * @return array
     */
    public function toOverseasArraySM ()
    {
        return [
            [ 'value' => 'CORESPONDENCE', 'label' => __( 'Corespondence' ) ],
            [ 'value' => 'CORESPONDENCE_TRACKED', 'label' => __( 'Corespondence Tracked' ) ],
        ];
    }

    /**
     * @return array
     */
    public function toOverseasArrayL ()
    {
        return [
            [ 'value' => 'PACKAGE', 'label' => __( 'Package' ) ],
        ];
    }
}

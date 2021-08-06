<?php
/**
 * @package    Eshoper/LPShipping/Model/Api/Data
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Api\Data;


interface ShippingTemplatesInterface
{
    const SHIPPING_TEMPLATES = 'shipping_templates';

    /**
     * @param int $id
     * @return mixed
     */
    public function setId ( $id );

    /**
     * @return int
     */
    public function getId ();

    /**
     * @param string $shippingTemplates
     * @return string
     */
    public function setShippingTemplates ( $shippingTemplates );

    /**
     * @return string
     */
    public function getShippingTemplates ();
}

<?php
/**
 * @package    Eshoper/LPShipping/Model/Api
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Api;


interface ShippingTemplateRepositoryInterface
{
    /**
     * @param string $type
     * @param string|null $size
     * @return int
     */
    public function getShippingTemplate ( $type, $size = null );

    /**
     * @param Data\ShippingTemplatesInterface $shippingTemplates
     * @return Data\ShippingTemplatesInterface
     */
    public function save ( \Eshoper\LPShipping\Api\Data\ShippingTemplatesInterface $shippingTemplates );
}

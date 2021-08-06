<?php
/**
 * @package    Eshoper/LPShipping/Model
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model;


class ShippingTemplates extends \Magento\Framework\Model\AbstractModel
    implements \Eshoper\LPShipping\Api\Data\ShippingTemplatesInterface
{
    public function _construct ()
    {
        $this->_init ( \Eshoper\LPShipping\Model\ResourceModel\ShippingTemplates::class );
    }

    /**
     * @inheritDoc
     */
    public function setShippingTemplates ( $shippingTemplates )
    {
        return $this->setData ( self::SHIPPING_TEMPLATES, $shippingTemplates );
    }

    /**
     * @inheritDoc
     */
    public function getShippingTemplates()
    {
        return $this->getData ( self::SHIPPING_TEMPLATES );
    }
}

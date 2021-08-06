<?php
/**
 * @package    Eshoper/LPShipping/Model
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model;

use Eshoper\LPShipping\Api\Data;

class ShippingTemplateRepository implements \Eshoper\LPShipping\Api\ShippingTemplateRepositoryInterface
{
    /**
     * @var \Eshoper\LPShipping\Model\ShippingTemplatesFactory $_shippingTemplatesFactory
     */
    protected $_shippingTemplatesFactory;

    /**
     * @var \Eshoper\LPShipping\Model\ResourceModel\ShippingTemplates $_shippingTemplatesResource
     */
    protected $_shippingTemplatesResource;

    /**
     * ShippingTemplatesRepository constructor.
     * @param \Eshoper\LPShipping\Model\ShippingTemplatesFactory $shippingTemplatesFactory
     * @param \Eshoper\LPShipping\Model\ResourceModel\ShippingTemplates $shippingTemplatesResource
     */
    public function __construct (
        \Eshoper\LPShipping\Model\ShippingTemplatesFactory $shippingTemplatesFactory,
        \Eshoper\LPShipping\Model\ResourceModel\ShippingTemplates $shippingTemplatesResource
    ) {
        $this->_shippingTemplatesFactory = $shippingTemplatesFactory;
        $this->_shippingTemplatesResource = $shippingTemplatesResource;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getShippingTemplate ( $type, $size = null )
    {
        $shippingTemplates = $this->_shippingTemplatesFactory->create ();
        $this->_shippingTemplatesResource->load ( $shippingTemplates, 1, 'id' );

        /** @var \Eshoper\LPShipping\Api\Data\ShippingTemplatesInterface $shippingTemplates */
        if ( !$shippingTemplates->getId () ) {
            throw new \Magento\Framework\Exception\NoSuchEntityException (
                __( 'Unable to find Shipping Templates.' )
            );
        }

        // For tracked types fixtures
        if ( $type == 'SMALL_CORESPONDENCE_TRACKED' ) {
            $size = 'Small';
        }

        if ( $type == 'MEDIUM_CORESPONDENCE_TRACKED' ) {
            $size = 'Medium';
        }

        // Get shipping data from JSON
        if ( $shippingTemplatesData = json_decode ( $shippingTemplates->getShippingTemplates (), true ) ) {
            $template = array_filter ( $shippingTemplatesData, function ( $template ) use ( $type, $size ) {
                return $template [ 'type' ] == $type && @$template [ 'size' ] == $size;
            });

            $template = reset ( $template );

            return $template;
        } else {
            throw new \Magento\Framework\Exception\NoSuchEntityException (
                __( 'Unable to find Shipping Templates.' )
            );
        }
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save ( Data\ShippingTemplatesInterface $shippingTemplates )
    {
        /** @var \Eshoper\LPShipping\Model\ShippingTemplates $shippingTemplates */
        $this->_shippingTemplatesResource->save ( $shippingTemplates );
        return $shippingTemplates;
    }
}

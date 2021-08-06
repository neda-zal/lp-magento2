<?php
/**
 * @package    Eshoper/LPShipping/Model
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model;

class CN23Repository implements \Eshoper\LPShipping\Api\CN23RepositoryInterface
{
    /**
     * @var \Eshoper\LPShipping\Model\CN23Factory $_CN23Factory
     */
    protected $_CN23Factory;

    /**
     * @var \Eshoper\LPShipping\Model\ResourceModel\CN23
     */
    protected $_CN23Resource;

    /**
     * CN23Repository constructor.
     * @param CN23Factory $CN23Factory
     * @param ResourceModel\CN23 $CN23Resource
     */
    public function  __construct (
        \Eshoper\LPShipping\Model\CN23Factory $CN23Factory,
        \Eshoper\LPShipping\Model\ResourceModel\CN23 $CN23Resource
    ) {
        $this->_CN23Factory = $CN23Factory;
        $this->_CN23Resource = $CN23Resource;
    }

    /**
     * @inheritDoc
     */
    public function getById ( $id )
    {
        $CN23 = $this->_CN23Factory->create ();
        $this->_CN23Resource->load ( $CN23, $id );

        /** @var \Eshoper\LPShipping\Model\CN23 $CN23 */
        if ( !$CN23->getId () ) {
            throw new \Magento\Framework\Exception\NoSuchEntityException (
                __( 'Unable to find CN23 with ID "%1"', $id )
            );
        }

        return $CN23;
    }

    /**
     * @inheritDoc
     */
    public function getByOrderId ( $orderId )
    {
        $CN23 = $this->_CN23Factory->create ();
        $this->_CN23Resource->load ( $CN23, $orderId, 'order_id' );

        return $CN23;
    }

    /**
     * @param \Eshoper\LPShipping\Api\Data\CN23Interface $CN23
     * @return \Eshoper\LPShipping\Api\Data\CN23Interface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save( \Eshoper\LPShipping\Api\Data\CN23Interface $CN23 )
    {
        /** @var \Eshoper\LPShipping\Model\CN23 $CN23 */
        $this->_CN23Resource->save ( $CN23 );
        return $CN23;
    }
}

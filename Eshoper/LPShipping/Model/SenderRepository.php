<?php
/**
 * @package    Eshoper/LPShipping/Model
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model;

class SenderRepository implements \Eshoper\LPShipping\Api\SenderRepositoryInterface
{
    /**
     * @var \Eshoper\LPShipping\Model\SenderFactory $_senderFactory
     */
    protected $_senderFactory;

    /**
     * @var \Eshoper\LPShipping\Model\ResourceModel\Sender $_senderResource
     */
    protected $_senderResource;

    /**
     * SenderRepository constructor.
     * @param \Eshoper\LPShipping\Model\SenderFactory $senderFactory
     * @param \Eshoper\LPShipping\Model\ResourceModel\Sender $senderResource
     */
    public function __construct (
        \Eshoper\LPShipping\Model\SenderFactory $senderFactory,
        \Eshoper\LPShipping\Model\ResourceModel\Sender $senderResource
    ) {
        $this->_senderFactory   = $senderFactory;
        $this->_senderResource  = $senderResource;
    }

    /**
     * @inheritDoc
     */
    public function getById ( $id )
    {
        $sender = $this->_senderFactory->create ();
        $this->_senderResource->load ( $sender, $id );

        /** @var \Eshoper\LPShipping\Model\Sender $sender */
        if ( !$sender->getId () ) {
            throw new \Magento\Framework\Exception\NoSuchEntityException (
                __( 'Unable to find Sender with ID "%1"', $id )
            );
        }

        return $sender;
    }

    /**
     * @inheritDoc
     */
    public function getByOrderId ( $orderId )
    {
        $sender = $this->_senderFactory->create ();
        $this->_senderResource->load ( $sender, $orderId, 'order_id' );

        return $sender;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save ( \Eshoper\LPShipping\Api\Data\SenderInterface $sender )
    {
        $this->_senderResource->save ( $sender );
        return $sender;
    }
}

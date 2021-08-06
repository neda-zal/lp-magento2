<?php
/**
 * @package    Eshoper/LPShipping/Model
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model;


class CN22 extends \Magento\Framework\Model\AbstractModel
    implements \Eshoper\LPShipping\Api\Data\CN22Interface
{
    public function _construct()
    {
        $this->_init ( \Eshoper\LPShipping\Model\ResourceModel\CN22::class );
    }

    /**
     * @inheritDoc
     */
    public function getOrderId ()
    {
        return $this->getData ( self::ORDER_ID );
    }

    /**
     * @inheritDoc
     */
    public function setOrderId ( $orderId )
    {
        return $this->setData ( self::ORDER_ID, $orderId );
    }

    /**
     * @inheritDoc
     */
    public function getParcelType ()
    {
        return $this->getData ( self::PARCEL_TYPE );
    }

    /**
     * @inheritDoc
     */
    public function setParcelType ( $parcelType )
    {
        return $this->setData ( self::PARCEL_TYPE, $parcelType );
    }

    /**
     * @inheritDoc
     */
    public function getParcelTypeNotes ()
    {
        return $this->getData ( self::PARCEL_TYPE_NOTES );
    }

    /**
     * @inheritDoc
     */
    public function setParcelTypeNotes ( $parcelTypeNotes )
    {
        return $this->setData ( self::PARCEL_TYPE_NOTES, $parcelTypeNotes );
    }

    /**
     * @inheritDoc
     */
    public function getParcelDescription ()
    {
        return $this->getData ( self::PARCEL_DESCRIPTION );
    }

    /**
     * @inheritDoc
     */
    public function setParcelDescription ( $parcelDescription )
    {
        return $this->setData ( self::PARCEL_DESCRIPTION, $parcelDescription );
    }

    /**
     * @inheritDoc
     */
    public function getCnParts ()
    {
        return $this->getData ( self::CN_PARTS );
    }

    /**
     * @inheritDoc
     */
    public function setCnParts ( $cnParts )
    {
        return $this->setData ( self::CN_PARTS, $cnParts );
    }
}

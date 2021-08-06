<?php
/**
 * @package    Eshoper/LPShipping/Model
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model;


class Tracking extends \Magento\Framework\Model\AbstractModel
    implements \Eshoper\LPShipping\Api\Data\TrackingInterface
{
    public function _construct()
    {
        $this->_init ( \Eshoper\LPShipping\Model\ResourceModel\Tracking::class );
    }

    /**
     * @inheritDoc
     */
    public function getTrackingCode ()
    {
        return $this->getData ( self::TRACKING_CODE );
    }

    /**
     * @inheritDoc
     */
    public function setTrackingCode ( $trackingCode )
    {
        return $this->setData ( self::TRACKING_CODE, $trackingCode );
    }

    /**
     * @inheritDoc
     */
    public function getStateCode ()
    {
        return $this->getData ( self::STATE );
    }

    /**
     * @inheritDoc
     */
    public function setStateCode ( $stateCode )
    {
        return $this->setData ( self::STATE, $stateCode );
    }

    /**
     * @inheritDoc
     */
    public function getEvents ()
    {
        return $this->getData ( self::EVENTS );
    }

    /**
     * @inheritDoc
     */
    public function setEvents ( $events )
    {
        return $this->setData ( self::EVENTS, $events );
    }
}

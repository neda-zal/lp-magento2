<?php
/**
 * @package    Eshoper/LPShipping/Model/Api
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Api;


interface TrackingRepositoryInterface
{
    /**
     * @param int $id
     * @return \Eshoper\LPShipping\Api\Data\TrackingInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById ( $id );

    /**
     * @param string $trackingCode
     * @return \Eshoper\LPShipping\Api\Data\TrackingInterface|bool
     */
    public function getByTrackingCode ( $trackingCode );

    /**
     * @param string $code
     * @return string
     */
    public function getEventDescriptionByCode ( $code );

    /**
     * @param \Eshoper\LPShipping\Api\Data\TrackingInterface $tracking
     * @return \Eshoper\LPShipping\Api\Data\TrackingInterface
     */
    public function save ( \Eshoper\LPShipping\Api\Data\TrackingInterface $tracking );
}

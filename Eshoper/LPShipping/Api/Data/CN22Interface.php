<?php
/**
 * @package    Eshoper/LPShipping/Model/Api/Data
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Api\Data;


interface CN22Interface
{
    const ORDER_ID              = 'order_id';
    const PARCEL_TYPE           = 'parcel_type';
    const PARCEL_TYPE_NOTES     = 'parcel_type_notes';
    const PARCEL_DESCRIPTION    = 'parcel_description';
    const CN_PARTS              = 'cn_parts';

    /**
     * @return int
     */
    public function getId ();

    /**
     * @param int $id
     * @return int
     */
    public function setId ( $id );

    /**
     * @return int
     */
    public function getOrderId ();

    /**
     * @param int $orderId
     * @return int
     */
    public function setOrderId ( $orderId );

    /**
     * @return string
     */
    public function getParcelType ();

    /**
     * @param string $parcelType
     * @return string
     */
    public function setParcelType ( $parcelType );

    /**
     * @return string
     */
    public function getParcelTypeNotes ();

    /**
     * @param string $parcelTypeNotes
     * @return mixed
     */
    public function setParcelTypeNotes ( $parcelTypeNotes );

    /**
     * @return string
     */
    public function getParcelDescription ();

    /**
     * @param string $parcelDescription
     * @return string
     */
    public function setParcelDescription ( $parcelDescription );

    /**
     * @return string
     */
    public function getCnParts ();

    /**
     * @param string $cnParts
     * @return string
     */
    public function setCnParts ( $cnParts );
}

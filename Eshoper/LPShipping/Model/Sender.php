<?php
/**
 * @package    Eshoper/LPShipping/Model
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model;


class Sender extends \Magento\Framework\Model\AbstractModel
    implements \Eshoper\LPShipping\Api\Data\SenderInterface
{
    public function _construct ()
    {
        $this->_init ( \Eshoper\LPShipping\Model\ResourceModel\Sender::class );
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
    public function getName ()
    {
        return $this->getData ( self::NAME );
    }

    /**
     * @inheritDoc
     */
    public function setName ( $name )
    {
        return $this->setData ( self::NAME, $name );
    }

    /**
     * @inheritDoc
     */
    public function getPhone ()
    {
        return $this->getData ( self::PHONE );
    }

    /**
     * @inheritDoc
     */
    public function setPhone ( $phone )
    {
        return $this->setData ( self::PHONE, $phone );
    }

    /**
     * @inheritDoc
     */
    public function getEmail ()
    {
        return $this->getData ( self::EMAIL );
    }

    /**
     * @inheritDoc
     */
    public function setEmail ( $email )
    {
        return $this->setData ( self::EMAIL, $email );
    }

    /**
     * @inheritDoc
     */
    public function getCountryId ()
    {
        return $this->getData ( self::COUNTRY_ID );
    }

    /**
     * @inheritDoc
     */
    public function setCountryId ( $countryId )
    {
        return $this->setData ( self::COUNTRY_ID, $countryId );
    }

    /**
     * @inheritDoc
     */
    public function getCity ()
    {
        return $this->getData ( self::CITY );
    }

    /**
     * @inheritDoc
     */
    public function setCity ( $city )
    {
        return $this->setData ( self::CITY, $city );
    }

    /**
     * @inheritDoc
     */
    public function getStreet ()
    {
        return $this->getData ( self::STREET );
    }

    /**
     * @inheritDoc
     */
    public function setStreet ( $street )
    {
        return $this->setData ( self::STREET, $street );
    }

    /**
     * @inheritDoc
     */
    public function getBuildingNumber ()
    {
        return $this->getData ( self::BUILDING_NUMBER );
    }

    /**
     * @inheritDoc
     */
    public function setBuildingNumber ( $buildingNumber )
    {
        return $this->setData ( self::BUILDING_NUMBER, $buildingNumber );
    }

    /**
     * @inheritDoc
     */
    public function getApartment ()
    {
        return $this->getData ( self::APARTMENT );
    }

    /**
     * @inheritDoc
     */
    public function setApartment ( $apartment )
    {
        return $this->setData ( self::APARTMENT, $apartment );
    }

    /**
     * @inheritDoc
     */
    public function getPostcode ()
    {
        return $this->getData ( self::POSTCODE );
    }

    /**
     * @inheritDoc
     */
    public function setPostcode ( $postcode )
    {
        return $this->setData ( self::POSTCODE, $postcode );
    }
}

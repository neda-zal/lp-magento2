<?php
/**
 * @package    Eshoper/LPShipping/Model/Config/Source
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model\Config\Source;


class AvailableCountries implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Eshoper\LPShipping\Model\ResourceModel\LPCountries\Collection $_availableCountryCollection
     */
    protected $_availableCountryCollection;

    /**
     * @var \Magento\Directory\Model\CountryFactory $_countryFactory
     */
    protected $_countryFactory;

    /**
     * AvailableCountries constructor.
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Eshoper\LPShipping\Model\ResourceModel\LPCountries\Collection $availableCountryCollection
     */
    public function __construct (
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Eshoper\LPShipping\Model\ResourceModel\LPCountries\Collection $availableCountryCollection
    ) {
        $this->_countryFactory = $countryFactory;
        $this->_availableCountryCollection = $availableCountryCollection;
    }

    /**
     * Get country name by code from magento
     * @param $countryCode
     * @return mixed
     */
    private function getCountryNameByCode ( $countryCode )
    {
        /** @var \Magento\Directory\Model\Country $country */
        $country = $this->_countryFactory->create ()->loadByCode ( $countryCode );
        return $country->getName ();
    }

    /**
     * For sender information need to add country ID as value from API
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $availableCountries = [];

        // Available LP countries
        foreach ( $this->_availableCountryCollection->getItems () as $item ) {
            array_push ( $availableCountries, [
                'label' => $this->getCountryNameByCode ( $item->getCountryCode () ),
                'value' => $item->getCountryId ()
            ] );
        }

        return $availableCountries;
    }

    /**
     * Need to add country code instead of ID for magento allowed countries
     * Also translated country names because API returns country name only in lithuanian
     * @return array
     */
    public function availableCountries ()
    {
        $availableCountries = [];

        // Available LP countries with codes instead of ID
        foreach ( $this->_availableCountryCollection->getItems () as $item ) {
            array_push ( $availableCountries, [
                'label' => $this->getCountryNameByCode ( $item->getCountryCode () ),
                'value' => $item->getCountryCode ()
            ] );
        }

        return $availableCountries;
    }
}

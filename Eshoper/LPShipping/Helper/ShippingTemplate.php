<?php
/**
 * @package    Eshoper/LPShipping/Helper
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Helper;

class ShippingTemplate extends \Magento\Framework\App\Helper\AbstractHelper
{
    // Additional services IDs
    const ADDITIONAL_COD_ID         = 8;
    const ADDITIONAL_PRIORITY_ID    = 1;
    const ADDITIONAL_REGISTERED_ID  = 2;

    // Default constants for CN data
    const CN_PARCEL_TYPE = 'sell';
    const CN_PARCEL_TYPE_NOTES = 'Sell Items';
    const CN_PARCEL_DESCRIPTION = 'Sell';

    // Special drawing right coefficient
    const SDR_COF = 1.786;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $_config
     */
    protected $_config;

    /**
     * @var \Eshoper\LPShipping\Api\CN22RepositoryInterface $_CN22Repository
     */
    protected $_CN22Repository;

    /**
     * @var \Eshoper\LPShipping\Api\CN23RepositoryInterface $_CN23Repository
     */
    protected $_CN23Repository;

    /**
     * @var \Eshoper\LPShipping\Api\ShippingTemplateRepositoryInterface $_shippingTemplateRepository
     */
    protected $_shippingTemplateRepository;

    /**
     * ShippingTemplate constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Eshoper\LPShipping\Api\CN22RepositoryInterface $CN22Repository
     * @param \Eshoper\LPShipping\Api\CN23RepositoryInterface $CN23Repository
     * @param \Eshoper\LPShipping\Api\ShippingTemplateRepositoryInterface $shippingTemplatesRepository
     */
    public function __construct (
        \Eshoper\LPShipping\Api\CN22RepositoryInterface $CN22Repository,
        \Eshoper\LPShipping\Api\CN23RepositoryInterface $CN23Repository,
        \Eshoper\LPShipping\Api\ShippingTemplateRepositoryInterface $shippingTemplatesRepository,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $config
    ) {
        $this->_config                      = $config;
        $this->_CN22Repository              = $CN22Repository;
        $this->_CN23Repository              = $CN23Repository;
        $this->_shippingTemplateRepository  = $shippingTemplatesRepository;

        parent::__construct ( $context );
    }

    /**
     * Get shipping template ID
     * @param string $size
     * @param string $type
     * @return mixed
     */
    public function getTemplate ( $type, $size )
    {
        return $this->_shippingTemplateRepository->getShippingTemplate ( $type, $size );
    }

    /**
     * Get SDR value
     * @param $price
     * @return float|int
     */
    public function getSDRValue ( $price )
    {
        return self::SDR_COF * $price;
    }

    /**
     * Get EU countries
     * @return array
     */
    protected function getEuCountries ()
    {
        $countries = $this->_config->getValue ( 'general/country/eu_countries' );
        return explode ( ',', $countries );
    }

    /**
     * Default CN22 structure for automatic CN form creation
     * @param \Magento\Sales\Model\Order $order
     * @param string $cnForm
     * @return array
     */
    protected function getCNDataDefaults ( $order, $cnForm )
    {
        $cnParts = [];

        // CN form parts is items in order
        foreach ( $order->getAllVisibleItems () as $item ) {
            $cnParts [] = [
                'summary' => $item->getName (),
                'amount' => $item->getPrice (),
                'countryCode' => 'LT', // Default country of origin
                'currencyCode' => $order->getBaseCurrencyCode (),
                'hsCode' => '',
                'weight' => $item->getWeight () / 1000, // Weight in grams
                'quantity' => intval ( $item->getQtyOrdered () )
            ];
        }

        return [
            $cnForm => [
                'parcelType' => self::CN_PARCEL_TYPE,
                'parcelTypeNotes' => self::CN_PARCEL_TYPE_NOTES,
                'description' => self::CN_PARCEL_DESCRIPTION,
                'cnParts' => $cnParts
            ]
        ];
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param $templateId
     * @return bool
     */
    public function isCN22 ( $order, $templateId )
    {
        // Check if not in EU
        if ( !in_array ( $order->getShippingAddress ()->getCountryId (), $this->getEuCountries () ) ) {
            // CN22
            if ( in_array ( $templateId, [ 42, 43, 70, 73 ] )
                && $this->getSDRValue ( $order->getGrandTotal () ) < 300 ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param $templateId
     * @return bool
     */
    public function isCN23 ( $order, $templateId )
    {
        // Check if not in EU
        if ( !in_array ( $order->getShippingAddress ()->getCountryId (), $this->getEuCountries () ) ) {
            // CN23
            if ( in_array ( $templateId, [ 42, 43, 70, 73 ] )
                && $this->getSDRValue ( $order->getGrandTotal () ) > 300 ) {
                return true;
            }

            // CN23
            if ( in_array ( $templateId, [ 45, 49, 50, 51, 52, 53 ] ) ) {
                return true;
            }
        }

        // CN23
        if ( $templateId == 44 ) {
            return true;
        }

        return false;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param int $templateId
     * @param string $countryCode
     * @return array|null
     */
    public function getCnData ( $order, $templateId )
    {
        // Get CN form data from edit shipment or use defaults
        $CN22 = $this->_CN22Repository->getByOrderId ( $order->getId () );
        $CN23 = $this->_CN23Repository->getByOrderId ( $order->getId () );

        if ( $CN22->getId () ) {
            $CN22FormData = [
                'cn22Form' => [
                    'parcelType' => $CN22->getParcelType (),
                    'parcelTypeNotes' => $CN22->getParcelTypeNotes (),
                    'description' => $CN22->getParcelDescription (),
                    'cnParts' => json_decode ( $CN22->getCnParts () )
                ]
            ];
        } else {
            $CN22FormData = $this->getCNDataDefaults ( $order, 'cn22Form' );
        }

        if ( $CN23->getId () ) {
            $CN23FormData = [
                'cn23Form' => array_filter ( [
                    'cnParts' => json_decode ( $CN23->getCnParts () ),
                    'exporterCustomsCode' => $CN23->getExporterCustomsCode (),
                    'parcelType' => $CN23->getParcelType (),
                    'parcelTypeNotes' => $CN23->getParcelTypeNotes (),
                    'license' => $CN23->getLicense (),
                    'certificate' => $CN23->getCertificate (),
                    'invoice' => $CN23->getInvoice (),
                    'notes' => $CN23->getNotes (),
                    'failureInstruction' => $CN23->getFailureInstruction (),
                    'importerCode' => $CN23->getImporterCode (),
                    'importerCustomsCode' => $CN23->getImporterCustomsCode (),
                    'importerEmail' => $CN23->getImporterEmail (),
                    'importerFax' => $CN23->getImporterFax (),
                    'importerPhone' => $CN23->getImporterPhone (),
                    'importerTaxCode' => $CN23->getImporterTaxCode (),
                    'importerVatCode' => $CN23->getImporterVatCode (),
                    'description' => $CN23->getDescription ()
                ] )
            ];
        } else {
            $CN23FormData = $this->getCNDataDefaults ( $order, 'cn23Form' );
        }

        // CN22
        if ( $this->isCN22 ( $order, $templateId ) ) {
            return $CN22FormData;
        }

        // CN23
        if ( $this->isCN23 ( $order, $templateId ) ) {
            return $CN23FormData;
        }

        return null;
    }

    /**
     * @param $templateId
     * @param bool $isCOD
     * @return bool
     */
    public function isCODAvailable ( $templateId, $isCOD = true )
    {
        return $isCOD && in_array ( $templateId, [ 42, 43, 44, 45, 46, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63 ] );
    }

    /**
     * @param $templateId
     * @param $amount
     * @param bool $isCOD
     * @param bool $priority
     * @param bool $registered
     * @return array
     */
    public function getAdditionalServices ( $templateId, $amount, $isCOD = true, $priority = false, $registered = false )
    {
        $result = [];

        // API instructions
        if ( $this->isCODAvailable ( $templateId, $isCOD ) ) {
            // COD Value
            $result [] = [ 'id' => self::ADDITIONAL_COD_ID, 'amount' => $amount ];
        }

        if ( $priority ) {
            $result [] = [ 'id' => self::ADDITIONAL_PRIORITY_ID ]; // Priority
        }

        if ( $registered || $templateId == 73 || $templateId == 70 ) {
            $result [] = [ 'id' => self::ADDITIONAL_REGISTERED_ID ]; // Registered
        }

        return $result;
    }
}

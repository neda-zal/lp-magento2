<?php
/**
 * @package    Eshoper/LPShipping/Model/Config/Backend/LPCountryRates
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model\Config\Backend\LPCountryRates;

use Eshoper\LPShipping\Model\ResourceModel\LPExpressTableRates;

class Rates extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Eshoper\LPShipping\Model\ResourceModel\LPCountryRatesFactory $_LPCountryRatesFactory;
     */
    private $_LPCountryRatesFactory;

    /**
     * Rates constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Eshoper\LPShipping\Model\ResourceModel\LPCountryRatesFactory $LPCountryRatesFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct (
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Eshoper\LPShipping\Model\ResourceModel\LPCountryRatesFactory $LPCountryRatesFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_LPCountryRatesFactory = $LPCountryRatesFactory;

        parent::__construct (
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * After config save information call resource model
     * to save price vs weight table
     *
     * @return \Magento\Framework\App\Config\Value
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterSave ()
    {
        /** @var \Eshoper\LPShipping\Model\ResourceModel\LPCountryRates $LPCountryRates */
        $LPCountryRates = $this->_LPCountryRatesFactory->create ();
        $LPCountryRates->uploadAndImport ( $this );

        return parent::afterSave();
    }
}

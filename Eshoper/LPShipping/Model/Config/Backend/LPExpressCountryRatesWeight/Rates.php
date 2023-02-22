<?php
/**
 * @package    Eshoper/LPShipping/Model/Config/Backend/LPExpressCountryRatesWeight
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model\Config\Backend\LPExpressCountryRatesWeight;

class Rates extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Eshoper\LPShipping\Model\ResourceModel\LPExpressCountryRatesWeightFactory $_LPExpressCountryRatesWeightFactory;
     */
    private $_LPExpressCountryRatesWeightFactory;

    /**
     * Rates constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Eshoper\LPShipping\Model\ResourceModel\LPExpressCountryRatesWeightFactory $LPExpressCountryRatesWeightFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct (
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Eshoper\LPShipping\Model\ResourceModel\LPExpressCountryRatesWeightFactory $LPExpressCountryRatesWeightFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_LPExpressCountryRatesWeightFactory = $LPExpressCountryRatesWeightFactory;

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
        /** @var \Eshoper\LPShipping\Model\ResourceModel\LPExpressCountryRatesWeight $LPCountryWeightRates */
        $LPCountryWeightRates = $this->_LPExpressCountryRatesWeightFactory->create ();
        $LPCountryWeightRates->uploadAndImport ( $this );

        return parent::afterSave();
    }
}

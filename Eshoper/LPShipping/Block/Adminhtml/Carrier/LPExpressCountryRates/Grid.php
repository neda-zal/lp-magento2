<?php
/**
 * @package    Eshoper/LPShipping/Block/Adminhtml/Carrier/LPExpressCountryRates
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Block\Adminhtml\Carrier\LPExpressCountryRates;


class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Website filter
     *
     * @var int
     */
    protected $_websiteId;

    /**
     * Condition filter
     *
     * @var string
     */
    protected $_conditionName;

    /**
     * @var \Eshoper\LPShipping\Model\LPExpressCountryRates $_countryRates
     */
    protected $_countryRates;

    /**
     * @var \Eshoper\LPShipping\Model\ResourceModel\LPExpressCountryRates\CollectionFactory $_collectionFactory
     */
    protected $_collectionFactory;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Eshoper\LPShipping\Model\ResourceModel\LPExpressCountryRates\CollectionFactory $collectionFactory
     * @param \Eshoper\LPShipping\Model\LPExpressCountryRates $rates
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Eshoper\LPShipping\Model\ResourceModel\LPExpressCountryRates\CollectionFactory $collectionFactory,
        \Eshoper\LPShipping\Model\LPExpressCountryRates $rates,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_countryRates = $rates;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Define grid properties
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct ();
        $this->setId ( 'lpexpress_shippingOverseasGrid' );
        $this->_exportPageSize = 10000;
    }

    /**
     * Set current website
     *
     * @param $websiteId
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setWebsiteId($websiteId)
    {
        $this->_websiteId = $this->_storeManager->getWebsite ( $websiteId )->getId ();
        return $this;
    }

    /**
     * Retrieve current website id
     *
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getWebsiteId()
    {
        if ( $this->_websiteId === null ) {
            $this->_websiteId = $this->_storeManager->getWebsite ()->getId ();
        }
        return $this->_websiteId;
    }

    /**
     * Set current website
     *
     * @param string $name
     * @return $this
     */
    public function setConditionName($name)
    {
        $this->_conditionName = $name;
        return $this;
    }

    /**
     * Retrieve current website id
     *
     * @return int
     */
    public function getConditionName()
    {
        return $this->_conditionName;
    }

    /**
     * Prepare shipping table rate collection
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareCollection()
    {
        /** @var \Eshoper\LPShipping\Model\ResourceModel\LPTableRates\Collection $collection */
        $collection = $this->_collectionFactory->create ();
        $this->setCollection ( $collection );

        return parent::_prepareCollection ();
    }

    /**
     * Prepare table columns
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn (
            'country_id',
            [ 'header' => __( 'Country' ), 'index' => 'country_id' ]
        );

        $this->addColumn (
            'price',
            [ 'header' => __( 'Price' ), 'index' => 'price' ]
        );

        return parent::_prepareColumns ();
    }
}

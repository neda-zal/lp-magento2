<?php
/**
 * @package    Eshoper/LPShipping/Model/ResourceModel/LPCountries
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model\ResourceModel\LPCountries;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Identification field
     *
     * @var string $_idFieldName
     */
    protected $_idFieldName = 'id';

    /**
     * Connect model with resource model
     */
    protected function _construct()
    {
        $this->_init (
            \Eshoper\LPShipping\Model\LPCountries::class,
            \Eshoper\LPShipping\Model\ResourceModel\LPCountries::class
        );
    }
}

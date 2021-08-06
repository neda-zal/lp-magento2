<?php
/**
 * @package    Eshoper/LPShipping/Model/ResourceModel/Tracking
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model\ResourceModel\Tracking;


class Collection extends
    \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string $_idFieldName
     */
    protected $_idFieldName = 'id';

    public function _construct()
    {
        $this->_init (
            \Eshoper\LPShipping\Model\Tracking::class,
            \Eshoper\LPShipping\Model\ResourceModel\Tracking::class
        );
    }
}

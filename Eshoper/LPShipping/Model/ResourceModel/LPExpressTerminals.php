<?php
/**
 * @package    Eshoper/LPShipping/Model/ResourceModel
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model\ResourceModel;


class LPExpressTerminals extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init ( 'lpexpress_terminal_list', 'id' );
    }
}

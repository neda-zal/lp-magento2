<?php
/**
 * @package    Eshoper/LPShipping/Model
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Model;

class LPCountryRates extends \Magento\Framework\Model\AbstractModel
{
    public function _construct()
    {
        $this->_init(
            \Eshoper\LPShipping\Model\ResourceModel\LPCountryRates::class
        );
    }
}

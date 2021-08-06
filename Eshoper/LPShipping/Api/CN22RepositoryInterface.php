<?php
/**
 * @package    Eshoper/LPShipping/Model/Api
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Api;


interface CN22RepositoryInterface
{
    /**
     * @param $id
     * @return \Eshoper\LPShipping\Api\Data\CN22Interface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById ( $id );

    /**
     * @param $orderId
     * @return \Eshoper\LPShipping\Api\Data\CN22Interface|bool
     */
    public function getByOrderId ( $orderId );

    /**
     * @param Data\CN22Interface $CN22
     * @return \Eshoper\LPShipping\Api\Data\CN22Interface
     */
    public function save ( \Eshoper\LPShipping\Api\Data\CN22Interface $CN22 );
}

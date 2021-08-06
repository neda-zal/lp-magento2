<?php
/**
 * @package    Eshoper/LPShipping/Model/Api
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Api;


interface CN23RepositoryInterface
{
    /**
     * @param int $id
     * @return \Eshoper\LPShipping\Api\Data\CN23Interface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById ( $id );

    /**
     * @param int $orderId
     * @return \Eshoper\LPShipping\Api\Data\CN23Interface|bool
     */
    public function getByOrderId ( $orderId );

    /**
     * @param \Eshoper\LPShipping\Api\Data\CN23Interface $CN23
     * @return \Eshoper\LPShipping\Api\Data\CN23Interface
     */
    public function save ( \Eshoper\LPShipping\Api\Data\CN23Interface $CN23 );
}

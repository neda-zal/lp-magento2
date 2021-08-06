<?php
/**
 * @package    Eshoper/LPShipping/Model/Api
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Api;


interface SenderRepositoryInterface
{
    /**
     * @param int $id
     * @return \Eshoper\LPShipping\Api\Data\SenderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById ( $id );

    /**
     * @param int $orderId
     * @return \Eshoper\LPShipping\Api\Data\SenderInterface
     */
    public function getByOrderId ( $orderId );

    /**
     * @param Data\SenderInterface $sender
     * @return \Eshoper\LPShipping\Api\Data\SenderInterface
     */
    public function save ( \Eshoper\LPShipping\Api\Data\SenderInterface $sender );
}

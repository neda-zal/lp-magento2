<?php
/**
 * @package    Eshoper/LPShipping/Model/Api
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Api;

interface LPExpressTerminalRepositoryInterface
{
    /**
     * @param int $terminalId
     * @return \Eshoper\LPShipping\Api\Data\LPExpressTerminalsInterface
     */
    public function getByTerminalId ( $terminalId );

    /**
     * @return array
     */
    public function getList ();

    /**
     * @param Data\LPExpressTerminalsInterface $terminal
     * @return \Eshoper\LPShipping\Api\Data\LPExpressTerminalsInterface
     */
    public function save ( \Eshoper\LPShipping\Api\Data\LPExpressTerminalsInterface $terminal );
}

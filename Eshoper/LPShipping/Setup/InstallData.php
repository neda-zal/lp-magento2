<?php
/**
 * @package    Eshoper/LPShipping/Setup
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Setup;

class InstallData implements \Magento\Framework\Setup\InstallDataInterface
{
    /**
     * @inheritDoc
     */
    public function install( \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
                             \Magento\Framework\Setup\ModuleContextInterface $context )
    {
        if (version_compare ( $context->getVersion (), '1.0.0', '<' ) ) {
            $data = [];

            $statuses = [
                'lp_courier_called'         => __( 'Courier Called' ),
                'lp_courier_not_called'     => __( 'Courier Not Called' ),
                'lp_shipment_created'       => __( 'Shipment Created' ),
                'lp_shipment_not_created'   => __( 'Shipment Not Created' )
            ];

            foreach ( $statuses as $code => $info ) {
                $data [] = [ 'status' => $code, 'label' => $info ];
            }

            $select = $setup->getConnection()->select()
                ->from($setup->getTable ( 'sales_order_status' ), 'status')
                ->where('status = :status');

            $bind = [':status' => 'lp_courier_called'];
            
            $exists = $setup->getConnection()->fetchOne($select, $bind);

            if ( !$exists ) {
                $setup->getConnection ()
                    ->insertArray ( $setup->getTable ( 'sales_order_status' ),
                        [ 'status', 'label' ], $data );
            }
        }
    }
}
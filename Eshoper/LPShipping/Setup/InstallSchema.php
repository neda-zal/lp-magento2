<?php
/**
 * @package    Eshoper/LPShipping/Setup
 * @author     MB "Eshoper" <pagalba@eshoper.lt>
 * @copyright  AB Lietuvos paštas. All rights reserved.
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
namespace Eshoper\LPShipping\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @inheritDoc
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup ();

        $setup->getConnection ()->addColumn (
            $setup->getTable ( 'quote' ),
            'lpexpress_terminal',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Selected LPExpress terminal'
            ]
        );

        $setup->getConnection ()->addColumn (
            $setup->getTable ( 'sales_order' ),
            'lpexpress_terminal',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Selected LPExpress terminal'
            ]
        );

        $setup->getConnection ()->addColumn (
            $setup->getTable ( 'sales_order' ),
            'lp_shipping_type',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'LP Shipping Type'
            ]
        );

        $setup->getConnection ()->addColumn (
            $setup->getTable ( 'sales_order' ),
            'lp_shipping_size',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'LP Shipping Size'
            ]
        );

        $setup->getConnection ()->addColumn (
            $setup->getTable ( 'sales_order' ),
            'lp_cart_id',
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'LP Shipping Cart ID'
            ]
        );

        $setup->getConnection ()->addColumn (
            $setup->getTable ( 'sales_order' ),
            'lp_shipping_item_id',
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'LP Shipping Item ID'
            ]
        );

        $setup->getConnection ()->addColumn (
            $setup->getTable ( 'sales_order' ),
            'lp_shipment_tracking_updated',
            [
                'type' => Table::TYPE_DATETIME,
                'nullable' => true,
                'comment' => 'LP Shipment Track Events Updated'
            ]
        );

        $setup->getConnection ()->addColumn (
            $setup->getTable ( 'sales_order' ),
            'lp_cod',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '20,4',
                'nullable' => true,
                'comment' => 'LP COD Value'
            ]
        );

        $setup->getConnection()->dropTable ( $setup->getTable ('lp_api_token' ) );
        $lp_api_token = $setup->getConnection ()->newTable (
            $setup->getTable ('lp_api_token' )
        )->addColumn (
            'id',
            Table::TYPE_INTEGER,
            null,
            [ 'identity' => true, 'nullable' => false, 'primary' => true ], 'ID'
        )->addColumn (
            'access_token',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false ]
        )->addColumn(
            'refresh_token',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false ]
        )->addColumn(
            'expires',
            Table::TYPE_DATETIME,
            [ 'nullable' => false ]
        )->addColumn(
            'updated',
            Table::TYPE_DATETIME,
            [ 'nullable' => true ]
        )->addIndex(
            $setup->getIdxName ( 'lp_api_token', [ 'id' ] ),
            [ 'id' ]
        )->setComment ( 'API Tokens' );

        $setup->getConnection ()->createTable ( $lp_api_token );

        $setup->getConnection()->dropTable ( $setup->getTable ('lp_shipping_template' ) );
        $lp_shipping_template = $setup->getConnection ()->newTable (
            $setup->getTable ('lp_shipping_template' )
        )->addColumn (
            'id',
            Table::TYPE_INTEGER,
            null,
            [ 'identity' => true, 'nullable' => false, 'primary' => true ], 'ID'
        )->addColumn (
            'shipping_templates',
            Table::TYPE_TEXT,
            null,
            [ 'nullable' => false ]
        )->addIndex (
            $setup->getIdxName ( 'lp_shipping_template', [ 'id' ] ),
            [ 'id' ]
        )->setComment ( 'Shipping template' );

        $setup->getConnection ()->createTable ( $lp_shipping_template );

        $setup->getConnection()->dropTable ( $setup->getTable ('lp_table_rates' ) );
        $lp_table_rates = $setup->getConnection ()->newTable (
            $setup->getTable ('lp_table_rates' )
        )->addColumn (
            'id',
            Table::TYPE_INTEGER,
            null,
            [ 'identity' => true, 'nullable' => false, 'primary' => true ], 'ID'
        )->addColumn (
            'weight_to',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false ]
        )->addColumn(
            'postoffice_price',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false ]
        )->addIndex(
            $setup->getIdxName ( 'lp_table_rates', [ 'weight_to' ] ),
            [ 'weight_to' ]
        )->setComment ( 'Table rates for LP' );

        $setup->getConnection ()->createTable ( $lp_table_rates );

        $setup->getConnection()->dropTable ( $setup->getTable ('lp_overseas_rates' ) );
        $lp_overseas_rates = $setup->getConnection ()->newTable (
            $setup->getTable ('lp_overseas_rates' )
        )->addColumn (
            'id',
            Table::TYPE_INTEGER,
            null,
            [ 'identity' => true, 'nullable' => false, 'primary' => true ], 'ID'
        )->addColumn (
            'country_id',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false ]
        )->addColumn (
            'price',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => true ]
        )->addIndex(
            $setup->getIdxName ( 'lp_overseas_rates', [ 'country_id' ] ),
            [ 'country_id' ]
        )->setComment ( 'Overseas rates for LP' );

        $setup->getConnection ()->createTable ( $lp_overseas_rates );

        $setup->getConnection()->dropTable ( $setup->getTable ('lp_country_list' ) );
        $lp_country_list = $setup->getConnection ()->newTable (
            $setup->getTable ('lp_country_list' )
        )->addColumn (
            'id',
            Table::TYPE_INTEGER,
            null,
            [ 'identity' => true, 'nullable' => false, 'primary' => true ], 'ID'
        )->addColumn (
            'country_id',
            Table::TYPE_INTEGER,
            255,
            [ 'nullable' => false ]
        )->addColumn (
            'country',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false ]
        )->addColumn (
            'country_code',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false ]
        )->addIndex(
            $setup->getIdxName ( 'lp_country_list', [ 'country_id' ] ),
            [ 'country_id' ]
        )->setComment ( 'LP available country list' );

        $setup->getConnection ()->createTable ( $lp_country_list );

        $setup->getConnection()->dropTable ( $setup->getTable ('lpexpress_table_rates' ) );
        $lpexpress_table_rates = $setup->getConnection ()->newTable (
            $setup->getTable ('lpexpress_table_rates' )
        )->addColumn (
            'id',
            Table::TYPE_INTEGER,
            null,
            [ 'identity' => true, 'nullable' => false, 'primary' => true ], 'ID'
        )->addColumn (
            'weight_to',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false ]
        )->addColumn(
            'courier_price',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false ]
        )->addColumn (
            'terminal_price',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false ]
        )->addColumn (
            'postoffice_price',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false ]
        )->addIndex(
            $setup->getIdxName ( 'lpexpress_table_rates', [ 'weight_to' ] ),
            [ 'weight_to' ]
        )->setComment ( 'Table rates for LP Express' );

        $setup->getConnection ()->createTable ( $lpexpress_table_rates );

        $setup->getConnection()->dropTable ( $setup->getTable ('lpexpress_overseas_rates' ) );
        $lpexpress_overseas_rates = $setup->getConnection ()->newTable (
            $setup->getTable ('lpexpress_overseas_rates' )
        )->addColumn (
            'id',
            Table::TYPE_INTEGER,
            null,
            [ 'identity' => true, 'nullable' => false, 'primary' => true ], 'ID'
        )->addColumn (
            'country_id',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false ]
        )->addColumn (
            'price',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => true ]
        )->addIndex(
            $setup->getIdxName ( 'lpexpress_overseas_rates', [ 'country_id' ] ),
            [ 'country_id' ]
        )->setComment ( 'Overseas rates for LP Express' );

        $setup->getConnection ()->createTable ( $lpexpress_overseas_rates );

        $setup->getConnection()->dropTable ( $setup->getTable ('lpexpress_terminal_list' ) );
        $lpexpress_terminal_list = $setup->getConnection ()->newTable (
            $setup->getTable ( 'lpexpress_terminal_list' )
        )->addColumn (
            'id',
            Table::TYPE_INTEGER,
            null,
            [ 'identity' => true, 'nullable' => false, 'primary' => true ],
            'ID'
        )->addColumn (
            'terminal_id',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false ],
            'LP Express terminal ID'
        )->addColumn (
            'name',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false ]
        )->addColumn (
            'address',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false ]
        )->addColumn (
            'city',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false ]
        )->addIndex (
            $setup->getIdxName ( 'lpexpress_terminal_item', ['terminal_id'] ),
            [ 'terminal_id' ]
        )->setComment ( 'LP Express terminal list' );

        $setup->getConnection ()->createTable ( $lpexpress_terminal_list );

        $setup->getConnection()->dropTable ( $setup->getTable ('lp_cn22_form_data' ) );
        $lp_cn22_form_data = $setup->getConnection ()->newTable (
            $setup->getTable ( 'lp_cn22_form_data' )
        )->addColumn (
            'id',
            Table::TYPE_INTEGER,
            null,
            [ 'identity' => true, 'nullable' => false, 'primary' => true ],
            'ID'
        )->addColumn (
            'order_id',
            Table::TYPE_INTEGER,
            null,
            [ 'nullable' => false ]
        )->addColumn (
            'parcel_type',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false ]
        )->addColumn (
            'parcel_type_notes',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false ]
        )->addColumn (
            'parcel_description',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false ]
        )->addColumn (
            'cn_parts',
            Table::TYPE_TEXT,
            null,
            [ 'nullable' => false ]
        )->addIndex (
            $setup->getIdxName ( 'lp_cn22_form_data', [ 'order_id' ] ),
            [ 'order_id' ]
        )->setComment ( 'LP CN22 form data' );

        $setup->getConnection ()->createTable ( $lp_cn22_form_data );

        $setup->getConnection()->dropTable ( $setup->getTable ('lp_cn23_form_data' ) );
        $lp_cn23_form_data = $setup->getConnection ()->newTable (
            $setup->getTable ( 'lp_cn23_form_data' )
        )->addColumn (
            'id',
            Table::TYPE_INTEGER,
            null,
            [ 'identity' => true, 'nullable' => false, 'primary' => true ],
            'ID'
        )->addColumn (
            'order_id',
            Table::TYPE_INTEGER,
            null,
            [ 'nullable' => false ]
        )->addColumn (
            'cn_parts',
            Table::TYPE_TEXT,
            null,
            [ 'nullable' => false ]
        )->addColumn (
            'exporter_customs_code',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => true ]
        )->addColumn (
            'parcel_type',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false ]
        )->addColumn (
            'parcel_type_notes',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => true ]
        )->addColumn (
            'license',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => true ]
        )->addColumn (
            'certificate',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => true ]
        )->addColumn (
            'invoice',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => true ]
        )->addColumn (
            'notes',
            Table::TYPE_TEXT,
            null,
            [ 'nullable' => true ]
        )->addColumn (
            'failure_instruction',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => true ]
        )->addColumn (
            'importer_code',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => true ]
        )->addColumn (
            'importer_customs_code',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => true ]
        )->addColumn (
            'importer_email',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => true ]
        )->addColumn (
            'importer_fax',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => true ]
        )->addColumn (
            'importer_phone',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => true ]
        )->addColumn (
            'importer_tax_code',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => true ]
        )->addColumn (
            'importer_vat_code',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => true ]
        )->addColumn (
            'description',
            Table::TYPE_TEXT,
            null,
            [ 'nullable' => true ]
        )->addIndex (
            $setup->getIdxName ( 'lp_cn23_form_data', [ 'order_id' ] ),
            [ 'order_id' ]
        )->setComment ( 'LP CN23 form data' );

        $setup->getConnection ()->createTable ( $lp_cn23_form_data );

        $setup->getConnection()->dropTable ( $setup->getTable ('lp_sender_data' ) );
        $lp_sender_data = $setup->getConnection ()->newTable (
            $setup->getTable ('lp_sender_data' )
        )->addColumn (
            'id',
            Table::TYPE_INTEGER,
            null,
            [ 'identity' => true, 'nullable' => false, 'primary' => true ], 'ID'
        )->addColumn (
            'order_id',
            Table::TYPE_INTEGER,
            null,
            [ 'nullable' => false ]
        )->addColumn (
            'name',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false ]
        )->addColumn (
            'phone',
            Table::TYPE_TEXT,
            null,
            [ 'nullable' => false ]
        )->addColumn (
            'email',
            Table::TYPE_TEXT,
            null,
            [ 'nullable' => false ]
        )->addColumn (
            'country_id',
            Table::TYPE_INTEGER,
            null,
            [ 'nullable' => false ]
        )->addColumn (
            'city',
            Table::TYPE_TEXT,
            null,
            [ 'nullable' => false ]
        )->addColumn (
            'street',
            Table::TYPE_TEXT,
            null,
            [ 'nullable' => false ]
        )->addColumn (
            'building_number',
            Table::TYPE_TEXT,
            null,
            [ 'nullable' => false ]
        )->addColumn (
            'apartment',
            Table::TYPE_TEXT,
            null,
            [ 'nullable' => false ]
        )->addColumn (
            'postcode',
            Table::TYPE_TEXT,
            null,
            [ 'nullable' => false ]
        )->addIndex (
            $setup->getIdxName ( 'lp_sender_data', [ 'order_id' ] ),
            [ 'order_id' ]
        )->setComment ( 'LP tracking events' );

        $setup->getConnection ()->createTable ( $lp_sender_data );

        $setup->getConnection()->dropTable ( $setup->getTable ('lp_tracking_events' ) );
        $lp_tracking_events = $setup->getConnection ()->newTable (
            $setup->getTable ('lp_tracking_events' )
        )->addColumn (
            'id',
            Table::TYPE_INTEGER,
            null,
            [ 'identity' => true, 'nullable' => false, 'primary' => true ], 'ID'
        )->addColumn (
            'tracking_code',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false ]
        )->addColumn (
            'state',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false ]
        )->addColumn (
            'events',
            Table::TYPE_TEXT,
            null,
            [ 'nullable' => false ]
        )->addIndex (
            $setup->getIdxName ( 'lp_tracking_events', [ 'tracking_code' ] ),
            [ 'tracking_code' ]
        )->setComment ( 'LP tracking events' );

        $setup->getConnection ()->createTable ( $lp_tracking_events );

        $setup->endSetup ();
    }
}

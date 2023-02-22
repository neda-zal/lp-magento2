<?php

namespace Eshoper\LPShipping\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    public function upgrade (SchemaSetupInterface $setup, ModuleContextInterface $context )
    {
        $installer = $setup;
        $installer->startSetup();

        if ( version_compare ( $context->getVersion(), '1.1.0', '<' ) ) {
            $installer->getConnection()->dropTable ( $installer->getTable ('lp_overseas_rates_weight' ) );
            $lp_overseas_rates_weight = $installer->getConnection ()->newTable (
                $installer->getTable ('lp_overseas_rates_weight' )
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
            )->addColumn (
                'weight',
                Table::TYPE_TEXT,
                255,
                [ 'nullable' => true ]
            )->addIndex(
                $setup->getIdxName ( 'lp_overseas_rates_weight', [ 'country_id' ] ),
                [ 'country_id' ]
            )->setComment ( 'Overseas rates vs weight for LP' );

            $installer->getConnection ()->createTable ( $lp_overseas_rates_weight );

            $lpexpress_overseas_rates_weight = $installer->getConnection ()->newTable (
                $installer->getTable ('lpexpress_overseas_rates_weight' )
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
            )->addColumn (
                'weight',
                Table::TYPE_TEXT,
                255,
                [ 'nullable' => true ]
            )->addIndex(
                $setup->getIdxName ( 'lpexpress_overseas_rates_weight', [ 'country_id' ] ),
                [ 'country_id' ]
            )->setComment ( 'Overseas rates vs weight for LPEXPRESS' );

            $installer->getConnection ()->createTable ( $lpexpress_overseas_rates_weight );
        }

        $installer->endSetup();
    }
}
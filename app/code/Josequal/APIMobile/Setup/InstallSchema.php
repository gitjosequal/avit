<?php
namespace Josequal\APIMobile\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Install schema for Custom Points System
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        // Create custom_points table
        $table = $installer->getConnection()->newTable(
            $installer->getTable('custom_points')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn(
            'customer_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Customer ID'
        )->addColumn(
            'points',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => 0],
            'Points'
        )->addColumn(
            'action',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Action'
        )->addColumn(
            'description',
            Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'Description'
        )->addColumn(
            'order_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'Order ID'
        )->addColumn(
            'product_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'Product ID'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
        )->addIndex(
            $installer->getIdxName('custom_points', ['customer_id']),
            ['customer_id']
        )->addIndex(
            $installer->getIdxName('custom_points', ['action']),
            ['action']
        )->addIndex(
            $installer->getIdxName('custom_points', ['created_at']),
            ['created_at']
        )->setComment(
            'Custom Points Table'
        );
        $installer->getConnection()->createTable($table);

        // Create custom_points_balance table
        $table = $installer->getConnection()->newTable(
            $installer->getTable('custom_points_balance')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn(
            'customer_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Customer ID'
        )->addColumn(
            'total_points',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => 0],
            'Total Points'
        )->addColumn(
            'used_points',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => 0],
            'Used Points'
        )->addColumn(
            'available_points',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => 0],
            'Available Points'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
        )->addIndex(
            $installer->getIdxName('custom_points_balance', ['customer_id']),
            ['customer_id'],
            ['type' => 'unique']
        )->setComment(
            'Custom Points Balance Table'
        );
        $installer->getConnection()->createTable($table);

        // Create custom_points_cart table
        $table = $installer->getConnection()->newTable(
            $installer->getTable('custom_points_cart')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn(
            'quote_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Quote ID'
        )->addColumn(
            'customer_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Customer ID'
        )->addColumn(
            'points_used',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => 0],
            'Points Used'
        )->addColumn(
            'discount_amount',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Discount Amount'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
        )->addIndex(
            $installer->getIdxName('custom_points_cart', ['quote_id']),
            ['quote_id'],
            ['type' => 'unique']
        )->addIndex(
            $installer->getIdxName('custom_points_cart', ['customer_id']),
            ['customer_id']
        )->setComment(
            'Custom Points Cart Table'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}

<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;

use Sarus\Sarus\Model\ResourceModel\Quote\Item\Attribute as ResourceQuoteAttribute;
use Sarus\Sarus\Model\ResourceModel\Order\Item\Attribute as ResourceOrderAttribute;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->installQuoteItemAttributeTable($setup);
        $this->installOrderItemAttributeTable($setup);

        $setup->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return void
     */
    private function installQuoteItemAttributeTable($setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(ResourceQuoteAttribute::TABLE_NAME))
            ->addColumn(
                'attribute_id',
                Table::TYPE_INTEGER,
                null,
                ['primary' => true, 'identity' => true, 'unsigned' => true, 'nullable' => false],
                'Attribute ID'
            )
            ->addColumn(
                'quote_item_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Quote Item ID'
            )
            ->addColumn(
                'course_uuid',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Sarus Course UUID'
            )
            ->addIndex(
                $setup->getIdxName(ResourceQuoteAttribute::TABLE_NAME, ['quote_item_id'], AdapterInterface::INDEX_TYPE_UNIQUE),
                ['quote_item_id'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addForeignKey(
                $setup->getFkName(ResourceQuoteAttribute::TABLE_NAME, 'quote_item_id', 'quote_item', 'item_id'),
                'quote_item_id',
                $setup->getTable('quote_item'),
                'item_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Sarus Quote Item Attribute Table');
        $setup->getConnection()->createTable($table);
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return void
     */
    private function installOrderItemAttributeTable($setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(ResourceOrderAttribute::TABLE_NAME))
            ->addColumn(
                'attribute_id',
                Table::TYPE_INTEGER,
                null,
                ['primary' => true, 'identity' => true, 'unsigned' => true, 'nullable' => false],
                'Attribute ID'
            )
            ->addColumn(
                'order_item_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Order Item ID'
            )
            ->addColumn(
                'course_uuid',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Sarus Course UUID'
            )
            ->addIndex(
                $setup->getIdxName(ResourceOrderAttribute::TABLE_NAME, ['order_item_id'], AdapterInterface::INDEX_TYPE_UNIQUE),
                ['order_item_id'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addForeignKey(
                $setup->getFkName(ResourceOrderAttribute::TABLE_NAME, 'order_item_id', 'sales_order_item', 'item_id'),
                'order_item_id',
                $setup->getTable('sales_order_item'),
                'item_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Sarus Order Item Attribute Table');
        $setup->getConnection()->createTable($table);
    }
}

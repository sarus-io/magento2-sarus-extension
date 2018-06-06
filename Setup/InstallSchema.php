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

use Sarus\Sarus\Model\Record\Submission as SubmissionRecord;
use Sarus\Sarus\Model\ResourceModel\Submission as SubmissionResource;
use Sarus\Sarus\Model\ResourceModel\Order\Item\Attribute as OrderAttributeResource;

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

        $this->createOrderItemAttributeTable($setup);
        $this->createSubmissionQueueTable($setup);

        $setup->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return void
     */
    private function createOrderItemAttributeTable($setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(OrderAttributeResource::TABLE_NAME))
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
                $setup->getIdxName(OrderAttributeResource::TABLE_NAME, ['order_item_id'], AdapterInterface::INDEX_TYPE_UNIQUE),
                ['order_item_id'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addForeignKey(
                $setup->getFkName(OrderAttributeResource::TABLE_NAME, 'order_item_id', 'sales_order_item', 'item_id'),
                'order_item_id',
                $setup->getTable('sales_order_item'),
                'item_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Sarus Order Item Attribute Table');
        $setup->getConnection()->createTable($table);
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return void
     */
    private function createSubmissionQueueTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable(
            $setup->getTable(SubmissionResource::TABLE_NAME)
        )->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'primary' => true, 'unsigned' => true, 'nullable' => false],
            'Id'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false],
            'Store ID'
        )->addColumn(
            'request',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'default' => ''],
            'Serialized Request'
        )->addColumn(
            'counter',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'Counter'
        )->addColumn(
            'status',
            Table::TYPE_TEXT,
            10,
            ['nullable' => false, 'default' => SubmissionRecord::STATUS_PENDING],
            'Status'
        )->addColumn(
            'error_message',
            Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'Error Message'
        )->addColumn(
            'creating_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Creating Time'
        )->addColumn(
            'submission_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true],
            'Submission Time'
        )->addForeignKey(
            $setup->getFkName(SubmissionResource::TABLE_NAME, 'store_id', 'store', 'store_id'),
            'store_id',
            $setup->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Sarus Submission Queue'
        );
        $setup->getConnection()->createTable($table);
    }
}

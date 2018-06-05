<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Service\Order\Item;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Sarus\Sarus\Api\OrderItemAttributeRepositoryInterface;
use Sarus\Sarus\Api\Data\OrderItemAttributeInterface;

class AttributeRepository implements OrderItemAttributeRepositoryInterface
{
    /**
     * @var \Sarus\Sarus\Api\Data\OrderItemAttributeInterfaceFactory
     */
    private $orderItemAttributeFactory;

    /**
     * @var \Sarus\Sarus\Model\Record\Order\Item\AttributeFactory
     */
    private $orderItemAttributeRecordFactory;

    /**
     * @var \Sarus\Sarus\Model\ResourceModel\Order\Item\Attribute
     */
    private $orderItemAttributeResource;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param \Sarus\Sarus\Api\Data\OrderItemAttributeInterfaceFactory $orderItemAttributeFactory
     * @param \Sarus\Sarus\Model\Record\Order\Item\AttributeFactory $orderItemAttributeRecordFactory
     * @param \Sarus\Sarus\Model\ResourceModel\Order\Item\Attribute $orderItemAttributeResource
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        \Sarus\Sarus\Api\Data\OrderItemAttributeInterfaceFactory $orderItemAttributeFactory,
        \Sarus\Sarus\Model\Record\Order\Item\AttributeFactory $orderItemAttributeRecordFactory,
        \Sarus\Sarus\Model\ResourceModel\Order\Item\Attribute $orderItemAttributeResource,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
    ) {
        $this->orderItemAttributeFactory = $orderItemAttributeFactory;
        $this->orderItemAttributeRecordFactory = $orderItemAttributeRecordFactory;
        $this->orderItemAttributeResource = $orderItemAttributeResource;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @param int $orderItemId
     * @return \Sarus\Sarus\Api\Data\OrderItemAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByOrderItemId($orderItemId)
    {
        $orderItemAttributeRecord = $this->orderItemAttributeRecordFactory->create();
        $this->orderItemAttributeResource->load($orderItemAttributeRecord, $orderItemId, OrderItemAttributeInterface::ORDER_ITEM_ID);

        if (!$orderItemAttributeRecord->getId()) {
            throw new NoSuchEntityException(
                __('Sarus order item attribute is not found for order item with id "%1".', $orderItemId)
            );
        }
        return $this->convertToDataObject($orderItemAttributeRecord);
    }

    /**
     * @param \Sarus\Sarus\Model\Order\Item\Attribute $orderItemAttributeRecord
     * @return \Sarus\Sarus\Api\Data\OrderItemAttributeInterface
     */
    private function convertToDataObject($orderItemAttributeRecord)
    {
        $orderItemAttribute = $this->orderItemAttributeFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $orderItemAttribute,
            $orderItemAttributeRecord->getData(),
            OrderItemAttributeInterface::class
        );
        return $orderItemAttribute;
    }

    /**
     * @param \Sarus\Sarus\Api\Data\OrderItemAttributeInterface $orderItemAttribute
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(OrderItemAttributeInterface $orderItemAttribute)
    {
        if (!$orderItemAttribute->getOrderItemId()) {
            throw new CouldNotSaveException(__('Order item ID is required'));
        }

        $orderItemAttributeRecord = $this->loadRecordByOrderItemId($orderItemAttribute->getOrderItemId());
        $orderItemAttributeRecord->setData(OrderItemAttributeInterface::ORDER_ITEM_ID, $orderItemAttribute->getOrderItemId());
        $orderItemAttributeRecord->setData(OrderItemAttributeInterface::COURSE_UUID, $orderItemAttribute->getCourseUuid());

        try {
            $this->orderItemAttributeResource->save($orderItemAttributeRecord);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save sarus order item attribute: %1', $e->getMessage()));
        }
    }

    /**
     * @param int $orderItemId
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteByOrderItemId($orderItemId)
    {
        $orderItemAttributeRecord = $this->loadRecordByOrderItemId($orderItemId);
        if (!$orderItemAttributeRecord->getId()) {
            return;
        }

        try {
            $this->orderItemAttributeResource->delete($orderItemAttributeRecord);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete sarus order item attribute: %1', $e->getMessage()));
        }
    }

    /**
     * @param int $quiteItemId
     * @return \Sarus\Sarus\Model\Record\Order\Item\Attribute
     */
    private function loadRecordByOrderItemId($quiteItemId)
    {
        $orderItemAttributeRecord = $this->orderItemAttributeRecordFactory->create();
        $this->orderItemAttributeResource->load(
            $orderItemAttributeRecord,
            $quiteItemId,
            OrderItemAttributeInterface::ORDER_ITEM_ID
        );
        return $orderItemAttributeRecord;
    }
}

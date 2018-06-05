<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Service\Order\Item;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderItemInterface;
use Sarus\Sarus\Api\OrderItemAttributeManagementInterface;

class AttributeManagement implements OrderItemAttributeManagementInterface
{
    /**
     * @var \Sarus\Sarus\Api\OrderItemAttributeRepositoryInterface
     */
    private $orderItemAttributeRepository;

    /**
     * @var \Sarus\Sarus\Api\Data\OrderItemAttributeInterfaceFactory
     */
    private $orderItemAttributeFactory;

    /**
     * @var \Magento\Framework\Api\ExtensionAttributesFactory
     */
    private $extensionAttributesFactory;

    /**
     * @param \Sarus\Sarus\Api\OrderItemAttributeRepositoryInterface $orderItemAttributeRepository
     * @param \Sarus\Sarus\Api\Data\OrderItemAttributeInterfaceFactory $orderItemAttributeFactory
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionAttributesFactory
     */
    public function __construct(
        \Sarus\Sarus\Api\OrderItemAttributeRepositoryInterface $orderItemAttributeRepository,
        \Sarus\Sarus\Api\Data\OrderItemAttributeInterfaceFactory $orderItemAttributeFactory,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionAttributesFactory
    ) {
        $this->orderItemAttributeRepository = $orderItemAttributeRepository;
        $this->orderItemAttributeFactory = $orderItemAttributeFactory;
        $this->extensionAttributesFactory = $extensionAttributesFactory;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderItemInterface $orderItem
     * @return \Sarus\Sarus\Api\Data\OrderItemAttributeInterface
     */
    public function getForOrderItem(OrderItemInterface $orderItem)
    {
        $orderItemAttributes = $orderItem->getExtensionAttributes() ?: $this->extensionAttributesFactory->create(OrderItemInterface::class);
        $orderItem->setExtensionAttributes($orderItemAttributes);

        $orderItemSarus = $orderItemAttributes->getSarus();
        if (!$orderItemSarus && $orderItem->getItemId()) {
            $orderItemSarus = $this->getByOrderItemId($orderItem->getItemId());
        }

        if (!$orderItemSarus) {
            $orderItemSarus = $this->orderItemAttributeFactory->create();
        }

        $orderItemAttributes->setSarus($orderItemSarus);
        $orderItemSarus->setOrderItemId($orderItem->getItemId());
        return $orderItemSarus;
    }

    /**
     * @param int $orderItemId
     * @return \Sarus\Sarus\Api\Data\OrderItemAttributeInterface|null
     */
    private function getByOrderItemId($orderItemId)
    {
        try {
            $orderItemSarus = $this->orderItemAttributeRepository->getByOrderItemId($orderItemId);
        } catch (NoSuchEntityException $e) {
            $orderItemSarus = null;
        }
        return $orderItemSarus;
    }
}

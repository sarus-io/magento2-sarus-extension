<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Plugin\Order;

class ItemRepository
{
    /**
     * @var \Sarus\Sarus\Model\Config\General
     */
    private $configGeneral;

    /**
     * @var \Sarus\Sarus\Api\OrderItemAttributeRepositoryInterface
     */
    private $orderItemAttributeRepository;

    /**
     * @param \Sarus\Sarus\Model\Config\General $configGeneral
     * @param \Sarus\Sarus\Api\OrderItemAttributeRepositoryInterface $orderItemAttributeRepository
     */
    public function __construct(
        \Sarus\Sarus\Model\Config\General $configGeneral,
        \Sarus\Sarus\Api\OrderItemAttributeRepositoryInterface $orderItemAttributeRepository
    ) {
        $this->configGeneral = $configGeneral;
        $this->orderItemAttributeRepository = $orderItemAttributeRepository;
    }

    /**
     * @param \Magento\Sales\Api\OrderItemRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\OrderItemInterface $orderItem
     * @return \Magento\Sales\Api\Data\OrderItemInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave($subject, $orderItem)
    {
        if ($this->configGeneral->isEnabled($orderItem->getStoreId())
            && $orderItem->getExtensionAttributes()
            && $orderItem->getExtensionAttributes()->getSarus()
            && $orderItem->getExtensionAttributes()->getSarus()->getCourseUuid()
        ) {
            $this->saveOrderItemAttribute($orderItem);
        }

        return $orderItem;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderItemInterface $orderItem
     * @return void
     */
    private function saveOrderItemAttribute($orderItem)
    {
        $orderItemSarus = $orderItem->getExtensionAttributes()->getSarus();
        $orderItemSarus->setOrderItemId($orderItem->getItemId());

        $this->orderItemAttributeRepository->save($orderItemSarus);
    }
}

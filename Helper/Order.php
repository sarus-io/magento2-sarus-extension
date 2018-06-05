<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Helper;

class Order
{
    /**
     * @var \Sarus\Sarus\Api\OrderItemAttributeManagementInterface
     */
    private $orderItemAttributeManagement;

    /**
     * @param \Sarus\Sarus\Api\OrderItemAttributeManagementInterface $orderItemAttributeManagement
     */
    public function __construct(
        \Sarus\Sarus\Api\OrderItemAttributeManagementInterface $orderItemAttributeManagement
    ) {
        $this->orderItemAttributeManagement = $orderItemAttributeManagement;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    public function getSarusProductUuids($order)
    {
        $uuids = [];

        /** @var \Magento\Sales\Model\Order\Item $orderItem */
        foreach ($order->getAllItems() as $orderItem) {
            if ($orderItem->getProduct()->isComposite()) {
                continue;
            }

            $orderItemSarus = $this->orderItemAttributeManagement->getForOrderItem($orderItem);
            if ($orderItemSarus->getCourseUuid()) {
                $uuids[] = $orderItemSarus->getCourseUuid();
            }
        }

        return $uuids;
    }
}

<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Helper;

class Creditmemo
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
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return array
     */
    public function getSarusProductUuids($creditmemo)
    {
        $uuids = [];

        /** @var \Magento\Sales\Model\Order\Creditmemo\Item $creditmemoItem */
        foreach ($creditmemo->getAllItems() as $creditmemoItem) {
            $orderItem = $creditmemoItem->getOrderItem();
            if ($orderItem->getProduct()->isComposite()) {
                continue;
            }

            $orderItemSarus = $this->orderItemAttributeManagement->getForOrderItem($orderItem);
            if (!$orderItemSarus->getCourseUuid()) {
                $uuids[] = $orderItemSarus->getCourseUuid();
            }
        }

        return $uuids;
    }
}

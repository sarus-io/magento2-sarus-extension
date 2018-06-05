<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Api;

use Magento\Sales\Api\Data\OrderItemInterface;

interface OrderItemAttributeManagementInterface
{
    /**
     * @param \Magento\Sales\Api\Data\OrderItemInterface $order
     * @return \Sarus\Sarus\Api\Data\OrderItemAttributeInterface
     */
    public function getForOrderItem(OrderItemInterface $order);
}

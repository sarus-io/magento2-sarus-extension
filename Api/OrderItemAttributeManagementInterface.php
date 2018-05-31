<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Api;

interface OrderItemAttributeManagementInterface
{
    /**
     * @param \Magento\Sales\Api\Data\OrderItemInterface $order
     * @return \Sarus\Sarus\Api\Data\OrderItemAttributeInterface
     */
    public function getForOrderItem($order);
}

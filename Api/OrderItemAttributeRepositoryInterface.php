<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Api;

use Sarus\Sarus\Api\Data\OrderItemAttributeInterface;

interface OrderItemAttributeRepositoryInterface
{
    /**
     * @param int $orderItemId
     * @return \Sarus\Sarus\Api\Data\OrderItemAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByOrderItemId($orderItemId);

    /**
     * @param \Sarus\Sarus\Api\Data\OrderItemAttributeInterface $orderAttribute
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(OrderItemAttributeInterface $orderAttribute);

    /**
     * @param int $orderItemId
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteByOrderItemId($orderItemId);
}

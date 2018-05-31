<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Api\Data;

interface OrderItemAttributeInterface
{
    const ORDER_ITEM_ID = 'order_item_id';
    const COURSE_UUID = 'course_uuid';

    /**
     * @return int
     */
    public function getOrderItemId();

    /**
     * @param int $orderItemId
     * @return \Sarus\Sarus\Api\Data\OrderItemAttributeInterface
     */
    public function setOrderItemId($orderItemId);

    /**
     * @return string
     */
    public function getCourseUuid();

    /**
     * @param string $courseUuid
     * @return \Sarus\Sarus\Api\Data\OrderItemAttributeInterface
     */
    public function setCourseUuid($courseUuid);
}

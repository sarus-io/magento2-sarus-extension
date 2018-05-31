<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Model\Order\Item;

use Magento\Framework\DataObject;

class Attribute extends DataObject implements \Sarus\Sarus\Api\Data\OrderItemAttributeInterface
{
    /**
     * @return int
     */
    public function getOrderItemId()
    {
        return $this->_getData(self::ORDER_ITEM_ID);
    }

    /**
     * @param int $orderItemId
     * @return \Sarus\Sarus\Api\Data\OrderItemAttributeInterface
     */
    public function setOrderItemId($orderItemId)
    {
        return $this->setData(self::ORDER_ITEM_ID, $orderItemId);
    }

    /**
     * @return string
     */
    public function getCourseUuid()
    {
        return $this->_getData(self::COURSE_UUID);
    }

    /**
     * @param string $courseUuid
     * @return \Sarus\Sarus\Api\Data\OrderItemAttributeInterface
     */
    public function setCourseUuid($courseUuid)
    {
        return $this->setData(self::COURSE_UUID, $courseUuid);
    }
}

<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Model\ResourceModel\Order\Item;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb as ResourceModelAbstractDb;

class Attribute extends ResourceModelAbstractDb
{
    const TABLE_NAME = 'sarus_order_item_attribute';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, 'attribute_id');
    }
}

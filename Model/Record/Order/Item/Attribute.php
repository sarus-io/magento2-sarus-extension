<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Model\Record\Order\Item;

use Sarus\Sarus\Model\ResourceModel\Order\Item\Attribute as ResourceOrderItemAttribute;

class Attribute extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceOrderItemAttribute::class);
    }
}

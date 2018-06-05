<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Model\Record\Order\Item;

use Magento\Framework\Model\AbstractModel;
use Sarus\Sarus\Model\ResourceModel\Order\Item\Attribute as OrderItemAttributeResource;

class Attribute extends AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(OrderItemAttributeResource::class);
    }
}

<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Model\Record\Quote\Item;

use Sarus\Sarus\Model\ResourceModel\Quote\Item\Attribute as ResourceQuoteItemAttribute;

class Attribute extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceQuoteItemAttribute::class);
    }
}

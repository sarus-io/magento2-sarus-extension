<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Helper;

use Sarus\Sarus\Model\Product\Type as SarusProduct;

class Product
{
    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function isSarus($product)
    {
        return !empty($product->getData(SarusProduct::ATTRIBUTE_COURSE_UUID));
    }
}

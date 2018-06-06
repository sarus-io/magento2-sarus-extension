<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Helper;

use Magento\Catalog\Model\Product as CatalogProduct;

class Product
{
    const ATTRIBUTE_SET_NAME = 'Sarus';

    const ATTRIBUTE_COURSE_UUID = 'sarus_course_uuid';

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function isSarus(CatalogProduct $product)
    {
        return !empty($product->getData(self::ATTRIBUTE_COURSE_UUID));
    }
}

<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Model\Product;

class Type extends \Magento\Catalog\Model\Product\Type\AbstractType
{
    const TYPE_CODE = 'sarus';

    const ATTRIBUTE_SET_NAME = 'Sarus';

    const ATTRIBUTE_COURSE_UUID = 'sarus_course_uuid';

    /**
     * Check is virtual product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function isVirtual($product)
    {
        return true;
    }

    /**
     * Check that product of this type has weight
     *
     * @return bool
     */
    public function hasWeight()
    {
        return false;
    }

    /**
     * Delete data specific for Virtual product type
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return void
     */
    public function deleteTypeSpecificData(\Magento\Catalog\Model\Product $product)
    {
    }
}

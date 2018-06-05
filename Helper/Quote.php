<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Helper;

class Quote
{
    /**
     * @var \Sarus\Sarus\Helper\Product
     */
    private $productHelper;

    /**
     * @param \Sarus\Sarus\Helper\Product $productHelper
     */
    public function __construct(\Sarus\Sarus\Helper\Product $productHelper)
    {
        $this->productHelper = $productHelper;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return bool
     */
    public function hasSarusProduct($quote)
    {
        if (!$quote->hasData('has_sarus_product')) {
            $quote->setData('has_sarus_product', $this->hasSarusProductInItems($quote));
        }

        return $quote->getData('has_sarus_product');
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return bool
     */
    private function hasSarusProductInItems($quote)
    {
        $result = false;

        /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
        foreach ($quote->getAllVisibleItems() as $quoteItem) {
            if ($this->hasQuoteItemSarusProduct($quoteItem)) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @return bool
     */
    public function hasQuoteItemSarusProduct($quoteItem)
    {
        return $quoteItem->getChildren()
            ? $this->hasChildItemsSarusProduct($quoteItem)
            : $this->productHelper->isSarus($quoteItem->getProduct());
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @return bool
     */
    private function hasChildItemsSarusProduct($quoteItem)
    {
        $result = false;
        /** @var \Magento\Quote\Model\Quote\Item $childItem */
        foreach ($quoteItem->getChildren() as $childItem) {
            if ($this->productHelper->isSarus($childItem->getProduct())) {
                $result = true;
                break;
            }
        }
        return $result;
    }
}

<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Api;

interface QuoteItemAttributeManagementInterface
{
    /**
     * @param \Magento\Quote\Api\Data\CartItemInterface $quoteItem
     * @return \Sarus\Sarus\Api\Data\QuoteItemAttributeInterface
     */
    public function getForQuoteItem($quoteItem);
}

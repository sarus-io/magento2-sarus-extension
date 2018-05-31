<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Api;

use Sarus\Sarus\Api\Data\QuoteItemAttributeInterface;

interface QuoteItemAttributeRepositoryInterface
{
    /**
     * @param int $quoteItemId
     * @return \Sarus\Sarus\Api\Data\QuoteItemAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByQuoteItemId($quoteItemId);

    /**
     * @param \Sarus\Sarus\Api\Data\QuoteItemAttributeInterface $quoteItemAttribute
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(QuoteItemAttributeInterface $quoteItemAttribute);

    /**
     * @param int $quoteItemId
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteByQuoteItemId($quoteItemId);
}

<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Service\Quote\Item;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartItemInterface;

class AttributeManagement implements \Sarus\Sarus\Api\QuoteItemAttributeManagementInterface
{
    /**
     * @var \Sarus\Sarus\Api\QuoteItemAttributeRepositoryInterface
     */
    private $quoteItemAttributeRepository;

    /**
     * @var \Sarus\Sarus\Api\Data\QuoteItemAttributeInterfaceFactory
     */
    private $quoteItemAttributeFactory;

    /**
     * @var \Magento\Framework\Api\ExtensionAttributesFactory
     */
    private $extensionAttributesFactory;

    /**
     * @param \Sarus\Sarus\Api\QuoteItemAttributeRepositoryInterface $quoteItemAttributeRepository
     * @param \Sarus\Sarus\Api\Data\QuoteItemAttributeInterfaceFactory $quoteItemAttributeFactory
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionAttributesFactory
     */
    public function __construct(
        \Sarus\Sarus\Api\QuoteItemAttributeRepositoryInterface $quoteItemAttributeRepository,
        \Sarus\Sarus\Api\Data\QuoteItemAttributeInterfaceFactory $quoteItemAttributeFactory,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionAttributesFactory
    ) {
        $this->quoteItemAttributeRepository = $quoteItemAttributeRepository;
        $this->quoteItemAttributeFactory = $quoteItemAttributeFactory;
        $this->extensionAttributesFactory = $extensionAttributesFactory;
    }

    /**
     * @param \Magento\Quote\Api\Data\CartItemInterface $quoteItem
     * @return \Sarus\Sarus\Api\Data\QuoteItemAttributeInterface
     */
    public function getForQuoteItem($quoteItem)
    {
        $quoteItemAttributes = $quoteItem->getExtensionAttributes() ?: $this->extensionAttributesFactory->create(CartItemInterface::class);
        $quoteItem->setExtensionAttributes($quoteItemAttributes);

        $quoteItemSarus = $quoteItemAttributes->getSarus();
        if (!$quoteItemSarus && $quoteItem->getItemId()) {
            $quoteItemSarus = $this->getByQuoteItemId($quoteItem->getItemId());
        }

        if (!$quoteItemSarus) {
            $quoteItemSarus = $this->quoteItemAttributeFactory->create();
        }

        $quoteItemAttributes->setSarus($quoteItemSarus);
        $quoteItemSarus->setQuoteItemId($quoteItem->getItemId());
        return $quoteItemSarus;
    }

    /**
     * @param $quoteItemId
     * @return \Sarus\Sarus\Api\Data\QuoteItemAttributeInterface|null
     */
    private function getByQuoteItemId($quoteItemId)
    {
        try {
            $quoteItemSarus = $this->quoteItemAttributeRepository->getByQuoteItemId($quoteItemId);
        } catch (NoSuchEntityException $e) {
            $quoteItemSarus = null;
        }
        return $quoteItemSarus;
    }
}

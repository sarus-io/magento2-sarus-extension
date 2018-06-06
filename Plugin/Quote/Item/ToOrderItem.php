<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Plugin\Quote\Item;

use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote\Item\ToOrderItem as QuoteItemToOrderItem;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Sarus\Sarus\Helper\Product as SarusProduct;
use Sarus\Sarus\Api\Data\OrderItemAttributeInterface;

class ToOrderItem
{
    /**
     * @var \Sarus\Sarus\Model\Config\General
     */
    private $configGeneral;

    /**
     * @param \Sarus\Sarus\Model\Config\General $configGeneral
     */
    public function __construct(
        \Sarus\Sarus\Model\Config\General $configGeneral
    ) {
        $this->configGeneral = $configGeneral;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item\ToOrderItem $subject
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @param array $data
     * @return array|null
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeConvert(QuoteItemToOrderItem $subject, QuoteItem $quoteItem, $data = [])
    {
        $result = false;
        if ($this->configGeneral->isEnabled($quoteItem->getStoreId())) {
            $result = $this->copyUuid($quoteItem, $data);
        }

        return $result ? [$quoteItem, $data] : null;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @param array $data
     * @return bool
     */
    private function copyUuid($quoteItem, &$data)
    {
        $sarusUuid = $quoteItem->getProduct()->getData(SarusProduct::ATTRIBUTE_COURSE_UUID);
        if (!empty($sarusUuid)) {
            $data[CartItemInterface::EXTENSION_ATTRIBUTES_KEY]['sarus'][OrderItemAttributeInterface::COURSE_UUID] = $sarusUuid;
        }

        return !empty($sarusUuid);
    }
}

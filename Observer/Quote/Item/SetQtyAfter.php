<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Observer\Quote\Item;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class SetQtyAfter implements ObserverInterface
{
    /**
     * @var \Sarus\Sarus\Model\Config\General
     */
    private $configGeneral;

    /**
     * @var \Sarus\Sarus\Helper\Quote
     */
    private $quoteHelper;

    /**
     * @param \Sarus\Sarus\Model\Config\General $configGeneral
     * @param \Sarus\Sarus\Helper\Quote $quoteHelper
     */
    public function __construct(
        \Sarus\Sarus\Model\Config\General $configGeneral,
        \Sarus\Sarus\Helper\Quote $quoteHelper
    ) {
        $this->configGeneral = $configGeneral;
        $this->quoteHelper = $quoteHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
        $quoteItem = $observer->getData('item');

        if (!$this->configGeneral->isEnabled($quoteItem->getStoreId())) {
            return;
        }

        if ($this->quoteHelper->hasQuoteItemSarusProduct($quoteItem)) {
            $quoteItem->setData('qty', 1);
        }
    }
}

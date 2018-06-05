<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Observer\Checkout;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class IsAllowedGuest implements ObserverInterface
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
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote  = $observer->getData('quote');

        $result = $observer->getData('result');

        if (!$this->configGeneral->isEnabled($quote->getStoreId())) {
            return;
        }

        if ($this->quoteHelper->hasSarusProduct($quote)) {
            $result->setIsAllowed(false);
        }
    }
}

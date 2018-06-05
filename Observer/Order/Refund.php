<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Observer\Order;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Sarus\Request\Enrollment\Deactivate as SarusDeactivate;

class Refund implements ObserverInterface
{
    /**
     * @var \Sarus\Sarus\Model\Config\General
     */
    private $configGeneral;

    /**
     * @var \Sarus\Sarus\Helper\Creditmemo
     */
    private $creditmemoHelper;

    /**
     * @var \Sarus\Sarus\Service\Queue
     */
    private $queue;

    /**
     * @param \Sarus\Sarus\Model\Config\General $configGeneral
     * @param \Sarus\Sarus\Helper\Creditmemo $creditmemoHelper
     * @param \Sarus\Sarus\Service\Queue $queue
     */
    public function __construct(
        \Sarus\Sarus\Model\Config\General $configGeneral,
        \Sarus\Sarus\Helper\Creditmemo $creditmemoHelper,
        \Sarus\Sarus\Service\Queue $queue
    ) {
        $this->configGeneral = $configGeneral;
        $this->creditmemoHelper = $creditmemoHelper;
        $this->queue = $queue;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
        $creditmemo = $observer->getData('creditmemo');

        if (!$this->configGeneral->isEnabled($creditmemo->getStoreId())) {
            return;
        }

        $sarusProductUuids = $this->creditmemoHelper->getSarusProductUuids($creditmemo);
        if (empty($sarusProductUuids)) {
            return;
        }

        $sarusRequest = new SarusDeactivate($creditmemo->getOrder()->getCustomerEmail(), $sarusProductUuids);
        $this->queue->addRequest($sarusRequest, $creditmemo->getStoreId());
    }
}

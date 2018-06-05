<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Observer\Order;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Sarus\Request\User as SarusUser;
use Sarus\Request\Product\Purchase as SarusPurchase;

class PlaceAfter implements ObserverInterface
{
    /**
     * @var \Sarus\Sarus\Model\Config\General
     */
    private $configGeneral;

    /**
     * @var \Sarus\Sarus\Helper\Order
     */
    private $orderHelper;

    /**
     * @var \Sarus\Sarus\Service\Queue
     */
    private $queue;

    /**
     * @param \Sarus\Sarus\Model\Config\General $configGeneral
     * @param \Sarus\Sarus\Helper\Order $orderHelper
     * @param \Sarus\Sarus\Service\Queue $queue
     */
    public function __construct(
        \Sarus\Sarus\Model\Config\General $configGeneral,
        \Sarus\Sarus\Helper\Order $orderHelper,
        \Sarus\Sarus\Service\Queue $queue
    ) {
        $this->configGeneral = $configGeneral;
        $this->orderHelper = $orderHelper;
        $this->queue = $queue;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getData('order');

        if (!$this->configGeneral->isEnabled($order->getStoreId())) {
            return;
        }

        $sarusProductUuids = $this->orderHelper->getSarusProductUuids($order);
        if (empty($sarusProductUuids)) {
            return;
        }

        $sarusRequest = new SarusPurchase($sarusProductUuids, $this->createSarusUser($order));
        $this->queue->addRequest($sarusRequest, $order->getStoreId());
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return \Sarus\Request\User
     */
    private function createSarusUser($order)
    {
        $billingAddress = $order->getBillingAddress();

        $sarusUser = new SarusUser(
            $order->getCustomerEmail(),
            $billingAddress->getFirstname(),
            $billingAddress->getLastname(),
            $order->getCustomerId()
        );
        $sarusUser->setAddress1($billingAddress->getStreet()[0]);
        if (isset($billingAddress->getStreet()[1])) {
            $sarusUser->setAddress2($billingAddress->getStreet()[1]);
        }

        $sarusUser->setCity($billingAddress->getCity());
        $sarusUser->setRegion($billingAddress->getRegion());
        $sarusUser->setPostalCode($billingAddress->getPostcode());
        $sarusUser->setCountry($billingAddress->getCountryId());

        return $sarusUser;
    }
}

<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Observer\Catalog;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Sarus\Sarus\Helper\Product as SarusProduct;
use Sarus\Request\Product\Unlink as SarusUnlink;

class ProductDeleteBefore implements ObserverInterface
{
    /**
     * @var \Sarus\Sarus\Model\Config\General
     */
    private $configGeneral;

    /**
     * @var \Magento\Store\Api\WebsiteRepositoryInterface
     */
    private $websiteRepository;

    /**
     * @var \Sarus\Sarus\Helper\Product
     */
    private $productHelper;

    /**
     * @var \Sarus\Sarus\Service\Platform
     */
    private $platform;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Sarus\Sarus\Model\Config\General $configGeneral
     * @param \Magento\Store\Api\WebsiteRepositoryInterface $websiteRepository
     * @param \Sarus\Sarus\Helper\Product $productHelper
     * @param \Sarus\Sarus\Service\Platform $platform
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Sarus\Sarus\Model\Config\General $configGeneral,
        \Magento\Store\Api\WebsiteRepositoryInterface $websiteRepository,
        \Sarus\Sarus\Helper\Product $productHelper,
        \Sarus\Sarus\Service\Platform $platform,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->configGeneral = $configGeneral;
        $this->websiteRepository = $websiteRepository;
        $this->productHelper = $productHelper;
        $this->platform = $platform;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getData('product');

        if (!$this->productHelper->isSarus($product)) {
            return;
        }

        foreach ($product->getWebsiteIds() as $websiteId) {
            /** @var \Magento\Store\Model\Website $website */
            $website = $this->websiteRepository->getById($websiteId);
            if ($website->getCode() === 'admin') {
                continue;
            }

            $storeId = $website->getDefaultStore()->getStoreId();
            if (!$this->configGeneral->isEnabled($storeId)) {
                continue;
            }

            if ($this->unlinkProduct($product, $storeId)) {
                $this->messageManager->addSuccessMessage(__('Product has been successfully unlinked from Sarus.'));
                break;
            }
        }
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param int $storeId
     * @return bool
     */
    private function unlinkProduct($product, $storeId)
    {
        $sarusRequest = new SarusUnlink($product->getData(SarusProduct::ATTRIBUTE_COURSE_UUID));
        try {
            $this->platform->sendRequest($sarusRequest, $storeId);
            $result = true;
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $result = false;
        }
        return $result;
    }
}

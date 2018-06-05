<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Service;

use Sarus\Sarus\Platform\SdkFactory;

class Platform
{
    /**
     * @var \Sarus\Sarus\Platform\SdkFactory
     */
    private $sdkFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Sarus\Sdk[]
     */
    private $register = [];

    /**
     * @param \Sarus\Sarus\Platform\SdkFactory $sdkFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Sarus\Sarus\Platform\SdkFactory $sdkFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->sdkFactory = $sdkFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param int|string|null $storeId
     * @return \Sarus\Sdk
     */
    public function getSdk($storeId = null)
    {
        $storeId = $this->storeManager->getStore($storeId)->getId();

        if (empty($this->register[$storeId])) {
            $this->register[$storeId] = $this->sdkFactory->create([SdkFactory::CONFIG_STORE => $storeId]);
        }

        return $this->register[$storeId];
    }

    /**
     * @param \Sarus\Request $sarusRequest
     * @param int|null $storeId
     * @return \Sarus\Response
     */
    public function sendRequest(\Sarus\Request $sarusRequest, $storeId = null)
    {
        return $this->getSdk($storeId)->handleRequest($sarusRequest);
    }
}

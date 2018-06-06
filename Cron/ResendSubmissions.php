<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Cron;

class ResendSubmissions
{
    /**
     * @var \Sarus\Sarus\Model\Config\General
     */
    private $configGeneral;

    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var \Sarus\Sarus\Service\QueueManager
     */
    private $queueManager;

    /**
     * @param \Sarus\Sarus\Model\Config\General $configGeneral
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     * @param \Sarus\Sarus\Service\QueueManager $queueManager
     */
    public function __construct(
        \Sarus\Sarus\Model\Config\General $configGeneral,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Sarus\Sarus\Service\QueueManager $queueManager
    ) {
        $this->configGeneral = $configGeneral;
        $this->storeRepository = $storeRepository;
        $this->queueManager = $queueManager;
    }

    /**
     * @return void
     */
    public function execute()
    {
        foreach ($this->storeRepository->getList() as $store) {
            if ($store->getId() == 0 || !$this->configGeneral->isEnabled($store->getId())) {
                continue;
            }

            $this->queueManager->sendFailedSubmissions($store->getId());
        }
    }
}

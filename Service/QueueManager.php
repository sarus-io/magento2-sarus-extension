<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Service;

use Sarus\Sarus\Model\Record\Submission as SubmissionRecord;
use Sarus\Sarus\Model\ResourceModel\Submission\Collection as  SubmissionCollection;

class QueueManager
{
    /**
     * @var \Sarus\Sarus\Model\Config\Api
     */
    private $configApi;

    /**
     * @var \Sarus\Sarus\Model\ResourceModel\Submission\CollectionFactory
     */
    private $submissionCollectionFactory;

    /**
     * @var \Sarus\Sarus\Service\Queue
     */
    private $queue;

    /**
     * @param \Sarus\Sarus\Model\Config\Api $configApi
     * @param \Sarus\Sarus\Model\ResourceModel\Submission\CollectionFactory $submissionCollectionFactory
     * @param \Sarus\Sarus\Service\Queue $queue
     */
    public function __construct(
        \Sarus\Sarus\Model\Config\Api $configApi,
        \Sarus\Sarus\Model\ResourceModel\Submission\CollectionFactory $submissionCollectionFactory,
        \Sarus\Sarus\Service\Queue $queue
    ) {
        $this->configApi = $configApi;
        $this->submissionCollectionFactory = $submissionCollectionFactory;
        $this->queue = $queue;
    }

    /**
     * @param int|null $storeId
     * @return void
     */
    public function sendPendingSubmissions($storeId = null)
    {
        /** @var \Sarus\Sarus\Model\ResourceModel\Submission\Collection $submissionCollection */
        $submissionCollection = $this->submissionCollectionFactory->create();
        $submissionCollection->filterStatus(SubmissionRecord::STATUS_PENDING);
        $submissionCollection->setOrder('entity_id', SubmissionCollection::SORT_ORDER_ASC);

        if ($storeId) {
            $submissionCollection->filterStore($storeId);
        }

        $this->queue->sendSubmissions($submissionCollection);
    }

    /**
     * @param int|null $storeId
     * @return void
     */
    public function sendFailedSubmissions($storeId = null)
    {
        /** @var \Sarus\Sarus\Model\ResourceModel\Submission\Collection $submissionCollection */
        $submissionCollection = $this->submissionCollectionFactory->create();
        $submissionCollection->filterStatus(SubmissionRecord::STATUS_FAIL);
        $submissionCollection->setOrder('entity_id', SubmissionCollection::SORT_ORDER_ASC);

        if ($storeId) {
            $submissionCollection->filterStore($storeId);
        }

        $threshold = $this->configApi->getMaxTimeResend();
        if ($threshold > 0) {
            $submissionCollection->filterCounter($threshold);
        }

        $this->queue->sendSubmissions($submissionCollection);
    }

    /**
     * @param int[] $submissionIds
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendByIds(array $submissionIds)
    {
        if (empty($submissionIds)) {
            throw new \InvalidArgumentException('$submissionIds cannot be empty.');
        }

        /** @var \Sarus\Sarus\Model\ResourceModel\Submission\Collection $submissionCollection */
        $submissionCollection = $this->submissionCollectionFactory->create();
        $submissionCollection->filterSubmissionIds($submissionIds);
        $submissionCollection->setOrder('entity_id', SubmissionCollection::SORT_ORDER_ASC);

        return $this->queue->sendSubmissions($submissionCollection);
    }
}

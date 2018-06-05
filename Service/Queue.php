<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Service;

use Sarus\Sarus\Model\Record\Submission as SubmissionRecord;

class Queue
{
    /**
     * @var \Sarus\Sarus\Model\Record\SubmissionFactory
     */
    private $submissionRecordFactory;

    /**
     * @var \Sarus\Sarus\Model\ResourceModel\Submission
     */
    private $submissionResource;

    /**
     * @var \Sarus\Sarus\Service\Platform
     */
    private $platform;

    /**
     * @param \Sarus\Sarus\Model\Record\SubmissionFactory $submissionRecordFactory
     * @param \Sarus\Sarus\Model\ResourceModel\Submission $submissionResource
     * @param \Sarus\Sarus\Service\Platform $platform
     */
    public function __construct(
        \Sarus\Sarus\Model\Record\SubmissionFactory $submissionRecordFactory,
        \Sarus\Sarus\Model\ResourceModel\Submission $submissionResource,
        \Sarus\Sarus\Service\Platform $platform
    ) {
        $this->submissionRecordFactory = $submissionRecordFactory;
        $this->submissionResource = $submissionResource;
        $this->platform = $platform;
    }

    /**
     * @param \Sarus\Sarus\Model\ResourceModel\Submission\Collection $submissionCollection
     * @return void
     */
    public function sendSubmissions($submissionCollection)
    {
        /** @var \Sarus\Sarus\Model\Record\Submission $submissionRecord */
        foreach ($submissionCollection as $submissionRecord) {
            $this->processSubmissionRecord($submissionRecord);
        }
    }

    /**
     * @param \Sarus\Sarus\Model\Record\Submission $submissionRecord
     * @return void
     */
    private function processSubmissionRecord($submissionRecord)
    {
        try {
            $sarusRequest = unserialize($submissionRecord->getRequest());
            $this->platform->sendRequest($sarusRequest, $submissionRecord->getStoreId());
            $submissionRecord->setStatus(SubmissionRecord::STATUS_DONE);
        } catch (\Exception $e) {
            $submissionRecord->setErrorMessage($e->getMessage());
            $submissionRecord->setStatus(SubmissionRecord::STATUS_ERROR);
        }

        $counter = $submissionRecord->getCounter() + 1;
        $submissionRecord->setCounter($counter);
        $this->submissionResource->save($submissionRecord);
    }

    /**
     * @param \Sarus\Request $sarusRequest
     * @param int $storeId
     * @return void
     */
    public function addRequest(\Sarus\Request $sarusRequest, $storeId)
    {
        /** @var \Sarus\Sarus\Model\Record\Submission $submissionRecord */
        $submissionRecord = $this->submissionRecordFactory->create();

        $submissionRecord->setRequest(serialize($sarusRequest));
        $submissionRecord->setStoreId($storeId);
        $submissionRecord->setCounter(0);
        $submissionRecord->setStatus(SubmissionRecord::STATUS_PENDING);

        $this->submissionResource->save($submissionRecord);
    }
}

<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Service;

use Sarus\Sarus\Model\Record\Submission as SubmissionRecord;
use Sarus\Client\Exception\HttpException as SarusHttpException;

class Queue
{
    /**
     * @var \Sarus\Sarus\Model\Config\Api
     */
    private $configApi;

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
     * @var \Sarus\Sarus\Model\FailNotification
     */
    private $failNotification;

    /**
     * @param \Sarus\Sarus\Model\Config\Api $configApi
     * @param \Sarus\Sarus\Model\Record\SubmissionFactory $submissionRecordFactory
     * @param \Sarus\Sarus\Model\ResourceModel\Submission $submissionResource
     * @param \Sarus\Sarus\Service\Platform $platform
     * @param \Sarus\Sarus\Model\FailNotification $failNotification
     */
    public function __construct(
        \Sarus\Sarus\Model\Config\Api $configApi,
        \Sarus\Sarus\Model\Record\SubmissionFactory $submissionRecordFactory,
        \Sarus\Sarus\Model\ResourceModel\Submission $submissionResource,
        \Sarus\Sarus\Service\Platform $platform,
        \Sarus\Sarus\Model\FailNotification $failNotification
    ) {
        $this->configApi = $configApi;
        $this->submissionRecordFactory = $submissionRecordFactory;
        $this->submissionResource = $submissionResource;
        $this->platform = $platform;
        $this->failNotification = $failNotification;
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
        $storeId = $submissionRecord->getStoreId();
        /** @var \Sarus\Request $sarusRequest */
        $sarusRequest = unserialize($submissionRecord->getRequest());

        try {
            $this->platform->sendRequest($sarusRequest, $storeId);
            $submissionRecord->setStatus(SubmissionRecord::STATUS_DONE);
        } catch (SarusHttpException $e) {
            $submissionRecord->setErrorMessage($e->getMessage());
            $submissionRecord->setStatus(SubmissionRecord::STATUS_ERROR);

            if (($submissionRecord->getCounter() + 1) === $this->configApi->getMaxTimeResend($storeId)) {
                $this->failNotification->notify(
                    $storeId,
                    $this->fetchCustomerEmail($sarusRequest),
                    $e->getRequest(),
                    $e->getResponse()
                );
            }
        }

        $counter = $submissionRecord->getCounter() + 1;
        $submissionRecord->setCounter($counter);
        $this->submissionResource->save($submissionRecord);
    }

    /**
     * @param \Sarus\Request $sarusRequest
     * @return string|null
     */
    private function fetchCustomerEmail($sarusRequest)
    {
        $requestBody = $sarusRequest->getBody();
        $customerEmail = !empty($requestBody['user']['email'])
            ? $requestBody['user']['email']
            : null;
        $customerEmail = $customerEmail === null && !empty($requestBody['email'])
            ? $requestBody['email']
            : $customerEmail;

        return $customerEmail;
    }
}

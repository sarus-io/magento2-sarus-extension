<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Service;

use Sarus\Sarus\Model\Record\Submission as SubmissionRecord;
use Sarus\Client\Exception\HttpException as SarusHttpException;
use Sarus\Request\CustomRequest as SarusCustomRequest;

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
     * @var \Sarus\Sarus\Model\ResourceModel\Submission\CollectionFactory
     */
    private $submissionCollectionFactory;

    /**
     * @var \Sarus\Sarus\Model\ResourceModel\Submission
     */
    private $submissionResource;

    /**
     * @var \Sarus\Sarus\Service\Platform
     */
    private $platform;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $jsonSerializer;

    /**
     * @var \Sarus\Sarus\Model\FailNotification
     */
    private $failNotification;

    /**
     * @param \Sarus\Sarus\Model\Config\Api $configApi
     * @param \Sarus\Sarus\Model\Record\SubmissionFactory $submissionRecordFactory
     * @param \Sarus\Sarus\Model\ResourceModel\Submission\CollectionFactory $submissionCollectionFactory
     * @param \Sarus\Sarus\Model\ResourceModel\Submission $submissionResource
     * @param \Sarus\Sarus\Service\Platform $platform
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
     * @param \Sarus\Sarus\Model\FailNotification $failNotification
     */
    public function __construct(
        \Sarus\Sarus\Model\Config\Api $configApi,
        \Sarus\Sarus\Model\Record\SubmissionFactory $submissionRecordFactory,
        \Sarus\Sarus\Model\ResourceModel\Submission\CollectionFactory $submissionCollectionFactory,
        \Sarus\Sarus\Model\ResourceModel\Submission $submissionResource,
        \Sarus\Sarus\Service\Platform $platform,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer,
        \Sarus\Sarus\Model\FailNotification $failNotification
    ) {
        $this->configApi = $configApi;
        $this->submissionRecordFactory = $submissionRecordFactory;
        $this->submissionCollectionFactory = $submissionCollectionFactory;
        $this->submissionResource = $submissionResource;
        $this->platform = $platform;
        $this->jsonSerializer = $jsonSerializer;
        $this->failNotification = $failNotification;
    }

    /**
     * @param \Sarus\Request $sarusRequest
     * @param int $storeId
     * @return bool
     */
    public function sendRequest(\Sarus\Request $sarusRequest, $storeId)
    {
        $submissionRecord = $this->addRequest($sarusRequest, $storeId);
        return $this->sendSubmissionRecord($submissionRecord);
    }

    /**
     * @param \Sarus\Request $sarusRequest
     * @param int $storeId
     * @return \Sarus\Sarus\Model\Record\Submission
     */
    public function addRequest(\Sarus\Request $sarusRequest, $storeId)
    {
        /** @var \Sarus\Sarus\Model\Record\Submission $submissionRecord */
        $submissionRecord = $this->submissionRecordFactory->create();

        $submissionRecord->setRequest($this->jsonSerializer->serialize($sarusRequest));
        $submissionRecord->setStoreId($storeId);
        $submissionRecord->setCounter(0);
        $submissionRecord->setStatus(SubmissionRecord::STATUS_PENDING);

        $this->submissionResource->save($submissionRecord);
        return $submissionRecord;
    }

    /**
     * @param \Sarus\Sarus\Model\ResourceModel\Submission\Collection $submissionCollection
     * @return int
     */
    public function sendSubmissions($submissionCollection)
    {
        $counter = 0;
        /** @var \Sarus\Sarus\Model\Record\Submission $submissionRecord */
        foreach ($submissionCollection as $submissionRecord) {
            $counter += $this->sendSubmissionRecord($submissionRecord) ? 1 : 0;
        }

        return $counter;
    }

    /**
     * @param \Sarus\Sarus\Model\Record\Submission $submissionRecord
     * @return bool
     */
    private function sendSubmissionRecord($submissionRecord)
    {
        $storeId = $submissionRecord->getStoreId();

        $sarusRequestData = $this->jsonSerializer->unserialize($submissionRecord->getRequest());

        /** @var \Sarus\Request $sarusRequest */
        $sarusRequest = SarusCustomRequest::fromArray($sarusRequestData);

        try {
            $this->platform->sendRequest($sarusRequest, $storeId);
            $submissionRecord->setStatus(SubmissionRecord::STATUS_DONE);
            $result = true;
        } catch (SarusHttpException $e) {
            $submissionRecord->setErrorMessage($e->getMessage());
            $submissionRecord->setStatus(SubmissionRecord::STATUS_FAIL);

            if (($submissionRecord->getCounter() + 1) === $this->configApi->getMaxTimeResend($storeId)) {
                $this->failNotification->notify(
                    $storeId,
                    $this->fetchCustomerEmail($sarusRequest),
                    $e->getRequest(),
                    $e->getResponse()
                );
            }
            $result = false;
        }

        $counter = $submissionRecord->getCounter() + 1;
        $submissionRecord->setCounter($counter);
        $this->submissionResource->save($submissionRecord);

        return $result;
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

    /**
     * @param int[] $submissionIds
     * @return void
     */
    public function deleteByIds(array $submissionIds)
    {
        /** @var \Sarus\Sarus\Model\ResourceModel\Submission\Collection $submissionCollection */
        $submissionCollection = $this->submissionCollectionFactory->create();
        if ($submissionIds) {
            $submissionCollection->filterSubmissionIds($submissionIds);
        }

        /** @var \Sarus\Sarus\Model\Record\Submission $submission */
        foreach ($submissionCollection as $submissionRecord) {
            $this->submissionResource->delete($submissionRecord);
        };
    }
}

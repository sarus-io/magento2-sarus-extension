<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Model\ResourceModel\Submission;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Sarus\Sarus\Model\ResourceModel\Submission as SubmissionResource;
use Sarus\Sarus\Model\Record\Submission as SubmissionRecord;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(SubmissionRecord::class, SubmissionResource::class);
    }

    /**
     * @param int $storeId
     * @return $this
     */
    public function filterStore($storeId)
    {
        $this->addFilter('store_id', $storeId);
        return $this;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function filterStatus($status)
    {
        $this->addFieldToFilter('status', ['eq' => $status]);
        return $this;
    }

    /**
     * @param int $threshold
     * @return $this
     */
    public function filterCounter($threshold)
    {
        $this->addFieldToFilter('counter', ['lt' => $threshold]);
        return $this;
    }
}

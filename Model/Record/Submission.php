<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Model\Record;

use Magento\Framework\Model\AbstractModel;
use Sarus\Sarus\Model\ResourceModel\Submission as SubmissionResource;

/**
 * @method int getStoreId()
 * @method $this setStoreId($storeId)
 * @method string getRequest()
 * @method $this setRequest($request)
 * @method int getCounter()
 * @method $this setCounter($counter)
 * @method string getStatus()
 * @method $this setStatus($status)
 * @method string getErrorMessage()
 * @method $this setErrorMessage($errorMessage)
 * @method string getCreatingTime()
 * @method $this setCreatingTime($creatingTime)
 * @method string getSubmissionTime()
 * @method $this setSubmissionTime($submissionTime)
 */
class Submission extends AbstractModel
{
    const STATUS_PENDING = 'pending';
    const STATUS_DONE = 'done';
    const STATUS_FAIL = 'fail';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(SubmissionResource::class);
    }
}

<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb as AbstractResourceModel;
use Sarus\Sarus\Model\Record\Submission as SubmissionRecord;

class Submission extends AbstractResourceModel
{
    const TABLE_NAME = 'sarus_submission';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, 'entity_id');
    }

    /**
     * @param \Sarus\Sarus\Model\Record\Submission|\Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    public function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getStatus() && $object->getStatus() != SubmissionRecord::STATUS_PENDING) {
            $object->setData('submission_time', new \Zend_Db_Expr('NOW()'));
        }

        return parent::_beforeSave($object);
    }
}

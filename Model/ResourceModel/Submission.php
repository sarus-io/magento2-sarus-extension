<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb as AbstractResourceModel;

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
}

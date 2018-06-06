<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Ui\Component\Listing\Column\Status;

use Sarus\Sarus\Model\Record\Submission;

class Options implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => Submission::STATUS_PENDING, 'label' => __('Pending')],
            ['value' => Submission::STATUS_DONE, 'label' => __('Done')],
            ['value' => Submission::STATUS_FAIL, 'label' => __('Fail')]
        ];
    }
}

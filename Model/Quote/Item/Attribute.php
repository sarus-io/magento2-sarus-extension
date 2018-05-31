<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Model\Quote\Item;

use Magento\Framework\DataObject;

class Attribute extends DataObject implements \Sarus\Sarus\Api\Data\QuoteItemAttributeInterface
{
    /**
     * @return int
     */
    public function getQuoteItemId()
    {
        return $this->_getData(self::QUOTE_ITEM_ID);
    }

    /**
     * @param int $quoteItemId
     * @return \Sarus\Sarus\Api\Data\QuoteItemAttributeInterface
     */
    public function setQuoteItemId($quoteItemId)
    {
        return $this->setData(self::QUOTE_ITEM_ID, $quoteItemId);
    }

    /**
     * @return string
     */
    public function getCourseUuid()
    {
        return $this->_getData(self::COURSE_UUID);
    }

    /**
     * @param string $courseUuid
     * @return \Sarus\Sarus\Api\Data\QuoteItemAttributeInterface
     */
    public function setCourseUuid($courseUuid)
    {
        return $this->setData(self::COURSE_UUID, $courseUuid);
    }
}

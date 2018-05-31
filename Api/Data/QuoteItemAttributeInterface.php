<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Api\Data;

interface QuoteItemAttributeInterface
{
    const QUOTE_ITEM_ID = 'quote_item_id';
    const COURSE_UUID = 'course_uuid';

    /**
     * @return int
     */
    public function getQuoteItemId();

    /**
     * @param int $quoteItemId
     * @return \Sarus\Sarus\Api\Data\QuoteItemAttributeInterface
     */
    public function setQuoteItemId($quoteItemId);

    /**
     * @return string
     */
    public function getCourseUuid();

    /**
     * @param string $courseUuid
     * @return \Sarus\Sarus\Api\Data\QuoteItemAttributeInterface
     */
    public function setCourseUuid($courseUuid);
}

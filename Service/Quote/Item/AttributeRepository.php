<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Service\Quote\Item;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Sarus\Sarus\Api\Data\QuoteItemAttributeInterface;

class AttributeRepository implements \Sarus\Sarus\Api\QuoteItemAttributeRepositoryInterface
{
    /**
     * @var \Sarus\Sarus\Api\Data\QuoteItemAttributeInterfaceFactory
     */
    private $quoteItemAttributeFactory;

    /**
     * @var \Sarus\Sarus\Model\Record\Quote\Item\AttributeFactory
     */
    private $quoteItemAttributeRecordFactory;

    /**
     * @var \Sarus\Sarus\Model\ResourceModel\Quote\Item\Attribute
     */
    private $quoteItemAttributeResource;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param \Sarus\Sarus\Api\Data\QuoteItemAttributeInterfaceFactory $quoteItemAttributeFactory
     * @param \Sarus\Sarus\Model\Record\Quote\Item\AttributeFactory $quoteItemAttributeRecordFactory
     * @param \Sarus\Sarus\Model\ResourceModel\Quote\Item\Attribute $quoteItemAttributeResource
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        \Sarus\Sarus\Api\Data\QuoteItemAttributeInterfaceFactory $quoteItemAttributeFactory,
        \Sarus\Sarus\Model\Record\Quote\Item\AttributeFactory $quoteItemAttributeRecordFactory,
        \Sarus\Sarus\Model\ResourceModel\Quote\Item\Attribute $quoteItemAttributeResource,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
    ) {
        $this->quoteItemAttributeFactory = $quoteItemAttributeFactory;
        $this->quoteItemAttributeRecordFactory = $quoteItemAttributeRecordFactory;
        $this->quoteItemAttributeResource = $quoteItemAttributeResource;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @param int $quoteItemId
     * @return \Sarus\Sarus\Api\Data\QuoteItemAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByQuoteItemId($quoteItemId)
    {
        $quoteItemAttributeRecord = $this->quoteItemAttributeRecordFactory->create();
        $this->quoteItemAttributeResource->load($quoteItemAttributeRecord, $quoteItemId, QuoteItemAttributeInterface::QUOTE_ITEM_ID);

        if (!$quoteItemAttributeRecord->getId()) {
            throw new NoSuchEntityException(
                __('Sarus quote item attribute is not found for quote item with id "%1".', $quoteItemId)
            );
        }
        return $this->convertToDataObject($quoteItemAttributeRecord);
    }

    /**
     * @param \Sarus\Sarus\Model\Quote\Item\Attribute $quoteItemAttributeRecord
     * @return \Sarus\Sarus\Api\Data\QuoteItemAttributeInterface
     */
    private function convertToDataObject($quoteItemAttributeRecord)
    {
        $quoteItemAttribute = $this->quoteItemAttributeFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $quoteItemAttribute,
            $quoteItemAttributeRecord->getData(),
            QuoteItemAttributeInterface::class
        );
        return $quoteItemAttribute;
    }

    /**
     * @param \Sarus\Sarus\Api\Data\QuoteItemAttributeInterface $quoteItemAttribute
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(QuoteItemAttributeInterface $quoteItemAttribute)
    {
        if (!$quoteItemAttribute->getQuoteItemId()) {
            throw new CouldNotSaveException(__('Quote item ID is required'));
        }

        $quoteItemAttributeRecord = $this->loadRecordByQuoteItemId($quoteItemAttribute->getQuoteItemId());
        $quoteItemAttributeRecord->setData(QuoteItemAttributeInterface::QUOTE_ITEM_ID, $quoteItemAttribute->getQuoteItemId());
        $quoteItemAttributeRecord->setData(QuoteItemAttributeInterface::COURSE_UUID, $quoteItemAttribute->getCourseUuid());

        try {
            $this->quoteItemAttributeResource->save($quoteItemAttributeRecord);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save sarus quote item attribute: %1', $e->getMessage()));
        }
    }

    /**
     * @param int $quoteItemId
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteByQuoteItemId($quoteItemId)
    {
        $quoteItemAttributeRecord = $this->loadRecordByQuoteItemId($quoteItemId);
        if (!$quoteItemAttributeRecord->getId()) {
            return;
        }

        try {
            $this->quoteItemAttributeResource->delete($quoteItemAttributeRecord);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete sarus quote item attribute: %1', $e->getMessage()));
        }
    }

    /**
     * @param int $quiteItemId
     * @return \Sarus\Sarus\Model\Record\Quote\Item\Attribute
     */
    private function loadRecordByQuoteItemId($quiteItemId)
    {
        $quoteItemAttributeRecord = $this->quoteItemAttributeRecordFactory->create();
        $this->quoteItemAttributeResource->load(
            $quoteItemAttributeRecord,
            $quiteItemId,
            QuoteItemAttributeInterface::QUOTE_ITEM_ID
        );
        return $quoteItemAttributeRecord;
    }
}

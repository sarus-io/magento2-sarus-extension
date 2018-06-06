<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Controller\Adminhtml\Queue;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

class MassSend extends \Magento\Backend\App\Action
{
    /**
     * @var \Sarus\Sarus\Service\QueueManager
     */
    private $queueManager;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Sarus\Sarus\Service\QueueManager $queueManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Sarus\Sarus\Service\QueueManager $queueManager
    ) {
        $this->queueManager = $queueManager;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('sarus_sarus/queue/index');

        $excluded = $this->getRequest()->getParam('excluded') === 'false' ? false : true;
        $submissionIds = (array)$this->getRequest()->getParam('selected');
        if (empty($submissionIds) && $excluded) {
            $this->messageManager->addWarningMessage(__("You haven't selected any item!"));
            return $resultRedirect;
        }

        try {
            $count = $this->queueManager->sendByIds($submissionIds);
            $this->messageManager->addSuccessMessage(__('%1 submission(s) were sent successfully.', $count));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('An error occurred while sending submissions.'));
        }

        return $resultRedirect;
    }
}

<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Controller\Adminhtml\Queue;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Sarus_Sarus::queue';

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $resultPage->setActiveMenu('Sarus_Sarus::queue');

        $resultPage->addBreadcrumb(__('Sarus'), __('Sarus'));
        $resultPage->addBreadcrumb(__('Sarus Queue'), __('Sarus Queue'));
        $resultPage->getConfig()->getTitle()->prepend(__('Sarus Queue'));

        return $resultPage;
    }
}

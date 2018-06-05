<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var \Sarus\Sarus\Model\Config\General
     */
    private $configGeneral;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Sarus\Sarus\Model\Config\General $configGeneral
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Sarus\Sarus\Model\Config\General $configGeneral
    ) {
        $this->configGeneral = $configGeneral;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page|\Magento\Framework\Controller\Result\Forward
     */
    public function execute()
    {
        if (!$this->configGeneral->isMyCoursesEnabled()) {
            /** @var \Magento\Framework\Controller\Result\Forward $forwardResult */
            $forwardResult = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            $forwardResult->forward('noroute');
            return $forwardResult;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set(__('My Courses'));
        return $resultPage;
    }
}

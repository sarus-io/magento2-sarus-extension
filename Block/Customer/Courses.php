<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Block\Customer;

class Courses extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Sarus\Sarus\Service\Platform
     */
    private $platform;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $catalogImageHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Sarus\Sarus\Service\Platform $platform
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Helper\Image $catalogImageHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Sarus\Sarus\Service\Platform $platform,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Helper\Image $catalogImageHelper,
        array $data = []
    ) {
        $this->platform = $platform;
        $this->customerSession = $customerSession;
        $this->catalogImageHelper = $catalogImageHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getCustomerCourses()
    {
        $sarusResponse = $this->platform->getSdk()->listEnrollments($this->getCustomerEmail());
        return (array)$sarusResponse->get('data') ?: [];
    }

    /**
     * @return string
     */
    private function getCustomerEmail()
    {
        if (!$this->customerSession->isLoggedIn()) {
            throw new \RuntimeException('Courses are not available for not logged in customers.');
        }
        return $this->customerSession->getCustomer()->getEmail();
    }

    /**
     * @param array $courseData
     * @return string
     */
    public function getCourseImageUrl($courseData)
    {
        return !empty($courseData['image_src'])
            ? $courseData['image_src']
            : $this->catalogImageHelper->getDefaultPlaceholderUrl('image');
    }
}

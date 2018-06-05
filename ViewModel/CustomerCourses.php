<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface as BlockArgumentInterface;

class CustomerCourses implements BlockArgumentInterface
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
     * @param \Sarus\Sarus\Service\Platform $platform
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Helper\Image $catalogImageHelper
     */
    public function __construct(
        \Sarus\Sarus\Service\Platform $platform,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Helper\Image $catalogImageHelper
    ) {
        $this->platform = $platform;
        $this->customerSession = $customerSession;
        $this->catalogImageHelper = $catalogImageHelper;
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

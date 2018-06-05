<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Model\Config;

use Magento\Store\Model\ScopeInterface;

class General
{
    const XML_PATH_ENABLED = 'sarus/general/enabled';

    const XML_PATH_MY_COURSES = 'sarus/general/my_courses';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param int|string|null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return bool
     */
    public function isMyCoursesEnabled($storeId = null)
    {
        return $this->isEnabled($storeId)
            && $this->scopeConfig->isSetFlag(self::XML_PATH_MY_COURSES, ScopeInterface::SCOPE_STORE, $storeId);
    }
}

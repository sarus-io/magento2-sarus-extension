<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Model\Config;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Api
{
    const XML_PATH_AUTH_TOKEN = 'sarus/api/auth_token';

    const XML_PATH_DEBUG = 'sarus/api/debug';

    const XML_PATH_LOG_FILENAME = 'sarus/api/log_filename';

    const XML_PATH_MAX_TIME_RESEND = 'sarus/api/max_time_resend';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    private $directoryList;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->directoryList = $directoryList;
    }

    /**
     * @param int|string|null $storeId
     * @return string
     */
    public function getAuthToken($storeId = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AUTH_TOKEN, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return int
     */
    public function getMaxTimeResend($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_MAX_TIME_RESEND, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return bool
     */
    public function isDebug($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_DEBUG, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @return string
     */
    public function getLogFilename()
    {
        $fileName = $this->scopeConfig->getValue(self::XML_PATH_LOG_FILENAME);
        $varDir = $this->directoryList->getPath(DirectoryList::VAR_DIR);
        return $varDir . DIRECTORY_SEPARATOR . ltrim($fileName, DIRECTORY_SEPARATOR);
    }
}

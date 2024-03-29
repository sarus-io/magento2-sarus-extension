<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Model\Config;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Api
{
    const XML_PATH_BASE_URI = 'sarus/api/base_uri';

    const XML_PATH_AUTH_TOKEN = 'sarus/api/auth_token';

    const XML_PATH_LOG_FILENAME = 'sarus/api/log_filename';

    const XML_PATH_MAX_TIME_RESEND = 'sarus/api/max_time_resend';

    const XML_PATH_NOTIFICATION_RECIPIENT = 'sarus/api/notification_recipient';

    const XML_PATH_NOTIFICATION_SENDER = 'sarus/api/notification_sender';

    const XML_PATH_NOTIFICATION_TEMPLATE = 'sarus/api/notification_template';

    const XML_PATH_DEBUG = 'sarus/api/debug';

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
     * @param string|null $storeId
     * @return string
     */
    public function getBaseUri($storeId = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_BASE_URI, ScopeInterface::SCOPE_STORE, $storeId);
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
     * @return string[]
     */
    public function getNotificationRecipients($storeId = null)
    {
        $recipients = $this->scopeConfig->getValue(self::XML_PATH_NOTIFICATION_RECIPIENT, ScopeInterface::SCOPE_STORE, $storeId);
        $recipients = !empty($recipients) ? explode(',', $recipients) : [];
        return array_map('trim', $recipients);
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getNotificationSender($storeId = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_NOTIFICATION_SENDER, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getNotificationTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_NOTIFICATION_TEMPLATE, ScopeInterface::SCOPE_STORE, $storeId);
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

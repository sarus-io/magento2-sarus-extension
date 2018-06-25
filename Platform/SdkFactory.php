<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Platform;

use Sarus\Config as SarusConfig;
use Sarus\SdkFactory as SarusSdkFactory;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;

class SdkFactory
{
    const CONFIG_STORE = 'store';
    const CONFIG_SECRET = 'secret';
    const CONFIG_BASE_URI = 'baseUri';
    const CONFIG_TIMEOUT = 'timeout';
    const CONFIG_SSL_VERIFY = 'sslVerify';

    /**
     * @var \Sarus\Sarus\Model\Config\Api
     */
    private $configApi;

    /**
     * @var string
     */
    private $messageFormat = "{method} - {uri}\nRequest body: {req_body}\n{code} {phrase}\nResponse body: {res_body}\n{error}\n";

    /**
     * @param \Sarus\Sarus\Model\Config\Api $configApi
     */
    public function __construct(\Sarus\Sarus\Model\Config\Api $configApi)
    {
        $this->configApi = $configApi;
    }

    /**
     * $config = [
     *     SdkFactory::CONFIG_STORE,
     *     SdkFactory::CONFIG_SECRET,
     *     SdkFactory::CONFIG_BASE_URI,
     *     SdkFactory::CONFIG_TIMEOUT,
     *     SdkFactory::CONFIG_SSL_VERIFY,
     * ]
     *
     * @param string[] $config
     * @return \Sarus\Sdk
     */
    public function create(array $config = [])
    {
        $store = !empty($config[self::CONFIG_STORE]) ? $config[self::CONFIG_STORE] : null;
        unset($config[self::CONFIG_STORE]);

        $sdkConfig = array_merge(
            [
                self::CONFIG_SECRET   => $this->configApi->getAuthToken($store),
                self::CONFIG_BASE_URI => $this->configApi->getBaseUri($store) ?: SarusConfig::DEFAULT_BASE_URI,
            ],
            $config
        );

        $config = SarusConfig::fromArray($sdkConfig);
        $factory = new SarusSdkFactory();

        return $this->configApi->isDebug($store)
            ? $factory->createWithLogger($config, $this->creteLogger(), $this->messageFormat)
            : $factory->create($config);
    }

    /**
     * @return \Monolog\Logger
     */
    private function creteLogger()
    {
        $logHandler = new RotatingFileHandler($this->configApi->getLogFilename());
        $logHandler->setFilenameFormat('{filename}-{date}', 'Y-m');
        $logHandler->setFormatter(new LineFormatter(null, null, true, true));
        return new Logger('Logger', [$logHandler]);
    }
}

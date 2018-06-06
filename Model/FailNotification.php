<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Model;

use Magento\Framework\App\Area;
use Magento\Store\Model\Store;

class FailNotification
{
    /**
     * @var \Sarus\Sarus\Model\Config\Api
     */
    private $configApi;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    private $inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Sarus\Sarus\Model\Config\Api $configApi
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Sarus\Sarus\Model\Config\Api $configApi,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->configApi = $configApi;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $logger;
    }

    /**
     * @param int $storeId
     * @param string|null $customerEmail
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface|null $response
     * @return void
     */
    public function notify($storeId, $customerEmail, $request, $response = null)
    {
        $recipients = $this->configApi->getNotificationRecipients($storeId);
        foreach ($recipients as $recipient) {
            $this->sendEmail($storeId, $customerEmail, $recipient, $request, $response);
        }
    }

    /**
     * @param int|null $storeId
     * @param string|null $customerEmail
     * @param string $recipient
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface|null $response
     * @return void
     * @throws \Exception
     */
    protected function sendEmail($storeId, $customerEmail, $recipient, $request, $response = null)
    {
        $this->inlineTranslation->suspend();

        try {
            $this->transportBuilder->setTemplateIdentifier($this->configApi->getNotificationTemplate($storeId));
            $this->transportBuilder->setTemplateOptions(['area' => Area::AREA_ADMINHTML, 'store' => Store::DEFAULT_STORE_ID]);
            $this->transportBuilder->setTemplateVars($this->prepareTemplateVars($customerEmail, $request, $response));
            $this->transportBuilder->setFrom($this->configApi->getNotificationSender($storeId));
            $this->transportBuilder->addTo($recipient);

            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }

        $this->inlineTranslation->resume();
    }

    /**
     * @param string|null $customerEmail
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface|null $response
     * @return string[]
     */
    private function prepareTemplateVars($customerEmail, $request, $response = null)
    {
        $vars = [
            'user_email' => $customerEmail,
            'endpoint' => $request->getUri(),
        ];

        if ($response) {
            $vars['response_code'] = (string)$response->getStatusCode();
            $vars['response_message'] = (string)$response->getReasonPhrase();
            $vars['response_body'] = (string)$response->getBody();
        }

        return $vars;
    }
}

<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Block\Checkout;

use Magento\Cms\Block\Block as CmsBlock;

class Success extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Sarus\Sarus\Model\Config\General
     */
    private $configGeneral;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Sarus\Sarus\Helper\Order
     */
    private $orderHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Sarus\Sarus\Model\Config\General $configGeneral
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Sarus\Sarus\Helper\Order $orderHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Sarus\Sarus\Model\Config\General $configGeneral,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Sarus\Sarus\Helper\Order $orderHelper,
        array $data = []
    ) {
        $this->configGeneral = $configGeneral;
        $this->checkoutSession = $checkoutSession;
        $this->orderHelper = $orderHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function hasOrderSarusProduct()
    {
        $order = $this->checkoutSession->getLastRealOrder();
        return !empty($this->orderHelper->getSarusProductUuids($order));
    }

    /**
     * @return bool
     */
    public function isMyCoursesEnabled()
    {
        return $this->configGeneral->isMyCoursesEnabled();
    }

    /**
     * @return string
     */
    public function getSarusProductsUrl()
    {
        return $this->getUrl('sarus_sarus/');
    }

    /**
     * @return string
     */
    public function getSarusStaticBlockHtml()
    {
        return $this->getLayout()->createBlock(CmsBlock::class)->setBlockId('sarus_success_block')->toHtml();
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        return $this->hasOrderSarusProduct() ? parent::_toHtml() : '';
    }
}

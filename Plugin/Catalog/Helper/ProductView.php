<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */

namespace Sarus\Sarus\Plugin\Catalog\Helper;

use Magento\Framework\DataObject;
use Magento\Framework\View\Result\Page as ResultPage;
use Magento\Catalog\Helper\Product\View as CatalogProductView;

class ProductView
{
    /**
     * @var \Sarus\Sarus\Model\Config\General
     */
    private $configGeneral;

    /**
     * @var \Sarus\Sarus\Helper\Product
     */
    private $productHelper;

    /**
     * @param \Sarus\Sarus\Model\Config\General $configGeneral
     * @param \Sarus\Sarus\Helper\Product $productHelper
     */
    public function __construct(
        \Sarus\Sarus\Model\Config\General $configGeneral,
        \Sarus\Sarus\Helper\Product $productHelper
    ) {
        $this->configGeneral = $configGeneral;
        $this->productHelper = $productHelper;
    }

    /**
     * @param \Magento\Catalog\Helper\Product\View $subject
     * @param \Magento\Framework\View\Result\Page $resultPage
     * @param $product
     * @param \Magento\Framework\DataObject|null $params
     * @return array|null
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeInitProductLayout(CatalogProductView $subject, ResultPage $resultPage, $product, $params = null)
    {
        $result = null;
        if ($this->configGeneral->isEnabled() && $this->productHelper->isSarus($product)) {
            $params = $this->addLayoutHandles($params);
            $result = [$resultPage, $product, $params];
        }

        return $result;
    }

    /**
     * @param \Magento\Framework\DataObject|null $params
     * @return \Magento\Framework\DataObject
     */
    private function addLayoutHandles($params = null)
    {
        $params = $params ?: new DataObject();
        $afterHandles = (array)$params->getData('after_handles');
        $afterHandles[] = 'sarus_product';
        $params->setData('after_handles', $afterHandles);
        return $params;
    }
}

<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */
namespace Sarus\Sarus\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as CatalogEavAttribute;
use Magento\Catalog\Model\Product;
use Sarus\Sarus\Helper\Product as SarusProduct;

class InstallData implements InstallDataInterface
{
    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var \Magento\Catalog\Setup\CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    private $attributeSetFactory;

    /**
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     * @param \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory
     */
    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory,
        \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->createSarusAttributeSet($setup);
        $this->crateSarusProductAttribute($setup);
    }

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @return void
     */
    private function createSarusAttributeSet(ModuleDataSetupInterface $setup)
    {
        /** @var \Magento\Catalog\Setup\CategorySetup $categorySetup */
        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

        $entityTypeId = $categorySetup->getEntityTypeId(Product::ENTITY);
        $attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);

        $attributeSet = $this->attributeSetFactory->create();
        $attributeSet->setAttributeSetName(SarusProduct::ATTRIBUTE_SET_NAME);
        $attributeSet->setEntityTypeId($entityTypeId);
        $attributeSet->setSortOrder(200);

        $attributeSet->validate();
        $attributeSet->save();

        $attributeSet->initFromSkeleton($attributeSetId);
        $attributeSet->save();
    }

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @return void
     */
    private function crateSarusProductAttribute($setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            Product::ENTITY,
            SarusProduct::ATTRIBUTE_COURSE_UUID,
            [
                'label' => 'Sarus Course UUID',
                'global' => CatalogEavAttribute::SCOPE_GLOBAL,
                'type' => 'varchar',
                'input' => 'text',
                'user_defined' => true,
                'required' => true,
                'visible' => true,
                'sort_order' => 5,
            ]
        );

        $eavSetup->addAttributeToSet(
            Product::ENTITY,
            SarusProduct::ATTRIBUTE_SET_NAME,
            'Product Details',
            SarusProduct::ATTRIBUTE_COURSE_UUID,
            25
        );
    }
}

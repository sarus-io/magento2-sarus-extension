<?php
/**
 * Copyright © Sarus, LLC. All rights reserved.
 */
namespace Sarus\Sarus\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

use Magento\Catalog\Model\Product;
use Sarus\Sarus\Model\Product\Type as SarusType;

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
        $this->addProductType($setup);
        $this->createSarusAttributeSet($setup);
        $this->crateSarusProductAttribute($setup);
    }

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @return void
     */
    private function addProductType($setup)
    {
        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

        $attributes = [
            'price',
            'special_price',
            'special_from_date',
            'special_to_date',
            'minimal_price',
            'tax_class_id'
        ];
        foreach ($attributes as $attributeCode) {
            $applyTo = explode(',', $categorySetup->getAttribute(Product::ENTITY, $attributeCode, 'apply_to'));

            if (!in_array(SarusType::TYPE_CODE, $applyTo, true)) {
                $applyTo[] = SarusType::TYPE_CODE;
                $categorySetup->updateAttribute(Product::ENTITY, $attributeCode, 'apply_to', implode(',', $applyTo));
            }
        }
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
        $attributeSet->setAttributeSetName(SarusType::ATTRIBUTE_SET_NAME);
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
            SarusType::ATTRIBUTE_COURSE_UUID,
            [
                'label' => 'Sarus Course UUID',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
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
            SarusType::ATTRIBUTE_SET_NAME,
            'Product Details',
            SarusType::ATTRIBUTE_COURSE_UUID,
            25
        );
    }
}

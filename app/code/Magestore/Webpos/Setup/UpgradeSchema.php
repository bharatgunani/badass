<?php
/**
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

namespace Magestore\Webpos\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Setup\EavSetup;

/**
 * Upgrade the Catalog module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    protected $moduleDataSetup;
    
    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;
    
    /**
     * UpgradeSchema constructor
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }
    
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            //add tax_class_id for sales_order_table table
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_item'),
                'custom_tax_class_id',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable'  => true,
                    'length'    => '11',
                    'comment'   => 'Custom Tax Class Id'
                )
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('catalog_product_entity'),
                'updated_datetime',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_UPDATE],
                'Updated Time'
            );
            /** @var EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
            /**
             * Remove attribute webpos_visible
             */
            //Find these in the eav_entity_type table
            $action = \Magento\Framework\App\ObjectManager::getInstance()->get(
                '\Magento\Catalog\Model\ResourceModel\Product\Action'
            );
            $attribute = $action->getAttribute('webpos_visible');
            if($attribute){
                $entityTypeId = \Magento\Framework\App\ObjectManager::getInstance()
                ->create('Magento\Eav\Model\Config')
                ->getEntityType(\Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE)
                ->getEntityTypeId();
                $eavSetup->removeAttribute($entityTypeId, 'webpos_visible');
            }
            
            $eavSetup->removeAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'webpos_visible'
            );
            
            /**
            * Add attributes to the eav/attribute
            */
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'webpos_visible',
                [
                    'group' => 'General',
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Enable On Webpos',
                    'input' => 'boolean',
                    'class' => '',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => '1',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'unique' => false,
                    'apply_to' => ''
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.1.1', '<')) {
            //add customer full name for sales_order table
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order'),
                'customer_fullname',
                array(
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable'  => true,
                    'length'    => '255',
                    'comment'   => 'Customer Full Name'
                )
            );
        }
        $setup->endSetup();
    }
}

<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(ProductMetadataInterface $productMetadata)
    {
        $this->productMetadata = $productMetadata;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '2.0.1', '<') && $this->productMetadata->getEdition() == 'Enterprise') {
            $installer->getConnection()->dropForeignKey(
                $installer->getTable('mageworx_seoxtemplates_template_relation_product'),
                $installer->getFkName(
                    'mageworx_seoxtemplates_template_relation_product',
                    'product_id',
                    'catalog_product_entity',
                    'entity_id'
                )
            );
            $installer->getConnection()->addForeignKey(
                $installer->getFkName(
                    'mageworx_seoxtemplates_template_relation_product',
                    'product_id',
                    'sequence_product',
                    'sequence_value'
                ),
                $installer->getTable('mageworx_seoxtemplates_template_relation_product'),
                'product_id',
                $installer->getTable('sequence_product'),
                'sequence_value',
                Table::ACTION_CASCADE
            );

            $installer->getConnection()->dropForeignKey(
                $installer->getTable('mageworx_seoxtemplates_template_relation_category'),
                $installer->getFkName(
                    'mageworx_seoxtemplates_template_relation_category',
                    'category_id',
                    'catalog_category_entity',
                    'entity_id'
                )
            );
            $installer->getConnection()->addForeignKey(
                $installer->getFkName(
                    'mageworx_seoxtemplates_template_relation_category',
                    'category_id',
                    'sequence_catalog_category',
                    'sequence_value'
                ),
                $installer->getTable('mageworx_seoxtemplates_template_relation_category'),
                'category_id',
                $installer->getTable('sequence_catalog_category'),
                'sequence_value',
                Table::ACTION_CASCADE
            );
        }
        
        $installer->endSetup();
    }
}

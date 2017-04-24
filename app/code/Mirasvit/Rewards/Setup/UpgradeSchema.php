<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rewards
 * @version   1.1.25
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rewards\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws \Zend_Db_Exception
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.0.1') < 0) {

            $table = $installer->getConnection()->newTable(
                $installer->getTable('mst_rewards_earning_rule_queue')
            )
                ->addColumn(
                    'queue_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
                    'Queue Id')
                ->addColumn(
                    'customer_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => false, 'nullable' => false],
                    'Customer Id')
                ->addColumn(
                    'website_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => false, 'nullable' => false],
                    'Website Id')
                ->addColumn(
                    'rule_type',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    ['unsigned' => false, 'nullable' => false],
                    'Rule Type')
                ->addColumn(
                    'rule_code',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    512,
                    ['unsigned' => false, 'nullable' => false],
                    'Rule Code')
                ->addColumn(
                    'is_processed',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => false, 'nullable' => false, 'default' => 0],
                    'Website Id')
                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['unsigned' => false, 'nullable' => true],
                    'Created At')
                ->addColumn(
                    'updated_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['unsigned' => false, 'nullable' => true],
                    'Updated At');
            $installer->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('mst_rewards_customer_referral_link')
            )
                ->addColumn(
                    'customer_referral_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
                    'Customer Referral Id')
                ->addColumn(
                    'customer_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Customer Id')
                ->addColumn(
                    'referral_link',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    50,
                    ['unsigned' => false, 'nullable' => false],
                    'Referral Link')
                ->addIndex(
                    $installer->getIdxName('mst_rewards_customer_referral_link', ['customer_id']),
                    ['customer_id']
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'mst_rewards_customer_referral_link',
                        'customer_id',
                        'customer_entity',
                        'entity_id'
                    ),
                    'customer_id',
                    $installer->getTable('customer_entity'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );
            $installer->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.0.3') < 0) {
            $installer->getConnection()->addColumn(
                $installer->getTable('mst_rewards_earning_rule'),
                'product_notification',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 1024,
                    'unsigned' => false,
                    'nullable' => false,
                    'comment'  => 'Product Notification'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.4') < 0) {
            include_once 'Upgrade_1_0_4.php';

            Upgrade_1_0_4::upgrade($installer, $context);
        }

        $installer->endSetup();
    }
}

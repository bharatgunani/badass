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
 * @version   1.1.22
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rewards\Controller\Adminhtml\SpendingRule;

/**
 * @magentoAppArea adminhtml
 */
class SaveTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Rewards::reward_points_spending_rule';
        $this->uri = 'backend/rewards/spending_rule/save';
        parent::setUp();
    }

    /**
     * @covers  Mirasvit\Rewards\Controller\Adminhtml\Spending\Rule\Save::execute
     *
     * @magentoDataFixture Mirasvit/Rewards/_files/spending_rule.php
     */
    public function testSaveAction()
    {
        $name = 'Test Spending Rule 3';

        $this->getRequest()->setMethod(
            'POST'
        )->setPostValue(
            [
                'form_key' => $this->_objectManager->get('Magento\Framework\Data\Form\FormKey')->getFormKey(),
                'name' => $name,
                'type' => 'product',
                'is_active' => 1,
                'customer_group_ids' => [1],
                'website_ids' => [1],
                'spend_points' => 5,
                'monetary_step' => 1,
                'conditions' => [
                    '1' => [
                        'type' => '\Mirasvit\Rewards\Model\Spending\Rule\Condition\Combine',
                        'aggregator' => 'all',
                        'value' => 1,
                        'new_child' => '',
                    ],
                    '1--1' => [
                        'type' => 'Magento\SalesRule\Model\Rule\Condition\Product',
                        'attribute' => 'base_subtotal',
                        'operator' => '>',
                        'value' => 1,
                    ],
                ],
                'actions' => [
                    '1' => [
                        'type' => 'Magento\SalesRule\Model\Rule\Condition\Product\Combine',
                        'aggregator' => 'all',
                        'value' => 1,
                        'new_child' => '',
                    ],
                    '1--1' => [
                        'type' => 'Magento\SalesRule\Model\Rule\Condition\Product',
                        'attribute' => 'quote_item_qty',
                        'operator' => '>',
                        'value' => 1,
                    ],
                ],
            ]
        );
        $this->dispatch('backend/rewards/spending_rule/save');

        $this->assertRedirect($this->stringContains('backend/rewards/spending_rule'));
        $this->assertSessionMessages(
            $this->equalTo(['Spending Rule was successfully saved']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );

        $rule = $this->_objectManager->create('Mirasvit\Rewards\Model\Spending\Rule')->load($name, 'name');

        $this->assertNotEquals(0, $rule->getId());
        $this->assertEquals(5, $rule->getSpendPoints());

        $expected = [
            'type' => '\Mirasvit\Rewards\Model\Spending\Rule\Condition\Combine',
            'attribute' => '',
            'operator' => '',
            'value' => 1,
            'is_value_processed' => '',
            'aggregator' => 'all',
        ];

        $this->assertEquals($expected, $rule->getConditions()->asArray());

        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertTrue($this->getResponse()->isRedirect());
    }
}

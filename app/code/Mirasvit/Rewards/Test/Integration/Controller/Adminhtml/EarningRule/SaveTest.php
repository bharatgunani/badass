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



namespace Mirasvit\Rewards\Controller\Adminhtml\EarningRule;

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
        $this->resource = 'Mirasvit_Rewards::reward_points_earning_rule';
        $this->uri = 'backend/rewards/earning_rule/save';
        parent::setUp();
    }

    /**
     * @covers  Mirasvit\Rewards\Controller\Adminhtml\Earning\Rule\Save::execute
     *
     * @magentoDataFixture Mirasvit/Rewards/_files/earning_rule.php
     */
    public function testSaveAction()
    {
        $name = 'Test Earning Rule 3';

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
                'behavior_trigger' => 'pinterest_pin',
                'conditions' => [
                    '1' => [
                        'type' => '\Mirasvit\Rewards\Model\Earning\Rule\Condition\Combine',
                        'aggregator' => 'all',
                        'value' => 1,
                        'new_child' => '',
                    ],
                    '1--1' => [
                        'type' => '\Mirasvit\Rewards\Model\Earning\Rule\Condition\Customer',
                        'attribute' => 'orders_number',
                        'operator' => '==',
                        'value' => 1,
                    ],
                ],
            ]
        );
        $this->dispatch('backend/rewards/earning_rule/save');

        $this->assertRedirect($this->stringContains('backend/rewards/earning_rule'));
        $this->assertSessionMessages(
            $this->equalTo(['Earning Rule was successfully saved']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );

        $rule = $this->_objectManager->create('Mirasvit\Rewards\Model\Earning\Rule')->load($name, 'name');

        $this->assertNotEquals(0, $rule->getId());
        $this->assertEquals('pinterest_pin', $rule->getBehaviorTrigger());

        $expected = [
            'type' => '\Mirasvit\Rewards\Model\Earning\Rule\Condition\Combine',
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

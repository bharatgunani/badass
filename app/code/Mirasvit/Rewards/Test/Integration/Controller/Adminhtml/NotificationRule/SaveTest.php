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



namespace Mirasvit\Rewards\Controller\Adminhtml\NotificationRule;

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
        $this->resource = 'Mirasvit_Rewards::reward_points_notification_rule';
        $this->uri = 'backend/rewards/notification_rule/save';
        parent::setUp();
    }

    /**
     * @covers  Mirasvit\Rewards\Controller\Adminhtml\Notification\Rule\Save::execute
     *
     * @magentoDataFixture Mirasvit/Rewards/_files/notification_rule.php
     */
    public function testSaveAction()
    {
        $name = 'Test Notification Rule 3';

        $this->getRequest()->setMethod(
            'POST'
        )->setPostValue(
            [
                'form_key' => $this->_objectManager->get('Magento\Framework\Data\Form\FormKey')->getFormKey(),
                'name' => $name,
                'is_active' => 1,
                'customer_group_ids' => [1],
                'website_ids' => [1],
                'conditions' => [
                    '1' => [
                        'type' => '\Mirasvit\Rewards\Model\Notification\Rule\Condition\Combine',
                        'aggregator' => 'all',
                        'value' => 1,
                        'new_child' => '',
                    ],
                    '1--1' => [
                        'type' => '\Magento\SalesRule\Model\Rule\Condition\Address',
                        'attribute' => 'weight',
                        'operator' => '<',
                        'value' => 500,
                    ],
                ],
            ]
        );
        $this->dispatch('backend/rewards/notification_rule/save');

        $this->assertRedirect($this->stringContains('backend/rewards/notification_rule'));
        $this->assertSessionMessages(
            $this->equalTo(['Notification Rule was successfully saved']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );

        $rule = $this->_objectManager->create('Mirasvit\Rewards\Model\Notification\Rule')->load($name, 'name');

        $this->assertNotEquals(0, $rule->getId());

        $expected = [
            'type' => '\Mirasvit\Rewards\Model\Notification\Rule\Condition\Combine',
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

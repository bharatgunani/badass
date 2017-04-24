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


/**
 * Admin Spending Rule Mass Change Test.
 */
namespace Mirasvit\Rewards\Controller\Adminhtml\SpendingRule;

/**
 * @magentoDataFixture Mirasvit/Rewards/_files/spending_rule.php
 *
 * @magentoAppArea adminhtml
 */
class MassChangeTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Rewards::reward_points_spending_rule';
        $this->uri = 'backend/rewards/spending_rule/masschange';
        parent::setUp();
    }

    /**
     * @covers  Mirasvit\Rewards\Controller\Adminhtml\Spending\Rule\MassDelete::execute
     */
    public function testMassChangeAction()
    {
        $this->getRequest()->setParam('spending_rule_id', [1, 2]);
        $this->getRequest()->setParam('is_active', 0);
        $this->dispatch('backend/rewards/spending_rule/masschange');
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertTrue($this->getResponse()->isRedirect());

        $this->assertSessionMessages(
            $this->equalTo(['Total of 2 record(s) were successfully updated']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect($this->stringContains('backend/rewards/spending_rule/index/'));

        /** @var \Mirasvit\Rewards\Model\Spending\Rule $rule */
        $rule = $this->_objectManager->create('Mirasvit\Rewards\Model\Spending\Rule')->load(1);
        $this->assertEquals(0, $rule->getIsActive());
        $rule = $this->_objectManager->create('Mirasvit\Rewards\Model\Spending\Rule')->load(2);
        $this->assertEquals(0, $rule->getIsActive());
    }
}

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
 * Admin Rewards Spending Rule Delete Test.
 */
namespace Mirasvit\Rewards\Controller\Adminhtml\SpendingRule;

/**
 * Class DeleteTest.
 *
 * @magentoDataFixture Mirasvit/Rewards/_files/spending_rule.php
 */
/**
 * @magentoAppArea adminhtml
 */
class DeleteTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Rewards::reward_points_spending_rule';
        $this->uri = 'backend/rewards/spending_rule/delete';
        parent::setUp();
    }

    /**
     * @covers  Mirasvit\Rewards\Controller\Adminhtml\Spending\Rule\Delete::execute
     */
    public function testDeleteAction()
    {
        $this->getRequest()->setParam('id', 1);
        $this->dispatch('backend/rewards/spending_rule/delete');
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertTrue($this->getResponse()->isRedirect());
        $this->assertSessionMessages(
            $this->equalTo(['Spending Rule was successfully deleted']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect($this->stringContains('backend/rewards/spending_rule/index/'));

        /** @var \Mirasvit\Rewards\Model\Spending\Rule $rule */
        $rule = $this->_objectManager->create('Mirasvit\Rewards\Model\Spending\Rule')->load(1);
        $this->assertEquals(0, $rule->getId());
    }
}

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
 * Admin Referral Mass Delete Test.
 */
namespace Mirasvit\Rewards\Controller\Adminhtml\Referral;

/**
 * @magentoDataFixture Mirasvit/Rewards/_files/referrals.php
 *
 * @magentoAppArea adminhtml
 */
class MassDeleteTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Rewards::reward_points_referral';
        $this->uri = 'backend/rewards/referral/massdelete';
        parent::setUp();
    }

    /**
     * @covers  Mirasvit\Rewards\Controller\Adminhtml\Spending\Rule\MassDelete::execute
     */
    public function testMassDeleteAction()
    {
        $this->getRequest()->setParam('referral_id', [1, 2]);
        $this->dispatch('backend/rewards/referral/massdelete');
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertTrue($this->getResponse()->isRedirect());

        $this->assertSessionMessages(
            $this->equalTo(['Total of 2 record(s) were successfully deleted']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect($this->stringContains('backend/rewards/referral/index/'));

        /** @var \Mirasvit\Rewards\Model\Spending\Rule $rule */
        $rule = $this->_objectManager->create('Mirasvit\Rewards\Model\Referral')->load(1);
        $this->assertEquals(0, $rule->getId());
        $rule = $this->_objectManager->create('Mirasvit\Rewards\Model\Referral')->load(2);
        $this->assertEquals(0, $rule->getId());
    }
}

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
 * @magentoDataFixture Mirasvit/Rewards/_files/earning_rule.php
 *
 * @magentoAppArea adminhtml
 */
class EditTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Rewards::reward_points_earning_rule';
        $this->uri = 'backend/rewards/earning_rule/edit';
        parent::setUp();
    }

    /**
     * @covers  Mirasvit\Rewards\Controller\Adminhtml\Earning\Rule\Edit::execute
     */
    public function testEditAction()
    {
        $rule = $this->_objectManager->create('Mirasvit\Rewards\Model\Earning\Rule')->load('Test Earning Rule', 'name');

        $this->getRequest()->setParam('id', $rule->getId());
        $this->dispatch('backend/rewards/earning_rule/edit');
        $body = $this->getResponse()->getBody();
        $this->assertNotEmpty($body);
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertFalse($this->getResponse()->isRedirect());
        $this->assertContains('Test Earning Rule', $body);
    }
}

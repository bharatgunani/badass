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
 * Admin Earning Rule Mass Change Test.
 */
namespace Mirasvit\Rewards\Controller\Adminhtml\EarningRule;

/**
 * @magentoAppArea adminhtml
 */
class NewConditionHtmlTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Rewards::reward_points_earning_rule';
        $this->uri = 'backend/rewards/earning_rule/newconditionhtml';
        parent::setUp();
    }

    /**
     * @covers  Mirasvit\Rewards\Controller\Adminhtml\Earning\Rule\NewConditionHtml::execute
     */
    public function testNewConditionHtmlAction()
    {
        $params = [
            'type' => '\Mirasvit\Rewards\Model\Earning\Rule\Condition\Customer|group_id',
            'id' => '1--1',
        ];

        $this->getRequest()->setParams($params);

        $this->dispatch('backend/rewards/earning_rule/newconditionhtml');
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());

        $response = '<input id="conditions__1--1__type" name="rule[conditions][1--1][type]"  '.
            'data-ui-id="form-element-rule[conditions][1--1][type]" '.
            'value="\Mirasvit\Rewards\Model\Earning\Rule\Condition\Customer" class="hidden" data-form-part=""'.
            ' type="hidden"/>';

        $body = $this->getResponse()->getBody();
        $this->assertNotEmpty($body);
        $this->assertContains($response, $body);
    }
}

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



namespace Mirasvit\Rewards\Test\Unit\Model\Notification\Rule\Condition;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Rewards\Model\Notification\Rule\Condition\Combine
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CombineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Model\Notification\Rule\Condition\Combine|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $combineModel;

    /**
     * @var \Mirasvit\Rewards\Model\Notification\Rule\Condition\ProductFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $notificationRuleConditionProductFactoryMock;

    /**
     * @var \Mirasvit\Rewards\Model\Notification\Rule\Condition\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $notificationRuleConditionProductMock;

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\AddressFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleConditionAddressFactoryMock;

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\Address|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleConditionAddressMock;

    /**
     * @var \Mirasvit\Rewards\Model\Notification\Rule\Condition\CustomFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $notificationRuleConditionCustomFactoryMock;

    /**
     * @var \Mirasvit\Rewards\Model\Notification\Rule\Condition\Custom|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $notificationRuleConditionCustomMock;

    /**
     * @var \Mirasvit\Rewards\Helper\Rule|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardsRuleMock;

    /**
     * @var \Magento\Framework\Model\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \Magento\Framework\Model\ResourceModel\AbstractResource|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;

    /**
     * @var \Magento\Framework\Data\Collection\AbstractDb|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceCollectionMock;

    public function setUp()
    {
        $this->notificationRuleConditionProductFactoryMock = $this->getMock(
            '\Mirasvit\Rewards\Model\Notification\Rule\Condition\ProductFactory', ['create'], [], '', false
        );
        $this->notificationRuleConditionProductMock = $this->getMock(
            '\Mirasvit\Rewards\Model\Notification\Rule\Condition\Product', ['load', 'save', 'delete'], [], '', false
        );
        $this->notificationRuleConditionProductFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->notificationRuleConditionProductMock));
        $this->ruleConditionAddressFactoryMock = $this->getMock(
            '\Magento\SalesRule\Model\Rule\Condition\AddressFactory', ['create'], [], '', false
        );
        $this->ruleConditionAddressMock = $this->getMock(
            '\Magento\SalesRule\Model\Rule\Condition\Address', ['load', 'save', 'delete'], [], '', false
        );
        $this->ruleConditionAddressFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->ruleConditionAddressMock));
        $this->notificationRuleConditionCustomFactoryMock = $this->getMock(
            '\Mirasvit\Rewards\Model\Notification\Rule\Condition\CustomFactory', ['create'], [], '', false
        );
        $this->notificationRuleConditionCustomMock = $this->getMock(
            '\Mirasvit\Rewards\Model\Notification\Rule\Condition\Custom', ['load', 'save', 'delete'], [], '', false
        );
        $this->notificationRuleConditionCustomFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->notificationRuleConditionCustomMock));
        $this->rewardsRuleMock = $this->getMock('\Mirasvit\Rewards\Helper\Rule', [], [], '', false);
        $this->registryMock = $this->getMock('\Magento\Framework\Registry', [], [], '', false);
        $this->resourceMock = $this->getMock(
            '\Mirasvit\Rewards\Model\ResourceModel\Notification\Rule\Condition\Combine', [], [], '', false
        );
        $this->resourceCollectionMock = $this->getMock(
            '\Mirasvit\Rewards\Model\ResourceModel\Notification\Rule\Condition\Combine\Collection', [], [], '', false
        );
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            'Magento\Framework\App\Action\Context',
            [
            ]
        );
        $this->combineModel = $this->objectManager->getObject(
            '\Mirasvit\Rewards\Model\Notification\Rule\Condition\Combine',
            [
                'notificationRuleConditionProductFactory' => $this->notificationRuleConditionProductFactoryMock,
                'ruleConditionAddressFactory' => $this->ruleConditionAddressFactoryMock,
                'notificationRuleConditionCustomFactory' => $this->notificationRuleConditionCustomFactoryMock,
                'rewardsRule' => $this->rewardsRuleMock,
                'context' => $this->contextMock,
                'registry' => $this->registryMock,
                'resource' => $this->resourceMock,
                'resourceCollection' => $this->resourceCollectionMock,
            ]
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->combineModel, $this->combineModel);
    }
}

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



namespace Mirasvit\Rewards\Test\Unit\Model\Earning\Rule\Condition;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Rewards\Model\Earning\Rule\Condition\Combine
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CombineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Model\Earning\Rule\Condition\Combine|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $combineModel;

    /**
     * @var \Mirasvit\Rewards\Model\Earning\Rule\Condition\ProductFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $earningRuleConditionProductFactoryMock;

    /**
     * @var \Mirasvit\Rewards\Model\Earning\Rule\Condition\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $earningRuleConditionProductMock;

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\AddressFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleConditionAddressFactoryMock;

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\Address|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleConditionAddressMock;

    /**
     * @var \Mirasvit\Rewards\Model\Earning\Rule\Condition\CustomerFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $earningRuleConditionCustomerFactoryMock;

    /**
     * @var \Mirasvit\Rewards\Model\Earning\Rule\Condition\Customer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $earningRuleConditionCustomerMock;

    /**
     * @var \Mirasvit\Rewards\Model\Earning\Rule\Condition\Referred\CustomerFactory|
     * \PHPUnit_Framework_MockObject_MockObject
     */
    protected $earningRuleConditionReferredCustomerFactoryMock;

    /**
     * @var \Mirasvit\Rewards\Model\Earning\Rule\Condition\Referred\Customer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $earningRuleConditionReferredCustomerMock;

    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

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
        $this->earningRuleConditionProductFactoryMock = $this->getMock(
            '\Mirasvit\Rewards\Model\Earning\Rule\Condition\ProductFactory', ['create'], [], '', false
        );
        $this->earningRuleConditionProductMock = $this->getMock(
            '\Mirasvit\Rewards\Model\Earning\Rule\Condition\Product', ['load', 'save', 'delete'], [], '', false
        );
        $this->earningRuleConditionProductFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->earningRuleConditionProductMock));
        $this->ruleConditionAddressFactoryMock = $this->getMock(
            '\Magento\SalesRule\Model\Rule\Condition\AddressFactory', ['create'], [], '', false
        );
        $this->ruleConditionAddressMock = $this->getMock(
            '\Magento\SalesRule\Model\Rule\Condition\Address', ['load', 'save', 'delete'], [], '', false
        );
        $this->ruleConditionAddressFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->ruleConditionAddressMock));
        $this->earningRuleConditionCustomerFactoryMock = $this->getMock(
            '\Mirasvit\Rewards\Model\Earning\Rule\Condition\CustomerFactory', ['create'], [], '', false
        );
        $this->earningRuleConditionCustomerMock = $this->getMock(
            '\Mirasvit\Rewards\Model\Earning\Rule\Condition\Customer', ['load', 'save', 'delete'], [], '', false
        );
        $this->earningRuleConditionCustomerFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->earningRuleConditionCustomerMock));
        $this->earningRuleConditionReferredCustomerFactoryMock = $this->getMock(
            '\Mirasvit\Rewards\Model\Earning\Rule\Condition\Referred\CustomerFactory', ['create'], [], '', false
        );
        $this->earningRuleConditionReferredCustomerMock = $this->getMock(
            '\Mirasvit\Rewards\Model\Earning\Rule\Condition\Referred\Customer',
            ['load', 'save', 'delete'],
            [], '', false
        );
        $this->earningRuleConditionReferredCustomerFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->earningRuleConditionReferredCustomerMock));
        $this->requestMock = $this->getMock('\Magento\Framework\App\Request\Http', [], [], '', false);
        $this->registryMock = $this->getMock('\Magento\Framework\Registry', [], [], '', false);
        $this->resourceMock = $this->getMock(
            '\Mirasvit\Rewards\Model\ResourceModel\Earning\Rule\Condition\Combine', [], [], '', false
        );
        $this->resourceCollectionMock = $this->getMock(
            '\Mirasvit\Rewards\Model\ResourceModel\Earning\Rule\Condition\Combine\Collection', [], [], '', false
        );
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Rule\Model\Condition\Context',
            [
            ]
        );
        $this->combineModel = $this->objectManager->getObject(
            '\Mirasvit\Rewards\Model\Earning\Rule\Condition\Combine',
            [
                'earningRuleConditionProductFactory' => $this->earningRuleConditionProductFactoryMock,
                'earningRuleConditionReferredCustomerFactory' => $this->earningRuleConditionReferredCustomerFactoryMock,
                'context' => $this->contextMock,
                'data' => []
            ]
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->combineModel, $this->combineModel);
    }
}

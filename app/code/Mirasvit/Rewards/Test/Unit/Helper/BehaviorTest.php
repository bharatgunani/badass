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



namespace Mirasvit\Rewards\Test\Unit\Helper;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Rewards\Helper\Behavior
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class BehaviorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Helper\Behavior|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $behaviorHelper;

    /**
     * @var \Magento\Customer\Model\CustomerFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerFactoryMock;

    /**
     * @var \Magento\Customer\Model\Customer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerMock;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Transaction\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $transactionCollectionFactoryMock;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Transaction\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $transactionCollectionMock;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Earning\Rule\CollectionFactory|
     * \PHPUnit_Framework_MockObject_MockObject
     */
    protected $earningRuleCollectionFactoryMock;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Earning\Rule\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $earningRuleCollectionMock;

    /**
     * @var \Magento\Framework\App\ResourceConnection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;

    /**
     * @var \Mirasvit\Rewards\Helper\Balance|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardsBalanceMock;

    /**
     * @var \Mirasvit\Rewards\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardsDataMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dateMock;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManagerMock;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    public function setUp()
    {
        $this->customerFactoryMock = $this->getMock(
            '\Magento\Customer\Model\CustomerFactory', ['create'], [], '', false
        );
        $this->customerMock = $this->getMock(
            '\Magento\Customer\Model\Customer', ['load', 'save', 'delete'], [], '', false
        );
        $this->customerFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->customerMock));
        $this->transactionCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Rewards\Model\ResourceModel\Transaction\CollectionFactory', ['create'], [], '', false
        );
        $this->transactionCollectionMock = $this->getMock(
            '\Mirasvit\Rewards\Model\ResourceModel\Transaction\Collection',
            ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
            [], '', false
        );
        $this->transactionCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->transactionCollectionMock));
        $this->earningRuleCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Rewards\Model\ResourceModel\Earning\Rule\CollectionFactory', ['create'], [], '', false
        );
        $this->earningRuleCollectionMock = $this->getMock(
            '\Mirasvit\Rewards\Model\ResourceModel\Earning\Rule\Collection',
            ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
            [], '', false
        );
        $this->earningRuleCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->earningRuleCollectionMock));
        $this->resourceMock = $this->getMock('\Magento\Framework\App\ResourceConnection', [], [], '', false);
        $this->rewardsBalanceMock = $this->getMock('\Mirasvit\Rewards\Helper\Balance', [], [], '', false);
        $this->rewardsDataMock = $this->getMock('\Mirasvit\Rewards\Helper\Data', [], [], '', false);
        $this->dateMock = $this->getMock('\Magento\Framework\Stdlib\DateTime\DateTime', [], [], '', false);
        $this->storeManagerMock = $this->getMockForAbstractClass(
            '\Magento\Store\Model\StoreManagerInterface', [], '', false, true, true, []
        );
        $this->messageManagerMock = $this->getMockForAbstractClass(
            '\Magento\Framework\Message\ManagerInterface', [], '', false, true, true, []
        );
        $this->customerSessionMock = $this->getMock('\Magento\Customer\Model\Session', [], [], '', false);
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\App\Helper\Context',
            [
            ]
        );
        $this->behaviorHelper = $this->objectManager->getObject(
            '\Mirasvit\Rewards\Helper\Behavior',
            [
                'customerFactory' => $this->customerFactoryMock,
                'transactionCollectionFactory' => $this->transactionCollectionFactoryMock,
                'earningRuleCollectionFactory' => $this->earningRuleCollectionFactoryMock,
                'resource' => $this->resourceMock,
                'rewardsBalance' => $this->rewardsBalanceMock,
                'rewardsData' => $this->rewardsDataMock,
                'context' => $this->contextMock,
                'date' => $this->dateMock,
                'storeManager' => $this->storeManagerMock,
                'messageManager' => $this->messageManagerMock,
                'customerSession' => $this->customerSessionMock,
            ]
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->behaviorHelper, $this->behaviorHelper);
    }
}

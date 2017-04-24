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
use Mirasvit\Rewards\Model\Config as Config;

/**
 * @covers \Mirasvit\Rewards\Model\Earning\Rule\Condition\Customer
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Model\Earning\Rule\Condition\Customer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerModel;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subscriberFactoryMock;

    /**
     * @var \Magento\Newsletter\Model\Subscriber|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subscriberMock;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Sale\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $saleCollectionFactoryMock;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Sale\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $saleCollectionMock;

    /**
     * @var \Magento\Review\Model\ResourceModel\Review\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $reviewCollectionFactoryMock;

    /**
     * @var \Magento\Review\Model\ResourceModel\Review\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $reviewCollectionMock;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Referral\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $referralCollectionFactoryMock;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Referral\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $referralCollectionMock;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderCollectionFactoryMock;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderCollectionMock;

    /**
     * @var \Mirasvit\Rewards\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\Customer\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerDataMock;

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
        $this->subscriberFactoryMock = $this->getMock(
            '\Magento\Newsletter\Model\SubscriberFactory', ['create'], [], '', false
        );
        $this->subscriberMock = $this->getMock(
            '\Mirasvit\Newsletter\Model\Subscriber', ['load', 'save', 'delete'], [], '', false
        );
        $this->subscriberFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->subscriberMock));
        $this->saleCollectionFactoryMock = $this->getMock(
            '\Magento\Sales\Model\ResourceModel\Sale\CollectionFactory', ['create'], [], '', false
        );
        $this->saleCollectionMock = $this->getMock(
            '\Magento\Sales\Model\ResourceModel\Sale\Collection',
            ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
            [], '', false
        );
        $this->saleCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->saleCollectionMock));
        $this->reviewCollectionFactoryMock = $this->getMock(
            '\Magento\Review\Model\ResourceModel\Review\CollectionFactory', ['create'], [], '', false
        );
        $this->reviewCollectionMock = $this->getMock(
            '\Magento\Review\Model\ResourceModel\Review\Collection',
            ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
            [], '', false
        );
        $this->reviewCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->reviewCollectionMock));
        $this->referralCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Rewards\Model\ResourceModel\Referral\CollectionFactory', ['create'], [], '', false
        );
        $this->referralCollectionMock = $this->getMock(
            '\Mirasvit\Rewards\Model\ResourceModel\Referral\Collection',
            ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
            [], '', false
        );
        $this->referralCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->referralCollectionMock));
        $this->orderCollectionFactoryMock = $this->getMock(
            '\Magento\Sales\Model\ResourceModel\Order\CollectionFactory', ['create'], [], '', false
        );
        $this->orderCollectionMock = $this->getMock(
            '\Magento\Sales\Model\ResourceModel\Order\Collection',
            ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
            [], '', false
        );
        $this->orderCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->orderCollectionMock));
        $this->configMock = $this->getMock('\Mirasvit\Rewards\Model\Config', [], [], '', false);
        $this->customerDataMock = $this->getMock('\Magento\Customer\Helper\Data', [], [], '', false);
        $this->registryMock = $this->getMock('\Magento\Framework\Registry', [], [], '', false);
        $this->resourceMock = $this->getMock(
            '\Magento\Framework\Model\ResourceModel\AbstractResource', [], [], '', false
        );
        $this->resourceCollectionMock = $this->getMock(
            '\Magento\Framework\Data\Collection\AbstractDb', [], [], '', false
        );
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Rule\Model\Condition\Context',
            [
            ]
        );
        $this->customerModel = $this->objectManager->getObject(
            '\Mirasvit\Rewards\Model\Earning\Rule\Condition\Customer',
            [
                'subscriberFactory' => $this->subscriberFactoryMock,
                'saleCollectionFactory' => $this->saleCollectionFactoryMock,
                'reviewCollectionFactory' => $this->reviewCollectionFactoryMock,
                'referralCollectionFactory' => $this->referralCollectionFactoryMock,
                'orderCollectionFactory' => $this->orderCollectionFactoryMock,
                'config' => $this->configMock,
                'customerData' => $this->customerDataMock,
                'context' => $this->contextMock,
                'registry' => $this->registryMock,
                'resource' => $this->resourceMock,
                'resourceCollection' => $this->resourceCollectionMock,
            ]
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->customerModel, $this->customerModel);
    }
}

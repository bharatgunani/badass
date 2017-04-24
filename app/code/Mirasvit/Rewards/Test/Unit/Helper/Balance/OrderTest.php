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



namespace Mirasvit\Rewards\Test\Unit\Helper\Balance;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Rewards\Helper\Balance\Order
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OrderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Helper\Balance\Order|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderHelper;

    /**
     * @var \Magento\Store\Model\StoreFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeFactoryMock;

    /**
     * @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Purchase\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $purchaseCollectionFactoryMock;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Purchase\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $purchaseCollectionMock;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteCollectionFactoryMock;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteCollectionMock;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Transaction\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $transactionCollectionFactoryMock;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Transaction\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $transactionCollectionMock;

    /**
     * @var \Mirasvit\Rewards\Helper\Purchase|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardsPurchaseMock;

    /**
     * @var \Mirasvit\Rewards\Helper\Balance\Earn|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardsBalanceEarnMock;

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

    public function setUp()
    {
        $this->storeFactoryMock = $this->getMock('\Magento\Store\Model\StoreFactory', ['create'], [], '', false);
        $this->storeMock = $this->getMock('\Magento\Store\Model\Store', ['load', 'save', 'delete'], [], '', false);
        $this->storeFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->storeMock));
        $this->purchaseCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Rewards\Model\ResourceModel\Purchase\CollectionFactory', ['create'], [], '', false
        );
        $this->purchaseCollectionMock = $this->getMock(
            '\Mirasvit\Rewards\Model\ResourceModel\Purchase\Collection',
            ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
            [], '', false
        );
        $this->purchaseCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->purchaseCollectionMock));
        $this->quoteCollectionFactoryMock = $this->getMock(
            '\Magento\Quote\Model\ResourceModel\Quote\CollectionFactory', ['create'], [], '', false
        );
        $this->quoteCollectionMock = $this->getMock(
            '\Magento\Quote\Model\ResourceModel\Quote\Collection',
            ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
            [], '', false
        );
        $this->quoteCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->quoteCollectionMock));
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
        $this->rewardsPurchaseMock = $this->getMock('\Mirasvit\Rewards\Helper\Purchase', [], [], '', false);
        $this->rewardsBalanceEarnMock = $this->getMock('\Mirasvit\Rewards\Helper\Balance\Earn', [], [], '', false);
        $this->rewardsBalanceMock = $this->getMock('\Mirasvit\Rewards\Helper\Balance', [], [], '', false);
        $this->rewardsDataMock = $this->getMock('\Mirasvit\Rewards\Helper\Data', [], [], '', false);
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\App\Helper\Context',
            [
            ]
        );
        $this->orderHelper = $this->objectManager->getObject(
            '\Mirasvit\Rewards\Helper\Balance\Order',
            [
                'storeFactory' => $this->storeFactoryMock,
                'purchaseCollectionFactory' => $this->purchaseCollectionFactoryMock,
                'quoteCollectionFactory' => $this->quoteCollectionFactoryMock,
                'transactionCollectionFactory' => $this->transactionCollectionFactoryMock,
                'rewardsPurchase' => $this->rewardsPurchaseMock,
                'rewardsBalanceEarn' => $this->rewardsBalanceEarnMock,
                'rewardsBalance' => $this->rewardsBalanceMock,
                'rewardsData' => $this->rewardsDataMock,
                'context' => $this->contextMock,
            ]
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->orderHelper, $this->orderHelper);
    }
}

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
 * @covers \Mirasvit\Rewards\Helper\Balance
 */
class BalanceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Helper\Balance|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $balanceHelper;

    /**
     * @var \Mirasvit\Rewards\Model\TransactionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $transactionFactoryMock;

    /**
     * @var \Mirasvit\Rewards\Model\Transaction|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $transactionMock;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Transaction\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $transactionCollectionFactoryMock;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Transaction\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $transactionCollectionMock;

    /**
     * @var \Magento\Framework\App\ResourceConnection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;

    /**
     * @var \Mirasvit\Rewards\Helper\Mail|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardsMailMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    public function setUp()
    {
        $this->transactionFactoryMock = $this->getMock(
            '\Mirasvit\Rewards\Model\TransactionFactory', ['create'], [], '', false
        );
        $this->transactionMock = $this->getMock(
            '\Mirasvit\Rewards\Model\Transaction', ['load', 'save', 'delete'], [], '', false
        );
        $this->transactionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->transactionMock));
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
        $this->resourceMock = $this->getMock('\Magento\Framework\App\ResourceConnection', [], [], '', false);
        $this->rewardsMailMock = $this->getMock('\Mirasvit\Rewards\Helper\Mail', [], [], '', false);
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\App\Helper\Context',
            [
            ]
        );
        $this->balanceHelper = $this->objectManager->getObject(
            '\Mirasvit\Rewards\Helper\Balance',
            [
                'transactionFactory' => $this->transactionFactoryMock,
                'transactionCollectionFactory' => $this->transactionCollectionFactoryMock,
                'resource' => $this->resourceMock,
                'rewardsMail' => $this->rewardsMailMock,
                'context' => $this->contextMock,
            ]
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->balanceHelper, $this->balanceHelper);
    }
}

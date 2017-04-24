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



namespace Mirasvit\Rewards\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;
use Mirasvit\Rewards\Model\Config as Config;

/**
 * @covers \Mirasvit\Rewards\Model\Purchase
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PurchaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Model\Purchase|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $purchaseModel;

    /**
     * @var \Magento\Quote\Model\QuoteFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteFactoryMock;

    /**
     * @var \Magento\Quote\Model\Quote|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteMock;

    /**
     * @var \Magento\Customer\Model\SessionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionFactoryMock;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionMock;

    /**
     * @var \Mirasvit\Rewards\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Mirasvit\Rewards\Helper\Balance\Spend|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardsBalanceSpendMock;

    /**
     * @var \Mirasvit\Rewards\Helper\Balance|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardsBalanceMock;

    /**
     * @var \Mirasvit\Rewards\Helper\Balance\Earn|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardsBalanceEarnMock;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

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
        $this->quoteFactoryMock = $this->getMock('\Magento\Quote\Model\QuoteFactory', ['create'], [], '', false);
        $this->quoteMock = $this->getMock('\Magento\Quote\Model\Quote', ['load', 'save', 'delete'], [], '', false);
        $this->quoteFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->quoteMock));
        $this->sessionFactoryMock = $this->getMock('\Magento\Customer\Model\SessionFactory', ['create'], [], '', false);
        $this->sessionMock = $this->getMock(
            '\Magento\Customer\Model\Session', ['load', 'save', 'delete'], [], '', false
        );
        $this->sessionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->sessionMock));
        $this->configMock = $this->getMock('\Mirasvit\Rewards\Model\Config', [], [], '', false);
        $this->rewardsBalanceSpendMock = $this->getMock('\Mirasvit\Rewards\Helper\Balance\Spend', [], [], '', false);
        $this->rewardsBalanceMock = $this->getMock('\Mirasvit\Rewards\Helper\Balance', [], [], '', false);
        $this->rewardsBalanceEarnMock = $this->getMock('\Mirasvit\Rewards\Helper\Balance\Earn', [], [], '', false);
        $this->customerSessionMock = $this->getMock('\Magento\Customer\Model\Session', [], [], '', false);
        $this->registryMock = $this->getMock('\Magento\Framework\Registry', [], [], '', false);
        $this->resourceMock = $this->getMock('\Mirasvit\Rewards\Model\ResourceModel\Purchase', [], [], '', false);
        $this->resourceCollectionMock = $this->getMock(
            '\Mirasvit\Rewards\Model\ResourceModel\Purchase\Collection', [], [], '', false
        );
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\Model\Context',
            [
            ]
        );
        $this->purchaseModel = $this->objectManager->getObject(
            '\Mirasvit\Rewards\Model\Purchase',
            [
                'quoteFactory' => $this->quoteFactoryMock,
                'sessionFactory' => $this->sessionFactoryMock,
                'config' => $this->configMock,
                'rewardsBalanceSpend' => $this->rewardsBalanceSpendMock,
                'rewardsBalance' => $this->rewardsBalanceMock,
                'rewardsBalanceEarn' => $this->rewardsBalanceEarnMock,
                'customerSession' => $this->customerSessionMock,
                'context' => $this->contextMock,
                'registry' => $this->registryMock,
                'resource' => $this->resourceMock,
                'resourceCollection' => $this->resourceCollectionMock,
            ]
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->purchaseModel, $this->purchaseModel);
    }
}

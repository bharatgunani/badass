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
 * @covers \Mirasvit\Rewards\Helper\Referral
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ReferralTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Helper\Referral|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $referralHelper;

    /**
     * @var \Mirasvit\Rewards\Model\ReferralFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $referralFactoryMock;

    /**
     * @var \Mirasvit\Rewards\Model\Referral|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $referralMock;

    /**
     * @var \Magento\Customer\Model\CustomerFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerFactoryMock;

    /**
     * @var \Magento\Customer\Model\Customer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerMock;

    /**
     * @var \Magento\Store\Model\StoreFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeFactoryMock;

    /**
     * @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Referral\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $referralCollectionFactoryMock;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Referral\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $referralCollectionMock;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionMock;

    /**
     * @var \Mirasvit\Rewards\Helper\Behavior|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardsBehaviorMock;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    public function setUp()
    {
        $this->referralFactoryMock = $this->getMock(
            '\Mirasvit\Rewards\Model\ReferralFactory', ['create'], [], '', false
        );
        $this->referralMock = $this->getMock(
            '\Mirasvit\Rewards\Model\Referral', ['load', 'save', 'delete'], [], '', false
        );
        $this->referralFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->referralMock));
        $this->customerFactoryMock = $this->getMock(
            '\Magento\Customer\Model\CustomerFactory', ['create'], [], '', false
        );
        $this->customerMock = $this->getMock(
            '\Magento\Customer\Model\Customer', ['load', 'save', 'delete'], [], '', false
        );
        $this->customerFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->customerMock));
        $this->storeFactoryMock = $this->getMock('\Magento\Store\Model\StoreFactory', ['create'], [], '', false);
        $this->storeMock = $this->getMock('\Magento\Store\Model\Store', ['load', 'save', 'delete'], [], '', false);
        $this->storeFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->storeMock));
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
        $this->sessionMock = $this->getMock('\Magento\Customer\Model\Session', [], [], '', false);
        $this->rewardsBehaviorMock = $this->getMock('\Mirasvit\Rewards\Helper\Behavior', [], [], '', false);
        $this->storeManagerMock = $this->getMockForAbstractClass(
            '\Magento\Store\Model\StoreManagerInterface', [], '', false, true, true, []
        );
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\App\Helper\Context',
            [
            ]
        );
        $this->referralHelper = $this->objectManager->getObject(
            '\Mirasvit\Rewards\Helper\Referral',
            [
                'referralFactory' => $this->referralFactoryMock,
                'customerFactory' => $this->customerFactoryMock,
                'storeFactory' => $this->storeFactoryMock,
                'referralCollectionFactory' => $this->referralCollectionFactoryMock,
                'session' => $this->sessionMock,
                'rewardsBehavior' => $this->rewardsBehaviorMock,
                'storeManager' => $this->storeManagerMock,
                'context' => $this->contextMock,
            ]
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->referralHelper, $this->referralHelper);
    }
}

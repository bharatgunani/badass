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

/**
 * @covers \Mirasvit\Rewards\Model\Referral
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class ReferralTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Model\Referral|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $referralModel;

    /**
     * @var \Magento\Store\Model\StoreFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeFactoryMock;

    /**
     * @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;

    /**
     * @var \Magento\Customer\Model\CustomerFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerFactoryMock;

    /**
     * @var \Magento\Customer\Model\Customer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerMock;

    /**
     * @var \Mirasvit\Rewards\Model\Config\Source\Referral\StatusFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configSourceReferralStatusFactoryMock;

    /**
     * @var \Mirasvit\Rewards\Model\Config\Source\Referral\Status|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configSourceReferralStatusMock;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerCollectionFactoryMock;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerCollectionMock;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionMock;

    /**
     * @var \Mirasvit\Rewards\Helper\Mail|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardsMailMock;

    /**
     * @var \Magento\Framework\UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlManagerMock;

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
        $this->storeFactoryMock = $this->getMock('\Magento\Store\Model\StoreFactory', ['create'], [], '', false);
        $this->storeMock = $this->getMock('\Magento\Store\Model\Store', ['load', 'save', 'delete'], [], '', false);
        $this->storeFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->storeMock));
        $this->customerFactoryMock = $this->getMock(
            '\Magento\Customer\Model\CustomerFactory', ['create'], [], '', false
        );
        $this->customerMock = $this->getMock(
            '\Magento\Customer\Model\Customer', ['load', 'save', 'delete'], [], '', false
        );
        $this->customerFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->customerMock));
        $this->configSourceReferralStatusFactoryMock = $this->getMock(
            '\Mirasvit\Rewards\Model\Config\Source\Referral\StatusFactory', ['create'], [], '', false
        );
        $this->configSourceReferralStatusMock = $this->getMock(
            '\Mirasvit\Rewards\Model\Config\Source\Referral\Status', ['load', 'save', 'delete'], [], '', false
        );
        $this->configSourceReferralStatusFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->configSourceReferralStatusMock));
        $this->customerCollectionFactoryMock = $this->getMock(
            '\Magento\Customer\Model\ResourceModel\Customer\CollectionFactory', ['create'], [], '', false
        );
        $this->customerCollectionMock = $this->getMock(
            '\Magento\Customer\Model\ResourceModel\Customer\Collection',
            ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
            [], '', false
        );
        $this->customerCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->customerCollectionMock));
        $this->sessionMock = $this->getMock('\Magento\Customer\Model\Session', [], [], '', false);
        $this->rewardsMailMock = $this->getMock('\Mirasvit\Rewards\Helper\Mail', [], [], '', false);
        $this->urlManagerMock = $this->getMockForAbstractClass(
            '\Magento\Framework\UrlInterface', [], '', false, true, true, []
        );
        $this->registryMock = $this->getMock('\Magento\Framework\Registry', [], [], '', false);
        $this->resourceMock = $this->getMock('\Mirasvit\Rewards\Model\ResourceModel\Referral', [], [], '', false);
        $this->resourceCollectionMock = $this->getMock(
            '\Mirasvit\Rewards\Model\ResourceModel\Referral\Collection', [], [], '', false
        );
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\Model\Context',
            [
            ]
        );
        $this->referralModel = $this->objectManager->getObject(
            '\Mirasvit\Rewards\Model\Referral',
            [
                'storeFactory' => $this->storeFactoryMock,
                'customerFactory' => $this->customerFactoryMock,
                'configSourceReferralStatusFactory' => $this->configSourceReferralStatusFactoryMock,
                'customerCollectionFactory' => $this->customerCollectionFactoryMock,
                'session' => $this->sessionMock,
                'rewardsMail' => $this->rewardsMailMock,
                'urlManager' => $this->urlManagerMock,
                'context' => $this->contextMock,
                'registry' => $this->registryMock,
                'resource' => $this->resourceMock,
                'resourceCollection' => $this->resourceCollectionMock,
            ]
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->referralModel, $this->referralModel);
    }
}

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



namespace Mirasvit\Rewards\Test\Unit\Controller\Referral;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Rewards\Controller\Referral\Invite
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class InviteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Controller\Referral\Invite|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $referralController;

    /**
     * @var \Mirasvit\Rewards\Model\ReferralFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $referralFactoryMock;

    /**
     * @var \Mirasvit\Rewards\Model\Referral|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $referralMock;

    /**
     * @var \Mirasvit\Rewards\Model\Customer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerMock;

    /**
     * @var \Magento\Framework\Session\Generic|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionMock;

    /**
     * @var \Mirasvit\Rewards\Helper\Referral|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardsReferralMock;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var \Magento\Framework\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\View\Result\PageFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultFactoryMock;

    /**
     * @var \Magento\Backend\Model\View\Result\Page|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultPageMock;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $redirectMock;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManagerMock;

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
        $this->customerMock = $this->getMock('\Mirasvit\Rewards\Model\Customer', [], [], '', false);
        $this->sessionMock = $this->getMock('\Magento\Framework\Session\Generic', [], [], '', false);
        $this->rewardsReferralMock = $this->getMock('\Mirasvit\Rewards\Helper\Referral', [], [], '', false);
        $this->storeManagerMock = $this->getMockForAbstractClass(
            '\Magento\Store\Model\StoreManagerInterface', [], '', false, true, true, []
        );
        $this->registryMock = $this->getMock('\Magento\Framework\Registry', [], [], '', false);
        $this->customerSessionMock = $this->getMock('\Magento\Customer\Model\Session', [], [], '', false);
        $this->requestMock = $this->getMockForAbstractClass(
            'Magento\Framework\App\RequestInterface', [], '', false, true, true, []
        );
        $this->resultFactoryMock = $this->getMock(
            'Magento\Framework\Controller\ResultFactory', ['create'], [], '', false
        );
        $this->resultPageMock = $this->getMock('Magento\Backend\Model\View\Result\Page', [], [], '', false);
        $this->resultFactoryMock->expects($this->any())
           ->method('create')
           ->willReturn($this->resultPageMock);

        $this->redirectMock = $this->getMockForAbstractClass(
            'Magento\Framework\App\Response\RedirectInterface', [], '', false, true, true, []
        );
        $this->messageManagerMock = $this->getMockForAbstractClass(
            'Magento\Framework\Message\ManagerInterface', [], '', false, true, true, []
        );
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->getMock('\Magento\Backend\App\Action\Context', [], [], '', false);
        $this->contextMock->expects($this->any())->method('getRequest')->willReturn($this->requestMock);
        $this->contextMock->expects($this->any())->method('getObjectManager')->willReturn($this->objectManager);
        $this->contextMock->expects($this->any())->method('getResultFactory')->willReturn($this->resultFactoryMock);
        $this->contextMock->expects($this->any())->method('getRedirect')->willReturn($this->redirectMock);
        $this->contextMock->expects($this->any())->method('getMessageManager')->willReturn($this->messageManagerMock);
        $this->referralController = $this->objectManager->getObject(
            '\Mirasvit\Rewards\Controller\Referral\Invite',
            [
                'referralFactory' => $this->referralFactoryMock,
                'customer' => $this->customerMock,
                'session' => $this->sessionMock,
                'rewardsReferral' => $this->rewardsReferralMock,
                'storeManager' => $this->storeManagerMock,
                'registry' => $this->registryMock,
                'customerSession' => $this->customerSessionMock,
                'context' => $this->contextMock,
            ]
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->referralController, $this->referralController);
    }
}

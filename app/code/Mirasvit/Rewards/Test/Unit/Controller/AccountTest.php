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



namespace Mirasvit\Rewards\Test\Unit\Controller;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Rewards\Controller\Account
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AccountTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Controller\Account|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $accountController;

    /**
     * @var \Mirasvit\Rewards\Model\TransactionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $transactionFactoryMock;

    /**
     * @var \Mirasvit\Rewards\Model\Transaction|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $transactionMock;

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
        $this->transactionFactoryMock = $this->getMock(
            '\Mirasvit\Rewards\Model\TransactionFactory', ['create'], [], '', false
        );
        $this->transactionMock = $this->getMock(
            '\Mirasvit\Rewards\Model\Transaction', ['load', 'save', 'delete'], [], '', false
        );
        $this->transactionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->transactionMock));
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
        $this->accountController = $this->getMockForAbstractClass(
            '\Mirasvit\Rewards\Controller\Account',
            [
                'transactionFactory' => $this->transactionFactoryMock,
                'registry' => $this->registryMock,
                'customerSession' => $this->customerSessionMock,
                'context' => $this->contextMock,
            ],
            '', false, true, true, []
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->accountController, $this->accountController);
    }
}

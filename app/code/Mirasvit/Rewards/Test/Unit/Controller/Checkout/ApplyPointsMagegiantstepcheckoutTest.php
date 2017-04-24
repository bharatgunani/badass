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



namespace Mirasvit\Rewards\Test\Unit\Controller\Checkout;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Rewards\Controller\Checkout\ApplyPointsMagegiantstepcheckout
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ApplyPointsMagegiantstepcheckoutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Controller\Checkout\ApplyPointsMagegiantstepcheckout|
     * \PHPUnit_Framework_MockObject_MockObject
     */
    protected $checkoutController;

    /**
     * @var \Magento\Checkout\Model\Type\Onepage|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $typeOnepageMock;

    /**
     * @var \Mirasvit\Rewards\Helper\Checkout|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardsCheckoutMock;

    /**
     * @var \Magento\Customer\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerDataMock;

    /**
     * @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $loggerMock;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var \Magento\Framework\Json\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $jsonEncoderMock;

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
        $this->typeOnepageMock = $this->getMock('\Magento\Checkout\Model\Type\Onepage', [], [], '', false);
        $this->rewardsCheckoutMock = $this->getMock('\Mirasvit\Rewards\Helper\Checkout', [], [], '', false);
        $this->customerDataMock = $this->getMock('\Magento\Customer\Helper\Data', [], [], '', false);
        $this->loggerMock = $this->getMockForAbstractClass('\Psr\Log\LoggerInterface', [], '', false, true, true, []);
        $this->scopeConfigMock = $this->getMockForAbstractClass(
            '\Magento\Framework\App\Config\ScopeConfigInterface', [], '', false, true, true, []
        );
        $this->jsonEncoderMock = $this->getMock('\Magento\Framework\Json\Helper\Data', [], [], '', false);
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
        $this->checkoutController = $this->objectManager->getObject(
            '\Mirasvit\Rewards\Controller\Checkout\ApplyPointsMagegiantstepcheckout',
            [
                'typeOnepage' => $this->typeOnepageMock,
                'rewardsCheckout' => $this->rewardsCheckoutMock,
                'customerData' => $this->customerDataMock,
                'logger' => $this->loggerMock,
                'scopeConfig' => $this->scopeConfigMock,
                'jsonEncoder' => $this->jsonEncoderMock,
                'context' => $this->contextMock,
            ]
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->checkoutController, $this->checkoutController);
    }
}

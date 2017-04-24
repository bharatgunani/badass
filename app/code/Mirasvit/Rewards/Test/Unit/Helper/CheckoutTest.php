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
 * @covers \Mirasvit\Rewards\Helper\Checkout
 */
class CheckoutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Helper\Checkout|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $checkoutHelper;

    /**
     * @var \Magento\Checkout\Model\Cart|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cartMock;

    /**
     * @var \Mirasvit\Rewards\Helper\Purchase|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardsPurchaseMock;

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
        $this->cartMock = $this->getMock('\Magento\Checkout\Model\Cart', [], [], '', false);
        $this->rewardsPurchaseMock = $this->getMock('\Mirasvit\Rewards\Helper\Purchase', [], [], '', false);
        $this->rewardsDataMock = $this->getMock('\Mirasvit\Rewards\Helper\Data', [], [], '', false);
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\App\Helper\Context',
            [
            ]
        );
        $this->checkoutHelper = $this->objectManager->getObject(
            '\Mirasvit\Rewards\Helper\Checkout',
            [
                'cart' => $this->cartMock,
                'rewardsPurchase' => $this->rewardsPurchaseMock,
                'rewardsData' => $this->rewardsDataMock,
                'context' => $this->contextMock,
            ]
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->checkoutHelper, $this->checkoutHelper);
    }
}

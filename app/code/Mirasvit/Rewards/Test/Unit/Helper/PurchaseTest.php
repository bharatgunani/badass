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
 * @covers \Mirasvit\Rewards\Helper\Purchase
 */
class PurchaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Helper\Purchase|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $purchaseHelper;

    /**
     * @var \Mirasvit\Rewards\Model\PurchaseFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $purchaseFactoryMock;

    /**
     * @var \Mirasvit\Rewards\Model\Purchase|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $purchaseMock;

    /**
     * @var \Magento\Checkout\Model\CartFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cartFactoryMock;

    /**
     * @var \Magento\Checkout\Model\Cart|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cartMock;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Purchase\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $purchaseCollectionFactoryMock;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Purchase\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $purchaseCollectionMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    public function setUp()
    {
        $this->purchaseFactoryMock = $this->getMock(
            '\Mirasvit\Rewards\Model\PurchaseFactory', ['create'], [], '', false
        );
        $this->purchaseMock = $this->getMock(
            '\Mirasvit\Rewards\Model\Purchase', ['load', 'save', 'delete'], [], '', false
        );
        $this->purchaseFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->purchaseMock));
        $this->cartFactoryMock = $this->getMock('\Magento\Checkout\Model\CartFactory', ['create'], [], '', false);
        $this->cartMock = $this->getMock('\Magento\Checkout\Model\Cart', ['load', 'save', 'delete'], [], '', false);
        $this->cartFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->cartMock));
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
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\App\Helper\Context',
            [
            ]
        );
        $this->purchaseHelper = $this->objectManager->getObject(
            '\Mirasvit\Rewards\Helper\Purchase',
            [
                'purchaseFactory' => $this->purchaseFactoryMock,
                'cartFactory' => $this->cartFactoryMock,
                'purchaseCollectionFactory' => $this->purchaseCollectionFactoryMock,
                'context' => $this->contextMock,
            ]
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->purchaseHelper, $this->purchaseHelper);
    }
}

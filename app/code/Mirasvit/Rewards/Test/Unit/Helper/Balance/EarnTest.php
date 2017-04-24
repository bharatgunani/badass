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
use Mirasvit\Rewards\Model\Config as Config;

/**
 * @covers \Mirasvit\Rewards\Helper\Balance\Earn
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EarnTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Helper\Balance\Earn|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $earnHelper;

    /**
     * @var \Magento\Checkout\Model\CartFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cartFactoryMock;

    /**
     * @var \Magento\Checkout\Model\Cart|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cartMock;

    /**
     * @var \Magento\Catalog\Model\ProductFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productFactoryMock;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Earning\Rule\CollectionFactory|
     * \PHPUnit_Framework_MockObject_MockObject
     */
    protected $earningRuleCollectionFactoryMock;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Earning\Rule\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $earningRuleCollectionMock;

    /**
     * @var \Mirasvit\Rewards\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\Tax\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $taxDataMock;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Customer\Model\CustomerFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerFactoryMock;

    /**
     * @var \Magento\Customer\Model\Customer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerMock;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    public function setUp()
    {
        $this->cartFactoryMock = $this->getMock('\Magento\Checkout\Model\CartFactory', ['create'], [], '', false);
        $this->cartMock = $this->getMock('\Magento\Checkout\Model\Cart', ['load', 'save', 'delete'], [], '', false);
        $this->cartFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->cartMock));
        $this->productFactoryMock = $this->getMock('\Magento\Catalog\Model\ProductFactory', ['create'], [], '', false);
        $this->productMock = $this->getMock(
            '\Magento\Catalog\Model\Product', ['load', 'save', 'delete'], [], '', false
        );
        $this->productFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->productMock));
        $this->earningRuleCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Rewards\Model\ResourceModel\Earning\Rule\CollectionFactory', ['create'], [], '', false
        );
        $this->earningRuleCollectionMock = $this->getMock(
            '\Mirasvit\Rewards\Model\ResourceModel\Earning\Rule\Collection',
            ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
            [], '', false
        );
        $this->earningRuleCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->earningRuleCollectionMock));
        $this->configMock = $this->getMock('\Mirasvit\Rewards\Model\Config', [], [], '', false);
        $this->taxDataMock = $this->getMock('\Magento\Tax\Helper\Data', [], [], '', false);
        $this->storeManagerMock = $this->getMockForAbstractClass(
            '\Magento\Store\Model\StoreManagerInterface', [], '', false, true, true, []
        );
        $this->customerFactoryMock = $this->getMock(
            '\Magento\Customer\Model\CustomerFactory', ['create'], [], '', false
        );
        $this->customerMock = $this->getMock(
            '\Magento\Customer\Model\Customer', ['load', 'save', 'delete'], [], '', false
        );
        $this->customerFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->customerMock));
        $this->customerSessionMock = $this->getMock('\Magento\Customer\Model\Session', [], [], '', false);
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\App\Helper\Context',
            [
            ]
        );
        $this->earnHelper = $this->objectManager->getObject(
            '\Mirasvit\Rewards\Helper\Balance\Earn',
            [
                'cartFactory' => $this->cartFactoryMock,
                'productFactory' => $this->productFactoryMock,
                'earningRuleCollectionFactory' => $this->earningRuleCollectionFactoryMock,
                'config' => $this->configMock,
                'taxData' => $this->taxDataMock,
                'storeManager' => $this->storeManagerMock,
                'customerFactory' => $this->customerFactoryMock,
                'customerSession' => $this->customerSessionMock,
                'context' => $this->contextMock,
            ]
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->earnHelper, $this->earnHelper);
    }
}

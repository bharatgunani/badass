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
 * @covers \Mirasvit\Rewards\Helper\Balance\Spend
 */
class SpendTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Helper\Balance\Spend|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $spendHelper;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Spending\Rule\CollectionFactory|
     * \PHPUnit_Framework_MockObject_MockObject
     */
    protected $spendingRuleCollectionFactoryMock;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Spending\Rule\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $spendingRuleCollectionMock;

    /**
     * @var \Mirasvit\Rewards\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\Tax\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $taxDataMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    public function setUp()
    {
        $this->spendingRuleCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Rewards\Model\ResourceModel\Spending\Rule\CollectionFactory', ['create'], [], '', false
        );
        $this->spendingRuleCollectionMock = $this->getMock(
            '\Mirasvit\Rewards\Model\ResourceModel\Spending\Rule\Collection',
            ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
            [], '', false
        );
        $this->spendingRuleCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->spendingRuleCollectionMock));
        $this->configMock = $this->getMock('\Mirasvit\Rewards\Model\Config', [], [], '', false);
        $this->taxDataMock = $this->getMock('\Magento\Tax\Helper\Data', [], [], '', false);
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\App\Helper\Context',
            [
            ]
        );
        $this->spendHelper = $this->objectManager->getObject(
            '\Mirasvit\Rewards\Helper\Balance\Spend',
            [
                'spendingRuleCollectionFactory' => $this->spendingRuleCollectionFactoryMock,
                'config' => $this->configMock,
                'taxData' => $this->taxDataMock,
                'context' => $this->contextMock,
            ]
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->spendHelper, $this->spendHelper);
    }
}

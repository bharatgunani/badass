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
 * @covers \Mirasvit\Rewards\Helper\Mage
 */
class MageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Helper\Mage|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mageHelper;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Grid\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderGridCollectionFactoryMock;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Grid\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderGridCollectionMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Backend\Model\Url|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendUrlManagerMock;

    public function setUp()
    {
        $this->orderGridCollectionFactoryMock = $this->getMock(
            '\Magento\Sales\Model\ResourceModel\Grid\CollectionFactory', ['create'], [], '', false
        );
        $this->orderGridCollectionMock = $this->getMock(
            '\Magento\Sales\Model\ResourceModel\Grid\Collection',
            ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
            [], '', false
        );
        $this->orderGridCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->orderGridCollectionMock));
        $this->backendUrlManagerMock = $this->getMock('\Magento\Backend\Model\Url', [], [], '', false);
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\App\Helper\Context',
            [
            ]
        );
        $this->mageHelper = $this->objectManager->getObject(
            '\Mirasvit\Rewards\Helper\Mage',
            [
                'orderGridCollectionFactory' => $this->orderGridCollectionFactoryMock,
                'context' => $this->contextMock,
                'backendUrlManager' => $this->backendUrlManagerMock,
            ]
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->mageHelper, $this->mageHelper);
    }
}

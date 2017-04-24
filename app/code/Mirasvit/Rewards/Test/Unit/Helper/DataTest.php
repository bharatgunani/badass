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
use Mirasvit\Rewards\Model\Config as Config;

/**
 * @covers \Mirasvit\Rewards\Helper\Data
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataHelper;

    /**
     * @var \Magento\Store\Model\StoreFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeFactoryMock;

    /**
     * @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;

    /**
     * @var \Magento\Store\Model\ResourceModel\Store\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeCollectionFactoryMock;

    /**
     * @var \Magento\Store\Model\ResourceModel\Store\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeCollectionMock;

    /**
     * @var \Mirasvit\Rewards\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Mirasvit\Core\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreDataMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Framework\View\DesignInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $designMock;

    public function setUp()
    {
        $this->storeFactoryMock = $this->getMock('\Magento\Store\Model\StoreFactory', ['create'], [], '', false);
        $this->storeMock = $this->getMock('\Magento\Store\Model\Store', ['load', 'save', 'delete'], [], '', false);
        $this->storeFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->storeMock));
        $this->storeCollectionFactoryMock = $this->getMock(
            '\Magento\Store\Model\ResourceModel\Store\CollectionFactory', ['create'], [], '', false
        );
        $this->storeCollectionMock = $this->getMock(
            '\Magento\Store\Model\ResourceModel\Store\Collection',
            ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
            [], '', false
        );
        $this->storeCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->storeCollectionMock));
        $this->configMock = $this->getMock('\Mirasvit\Rewards\Model\Config', [], [], '', false);
        $this->coreDataMock = $this->getMock('\Mirasvit\MstCore\Helper\Data', [], [], '', false);
        $this->storeManagerMock = $this->getMockForAbstractClass(
            '\Magento\Store\Model\StoreManagerInterface', [], '', false, true, true, []
        );
        $this->designMock = $this->getMockForAbstractClass(
            '\Magento\Framework\View\DesignInterface', [], '', false, true, true, []
        );
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\App\Helper\Context',
            [
            ]
        );
        $this->dataHelper = $this->objectManager->getObject(
            '\Mirasvit\Rewards\Helper\Data',
            [
                'storeFactory' => $this->storeFactoryMock,
                'storeCollectionFactory' => $this->storeCollectionFactoryMock,
                'config' => $this->configMock,
                'coreData' => $this->coreDataMock,
                'context' => $this->contextMock,
                'storeManager' => $this->storeManagerMock,
                'design' => $this->designMock,
            ]
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->dataHelper, $this->dataHelper);
    }
}

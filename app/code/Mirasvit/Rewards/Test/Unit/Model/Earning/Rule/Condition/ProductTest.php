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



namespace Mirasvit\Rewards\Test\Unit\Model\Earning\Rule\Condition;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;
use Mirasvit\Rewards\Model\Config as Config;

/**
 * @covers \Mirasvit\Rewards\Model\Earning\Rule\Condition\Product
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class ProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Model\Earning\Rule\Condition\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productModel;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\ItemFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockItemFactoryMock;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\Item|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockItemMock;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory|
     * \PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityAttributeSetCollectionFactoryMock;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityAttributeSetCollectionMock;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\ProductFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productFactoryMock;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \Magento\Eav\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\Catalog\Model\Product\Type|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productTypeMock;

    /**
     * @var \Magento\Backend\Model\Url|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendUrlManagerMock;

    /**
     * @var \Magento\Framework\Locale\FormatInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeFormatMock;

    /**
     * @var \Magento\Framework\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystemMock;

    /**
     * @var \Magento\Framework\Model\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Backend\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendDataMock;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productRepositoryMock;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attrSetCollectionFormatMock;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->stockItemFactoryMock = $this->getMock(
            '\Magento\CatalogInventory\Model\Stock\ItemFactory', ['create'], [], '', false
        );
        $this->stockItemMock = $this->getMock(
            '\Magento\CatalogInventory\Model\Stock\Item', ['load', 'save', 'delete'], [], '', false
        );
        $this->stockItemFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->stockItemMock));
        $this->entityAttributeSetCollectionFactoryMock = $this->getMock(
            '\Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory', ['create'], [], '', false
        );
        $this->entityAttributeSetCollectionMock = $this->getMock(
            '\Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection',
            ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
            [],
            '',
            false
        );
        $this->entityAttributeSetCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->entityAttributeSetCollectionMock));
        $this->productTypeMock = $this->getMock('\Magento\Catalog\Model\Product\Type', [], [], '', false);
        $this->backendUrlManagerMock = $this->getMock('\Magento\Backend\Model\Url', [], [], '', false);
        $this->filesystemMock = $this->getMock('\Magento\Framework\Filesystem', [], [], '', false);
        $this->contextMock = $this->objectManager
            ->getObject('\Magento\Rule\Model\Condition\Context', [], [], '', false);
        $this->backendDataMock = $this->objectManager->getObject('\Magento\Backend\Helper\Data', [], [], '', false);
        $this->configMock = $this->getMock('\Magento\Eav\Model\Config', [], [], '', false);
        $this->productFactoryMock = $this->getMock(
            '\Magento\Catalog\Model\ProductFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->productRepositoryMock = $this->getMock(
            '\Magento\Catalog\Api\ProductRepositoryInterface', [], [], '', false
        );
        $this->productMock = $this->getMock(
            '\Magento\Catalog\Model\ResourceModel\Product',
            ['loadAllAttributes', 'getAttributesByCode'],
            [],
            '',
            false
        );
        $this->attrSetCollectionFormatMock = $this->getMockForAbstractClass(
            '\Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection', [], '', false, true, true, []
        );
        $this->localeFormatMock = $this->getMockForAbstractClass(
            '\Magento\Framework\Locale\FormatInterface', [], '', false, true, true, []
        );

        $this->productFactoryMock->expects($this->any())->method('create')
            ->will($this->returnValue($this->productMock));
        $this->productMock->expects($this->any())->method('loadAllAttributes')
            ->will($this->returnValue($this->productMock));
        $this->productMock->expects($this->any())->method('getAttributesByCode')
            ->will($this->returnValue($this->productMock));

        $this->productModel = $this->objectManager->getObject(
            '\Mirasvit\Rewards\Model\Earning\Rule\Condition\Product',
            [
                'stockItemFactory'                    => $this->stockItemFactoryMock,
                'entityAttributeSetCollectionFactory' => $this->entityAttributeSetCollectionFactoryMock,
                'productType'                         => $this->productTypeMock,
                'backendUrlManager'                   => $this->backendUrlManagerMock,
                'filesystem'                          => $this->filesystemMock,
                'context'                             => $this->contextMock,
                'backendData'                         => $this->backendDataMock,
                'config'                              => $this->configMock,
                'productFactory'                      => $this->productFactoryMock,
                'productRepository'                   => $this->productRepositoryMock,
                'productResource'                     => $this->productMock,
                'attrSetCollectionFormat'             => $this->attrSetCollectionFormatMock,
                'registry'                            => $this->localeFormatMock,
            ]
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->productModel, $this->productModel);
    }
}

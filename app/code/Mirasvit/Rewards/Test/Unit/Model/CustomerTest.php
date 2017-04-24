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



namespace Mirasvit\Rewards\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Rewards\Model\Customer
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class CustomerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Model\Customer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerModel;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Transaction\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $transactionCollectionFactoryMock;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Transaction\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $transactionCollectionMock;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Balance\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $balanceCollectionFactoryMock;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Balance\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $balanceCollectionMock;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var \Magento\Framework\Model\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;

    /**
     * @var \Magento\Framework\Data\Collection\AbstractDb|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceCollectionMock;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $configMock;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfigMock;

    /**
     * @var \Magento\Customer\Model\Config\Share
     */
    protected $configShareMock;

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $addressFactoryMock;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Address\CollectionFactory
     */
    protected $addressesFactoryMock;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilderMock;

    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    protected $groupRepositoryMock;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptorMock;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTimeMock;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    protected $customerDataFactoryMock;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessorMock;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelperMock;

    /**
     * @var \Magento\Customer\Api\CustomerMetadataInterface
     */
    protected $metadataServiceMock;

    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry
     */
    protected $indexerRegistryMock;

    public function setUp()
    {
        $this->transactionCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Rewards\Model\ResourceModel\Transaction\CollectionFactory', ['create'], [], '', false
        );
        $this->transactionCollectionMock = $this->getMock(
            '\Mirasvit\Rewards\Model\ResourceModel\Transaction\Collection',
            ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
            [], '', false
        );
        $this->transactionCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->transactionCollectionMock));
        $this->balanceCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Rewards\Model\ResourceModel\Balance\CollectionFactory', ['create'], [], '', false
        );
        $this->balanceCollectionMock = $this->getMock(
            '\Mirasvit\Rewards\Model\ResourceModel\Balance\Collection',
            ['load', 'save', 'delete', 'addFieldToFilter', 'setOrder', 'getFirstItem', 'getLastItem'],
            [], '', false
        );
        $this->balanceCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->balanceCollectionMock));
        $this->customerSessionMock = $this->getMock('\Magento\Customer\Model\Session', [], [], '', false);
        $this->registryMock = $this->getMock('\Magento\Framework\Registry', [], [], '', false);
        $this->resourceMock = $this->getMock('\Magento\Customer\Model\ResourceModel\Customer', [], [], '', false);
        $this->resourceCollectionMock = $this->getMock(
            '\Magento\Framework\Data\Collection\AbstractDb', [], [], '', false
        );

        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface', [], [], '', false);
        $this->configMock = $this->getMock('\Magento\Eav\Model\Config', [], [], '', false);
        $this->scopeConfigMock = $this->getMock(
            '\Magento\Framework\App\Config\ScopeConfigInterface', [], [], '', false
        );
        $this->configShareMock = $this->getMock('\Magento\Customer\Model\Config\Share', [], [], '', false);
        $this->addressFactoryMock = $this->getMock('\Magento\Customer\Model\AddressFactory', [], [], '', false);
        $this->addressesFactoryMock = $this->getMock(
            '\Magento\Customer\Model\ResourceModel\Address\CollectionFactory', [], [], '', false
        );
        $this->transportBuilderMock = $this->getMock(
            '\Magento\Framework\Mail\Template\TransportBuilder', [], [], '', false
        );
        $this->groupRepositoryMock = $this->getMock(
            '\Magento\Customer\Api\GroupRepositoryInterface', [], [], '', false
        );
        $this->encryptorMock = $this->getMock('\Magento\Framework\Encryption\EncryptorInterface', [], [], '', false);
        $this->dateTimeMock = $this->getMock('\Magento\Framework\Stdlib\DateTime', [], [], '', false);
        $this->customerDataFactoryMock = $this->getMock(
            '\Magento\Customer\Api\Data\CustomerInterfaceFactory', [], [], '', false
        );
        $this->dataObjectProcessorMock = $this->getMock(
            '\Magento\Framework\Reflection\DataObjectProcessor', [], [], '', false
        );
        $this->dataObjectHelperMock = $this->getMock('\Magento\Framework\Api\DataObjectHelper', [], [], '', false);
        $this->metadataServiceMock = $this->getMock(
            '\Magento\Customer\Api\CustomerMetadataInterface', [], [], '', false
        );
        $this->indexerRegistryMock = $this->getMock('\Magento\Framework\Indexer\IndexerRegistry', [], [], '', false);

        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\Model\Context',
            [
            ]
        );
        $this->customerModel = $this->objectManager->getObject(
            '\Mirasvit\Rewards\Model\Customer',
            [
                'transactionCollectionFactory' => $this->transactionCollectionFactoryMock,
                'customerSession' => $this->customerSessionMock,
                'context' => $this->contextMock,
                'registry' => $this->registryMock,


                'storeManager' => $this->storeManagerMock,
                'config' => $this->configMock,
                'scopeConfig' => $this->scopeConfigMock,
                'configShare' => $this->configShareMock,
                'addressFactory' => $this->addressFactoryMock,
                'addressesFactory' => $this->addressesFactoryMock,
                'transportBuilder' => $this->transportBuilderMock,
                'groupRepository' => $this->groupRepositoryMock,
                'encryptor' => $this->encryptorMock,
                'dateTime' => $this->dateTimeMock,
                'customerDataFactory' => $this->customerDataFactoryMock,
                'dataObjectProcessor' => $this->dataObjectProcessorMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'metadataService' => $this->metadataServiceMock,
                'indexerRegistry' => $this->indexerRegistryMock,


                'resource' => $this->resourceMock,
                'resourceCollection' => $this->resourceCollectionMock,
            ]
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->customerModel, $this->customerModel);
    }
}

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



namespace Mirasvit\Rewards\Test\Unit\Model\Rewrite;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Rewards\Model\Rewrite\Sendfriend
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SendfriendTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Model\Rewrite\Sendfriend|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sendfriendModel;

    /**
     * @var \Mirasvit\Rewards\Helper\Behavior|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardsBehaviorMock;

    /**
     * @var \Magento\Framework\Model\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \Magento\Framework\Model\ResourceModel\AbstractResource|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;

    /**
     * @var \Magento\Framework\Data\Collection\AbstractDb|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceCollectionMock;


    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $transportBuilder;

    /**
     * @var \Magento\Catalog\Helper\Image|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogImage;

    /**
     * @var \Magento\SendFriend\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sendfriendData;

    /**
     * @var \Magento\Framework\Escaper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $escaper;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $remoteAddress;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cookieManager;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $inlineTranslation;

    public function setUp()
    {
        $this->rewardsBehaviorMock = $this->getMock('\Mirasvit\Rewards\Helper\Behavior', [], [], '', false);
        $this->registryMock = $this->getMock('\Magento\Framework\Registry', [], [], '', false);
        $this->resourceMock = $this->getMock(
            'Magento\Framework\Model\ResourceModel\AbstractResource',
            [
                '_construct',
                'getConnection',
                'getIdFieldName',
                'getSearchableData',
            ],
            [],
            '',
            false
        );
        $this->resourceCollectionMock = $this->getMock(
            '\Magento\Framework\Data\Collection\AbstractDb', [], [], '', false
        );

        $this->storeManager = $this->getMock('\Magento\Store\Model\StoreManagerInterface', [], [], '', false);
        $this->transportBuilder = $this->getMock(
            '\Magento\Framework\Mail\Template\TransportBuilder', [], [], '', false
        );
        $this->catalogImage = $this->getMock('\Magento\Catalog\Helper\Image', [], [], '', false);
        $this->sendfriendData = $this->getMock('\Magento\SendFriend\Helper\Data', [], [], '', false);
        $this->escaper = $this->getMock('\Magento\Framework\Escaper', [], [], '', false);
        $this->remoteAddress = $this->getMock(
            '\Magento\Framework\HTTP\PhpEnvironment\RemoteAddress', [], [], '', false
        );
        $this->cookieManager = $this->getMock('\Magento\Framework\Stdlib\CookieManagerInterface', [], [], '', false);
        $this->inlineTranslation = $this->getMock(
            '\Magento\Framework\Translate\Inline\StateInterface', [], [], '', false
        );

        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\Model\Context',
            [
            ]
        );
        $this->sendfriendModel = $this->objectManager->getObject(
            '\Mirasvit\Rewards\Model\Rewrite\Sendfriend',
            [
                'rewardsBehavior' => $this->rewardsBehaviorMock,
                'context' => $this->contextMock,
                'registry' => $this->registryMock,
                'storeManager' => $this->storeManager,
                'transportBuilder' => $this->transportBuilder,
                'catalogImage' => $this->catalogImage,
                'sendfriendData' => $this->sendfriendData,
                'escaper' => $this->escaper,
                'remoteAddress' => $this->remoteAddress,
                'cookieManager' => $this->cookieManager,
                'inlineTranslation' => $this->inlineTranslation,

                'resource' => $this->resourceMock,
                'resourceCollection' => $this->resourceCollectionMock,
            ]
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->sendfriendModel, $this->sendfriendModel);
    }
}

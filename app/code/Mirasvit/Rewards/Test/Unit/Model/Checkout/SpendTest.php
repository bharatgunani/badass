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



namespace Mirasvit\Rewards\Test\Unit\Model\Checkout;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;
use Mirasvit\Rewards\Model\Config as Config;

/**
 * @covers \Mirasvit\Rewards\Model\Checkout\Spend
 */
class SpendTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Model\Checkout\Spend|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $spendModel;

    /**
     * @var \Mirasvit\Rewards\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Mirasvit\Rewards\Helper\Purchase|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardsPurchaseMock;

    /**
     * @var \Mirasvit\Rewards\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardsDataMock;

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

    public function setUp()
    {
        $this->configMock = $this->getMock('\Mirasvit\Rewards\Model\Config', [], [], '', false);
        $this->rewardsPurchaseMock = $this->getMock('\Mirasvit\Rewards\Helper\Purchase', [], [], '', false);
        $this->rewardsDataMock = $this->getMock('\Mirasvit\Rewards\Helper\Data', [], [], '', false);
        $this->registryMock = $this->getMock('\Magento\Framework\Registry', [], [], '', false);
        $this->resourceMock = $this->getMock('\Mirasvit\Rewards\Model\ResourceModel\Checkout\Spend', [], [], '', false);
        $this->resourceCollectionMock = $this->getMock(
            '\Mirasvit\Rewards\Model\ResourceModel\Checkout\Spend\Collection', [], [], '', false
        );
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\Model\Context',
            [
            ]
        );
        $this->spendModel = $this->objectManager->getObject(
            '\Mirasvit\Rewards\Model\Checkout\Spend',
            [
                'config' => $this->configMock,
                'rewardsPurchase' => $this->rewardsPurchaseMock,
                'rewardsData' => $this->rewardsDataMock,
                'context' => $this->contextMock,
                'registry' => $this->registryMock,
                'resource' => $this->resourceMock,
                'resourceCollection' => $this->resourceCollectionMock,
            ]
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->spendModel, $this->spendModel);
    }
}

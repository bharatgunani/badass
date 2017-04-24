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

/**
 * @covers \Mirasvit\Rewards\Model\Checkout\Earn
 */
class EarnTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Model\Checkout\Earn|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $earnModel;

    /**
     * @var \Mirasvit\Rewards\Helper\Purchase|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardsPurchaseMock;

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
     * @var \Mirasvit\Rewards\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardsDataMock;

    public function setUp()
    {
        $this->rewardsPurchaseMock = $this->getMock('\Mirasvit\Rewards\Helper\Purchase', [], [], '', false);
        $this->rewardsDataMock = $this->getMock('\Mirasvit\Rewards\Helper\Data', [], [], '', false);
        $this->registryMock = $this->getMock('\Magento\Framework\Registry', [], [], '', false);
        $this->resourceMock = $this->getMock(
            '\Magento\Framework\Model\ResourceModel\AbstractResource', [], [], '', false
        );
        $this->resourceCollectionMock = $this->getMock(
            '\Magento\Framework\Data\Collection\AbstractDb', [], [], '', false
        );
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\Model\Context',
            [
            ]
        );
        $this->earnModel = $this->objectManager->getObject(
            '\Mirasvit\Rewards\Model\Checkout\Earn',
            [
                'rewardsPurchase' => $this->rewardsPurchaseMock,
                'rewardsData' => $this->rewardsDataMock,
                'context' => $this->contextMock,
                'registry' => $this->registryMock,
                'resource' => $this->resourceMock,
                'resourceCollection' => $this->resourceCollectionMock,
                'data' => []
            ]
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->earnModel, $this->earnModel);
    }
}

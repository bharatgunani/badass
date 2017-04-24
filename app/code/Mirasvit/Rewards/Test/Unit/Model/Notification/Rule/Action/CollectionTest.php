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



namespace Mirasvit\Rewards\Test\Unit\Model\Notification\Rule\Action;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Rewards\Model\Notification\Rule\Action\Collection
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Model\Notification\Rule\Action\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionModel;

    /**
     * @var \Magento\Framework\View\Asset\Repository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $assetRepoMock;

    /**
     * @var \Magento\Framework\View\LayoutInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutMock;

    /**
     * @var \Magento\Rule\Model\ActionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $actionFactoryMock;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->assetRepoMock = $this->getMock('\Magento\Framework\View\Asset\Repository', [], [], '', false);
        $this->layoutMock = $this->getMock('\Magento\Framework\View\LayoutInterface', [], [], '', false);
        $this->actionFactoryMock = $this->getMock('\Magento\Rule\Model\ActionFactory', [], [], '', false);

        $this->collectionModel = $this->objectManager->getObject(
            '\Mirasvit\Rewards\Model\Notification\Rule\Action\Collection',
            [
                'assetRepo' => $this->assetRepoMock,
                'layout' => $this->layoutMock,
                'actionFactory' => $this->actionFactoryMock,
                'data' => []
            ]
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->collectionModel, $this->collectionModel);
    }
}

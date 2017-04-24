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



namespace Mirasvit\Rewards\Test\Unit\Model\Config\Source;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Rewards\Model\Config\Source\Direction
 */
class DirectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Model\Config\Source\Direction|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $directionModel;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->directionModel = $this->objectManager->getObject(
            '\Mirasvit\Rewards\Model\Config\Source\Direction',
            [
            ]
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->directionModel, $this->directionModel);
    }
}

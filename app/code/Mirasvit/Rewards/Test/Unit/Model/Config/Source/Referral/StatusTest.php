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



namespace Mirasvit\Rewards\Test\Unit\Model\Config\Source\Referral;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Rewards\Model\Config\Source\Referral\Status
 */
class StatusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Model\Config\Source\Referral\Status|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $statusModel;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->statusModel = $this->objectManager->getObject(
            '\Mirasvit\Rewards\Model\Config\Source\Referral\Status',
            [
            ]
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->statusModel, $this->statusModel);
    }
}

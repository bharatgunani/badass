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
 * @covers \Mirasvit\Rewards\Helper\Validator
 */
class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Helper\Validator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $validatorHelper;

    /**
     * @var \Mirasvit\MstCore\Helper\Validator\Crc|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mstcoreValidatorCrcMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    public function setUp()
    {
        $this->mstcoreValidatorCrcMock = $this->getMock('\Mirasvit\MstCore\Helper\Validator\Crc', [], [], '', false);
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\App\Helper\Context',
            [
            ]
        );
        $this->validatorHelper = $this->objectManager->getObject(
            '\Mirasvit\Rewards\Helper\Validator',
            [
                'mstcoreValidatorCrc' => $this->mstcoreValidatorCrcMock,
                'context' => $this->contextMock,
            ]
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->validatorHelper, $this->validatorHelper);
    }
}

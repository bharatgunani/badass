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
 * @covers \Mirasvit\Rewards\Helper\Mail
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MailTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rewards\Helper\Mail|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mailHelper;

    /**
     * @var \Magento\Email\Model\TemplateFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $emailTemplateFactoryMock;

    /**
     * @var \Magento\Email\Model\Template|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $emailTemplateMock;

    /**
     * @var \Mirasvit\Rewards\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Mirasvit\MstCore\Model\Translate|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $translateMock;

    /**
     * @var \Mirasvit\Rewards\Helper\Balance|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardsBalanceMock;

    /**
     * @var \Mirasvit\Rewards\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardsDataMock;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Framework\View\Asset\Repository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $assetRepoMock;

    /**
     * @var \Magento\Framework\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystemMock;

    /**
     * @var \Magento\Framework\View\DesignInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $designMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    public function setUp()
    {
        $this->emailTemplateFactoryMock = $this->getMock(
            '\Magento\Email\Model\TemplateFactory', ['create'], [], '', false
        );
        $this->emailTemplateMock = $this->getMock(
            '\Magento\Email\Model\Template', ['load', 'save', 'delete'], [], '', false
        );
        $this->emailTemplateFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->emailTemplateMock));
        $this->configMock = $this->getMock('\Mirasvit\Rewards\Model\Config', [], [], '', false);
        $this->translateMock = $this->getMock('\Mirasvit\MstCore\Model\Translate', [], [], '', false);
        $this->rewardsBalanceMock = $this->getMock('\Mirasvit\Rewards\Helper\Balance', [], [], '', false);
        $this->rewardsDataMock = $this->getMock('\Mirasvit\Rewards\Helper\Data', [], [], '', false);
        $this->storeManagerMock = $this->getMockForAbstractClass(
            '\Magento\Store\Model\StoreManagerInterface', [], '', false, true, true, []
        );
        $this->assetRepoMock = $this->getMock('\Magento\Framework\View\Asset\Repository', [], [], '', false);
        $this->filesystemMock = $this->getMock('\Magento\Framework\Filesystem', [], [], '', false);
        $this->designMock = $this->getMockForAbstractClass(
            '\Magento\Framework\View\DesignInterface', [], '', false, true, true, []
        );
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\App\Helper\Context',
            [
            ]
        );
        $this->mailHelper = $this->objectManager->getObject(
            '\Mirasvit\Rewards\Helper\Mail',
            [
                'emailTemplateFactory' => $this->emailTemplateFactoryMock,
                'config' => $this->configMock,
                'translate' => $this->translateMock,
                'rewardsBalance' => $this->rewardsBalanceMock,
                'rewardsData' => $this->rewardsDataMock,
                'storeManager' => $this->storeManagerMock,
                'assetRepo' => $this->assetRepoMock,
                'filesystem' => $this->filesystemMock,
                'design' => $this->designMock,
                'context' => $this->contextMock,
            ]
        );
    }

    public function testDummy()
    {
        $this->assertEquals($this->mailHelper, $this->mailHelper);
    }
}

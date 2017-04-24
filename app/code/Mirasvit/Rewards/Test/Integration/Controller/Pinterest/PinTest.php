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



namespace Mirasvit\Rewards\Controller\Pinterest;

/**
 * @magentoDataFixture Mirasvit/Rewards/_files/customer_with_pinterest_rule.php
 *
 * @package Mirasvit\Rewards\Controller\Pinterest
 */
class PinTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /** @var \Magento\Customer\Api\AccountManagementInterface */
    private $accountManagement;

    protected function setUp()
    {
        parent::setUp();
        $logger = $this->getMock('Psr\Log\LoggerInterface', [], [], '', false);
        $session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Customer\Model\Session',
            [$logger]
        );
        $this->accountManagement = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Customer\Api\AccountManagementInterface'
        );
        $customer = $this->accountManagement->authenticate('customer@example.com', 'password');
        $session->setCustomerDataAsLoggedIn($customer);
    }

    /**
     *
     * @covers  Mirasvit\Rewards\Controller\Pinterest\Pin::execute
     *
     * @magentoAppIsolation enabled
     */
    public function testPinAction()
    {
        $checkoutSession = $this->_objectManager->create('Magento\Checkout\Model\Session');
        $checkoutSession->getQuote();

        /** @var \Magento\Framework\Data\Form\FormKey $formKey */
        $formKey = $this->_objectManager->get('Magento\Framework\Data\Form\FormKey');
        $postData = [
            'form_key' => $formKey->getFormKey(),
            'url' => 'test-fb-like-url',
        ];

        $this->getRequest()->setPostValue($postData);
        $this->dispatch('rewards/pinterest/pin');

        $body = $this->getResponse()->getBody();

        $this->assertContains("You've earned 15 Reward Points for Pin!", $body);
    }
}

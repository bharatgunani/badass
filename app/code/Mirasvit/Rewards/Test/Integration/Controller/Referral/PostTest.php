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



namespace Mirasvit\Rewards\Controller\Adminhtml\Referral;

/**
 * @magentoDataFixture Mirasvit/Rewards/_files/customer.php
 */
class PostTest extends \Magento\TestFramework\TestCase\AbstractController
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
     * @covers  Mirasvit\Rewards\Controller\Adminhtml\Referral\Post::execute
     */
    public function testPostAction()
    {
        $refEmail = 'send_ref_invite@test.ml';

        $this->getRequest()->setMethod(
            'POST'
        )->setPostValue(
            [
                'form_key' => $this->_objectManager->get('Magento\Framework\Data\Form\FormKey')->getFormKey(),
                'email' => [
                    $refEmail
                ],
                'name' => 'Test Referral',
                'message' => 'test referral message',
            ]
        );
        $this->dispatch('rewards/referral/post');

        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertTrue($this->getResponse()->isRedirect());

        $this->assertRedirect($this->stringContains('rewards/referral'));
        $this->assertSessionMessages(
            $this->equalTo(['Your invitations were sent. Thanks!']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );

        $referral = $this->_objectManager->create('Mirasvit\Rewards\Model\Referral')->load($refEmail, 'email');

        $this->assertNotEquals(0, $referral->getId());
        $this->assertEquals($refEmail, $referral->getEmail());
    }
}

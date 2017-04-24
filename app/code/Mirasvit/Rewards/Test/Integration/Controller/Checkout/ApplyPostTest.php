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



namespace Mirasvit\Rewards\Controller\Checkout;

class ApplyPointsPostTest extends \Magento\TestFramework\TestCase\AbstractController
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
     * @magentoDataFixture Mirasvit/Rewards/_files/rewards_quote_with_simple_product.php
     *
     * @covers  Mirasvit\Rewards\Controller\Checkout\ApplyPointsPost::execute
     *
     * @magentoAppIsolation enabled
     */
    public function testApplyPostAction()
    {
        $checkoutSession = $this->_objectManager->create('Magento\Checkout\Model\Session');
        $quote = $checkoutSession->getQuote();

        $purchaseHelper = $this->_objectManager->get('\Mirasvit\Rewards\Helper\Purchase');
        $purchase = $purchaseHelper->getByQuote($quote->getId());
        $purchase->setSpendPoints(4);

        /** @var \Magento\Framework\Data\Form\FormKey $formKey */
        $formKey = $this->_objectManager->get('Magento\Framework\Data\Form\FormKey');
        $postData = [
            'form_key' => $formKey->getFormKey(),
            'remove-points' => 0,
            'points_amount' => 4,
            'points_all' => 'on',
        ];

        $this->getRequest()->setPostValue($postData);
        $this->dispatch('rewards/checkout/applypointspost');

        $this->assertSessionMessages(
            $this->equalTo(['4 Reward Points was applied.']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );

        $quote = $this->_objectManager->create('Magento\Quote\Model\Quote');
        $quote->load($checkoutSession->getQuote()->getId());
    }
}

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



namespace Mirasvit\Rewards\Controller\Referral;

/**
 *
 * @magentoDataFixture Mirasvit/Rewards/_files/customer_referral_link.php
 *
 * @package Mirasvit\Rewards\Controller\Referral
 */
class ReferralVisitTest extends \Magento\TestFramework\TestCase\AbstractController
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
     * @covers  Mirasvit\Rewards\Controller\Referral\ReferralVisit::execute
     */
    public function testReferralVisitAction()
    {
        $this->getRequest()->setParam('referral_link', 'df78g98dfgd9fg6d987fg8d');
        $this->dispatch('rewards/referral/referralvisit');

        $session = $this->_objectManager->get('Magento\Framework\Session\SessionManager');

        $referral = $this->_objectManager->create('Mirasvit\Rewards\Model\Referral')->load($session->getReferral());

        $this->assertEquals('visited', $referral->getStatus());
        $this->assertEquals('', $referral->getEmail());
    }
}

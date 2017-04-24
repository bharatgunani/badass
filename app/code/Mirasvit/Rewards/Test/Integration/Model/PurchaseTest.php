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



namespace Mirasvit\Rewards\Model;

/**
 * @covers \Mirasvit\Rewards\Model\Purchase
 */
class PurchaseTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \Mirasvit\Rewards\Model\Purchase */
    protected $purchaseModel;

    /** @var \Magento\Customer\Api\AccountManagementInterface */
    private $accountManagement;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    public function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

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

        $this->purchaseModel = $this->objectManager->create('Mirasvit\Rewards\Model\Purchase');
    }

    /**
     * @covers \Mirasvit\Rewards\Model\Purchase::getQuote
     *
     * @magentoDataFixture Mirasvit/Rewards/_files/customer.php
     */
    public function testGetQuote()
    {
        $this->setQuote();

        $quote = $this->purchaseModel->getQuote();
        $this->assertNotEquals(0, $quote->getId());
        $this->assertEquals(1, $quote->getIsActive());
    }

    /**
     * @covers \Mirasvit\Rewards\Model\Purchase::refreshPointsNumber
     *
     * @magentoDataFixture Mirasvit/Rewards/_files/rewards_quote_with_simple_product.php
     */
    public function testRefreshPointsNumber()
    {
        $this->setQuote();

        $this->purchaseModel->setSpendPoints(55);
        $this->purchaseModel->refreshPointsNumber(true);

        $this->assertNotEquals(55, $this->purchaseModel->getSpendPoints());
        $this->assertEquals(4, $this->purchaseModel->getSpendPoints());

        $this->purchaseModel->setSpendPoints(2);
        $this->purchaseModel->refreshPointsNumber(true);

        $this->assertNotEquals(55, $this->purchaseModel->getSpendPoints());
        $this->assertEquals(2, $this->purchaseModel->getSpendPoints());
    }

    protected function setQuote()
    {
        $checkoutSession = $this->objectManager->create('Magento\Checkout\Model\Session');
        $quote = $checkoutSession->getQuote();
        $quote->save();

        $this->purchaseModel->setData(
            [
                'quote_id' => $quote->getId()
            ]
        )->save();
    }
}

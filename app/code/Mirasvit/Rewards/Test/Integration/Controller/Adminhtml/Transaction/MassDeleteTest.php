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


/**
 * Admin Transaction Mass Delete Test.
 */
namespace Mirasvit\Rewards\Controller\Adminhtml\Transaction;

/**
 * @magentoDataFixture Mirasvit/Rewards/_files/customer.php
 *
 * @magentoAppArea adminhtml
 */
class MassDeleteTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Rewards::reward_points_transaction';
        $this->uri = 'backend/rewards/transaction/massdelete';
        parent::setUp();
    }

    /**
     * @covers  Mirasvit\Rewards\Controller\Adminhtml\Transaction\MassDelete::execute
     */
    public function testMassDeleteAction()
    {
        $transactionIdFirst = $this->_objectManager->create('Mirasvit\Rewards\Model\Transaction')
            ->load('test transactions', 'comment')
            ->getId();
        $transactionIdSecond = $this->_objectManager->create('Mirasvit\Rewards\Model\Transaction')
            ->load('test transactions', 'comment')
            ->getId();

        $this->getRequest()->setParam('transaction_id', [$transactionIdFirst, $transactionIdSecond]);
        $this->dispatch('backend/rewards/transaction/massdelete');
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertTrue($this->getResponse()->isRedirect());

        $this->assertSessionMessages(
            $this->equalTo(['Total of 2 record(s) were successfully deleted']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect($this->stringContains('backend/rewards/transaction/index/'));

        /** @var \Mirasvit\Rewards\Model\Transaction $transaction */
        $transaction = $this->_objectManager->create('Mirasvit\Rewards\Model\Transaction')->load($transactionIdFirst);
        $this->assertEquals(0, $transaction->getId());
        $transaction = $this->_objectManager->create('Mirasvit\Rewards\Model\Transaction')->load($transactionIdSecond);
        $this->assertEquals(0, $transaction->getId());
    }
}

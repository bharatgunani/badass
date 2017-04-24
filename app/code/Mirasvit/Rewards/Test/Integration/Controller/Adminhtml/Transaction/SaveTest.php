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



namespace Mirasvit\Rewards\Controller\Adminhtml\Transaction;

/**
 * @magentoAppArea adminhtml
 */
class SaveTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Rewards::reward_points_transaction';
        $this->uri = 'backend/rewards/transaction/save';
        parent::setUp();
    }

    /**
     * @covers  Mirasvit\Rewards\Controller\Adminhtml\Transaction\Save::execute
     *
     * @magentoDataFixture Mirasvit/Rewards/_files/customer.php
     */
    public function testSaveAction()
    {
        $name = 'Test Transaction 3';

        $this->getRequest()->setMethod(
            'POST'
        )->setPostValue(
            [
                'form_key' => $this->_objectManager->get('Magento\Framework\Data\Form\FormKey')->getFormKey(),
                'in_transaction_user' => '1=true',
                'amount' => 3,
                'history_message' => $name,
                'email_message' => 'test transactions3 email',
            ]
        );
        $this->dispatch('backend/rewards/transaction/save');

        $this->assertRedirect($this->stringContains('backend/rewards/transaction'));
        $this->assertSessionMessages(
            $this->equalTo(['Transaction was successfully saved']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );

        $transaction = $this->_objectManager->create('Mirasvit\Rewards\Model\Transaction')->load($name, 'comment');

        $this->assertNotEquals(0, $transaction->getId());
        $this->assertEquals(3, $transaction->getAmount());

        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertTrue($this->getResponse()->isRedirect());
    }
}

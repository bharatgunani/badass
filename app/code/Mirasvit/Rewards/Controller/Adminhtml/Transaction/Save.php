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
 * @version   1.1.25
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rewards\Controller\Adminhtml\Transaction;

use Magento\Framework\Controller\ResultFactory;

class Save extends \Mirasvit\Rewards\Controller\Adminhtml\Transaction
{
    /**
     * @return void
     */
    public function execute()
    {
        if ($data = $this->getRequest()->getParams()) {
            $customers = $data['in_transaction_user'];
            parse_str($customers, $customers);
            $customers = array_keys($customers);

            try {
                foreach ($customers as $customerId) {
                    if ((int) $customerId > 0) {
                        $emailMessage = '';
                        if (isset($data['email_message'])) {
                            $emailMessage = $data['email_message'];
                        }
                        $this->rewardsBalance->changePointsBalance(
                            $customerId,
                            $data['amount'],
                            $data['history_message'],
                            false,
                            true,
                            $emailMessage
                        );
                    }
                }

                $this->messageManager->addSuccess(__('Transaction was successfully saved'));
                $this->backendSession->setFormData(false);

                $this->_redirect('*/*/index');

                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->backendSession->setFormData($data);
                $this->_redirect('*/*/index');

                return;
            }
        }
        $this->messageManager->addError(__('Unable to find Transaction to save'));
        $this->_redirect('*/*/');
    }
}

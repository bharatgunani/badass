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



namespace Mirasvit\Rewards\Controller\Adminhtml\Spending\Rule;

use Magento\Framework\Controller\ResultFactory;

class MassDelete extends \Mirasvit\Rewards\Controller\Adminhtml\Spending\Rule
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $ids = $this->getRequest()->getParam('spending_rule_id');
        if (!is_array($ids)) {
            $this->messageManager->addError(__('Please select Spending Rule(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    /** @var \Mirasvit\Rewards\Model\Spending\Rule $spendingRule */
                    $spendingRule = $this->spendingRuleFactory->create()
                        ->setIsMassDelete(true)
                        ->load($id);
                    $spendingRule->delete();
                }
                $this->messageManager->addSuccess(
                    __(
                        'Total of %1 record(s) were successfully deleted', count($ids)
                    )
                );
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}

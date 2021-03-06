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

class Add extends \Mirasvit\Rewards\Controller\Adminhtml\Spending\Rule
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->_initSpendingRule();

        $this->initPage($resultPage)->getConfig()->getTitle()->prepend(__('New Spending Rule'));

        $resultPage->getLayout()
            ->getBlock('head')
            ;
        $this->_addContent(
            $resultPage->getLayout()->createBlock('\Mirasvit\Rewards\Block\Adminhtml\Spending\Rule\Edit')
        )->_addLeft(
            $resultPage->getLayout()->createBlock('\Mirasvit\Rewards\Block\Adminhtml\Spending\Rule\Edit\Tabs')
        );
        $resultPage->getLayout()->getBlock('head');

        return $resultPage;
    }
}

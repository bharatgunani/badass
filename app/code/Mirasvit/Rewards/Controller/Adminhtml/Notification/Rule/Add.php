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



namespace Mirasvit\Rewards\Controller\Adminhtml\Notification\Rule;

use Magento\Framework\Controller\ResultFactory;

class Add extends \Mirasvit\Rewards\Controller\Adminhtml\Notification\Rule
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->initPage($resultPage)->getConfig()->getTitle()->prepend(__('New Notification Rule'));

        $this->_initNotificationRule();

        $resultPage->getLayout()
            ->getBlock('head');
        $this->_addContent(
            $resultPage->getLayout()->createBlock('\Mirasvit\Rewards\Block\Adminhtml\Notification\Rule\Edit')
        )->_addLeft(
            $resultPage->getLayout()->createBlock('\Mirasvit\Rewards\Block\Adminhtml\Notification\Rule\Edit\Tabs')
        );
        $resultPage->getLayout()->getBlock('head');

        return $resultPage;
    }
}

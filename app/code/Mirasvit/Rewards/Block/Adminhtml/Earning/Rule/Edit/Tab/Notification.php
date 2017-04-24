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



namespace Mirasvit\Rewards\Block\Adminhtml\Earning\Rule\Edit\Tab;

class Notification extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var \Mirasvit\Rewards\Helper\Message
     */
    protected $rewardsMessage;

    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $formFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Rewards\Helper\Message      $rewardsMessage
     * @param \Magento\Framework\Data\FormFactory   $formFactory
     * @param \Magento\Framework\Registry           $registry
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array                                 $data
     */
    public function __construct(
        \Mirasvit\Rewards\Helper\Message $rewardsMessage,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->rewardsMessage = $rewardsMessage;
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create();
        $this->setForm($form);
        /** @var \Mirasvit\Rewards\Model\Earning\Rule $earningRule */
        $earningRule = $this->registry->registry('current_earning_rule');

        $fieldset = $form->addFieldset('notification_fieldset', ['legend' => __('Notifications')]);
        if ($earningRule->getId()) {
            $fieldset->addField('earning_rule_id', 'hidden', [
                'name' => 'earning_rule_id',
                'value' => $earningRule->getId(),
            ]);
        }
        $fieldset->addField('store_id', 'hidden', [
            'name' => 'store_id',
            'value' => (int) $this->getRequest()->getParam('store'),
        ]);

        $fieldset->addField('history_message', 'text', [
            'label' => __('Message in the rewards history'),
            'required' => true,
            'name' => 'history_message',
            'value' => $earningRule->getHistoryMessage(),
            'note' => __('Customer will see this in his account'),
            'after_element_html' => ' [STORE VIEW]',
        ]);

        $fieldset->addField('email_message', 'editor', [
            'label' => __('Message for customer notification email'),
            'name' => 'email_message',
            'value' => $earningRule->getEmailMessage(),
            'wysiwyg' => true,
            'note' => $this->rewardsMessage->getNoteWithVariables(),
            'after_element_html' => ' [STORE VIEW]',
            'style' => 'width: 600px; height: 300px;',
        ]);

        return parent::_prepareForm();
    }

    /**
     * @return string
     */
    public function getDefaultEmailMessage()
    {
        return
        '<p>Dear {{var customer.name}},</p>
<p>Your account balance has been updated at <a href="{{store url=""}}">{{var store.getFrontendName()}}</a>. <p>
<ul>
<li>Balance Update: <b>{{var transaction_amount}}</b></li>
<li>Balance Total: <b>{{var balance_total}}</b></li>
</ul>
Thank you,<br>
<strong>{{var store.getFrontendName()}}</strong>
        ';
    }
}

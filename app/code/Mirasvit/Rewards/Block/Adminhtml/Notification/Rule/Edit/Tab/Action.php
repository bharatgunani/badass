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



namespace Mirasvit\Rewards\Block\Adminhtml\Notification\Rule\Edit\Tab;

class Action extends \Magento\Backend\Block\Widget\Form
{
    public function __construct(
        \Mirasvit\Rewards\Helper\Message $messageHelper,
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Mirasvit\Rewards\Model\Config\Source\Notification\Position $configSourceNotificationPosition,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->messageHelper                    = $messageHelper;
        $this->storeFactory                     = $storeFactory;
        $this->configSourceNotificationPosition = $configSourceNotificationPosition;
        $this->formFactory                      = $formFactory;
        $this->registry                         = $registry;
        $this->wysiwygConfig                    = $wysiwygConfig;

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
        /** @var \Mirasvit\Rewards\Model\Notification\Rule $notificationRule */
        $notificationRule = $this->registry->registry('current_notification_rule');

        $fieldset = $form->addFieldset('action_fieldset', ['legend' => __('Actions')]);
        if ($notificationRule->getId()) {
            $fieldset->addField('notification_rule_id', 'hidden', [
                'name'  => 'notification_rule_id',
                'value' => $notificationRule->getId(),
            ]);
        }
        $fieldset->addField('store_id', 'hidden', [
            'name'  => 'store_id',
            'value' => (int) $this->getRequest()->getParam('store'),
        ]);

        $fieldset->addField('type', 'multiselect', [
            'label'    => __('Show message on'),
            'required' => true,
            'name'     => 'type[]',
            'value'    => $notificationRule->getType(),
            'values'   => $this->configSourceNotificationPosition->toOptionArray(),
        ]);
        $fieldset->addField('message', 'editor', [
            'label'    => __('Message'),
            'required' => false,
            'name'     => 'message',
            'value'    => $notificationRule->getMessage(),
            'wysiwyg'  => true,
            'style'    => 'height:15em',
            'config'   => $this->wysiwygConfig->getConfig(),
            'note'     => $this->getMessageNote(),
        ]);

        return parent::_prepareForm();
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    protected function getMessageNote()
    {
        $note = '';
        $store = $this->storeFactory->create()->load((int) $this->getRequest()->getParam('store'));
        if (count($store->getAvailableCurrencyCodes(true)) > 1) {
            $note = __('If you insert a variable like ##100.00, it will be converted to the price in currency of '.
                'customer. E.g. ##100.00 will be displayed as \'$100.00\' in USD and as \'€88\' in Euro.<br><br>');
        }

        $note .= $this->messageHelper->getNotificationNoteWithVariables();

        return $note;
    }

    /************************/
}

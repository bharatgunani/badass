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

class ProductNotification extends \Magento\Backend\Block\Widget\Form
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
     * @param \Magento\Cms\Model\Wysiwyg\Config     $wysiwygConfig
     * @param \Magento\Framework\Data\FormFactory   $formFactory
     * @param \Magento\Framework\Registry           $registry
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array                                 $data
     */
    public function __construct(
        \Mirasvit\Rewards\Helper\Message $rewardsMessage,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->rewardsMessage = $rewardsMessage;
        $this->wysiwygConfig  = $wysiwygConfig;
        $this->formFactory    = $formFactory;
        $this->registry       = $registry;
        $this->context        = $context;

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

        $fieldset->addField('product_notification', 'editor', [
            'label'              => __('Message'),
            'required'           => false,
            'name'               => 'product_notification',
            'wysiwyg'            => true,
            'style'              => 'height:15em',
            'config'             => $this->wysiwygConfig->getConfig(),
            'value'              => $earningRule->getProductNotification(),
            'note'               => $this->rewardsMessage->getEarningRuleNotificationNote(),
            'after_element_html' => ' [STORE VIEW]',
        ]);

        return parent::_prepareForm();
    }
}

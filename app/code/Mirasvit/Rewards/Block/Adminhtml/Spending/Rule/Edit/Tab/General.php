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



namespace Mirasvit\Rewards\Block\Adminhtml\Spending\Rule\Edit\Tab;

class General extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @var \Magento\Config\Model\Config\Source\Website
     */
    protected $systemConfigSourceWebsite;

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
     * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory
     * @param \Magento\Config\Model\Config\Source\Website                   $systemConfigSourceWebsite
     * @param \Magento\Framework\Data\FormFactory                           $formFactory
     * @param \Magento\Framework\Registry                                   $registry
     * @param \Magento\Backend\Block\Widget\Context                         $context
     * @param array                                                         $data
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory,
        \Magento\Config\Model\Config\Source\Website $systemConfigSourceWebsite,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->systemConfigSourceWebsite = $systemConfigSourceWebsite;
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
        /** @var \Mirasvit\Rewards\Model\Spending\Rule $spendingRule */
        $spendingRule = $this->registry->registry('current_spending_rule');

        $fieldset = $form->addFieldset('edit_fieldset', ['legend' => __('General Information')]);
        if ($spendingRule->getId()) {
            $fieldset->addField('spending_rule_id', 'hidden', [
                'name' => 'spending_rule_id',
                'value' => $spendingRule->getId(),
            ]);
        }
        $fieldset->addField('store_id', 'hidden', [
            'name' => 'store_id',
            'value' => (int) $this->getRequest()->getParam('store'),
        ]);

        $fieldset->addField('name', 'text', [
            'label' => __('Rule Name'),
            'required' => true,
            'name' => 'name',
            'value' => $spendingRule->getName(),
        ]);
        $fieldset->addField('is_active', 'select', [
            'label' => __('Is Active'),
            'required' => true,
            'name' => 'is_active',
            'value' => $spendingRule->getIsActive(),
            'values' => [0 => __('No'), 1 => __('Yes')],
        ]);
        $dateFormat = $this->_localeDate->getDateFormat(
            \IntlDateFormatter::SHORT
        );
        $fieldset->addField(
            'active_to',
            'date',
            [
                'label' => __('Active From'),
                'name' => 'active_from',
                'date_format' => $dateFormat,
                'value' => $spendingRule->getActiveFrom(),
            ]
        );
        $fieldset->addField(
            'active_to_time',
            'date',
            [
                'label' => __('Active To'),
                'name' => 'active_to',
                'date_format' => $dateFormat,
                'value' => $spendingRule->getActiveTo(),
            ]
        );
        $websites = $this->systemConfigSourceWebsite->toOptionArray();
        if (!$this->context->getStoreManager()->isSingleStoreMode() && count($websites) > 1) {
            $fieldset->addField('website_ids', 'multiselect', [
                'name' => 'website_ids[]',
                'label' => __('Websites'),
                'title' => __('Websites'),
                'required' => true,
                'values' => $websites,
                'value' => $spendingRule->getWebsiteIds(),
            ]);
        } else {
            $fieldset->addField('website_ids', 'hidden', [
                'name' => 'website_ids',
                'value' => $this->context->getStoreManager()->getStore(true)->getWebsiteId(),
            ]);
            $spendingRule->setWebsiteId($this->context->getStoreManager()->getStore(true)->getWebsiteId());
        }
        $fieldset->addField('customer_group_ids', 'multiselect', [
            'label' => __('Customer Groups'),
            'required' => true,
            'name' => 'customer_group_ids[]',
            'value' => $spendingRule->getCustomerGroupIds(),
            'values' => $this->groupCollectionFactory->create()->toOptionArray(),
        ]);
        $fieldset->addField('is_stop_processing', 'select', [
            'label' => __('Stop further rules processing'),
            'name' => 'is_stop_processing',
            'value' => $spendingRule->getIsStopProcessing(),
            'values' => [0 => __('No'), 1 => __('Yes')],
        ]);
        $fieldset->addField('sort_order', 'text', [
            'label' => __('Priority'),
            'name' => 'sort_order',
            'value' => $spendingRule->getSortOrder(),
            'note' => __('Arranged in the ascending order. 0 is the highest.'),
        ]);

        return parent::_prepareForm();
    }

    /************************/
}

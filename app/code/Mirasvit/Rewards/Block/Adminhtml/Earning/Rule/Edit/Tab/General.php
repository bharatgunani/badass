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

class General extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @var \Mirasvit\Rewards\Model\Config\Source\Type
     */
    protected $configSourceType;

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
     * @param \Mirasvit\Rewards\Model\Config\Source\Type                    $configSourceType
     * @param \Magento\Config\Model\Config\Source\Website                   $systemConfigSourceWebsite
     * @param \Magento\Framework\Data\FormFactory                           $formFactory
     * @param \Magento\Framework\Registry                                   $registry
     * @param \Magento\Backend\Block\Widget\Context                         $context
     * @param array                                                         $data
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory,
        \Mirasvit\Rewards\Model\Config\Source\Type $configSourceType,
        \Magento\Config\Model\Config\Source\Website $systemConfigSourceWebsite,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->configSourceType = $configSourceType;
        $this->systemConfigSourceWebsite = $systemConfigSourceWebsite;
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create();
        $this->setForm($form);
        /** @var \Mirasvit\Rewards\Model\Earning\Rule $earningRule */
        $earningRule = $this->registry->registry('current_earning_rule');

        $fieldset = $form->addFieldset('edit_fieldset', ['legend' => __('General Information')]);
        if ($earningRule->getId()) {
            $fieldset->addField('earning_rule_id', 'hidden', [
                'name'  => 'earning_rule_id',
                'value' => $earningRule->getId(),
            ]);
        }
        $fieldset->addField('store_id', 'hidden', [
            'name'  => 'store_id',
            'value' => (int)$this->getRequest()->getParam('store'),
        ]);
        $fieldset->addField('name', 'text', [
            'label'    => __('Rule Name'),
            'required' => true,
            'name'     => 'name',
            'value'    => $earningRule->getName(),
        ]);
        $fieldset->addField('type', 'select', [
            'label'    => __('Type'),
            'required' => true,
            'name'     => 'type',
            'value'    => $earningRule->getType(),
            'values'   => $this->configSourceType->toOptionArray(),
            'disabled' => $earningRule->getType() != false,
        ]);
        $fieldset->addField('is_active', 'select', [
            'label'    => __('Is Active'),
            'required' => true,
            'name'     => 'is_active',
            'value'    => $earningRule->getIsActive(),
            'values'   => [0 => __('No'), 1 => __('Yes')],
        ]);
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $fieldset->addField(
            'active_from',
            'date',
            [
                'label'       => __('Active From'),
                'name'        => 'active_from',
                'date_format' => $dateFormat,
                'value'       => $earningRule->getActiveFrom(),
            ]
        );
        $fieldset->addField(
            'active_to',
            'date',
            [
                'label'       => __('Active To'),
                'name'        => 'active_to',
                'date_format' => $dateFormat,
                'value'       => $earningRule->getActiveTo(),
            ]
        );
        $websites = $this->systemConfigSourceWebsite->toOptionArray();
        if (!$this->context->getStoreManager()->isSingleStoreMode() && count($websites) > 1) {
            $fieldset->addField('website_ids', 'multiselect', [
                'name'     => 'website_ids[]',
                'label'    => __('Websites'),
                'title'    => __('Websites'),
                'required' => true,
                'values'   => $websites,
                'value'    => $earningRule->getWebsiteIds(),
            ]);
        } else {
            $fieldset->addField('website_ids', 'hidden', [
                'name'  => 'website_ids',
                'value' => $this->context->getStoreManager()->getStore(true)->getWebsiteId(),
            ]);
            $earningRule->setWebsiteId($this->context->getStoreManager()->getStore(true)->getWebsiteId());
        }
        $fieldset->addField('customer_group_ids', 'multiselect', [
            'label'    => __('Customer Groups'),
            'required' => true,
            'name'     => 'customer_group_ids[]',
            'value'    => $earningRule->getId() ? $earningRule->getCustomerGroupIds() : null,
            'values'   => $this->groupCollectionFactory->create()->toOptionArray(),
        ]);
        $fieldset->addField('is_stop_processing', 'select', [
            'label'  => __('Stop further rules processing'),
            'name'   => 'is_stop_processing',
            'value'  => $earningRule->getIsStopProcessing(),
            'values' => [0 => __('No'), 1 => __('Yes')],
        ]);
        $fieldset->addField('sort_order', 'text', [
            'label' => __('Priority'),
            'name'  => 'sort_order',
            'value' => (int)$earningRule->getSortOrder(),
            'note'  => __('Arranged in the ascending order. 0 is the highest.'),
        ]);

        return parent::_prepareForm();
    }

    /************************/
}

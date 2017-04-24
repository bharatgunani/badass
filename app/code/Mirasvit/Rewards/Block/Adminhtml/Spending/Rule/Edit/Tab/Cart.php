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

class Cart extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    protected $widgetFormRendererFieldset;

    /**
     * @var \Magento\Rule\Block\Actions
     */
    protected $actions;

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
     * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $widgetFormRendererFieldset
     * @param \Magento\Rule\Block\Actions                          $actions
     * @param \Magento\Framework\Data\FormFactory                  $formFactory
     * @param \Magento\Framework\Registry                          $registry
     * @param \Magento\Backend\Block\Widget\Context                $context
     * @param array                                                $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $widgetFormRendererFieldset,
        \Magento\Rule\Block\Actions $actions,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->widgetFormRendererFieldset = $widgetFormRendererFieldset;
        $this->actions = $actions;
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

        $fieldset = $form->addFieldset('action_fieldset', ['legend' => __('Actions')]);

        $fieldset->addField('spend_points', 'text', [
            'label' => __('For each spent X points'),
            'required' => true,
            'name' => 'spend_points',
            'value' => $spendingRule->getSpendPoints(),
            'note' => 'number of points.',
        ]);

        $fieldset->addField('monetary_step', 'text', [
            'label' => __('Customer receive Y discount'),
            'required' => true,
            'name' => 'monetary_step',
            'value' => $spendingRule->getMonetaryStep(),
            'note' => 'in base currency.',
        ]);

        $fieldset->addField('spend_min_points', 'text', [
            'label' => __('Spend minimum'),
            'name' => 'spend_min_points',
            'value' => $spendingRule->getSpendMinPoints(),
            'note' => 'You can enter amount of points or percent. e.g. 100 or 5%. Leave empty to disable.',
        ]);

        $fieldset->addField('spend_max_points', 'text', [
            'label' => __('Spend maximum'),
            'name' => 'spend_max_points',
            'value' => $spendingRule->getSpendMaxPoints(),
            'note' => 'You can enter amount of points or percent. e.g. 100 or 5%. Leave empty to disable.',
        ]);

        //Apply the rule only to cart items matching the following conditions (leave blank for all items)
        $renderer = $this->widgetFormRendererFieldset
            ->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('sales_rule/promo_quote/newActionHtml/form/rule_actions_fieldset'));

        $fieldset = $form->addFieldset('rule_actions_fieldset', [
            'legend' => __(
                'Apply the rule only to cart items matching the following conditions (leave blank for all items)'
            ),
        ])->setRenderer($renderer);

        $fieldset->addField('actions', 'text', [
            'name' => 'actions',
            'label' => __('Apply To'),
            'title' => __('Apply To'),
            'required' => true,
        ])->setRule($spendingRule)->setRenderer($this->actions);

        return parent::_prepareForm();
    }

    /************************/
}

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

use Mirasvit\Rewards\Model\Config as Config;

class Behavior extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var \Mirasvit\Rewards\Model\Config\Source\Behavior\Trigger
     */
    protected $configSourceBehaviorTrigger;

    /**
     * @var \Mirasvit\Rewards\Model\System\Source\Cartearningstyle
     */
    protected $systemSourceCartearningstyle;

    /**
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    protected $widgetFormRendererFieldset;

    /**
     * @var \Magento\Rule\Block\Conditions
     */
    protected $conditions;

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
     * @param \Mirasvit\Rewards\Model\Config\Source\Behavior\Trigger $configSourceBehaviorTrigger
     * @param \Mirasvit\Rewards\Model\System\Source\Cartearningstyle $systemSourceCartearningstyle
     * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset   $widgetFormRendererFieldset
     * @param \Magento\Rule\Block\Conditions                         $conditions
     * @param \Magento\Framework\Data\FormFactory                    $formFactory
     * @param \Magento\Framework\Registry                            $registry
     * @param \Magento\Backend\Block\Widget\Context                  $context
     * @param array                                                  $data
     */
    public function __construct(
        \Mirasvit\Rewards\Model\Config\Source\Behavior\Trigger $configSourceBehaviorTrigger,
        \Mirasvit\Rewards\Model\System\Source\Cartearningstyle $systemSourceCartearningstyle,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $widgetFormRendererFieldset,
        \Magento\Rule\Block\Conditions $conditions,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->configSourceBehaviorTrigger = $configSourceBehaviorTrigger;
        $this->systemSourceCartearningstyle = $systemSourceCartearningstyle;
        $this->widgetFormRendererFieldset = $widgetFormRendererFieldset;
        $this->conditions = $conditions;
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

        $fieldset = $form->addFieldset('rule_conditions_fieldset', ['legend' => __('Conditions')]);
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

        $fieldset->addField('behavior_trigger', 'select', [
            'label' => __('Event'),
            'required' => true,
            'name' => 'behavior_trigger',
            'value' => $earningRule->getBehaviorTrigger(),
            'values' => $this->configSourceBehaviorTrigger->toOptionArray(),
        ]);
        $fieldset->addField('inactivity_period', 'text', [
            'label' => __('Number of Inactive Days'),
            'required' => true,
            'name' => 'param1',
            'value' => $earningRule->getDataByKey('param1'),
        ]);
        $fieldset = $form->addFieldset('action_fieldset', ['legend' => __('Actions')]);

        $fieldset->addField('earning_style', 'select', [
            'label' => __('Customer Earning Style'),
            'required' => true,
            'name' => 'earning_style',
            'value' => $earningRule->getEarningStyle(),
            'values' => $this->systemSourceCartearningstyle->toArray(),
        ]);

        $fieldset->addField('earn_points', 'text', [
            'label' => __('Number of points to give (X)'),
            'required' => true,
            'name' => 'earn_points',
            'value' => $earningRule->getEarnPoints(),
        ]);

        $fieldset->addField('monetary_step', 'text', [
            'label' => __('Step (Y)'),
            'required' => true,
            'name' => 'monetary_step',
            'value' => $earningRule->getMonetaryStep(),
            'note' => 'in base currency',
        ]);

        $fieldset->addField('qty_step', 'text', [
            'label' => __('Quantity Step (Z)'),
            'required' => true,
            'name' => 'qty_step',
            'value' => $earningRule->getQtyStep(),
        ]);

        $fieldset->addField('points_limit', 'text', [
            'label' => __('Maximum number of earned points for one customer per day'),
            'name' => 'points_limit',
            'value' => $earningRule->getPointsLimit(),
            'note' => __('Set 0 to disable limit'),
        ]);

        $renderer = $this->widgetFormRendererFieldset
            ->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/*/newConditionHtml/form/rule_conditions_fieldset'));

        $fieldset = $form->addFieldset('conditions_fieldset', [
            'legend' => __('Apply the rule only if the following conditions are met'),
        ])->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', [
            'name' => 'conditions',
            'label' => __('Conditions'),
            'title' => __('Conditions'),
        ])->setRule($earningRule)->setRenderer($this->conditions);

        return parent::_prepareForm();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _toHtml()
    {
        $html = parent::_toHtml();
        $block = $this->getLayout()->createBlock('\Magento\Backend\Block\Widget\Form\Element\Dependence')
            ->addFieldMap('behavior_trigger', 'behavior_trigger')
            ->addFieldMap('inactivity_period', 'inactivity_period')
            ->addFieldMap('earning_style', 'earning_style')
            ->addFieldDependence('inactivity_period', 'behavior_trigger', Config::BEHAVIOR_TRIGGER_INACTIVITY)
            ->addFieldDependence('earning_style', 'behavior_trigger', Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_ORDER)
            ;
        $html .= $block->toHtml();

        $block = $this->getLayout()->createBlock('\Magento\Backend\Block\Widget\Form\Element\Dependence')
            ->addFieldMap('earning_style', 'earning_style')
            ->addFieldMap('monetary_step', 'monetary_step')
            ->addFieldMap('qty_step', 'qty_step')
            ->addFieldMap('behavior_trigger', 'behavior_trigger')
            ->addFieldMap('points_limit', 'points_limit')
            ->addFieldDependence('monetary_step', 'behavior_trigger', Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_ORDER)
            ->addFieldDependence('qty_step', 'behavior_trigger', Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_ORDER)
            ->addFieldDependence('monetary_step', 'earning_style', Config::EARNING_STYLE_AMOUNT_SPENT)
            ->addFieldDependence('qty_step', 'earning_style', Config::EARNING_STYLE_QTY_SPENT)
            ;
        $html .= $block->toHtml();

        return $html;
    }
}

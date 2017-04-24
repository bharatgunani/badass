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

class Cart extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var \Mirasvit\Rewards\Model\System\Source\Cartearningstyle
     */
    protected $systemSourceCartearningstyle;

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
     * @param \Mirasvit\Rewards\Model\System\Source\Cartearningstyle $systemSourceCartearningstyle
     * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset   $widgetFormRendererFieldset
     * @param \Magento\Rule\Block\Actions                            $actions
     * @param \Magento\Framework\Data\FormFactory                    $formFactory
     * @param \Magento\Framework\Registry                            $registry
     * @param \Magento\Backend\Block\Widget\Context                  $context
     * @param array                                                  $data
     */
    public function __construct(
        \Mirasvit\Rewards\Model\System\Source\Cartearningstyle $systemSourceCartearningstyle,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $widgetFormRendererFieldset,
        \Magento\Rule\Block\Actions $actions,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->systemSourceCartearningstyle = $systemSourceCartearningstyle;
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
        /** @var \Mirasvit\Rewards\Model\Earning\Rule $earningRule */
        $earningRule = $this->registry->registry('current_earning_rule');

        $fieldset = $form->addFieldset('action_fieldset', ['legend' => __('Actions')]);
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

        $fieldset->addField('earning_style', 'select', [
            'label' => __('Customer Earning Style'),
            'required' => true,
            'name' => 'earning_style',
            'value' => $earningRule->getEarningStyle(),
            'values' => $this->systemSourceCartearningstyle->toArray(),
        ]);

        $fieldset->addField('earn_points', 'text', [
            'label' => __('Number of Points (X)'),
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
            'label' => __('Earn Maximum'),
            'name' => 'points_limit',
            'value' => $earningRule->getPointsLimit() == 0 ? '' : $earningRule->getPointsLimit(),
            'note' => 'You can enter amount of points or percent. Leave empty to disable.',
        ]);
        $this->setChild('form_after',
            $this->getLayout()->createBlock('\Magento\Backend\Block\Widget\Form\Element\Dependence')
            ->addFieldMap('earning_style', 'earning_style')
            ->addFieldMap('monetary_step', 'monetary_step')
            ->addFieldMap('qty_step', 'qty_step')
            ->addFieldMap('points_limit', 'points_limit')
            ->addFieldDependence('monetary_step', 'earning_style', Config::EARNING_STYLE_AMOUNT_SPENT)
            ->addFieldDependence('qty_step', 'earning_style', Config::EARNING_STYLE_QTY_SPENT)
        );

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
        ])->setRule($earningRule)->setRenderer($this->actions);

        return parent::_prepareForm();
    }

    /************************/
}

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

class Product extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var \Mirasvit\Rewards\Model\System\Source\Productearningstyle
     */
    protected $systemSourceProductearningstyle;

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
     * @var \Magento\Backend\Model\Url
     */
    protected $backendUrlManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Rewards\Model\System\Source\Productearningstyle $systemSourceProductearningstyle
     * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset      $widgetFormRendererFieldset
     * @param \Magento\Rule\Block\Conditions                            $conditions
     * @param \Magento\Framework\Data\FormFactory                       $formFactory
     * @param \Magento\Backend\Model\Url                                $backendUrlManager
     * @param \Magento\Framework\Registry                               $registry
     * @param \Magento\Backend\Block\Widget\Context                     $context
     * @param array                                                     $data
     */
    public function __construct(
        \Mirasvit\Rewards\Model\System\Source\Productearningstyle $systemSourceProductearningstyle,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $widgetFormRendererFieldset,
        \Magento\Rule\Block\Conditions $conditions,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Backend\Model\Url $backendUrlManager,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->systemSourceProductearningstyle = $systemSourceProductearningstyle;
        $this->widgetFormRendererFieldset = $widgetFormRendererFieldset;
        $this->conditions = $conditions;
        $this->formFactory = $formFactory;
        $this->backendUrlManager = $backendUrlManager;
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

        $renderer = $this->widgetFormRendererFieldset
            ->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
            ->setNewChildUrl($this->backendUrlManager->getUrl(
                    '*/earning_rule/newConditionHtml/form/rule_conditions_fieldset',
                    ['rule_type' => $earningRule->getType()]
                )
            );
        $fieldset->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', [
            'name' => 'conditions',
            'label' => __('Filters'),
            'title' => __('Filters'),
            'required' => true,
        ])->setRule($earningRule)
            ->setRenderer($this->conditions);

        $fieldset = $form->addFieldset('action_fieldset', ['legend' => __('Actions')]);

        $fieldset->addField('earning_style', 'select', [
            'label' => __('Customer Earning Style'),
            'required' => true,
            'name' => 'earning_style',
            'value' => $earningRule->getEarningStyle(),
            'values' => $this->systemSourceProductearningstyle->toArray(),
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
        $fieldset->addField('points_limit', 'text', [
            'label' => __('Maximum Distributed Points'),
            'name' => 'points_limit',
            'value' => $earningRule->getPointsLimit(),
        ]);

        $this->setChild('form_after',
            $this->getLayout()->createBlock('\Magento\Backend\Block\Widget\Form\Element\Dependence')
            ->addFieldMap('earning_style', 'earning_style')
            ->addFieldMap('monetary_step', 'monetary_step')
            ->addFieldMap('points_limit', 'points_limit')
            ->addFieldDependence('monetary_step', 'earning_style', Config::EARNING_STYLE_AMOUNT_PRICE)
            ->addFieldDependence('points_limit', 'earning_style', Config::EARNING_STYLE_AMOUNT_PRICE)
        );

        return parent::_prepareForm();
    }

    /************************/
}

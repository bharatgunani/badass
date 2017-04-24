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



namespace Mirasvit\Rewards\Block\Adminhtml\Notification\Rule;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Mirasvit\Rewards\Model\Notification\RuleFactory
     */
    protected $notificationRuleFactory;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    /**
     * @var \Magento\Config\Model\Config\Source\Website
     */
    protected $websiteSource;

    /**
     * @param \Mirasvit\Rewards\Model\Notification\RuleFactory $notificationRuleFactory
     * @param \Magento\Config\Model\Config\Source\Website      $websiteSource
     * @param \Magento\Backend\Block\Widget\Context            $context
     * @param \Magento\Backend\Helper\Data                     $backendHelper
     * @param array                                            $data
     */
    public function __construct(
        \Mirasvit\Rewards\Model\Notification\RuleFactory $notificationRuleFactory,
        \Magento\Config\Model\Config\Source\Website $websiteSource,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        $this->notificationRuleFactory = $notificationRuleFactory;
        $this->websiteSource = $websiteSource;
        $this->context = $context;
        $this->backendHelper = $backendHelper;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('grid');
        $this->setDefaultSort('notification_rule_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->notificationRuleFactory->create()
            ->getCollection()
            ->addWebsiteColumn();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('notification_rule_id', [
                'header'       => __('ID'),
                'index'        => 'notification_rule_id',
                'filter_index' => 'main_table.notification_rule_id',
            ]
        );
        $this->addColumn('name', [
                'header'       => __('Rule Name'),
                'index'        => 'name',
                'filter_index' => 'main_table.name',
            ]
        );

        $websites = [0 => __('All')];
        foreach ($this->websiteSource->toOptionArray() as $item) {
            $websites[$item['value']] = $item['label'];
        }

        if (!$this->context->getStoreManager()->isSingleStoreMode() && count($websites) > 1) {
            $this->addColumn('website_ids', [
                'header'                    => __('Website'),
                'index'                     => 'website_ids',
                'type'                      => 'options',
                'options'                   => $websites,
                'sortable'                  => false,
                'filter_condition_callback' => [$this, 'websiteFilter'],
            ]);
        }

        $this->addColumn('is_active', [
                'header'       => __('Is Active'),
                'index'        => 'is_active',
                'filter_index' => 'main_table.is_active',
                'type'         => 'options',
                'options'      => [
                    0 => __('No'),
                    1 => __('Yes'),
                ],
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('notification_rule_id');
        $this->getMassactionBlock()->setFormFieldName('notification_rule_id');
        $statuses = [
            ['label' => '', 'value' => ''],
            ['label' => __('Disabled'), 'value' => 0],
            ['label' => __('Enabled'), 'value' => 1],
        ];
        $this->getMassactionBlock()->addItem('is_active', [
            'label'      => __('Change status'),
            'url'        => $this->getUrl('*/*/massChange', ['_current' => true]),
            'additional' => [
                'visibility' => [
                    'name'   => 'is_active',
                    'type'   => 'select',
                    'class'  => 'required-entry',
                    'label'  => __('Status'),
                    'values' => $statuses,
                ],
            ],
        ]);
        $this->getMassactionBlock()->addItem('delete', [
            'label'   => __('Delete'),
            'url'     => $this->getUrl('*/*/massDelete'),
            'confirm' => __('Are you sure?'),
        ]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareGrid()
    {
        parent::_prepareGrid();

        foreach ($this->getCollection() as $item) {
            $item->setData('website_ids', explode(',', $item->getData('website_ids')));
        }

        return $this;
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }

    /**
     * @param \Mirasvit\Rewards\Model\ResourceModel\Notification\Rule\Collection $collection
     * @param \Magento\Backend\Block\Widget\Grid\Column\Extended                 $column
     * @return $this
     */
    protected function websiteFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $collection->addWebsiteFilter($value);

        return $this;
    }
}

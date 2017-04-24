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



namespace Mirasvit\Rewards\Block\Adminhtml\Referral;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Mirasvit\Rewards\Model\ReferralFactory
     */
    protected $referralFactory;

    /**
     * @var \Mirasvit\Rewards\Model\Config\Source\Referral\Status
     */
    protected $configSourceReferralStatus;

    /**
     * @var \Mirasvit\Rewards\Helper\Mage
     */
    protected $rewardsMage;

    /**
     * @var \Mirasvit\Rewards\Helper\Data
     */
    protected $rewardsData;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    /**
     * @param \Mirasvit\Rewards\Model\ReferralFactory               $referralFactory
     * @param \Mirasvit\Rewards\Model\Config\Source\Referral\Status $configSourceReferralStatus
     * @param \Mirasvit\Rewards\Helper\Mage                         $rewardsMage
     * @param \Mirasvit\Rewards\Helper\Data                         $rewardsData
     * @param \Magento\Backend\Block\Widget\Context                 $context
     * @param \Magento\Backend\Helper\Data                          $backendHelper
     * @param array                                                 $data
     */
    public function __construct(
        \Mirasvit\Rewards\Model\ReferralFactory $referralFactory,
        \Mirasvit\Rewards\Model\Config\Source\Referral\Status $configSourceReferralStatus,
        \Mirasvit\Rewards\Helper\Mage $rewardsMage,
        \Mirasvit\Rewards\Helper\Data $rewardsData,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        $this->referralFactory = $referralFactory;
        $this->configSourceReferralStatus = $configSourceReferralStatus;
        $this->rewardsMage = $rewardsMage;
        $this->rewardsData = $rewardsData;
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
        $this->setDefaultSort('referral_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->referralFactory->create()
            ->getCollection()
            ->addNameToSelect()
        ;
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('referral_id', [
            'header' => __('ID'),
            'index' => 'referral_id',
            'filter_index' => 'main_table.referral_id',
            ]
        );
        $this->addColumn('customer', [
            'header' => __('Affiliate Customer'),
            'index' => 'customer_name',
            ]
        );
        $this->addColumn('new_customer', [
            'header' => __('Referral Customer'),
            'index' => 'new_customer_name',
            ]
        );
        $this->addColumn('email', [
            'header' => __('Referral Email'),
            'index' => 'email',
            'filter_index' => 'main_table.email',
            'frame_callback' => [$this, '_renderOfEmptyFields'],
            ]
        );
        $this->addColumn('name', [
            'header' => __('Referral Name'),
            'index' => 'name',
            'filter_index' => 'main_table.name',
            'frame_callback' => [$this, '_renderOfEmptyFields'],
            ]
        );
        $this->addColumn('status', [
            'header' => __('Status'),
            'index' => 'status',
            'filter_index' => 'main_table.status',
            'type' => 'options',
            'options' => $this->configSourceReferralStatus->toArray(),
            ]
        );
        $this->addColumn('created_at', [
            'header' => __('Created At'),
            'index' => 'created_at',
            'filter_index' => 'main_table.created_at',
            'type' => 'date',
            ]
        );
        $this->addColumn('store_id', [
            'header' => __('Store'),
            'index' => 'store_id',
            'filter_index' => 'main_table.store_id',
            'type' => 'options',
            'options' => $this->rewardsData->getCoreStoreOptionArray(),
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @param string                        $renderedValue
     * @param array                         $row
     * @param \Magento\Framework\DataObject $column
     * @param bool                          $isExport
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function _customerFormat($renderedValue, $row, $column, $isExport)
    {
        $url = $this->rewardsMage->getBackendCustomerUrl($row['customer_id']);

        return "<a href='{$url}'>{$row['customer_name']}</a>";
    }

    /**
     * @param string                        $renderedValue
     * @param array                         $row
     * @param \Magento\Framework\DataObject $column
     * @param bool                          $isExport
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function _newCustomerFormat($renderedValue, $row, $column, $isExport)
    {
        if ((int) $row['new_customer_id'] == 0) {
            return '';
        }
        $url = $this->rewardsMage->getBackendCustomerUrl($row['new_customer_id']);

        return "<a href='{$url}'>{$row['new_customer_name']}</a>";
    }

    /**
     * @param string                        $renderedValue
     * @param array                         $row
     * @param \Magento\Framework\DataObject $column
     * @param bool                          $isExport
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function _renderOfEmptyFields($renderedValue, $row, $column, $isExport)
    {
        if ($renderedValue == '') {
            return '-';
        }

        return $renderedValue;
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('referral_id');
        $this->getMassactionBlock()->setFormFieldName('referral_id');
        $this->getMassactionBlock()->addItem('delete', [
            'label' => __('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => __('Are you sure?'),
        ]);

        return $this;
    }

    /************************/
}

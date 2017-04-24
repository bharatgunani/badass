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



namespace Mirasvit\Rewards\Block\Adminhtml\Transaction\Edit\Customer;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    /**
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory    $groupCollectionFactory
     * @param \Magento\Store\Model\System\Store                                $systemStore
     * @param \Magento\Backend\Block\Widget\Context                            $context
     * @param \Magento\Backend\Helper\Data                                     $backendHelper
     * @param array                                                            $data
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->systemStore = $systemStore;
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
        $this->setId('customer_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareCollection()
    {
        $collection = $this->customerCollectionFactory->create()
            ->addNameToSelect()
            ->addAttributeToSelect('group_id')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('email')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_transaction_users', [
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'in_transaction_users',
            'align' => 'center',
            'index' => 'entity_id',
        ]);

        $this->addColumn('entity_id', [
            'header' => __('ID'),
            'width' => '50px',
            'index' => 'entity_id',
            'type' => 'number',
        ]);
        $this->addColumn('name', [
            'header' => __('Name'),
            'index' => 'name',
        ]);
        $this->addColumn('email', [
            'header' => __('Email'),
            'width' => '150',
            'index' => 'email',
        ]);

        $groups = $this->groupCollectionFactory->create()
            ->addFieldToFilter('customer_group_id', ['gt' => 0])
            ->load()
            ->toOptionHash();

        $this->addColumn('group', [
            'header' => __('Group'),
            'width' => '100',
            'index' => 'group_id',
            'type' => 'options',
            'options' => $groups,
        ]);

        $this->addColumn('Telephone', [
            'header' => __('Telephone'),
            'width' => '100',
            'index' => 'billing_telephone',
        ]);

        $this->addColumn('billing_postcode', [
            'header' => __('ZIP'),
            'width' => '90',
            'index' => 'billing_postcode',
        ]);

        $this->addColumn('billing_country_id', [
            'header' => __('Country'),
            'width' => '100',
            'type' => 'country',
            'index' => 'billing_country_id',
        ]);

        $this->addColumn('billing_region', [
            'header' => __('State/Province'),
            'width' => '100',
            'index' => 'billing_region',
        ]);

        $this->addColumn('customer_since', [
            'header' => __('Customer Since'),
            'type' => 'datetime',
            'align' => 'center',
            'index' => 'created_at',
            'gmtoffset' => true,
        ]);

        if (!$this->context->getStoreManager()->isSingleStoreMode()) {
            $this->addColumn('website_id', [
                'header' => __('Website'),
                'align' => 'center',
                'width' => '80px',
                'type' => 'options',
                'options' => $this->systemStore->getWebsiteOptionHash(true),
                'index' => 'website_id',
            ]);
        }

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
}

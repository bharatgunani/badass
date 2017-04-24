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



namespace Mirasvit\Rewards\Model\Earning;

/**
 * @method \Mirasvit\Rewards\Model\ResourceModel\Earning\Rule\Collection getCollection()
 * @method \Mirasvit\Rewards\Model\Earning\Rule load(int $id)
 * @method bool getIsMassDelete()
 * @method \Mirasvit\Rewards\Model\Earning\Rule setIsMassDelete(bool $flag)
 * @method bool getIsMassStatus()
 * @method \Mirasvit\Rewards\Model\Earning\Rule setIsMassStatus(bool $flag)
 * @method string getProductNotification()
 * @method \Mirasvit\Rewards\Model\Earning\Rule setProductNotification(string $text)
 * @method \Mirasvit\Rewards\Model\ResourceModel\Earning\Rule getResource()
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Rule extends \Magento\SalesRule\Model\Rule
{
    const TYPE_PRODUCT = 'product';
    const TYPE_CART = 'cart';
    const TYPE_BEHAVIOR = 'behavior';

    const CACHE_TAG = 'rewards_earning_rule';
    /**
     * @var string
     */
    protected $_cacheTag = 'rewards_earning_rule';
    /**
     * @var string
     */
    protected $_eventPrefix = 'rewards_earning_rule';

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * @var \Mirasvit\Rewards\Model\Earning\Rule\Condition\CombineFactory
     */
    protected $earningRuleConditionCombineFactory;

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory
     */
    protected $ruleConditionProductCombineFactory;

    /**
     * @var \Mirasvit\Rewards\Model\Earning\Rule\Action\CollectionFactory
     */
    protected $earningRuleActionCollectionFactory;

    /**
     * @var \Mirasvit\Rewards\Helper\Storeview
     */
    protected $rewardsStoreview;

    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Model\ResourceModel\AbstractResource
     */
    protected $resource;

    /**
     * @var \Magento\Framework\Data\Collection\AbstractDb
     */
    protected $resourceCollection;

    /**
     * @param Rule\Condition\CombineFactory                                  $earningRuleConditionCombineFactory
     * @param \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $ruleConditionProductCombineFactory
     * @param Rule\Action\CollectionFactory                                  $earningRuleActionCollectionFactory
     * @param \Mirasvit\Rewards\Helper\Storeview                             $rewardsStoreview
     * @param \Magento\Framework\Model\Context                               $context
     * @param \Magento\Framework\Registry                                    $registry
     * @param \Magento\Framework\Data\FormFactory                            $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface           $localeDate
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null   $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null             $resourceCollection
     * @param array                                                          $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Rule\Condition\CombineFactory $earningRuleConditionCombineFactory,
        \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $ruleConditionProductCombineFactory,
        Rule\Action\CollectionFactory $earningRuleActionCollectionFactory,
        \Mirasvit\Rewards\Helper\Storeview $rewardsStoreview,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Magento\SalesRule\Model\Coupon\CodegeneratorFactory $codegenFactory,
        \Magento\SalesRule\Model\Rule\Condition\CombineFactory $condCombineFactory,
        \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $condProdCombineF,
        \Magento\SalesRule\Model\ResourceModel\Coupon\Collection $couponCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->earningRuleConditionCombineFactory = $earningRuleConditionCombineFactory;
        $this->ruleConditionProductCombineFactory = $ruleConditionProductCombineFactory;
        $this->earningRuleActionCollectionFactory = $earningRuleActionCollectionFactory;
        $this->rewardsStoreview = $rewardsStoreview;
        $this->context = $context;
        $this->registry = $registry;
        $this->resource = $resource;
        $this->resourceCollection = $resourceCollection;
        parent::__construct($context, $registry, $formFactory, $localeDate, $couponFactory, $codegenFactory,
            $condCombineFactory, $condProdCombineF, $couponCollection, $storeManager, $resource, $resourceCollection,
            $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Rewards\Model\ResourceModel\Earning\Rule');
    }

    /**
     * @param bool|false $emptyOption
     * @return array
     */
    public function toOptionArray($emptyOption = false)
    {
        return $this->getCollection()->toOptionArray($emptyOption);
    }

    /**
     * @return Rule\Condition\Combine
     */
    public function getConditionsInstance()
    {
        $combine = $this->earningRuleConditionCombineFactory->create();
        return $combine;
    }

    /**
     * @return \Magento\SalesRule\Model\Rule\Condition\Product\Combine
     */
    public function getActionsInstance()
    {
        return $this->ruleConditionProductCombineFactory->create();
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductIds()
    {
        return $this->_getResource()->getRuleProductIds($this->getId());
    }

    /**
     * @param string $format
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toString($format = '')
    {
        $this->load($this->getId());
        $string = $this->getConditions()->asStringRecursive();

        $string = nl2br(preg_replace('/ /', '&nbsp;', $string));

        return $string;
    }

    /**
     * @return string
     */
    public function getEmailMessage()
    {
        return $this->rewardsStoreview->getStoreViewValue($this, 'email_message');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setEmailMessage($value)
    {
        $this->rewardsStoreview->setStoreViewValue($this, 'email_message', $value);

        return $this;
    }

    /**
     * @return string
     */
    public function getHistoryMessage()
    {
        return $this->rewardsStoreview->getStoreViewValue($this, 'history_message');
    }

    /**
     * @param int|string $value
     * @return $this
     */
    public function setHistoryMessage($value)
    {
        $this->rewardsStoreview->setStoreViewValue($this, 'history_message', $value);

        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function addData(array $data)
    {
        if (isset($data['email_message']) && strpos($data['email_message'], 'a:') !== 0) {
            $this->setEmailMessage($data['email_message']);
            unset($data['email_message']);
        }

        if (isset($data['history_message']) && strpos($data['history_message'], 'a:') !== 0) {
            $this->setHistoryMessage($data['history_message']);
            unset($data['history_message']);
        }

        return parent::addData($data);
    }
    /************************/

    /**
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function applyAll()
    {
        $this->_getResource()->applyAllRulesForDateRange();
    }

    /**
     * @return array
     */
    public function getWebsiteIds()
    {
        return $this->getData('website_ids');
    }
}

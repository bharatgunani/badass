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



namespace Mirasvit\Rewards\Block\Product;

class Points extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'Mirasvit_Rewards::product/points.phtml';

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    public function __construct(
        \Mirasvit\Rewards\Model\Config $config,
        \Mirasvit\Rewards\Helper\Balance\Earn $earnHelper,
        \Mirasvit\Rewards\Helper\Balance\Spend $spendHelper,
        \Mirasvit\Rewards\Helper\Data $rewardsDataHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->config            = $config;
        $this->earnHelper        = $earnHelper;
        $this->spendHelper       = $spendHelper;
        $this->rewardsDataHelper = $rewardsDataHelper;
        $this->registry          = $registry;
        $this->customerSession   = $customerSession;
    }

    /**
     * @return bool
     */
    public function isShowPoints()
    {
        return $this->config->getDisplayOptionsIsShowPointsOnProductPage();
    }

    /**
     * @return int
     */
    public function getPoints()
    {
        if (!$this->getProduct()) {
            return 0;
        }

        /** @var \Magento\Catalog\Model\Product\Interceptor $productPoints */
        $productPoints = $this->earnHelper->getProductPoints(
            $this->getProduct(),
            $this->customerSession->getCustomerGroupId(),
            $this->_storeManager->getStore()->getWebsiteId()
        );

        return $productPoints;
    }

    /**
     * @return int
     */
    public function getPointsFormatted()
    {
        if (!$this->getProduct()) {
            return 0;
        }

        $websiteId       = $this->_storeManager->getStore()->getWebsiteId();
        $customerGroupId = $this->customerSession->getCustomer()->getGroupId();

        $points = $this->getPoints();
        if (!$points) {
            return 0;
        }
        $money  = $this->spendHelper->getProductPointsAsMoney($points, $websiteId, $customerGroupId);
        if ($points != $money) {
            return __('Possible discount '.$this->getLabel().' %1', $money);
        }

        return __('Earn '.$this->getLabel().' %1', $this->rewardsDataHelper->formatPoints($points));
    }

    /**
     * @return bool|true
     */
    public function getIsProductPointsPossible()
    {
        if (!$this->getProduct()) {
            return false;
        }
        return $this->earnHelper->getIsProductPointsPossible(
            $this->getProduct(),
            $this->customerSession->getCustomerGroupId(),
            $this->_storeManager->getStore()->getWebsiteId()
        );
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        $label = '';
        if (!$this->getProduct()) {
            return $label;
        }

        switch ($this->getProduct()->getTypeId()) {
            case \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE:
                $label = 'starting at';
                break;
            case \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE:
                $label = 'Up to';
                break;
        }

        return $label;
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (!$this->product) {
            return $this->registry->registry('product');
        } else {
            return $this->product;
        }
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return $this
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }
}

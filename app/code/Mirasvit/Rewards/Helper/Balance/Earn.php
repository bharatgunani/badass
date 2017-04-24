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



namespace Mirasvit\Rewards\Helper\Balance;

use Mirasvit\Rewards\Model\Config as Config;

class Earn extends \Magento\Framework\App\Helper\AbstractHelper
{
    const PRICE = 'price';
    const PRICE_WITH_TAX = 'tax_price';

    /**
     * @var array
     */
    private $productMessages = [];

    public function __construct(
        \Magento\Checkout\Model\CartFactory $cartFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Mirasvit\Rewards\Model\ResourceModel\Earning\Rule\CollectionFactory $earningRuleCollectionFactory,
        \Mirasvit\Rewards\Model\Config $config,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->cartFactory                  = $cartFactory;
        $this->productFactory               = $productFactory;
        $this->earningRuleCollectionFactory = $earningRuleCollectionFactory;
        $this->config                       = $config;
        $this->catalogData                  = $catalogData;
        $this->storeManager                 = $storeManager;
        $this->customerFactory              = $customerFactory;
        $this->customerSession              = $customerSession;
        $this->context                      = $context;

        parent::__construct($context);
    }

    /**
     * @return \Mirasvit\Rewards\Model\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return bool
     */
    public function isIncludeTax()
    {
        return $this->getConfig()->getGeneralIsIncludeTaxEarning();
    }

    /**
     * @param \Magento\Quote\Model\Quote           $quote
     * @param \Mirasvit\Rewards\Model\Earning\Rule $rule
     *
     * @return float
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function getLimitedSubtotal($quote, $rule)
    {
        $priceIncludesTax = $this->isIncludeTax($quote);

        $subtotal = 0;
        foreach ($quote->getItemsCollection() as $item) {
            /** @var \Magento\Quote\Model\Quote\Item $item */
            if ($item->getParentItemId()) {
                continue;
            }
            if ($rule->getActions()->validate($item)) {
                if ($priceIncludesTax) {
                    $subtotal += ($item->getBasePriceInclTax() + (float)$item->getWeeeTaxAppliedAmountInclTax()) *
                        $item->getQty() - $item->getBaseDiscountAmount();
                } else {
                    $subtotal += $item->getBasePrice() * $item->getQty() - $item->getBaseDiscountAmount();
                }
            }
        }

        if ($this->getConfig()->getGeneralIsEarnShipping()) {
            if ($priceIncludesTax) {
                $shipping = $quote->getShippingAddress()->getBaseShippingInclTax();
            } else {
                $shipping = $quote->getShippingAddress()->getBaseShippingInclTax() -
                    $quote->getShippingAddress()->getBaseShippingTaxAmount();
            }

            $subtotal += $shipping;
        }

        if ($this->context->getModuleManager()->isEnabled('Mirasvit_Credit')) {
            if ($credit = $quote->getShippingAddress()->getBaseCreditAmount()) {
                $subtotal -= $credit;
            }
        }

        if ($subtotal < 0) {
            $subtotal = 0;
        }

        return $subtotal;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return int number of points
     */
    public function getPointsEarned($quote)
    {
        $totalPoints = 0;
        foreach ($quote->getAllItems() as $item) {
            $productId = $item->getProductId();
            $product = $this->productFactory->create()->load($productId);

            if ($item->getParentItemId() && $product->getTypeID() == 'simple') {
                continue;
            }

            $productPoints = $this->getProductPoints(
                    $product,
                    $quote->getCustomerGroupId(),
                    $quote->getStore()->getWebsiteId(),
                    $item
                );

            $totalPoints += $productPoints;
        }

        $totalPoints += $this->getCartPoints($quote);

        return $totalPoints;
    }

    /**
     * Function returns true for grouped or bundled products if after adding to the cart
     * customer may receive product points
     *
     * @param \Magento\Catalog\Model\Product       $product
     * @param int|bool                             $customerGroupId
     * @param int|bool                             $websiteId
     * @return bool|true
     */
    public function getIsProductPointsPossible($product, $customerGroupId = false, $websiteId = false)
    {
        $possibleNotstandardProducts = [
            \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE,
            \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE,
        ];

        if (!in_array($product->getTypeId(), $possibleNotstandardProducts)) {
            return false;
        }

        if ($customerGroupId === false) {
            $customerGroupId = $this->customerFactory->create()
                ->load($this->customerSession->getCustomerId())
                ->getGroupId();
        }

        if ($websiteId === false) {
            $websiteId = $this->storeManager->getWebsite()->getId();
        }

        $rules = $this->earningRuleCollectionFactory->create()
            ->addWebsiteFilter($websiteId)
            ->addCustomerGroupFilter($customerGroupId)
            ->addCurrentFilter()
            ->addFieldToFilter('type', \Mirasvit\Rewards\Model\Earning\Rule::TYPE_PRODUCT)
            ->setOrder('sort_order')
        ;
        return $rules->count() > 0;
    }

    /**
     * Calculates the number of points for some product.
     *
     * @param \Magento\Catalog\Model\Product       $product
     * @param int|bool                             $customerGroupId
     * @param int|bool                             $websiteId
     * @param \Magento\Quote\Model\Quote\Item|bool $item
     * @param string|bool                          $price
     *
     * @return int number of points
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getProductPoints(
        $product,
        $customerGroupId = false,
        $websiteId = false,
        $item = false,
        $price = false
    ) {
        $product = $this->productFactory->create()->load($product->getId());
        if ($customerGroupId === false) {
            $customerGroupId = $this->customerFactory->create()
                ->load($this->customerSession->getCustomerId())
                ->getGroupId();
        }

        if ($websiteId === false) {
            $websiteId = $this->storeManager->getWebsite()->getId();
        }

        $rules = $this->earningRuleCollectionFactory->create()
            ->addWebsiteFilter($websiteId)
            ->addCustomerGroupFilter($customerGroupId)
            ->addCurrentFilter()
            ->addFieldToFilter('type', \Mirasvit\Rewards\Model\Earning\Rule::TYPE_PRODUCT)
            ->setOrder('sort_order')
        ;
        $total = 0;
        foreach ($rules as $rule) {
            $rule->afterLoad();
            if ($rule->validate($product)) {
                switch ($rule->getEarningStyle()) {
                    case Config::EARNING_STYLE_GIVE:
                        $total += $rule->getEarnPoints();
                        break;

                    case Config::EARNING_STYLE_AMOUNT_PRICE:
                        $rulePrice = $price ?: $this->getProductPrice($product, $item);
                        $rulePrice = $rulePrice / $rule->getMonetaryStep() * $rule->getEarnPoints();
                        $amount = round($rulePrice, 0, PHP_ROUND_HALF_DOWN);//don't use intvat due to php converting bug
                        if ($rule->getPointsLimit() && $amount > $rule->getPointsLimit()) {
                            $amount = $rule->getPointsLimit();
                        }
                        $total += $amount;
                        break;
                }
                $this->productMessages[$product->getId()][$rule->getId()] = $rule->getProductNotification();

                if ($rule->getIsStopProcessing()) {
                    break;
                }
            }
        }

        return $total;
    }

    /**
     * @param int $productId
     * @return array
     */
    public function getProductMessages($productId = 0)
    {
        if ($productId) {
            return isset($this->productMessages[$productId]) ? $this->productMessages[$productId] : [];
        }

        return $this->productMessages;
    }

    /**
     * @param \Magento\Catalog\Model\Product       $product
     * @param \Magento\Quote\Model\Quote\Item|bool $item
     *
     * @return string
     */
    private function getProductPrice($product, $item)
    {
        if ($item) {
            $price = $item->getBasePrice();
            $price = $price * $item->getQty();
            $price = $this->catalogData
                ->getTaxPrice($product, $price, $this->isIncludeTax(), null, null, null, null, false);
            if ($this->isIncludeTax()) {
                $wee   = (float)$item->getWeeeTaxAppliedAmountInclTax();
                $price = $price + ($wee * $item->getQty());
            }

        } else {
            $finalPrice = $product->getPriceInfo()->getPrice('final_price')->getMaximalPrice()->getValue();
            $price = $this->catalogData
                ->getTaxPrice($product, $finalPrice, null, null, null, null, null, !$this->isIncludeTax());
        }

        return $price;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return int number of points
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function getCartPoints($quote)
    {
        $total = 0;
        $customerGroupId = $quote->getCustomerGroupId();
        $websiteId = $quote->getStore()->getWebsiteId();
        $rules = $this->earningRuleCollectionFactory->create()
                    ->addWebsiteFilter($websiteId)
                    ->addCustomerGroupFilter($customerGroupId)
                    ->addCurrentFilter()
                    ->addFieldToFilter('type', \Mirasvit\Rewards\Model\Earning\Rule::TYPE_CART)
                    ->setOrder('sort_order')
                    ;
        /** @var \Mirasvit\Rewards\Model\Earning\Rule $rule */
        foreach ($rules as $rule) {
            $rule->afterLoad();
            if ($quote->getItemVirtualQty() > 0) {
                $address = $quote->getBillingAddress();
            } else {
                $address = $quote->getShippingAddress();
            }
            if ($rule->validate($address)) {
                switch ($rule->getEarningStyle()) {
                    case Config::EARNING_STYLE_GIVE:
                        $total += $rule->getEarnPoints();
                        break;

                    case Config::EARNING_STYLE_AMOUNT_SPENT:
                        $subtotal = $this->getLimitedSubtotal($quote, $rule);
                        $steps = round($subtotal / $rule->getMonetaryStep(), 0, PHP_ROUND_HALF_DOWN);
                        $amount = $steps * $rule->getEarnPoints();
                        if ($rule->getPointsLimit() && $amount > $rule->getPointsLimit()) {
                            $amount = $rule->getPointsLimit();
                        }
                        $total += $amount;
                        break;
                    case Config::EARNING_STYLE_QTY_SPENT:
                        $steps = round($quote->getItemsQty() / $rule->getQtyStep(), 0, PHP_ROUND_HALF_DOWN);
                        $amount = $steps * $rule->getEarnPoints();
                        if ($rule->getPointsLimit() && $amount > $rule->getPointsLimit()) {
                            $amount = $rule->getPointsLimit();
                        }
                        $total += $amount;
                        break;
                }
                if ($rule->getIsStopProcessing()) {
                    break;
                }
            }
        }

        return $total;
    }
}

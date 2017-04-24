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


namespace Mirasvit\Rewards\Plugin;

use Magento\Quote\Model\Quote\Item\AbstractItem;

/**
 * @package Mirasvit\Rewards\Plugin
 */
class SalesRuleValidator
{
    public function __construct(
        \Mirasvit\Rewards\Model\Config $config,
        \Mirasvit\Rewards\Helper\Purchase $rewardsPurchase,
        \Mirasvit\Rewards\Helper\Data $rewardsData,
        \Magento\Tax\Helper\Data $taxData
    ) {
        $this->config          = $config;
        $this->rewardsPurchase = $rewardsPurchase;
        $this->rewardsData     = $rewardsData;
        $this->taxData         = $taxData;
    }

    /**
     * @param \Magento\SalesRule\Model\Validator $validator
     * @param \callable                          $proceed
     * @param AbstractItem                       $item
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundProcess(\Magento\SalesRule\Model\Validator $validator, $proceed, AbstractItem $item)
    {
        $returnValue = $proceed($item);
        if ($returnValue && $this->config->getCalculateTotalFlag()) {
            $this->process($item);
        }

        return $returnValue;
    }

    /**
     * @param AbstractItem $item
     * @return $this
     */
    private function process($item)
    {
        $quote = $item->getQuote();
        $address = $item->getAddress();

        if (!$this->canProcess($item)) {
            return $this;
        }
        $purchase = $this->rewardsPurchase->getByQuote($quote);
        $spendAmount = $purchase->getSpendAmount();
        //price with TAX
        $itemTotalPrice = $item->getData('row_total_incl_tax') + $item->getData('weee_tax_applied_row_amount');

        //in some cases, when we add a new configurable item to the cart, it has not updated the price yet.
        $total = $address->getSubtotalInclTax(); //price with TAX, in current currency

        if ($itemTotalPrice == 0 || $total == 0) {//protection from division on zero
            return $this;
        }

        //now we need to check: should we make order grand total = 0.01 or not.
        if (!$this->config->getGeneralIsAllowZeroOrders()) {
            $priceIncludesTax = $this->taxData->priceIncludesTax($quote->getStore());
            $addr = $quote->getShippingAddress();
            if ($priceIncludesTax) {
                $grandTotal = $addr->getBaseSubtotalInclTax() + $addr->getBaseShippingInclTax();
            } else {
                $grandTotal = $addr->getBaseSubtotal() + $addr->getBaseShippingAmount();
            }
            if ($grandTotal == $spendAmount) {
                $spendAmount -= 0.01;
            }
        }

        $discount = $itemTotalPrice / $total * $spendAmount;

        $baseItemTotalPrice = $item->getData('base_row_total_incl_tax'); //price with TAX
        $baseTotal = $address->getBaseSubtotalInclTax(); //price with TAX
        if (!$baseTotal) {
            $baseTotal = $total;
        }
        $baseSpendAmount = $spendAmount * $baseItemTotalPrice / $itemTotalPrice;
        $baseDiscount = $baseItemTotalPrice / $baseTotal * $baseSpendAmount;

        $item->setDiscountAmount($discount + $item->getDiscountAmount());
        $item->setBaseDiscountAmount($baseDiscount + $item->getBaseDiscountAmount());

        return $this;
    }

    /**
     * @param AbstractItem $item
     * @return bool
     */
    private function canProcess($item)
    {
        $quote = $item->getQuote();
        $address = $item->getAddress();

        if ($this->rewardsData->isMultiship($address)) {
            return false;
        }
        if (!$quote->getId()) {
            return false;
        }
        $purchase = $this->rewardsPurchase->getByQuote($quote);

        if (!$purchase->getSpendAmount()) {
            return false;
        }
        $spendAmount = $purchase->getSpendAmount();
        if ($spendAmount == 0) {
            return false;
        }

        return true;
    }
}
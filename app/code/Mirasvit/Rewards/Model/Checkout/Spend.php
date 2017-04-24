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



namespace Mirasvit\Rewards\Model\Checkout;

class Spend extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * @var \Mirasvit\Rewards\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\Rewards\Helper\Purchase
     */
    protected $rewardsPurchase;

    /**
     * @param \Mirasvit\Rewards\Model\Config    $config
     * @param \Mirasvit\Rewards\Helper\Purchase $rewardsPurchase
     */
    public function __construct(
        \Mirasvit\Rewards\Model\Config $config,
        \Mirasvit\Rewards\Helper\Purchase $rewardsPurchase
    ) {
        $this->setCode('reward_spend');
        $this->config = $config;
        $this->rewardsPurchase = $rewardsPurchase;
    }

    /**
     * @return bool|\Mirasvit\Rewards\Model\Purchase
     */
    protected function getPurchase()
    {
        $purchase = $this->rewardsPurchase->getByQuote($this->getQuote());

        return $purchase;
    }
    /**
     * Add discount total information to address.
     *
     * @param \Magento\Quote\Model\Quote               $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     *
     * @return \Magento\SalesRule\Model\Quote\Discount
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        if ($quote->getIsVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }
        if ($address->getAddressType() == \Magento\Quote\Model\Quote\Address::TYPE_SHIPPING) {
            return $this;
        }

        if (!$purchase = $this->rewardsPurchase->getByQuote($quote)) {
            return $this;
        }
        $amount = $purchase->getSpendAmount();
        $rewardPoints = $purchase->getSpendPoints();

        $config = $this->config;

        if ($amount != 0 && !$config->getSpendTotalAppliedFlag()) {
            $title = __('You Spend'); //will be used in some modifications of iwd onestepcheckout
            $address->addTotal([
                'code' => $this->getCode(),
                'title' => $title,
                'value' => $rewardPoints, //will be used in some modifications of iwd onestepcheckout
            ]);
        }
        $config->setSpendTotalAppliedFlag(true);

        return $this;
    }
}

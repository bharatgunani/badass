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


namespace Mirasvit\Rewards\Pricing\Price;

use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\Price\AbstractPrice;

/**
 * Final price model
 */
class RewardsEarn extends AbstractPrice implements RewardsEarnInterface
{
    /**
     * Price type final
     */
    const PRICE_CODE = 'rewards_earning';

    /**
     * @var BasePrice
     */
    private $basePrice;

    /**
     * @var \Magento\Framework\Pricing\Amount\AmountInterface
     */
    protected $minimalPrice;

    /**
     * @var \Magento\Framework\Pricing\Amount\AmountInterface
     */
    protected $maximalPrice;

    /**
     * Get Value
     *
     * @return float|bool
     */
    public function getValue()
    {
        return max(0, $this->getBasePrice()->getValue());
    }

    /**
     * Get Minimal Price Amount
     *
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function getMinimalPrice()
    {
        if (!$this->minimalPrice) {
            $minimalPrice = $this->product->getMinimalPrice();
            if ($minimalPrice === null) {
                $minimalPrice = $this->getValue();
            } else {
                $minimalPrice = $this->priceCurrency->convertAndRound($minimalPrice);
            }
            $this->minimalPrice = $this->calculator->getAmount($minimalPrice, $this->product);
        }
        return $this->minimalPrice;
    }

    /**
     * Get Maximal Price Amount
     *
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function getMaximalPrice()
    {
        if (!$this->maximalPrice) {
            $this->maximalPrice = $this->calculator->getAmount($this->getValue(), $this->product);
        }
        return $this->maximalPrice;
    }

    /**
     * Retrieve base price instance lazily
     *
     * @return BasePrice|\Magento\Framework\Pricing\Price\PriceInterface
     */
    protected function getBasePrice()
    {
        if (!$this->basePrice) {
            $this->basePrice = $this->priceInfo->getPrice(BasePrice::PRICE_CODE);
        }
        return $this->basePrice;
    }
}

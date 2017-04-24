<?php

/**
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */
namespace Magestore\Webpos\Model\Cart\Data;

/**
 * Class Quote
 * @package Magestore\Webpos\Model\Cart\Data
 */
class Checkout extends \Magento\Framework\Model\AbstractExtensibleModel
    implements \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
{
    /**
     * Sets items
     *
     * @param \Magestore\Webpos\Api\Data\Cart\QuoteItemInterface[] Arrray of $items
     * @return $this
     */
    public function setItems(array $items = null)
    {
        return $this->setData(self::ITEMS, $items);
    }
    
    /**
     * Gets items
     *
     * @return \Magestore\Webpos\Api\Data\Cart\QuoteItemInterface[] Arrray of $items
     */
    public function getItems()
    {
        return $this->getData(self::ITEMS);
    }

    /**
     * Sets shipping
     *
     * @param \Magestore\Webpos\Api\Data\Shipping\ShippingInterface[] Arrray of $shipping
     * @return $this
     */
    public function setShipping(array $shipping = null)
    {
        return $this->setData(self::SHIPPING, $shipping);
    }

    /**
     * Gets shipping
     *
     * @return \Magestore\Webpos\Api\Data\Shipping\ShippingInterface[] Arrray of $shipping
     */
    public function getShipping()
    {
        return $this->getData(self::SHIPPING);
    }

    /**
     * Sets payment
     *
     * @param \Magestore\Webpos\Api\Data\Payment\PaymentInterface[] Arrray of $payment
     * @return $this
     */
    public function setPayment(array $payment = null)
    {
        return $this->setData(self::PAYMENT, $payment);
    }

    /**
     * Gets payment
     *
     * @return \Magestore\Webpos\Api\Data\Payment\PaymentInterface[] Arrray of $payment
     */
    public function getPayment()
    {
        return $this->getData(self::PAYMENT);
    }

    /**
     * Sets quote
     *
     * @param \Magestore\Webpos\Api\Data\Cart\QuoteInterface $quote
     * @return $this
     */
    public function setQuoteInit($quote)
    {
        return $this->setData(self::QUOTE_INIT, $quote);
    }

    /**
     * Gets quote
     *
     * @return \Magestore\Webpos\Api\Data\Cart\QuoteInterface
     */
    public function getQuoteInit()
    {
        return $this->getData(self::QUOTE_INIT);
    }

    /**
     * Sets total
     *
     * @param \Magestore\Webpos\Api\Data\Cart\TotalInterface[] $totals
     * @return $this
     */
    public function setTotals(array $totals = null)
    {
        return $this->setData(self::TOTALS, $totals);
    }

    /**
     * Gets quote
     *
     * @return \Magestore\Webpos\Api\Data\Cart\TotalInterface[]
     */
    public function getTotals()
    {
        return $this->getData(self::TOTALS);
    }
}

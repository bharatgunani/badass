<?php

/**
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */
namespace Magestore\Webpos\Api\Data\Cart;

interface CheckoutInterface
{
    const ITEMS = 'items';
    const SHIPPING = 'shipping';
    const PAYMENT = 'payment';
    const QUOTE_INIT = 'quote_init';
    const TOTALS = 'totals';

    const EVENT_WEBPOS_EMPTY_CART_BEFORE = 'webpos_empty_cart_before';
    const EVENT_WEBPOS_EMPTY_CART_AFTER = 'webpos_empty_cart_after';
    const EVENT_WEBPOS_SAVE_CART_AFTER = 'webpos_save_cart_after';
    const EVENT_WEBPOS_SEND_RESPONSE_BEFORE = 'webpos_send_response_before';

    /**
     * Sets items
     *
     * @param \Magestore\Webpos\Api\Data\Cart\QuoteItemInterface[] Arrray of $items
     * @return $this
     */
    public function setItems(array $items = null);
    
    /**
     * Gets items
     *
     * @return \Magestore\Webpos\Api\Data\Cart\QuoteItemInterface[] Arrray of $items
     */
    public function getItems();

    /**
     * Sets shipping
     *
     * @param \Magestore\Webpos\Api\Data\Shipping\ShippingInterface[] Arrray of $shipping
     * @return $this
     */
    public function setShipping(array $shipping = null);

    /**
     * Gets shipping
     *
     * @return \Magestore\Webpos\Api\Data\Shipping\ShippingInterface[] Arrray of $shipping
     */
    public function getShipping();

    /**
     * Sets payment
     *
     * @param \Magestore\Webpos\Api\Data\Payment\PaymentInterface[] Arrray of $payment
     * @return $this
     */
    public function setPayment(array $payment = null);

    /**
     * Gets payment
     *
     * @return \Magestore\Webpos\Api\Data\Payment\PaymentInterface[] Arrray of $payment
     */
    public function getPayment();

    /**
     * Sets quote
     *
     * @param \Magestore\Webpos\Api\Data\Cart\QuoteInterface $quote
     * @return $this
     */
    public function setQuoteInit($quote);

    /**
     * Gets quote
     *
     * @return \Magestore\Webpos\Api\Data\Cart\QuoteInterface
     */
    public function getQuoteInit();

    /**
     * Sets total
     *
     * @param \Magestore\Webpos\Api\Data\Cart\TotalInterface[] $totals
     * @return $this
     */
    public function setTotals(array $totals = null);

    /**
     * Gets quote
     *
     * @return \Magestore\Webpos\Api\Data\Cart\TotalInterface[]
     */
    public function getTotals();
}

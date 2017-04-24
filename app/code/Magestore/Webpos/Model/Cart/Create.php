<?php

/**
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */
namespace Magestore\Webpos\Model\Cart;

use Magestore\Webpos\Model\Checkout\Data\Payment;
use Magestore\Webpos\Model\Checkout\Data\CartItem;

/**
 * Class Create
 * @package Magestore\Webpos\Model\Cart
 */
class Create extends \Magestore\Webpos\Model\Checkout\Create implements \Magestore\Webpos\Api\Cart\CheckoutInterface
{

    /**
     * @var array
     */
    protected $quoteInitData = array();

    /**
     * @var \Magento\Quote\Api\GuestShipmentEstimationInterface
     */
    protected $guestShipmentEstimationInterface = array();

    /**
     *
     * @param string $customerId
     * @param string $quoteId
     * @param string $currencyId
     * @param string $storeId
     * @param string $tillId
     * @param \Magestore\Webpos\Api\Data\Cart\CustomerInterface $customer
     * @param \Magestore\Webpos\Api\Data\Checkout\CartItemInterface[] $items
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     * @throws \Exception
     */
    public function saveCart($customerId, $quoteId, $currencyId, $storeId, $tillId, $customer, $items)
    {
        $this->createQuoteByParams($customerId, $quoteId, $currencyId, $storeId,
                                            $tillId, $customer, $items);
        $result = $this->getQuoteData();
        return $result;
    }

    /**
     *
     * @param string $customerId
     * @param \Magestore\Webpos\Api\Data\CartItemInterface[] $items
     * @param \Magestore\Webpos\Api\Data\Checkout\PaymentInterface $payment
     * @param \Magestore\Webpos\Api\Data\Checkout\ShippingInterface $shipping
     * @param \Magestore\Webpos\Api\Data\Checkout\ConfigInterface $config
     * @param string $couponCode
     * @param \Magestore\Webpos\Api\Data\Checkout\SessionDataInterface[] $sessionData
     * @return \Magestore\Webpos\Api\Data\Sales\OrderInterface
     * @throws \Exception
     */
    public function createQuoteByParams($customerId, $quoteId, $currencyId, $storeId,
                                        $tillId, $customer, $items){
        if($quoteId){
            $quote = $this->quoteRepository->get($quoteId);
            $this->setQuote($quote);
        }
        $webposSession = $this->getSession();
        $webposSession->clearStorage();
        $store = $webposSession->getStore();
        $webposSession->setCurrencyId($currencyId);
        $webposSession->setStoreId($storeId);
        $webposSession->setData('checking_promotion',false);
        $webposSession->setData('webpos_order', 1);
        $webposSession->setData('till_id', $tillId);
        $store->setCurrentCurrencyCode($currencyId);
        $this->getQuote()->setQuoteCurrencyCode($currencyId);
        $helperCustomer = $this->_objectManager->create('Magestore\Webpos\Helper\Customer');
        $storeAddress = $helperCustomer->getStoreAddressData();
        if ($customerId) {
            $customerData = $this->customerRepository->getById($customerId);
            if ($customerData->getId()) {
                $webposSession->setCustomerId($customerId);
                $this->getQuote()->setCustomer($customerData);
            }
        }else{
            $this->getQuote()->setCustomerIsGuest(true);
            $this->getQuote()->setCustomerEmail($storeAddress['email']);
        }
        //$this->initRuleData();
        $this->_processCart($items);

        $billingAddress = $customer->getBillingAddress()->getData();
        $shippingAddress = $customer->getShippingAddress()->getData();
        if(!$billingAddress)
            $billingAddress = $storeAddress;
        if(!$shippingAddress)
            $shippingAddress = $storeAddress;
        $this->setBillingAddress($billingAddress);
        if(!$this->getQuote()->isVirtual()){
            $this->setShippingAddress($shippingAddress);
            $this->getQuote()->getShippingAddress()->setCollectShippingRates(true)
                  ->collectShippingRates();
        }
        $this->getQuote()->setIsActive(1);
        $this->saveQuote($this->getQuote());
    }

    /**
     * @return mixed
     */
    public function getQuoteInitData(){
        $quote = $this->getQuote();
        $quoteInitData = $this->_objectManager->create('Magestore\Webpos\Model\Cart\Data\Quote');
        $quoteInitData->setData(\Magestore\Webpos\Api\Data\Cart\QuoteInterface::QUOTE_ID, $quote->getId());
        $quoteInitData->setData(\Magestore\Webpos\Api\Data\Cart\QuoteInterface::CUSTOMER_ID, $quote->getCustomerId());
        $quoteInitData->setData(\Magestore\Webpos\Api\Data\Cart\QuoteInterface::CURRENCY_ID, $quote->getQuoteCurrencyCode());
        $quoteInitData->setData(\Magestore\Webpos\Api\Data\Cart\QuoteInterface::TILL_ID, $quote->getTillId());
        $quoteInitData->setData(\Magestore\Webpos\Api\Data\Cart\QuoteInterface::STORE_ID, $quote->getStoreId());
        $quoteInitData->setData(\Magestore\Webpos\Api\Data\Cart\QuoteInterface::SHIPPING_METHOD,
                                $quote->getShippingAddress()->getShippingMethod());
        return $quoteInitData;
    }

    /**
     * @param $sections
     * @param $model
     * @return array
     */
    protected function getQuoteData($sections = null){
        $quoteData = $this->_objectManager->create('Magestore\Webpos\Model\Cart\Data\Checkout');
        if(empty($sections) || $sections == \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::QUOTE_INIT ||
            (is_array($sections) && in_array(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::QUOTE_INIT, $sections))){
            $quoteData->setData(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::QUOTE_INIT,
                                $this->getQuoteInitData());
        }
        if(empty($sections) || $sections == \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::ITEMS ||
            (is_array($sections) && in_array(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::ITEMS, $sections))){
            $quoteData->setData(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::ITEMS,
                                $this->getQuoteItems());
        }
        if(empty($sections) || $sections == \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::TOTALS ||
            (is_array($sections) && in_array(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::TOTALS, $sections))){
            $quoteData->setData(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::TOTALS,
                                $this->getCartTotals());
        }
        if(empty($sections) || $sections == \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::SHIPPING ||
            (is_array($sections) && in_array(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::SHIPPING, $sections))){
            $quoteData->setData(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::SHIPPING,
                $this->getShipping());
        }
        if(empty($sections) || $sections == \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::PAYMENT ||
            (is_array($sections) && in_array(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::PAYMENT, $sections))){
            $quoteData->setData(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::PAYMENT,
                $this->getPayment());
        }
        return $quoteData;
    }

    /**
     * @return array
     */
    public function getQuoteItems(){
        $result = array();
        $items = $this->getQuote()->getAllVisibleItems();
        if(count($items)){
            foreach ($items as $item){
                $product = $item->getProduct();
                $stockItem = $this->stockRegistry->getStockItem($product->getId(), $product->getStore()->getWebsiteId());
                $imageUrl = $this->getImage($product);
                $item->setData('offline_item_id', $item->getBuyRequest()->getData('item_id'));
                $item->setData('minimum_qty', $stockItem->getMinSaleQty());
                $item->setData('maximum_qty', $stockItem->getMaxSaleQty());
                $item->setData('qty_increment', $stockItem->getQtyIncrements());
                $item->setData('image_url', $imageUrl);
                $result[$item->getId()] = $item;
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getCartTotals(){
        $totals = $this->getAllTotals($this->getQuote());
        $totalsResult = array();
        foreach ($totals as $total) {
            $data = $total->getData();
            $totalsResult[] = $data;
        }
        return $totalsResult;
    }

    /**
     * Get all quote totals (sorted by priority)
     * Method process quote states isVirtual and isMultiShipping
     *
     * @return array
     */
    public function getAllTotals($quote){
        if ($quote->isVirtual()) {
            return $quote->getBillingAddress()->getTotals();
        }
        return $quote->getShippingAddress()->getTotals();
    }

    /**
     * @return array
     */
    public function getShipping(){
        $shippingList = array();
        $api = $this->_objectManager->create('Magento\Quote\Model\ShippingMethodManagement');
        $list = $api->getList($this->getQuote()->getId());
        if(count($list) > 0){
            $shippingHelper = $this->_objectManager->create('Magestore\Webpos\Helper\Shipping');
            foreach ($list as $data) {
                $methodCode = $data->getMethodCode();
                $isDefault = '0';
                if($methodCode == $shippingHelper->getDefaultShippingMethod()) {
                    $isDefault = '1';
                }
                $methodTitle = $data->getCarrierTitle() . ' - ' . $data->getMethodTitle();
                $methodPrice = ($data->getPriceExclTax() != null) ? $data->getPriceExclTax() : '0';
                $methodPriceType = '';
//                $methodDescription = ($data->getDescription() != null) ? $data->getDescription() : '0';
//                $methodSpecificerrmsg = ($data->getErrorMessage() != null) ? $data->getErrorMessage() : '';
                $methodDescription = '';
                $methodSpecificerrmsg = '';

                $shippingModel =  $this->_objectManager->create('Magestore\Webpos\Model\Shipping\Shipping');
                $shippingModel->setCode($methodCode);
                $shippingModel->setTitle($methodTitle);
                $shippingModel->setPrice($methodPrice);
                $shippingModel->setDescription($methodDescription);
                $shippingModel->setIsDefault($isDefault);
                $shippingModel->setErrorMessage($methodSpecificerrmsg);
                $shippingModel->setPriceType($methodPriceType);
                $shippingList[] = $shippingModel;
            }
        }
        return $shippingList;
    }

    /**
     * @return mixed
     */
    public function getPayment(){
        $api = $this->_objectManager->create('Magento\Quote\Model\PaymentMethodManagement');
        $list = $api->getList($this->getQuote()->getId());
        $paymentList = array();
        if(count($list) > 0) {
            $paymentHelper = $this->_objectManager->create('Magestore\Webpos\Helper\Payment');
            foreach ($list as $data) {
                $code = $data->getCode();
                $title = $data->getTitle();
                $ccTypes = $data->getCCTypes();
                if ($paymentHelper->isWebposPayment($code))
                    continue;
                $iconClass = 'icon-iconPOS-payment-cp1forpos';
                $isDefault = ($code == $paymentHelper->getDefaultPaymentMethod()) ?
                    \Magestore\Webpos\Api\Data\Payment\PaymentInterface::YES :
                    \Magestore\Webpos\Api\Data\Payment\PaymentInterface::NO;
                $isReferenceNumber = (!$ccTypes) ? 1 : 0;
                $isPayLater = '0';

                $paymentModel = $this->_objectManager->create('Magestore\Webpos\Model\Payment\Payment');
                $paymentModel->setCode($code);
                $paymentModel->setIconClass($iconClass);
                $paymentModel->setTitle($title);
                $paymentModel->setInformation('');
                $paymentModel->setType(($ccTypes) ? $ccTypes : \Magestore\Webpos\Api\Data\Payment\PaymentInterface::NO );
                $paymentModel->setIsDefault($isDefault);
                $paymentModel->setIsReferenceNumber($isReferenceNumber);
                $paymentModel->setIsPayLater($isPayLater);
                $paymentModel->setMultiable(false);
                $paymentList[] = $paymentModel->getData();
            }
        }
        return $paymentList;
    }

    /**
     * Sets product image from it's child if possible
     *
     * @return string
     */
    public function getImage($product){
        $imageString = $product->getThumbnail();
        if ($imageString && $imageString != 'no_selection') {
            return $product->getMediaConfig()->getMediaUrl($imageString);
        } else {
            $storeManager = \Magento\Framework\App\ObjectManager::getInstance()->get(
                '\Magento\Store\Model\StoreManagerInterface'
            );
            $url = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            return $url.'webpos/catalog/category/image.jpg';
        }
    }

    /**
     * remove cart by quote id
     *
     * @param string $quoteId
     * @return \Magestore\Webpos\Api\Data\Cart\QuoteInterface
     * @throws \Exception
     */
    public function removeCart($quoteId){
        $quoteData = $this->_objectManager->create('Magestore\Webpos\Model\Cart\Data\Quote');
        if(!empty($quoteId)){
            $quote = $this->quoteRepository->get($quoteId);
            $eventData = array(
                'quote' => $quote
            );
            $this->_eventManager->dispatch(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::EVENT_WEBPOS_EMPTY_CART_BEFORE,
                $eventData);

            try{
                $this->quoteRepository->delete($quote);
            }catch (\Exception $e){
                throw new \Magento\Framework\Exception\StateException(
                    __('Unable to remove cart')
                );
            }
            $this->_eventManager->dispatch(\Magestore\Webpos\Api\Data\Cart\CheckoutInterface::EVENT_WEBPOS_EMPTY_CART_AFTER,
                $eventData);
        }
        return $quoteData;
    }

    /**
     * save shipping method
     *
     * @param string $quoteId
     * @param string $shippingMethod
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     * @throws \Exception
     */
    public function saveShippingMethod($quoteId, $shippingMethod){
        if($quoteId){
            $quote = $this->quoteRepository->get($quoteId);
            $this->setQuote($quote);
        }
        try {
            $this->setShippingmethod($shippingMethod);
            $this->saveQuote($this->getQuote());
        }catch (\Exception $e){
            throw new \Magento\Framework\Exception\StateException(
                __('Unable to save shipping method')
            );
        }
        $data = array(
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::QUOTE_INIT,
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::ITEMS,
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::PAYMENT,
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::TOTALS
        );
        $result = $this->getQuoteData($data);
        return $result;
    }

    /**
     * save payment method
     *
     * @param string $quoteId
     * @param string $paymentMethod
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     * @throws \Exception
     */
    public function savePaymentMethod($quoteId, $paymentMethod){
        if($quoteId){
            $quote = $this->quoteRepository->get($quoteId);
            $this->setQuote($quote);
        }
        try {
            $this->setPaymentMethod($paymentMethod);
            $this->saveQuote($this->getQuote());
        }catch (\Exception $e){
            throw new \Magento\Framework\Exception\StateException(
                __('Unable to save shipping method')
            );
        }
        $data = array(
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::QUOTE_INIT,
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::ITEMS,
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::TOTALS
        );
        $result = $this->getQuoteData($data);
        return $result;
    }

    /**
     * @param string $quoteId
     * @param \Magestore\Webpos\Api\Data\Cart\QuoteDataInterface $quoteData
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     * @throws \Exception
     */
    public function saveQuoteData($quoteId, $quoteData)
    {

        if($quoteId){
            $quote = $this->quoteRepository->get($quoteId);
            $this->setQuote($quote);
        }
        try {
            $this->getQuote()->addData($quoteData->getData());
            $this->quoteRepository->save($this->getQuote());
        }catch (\Exception $e){
            throw new \Magento\Framework\Exception\StateException(
                __('Unable to save cart')
            );
        }
        $result = $this->getQuoteData();
        return $result;
    }

    /**
     * @param string $quoteId
     * @param \Magestore\Webpos\Api\Data\Checkout\PaymentInterface $payment
     * @param \Magestore\Webpos\Api\Data\Cart\QuoteDataInterface $quoteData
     * @param \Magestore\Webpos\Api\Data\Cart\ActionInterface $actions
     * @param array $integration
     * @return \Magestore\Webpos\Api\Data\Sales\OrderInterface
     * @throws \Exception
     */
    public function placeOrder($quoteId, $payment, $quoteData, $actions, $integration){
        if($quoteId){
            $quote = $this->quoteRepository->get($quoteId);
            $this->setQuote($quote);
        }
        try {
            $order = $this->createOrder();
            $this->_removeSessionData($this->getSession());
            $order = $this->_processIntegration($order, $integration);
            $this->processActionsAfterCreateOrder($order, $actions);
            $order = $this->processPaymentAfterCreateOrder($order, $payment);
            $this->_eventManager->dispatch('webpos_order_sync_after', ['order' => $order]);
            $this->emailSender->send($order);
        }catch (\Exception $e){
            throw new \Magento\Framework\Exception\StateException(
                __($e->getMessage())
            );
        }
        return $order;
    }

    /**
     * save payment information
     *
     * @param $order
     * @param $payment
     */
    public function processPaymentAfterCreateOrder($order, $payment){
        $paidPayment = [
            'amount' => 0,
            'base_amount' => 0
        ];
        if(isset($payment[Payment::KEY_DATA])){
            $paidPayment = $this->_getPaidPayment($payment[Payment::KEY_DATA]);
        }
        $order->setData('total_paid',$paidPayment['amount']);
        $order->setData('base_total_paid',$paidPayment['base_amount']);
        $order->save();
        if(isset($payment[Payment::KEY_DATA])){
            $this->_savePaymentsToOrder($order, $payment[Payment::KEY_DATA]);
        }
        return $order;
    }

    /**
     * process invoice shipment and other information
     *
     * @param $order
     * @param $actions
     */
    public function processActionsAfterCreateOrder($order, $actions){
        $createInvoice = 0;
        $createShipment = 0;
        if(!empty($actions)){
            if($actions->getData(\Magestore\Webpos\Api\Data\Cart\ActionInterface::CREATE_INVOICE)){
                $createInvoice = $actions->getData(\Magestore\Webpos\Api\Data\Cart\ActionInterface::CREATE_INVOICE);
            }
            if($actions->getData(\Magestore\Webpos\Api\Data\Cart\ActionInterface::CREATE_SHIPMENT)){
                $createShipment = $actions->getData(\Magestore\Webpos\Api\Data\Cart\ActionInterface::CREATE_SHIPMENT);
            }
        }
        try{
            $this->processInvoiceAndShipment($order->getId(),$order,$createInvoice,$createShipment,
                array(), array());
        }catch(\Exception $e){
        }
    }

    /**
     *
     * @param string $quoteId
     * @param string $itemId
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     * @throws \Exception
     */
    public function removeItemById($quoteId, $itemId)
    {
        if($quoteId) {
            $quote = $this->quoteRepository->get($quoteId);
            $this->setQuote($quote);
            try {
                $this->removeItem($itemId, 'quote');
                $this->quoteRepository->save($quote);
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\StateException(
                    __('Unable to remove item')
                );
            }
        }else{
            throw new \Magento\Framework\Exception\StateException(
                __('Unable to remove item')
            );
        }
        $data = array(
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::QUOTE_INIT,
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::ITEMS,
            \Magestore\Webpos\Api\Data\Cart\CheckoutInterface::TOTALS
        );
        $result = $this->getQuoteData($data);

        return $result;
    }

    /**
     * @param string $quoteId
     * @param string $couponCode
     * @return \Magestore\Webpos\Api\Data\Cart\CheckoutInterface
     * @throws \Exception
     */
    public function applyCouponCode($quoteId, $couponCode){
        if($quoteId){
            $quote = $this->quoteRepository->getActive($quoteId);
            $this->setQuote($quote);
            if (!$this->getQuote()->getItemsCount()) {
                throw new \Magento\Framework\Exception\StateException(
                    __('Cart %1 doesn\'t contain products', $quoteId));
            }
            try {
                $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
                $this->getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '');
                $this->quoteRepository->save($this->getQuote());
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\StateException(__('Could not apply coupon code'));
            }

            if ($this->getQuote()->getCouponCode() != $couponCode) {
                throw new \Magento\Framework\Exception\StateException(__('Coupon code is not valid'));
            }
        }else{
            throw new \Magento\Framework\Exception\StateException(
                __('Could not apply')
            );
        }
        $result = $this->getQuoteData();

        return $result;
    }

    /**
     * Add product to current order quote
     * @param int|\Magento\Catalog\Model\Product $productData
     * @param array|float|int|\Magento\Framework\DataObject $config
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addProduct($productData, $config = 1)
    {
        if (!is_array($config) && !$config instanceof \Magento\Framework\DataObject) {
            $config = ['qty' => $config];
        }

        $config = new \Magento\Framework\DataObject($config);

        if (!$productData instanceof \Magento\Catalog\Model\Product) {
            $productId = $productData;
            $productData = $this->_objectManager->create(
                'Magento\Catalog\Model\Product'
            )->setStore(
                $this->getSession()->getStore()
            )->setStoreId(
                $this->getSession()->getStoreId()
            )->load(
                $productData
            );
            if (!$productData->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('We could not add a product to cart by the ID "%1".', $productId)
                );
            }
        }
        $item = $this->quoteInitializer->init($this->getQuote(), $productData, $config);
        if (is_string($item)) {
            throw new \Magento\Framework\Exception\LocalizedException(__($item));
        }
        $item->checkData();
        if($config->getData(CartItem::KEY_CUSTOM_PRICE)){
            $customPrice = $config->getData(CartItem::KEY_CUSTOM_PRICE);
            $item->setCustomPrice($customPrice);
            $item->setOriginalCustomPrice($customPrice);
            $item->getProduct()->setIsSuperMode(true);
        }
        if($config->getData(CartItem::KEY_IS_CUSTOM_SALE)){
            $options = $config->getData(CartItem::KEY_CUSTOM_OPTION);
            if(isset($options['name'])){
                $item->setName($options['name']);
            }
            if(isset($options['tax_class_id'])){
                $item->getProduct()->setTaxClassId($options['tax_class_id']);
            }
        }
        if($config->getData(CartItem::KEY_QTY_TO_SHIP) > 0){
            $this->_addItemToShip($item->getItemId(),$config->getData(CartItem::KEY_QTY_TO_SHIP));
        }
        $this->setRecollect(true);
        return $this;
    }

}
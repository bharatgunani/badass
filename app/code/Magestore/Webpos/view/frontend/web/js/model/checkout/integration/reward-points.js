/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/resource-model/magento-rest/integration/rewardpoints/reward-points',
        'Magestore_Webpos/js/model/resource-model/indexed-db/integration/rewardpoints/reward-points',
        'Magestore_Webpos/js/model/collection/integration/reward-points',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/view/checkout/cart',
        'Magestore_Webpos/js/model/checkout/cart/totals',
        'Magestore_Webpos/js/model/checkout/cart/data/cart',
        'Magestore_Webpos/js/view/settings/general/rewardpoints/show-rewardpoints-balance',
        'Magestore_Webpos/js/view/settings/general/rewardpoints/auto-sync-balance',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/checkout/integration/rewardpoints/rate',
        'Magestore_Webpos/js/model/checkout/checkout'
    ],
    function ($, ko, modelAbstract, onlineResource, offlineResource, collection, CartModel, CartView, Totals, CartData, ShowPointBalance, AutoSyncBalance, Helper, RateModel, CheckoutModel) {
        "use strict";
        return modelAbstract.extend({
            sync_id:'customer_points',
            TOTAL_CODE:'rewardpoints',
            MODULE_CODE:'os_reward_points',
            STATUS_INACTIVE: 0,
            STATUS_ACTIVE: 1,
            TYPE_POINT_TO_MONEY: '1',
            TYPE_MONEY_TO_POINT: '2',
            CUSTOMER_GROUP_ALL: '0',
            EARN_WHEN_INVOICE: '1',
            balance: ko.observable(0),
            currentAmount: ko.observable(0),
            appliedAmount: ko.observable(0),
            visible: ko.observable(false),
            applied: ko.observable(false),
            useMaxPoint: ko.observable(true),
            updatingBalance: ko.observable(false),
            remaining: CheckoutModel.remainTotal,
            rates: ko.observableArray([]),
            earningRateValue: ko.observable(0),
            spendingRateValue: ko.observable(0),
            earningRate: ko.observable(),
            spendingRate: ko.observable(),
            discountAmount: ko.observable(),
            initialize: function () {
                if(Helper.isRewardPointsEnable()) {
                    this._super();
                    this.setResource(onlineResource(), offlineResource());
                    this.setResourceCollection(collection());
                    this.TotalsModel = Totals();
                    this.initObserver();
                }
            },
            getConfig: function(path){
                var allConfig = Helper.getBrowserConfig('plugins_config');
                if(allConfig[this.MODULE_CODE]){
                    var configs = allConfig[this.MODULE_CODE];
                    if(configs[path]){
                        return configs[path];
                    }
                }
                return false;
            },
            loadRates: function() {
                var self = this;
                var deferred = RateModel().getCollection().setOrder('sort_order', 'DESC').load();
                deferred.done(function(data){
                    self.rates(data.items);
                });
            },
            getAmountForShipping: function(items){
                var amount = {
                    base:0,
                    amount:0
                };
                if(items && items.length > 0){
                    var totalBase = 0;
                    var totalAmount = 0;
                    ko.utils.arrayForEach(items, function(item) {
                        if(item.item_base_point_discount){
                            totalBase += item.item_base_point_discount;
                        }
                        if(item.item_point_discount){
                            totalAmount += item.item_point_discount;
                        }
                    });
                    amount.base = Helper.toBasePrice(this.discountAmount()) - totalBase;
                    amount.amount = this.discountAmount() - totalAmount;
                }
                return amount;
            },
            initObserver: function(){
                var self = this;
                self.hadObserver = true;
                self.loadRates();
                self.visible = ko.pureComputed(function(){
                    return self.canUseExtension();
                });

                self.applied(false);

                self.balanceAfterApply = ko.pureComputed(function () {
                    return (self.applied()) ? (self.balance() - self.appliedAmount()) : self.balance();
                });

                self.earningPoint = ko.pureComputed(function () {
                    var point = 0;
                    if(self.earningRateValue() > 0 && self.canUseExtension()){
                        point = parseFloat(self.collectTotalToEarn()) * parseFloat(self.earningRateValue());
                        point = self.roundEarning(point);
                    }
                    return point;
                });

                self.earningPoint.subscribe(function (value) {
                    var visible = (value > 0)?true:false;
                    self.TotalsModel.addAdditionalInfo({
                        code:'rewardpoints_earning_point',
                        title:Helper.__('Customer will earn ')+value+' point(s)',
                        value:'',
                        visible:visible
                    });
                });

                self.useMaxPoint.subscribe(function (value) {
                    if (value == true) {
                        self.validate(self.balance(),self.convertMoneyToPoint(self.TotalsModel.positiveTotal()));
                    }
                });
                self.balance.subscribe(function (value) {
                    if (self.useMaxPoint() == true) {
                        self.currentAmount(self.balance());
                    }
                });
                self.currentAmount.subscribe(function (value) {
                    self.validate();
                });

                self.TotalsModel.positiveTotal.subscribe(function (value) {
                    if (self.applied() && self.applied() == true) {
                        if(value > 0){
                            var valid = self.validate(self.appliedAmount(), self.convertMoneyToPoint(value));
                            if (valid) {
                                self.apply();
                            }
                        }else{
                            self.remove();
                        }
                    } else {
                        self.currentAmount(self.convertMoneyToPoint(value));
                        self.useMaxPoint(true);
                    }
                });
                self.TotalsModel.baseDiscountAmount.subscribe(function (value) {
                    if (self.applied() && self.applied() == true) {
                        var maxAmount = self.TotalsModel.positiveTotal() - value;
                        var valid = self.validate(self.appliedAmount(), self.convertMoneyToPoint(maxAmount));
                        if (valid) {
                            self.apply();
                        }
                    }else {
                        var maxAmount = self.TotalsModel.positiveTotal() - value;
                        self.currentAmount(self.convertMoneyToPoint(maxAmount));
                        self.useMaxPoint(true);
                    }
                });

                /**
                 * Reset data after cart empty
                 */
                Helper.observerEvent('cart_empty_after', function (event, data) {
                    self.cartEmptyAfter(data);
                });

                /**
                 * Load customer balance when select customer to checkout
                 */
                Helper.observerEvent('checkout_select_customer_after', function (event, data) {
                    self.selectCustomerToCheckoutAfter(data);
                });

                /**
                 * Show customer point balance on receipt
                 */
                Helper.observerEvent('prepare_receipt_totals', function (event, data) {
                    self.prepareReceipt(data);
                });

                /**
                 * Update customer point balance on local after checkout
                 */
                Helper.observerEvent('webpos_order_save_after', function (event, data) {
                    self.orderSaveAfter(data);
                });

                /**
                 * Add params to sync data when syncing order
                 */
                Helper.observerEvent('webpos_place_order_before', function (event, data) {
                    self.placeOrderBefore(data);
                });

                /**
                 * Call api to update customer credit balance after refund by credit
                 */
                Helper.observerEvent('sales_order_creditmemo_afterSave', function (event, data) {
                    self.refundAfter(data);
                });

                /**
                 * Reload rate data after sync
                 */
                Helper.observerEvent('rewardpoint_rates_finish_pull_after',function(event,data){
                    self.loadRates();
                });
            },
            /**
             * Event cart empty after - remove applied data
             * @param data
             */
            cartEmptyAfter: function(data){
                this.remove();
                this.resetAllData();
            },
            /**
             * Event after select customer - load balance and collect rates
             * @param data
             */
            selectCustomerToCheckoutAfter: function(data){
                var self = this;
                if (self.updatingBalance() == false && CartModel.customerId()) {
                    if (data.customer && data.customer.id) {
                        self.loadBalanceByCustomerId(data.customer.id);
                        var autoSyncBalance = Helper.getLocalConfig(AutoSyncBalance().configPath);
                        if(autoSyncBalance == true){
                            self.updateBalance();
                        }
                        self.collectRates();
                    }
                }
            },
            /**
             * Event before show receipt - Add data to show on receipt
             * @param data
             */
            prepareReceipt: function(data){
                data.totals.push({
                    code: 'rewardpoints_discount',
                    title: 'Points Discount',
                    required: false,
                    sortOrder: 37,
                    isPrice: true
                });
                data.totals.push({
                    code: 'rewardpoints_spent',
                    title: 'Spent',
                    required: false,
                    sortOrder: 6,
                    isPrice: false,
                    valueLabel: Helper.__('Points')
                });
                data.totals.push({
                    code: 'rewardpoints_earn',
                    title: 'Earn',
                    required: false,
                    sortOrder: 5,
                    isPrice: false,
                    valueLabel: Helper.__('Points')
                });
                if (data.customer_id) {
                    var self = this;
                    var showBalance = Helper.getLocalConfig(ShowPointBalance().configPath);
                    if (CartModel.customerId() && showBalance == true) {
                        var balance = self.balance() - self.appliedAmount();
                        var earnWhenInvoice = self.getConfig('rewardpoints/earning/order_invoice');
                        var holdDays = Helper.toNumber(self.getConfig('rewardpoints/earning/holding_days'));
                        if(holdDays <= 0) {
                            if (earnWhenInvoice == self.EARN_WHEN_INVOICE) {
                                if (CheckoutModel.createInvoice()) {
                                    balance += self.earningPoint();
                                }
                            } else {
                                if (CheckoutModel.createInvoice() && CheckoutModel.createShipment()) {
                                    balance += self.earningPoint();
                                }
                            }
                        }
                        data.accountInfo.push({
                            label: Helper.__('Customer points balance'),
                            value: balance
                        });
                    }
                }
            },
            /**
             * Event order save after - update balance on local
             * @param data
             */
            orderSaveAfter: function(data){
                var self = this;
                var balance = self.balance();
                if (data && data.customer_id && data.rewardpoints_spent > 0) {
                    balance -= data.rewardpoints_spent;
                }
                var earnWhenInvoice = self.getConfig('rewardpoints/earning/order_invoice');
                var holdDays = Helper.toNumber(self.getConfig('rewardpoints/earning/holding_days'));
                if(holdDays <= 0) {
                    if (earnWhenInvoice == self.EARN_WHEN_INVOICE) {
                        if (CheckoutModel.createInvoice()) {
                            if (data && data.customer_id && data.rewardpoints_earn > 0) {
                                balance += data.rewardpoints_earn;
                            }
                        }
                    } else {
                        if (CheckoutModel.createInvoice() && CheckoutModel.createShipment()) {
                            if (data && data.customer_id && data.rewardpoints_earn > 0) {
                                balance += data.rewardpoints_earn;
                            }
                        }
                    }
                }
                if(balance != self.balance()){
                    self.saveBalance(data.customer_id, balance);
                }
            },
            /**
             * Event place order before - add data to order
             * @param data
             */
            placeOrderBefore: function(data){
                var self = this;
                if (data && data.increment_id && CartModel.customerId()) {
                    var order_data = [];
                    if(self.earningPoint() > 0){
                        data.rewardpoints_earn = self.earningPoint();
                        order_data.push({
                            key: "rewardpoints_earn",
                            value: self.earningPoint()
                        });
                    }
                    if(self.applied() && self.appliedAmount() > 0){
                        data.rewardpoints_spent = self.appliedAmount();
                        data.rewardpoints_discount = -self.discountAmount();
                        data.rewardpoints_base_discount = -Helper.toBasePrice(self.discountAmount());

                        order_data.push({
                            key: "rewardpoints_spent",
                            value: self.appliedAmount()
                        });
                        order_data.push({
                            key: "rewardpoints_discount",
                            value: self.discountAmount()
                        });
                        order_data.push({
                            key: "rewardpoints_base_discount",
                            value: Helper.toBasePrice(self.discountAmount())
                        });
                    }

                    var useForShipping = self.getConfig('rewardpoints/spending/spend_for_shipping');
                    if (useForShipping == '1') {
                        order_data.push({
                            key: "rewardpoints_amount",
                            value: self.getAmountForShipping(data.items).amount
                        });
                        order_data.push({
                            key: "rewardpoints_base_amount",
                            value: self.getAmountForShipping(data.items).base
                        });
                    }

                    data.sync_params.integration.push({
                        'module': 'os_reward_points',
                        'event_name': 'webpos_create_order_with_points_after',
                        'order_data': order_data,
                        'extension_data': []
                    });
                }
            },
            refundAfter: function(data){
                var self = this;
                if (data && data.response && data.response.customer_id) {
                    if(data.response.rewardpoints_earn > 0 || data.response.rewardpoints_spent > 0){
                        self.updateBalance(data.response.customer_id);
                    }
                }
            },
            /**
             * Reset all model data
             */
            resetAllData: function(){
                this.balance(0);
                this.currentAmount(0);
                this.applied(false);
                this.useMaxPoint(false);
                this.earningRateValue(0);
                this.spendingRateValue(0);
                this.earningRate({});
                this.spendingRate({});
            },
            /**
             * Reset model data
             */
            resetData: function(){
                this.currentAmount(0);
                this.applied(false);
                this.useMaxPoint(false);
            },
            /**
             * Update balance from server
             * @param customerId
             */
            updateBalance: function(customerId){
                customerId = (customerId)?customerId:CartModel.customerId();
                if(customerId){
                    var self = this;
                    var deferred = $.Deferred();
                    var params = {
                        customer_id: customerId
                    };
                    onlineResource().setPush(true).setLog(false).getBalance(params,deferred);
                    self.updatingBalance(true);
                    deferred.done(function (response) {
                        var data =  JSON.parse(response);
                        if(typeof data.balance != undefined){
                            self.balance(data.balance);
                            self.saveBalance(customerId, data.balance);
                        }
                    }).fail(function (response) {
                        if(response.responseText){
                            var error = JSON.parse(response.responseText);
                            if(error.message != undefined){
                                Helper.addNotification(error.message, true, 'danger', 'Error');
                            }
                        }else{
                            Helper.addNotification("Please check your network connection", true, 'danger', 'Error');
                        }
                    }).always(function (response) {
                        var data =  JSON.parse(response);
                        if(data.message != undefined){
                            if(data.success){
                                Helper.addNotification(data.message, true, 'success', 'Message');
                            }
                            if(data.error){
                                Helper.addNotification(data.message, true, 'danger', 'Error');
                            }
                        }
                        self.updatingBalance(false);
                    });
                }
            },
            /**
             * Apply discount to cart
             * @param apply
             */
            apply: function(apply){
                var amount = (apply === false)?0:this.currentAmount();
                var visible = (amount > 0)?true:false;
                this.applied(visible);
                this.currentAmount(amount);
                if(visible){
                    this.appliedAmount(amount);
                }else{
                    this.appliedAmount(0);
                }
                this.collectDiscountAmount();
                this.TotalsModel.addTotal({
                    code: this.TOTAL_CODE,
                    cssClass: 'discount',
                    title: Helper.__('Points Discount'),
                    value: -this.discountAmount(),
                    isVisible: visible,
                    removeAble: true,
                    actions:{
                        remove: $.proxy(this.remove, this),
                        collect: $.proxy(this.collect, this)
                    }
                });
                this.TotalsModel.updateTotal('rewardpoints',{isVisible: visible});
                this.process(Helper.toBasePrice(this.discountAmount()));
                Helper.dispatchEvent('reset_payments_data', '');
            },
            /**
             * Remove data
             */
            remove: function(){
                this.resetData();
                this.apply(false);
            },
            /**
             * Validate can use module
             * @returns {boolean}
             */
            canUseExtension: function(){
                var customerId = CartModel.customerId();
                var moduleEnable = Helper.isRewardPointsEnable();
                var canuse = (
                    moduleEnable
                    && customerId
                )?true:false;
                return canuse;
            },
            /**
             * Validate amount before apply
             * @param balance
             * @param max
             * @returns {boolean}
             */
            validate: function(balance, max){
                if(!this.canUseExtension()){
                    if(this.visible() == true && this.applied() && this.applied() == true){
                        this.remove();
                    }
                    return false;
                }
                var amount = 0;
                if(!balance){
                    amount = this.currentAmount();
                }else{
                    amount = balance;
                }
                var validTotal = this.collectValidTotal();
                var max = (max)?max:this.convertMoneyToPoint(validTotal);
                if(max > this.balance()){
                    max = this.balance();
                }
                amount = (amount > max || (this.useMaxPoint() == true))?max:amount;
                if(this.currentAmount() > amount || balance){
                    amount = (parseFloat(amount) > 0)?amount:0;
                    this.currentAmount(parseFloat(amount));
                }
                return true;
            },
            collectValidTotal: function(){
                var discountAmount = (this.discountAmount())?this.discountAmount():0;
                var applyAfterTax = (Helper.getBrowserConfig('tax/calculation/apply_after_discount'))?'0':'1';
                var validTotal = (applyAfterTax == '0')?(this.TotalsModel.grandTotal() + discountAmount - this.TotalsModel.tax()):(this.TotalsModel.grandTotal() + discountAmount);
                var useForShipping = this.getConfig('rewardpoints/spending/spend_for_shipping');
                if(useForShipping == '0'){
                    validTotal -= this.TotalsModel.shippingFee();
                }
                return validTotal;
            },
            collectMaxTotalToDiscount: function(){
                var max = 0;
                if(CartData.totals().length > 0){
                    var self = this;
                    ko.utils.arrayForEach(CartData.totals(), function (total) {
                       if(total.code() != self.TOTAL_CODE && total.code() != self.TotalsModel.GRANDTOTAL_TOTAL_CODE && total.value()){
                           max += Helper.toNumber(total.value());
                       }
                    });
                }
                var applyAfterTax = (Helper.getBrowserConfig('tax/calculation/apply_after_discount'))?'0':'1';
                if(applyAfterTax == '0'){
                    max -= Helper.toNumber(this.TotalsModel.tax());
                }
                var useForShipping = this.getConfig('rewardpoints/spending/spend_for_shipping');
                if(useForShipping == '0'){
                    max -= Helper.toNumber(this.TotalsModel.shippingFee());
                }
                return max;
            },
            collectDiscountAmount: function(){
                var discount = 0;
                if(this.spendingRateValue() > 0 && this.applied() && this.appliedAmount() > 0){
                    discount = this.convertPointToMoney(this.appliedAmount());
                    var max = this.collectMaxTotalToDiscount();
                    if(discount > max){
                        discount = max;
                    }
                }
                this.discountAmount(discount);
            },
            /**
             * Load balance by customer from local
             * @param customerId
             */
            loadBalanceByCustomerId: function(customerId){
                var self = this;
                if(customerId) {
                    self.getCollection().addFieldToFilter('customer_id', customerId, 'eq');
                    self.getCollection().load().done(function (response) {
                        if (response.items && response.items.length > 0) {
                            var balance = parseFloat(response.items[0].point_balance);
                            self.balance(balance);
                            if(balance <= 0){
                                self.remove();
                            }else{
                                if(self.applied()){
                                    self.collect();
                                }
                            }
                        }else{
                            self.balance(0);
                            self.remove();
                        }
                    });
                }else{
                    if(self.visible() == true && self.applied() && self.applied() == true){
                        self.balance(0);
                        self.remove();
                    }
                }
            },
            /**
             * Update balance from local
             */
            updateStorageBalance: function(){
                var self = this;
                if(CartModel.customerId()) {
                    self.getCollection().addFieldToFilter('customer_id', CartModel.customerId(), 'eq');
                    self.getCollection().load().done(function (response) {
                        if (response.items && response.items.length > 0) {
                            self.balance(parseFloat(response.items[0].point_balance));
                        }else{
                            self.balance(0);
                            self.remove();
                        }
                    });
                }else{
                    if(self.visible() == true && self.applied() && self.applied() == true){
                        self.balance(0);
                        self.remove();
                    }
                }
            },
            /**
             * Load point balance from local by customer id
             * @param customerId
             */
            loadStorageBalanceByCustomerId: function(customerId){
                var self = this;
                if(customerId){
                    self.getCollection().addFieldToFilter('customer_id', customerId, 'eq');
                    self.getCollection().load().done(function (response) {
                        if (response.items && response.items.length > 0) {
                            self.balance(parseFloat(response.items[0].point_balance));
                        }else{
                            self.balance(0);
                        }
                    });
                }else{
                    self.balance(0);
                }
            },
            /**
             * Save point balance to local
             * @param customerId
             * @param balance
             */
            saveBalance: function(customerId, balance){
                if(customerId) {
                    var self = this;
                    self.getCollection().addFieldToFilter('customer_id', customerId, 'eq');
                    self.getCollection().load().done(function (response) {
                        var data = {};
                        if (response.items && response.items.length > 0) {
                            data = response.items[0];
                            data.point_balance = balance;
                        }else{
                            data.customer_id = customerId;
                            data.point_balance = balance;
                        }
                        self.setData(data).save();
                    });
                }
            },
            /**
             * add point to balance on local
             * @param customerId
             * @param amount
             */
            addPoint: function(customerId, amount){
                if(customerId) {
                    var self = this;
                    self.getCollection().addFieldToFilter('customer_id', customerId, 'eq');
                    self.getCollection().load().done(function (response) {
                        var data = {};
                        if (response.items && response.items.length > 0) {
                            data = response.items[0];
                            data.point_balance += amount;
                            self.setData(data).save();
                        }
                    });
                }
            },
            /**
             * remove point from balance on local
             * @param customerId
             * @param amount
             */
            removePoint: function(customerId, amount){
                if(customerId) {
                    var self = this;
                    self.getCollection().addFieldToFilter('customer_id', customerId, 'eq');
                    self.getCollection().load().done(function (response) {
                        var data = {};
                        if (response.items && response.items.length > 0) {
                            data = response.items[0];
                            data.point_balance -= amount;
                            self.setData(data).save();
                        }
                    });
                }
            },
            /**
             * Reset discount per item
             */
            reset: function(){
                var self = this;
                ko.utils.arrayForEach(CartData.items(), function (item) {
                    item.item_point_discount(0);
                    item.item_base_point_discount(0);
                });
            },
            /**
             * Process discount per item
             * @param cartBaseTotalAmount
             */
            process: function(cartBaseTotalAmount){
                var self = this;
                if(cartBaseTotalAmount > 0){
                    var maxAmount = CartData.getMaxDiscountAmount();
                    var itemsAmountTotal = (cartBaseTotalAmount > maxAmount)?maxAmount:cartBaseTotalAmount;
                    var amountApplied = 0;
                    ko.utils.arrayForEach(CartData.items(), function (item, index) {
                        var maxAmountItem = CartData.getMaxItemDiscountAmount(item.item_id());
                        var discountPercent = maxAmountItem/maxAmount;
                        var item_base_amount = (index == CartData.items().length - 1)?(itemsAmountTotal - amountApplied):itemsAmountTotal*discountPercent;
                        amountApplied += item_base_amount;
                        var item_amount = Helper.convertPrice(item_base_amount);
                        item.item_base_point_discount(item_base_amount);
                        item.item_point_discount(item_amount);
                        item.item_point_spent(self.convertMoneyToPoint(item_base_amount));
                        if(self.earningPoint() > 0){
                            var itemEarningPoint = parseFloat(item.row_total()) * parseFloat(self.earningRateValue());
                            item.item_point_earn(self.roundEarning(itemEarningPoint));
                        }
                    });
                }else{
                    self.reset();
                }
            },
            /**
             * Collect discount per item
             */
            collect: function(){
                var amount = (this.appliedAmount())?this.appliedAmount():0;
                this.currentAmount(amount);
                this.apply();
            },

            /**
             * Collect spending rate and earning rate
             */
            collectRates: function() {
                var self = this;
                self.earningRateValue(0);
                self.spendingRateValue(0);
                var earningRates = [];
                var spendingRates = [];
                ko.utils.arrayForEach(self.rates(), function (rate, index) {
                    if(self.canApplyRate(rate, CartModel.customerGroup())) {
                        if(rate.direction === self.TYPE_POINT_TO_MONEY) {
                            var value = parseFloat(rate.money) / parseFloat(rate.points);
                            spendingRates.push({
                                rate:rate,
                                value:value,
                                sort_order:parseFloat(rate.sort_order),
                            });
                        }
                        if(rate.direction === self.TYPE_MONEY_TO_POINT) {
                            var value = parseFloat(rate.points) / parseFloat(rate.money);
                            earningRates.push({
                                rate:rate,
                                value:value,
                                sort_order:parseFloat(rate.sort_order),
                            });
                        }
                    }
                });
                if(earningRates.length > 0){
                    earningRates.sort(self.sortBy("sort_order"));
                    self.earningRateValue(earningRates[0].value);
                }
                if(spendingRates.length > 0){
                    spendingRates.sort(self.sortBy("sort_order"));
                    self.spendingRateValue(spendingRates[0].value);
                }
            },
            /**
             * Validate customer group allowed to use
             * @param rate
             * @param customerGroupId
             * @returns {boolean}
             */
            canApplyRate: function(rate, customerGroupId) {
                if(rate && rate.customer_group_ids){
                    var groups = rate.customer_group_ids;
                    groups = groups.split(',');
                    if(typeof customerGroupId == "undefined" || !customerGroupId) {
                        return false;
                    }
                    if(groups.indexOf(this.CUSTOMER_GROUP_ALL) > -1) {
                        return true;
                    }
                    if(groups.indexOf(String(customerGroupId)) > -1) {
                        return true;
                    }
                }
                return false;
            },
            /**
             * Round earning point
             * @param point
             * @returns {*}
             */
            roundEarning: function(point){
                var roundMethod = this.getConfig('rewardpoints/earning/rounding_method');
                switch(roundMethod){
                    case 'round':
                        point = Math.round(point);
                        break;
                    case 'ceil':
                        point = Math.ceil(point);
                        break;
                    case 'floor':
                        point = Math.floor(point);
                        break;
                }
                return point;
            },
            /**
             * Collect total can earn point
             * @param point
             * @returns {*}
             */
            collectTotalToEarn: function(){
                var earnByShipping = this.getConfig('rewardpoints/earning/by_shipping');
                var earnByTax = this.getConfig('rewardpoints/earning/by_tax');
                var earnWhenSpend = this.getConfig('rewardpoints/earning/earn_when_spend');
                var cancelState = this.getConfig('rewardpoints/earning/order_cancel_state');
                var earnWhenInvoice = this.getConfig('rewardpoints/earning/order_invoice');
                var total = this.TotalsModel.grandTotal();
                if(earnByShipping == '0' && this.TotalsModel.shippingFee()){
                    total -= this.TotalsModel.shippingFee();
                }
                if(earnByTax == '0' && this.TotalsModel.tax()){
                    total -= this.TotalsModel.tax()
                }
                if(earnWhenSpend == '0' && this.applied() && this.appliedAmount() >0){
                    total = 0;
                }
                return total;
            },
            /**
             * Convert point to money
             * @param point
             * @returns {number}
             */
            convertPointToMoney: function(point){
                return parseFloat(point) * parseFloat(this.spendingRateValue())
            },
            /**
             * Convert money to point
             * @param discount
             * @returns {number}
             */
            convertMoneyToPoint: function(discount){
                return (this.spendingRateValue() > 0)?Math.ceil(parseFloat(discount) / parseFloat(this.spendingRateValue())):0;
            },
            /**
             * Function use to sort json array
             * @param prop
             * @returns {Function}
             */
            sortBy: function(prop){
                return function(a,b){
                    if( a[prop] > b[prop]){
                        return 1;
                    }else if( a[prop] < b[prop] ){
                        return -1;
                    }
                    return 0;
                }
            }
        });
    }
);
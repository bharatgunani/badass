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
        'Magestore_Webpos/js/model/checkout/cart/discountpopup',
        'Magestore_Webpos/js/helper/general'
    ],
    function ($,ko, modelAbstract, DiscountModel, Helper) {
        "use strict";
        return modelAbstract.extend({
            initialize: function () {
                this._super();
                this.CUSTOM_PRICE_CODE = "price";
                this.CUSTOM_DISCOUNT_CODE = "discount";
                this.FIXED_AMOUNT_CODE = "$";
                this.PERCENTAGE_CODE = "%";
                this.APPLY_TAX_ON_CUSTOMPRICE = "0";
                this.APPLY_TAX_ON_ORIGINALPRICE = "1";
                this.itemFields = [
                    'product_id','product_name','item_id','tier_price','maximum_qty','minimum_qty','qty_increment',
                    'qty','unit_price','has_custom_price','custom_type','custom_price_type','custom_price_amount','image_url',
                    'super_attribute','super_group','options','bundle_option','bundle_option_qty','is_out_of_stock','row_total',
                    'tax_class_id','is_virtual', 'qty_to_ship', 'tax_amount', 'tax_rate','item_base_discount_amount',
                    'item_discount_amount', 'item_base_credit_amount', 'item_credit_amount'
                ];

                /* S: Define the init fields - use to get data for item object */
                this.initFields = [
                    'product_id','product_name','item_id','tier_price','maximum_qty','minimum_qty','qty_increment',
                    'qty','unit_price','has_custom_price','custom_type','custom_price_type','custom_price_amount','image_url',
                    'super_attribute','super_group','options','bundle_option','bundle_option_qty','is_out_of_stock',
                    'tax_class_id','is_virtual', 'qty_to_ship', 'tax_rate', 'sku', 'product_type', 'child_id', 
                    'options_label', 'stocks', 'stock','id','type_id','bundle_childs_qty','item_base_discount_amount',
                    'item_discount_amount'
                ];
                if(Helper.isStoreCreditEnable()) {
                    this.initFields.push('item_credit_amount');
                    this.initFields.push('item_base_credit_amount');
                }
                if(Helper.isRewardPointsEnable()) {
                    this.initFields.push('item_point_earn');
                    this.initFields.push('item_point_spent');
                    this.initFields.push('item_point_discount');
                    this.initFields.push('item_base_point_discount');
                }
                if(Helper.isGiftCardEnable()) {
                    this.initFields.push('item_giftcard_discount');
                    this.initFields.push('item_base_giftcard_discount');
                }
                /* E: Define the init fields */
            },
            init: function(data){
                var self = this;
                self.id = (typeof data.id != "undefined")?ko.observable(data.id):ko.observable();
                self.product_id = (typeof data.product_id != "undefined")?ko.observable(data.product_id):ko.observable();
                self.product_name = (typeof data.product_name != "undefined")?ko.observable(data.product_name):ko.observable();
                self.type_id = (typeof data.type_id != "undefined")?ko.observable(data.type_id):ko.observable();
                
                self.item_id = (typeof data.item_id != "undefined")?ko.observable(data.item_id):ko.observable();
                self.tier_prices = (typeof data.tier_prices != "undefined")?ko.observable(data.tier_prices):ko.observable();
                self.maximum_qty = (typeof data.maximum_qty != "undefined")?ko.observable(data.maximum_qty):ko.observable();
                self.minimum_qty = (typeof data.minimum_qty != "undefined")?ko.observable(data.minimum_qty):ko.observable();
                self.qty_increment = (typeof data.qty_increment != "undefined")?ko.observable(data.qty_increment):ko.observable(1);
                self.qty = (typeof data.qty != "undefined")?ko.observable(data.qty):ko.observable();
                self.qty_to_ship = (typeof data.qty_to_ship != "undefined")?ko.observable(data.qty_to_ship):ko.observable(0);
                self.unit_price = (typeof data.unit_price != "undefined")?ko.observable(data.unit_price):ko.observable(0);
                self.has_custom_price = (typeof data.has_custom_price != "undefined")?ko.observable(data.has_custom_price):ko.observable(false);
                self.custom_type = (typeof data.custom_type != "undefined")?ko.observable(data.custom_type):ko.observable();
                self.custom_price_type = (typeof data.custom_price_type != "undefined")?ko.observable(data.custom_price_type):ko.observable();
                self.custom_price_amount = (typeof data.custom_price_amount != "undefined")?ko.observable(data.custom_price_amount):ko.observable();
                self.image_url = (typeof data.image_url != "undefined")?ko.observable(data.image_url):ko.observable();
                self.super_attribute = (typeof data.super_attribute != "undefined")?ko.observable(data.super_attribute):ko.observable();
                self.super_group = (typeof data.super_group != "undefined")?ko.observable(data.super_group):ko.observable();
                self.options = (typeof data.options != "undefined")?ko.observable(data.options):ko.observable();
                self.bundle_option = (typeof data.bundle_option != "undefined")?ko.observable(data.bundle_option):ko.observable();
                self.bundle_option_qty = (typeof data.bundle_option_qty != "undefined")?ko.observable(data.bundle_option_qty):ko.observable();
                self.is_out_of_stock = (typeof data.is_out_of_stock != "undefined")?ko.observable(data.is_out_of_stock):ko.observable(false);
                self.tax_class_id = (typeof data.tax_class_id != "undefined")?ko.observable(data.tax_class_id):ko.observable();
                self.is_virtual = (typeof data.is_virtual != "undefined")?ko.observable(data.is_virtual):ko.observable(false);
                self.tax_rate = (typeof data.tax_rate != "undefined")?ko.observable(data.tax_rate):ko.observable(0);
                
                self.sku = (typeof data.sku != "undefined")?ko.observable(data.sku):ko.observable();
                self.product_type = (typeof data.product_type != "undefined")?ko.observable(data.product_type):ko.observable();
                self.child_id = (typeof data.child_id != "undefined")?ko.observable(data.child_id):ko.observable();
                self.options_label = (typeof data.options_label != "undefined")?ko.observable(data.options_label):ko.observable();
                self.tier_price = (typeof data.tier_price != "undefined")?ko.observable(data.tier_price):ko.observable();
                self.stock = (typeof data.stock != "undefined")?ko.observable(data.stock):ko.observable();
                self.stocks = (typeof data.stocks != "undefined")?ko.observable(data.stocks):ko.observable();
                self.bundle_childs_qty = (typeof data.bundle_childs_qty != "undefined")?ko.observable(data.bundle_childs_qty):ko.observable();
                self.item_discount_amount = (typeof data.item_discount_amount != "undefined")?ko.observable(data.item_discount_amount):ko.observable();
                self.item_base_discount_amount = (typeof data.item_base_discount_amount != "undefined")?ko.observable(data.item_base_discount_amount):ko.observable();

                /* S: Integration custom discount per item - define variale to store the data */
                if(Helper.isStoreCreditEnable()) {
                    self.credit_price_amount = (typeof data.credit_price_amount != "undefined") ? ko.observable(data.credit_price_amount) : ko.observable();
                    self.amount = (typeof data.amount != "undefined") ? ko.observable(data.amount) : ko.observable();
                    self.item_credit_amount = (typeof data.item_credit_amount != "undefined") ? ko.observable(data.item_credit_amount) : ko.observable();
                    self.item_base_credit_amount = (typeof data.item_base_credit_amount != "undefined") ? ko.observable(data.item_base_credit_amount) : ko.observable();
                }
                if(Helper.isRewardPointsEnable()) {
                    self.item_point_earn = (typeof data.item_point_earn != "undefined") ? ko.observable(data.item_point_earn) : ko.observable();
                    self.item_point_spent = (typeof data.item_point_spent != "undefined") ? ko.observable(data.item_point_spent) : ko.observable();
                    self.item_point_discount = (typeof data.item_point_discount != "undefined") ? ko.observable(data.item_point_discount) : ko.observable();
                    self.item_base_point_discount = (typeof data.item_base_point_discount != "undefined") ? ko.observable(data.item_base_point_discount) : ko.observable();
                }
                if(Helper.isGiftCardEnable()) {
                    self.item_giftcard_discount = (typeof data.item_giftcard_discount != "undefined") ? ko.observable(data.item_giftcard_discount) : ko.observable();
                    self.item_base_giftcard_discount = (typeof data.item_base_giftcard_discount != "undefined") ? ko.observable(data.item_base_giftcard_discount) : ko.observable();
                }
                /* E: Integration custom discount per item */

                if(self.maximum_qty() && self.qty() > self.maximum_qty()){
                    self.qty(Helper.toNumber(self.maximum_qty()));
                    Helper.alert({
                        priority: "warning",
                        title: "Warning",
                        message: self.product_name()+Helper.__(" has maximum quantity allow in cart is ")+Helper.toNumber(self.maximum_qty())
                    });
                }
                
                if(self.minimum_qty() && self.qty() < self.minimum_qty()){
                    self.qty(Helper.toNumber(self.minimum_qty()));
                    Helper.alert({
                        priority: "warning",
                        title: "Warning",
                        message: self.product_name()+Helper.__(" has minimum quantity allow in cart is ")+Helper.toNumber(self.minimum_qty())
                    });
                }
                if(!self.item_price) {
                    self.item_price = ko.pureComputed(function () {
                        var itemPrice = (self.tier_price()) ? self.tier_price() : self.unit_price();
                        var unitPrice = itemPrice;
                        var discountPercentage = 0;
                        var maximumPercent = Helper.toNumber(DiscountModel.maximumPercent());
                        var customAmount = (self.custom_price_type() == self.FIXED_AMOUNT_CODE) ? Helper.toBasePrice(self.custom_price_amount()) : self.custom_price_amount();
                        var validAmount = customAmount;
                        if (self.has_custom_price() == true && customAmount >= 0 && self.custom_price_type()) {
                            if (self.custom_type() == self.CUSTOM_PRICE_CODE) {
                                itemPrice = (self.custom_price_type() == self.FIXED_AMOUNT_CODE) ?
                                    customAmount :
                                    (customAmount * unitPrice / 100);
                                if (self.custom_price_type() == self.FIXED_AMOUNT_CODE) {
                                    discountPercentage = (100 - itemPrice / unitPrice * 100);
                                } else {
                                    discountPercentage = customAmount;
                                }
                                if (maximumPercent && discountPercentage > maximumPercent) {
                                    if (self.custom_price_type() == self.FIXED_AMOUNT_CODE) {
                                        validAmount = unitPrice - unitPrice * maximumPercent / 100;
                                    }
                                }
                            } else {
                                if (self.custom_type() == self.CUSTOM_DISCOUNT_CODE) {
                                    itemPrice = (self.custom_price_type() == self.FIXED_AMOUNT_CODE) ?
                                        (unitPrice - customAmount) :
                                        (unitPrice - customAmount * unitPrice / 100);
                                    if (self.custom_price_type() == self.FIXED_AMOUNT_CODE) {
                                        discountPercentage = (customAmount / unitPrice * 100);
                                    } else {
                                        discountPercentage = customAmount;
                                    }
                                }
                                if (maximumPercent && discountPercentage > maximumPercent) {
                                    if (self.custom_price_type() == self.FIXED_AMOUNT_CODE) {
                                        validAmount = unitPrice * maximumPercent / 100;
                                    }
                                }
                            }
                        }
                        if (maximumPercent && discountPercentage > maximumPercent) {
                            if (self.custom_price_type() == self.PERCENTAGE_CODE) {
                                self.custom_price_amount(maximumPercent);
                            } else {
                                self.custom_price_amount(Helper.convertPrice(validAmount));
                            }
                            itemPrice = unitPrice - unitPrice * maximumPercent / 100;
                            Helper.alert({
                                priority: "warning",
                                title: "Warning",
                                message: Helper.__(" You are able to apply discount under ") + maximumPercent + "% " + Helper.__("only")
                            });
                        }
                        return (itemPrice > 0) ? itemPrice : 0;
                    });
                }
                if(!self.row_total) {
                    self.row_total = ko.pureComputed(function () {
                        var rowTotal = self.qty() * self.item_price();
                        if(Helper.isProductPriceIncludesTax()){
                            rowTotal = rowTotal / (1 + self.tax_rate()/100);
                        }
                        var discountItem = 0;
                        var apply_after_discount = window.webposConfig['tax/calculation/apply_after_discount'];
                        if (apply_after_discount == 1 && self.item_base_discount_amount() > 0) {
                            discountItem += self.item_base_discount_amount();
                        }
                        if (apply_after_discount == 1 && Helper.isProductPriceIncludesTax()) {
                            rowTotal += discountItem - discountItem / (1 + self.tax_rate()/100);
                        }

                        return Helper.correctPrice(rowTotal);
                    });
                }
                if(!self.row_total_without_discount) {
                    self.row_total_without_discount = ko.pureComputed(function () {
                        var rowTotal = self.qty() * self.unit_price();
                        if(Helper.isProductPriceIncludesTax()){
                            rowTotal = rowTotal / (1 + self.tax_rate()/100);
                        }
                        return Helper.correctPrice(rowTotal);
                    });
                }
                if(!self.tax_amount) {
                    self.tax_amount = ko.pureComputed(function () {
                        var rowTotal = self.qty() * self.item_price();
                        var total;
                        if(Helper.isProductPriceIncludesTax()){
                            rowTotal = rowTotal / (1 + self.tax_rate()/100);
                            total =rowTotal;
                        } else {
                            total = self.row_total();
                        }

                        /* temporary disable this functionality, because magento core is having a bug in here, currently they don't check this setting when creating order from backend also.
                         * ------------- *
                         var apply_tax_on = window.webposConfig['tax/calculation/apply_tax_on'];
                         if(apply_tax_on == self.APPLY_TAX_ON_ORIGINALPRICE){
                         total = self.row_total_without_discount();
                         }
                         * ------------- *
                         */

                        var discountItem = 0;
                        var apply_after_discount = window.webposConfig['tax/calculation/apply_after_discount'];
                        if (apply_after_discount == 1 && self.item_base_discount_amount() > 0) {
                            discountItem += self.item_base_discount_amount();
                        }

                        /* S: Integration custom discount per item - recalculate tax - tax after discount */
                        var allConfig = Helper.getBrowserConfig('plugins_config');
                        if(Helper.isStoreCreditEnable() && allConfig['os_store_credit']){
                            var configs = allConfig['os_store_credit'];
                            if(configs['customercredit/spend/tax'] && configs['customercredit/spend/tax'] == '0'){
                                if (self.item_base_credit_amount() > 0) {
                                    discountItem += self.item_base_credit_amount();
                                }
                            }
                        }
                        if(Helper.isRewardPointsEnable()  && apply_after_discount == 1){
                            if(self.item_base_point_discount() > 0){
                                discountItem += self.item_base_point_discount();
                            }
                        }
                        if(Helper.isGiftCardEnable()  &&  allConfig['os_gift_card']){
                            var configs = allConfig['os_gift_card'];
                            if(configs['giftvoucher/general/apply_after_tax'] == '0'){
                                if (self.item_base_giftcard_discount() > 0) {
                                    discountItem += self.item_base_giftcard_discount();
                                }
                            }

                        }
                        /* E: Integration custom discount per item */

                        total -= (Helper.correctPrice(discountItem) > total)?total:Helper.correctPrice(discountItem);
                        var tax = self.tax_rate() * total / 100;
                        if (apply_after_discount == 1 && Helper.isProductPriceIncludesTax()) {
                            var price_without_discount = self.row_total_without_discount();
                            var rate = self.tax_rate();
                            var priceTotal = price_without_discount + price_without_discount * (rate / 100) - discountItem;
                            // total -= (Helper.correctPrice(discountItem) > total)?total:Helper.correctPrice(discountItem);
                            var tax = priceTotal - priceTotal / (1 + (self.tax_rate()  / 100));
                            total = priceTotal;
                        }
                        return Helper.correctPrice(tax);
                    });
                }
                if(!self.tax_amount_converted) {
                    self.tax_amount_converted = ko.pureComputed(function () {
                        return Helper.convertPrice(self.tax_amount());
                    });
                }
                if(!self.row_total_include_tax) {
                    self.row_total_include_tax = ko.pureComputed(function () {
                        var rowTotal = self.qty() * self.item_price();
                        if(!Helper.isProductPriceIncludesTax()){
                            rowTotal += self.tax_amount();
                        }
                        return Helper.correctPrice(rowTotal);
                    });
                }
                if(!self.row_total_formated) {
                    self.row_total_formated = ko.pureComputed(function () {
                        var displayIncludeTax = Helper.isCartDisplayIncludeTax('price');
                        var rowTotal = self.row_total();
                        if(displayIncludeTax){
                            rowTotal = self.row_total_include_tax();
                        }
                        return Helper.convertAndFormatPrice(rowTotal);
                    });
                }
                if(!self.original_row_total_formated) {
                    self.original_row_total_formated = ko.pureComputed(function () {
                        var displayIncludeTax = Helper.isCartDisplayIncludeTax('price');
                        var rowTotal = self.qty() * self.unit_price();
                        if(Helper.isProductPriceIncludesTax() && !displayIncludeTax){
                            rowTotal = rowTotal / (1 + self.tax_rate()/100);
                        }
                        return "Reg. " + Helper.convertAndFormatPrice(rowTotal);
                    });
                }
                if(!self.show_original_price) {
                    self.show_original_price = ko.pureComputed(function () {
                        return (self.has_custom_price() == true && self.custom_price_amount() >= 0 && self.custom_price_type());
                    });
                }
            },
            setData: function(key,value){
                var self = this;
                if(typeof self[key] != "undefined"){
                    if(key == "qty"){
                        if(self.maximum_qty() && value > self.maximum_qty()){
                            value = Helper.toNumber(self.maximum_qty());
                            Helper.alert({
                                priority: "warning",
                                title: "Warning",
                                message: self["product_name"]()+Helper.__(" has maximum quantity allow in cart is ")+value
                            });
                        }
                        if(self.minimum_qty() && value < self.minimum_qty()){
                            value = Helper.toNumber(self.minimum_qty());
                            Helper.alert({
                                priority: "warning",
                                title: "Warning",
                                message: self["product_name"]()+Helper.__(" has minimum quantity allow in cart is ")+value
                            });
                        }
                    }
                    self[key](value); 
                }
            },
            getData: function(key){
                var self = this;
                var data = {};
                if(typeof key != "undefined"){
                    data = self[key]();
                }else{
                    var data = {};
                    $.each(this.initFields, function(){
                        data[this] = self[this]();
                    });
                }
                return data;
            },
            getInfoBuyRequest: function(){
                var infobuy = {};
                infobuy.id = this.product_id();
                infobuy.qty = this.qty();
                infobuy.qty_to_ship = this.qty_to_ship();

                if(this.product_id() == "customsale"){
                    infobuy.is_custom_sale = true;
                }
                
                if(this.has_custom_price() == true && this.custom_price_amount() >= 0){
                    infobuy.custom_price = Helper.convertPrice(this.item_price());
                }
                if(this.super_attribute()){
                    infobuy.super_attribute = this.super_attribute();
                }
                if(this.options()){
                    infobuy.options = this.options();
                }else{
                    if(this.product_id() == "customsale"){
                        infobuy.options = [
                            {code:"tax_class_id",value:this.tax_class_id()},
                            {code:"price",value:this.unit_price()},
                            {code:"is_virtual",value:this.is_virtual()},
                            {code:"name",value:this.product_name()}
                        ];
                    }
                }
                if(this.super_group()){
                    infobuy.super_group = this.super_group();
                }
                if(this.bundle_option() && this.bundle_option_qty()){
                    infobuy.bundle_option = this.bundle_option();
                    infobuy.bundle_option_qty = this.bundle_option_qty();
                }

                infobuy.extension_data = [
                    {key:"row_total",value:Helper.correctPrice(Helper.convertPrice(this.row_total()))},
                    {key:"base_row_total",value:Helper.correctPrice(this.row_total())},
                    {key:"price",value:Helper.correctPrice(Helper.convertPrice(this.item_price()))},
                    {key:"base_price",value:Helper.correctPrice(this.item_price())},
                    {key:"discount_amount",value:Helper.correctPrice(this.item_discount_amount())},
                    {key:"base_discount_amount",value:Helper.correctPrice(this.item_base_discount_amount())},
                    {key:"tax_amount",value:Helper.correctPrice(this.tax_amount_converted())},
                    {key:"base_tax_amount",value:Helper.correctPrice(this.tax_amount())},
                    {key:"custom_tax_class_id",value:Helper.correctPrice(this.tax_class_id())}
                ];

                /* S: Integration custom discount per item - add item discount data to save on server database */
                if(Helper.isStoreCreditEnable()){
                    infobuy.amount = this.amount();
                    infobuy.credit_price_amount = this.credit_price_amount();
                    infobuy.extension_data.push({
                        key: "customercredit_discount",
                        value: Helper.correctPrice(this.item_credit_amount())
                    });
                    infobuy.extension_data.push({
                        key: "base_customercredit_discount",
                        value: Helper.correctPrice(this.item_base_credit_amount())
                    });
                    if(this.credit_price_amount()){
                        infobuy.extension_data.push({
                            key: "original_price",
                            value: Helper.convertPrice(this.credit_price_amount())
                        });
                        infobuy.extension_data.push({
                            key: "base_original_price",
                            value: this.credit_price_amount()
                        });
                    }
                    if(!infobuy.options){
                        infobuy.options = [];
                    }
                    infobuy.options.push({
                        code: "credit_price_amount",
                        value: this.credit_price_amount()
                    });
                    infobuy.options.push({
                        code: "amount",
                        value: this.amount()
                    });
                }
                if(Helper.isRewardPointsEnable()) {
                    infobuy.extension_data.push({
                        key: "rewardpoints_earn",
                        value: Helper.correctPrice(this.item_point_earn())
                    });
                    infobuy.extension_data.push({
                        key: "rewardpoints_spent",
                        value: Helper.correctPrice(this.item_point_spent())
                    });
                    infobuy.extension_data.push({
                        key: "rewardpoints_discount",
                        value: Helper.correctPrice(this.item_point_discount())
                    });
                    infobuy.extension_data.push({
                        key: "rewardpoints_base_discount",
                        value: Helper.correctPrice(this.item_base_point_discount())
                    });
                }
                if(Helper.isGiftCardEnable()) {
                    infobuy.extension_data.push({
                        key: "gift_voucher_discount",
                        value: Helper.correctPrice(this.item_giftcard_discount())
                    });
                    infobuy.extension_data.push({
                        key: "base_gift_voucher_discount",
                        value: Helper.correctPrice(this.item_base_giftcard_discount())
                    });
                }
                /* E: Integration custom discount per item  */

                return infobuy;
            },
            getDataForOrder: function(){
                var data = {
                    item_id:this.item_id(),
                    name:this.product_name(),
                    product_id:this.product_id(),
                    product_type:this.product_type(),
                    sku:this.sku(),
                    qty_canceled:0,
                    qty_invoiced:0,
                    qty_ordered:this.qty(),
                    qty_refunded:0,
                    qty_shipped:0,
                    discount_amount:Helper.correctPrice(this.item_discount_amount()),
                    base_discount_amount:Helper.correctPrice(this.item_base_discount_amount()),
                    original_price:Helper.convertPrice(this.unit_price()),
                    base_original_price:this.unit_price(),
                    tax_amount:Helper.convertPrice(this.tax_amount()),
                    base_tax_amount:this.tax_amount(),
                    price:Helper.convertPrice(this.item_price()),
                    base_price:this.item_price(),
                    row_total:Helper.convertPrice(this.row_total()),
                    base_row_total:this.row_total()
                };

                /* S: Integration custom discount per item - add item data for offline order */
                if(Helper.isStoreCreditEnable()) {
                    data.amount = this.amount();
                    data.credit_price_amount = this.credit_price_amount();
                    data.customercredit_discount = Helper.correctPrice(this.item_credit_amount());
                    data.base_customercredit_discount = Helper.correctPrice(this.item_base_credit_amount());
                    if(this.credit_price_amount()){
                        data.original_price = Helper.convertPrice(this.credit_price_amount());
                        data.base_original_price = this.credit_price_amount();
                    }
                }
                if(Helper.isRewardPointsEnable()) {
                    data.rewardpoints_earn = Helper.correctPrice(this.item_point_earn());
                    data.rewardpoints_spent = Helper.correctPrice(this.item_point_spent());
                    data.rewardpoints_discount = Helper.correctPrice(this.item_point_discount());
                    data.rewardpoints_base_discount = Helper.correctPrice(this.item_base_point_discount());
                }
                if(Helper.isGiftCardEnable()) {
                    data.gift_voucher_discount = Helper.correctPrice(this.item_giftcard_discount());
                    data.base_gift_voucher_discount = Helper.correctPrice(this.item_base_giftcard_discount());
                }
                /* E: Integration custom discount per item  */

                return data;
            }
        });
    }
);

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
        'Magestore_Webpos/js/model/checkout/cart/items',
        'Magestore_Webpos/js/model/checkout/cart/totals/total',
        'Magestore_Webpos/js/model/checkout/taxcalculator',
        'Magestore_Webpos/js/model/checkout/cart/discountpopup',
        'Magestore_Webpos/js/helper/staff',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/checkout/cart/data/cart'
    ],
    function ($, ko, modelAbstract, Items, Total, TaxCalculator, DiscountModel, Staff, Helper, CartData) {
        "use strict";

        Helper.observerEvent('recollect_totals', function(event, data){
            if(data.code){
                ko.utils.arrayForEach(CartData.totals(), function (total) {
                    if(total.code() !== data.code) {
                        if(total.actions() && total.actions().collect){
                            if(typeof total.actions().collect == 'function'){
                                total.actions().collect();
                            }
                        }
                    }
                });
            }
        });

        return modelAbstract.extend({
            totals: CartData.totals,
            buttons: ko.observableArray(),
            shippingData: ko.observable(),
            shippingFee: ko.observable(0),
            loadedTaxData: ko.observable(false),
            subtotal: ko.pureComputed(function () {
                var subtotal = 0;
                ko.utils.arrayForEach(CartData.items(), function (item) {
                    var convertedAmount = Helper.convertPrice(item.row_total());
                    subtotal += Helper.correctPrice(convertedAmount);
                });
                return Helper.toBasePrice(subtotal);
            }),
            subtotalIncludeTax: ko.pureComputed(function () {
                var subtotal = 0;
                ko.utils.arrayForEach(CartData.items(), function (item) {
                    var convertedAmount = Helper.convertPrice(item.row_total_include_tax());
                    subtotal += Helper.correctPrice(convertedAmount);
                });
                return Helper.toBasePrice(subtotal);
            }),
            additionalInfo: ko.observableArray(),
            SUBTOTAL_TOTAL_CODE: "subtotal",
            TAX_TOTAL_CODE: "tax",
            ADD_DISCOUNT_TOTAL_CODE: "add-discount",
            DISCOUNT_TOTAL_CODE: "discount",
            SHIPPING_TOTAL_CODE: "shipping",
            GRANDTOTAL_TOTAL_CODE: "grandtotal",
            BASE_TOTALS: [
                "subtotal",
                'tax',
                "add-discount",
                "discount",
                "shipping",
                "grandtotal"
            ],
            HOLD_BUTTON_CODE: "hold",
            CHECKOUT_BUTTON_CODE: "checkout",
            HOLD_BUTTON_TITLE: Helper.__("Hold"),
            CHECKOUT_BUTTON_TITLE: Helper.__("Checkout"),
            initialize: function () {
                this._super();
                var self = this;
                if(!self.grandTotalBeforeDiscount){
                    self.grandTotalBeforeDiscount = ko.pureComputed(function(){
                        var grandTotal = self.getPositiveTotal();
                        var negativeAmount = self.getNegativeTotal();
                        if (negativeAmount < 0) {
                            grandTotal += negativeAmount;
                        }
                        if(DiscountModel.appliedDiscount() == true || DiscountModel.appliedPromotion() == true) {
                            grandTotal += DiscountModel.calculateBaseAmount(grandTotal);
                        }
                        return grandTotal;
                    });
                }

                if(!self.tax) {
                    self.tax = ko.pureComputed(function () {
                        var tax = 0;
                        ko.utils.arrayForEach(CartData.items(), function (item) {
                            tax += item.tax_amount();
                        });
                        return tax;
                    });
                }

                if(!self.discountAmount) {
                    self.discountAmount = ko.pureComputed(function () {
                        var discountAmount = 0;
                        var grandTotal = 0;
                        if((DiscountModel.appliedDiscount() == true || DiscountModel.appliedPromotion() == true) && DiscountModel.cartBaseDiscountAmount() > 0 && self.positiveTotal() > 0) {
                            ko.utils.arrayForEach(self.getTotals(), function (total) {
                                if (
                                    total.code() !== self.DISCOUNT_TOTAL_CODE
                                    && total.code() !== self.GRANDTOTAL_TOTAL_CODE
                                    && total.value()
                                    && total.value() > 0
                                ) {
                                    var apply_after_discount = window.webposConfig['tax/calculation/apply_after_discount'];
                                    if (total.code() !== self.TAX_TOTAL_CODE) {
                                        grandTotal += parseFloat(total.value());
                                    }else{
                                        if (apply_after_discount != 1) {
                                            grandTotal += parseFloat(total.value());
                                        }
                                    }
                                }
                            });
                            grandTotal = Helper.convertPrice(grandTotal)
                            discountAmount = DiscountModel.calculateAmount(grandTotal);
                        }
                        return discountAmount;
                    });
                }

                if(!self.baseDiscountAmount) {
                    self.baseDiscountAmount = ko.pureComputed(function(){
                        var discountAmount = 0;
                        var baseGrandTotal = 0;
                        if((DiscountModel.appliedDiscount() == true || DiscountModel.appliedPromotion() == true) && DiscountModel.cartBaseDiscountAmount() > 0 && self.positiveTotal() > 0){
                            ko.utils.arrayForEach(self.getTotals(), function (total) {
                                if(
                                    total.code() !== self.DISCOUNT_TOTAL_CODE
                                    && total.code() !== self.GRANDTOTAL_TOTAL_CODE
                                    && total.value()
                                    && total.value() > 0
                                ) {
                                    var apply_after_discount = window.webposConfig['tax/calculation/apply_after_discount'];
                                    if (total.code() !== self.TAX_TOTAL_CODE) {
                                        baseGrandTotal += parseFloat(total.value());
                                    }else{
                                        if (apply_after_discount != 1) {
                                            baseGrandTotal += parseFloat(total.value());
                                        }
                                    }
                                }
                            });
                            discountAmount = DiscountModel.calculateBaseAmount(baseGrandTotal);
                        }
                        return discountAmount;
                    });
                }
                if(!self.negativeTotal) {
                    self.negativeTotal = ko.pureComputed(function () {
                        return self.getNegativeTotal();
                    });
                }
                if(!self.positiveTotal) {
                    self.positiveTotal = ko.pureComputed(function () {
                        return self.getPositiveTotal();
                    });
                }
                if(!self.grandTotal) {
                    self.grandTotal = ko.pureComputed(function () {
                        return self.getGrandTotal();
                    });
                }
                self.initButtons();
                self.initTotals();

                if (self.loadedTaxData() == false) {
                    self.initTaxData();
                    self.loadedTaxData(true);
                }
                if(!this.reinitObserver){
                    this.initObserver();
                }
                return this;
            },
            initObserver: function(){
                var self = this;
                self.reinitObserver = false;
            },
            getGrandTotal: function(){
                var grandTotal = this.positiveTotal();
                if (this.negativeTotal() < 0) {
                    grandTotal += this.negativeTotal();
                }
                return grandTotal;
            },
            getPositiveTotal: function(){
                var self = this;
                var grandTotal = 0;
                ko.utils.arrayForEach(self.getTotals(), function (total) {
                    if(total.code() !== self.GRANDTOTAL_TOTAL_CODE && total.value() && total.value() > 0) {
                        grandTotal += parseFloat(total.value());
                    }

                });
                return Helper.correctPrice(grandTotal);
            },
            getNegativeTotal: function(){
                var self = this;
                var grandTotal = 0;
                ko.utils.arrayForEach(self.getTotals(), function (total) {
                    if(total.code() !== self.GRANDTOTAL_TOTAL_CODE && total.value() && total.value() < 0) {
                        grandTotal += parseFloat(total.value());
                    }
                });
                return Helper.correctPrice(grandTotal);
            },
            initTaxData: function () {
                TaxCalculator().initData();
            },
            getButtons: function () {
                return this.buttons();
            },
            initButtons: function () {
                var self = this;
                if (self.isNewButton(self.HOLD_BUTTON_CODE)) {
                    var hold = {
                        code: self.HOLD_BUTTON_CODE,
                        cssClass: "hold btn-cl-cfg-other",
                        title: self.HOLD_BUTTON_TITLE
                    };
                    self.buttons.push(hold);
                }
                if (self.isNewButton(self.CHECKOUT_BUTTON_CODE)) {
                    var checkout = {
                        code: self.CHECKOUT_BUTTON_CODE,
                        cssClass: "checkout btn-cl-cfg-active",
                        title: self.CHECKOUT_BUTTON_TITLE
                    };
                    self.buttons.push(checkout);
                }
            },
            isNewButton: function (buttonCode) {
                var button = ko.utils.arrayFirst(this.buttons(), function (button) {
                    return button.code == buttonCode;
                });
                return (button) ? false : true;
            },
            getTotals: function () {
                return CartData.totals();
            },
            addTotal: function (data) {
                if (this.isNewTotal(data.code)) {
                    var total = new Total();
                    total.init(data);
                    this.totals.push(total);
                } else {
                    this.setTotalData(data.code, "value", data.value);
                    if(data.includeTaxValue){
                        this.setTotalData(data.code, "includeTaxValue", data.includeTaxValue);
                    }
                }
            },
            setTotalData: function (totalCode, key, value) {
                var total = this.getTotal(totalCode);
                if (total != false) {
                    total.setData(key, value);
                }
            },
            isNewTotal: function (totalCode) {
                var total = ko.utils.arrayFirst(CartData.totals(), function (total) {
                    return total.code() == totalCode;
                });
                return (total) ? false : true;
            },
            getTotalValue: function (totalCode) {
                var value = "";
                var total = this.getTotal(totalCode);
                if (total !== false) {
                    value = total.value();
                }
                return value;
            },
            getTotal: function (totalCode) {
                var totalFound = ko.utils.arrayFirst(CartData.totals(), function (total) {
                    return total.code() == totalCode;
                });
                return (totalFound) ? totalFound : false;
            },
            updateTotal: function (totalCode, data) {
                var totals = ko.utils.arrayMap(CartData.totals(), function (total) {
                    if (total.code() == totalCode) {
                        if (typeof data.isVisible != "undefined") {
                            total.isVisible(data.isVisible);
                        }
                        if (typeof data.value != "undefined") {
                            total.value(data.value);
                        }
                        if (typeof data.title != "undefined") {
                            total.title(data.title);
                        }
                    }
                    return total;
                });
                this.totals(totals);
            },
            initTotals: function () {
                var self = this;
                this.addTotal({
                    code: self.SUBTOTAL_TOTAL_CODE,
                    cssClass: "subtotal",
                    title: Helper.__("Subtotal"),
                    value: this.subtotal(),
                    includeTaxValue: this.subtotalIncludeTax(),
                    displayIncludeTax: Helper.isCartDisplayIncludeTax('subtotal'),
                    isVisible: true,
                    removeAble: false
                });
                var canUseDiscount = false;
                if(
                    Staff.isHavePermission("Magestore_Webpos::all_discount") ||
                    Staff.isHavePermission("Magestore_Webpos::apply_coupon") ||
                    Staff.isHavePermission("Magestore_Webpos::apply_discount_per_cart")
                ){
                    canUseDiscount = true;
                }
                this.addTotal({
                    code: self.ADD_DISCOUNT_TOTAL_CODE,
                    cssClass: "add-discount",
                    title: Helper.__("Add Discount"),
                    value: "",
                    isVisible: ((this.baseDiscountAmount() > 0 && canUseDiscount) || !canUseDiscount) ? false : true,
                    removeAble: false
                });
                this.addTotal({
                    code: self.DISCOUNT_TOTAL_CODE,
                    cssClass: "discount",
                    title: Helper.__("Discount"),
                    value: -this.baseDiscountAmount(),
                    isVisible: (this.baseDiscountAmount() > 0 && canUseDiscount) ? true : false,
                    removeAble: true,
                    actions: {
                        remove: 'removeDiscount',
                        collect: $.proxy(DiscountModel.collect, DiscountModel)
                    }
                });
                this.addTotal({
                    code: self.SHIPPING_TOTAL_CODE,
                    cssClass: "shipping",
                    title: Helper.__("Shipping"),
                    value: this.shippingFee(),
                    isVisible: (this.shippingFee() > 0) ? true : false,
                    removeAble: false
                });
                this.addTotal({
                    code: self.TAX_TOTAL_CODE,
                    cssClass: "tax",
                    title: Helper.__("Tax"),
                    value: this.tax(),
                    isVisible: true,
                    removeAble: false
                });
                this.addTotal({
                    code: self.GRANDTOTAL_TOTAL_CODE,
                    cssClass: "total",
                    title: Helper.__("Total"),
                    value: this.grandTotal(),
                    isVisible: true,
                    removeAble: false
                });
            },
            updateShippingAmount: function (shippingAmount) {
                var hasShipping = (shippingAmount > 0 || this.shippingData()) ? true : false;
                this.updateTotal(this.SHIPPING_TOTAL_CODE, {
                    isVisible: hasShipping,
                    value: shippingAmount
                });
                this.shippingFee(shippingAmount);
            },
            updateDiscountTotal: function () {
                var canUseDiscount = false;
                if(
                    Staff.isHavePermission("Magestore_Webpos::all_discount") ||
                    Staff.isHavePermission("Magestore_Webpos::apply_coupon") ||
                    Staff.isHavePermission("Magestore_Webpos::apply_discount_per_cart")
                ){
                    canUseDiscount = true;
                }
                var name = (DiscountModel.appliedPromotion() == true)?DiscountModel.couponCode():DiscountModel.cartDiscountName();
                var hasDiscount = (this.baseDiscountAmount() > 0 && canUseDiscount) ? true : false;
                var title = (name != "")?Helper.__("Discount ")+"( "+name+" )":Helper.__("Discount");
                this.updateTotal(this.DISCOUNT_TOTAL_CODE, {
                    title: title,
                    isVisible: hasDiscount,
                    value: -this.baseDiscountAmount()
                });
                this.updateTotal(this.ADD_DISCOUNT_TOTAL_CODE, {
                    isVisible: (!hasDiscount && canUseDiscount)
                });
            },
            collectShippingTotal: function () {
                var shippingFee = 0;
                var shippingData = this.shippingData();
                if (shippingData && typeof shippingData.price != "undefined") {
                    shippingFee = shippingData.price;
                    if (typeof shippingData.price_type != "undefined") {
                        shippingFee = (shippingData.price_type == "I") ? (shippingFee * Items.totalShipableItems()) : shippingFee;
                    }
                }
                shippingFee = Helper.toNumber(shippingFee);
                var hasShipping = (shippingFee > 0 || this.shippingData()) ? true : false;
                this.updateTotal(this.SHIPPING_TOTAL_CODE, {
                    isVisible: hasShipping,
                    value: shippingFee
                });
                this.shippingFee(shippingFee);
            },
            collectTaxTotal: function () {
                var tax = 0;
                if (CartData.items().length > 0) {
                    $.each(CartData.items(), function () {
                        tax += this.tax_amount();
                    });
                }
                this.updateTotal(this.TAX_TOTAL_CODE, {
                    isVisible: true,
                    value: tax
                });
            },
            getDisplayTotals: function() {
                var displayTotals = ko.observableArray();
                var self = this;
                self.initTotals();
                $.each(this.getTotals(), function () {
                    if(this.code() !== self.GRANDTOTAL_TOTAL_CODE
                            && this.code() !== self.TAX_TOTAL_CODE) {
                        displayTotals.push(this);
                    }
                });
                displayTotals.push(this.getTotal(self.TAX_TOTAL_CODE));
                displayTotals.push(this.getTotal(self.GRANDTOTAL_TOTAL_CODE));
                return displayTotals;
            },
            getGrandTotalWithoutCustomTotal: function(totalCode) {
                var grandTotal = this.grandTotal();
                $.each(this.getTotals(), function () {
                    if(this.code() == totalCode) {
                        grandTotal -= this.value();
                    }
                });
                return grandTotal;
            },
            getAdditionalInfo: function () {
                return this.additionalInfo();
            },
            addAdditionalInfo: function (data) {
                var infoFound = ko.utils.arrayFirst(this.additionalInfo(), function (info) {
                    return info.code() == data.code;
                });

                if (infoFound) {
                    infoFound.title(data.title);
                    infoFound.value(data.value);
                    infoFound.visible(data.visible);
                } else {
                    var info = {};
                    info.code = ko.observable(data.code);
                    info.title = ko.observable(data.title);
                    info.value = ko.observable(data.value);
                    info.visible = ko.observable(data.visible);
                    this.additionalInfo().push(info);
                }
            },
            getMaxDiscountAbleAmount: function(){
                var self = this;
                return (CartData.apply_tax_after_discount == true)?(self.grandTotalBeforeDiscount() - self.tax()):self.grandTotalBeforeDiscount();
            },
            hasSpecialDiscount: function(){
                var self = this;
                var hasSpecialDiscount = false;
                $.each(this.getTotals(), function () {
                    if($.inArray(this.code(),self.BASE_TOTALS) < 0 && this.value() < 0) {
                        hasSpecialDiscount = true;
                    }
                });
                return hasSpecialDiscount;
            }
        });
    }
);
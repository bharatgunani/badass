/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'jquery',
        'ko',
        'mage/translate',
        'uiComponent',
        'Magestore_Webpos/js/model/checkout/checkout',
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/checkout/cart/totals-factory',
    ],
    function ($, ko, $t, Component, CheckoutModel, PriceHelper, Helper, TotalsFactory) {
        "use strict";
        return Component.extend({
            currentTotal: ko.observable(),
            cartTotal: ko.pureComputed(function(){
                return TotalsFactory.get().grandTotal();
            }),
            defaults: {
                template: 'Magestore_Webpos/checkout/checkout/payment_selected'
            },
            initialize: function () {
                this._super();
                this.selectedPayment = '#payment_selected';
                this.creditCardPayment = '#payment_creditcard';
                this.paymentList = '#payment_list';
                this.addPaymentButton = '#add_payment_button';
                this.rightPopup = '.popup-for-right';
                this.wrapBackover = '.wrap-backover';
                if(this.currentTotal() == undefined){
                    CheckoutModel.remainTotal(this.cartTotal());
                }
            },
            initPayments: function () {
                CheckoutModel.selectedPayments([]);
                CheckoutModel.remainTotal(0);
                this.currentTotal(undefined);
                if($(this.creditCardPayment) !== undefined){
                    $(this.creditCardPayment).hide();
                }
                if($(this.selectedPayment) !== undefined){
                    $(this.selectedPayment).hide();
                }
                if($(this.paymentList) !== undefined){
                    $(this.paymentList).show();
                }
            },
            setPayments: function (payments) {
                CheckoutModel.selectedPayments(payments);
            },
            getPayments: function () {
                var self = this;
                var selectedPayments = CheckoutModel.selectedPayments();
                selectedPayments = $.grep(selectedPayments, function(data) {
                    return data.type == self.visibilityPaymentType();
                });

                return selectedPayments;
            },
            visibilityPaymentType: function () {
                return '0';
            },
            addPayment: function(data){
                // if(CheckoutModel.selectedPayments().length <= 0){
                //     CheckoutModel.paymentCode(data.code);
                // }
                // if(CheckoutModel.selectedPayments().length > 0){
                CheckoutModel.paymentCode('multipaymentforpos');
                // }
                if(data.is_pay_later == '1'){
                    CheckoutModel.selectedPayments.push({
                        id:CheckoutModel.selectedPayments().length,
                        code:data.code,
                        iconClass:"icon-iconPOS-payment-"+data.code,
                        title:data.title,
                        price:data.price,
                        type:data.type,
                        is_pay_later:data.is_pay_later,
                        is_reference_number:data.is_reference_number,
                        cart_total:0,
                        paid_amount:0,
                        cart_total_formated: PriceHelper.convertAndFormat(0),
                        is_extension_method: false
                    });
                    // this.disableAddPaymentButton();
                }else{
                    this.removePayLaterPayment();
                    var paymentData = {
                        id:CheckoutModel.selectedPayments().length,
                        code:data.code,
                        iconClass:"icon-iconPOS-payment-"+data.code,
                        title:data.title,
                        price:data.price,
                        type:data.type,
                        is_pay_later:data.is_pay_later,
                        is_reference_number:data.is_reference_number,
                        cart_total:CheckoutModel.remainTotal(),
                        paid_amount:CheckoutModel.remainTotal(),
                        cart_total_formated: PriceHelper.convertAndFormat(CheckoutModel.remainTotal()),
                        is_extension_method: false
                    }
                    if(data.is_extension){
                        paymentData.paid_amount = data.paid_amount;
                        paymentData.cart_total = data.cart_total;
                    }
                    CheckoutModel.selectedPayments.push(paymentData);
                    CheckoutModel.remainTotal(0);
                    this.reCalculateTotal(-1);
                    this.disableAddPaymentButton();
                }
                this.hidePaymentPopup();
            },
            getExtensionPayment: function(code){
                if(code){
                    var item = ko.utils.arrayFirst(CheckoutModel.selectedPayments(), function (item) {
                        return item.code == code;
                    });
                    return (item) ? item : false;
                }
                return false;
            },
            addExtensionPayment: function(data){
                if(data && data.code){
                    var self = this;
                    var item = self.getExtensionPayment(data.code);
                    if(item !== false) {
                        self.updatePaymentPrice(data.code, data.price, item);
                    }else{
                        this.removePayLaterPayment();
                        var paymentData = {
                            id:CheckoutModel.selectedPayments().length,
                            code:data.code,
                            iconClass:"icon-iconPOS-payment-"+data.code,
                            title:data.title,
                            price:data.price,
                            type:data.type,
                            is_pay_later:data.is_pay_later,
                            is_reference_number:data.is_reference_number,
                            is_extension_method:data.is_extension_method,
                            cart_total:data.cart_total,
                            paid_amount:data.paid_amount,
                            cart_total_formated: ko.observable(PriceHelper.convertAndFormat(data.cart_total)),
                            actions: data.actions
                        }
                        CheckoutModel.selectedPayments.push(paymentData);
                        this.reCalculateTotal(paymentData.id);
                    }
                }
            },
            updatePaymentPrice: function(code, amount, item){
                if(code){
                    var self = this;
                    item = (item)?item:self.getExtensionPayment(code);
                    if(item !== false) {
                        CheckoutModel.selectedPayments()[item.id].cart_total = amount;
                        CheckoutModel.selectedPayments()[item.id].paid_amount = amount;
                        CheckoutModel.selectedPayments()[item.id].cart_total_formated(PriceHelper.convertAndFormat(amount));
                        self.reCalculateTotal(item.id);
                    }
                }
            },
            editPaymentPrice: function (data, event) {
                var seletctedId = data.id;
                var paymentPrice = PriceHelper.toBasePrice(PriceHelper.toNumber(event.target.value));
                event.target.value = PriceHelper.formatPrice(PriceHelper.toNumber(event.target.value));
                CheckoutModel.selectedPayments()[seletctedId].cart_total = paymentPrice;
                CheckoutModel.selectedPayments()[seletctedId].paid_amount = paymentPrice;
                this.reCalculateTotal(seletctedId);
            },
            reCalculateTotal: function (seletctedId) {
                var currenTotal = 0;
                ko.utils.arrayForEach(CheckoutModel.selectedPayments(), function(item) {
                    currenTotal += PriceHelper.toNumber(item.cart_total);
                });
                this.currentTotal(currenTotal);
                this.checkAddPayment(currenTotal);
                CheckoutModel.remainTotal(this.cartTotal() - currenTotal);
                if(seletctedId >= 0 && CheckoutModel.remainTotal() < 0) {
                    this.setTotalWithoutChange(seletctedId, CheckoutModel.remainTotal());
                }
            },
            setTotalWithoutChange: function (seletctedId, remailTotal) {
                var cartTotal = CheckoutModel.selectedPayments()[seletctedId].cart_total;
                CheckoutModel.selectedPayments()[seletctedId].paid_amount = cartTotal + remailTotal;
            },
            checkAddPayment: function (currenTotal) {
                if(currenTotal < this.cartTotal()){
                    $(this.addPaymentButton).prop('disabled', false);
                }else{
                    $(this.addPaymentButton).prop('disabled', true);
                }
            },
            disableAddPaymentButton: function () {
                $(this.addPaymentButton).prop('disabled', true);
            },
            hidePaymentPopup:function () {
                $(this.rightPopup).hide();
                $(this.rightPopup).removeClass('fade-in');
                $(this.wrapBackover).hide()
            },
            removeSelectedPayment: function (data, event) {
                CheckoutModel.selectedPayments.remove(data);
                this.reCalculateTotal(-1);
                this.showActivePayments();
                if(data.is_extension_method == true && data.actions && data.actions.remove){
                    data.actions.remove();
                }
            },
            removePayLaterPayment: function (data, event) {
                ko.utils.arrayForEach(CheckoutModel.selectedPayments(), function(item) {
                    if(item != undefined) {
                        if (item.is_pay_later == '1') {
                            CheckoutModel.selectedPayments.remove(item);
                        }
                    }
                });
            },
            showActivePayments: function () {
                if(CheckoutModel.selectedPayments().length == 0){
                    if($(this.selectedPayment) !== undefined){
                        $(this.selectedPayment).hide();
                    }
                    if($(this.creditCardPayment) !== undefined){
                        $(this.creditCardPayment).hide();
                    }
                    if($(this.paymentList) !== undefined){
                        $(this.paymentList).show();
                    }
                    $(this.addPaymentButton).prop('disabled', false);
                }
            },
            isShowReferenceNumber: function (check) {
                if(check == '1'){
                    return true;
                }
                return false;
            },
            editReferenceNumber: function (data, event) {
                var seletctedId = data.id;
                CheckoutModel.selectedPayments()[seletctedId].reference_number = event.target.value;
            },
            getRefenceNumberText: function () {
                return $t('Reference Number');
            },
            getRefenrenceNumberId: function (code) {
                return code+'_reference_number';
            },
            renewPayments: function () {
                CheckoutModel.selectedPayments([]);
                this.currentTotal(undefined);
                CheckoutModel.remainTotal(this.cartTotal());
                if($(this.paymentList) !== undefined){
                    $(this.paymentList).show();
                }
                Helper.dispatchEvent('payments_reset_after', '');
            },
            checkVisibleInputBox: function (check) {
                if(check == '1'){
                    return false;
                }
                return true;
            }
        });
    }
);
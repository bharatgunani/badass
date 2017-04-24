/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/layout',
        'uiComponent',
        'Magestore_Webpos/js/model/checkout/cart/items',
        'Magestore_Webpos/js/helper/alert',
        'Magestore_Webpos/js/action/cart/hold',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/model/checkout/cart/totals-factory'
    ],
    function ($, ko, ViewManager, Component, Items, Alert, Hold, EventManager, TotalsFactory) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/checkout/cart/totals'
            },
            isZeroTotal: ko.pureComputed(function(){
                return (TotalsFactory.get().grandTotal())?false:true;
            }),
            isOnCartPage: ko.pureComputed(function(){
                return (ViewManager.getSingleton('view/checkout/cart').currentPage() == ViewManager.getSingleton('view/checkout/cart').PAGE.CART)?true:false;
            }),
            isOnCheckoutPage: ko.pureComputed(function(){
                return (ViewManager.getSingleton('view/checkout/cart').currentPage() == ViewManager.getSingleton('view/checkout/cart').PAGE.CHECKOUT)?true:false;
            }),
            createdOrder: ko.pureComputed(function(){
                return (ViewManager.getSingleton('view/checkout/cart').createdOrder() == true)?true:false;
            }),
            initialize: function () {
                this._super();
            },
            getTotals: function(){
                return TotalsFactory.get().getDisplayTotals();
            },
            getButtons: function(){
                return TotalsFactory.get().getButtons();
            },
            totalItemClick: function(totalItem,event){
                if(this.createdOrder() == true){
                    return false;
                }
                var classes = event.target.getAttribute("class");
                if(totalItem.code() == TotalsFactory.get().ADD_DISCOUNT_TOTAL_CODE || totalItem.code() == TotalsFactory.get().DISCOUNT_TOTAL_CODE ){
                    if(Items.isEmpty()){
                        Alert({
                            priority: "warning",
                            title: "Warning",
                            message: "Please add item(s) to cart!"
                        });

                    }else{
                        if(!classes || (classes && classes.indexOf("icon-iconPOS-remove") < 0)){
                            ViewManager.getSingleton('view/checkout/cart').showCartDiscountPopup(event);
                        }
                    }
                }
            },
            buttonClick: function(button){
                if(button.code == TotalsFactory.get().HOLD_BUTTON_CODE){
                    if(Items.isEmpty()){
                        Alert({
                            priority: "warning",
                            title: "Warning",
                            message: "Please add item(s) to cart!"
                        });
                        return;
                    }else{
                        Hold.execute();
                        return;
                    }
                }
                if(button.code == TotalsFactory.get().CHECKOUT_BUTTON_CODE){
                    if(Items.isEmpty()){
                        Alert({
                            priority: "warning",
                            title: "Warning",
                            message: "Please add item(s) to cart!"
                        });
                        return;
                    }else{
                        ViewManager.getSingleton('view/checkout/cart').switchToCheckout();
                        ViewManager.getSingleton('view/checkout/checkout/shipping').saveDefaultShippingMethod();
                        ViewManager.getSingleton('view/checkout/checkout/payment').collection.reset();
                        ViewManager.getSingleton('view/checkout/checkout/payment').saveDefaultPaymentMethod();
                        EventManager.dispatch('go_to_checkout_page', '', true);
                        return;
                    }
                }
                if(button.code == TotalsFactory.get().BACK_CART_BUTTON_CODE){
                    ViewManager.getSingleton('view/checkout/cart').switchToCart();
                }
            },
            removeDiscount: function(){
                ViewManager.getSingleton('view/checkout/cart/discountpopup').initDefaultData();
                ViewManager.getSingleton('view/checkout/cart/discountpopup').resetData();
                TotalsFactory.get().updateDiscountTotal();
                ViewManager.getSingleton('view/checkout/checkout/payment').collection.reset();
                ViewManager.getSingleton('view/checkout/checkout/payment').saveDefaultPaymentMethod();
            },
            getAdditionalInfo: function() {
                return TotalsFactory.get().getAdditionalInfo();
            },
            removeTotal: function(el){
                if(el.actions() && el.actions().remove){
                    if(typeof el.actions().remove == 'string'){
                        if(typeof this[el.actions().remove] == 'function'){
                            this[el.actions().remove]();
                        }
                    }
                    if(typeof el.actions().remove == 'function'){
                        el.actions().remove();
                    }
                }
            },
        });
    }
);
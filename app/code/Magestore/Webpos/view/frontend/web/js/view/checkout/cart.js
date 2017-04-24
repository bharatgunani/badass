/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'require',
        'jquery',
        'ko',   
        'Magestore_Webpos/js/view/layout',
        'uiComponent',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/model/checkout/cart/items',
        'Magestore_Webpos/js/model/customer/current-customer',
        'Magestore_Webpos/js/helper/alert',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/checkout/cart/totals-factory',
    ],
    function (require, $, ko, ViewManager, Component, CartModel, Items, currentCustomer, Alert, Helper, TotalsFactory) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/checkout/cart'
            },
            renderedItems: ko.observable(false),
            renderedTotals: ko.observable(false),
            currentCustomerName: ko.pureComputed(function() {
                return currentCustomer.fullName();
            }),
            currentCustomerId: ko.pureComputed(function() {
                return CartModel.customerId();
            }),
            isShowCustomerId: ko.pureComputed(function() {
                return currentCustomer.customerId()!= 0;
            }),
            PAGE:{
                CART:"cart",
                CHECKOUT:"checkout"
            },
            currentPage: ko.observable(),
            cartTitle: ko.pureComputed(function() {
                return "Cart ("+ Items.totalItems() + ")";
            }),
            initialize: function () {
                this._super();
                var self = this;
                this.renderedPage = ko.pureComputed(function(){
                    return (self.renderedItems() && self.renderedTotals() && CartModel.loading() != true);
                });
                if(!this.currentPage()){
                    this.currentPage(this.PAGE.CART);
                }
                this.isOnCheckoutPage = ko.pureComputed(function(){
                    return (self.currentPage() == self.PAGE.CHECKOUT);
                });
                this.createdOrder = ko.pureComputed(function(){
                    var result = false;
                    var model = CartModel.CheckoutModel();
                    if(model){
                        result = (model.createOrderResult() && model.createOrderResult().increment_id)?true:false;
                    }
                    return result;
                });
            },
            goToCheckoutPage: function(){
                if($('#webpos_checkout').length > 0){
                    $('#webpos_checkout').addClass("active");
                    var categoryWith = $('#checkout_container .col-left').width();
                    $('#checkout_container').css({
                        left:"-"+categoryWith+"px"
                    });
                    $('#popup-change-customer').addClass('active-on-checkout');
                }
            },
            goToCartPage: function(){
                if($('#webpos_checkout').length > 0){
                    $('#webpos_checkout').removeClass("active");
                    $('#checkout_container').css({
                        left:"0px"
                    });
                    $('#popup-change-customer').removeClass('active-on-checkout');
                }
            },
            hideMenuButton: function(){
                if($('#c-button--push-left').length > 0){
                    $('#c-button--push-left').hide();
                    $('#c-button--push-left').addClass('hide');
                }
            },
            showMenuButton: function(){
                if($('#c-button--push-left').length > 0){
                    $('#c-button--push-left').show();
                    $('#c-button--push-left').removeClass('hide');
                }
            },
            transformInterface: function(){
                var self = this;
                switch(self.currentPage()){
                    case self.PAGE.CART:
                        self.goToCartPage();
                        self.showMenuButton();
                        break;
                    case self.PAGE.CHECKOUT:
                        self.goToCheckoutPage();
                        self.hideMenuButton();
                        break;
                }
            },
            switchToCart: function(){
                this.currentPage(this.PAGE.CART);
                this.transformInterface();
                $('#checkout_container').addClass('showMenu');
            },
            switchToCheckout: function(){
                if(Items.isEmpty()){
                    return;
                }else{
                    this.currentPage(this.PAGE.CHECKOUT);
                    this.transformInterface();
                    $('#checkout_container').removeClass('showMenu');
                }
            },
            initNotePopup: function(){
                $("[data-toggle=popover]").popover({
                    html: true,
                    content: function () {
                        var content = $(this).attr("data-popover-content");
                        return $(content).children(".popover-body").html();
                    },
                    title: function () {
                        var title = $(this).attr("data-popover-content");
                        return $(title).children(".popover-heading").html();
                    }
                });
                $('[rel="popover"]').popover({
                    trigger: 'focus',
                    container: 'body',
                    html: true,
                    content: function () {
                        var clone = $($(this).data('popover-content')).clone(true).removeClass('hide');
                        return clone;
                    }
                }).click(function (e) {
                    e.preventDefault();
                });
                if($('.pos_modal_link').length > 0){
                    $('.pos_modal_link').click(function(){
                        $('#form-add-note').addClass("hide");
                        $('#form-add-note').removeClass("show");
                        $('#form-add-note').hide();
                        if($(this).data("target")){
                            var target = $(this).data("target");
                            if($(target).length > 0){
                                $(target).removeClass("fade");
                                $(target).addClass("show");
                                $(target).addClass("fade-in");
                                $(target).show();
                                $('#c-button--push-left').hide();
                                if($(target+' .pos_overlay').length > 0){
                                    $(target+' .pos_overlay').click(function(){
                                        $(target).addClass("fade");
                                        $(target).removeClass("show");
                                        $(target).removeClass("fade-in");
                                        $(target).hide();
                                        if($('#checkout_container').hasClass('showMenu')){
                                            $('#c-button--push-left').show();
                                            $('#c-button--push-left').removeClass('hide');
                                        }else{
                                            $('#c-button--push-left').hide();
                                            $('#c-button--push-left').addClass('hide');
                                        }
                                    });
                                }
                                if($(target+' button').length > 0){
                                    $(target+' button').each(function(){
                                        if($(this).data("dismiss") && $(this).data("dismiss") == "modal"){
                                            $(this).click(function(){
                                                $(target).addClass("fade");
                                                $(target).removeClass("show");
                                                $(target).removeClass("fade-in");
                                                $(target).hide();
                                                if($('#checkout_container').hasClass('showMenu')){
                                                    $('#c-button--push-left').show();
                                                    $('#c-button--push-left').removeClass('hide');
                                                }else{
                                                    $('#c-button--push-left').hide();
                                                    $('#c-button--push-left').addClass('hide');
                                                }
                                            });
                                        }
                                    });
                                }
                            }
                        }
                    });
                }
            },
            showAddtitionalAction: function(){
                if($('#form-add-note').hasClass("show")){
                    $('#form-add-note').addClass("hide");
                    $('#form-add-note').removeClass("show");
                    $('#form-add-note').hide();
                }else{
                    $('#form-add-note').removeClass("hide");
                    $('#form-add-note').addClass("show");
                    $('#form-add-note').show();
                    $('#form-add-note').mouseout(function(){
                        $('#form-add-note').addClass("hide");
                        $('#form-add-note').removeClass("show");
                        $('#form-add-note').hide();
                    });
                    $('#form-add-note').mouseover(function(){
                        $('#form-add-note').removeClass("hide");
                        $('#form-add-note').addClass("show");
                        $('#form-add-note').show();
                    });
                }
            },
            showCartDiscountPopup: function(event){
                var viewManager = require('Magestore_Webpos/js/view/layout');
                if(this.isOnCheckoutPage()){
                    viewManager.getSingleton('view/checkout/cart/discountpopup').isOnCheckout(true);
                }else{
                    viewManager.getSingleton('view/checkout/cart/discountpopup').isOnCheckout(false);
                }
                viewManager.getSingleton('view/checkout/cart/discountpopup').show();
            },
            afterRenderCart: function(){

            },
            addTestProducts: function(){
                CartModel.addTestProducts();
            },

            changeCustomer: function () {
                $('#popup-change-customer').addClass('fade-in');
                $('.wrap-backover').show();
                $('.notification-bell').hide();
                $('#c-button--push-left').hide();
                Helper.dispatchEvent('checkout_customer_list_show_after',{});
            },

            editCustomer: function () {
                $('#form-edit-customer').removeClass('fade');
                $('#form-edit-customer').addClass('fade-in');
                $('#form-edit-customer').addClass('show');
                $('.wrap-backover').show();
                $('.notification-bell').hide();
                $('#c-button--push-left').hide();
            },

            emptyCart: function(){
                var viewManager = require('Magestore_Webpos/js/view/layout');
                CartModel.emptyCart();
                viewManager.getSingleton('view/checkout/cart/discountpopup').resetData();
                TotalsFactory.get().updateDiscountTotal();
            }
        });
    }
);
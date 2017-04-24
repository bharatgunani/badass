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
        'Magestore_Webpos/js/view/base/list/collection-list',
        'Magestore_Webpos/js/model/checkout/checkout',
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/model/checkout/cart/items',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/model/checkout/shipping-factory',
        'mage/calendar',
    ],
    function (require, $, ko, ViewManager, colGrid, CheckoutModel, PriceHelper, Items, CartModel, ShippingFactory) {
        "use strict";
        return colGrid.extend({
            defaults: {
                template: 'Magestore_Webpos/checkout/checkout/shipping',
            },
            initialize: function () {
                this.isShowHeader = true;
                this.model = ShippingFactory.get().setMode('offline');
                this.isCheck = ko.pureComputed(function(){
                    return CheckoutModel.selectedShippingCode();
                });
                this._super();
                this._render();
            },
            _prepareCollection: function () {
                if(this.collection == null) {
                    this.collection = this.model.getCollection().setOrder('price','DESC');
                }
                this.collection.setPageSize(10);
                this.collection.setCurPage(1);
            },
            setShippingMethod: function (data, event) {
                var viewManager = require('Magestore_Webpos/js/view/layout');
                CheckoutModel.saveShipping(data);
                viewManager.getSingleton('view/checkout/checkout/payment').saveDefaultPaymentMethod();
            },
            getShippingPrice: function(price, priceType){
                var shippingFee = 0;
                shippingFee = price;
                if(typeof priceType != "undefined"){
                    shippingFee = (priceType == "I")?(shippingFee * Items.totalShipableItems()):shippingFee;
                }
                return PriceHelper.convertAndFormat(shippingFee);
            },
            getDefaultShippingMethod: function () {
                var shippingList = this.items();
                if(shippingList.length > 0){
                    for(var i = 0; i < shippingList.length; i++){
                        if(shippingList[i].code == CheckoutModel.selectedShippingCode()) {
                            return shippingList[i];
                        }
                        if(shippingList[i].is_default == '1' && CheckoutModel.selectedShippingCode() == ''){
                            return shippingList[i];
                        }
                    }
                }
                return false;
            },
            saveDefaultShippingMethod: function () {
                if(this.getDefaultShippingMethod() && !CartModel.isVirtual()){
                    this.setShippingMethod(this.getDefaultShippingMethod());
                }
            },
            checkDefaultMethod: function (code) {
                if(this.getDefaultShippingMethod() && this.getDefaultShippingMethod().code == code){
                    $('#'+code).prop("checked", true);
                }
            },
            useDeliveryTime: function () {
                return (WEBPOS.getConfig('webpos/general/enable_delivery_date') == 1) ? true : false;
            },
            initDate: function () {
                var currentDate = new Date();
                var year = currentDate.getFullYear();
                var month = currentDate.getMonth();
                var day = currentDate.getDate();
                $("#delivery_date").calendar({
                    showsTime: true,
                    controlType: 'select',
                    timeFormat: 'HH:mm TT',
                    showTime: false,
                    minDate: new Date(year, month, day, '00', '00', '00', '00'),
                });
            }
        });
    }
);
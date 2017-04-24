/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

/*global define*/
define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/checkout/checkout',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/view/layout',
        'Magestore_Webpos/js/model/sales/order-factory',
        'Magestore_Webpos/js/model/checkout/cart/totals-factory',        
    ],
    function($, ko, CheckoutModel, CartModel, ViewManager, OrderFactory, TotalsFactory) {
        'use strict';
        return {
            execute: function(){
                var data = CheckoutModel.getHoldOrderData();
                OrderFactory.get().setData(data).save().done(function (response) {
                    if(response){
                        ViewManager.getSingleton('view/checkout/cart/discountpopup').resetData();
                        TotalsFactory.get().updateDiscountTotal();
                        CartModel.emptyCart();
                        CartModel.removeCustomer();
                        CheckoutModel.resetCheckoutData();
                    }
                });
            }
        }
    }
);

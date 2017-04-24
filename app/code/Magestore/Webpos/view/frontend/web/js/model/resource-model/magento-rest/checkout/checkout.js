/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'Magestore_Webpos/js/model/resource-model/magento-rest/abstract'
    ],
    function (onlineAbstract) {
        "use strict";

        return onlineAbstract.extend({
            requireActionIdPath:'customer_id',
            type:'order',
            initialize: function () {
                this._super();
                this.apiGetShippingRateUrl = "/webpos/checkout/getShippingRates";
                this.apiCheckPromotionUrl = "/webpos/checkout/checkPromotion";
                this.apiCreateOrderUrl = "/webpos/checkout/create";
                this.apiSubmitOnlineOrderUrl = "/webpos/checkout/submitonline";
                this.apiSendEmailUrl = "/webpos/checkout/sendEmail";
            },
            getCallBackEvent: function(key){
                switch(key){
                    case "createOrder":
                        return "sync_offline_order_after";
                }
            },
            setApiUrl: function(key,value){
                switch(key){
                    case "apiGetShippingRateUrl":
                        this.apiGetShippingRateUrl = value;
                        break;
                    case "apiCheckPromotionUrl":
                        this.apiCheckPromotionUrl = value;
                        break;
                    case "apiCreateOrderUrl":
                        this.apiCreateOrderUrl = value;
                        break;
                    case "apiSubmitOnlineOrderUrl":
                        this.apiSubmitOnlineOrderUrl = value;
                        break;
                    case "apiSendEmailUrl":
                        this.apiSendEmailUrl = value;
                        break;
                }
            },
            getApiUrl: function(key){
                switch(key){
                    case "apiGetShippingRateUrl":
                        return this.apiGetShippingRateUrl;
                    case "apiCheckPromotionUrl":
                        return this.apiCheckPromotionUrl;
                    case "apiCreateOrderUrl":
                        return this.apiCreateOrderUrl;
                    case "apiSubmitOnlineOrderUrl":
                        return this.apiSubmitOnlineOrderUrl;
                    case "apiSendEmailUrl":
                        return this.apiSendEmailUrl;
                }
            },
            getShippingRates: function(params,deferred){
                var apiUrl,
                    urlParams;
                apiUrl = this.getApiUrl("apiGetShippingRateUrl");
                urlParams = {};
                this.callRestApi(apiUrl, "post", urlParams, params, deferred);
            },
            checkPromotion: function(params,deferred){
                var apiUrl,
                    urlParams;
                apiUrl = this.getApiUrl("apiCheckPromotionUrl");
                urlParams = {};
                this.callRestApi(apiUrl, "post", urlParams, params, deferred);
            },
            submitOnlineOrder: function(params,deferred){
                var apiUrl,
                    urlParams;
                apiUrl = this.getApiUrl("apiSubmitOnlineOrderUrl");
                urlParams = {};
                this.callRestApi(apiUrl, "post", urlParams, params, deferred);
            },
            createOrder: function(params,deferred){
                var apiUrl,
                    urlParams,
                    callBackEvent;
                apiUrl = this.getApiUrl("apiCreateOrderUrl");
                callBackEvent = this.getCallBackEvent("createOrder");
                urlParams = {};
                this.callRestApi(apiUrl, "post", urlParams, params, deferred, callBackEvent);
            },
            sendEmail: function(params,deferred){
                var apiUrl,
                    urlParams;
                apiUrl = this.getApiUrl("apiSendEmailUrl");
                urlParams = {};
                this.callRestApi(apiUrl, "post", urlParams, params, deferred);
            }
        });
    }
);
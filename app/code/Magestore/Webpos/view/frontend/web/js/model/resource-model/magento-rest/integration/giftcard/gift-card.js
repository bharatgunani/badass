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
            initialize: function () {
                this._super();
                this.apiGetGiftcardBalanceUrl = "/webpos/integration/getGiftcardBalance";
                this.apiRefundGiftcardBalanceUrl = "/webpos/integration/refundGiftcardBalance";
            },
            getCallBackEvent: function(key){
                switch(key){

                }
            },
            setApiUrl: function(key,value){
                switch(key){
                    case "apiGetGiftcardBalanceUrl":
                        this.apiGetGiftcardBalanceUrl = value;
                        break;
                    case "apiRefundGiftcardBalanceUrl":
                        this.apiRefundGiftcardBalanceUrl = value;
                        break;
                }
            },
            getApiUrl: function(key){
                switch(key){
                    case "apiGetGiftcardBalanceUrl":
                        return this.apiGetGiftcardBalanceUrl;
                    case "apiRefundGiftcardBalanceUrl":
                        return this.apiRefundGiftcardBalanceUrl;
                }
            },
            getBalance: function(params,deferred){
                var apiUrl,
                    urlParams;
                apiUrl = this.getApiUrl("apiGetGiftcardBalanceUrl");
                urlParams = {};
                this.callRestApi(apiUrl, "post", urlParams, params, deferred);
            },
            refundToBalance: function(params,deferred){
                var apiUrl,
                    urlParams;
                apiUrl = this.getApiUrl("apiRefundGiftcardBalanceUrl");
                urlParams = {};
                this.callRestApi(apiUrl, "post", urlParams, params, deferred);
            },
        });
    }
);
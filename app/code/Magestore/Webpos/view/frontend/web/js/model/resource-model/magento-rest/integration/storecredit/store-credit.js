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
                this.setSearchApiUrl('/webpos/integration/getCreditList');
                this.apiGetCreditBalanceUrl = "/webpos/integration/getCreditBalance";
                this.apiRefundByCreditUrl = "/webpos/integration/refundByCredit";
            },
            getCallBackEvent: function(key){
                switch(key){

                }
            },
            setApiUrl: function(key,value){
                switch(key){
                    case "apiGetCreditBalanceUrl":
                        this.apiGetCreditBalanceUrl = value;
                        break;
                    case "apiRefundByCreditUrl":
                        this.apiRefundByCreditUrl = value;
                        break;
                }
            },
            getApiUrl: function(key){
                switch(key){
                    case "apiGetCreditBalanceUrl":
                        return this.apiGetCreditBalanceUrl;
                    case "apiRefundByCreditUrl":
                        return this.apiRefundByCreditUrl;
                }
            },
            getBalance: function(params,deferred){
                var apiUrl,
                    urlParams;
                apiUrl = this.getApiUrl("apiGetCreditBalanceUrl");
                urlParams = {};
                this.callRestApi(apiUrl, "post", urlParams, params, deferred);
            },
            refund: function(params,deferred){
                var apiUrl,
                    urlParams;
                apiUrl = this.getApiUrl("apiRefundByCreditUrl");
                urlParams = {};
                this.callRestApi(apiUrl, "post", urlParams, params, deferred);
            }
        });
    }
);
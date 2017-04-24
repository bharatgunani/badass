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
                this.setSearchApiUrl('/webpos/integration/getCustomerPoints');
                this.apiGetPointBalanceUrl = "/webpos/integration/getPointBalance";
            },
            getCallBackEvent: function(key){
                switch(key){

                }
            },
            setApiUrl: function(key,value){
                switch(key){
                    case "apiGetPointBalanceUrl":
                        this.apiGetPointBalanceUrl = value;
                        break;
                }
            },
            getApiUrl: function(key){
                switch(key){
                    case "apiGetPointBalanceUrl":
                        return this.apiGetPointBalanceUrl;
                }
            },
            getBalance: function(params,deferred){
                var apiUrl,
                    urlParams;
                apiUrl = this.getApiUrl("apiGetPointBalanceUrl");
                urlParams = {};
                this.callRestApi(apiUrl, "post", urlParams, params, deferred);
            },
        });
    }
);
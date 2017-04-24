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
        'Magestore_Webpos/js/helper/datetime',
        'Magestore_Webpos/js/model/sales/order-factory',
    ],
    function ($, ko, modelAbstract, Datetime, OrderFactory) {
        "use strict";
        return modelAbstract.extend({
            CheckoutModel:ko.observable(),
            initialize: function () {
                this._super();
                this.tmpForm = false;
                this.iframeId = '#webpos-payment-iframe';
            },
            processPayment: function(orderId, paymentInfo){
                var self = this;
                var paymentUrl = paymentInfo.url;
                var paymentParams = paymentInfo.params;
                $.when(
                    this.requestOnlinePayment(paymentUrl, paymentParams)
                ).done(function (response) {
                    if(response == undefined){
                        self.CheckoutModel().loading(false);
                        return;
                    }
                    if (orderId) {
                        OrderFactory.get().setPush(true).setLog(false).setMode("online").load(orderId).done(function (response) {
                            response.created_at = Datetime.getBaseSqlDatetime();
                            response.updated_at = Datetime.getBaseSqlDatetime();
                            OrderFactory.get().setMode("offline").setData(response).setPush(false).save().done(function (response) {
                                if (response) {
                                    self.CheckoutModel().placeOrder(response);
                                }
                                self.CheckoutModel().loading(false);
                            });
                        });
                    self.CheckoutModel().loading(false);
                    }
                }).fail(function () {
                    self.CheckoutModel().loading(false);
                });
            },
            requestOnlinePayment: function(apiUrl, params){
                var paymentData = this.preparePaymentRequest(params);
                this.sendPaymentRequest(apiUrl, paymentData)
            },
            sendPaymentRequest : function(cgiUrl, paymentData) {
                this.recreateIframe();
                this.tmpForm = document.createElement('form');
                this.tmpForm.style.display = 'none';
                this.tmpForm.enctype = 'application/x-www-form-urlencoded';
                this.tmpForm.method = 'POST';
                document.body.appendChild(this.tmpForm);
                this.tmpForm.action = cgiUrl;
                this.tmpForm.target = $(this.iframeId).attr('name');
                this.tmpForm.setAttribute('target', $(this.iframeId).attr('name'));
                for ( var param in paymentData) {
                    this.tmpForm.appendChild(this.createHiddenElement(param, paymentData[param]));
                }
                this.tmpForm.submit();
            },
            createHiddenElement : function(name, value) {
                var field;
                // if (isIE) {
                //     field = document.createElement('input');
                //     field.setAttribute('type', 'hidden');
                //     field.setAttribute('name', name);
                //     field.setAttribute('value', value);
                // } else {
                field = document.createElement('input');
                field.type = 'hidden';
                field.name = name;
                field.value = value;
                // }

                return field;
            },
            recreateIframe : function() {
                if ($(this.iframeId) != undefined) {
                    $(this.iframeId).empty();
                    $(this.iframeId).show();
                    if (this.tmpForm) {
                        document.body.removeChild(this.tmpForm);
                    }
                }
            },
            preparePaymentRequest : function(data) {
                if($('#webpos_cc_exp_month') != "undefined" && $('#webpos_cc_exp_month').val() != "" &&
                    $('#webpos_cc_exp_year') != "undefined" && $('#webpos_cc_exp_year').val() != "") {
                    var year = $('#webpos_cc_exp_year').val();
                    if (year.length > 2) {
                        year = year.substring(2);
                    }
                    var month = parseInt($('#webpos_cc_exp_month').val(), 10);
                    if (month < 10) {
                        month = '0' + month;
                    }
                    data.x_exp_date = month + '/' + year;
                }
                if($('#webpos_cc_number') != "undefined" && $('#webpos_cc_number').val() != "")
                    data.x_card_num = $('#webpos_cc_number').val();
                if($('#webpos_cc_owner') != "undefined" && $('#webpos_cc_owner').val() != "")
                    data.cc_owner = $('#webpos_cc_owner').val();
                if($('#webpos_cc_type') != "undefined" && $('#webpos_cc_type').val() != "")
                    data.cc_type = $('#webpos_cc_type').val();

                return data;
            },
        });
    }
);
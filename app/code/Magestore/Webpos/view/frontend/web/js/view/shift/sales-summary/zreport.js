/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [ 'jquery',
        'ko',
        'uiComponent',

        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/helper/datetime',


    ],
    function ($, ko, Component,  priceHelper, datetimeHelper) {
        "use strict";

        return Component.extend({
            shiftData:  ko.observable({}),
            openedAtFormatted: ko.observable(''),
            closedAtFormatted: ko.observable(''),
            floatAmountFormatted: ko.observable(0),
            cashLeftFormatted: ko.observable(0),
            closedAmountFormatted: ko.observable(0),
            totalSalesFormatted: ko.observable(0),
            refundFormatted: ko.observable(0),
            discountFormatted: ko.observable(0),
            cashAddedFormatted: ko.observable(0),
            cashRemovedFormatted: ko.observable(0),
            cashSaleFormatted: ko.observable(0),
            staffName: ko.observable(window.webposConfig.staffName),

            defaults: {
                template: 'Magestore_Webpos/shift/sales-summary/zreport',
            },

            initialize: function () {
                this._super();
            },

            setShiftData: function(data){
                this.shiftData(data);
                
                //init data
                this.openedAtFormatted(datetimeHelper.getFullDatetime(data.opened_at));
                this.closedAtFormatted(datetimeHelper.getFullDatetime(data.closed_at));
                this.floatAmountFormatted(priceHelper.formatPrice(data.float_amount));
                this.closedAmountFormatted(priceHelper.formatPrice(data.closed_amount));
                this.cashLeftFormatted(priceHelper.formatPrice(data.cash_left));
                this.cashAddedFormatted(priceHelper.formatPrice(data.cash_added));
                this.cashRemovedFormatted(priceHelper.formatPrice(data.cash_removed));
                this.cashSaleFormatted(priceHelper.formatPrice(data.cash_sale));

                //calculate total sales
                var zreport_sales_summary = data.zreport_sales_summary;

                this.totalSalesFormatted(priceHelper.formatPrice(zreport_sales_summary['grand_total']));
                this.refundFormatted(priceHelper.formatPrice(zreport_sales_summary['total_refunded']));
                this.discountFormatted(priceHelper.formatPrice(zreport_sales_summary['discount_amount']));


            },

            getFont: function(){
                return window.webposConfig["webpos/receipt/font_type"];
            },
            printReport: function () {
                var html = $('#zreport-print-content').html();
                var print_window = window.open('', 'print_offline', 'status=1,width=700,height=700');
                print_window.document.write(html);
                print_window.print();
            },
            
            formatPrice: function (value) {
                return priceHelper.formatPrice(value);
            }

        });
    }
);
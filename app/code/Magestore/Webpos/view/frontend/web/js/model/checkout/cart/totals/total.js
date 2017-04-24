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
        'Magestore_Webpos/js/helper/price'
    ],
    function ($,ko, modelAbstract, PriceHelper) {
        "use strict";
        return modelAbstract.extend({
            initialize: function () {
                this._super();
                this.itemFields = [
                    'isVisible','cssClass','title','value','code','valueFormated'
                ];
                this.initFields = [
                    'isVisible', 'cssClass', 'title', 'value', 'code', 'displayIncludeTax', 'includeTaxValue', 'removeAble'
                ];
            },
            init: function(data){
                var self = this;
                self.isVisible = (typeof data.isVisible != "undefined")?ko.observable(data.isVisible):ko.observable(true);
                self.cssClass = (typeof data.cssClass != "undefined")?ko.observable(data.cssClass):ko.observable();
                self.title = (typeof data.title != "undefined")?ko.observable(data.title):ko.observable();
                self.value = (typeof data.value != "undefined")?ko.observable(data.value):ko.observable();
                self.code = (typeof data.code != "undefined")?ko.observable(data.code):ko.observable();
                self.includeTaxValue = (typeof data.includeTaxValue != "undefined")?ko.observable(data.includeTaxValue):ko.observable();
                self.displayIncludeTax = (typeof data.displayIncludeTax != "undefined")?ko.observable(data.displayIncludeTax):ko.observable(false);
                self.removeAble = (typeof data.removeAble != "undefined")?ko.observable(data.removeAble):ko.observable(false);
                self.actions = (typeof data.actions != "undefined")?ko.observable(data.actions):ko.observable({
                    remove:function(){},
                    collect:function(){}
                });
                self.valueFormated = ko.pureComputed(function(){
                    var value = self.value();
                    if(self.displayIncludeTax() == true){
                        value = self.includeTaxValue();
                    }
                    return PriceHelper.convertAndFormat(value);
                });
            },
            setData: function(key,value){
                if(typeof this[key] != "undefined"){
                    this[key](value); 
                }
            },
            getData: function(key){
                var self = this;
                var data = {};
                if(typeof key != "undefined"){
                    data = self[this]();
                }else{
                    var data = {};
                    $.each(this.initFields, function(){
                        data[this] = self[this]();
                    });
                }
                return data;
            },
        });
    }
);
/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/checkout/cart/items',
        'Magestore_Webpos/js/model/checkout/taxcalculator',
        'mage/translate',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/model/checkout/cart/totals-factory',
        'Magestore_Webpos/js/model/catalog/product-factory',
        'Magestore_Webpos/js/helper/pole',
        'Magestore_Webpos/js/helper/price'
    ],
    function ($, ko, Items, TaxCalculator, Translate, Event, TotalsFactory, ProductFactory, poleHelper, priceHelper) {
        "use strict";
        return {
            loading: ko.observable(),
            customerId: ko.observable(''),
            customerGroup: ko.observable(''),
            customerData: ko.observable({}),
            billingAddress: ko.observable(),
            GUEST_CUSTOMER_NAME: Translate("Guest"),
            BACK_CART_BUTTON_CODE: "back_to_cart",
            CHECKOUT_BUTTON_CODE: "checkout",
            HOLD_BUTTON_CODE: "hold",
            CheckoutModel:ko.observable(),
            CartCustomerModel:ko.observable(),
            emptyCart: function(){
                Items.items.removeAll();
                this.removeCustomer();
                this.CheckoutModel().useDefaultAddress(this.CheckoutModel().ADDRESS_TYPE.BILLING);
                this.CheckoutModel().useDefaultAddress(this.CheckoutModel().ADDRESS_TYPE.SHIPPING);
                this.CheckoutModel().selectedShippingCode("");
                this.CheckoutModel().selectedShippingPrice(0);
                TotalsFactory.get().shippingData("");
                TotalsFactory.get().shippingFee(0);
                TotalsFactory.get().updateShippingAmount(0);
                Event.dispatch('cart_empty_after','');
                poleHelper('', 'Total: ' +
                    priceHelper.convertAndFormat(TotalsFactory.get().grandTotal()));
            },
            addCustomer: function(data){
                this.customerData(data);
                this.customerId(data.id);
                this.customerGroup(data.group_id);
                this.collectTierPrice();
                poleHelper('', 'Total: ' +
                    priceHelper.convertAndFormat(TotalsFactory.get().grandTotal()));
            },
            removeCustomer: function(){
                this.CartCustomerModel().setCustomerId("");
                this.CartCustomerModel().setFullName(this.GUEST_CUSTOMER_NAME);
                this.customerId("");
                this.customerGroup("");
                this.customerData({});
                this.collectTierPrice();
                poleHelper('', 'Total: ' +
                    priceHelper.convertAndFormat(TotalsFactory.get().grandTotal()));
            },
            removeItem: function(itemId){
                Items.removeItem(itemId);
                if(Items.items().length == 0){
                    TotalsFactory.get().updateShippingAmount(0);
                }
                TotalsFactory.get().collectShippingTotal();
                TotalsFactory.get().collectTaxTotal();
                Event.dispatch('cart_item_remove_after',Items.items());
                poleHelper('', 'Total: ' +
                    priceHelper.convertAndFormat(TotalsFactory.get().grandTotal()));
            },
            addProduct: function(data){
                var validate = true;
                var item = Items.getAddedItem(data);
                if(item !== false){
                    var dataToValidate = item.getData();
                    if(dataToValidate.product_id != "customsale" && data.product_type != "bundle"){
                        dataToValidate.qty += data.qty;
                        dataToValidate.customer_group = this.customerGroup();
                        validate = ProductFactory.get().validateQtyInCart(dataToValidate);
                    }
                }else{
                    if(data.product_id != "customsale" && data.product_type != "bundle"){
                        data.customer_group = this.customerGroup();
                        if(data.minimum_qty && data.qty < data.minimum_qty){
                            data.qty = data.minimum_qty;
                        }
                        if(data.maximum_qty && data.maximum_qty > 0 && data.qty > data.maximum_qty){
                            data.qty = data.maximum_qty;
                        }
                        validate = ProductFactory.get().validateQtyInCart(data);
                    }
                }
                if(validate){
                    data = this.collectTaxRate(data);
                    Items.addItem(data);
                    TotalsFactory.get().collectShippingTotal();
                    TotalsFactory.get().collectTaxTotal();
                    this.collectTierPrice();
                }
                poleHelper(data.sku + ' +' + priceHelper.convertAndFormat(parseFloat(data.unit_price) * parseFloat(data.qty)), 'Total: ' +
                    priceHelper.convertAndFormat(TotalsFactory.get().grandTotal()));
            },
            updateItem: function(itemId, key, value){
                var validate = true;
                var item = Items.getItem(itemId);
                if(item){
                    if(key == "qty"){
                        var data = item.getData();
                        data.qty = value;
                        if(data.product_id != "customsale" && data.product_type != "bundle"){
                            data.customer_group = this.customerGroup();
                            validate = ProductFactory.get().validateQtyInCart(data);
                        }
                        if(data.product_id == "customsale"){
                            value = (value > 0)?value:1;
                        }
                    }
                    if(validate){
                        Items.setItemData(itemId, key, value);
                        TotalsFactory.get().collectShippingTotal();
                        TotalsFactory.get().collectTaxTotal();
                    }
                }
            },
            getItemData: function(itemId, key){
                return Items.getItemData(itemId, key);
            },
            getItemsInfo: function(){
                var itemsInfo = [];
                if(Items.items().length > 0){
                    ko.utils.arrayForEach(Items.items(), function(item) {
                        itemsInfo.push(item.getInfoBuyRequest());
                    });
                }
                return itemsInfo;
            },
            getItemsDataForOrder: function(){
                var itemsData = [];
                if(Items.items().length > 0){
                    ko.utils.arrayForEach(Items.items(), function(item) {
                        itemsData.push(item.getDataForOrder());
                    });
                }
                return itemsData;
            },
            getItemsInitData: function(){
                var itemsData = [];
                if(Items.items().length > 0){
                    ko.utils.arrayForEach(Items.items(), function(item) {
                        itemsData.push(item.getData());
                    });
                }
                return itemsData;
            },
            isVirtual: function(){
                var isVirtual = true;
                if(Items.items().length > 0){
                    var notVirtualItem = ko.utils.arrayFilter(Items.items(), function(item) {
                        return item.is_virtual() == false;
                    });
                    isVirtual = (notVirtualItem.length > 0)?false:true;
                }
                return isVirtual;
            },
            totalItems: function(){
                return Items.totalItems();
            },
            totalShipableItems: function(){
                return Items.totalShipableItems();
            },
            collectTaxRate: function(data){
                var self = this;
                var calculateTaxBaseOn = window.webposConfig["tax/calculation/based_on"];
                var address = (calculateTaxBaseOn == 'shipping')?self.CheckoutModel().shippingAddress():self.CheckoutModel().billingAddress();
                data.tax_rate = TaxCalculator().getProductTaxRate(data.tax_class_id, this.customerGroup(), address);
                return data;
            },
            reCollectTaxRate: function(){
                var self = this;
                if(Items.items().length > 0){
                    var calculateTaxBaseOn = window.webposConfig["tax/calculation/based_on"];
                    var address = (calculateTaxBaseOn == 'shipping')?self.CheckoutModel().shippingAddress():self.CheckoutModel().billingAddress();
                    ko.utils.arrayForEach(Items.items(), function(item) {
                        var taxrate = TaxCalculator().getProductTaxRate(item.tax_class_id(), self.customerGroup(), address);
                        self.updateItem(item.item_id(),'tax_rate',taxrate);
                    });
                }
            },
            collectTierPrice: function(){
                var self = this;
                if(Items.items().length > 0){
                    var hasTierPriceItems = ko.utils.arrayFilter(Items.items(), function(item) {
                        return (item.tier_prices())?true:false;
                    });
                    ko.utils.arrayForEach(hasTierPriceItems, function(item) {
                        var tier_prices = item.tier_prices();
                        var itemQty = item.qty();
                        var tier_price = false;
                        if(tier_prices){
                            var validTierPrice = ko.utils.arrayFirst(tier_prices, function(data) {
                                return (self.customerGroup() == data.customer_group_id  && itemQty >= data.qty);
                            });
                            if(validTierPrice){
                                tier_price = validTierPrice.value;
                            }
                        }
                        self.updateItem(item.item_id(),'tier_price',tier_price);
                    });
                }
            },
            validateItemsQty: function(){
                var self = this;
                var error = [];
                if(Items.items().length > 0){
                    ko.utils.arrayForEach(Items.items(), function(item) {
                        var data = item.getData();
                        if(data.product_id != "customsale" && data.product_type != "bundle"){
                            data.customer_group = self.customerGroup();
                            var validate = ProductFactory.get().checkStockItemsInCart(data);
                            if(validate !== true){
                                error.push(validate);
                            }
                        }
                    });
                }
                return (error.length > 0)?error:true;
            },
            getItemChildsQty: function(){
                var qtys = [];
                if(Items.items().length > 0){
                    ko.utils.arrayForEach(Items.items(), function(item) {
                        var data = item.getData();
                        if(data.product_id != "customsale"){
                            if(data.product_type == "bundle"){
                                if(data.bundle_childs_qty){
                                    ko.utils.arrayForEach(data.bundle_childs_qty, function(option) {
                                        qtys.push({id:option.code,qty:option.value});
                                    });
                                }
                            }else{
                                if(data.child_id){
                                    qtys.push({id:data.child_id,qty:data.qty});
                                }else{
                                    qtys.push({id:data.product_id,qty:data.qty});
                                }
                            }
                        }
                    });
                }
                return qtys;
            },
            getQtyInCart: function(productId){
                var qty = 0;
                if(productId && Items.items().length > 0){
                    ko.utils.arrayForEach(Items.items(), function(item) {
                        if(item.getData('product_id') == productId){
                            qty += item.getData('qty');
                        }
                    });
                }
                return qty;
            },
            hasStorecredit: function(){
                if(Items.items().length > 0){
                    var storecreditItem = ko.utils.arrayFirst(Items.items(), function(item) {
                        return (item.product_type() == "customercredit");
                    });
                    if(storecreditItem){
                        return true;
                    }
                }
                return false;
            },
            canCheckoutStorecredit: function(){
                var hasStorecredit = this.hasStorecredit();
                if(hasStorecredit && this.customerId() == ''){
                    return false;
                }
                return true;
            }
        };
    }
);
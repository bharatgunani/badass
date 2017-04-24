/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/checkout/cart/items/item',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/checkout/cart/data/cart'
    ],
    function ($, ko, Item, Helper, CartData) {
        "use strict";
        return {
            apply_tax_after_discount: CartData.apply_tax_after_discount,
            items: CartData.items,
            isEmpty: ko.pureComputed(function(){
                return (CartData.items().length > 0)?false:true;
            }),
            getItems: function(){
                return CartData.items();
            },
            getAddedItem: function(data){
                var isNew = false;
                if(typeof data.item_id != "undefined"){
                    var foundItem = ko.utils.arrayFirst(CartData.items(), function(item) {
                        return (item.item_id() == data.item_id);
                    });
                    if(foundItem && foundItem.length > 0){
                        return foundItem;
                    }
                }else{
                    if(typeof data.hasOption !== "undefined"){
                        var foundItem = ko.utils.arrayFirst(CartData.items(), function(item) {
                            return (
                                    (data.hasOption === false && item.product_id() == data.product_id) || 
                                    (data.hasOption === true && item.product_id() == data.product_id && item.options_label() == data.options_label )
                                   );
                        });
                        if(foundItem){
                            return foundItem;
                        }
                    }
                }
                return isNew;
            },
            addItem: function(data){
                var item = this.getAddedItem(data);
                if(item === false){
                    data.item_id = $.now();
                    var item = new Item();
                    item.init(data);
                    this.items.push(item);
                }else{
                    var qty = item.qty();
                    qty += data.qty;
                    this.setItemData(item.item_id(), "qty", qty);
                }
            },
            getItem: function(itemId){
                var item = false;
                var foundItem = ko.utils.arrayFirst(CartData.items(), function(item) {
                    return (item.item_id() == itemId);
                });
                if(foundItem){
                    item = foundItem;
                }
                return item;
            },
            getItemData: function(itemId, key){
                var item = this.getItem(itemId);
                if(item != false && typeof item[key] != "undefined"){
                    return item[key]();
                }
                return "";
            },
            setItemData: function(itemId, key, value){
                var item = this.getItem(itemId);
                if(item != false){
                    item.setData(key,value);
                }
            },
            removeItem: function(itemId){
                this.items.remove(function (item) {
                    return item.item_id() == itemId; 
                });
            },
            totalItems: function(){
                var total = 0;
                if(CartData.items().length > 0){
                    ko.utils.arrayForEach(CartData.items(), function(item) {
                        total += item.qty();
                    });
                }
                return total;
            },
            totalShipableItems: function(){
                var total = 0;
                if(CartData.items().length > 0){
                    var shipItems = ko.utils.arrayFilter(CartData.items(), function(item) {
                        return (item.is_virtual() == false);
                    });
                    if(shipItems.length > 0){
                        ko.utils.arrayForEach(shipItems, function(item) {
                            total += item.qty();
                        });
                    }
                }
                return total;
            },
        };
    }
);
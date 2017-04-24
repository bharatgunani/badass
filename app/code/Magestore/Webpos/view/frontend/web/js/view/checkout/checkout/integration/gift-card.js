/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */
define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/checkout/checkout/integration/abstract',
         'Magestore_Webpos/js/model/checkout/integration/gift-card-factory',
    ],
    function ($,ko, Abstract, GiftCartFactory) {
        "use strict";
        return Abstract.extend({
            defaults: {
                template: 'Magestore_Webpos/checkout/checkout/integration/giftcard'
            },
            initialize: function () {
                this._super();
                this.model = GiftCartFactory.get();
                this.initData();
            },
            initData: function(){
                var self = this;
                self.balance = ko.pureComputed(function(){
                    return self.convertAndFormatPrice(self.model.balanceAfterApply());
                });
                self.currentAmount = ko.pureComputed(function(){
                    return self.convertAndFormatWithoutSymbol(self.model.currentAmount());
                });
                self.useMaxPoint = self.model.useMaxPoint;
                self.updatingBalance = self.model.updatingBalance;
                self.visible = self.model.visible;
                self.canApply = ko.pureComputed(function(){
                    return (self.model.balance() > 0)?true:false;
                });
                self.code = self.model.giftcardCode;
                self.appliedCards = ko.pureComputed(function(){
                    return self.getAplliedCards();
                });
            },
            pointUseChange: function(el, event){
                var amount = this.getPriceHelper().toNumber(event.target.value);
                amount = (amount > 0)?amount:0;
                amount = this.getPriceHelper().toBasePrice(amount);
                this.model.currentAmount(amount);
                if(amount >= this.model.balance()){
                    amount = this.model.balance();
                    this.model.useMaxPoint(true);
                }else{
                    this.model.useMaxPoint(false);
                }
            },
            useMaxPointChange: function(el, event){
                this.useMaxPoint(event.target.checked);
                this.model.useMaxPoint(event.target.checked);
            },
            apply: function(){
                this.model.apply();
            },
            updateBalance: function(){
                if(this.updatingBalance() == false){
                    try{
                        this.model.updateBalance();
                    }catch(error){
                        console.log(error.message);
                    }
                }
            },
            saveCode: function(data, event){
                this.model.giftcardCode(event.target.value);
                this.updateBalance();
            },
            getAplliedCards: function(){
                var self = this;
                var cards = [];
                var appliedCards = this.model.appliedCards();
                ko.utils.arrayForEach(appliedCards, function(giftcard) {
                    cards.push({
                        code: giftcard.code,
                        value: giftcard.value,
                        balance: giftcard.balance,
                        remain: giftcard.remain,
                        usemax: giftcard.usemax,
                        valueFormated: ko.pureComputed(function(){
                            return self.convertAndFormatPrice(giftcard.value())
                        }),
                        balanceFormated: ko.pureComputed(function(){
                            return self.convertAndFormatPrice(giftcard.balance())
                        }),
                        remainFormated: ko.pureComputed(function(){
                            return self.convertAndFormatPrice(giftcard.remain())
                        })
                    });
                });
                return cards;
            },
            removeCard: function(card){
                this.model.applyGiftCode(card.code, 0);
            },
            editCard: function(card){
                this.model.editCard(card);
            }
        });
    }
);

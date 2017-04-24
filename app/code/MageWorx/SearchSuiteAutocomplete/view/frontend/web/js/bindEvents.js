define(['jquery', 'uiComponent', 'uiRegistry', 'mageUtils'], function($, Component, registry, utils) {
    'use strict';
    return Component.extend({
        defaults: {
            minSearchLength: 2
        },
        initialize: function() {
            this._super();
            utils.limit(this, 'load', this.searchDelay);
            $(this.inputSelector).unbind('input').on('input', $.proxy(this.load, this)).on('input', $.proxy(this.searchButtonStatus, this)).on('focus', $.proxy(this.showPopup, this));
            $(document).on('click', $.proxy(this.hidePopup, this));
            $(document).ready($.proxy(this.load, this));
            $(document).ready($.proxy(this.searchButtonStatus, this));
        },
        load: function(event) {
            var self = this;
            var searchText = $(self.inputSelector).val();
            if (searchText.length < self.minSearchLength) {
                return false;
            }
            registry.get('searchsuiteautocompleteDataProvider', function(dataProvider) {
                dataProvider.searchText = searchText;
                dataProvider.load();
            });
        },
        showPopup: function(event) {
            var self = this,
                searchField = $(self.inputSelector),
                searchFieldHasFocus = searchField.is(':focus') && searchField.val().length >= self.minSearchLength;
            registry.get('searchsuiteautocomplete_form', function(autocomplete) {
                autocomplete.showPopup(searchFieldHasFocus);
            });
        },
        hidePopup: function(event) {
            if ($(this.searchFormSelector).has($(event.target)).length <= 0) {
                registry.get('searchsuiteautocomplete_form', function(autocomplete) {
                    autocomplete.showPopup(false);
                });
            }
        },
        searchButtonStatus: function(event) {
            var self = this,
                searchField = $(self.inputSelector),
                searchButton = $(self.searchButtonSelector),
                searchButtonDisabled = (searchField.val().length > 0) ? false : true;
               //searchButton.attr('disabled', searchButtonDisabled);
        },
        spinnerShow: function() {
            var spinner = $(this.searchFormSelector);
            spinner.addClass('loading');
        },
        spinnerHide: function() {
            var spinner = $(this.searchFormSelector);
            spinner.removeClass('loading');
        }
    });
});
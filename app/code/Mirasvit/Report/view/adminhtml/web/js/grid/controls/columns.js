define([
    'underscore',
    'mageUtils',
    'mage/translate',
    'Magento_Ui/js/grid/controls/columns'
], function (_, utils, $t, Columns) {
    'use strict';

    return Columns.extend({
        defaults: {
            exports: {
                columns: '${ $.provider }:params.columns'
            }
        },

        /**
         * Helper, checks
         *  - if less than one item choosen
         *  - if more then viewportMaxSize choosen
         *
         * @param {Object} elem
         * @returns {Boolean}
         */
        isDisabled: function (elem) {
            var disabled = this._super();

            return disabled || elem.isDisabled;
        },

        /**
         * Counts number of visible columns.
         *
         * @returns {Number}
         */
        countVisible: function () {
            var columns = [];
            _.each(this.elems.filter('visible'), function (item) {
                columns.push(item.index);
            });

            if (this.get('columns') == undefined || columns.length > this.get('columns').length) {
                // set and reload
                this.set('columns', columns);
            }

            return this.elems.filter('visible').length;
        }
    });
});

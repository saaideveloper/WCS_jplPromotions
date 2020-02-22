/**
 * @category  WCS
 * @package   WCS\jplPromotions
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 */
define([
    'uiRegistry',
    'Magento_Ui/js/form/components/fieldset',
    'jquery'
], function (uiRegistry, fieldset, $) {
    'use strict';

    return fieldset.extend({

        /**
         * Check if is offer product rule
         *
         * @returns {boolean}
         */
        isOfferProducRule: function () {
            var value = uiRegistry
                .get('sales_rule_form.sales_rule_form.actions.simple_action')
                .value();
            return value == 'jpl_discount_product' || value == 'jpl_discount_product_per_price_range';
        },

        /**
         * Sets 'opened' flag to true.
         *
         * @returns {Collapsible} Chainable.
         */
        open: function () {
            this._super();

            setTimeout(this.updateProductSelect, 2000, this);

            return this;
        },

        /**
         * Display or not "Cart Item Attribute" in product select
         */
        /*
        updateProductSelect: function (fieldset) {
            var optgroup = $("optgroup[label='Cart Item Attribute']");

            if (optgroup) {
                if (fieldset.isOfferProducRule()) {
                    optgroup.hide();
                } else {
                    optgroup.show();
                }
            }
        }*/
    });
});

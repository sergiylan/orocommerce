define(function(require) {
    'use strict';

    var AddressView;
    var _ = require('underscore');
    var $ = require('jquery');
    var BaseComponent = require('oroui/js/app/views/base/view');

    AddressView = BaseComponent.extend({
        options: {
            selectors: {
                address: null,
                fieldsContainer: null,
                region: null
            },
            hideInputOnShipTo: false
        },

        /**
         * @inheritDoc
         */
        initialize: function(options) {
            this.options = $.extend(true, {}, this.options, options);

            this.$addressSelector = this.$el.find(this.options.selectors.address);
            this.$fieldsContainer = this.$el.find(this.options.selectors.fieldsContainer);
            this.$regionSelector = this.$el.find(this.options.selectors.region);

            this.needCheckAddressTypes = this.options.selectors.hasOwnProperty('shipToBillingCheckbox');
            if (this.needCheckAddressTypes) {
                this.typesMapping = this.$addressSelector.data('addresses-types');
                this.$shipToBillingCheckbox = this.$el.find(this.options.selectors.shipToBillingCheckbox);
                this.$shipToBillingCheckbox.on('change', _.bind(this._handleShipToBillingAddressCheckbox, this));
                this.shipToBillingContainer = this.$shipToBillingCheckbox.closest('fieldset');
            }

            this.$addressSelector.on('change', _.bind(this._onAddressChanged, this));
            this.$regionSelector.on('change', _.bind(this._onRegionListChanged, this));

            if (this.$fieldsContainer.find('.notification_error').length) {
                this.$fieldsContainer.removeClass('hidden');
            }

            this._onAddressChanged();
            this._handleShipToBillingAddressCheckbox();
        },

        _handleShipToBillingAddressCheckbox: function(e) {
            var isOneOption = this.$addressSelector[0].length === 1;
            var disabled = this.needCheckAddressTypes ? this.$shipToBillingCheckbox.prop('checked') : false;
            if (!disabled && this._isFormVisible()) {
                this._showForm();
            } else {
                this._hideForm(true);
                this.$addressSelector.focus();
            }
            this.$addressSelector.prop('disabled', disabled || isOneOption).inputWidget('refresh');
            if (isOneOption) {
                this.$addressSelector.inputWidget('dispose');
                this.$addressSelector.hide();
            }

            if (!this.options.hideInputOnShipTo) {
                return;
            }

            this.$addressSelector.siblings('.select2-container').toggle(!disabled);
        },

        _onAddressChanged: function(e) {
            if (this._isFormVisible()) {
                this._showForm();
            } else {
                this._hideForm();
            }
        },

        _isFormVisible: function() {
            return this.$addressSelector[0].length === 1 || this.$addressSelector.val() === '0';
        },

        _showForm: function() {
            if (this.needCheckAddressTypes) {
                this.shipToBillingContainer.removeClass('hidden');
            }

            this.$fieldsContainer.removeClass('hidden');
        },

        _hideForm: function(showCheckbox) {
            if (this.needCheckAddressTypes) {
                if (showCheckbox || _.indexOf(this.typesMapping[this.$addressSelector.val()], 'shipping') > -1) {
                    this.shipToBillingContainer.removeClass('hidden');
                } else {
                    this.$shipToBillingCheckbox.prop('checked', false);
                    this.$shipToBillingCheckbox.trigger('change');
                    this.shipToBillingContainer.addClass('hidden');
                }
            }

            this.$fieldsContainer.addClass('hidden');
        },

        _setAddressSelectorState: function(state) {
            this.$addressSelector.prop('disabled', state).inputWidget('refresh');
        },

        _onRegionListChanged: function(e) {
            this.$regionSelector.inputWidget('refresh');
        }
    });

    return AddressView;
});

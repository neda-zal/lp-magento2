define([
    'jquery',
    'ko',
    'Magento_Ui/js/form/element/select',
    'Magento_Checkout/js/model/quote',
    'jquery/validate',
    'mage/translate',
    'mage/storage'
], function ( $, ko, Select, quote, storage ) {
    return Select.extend({
        initialize: function () {
            var self = this;
            self.availableTerminals      = ko.observableArray ();
            self.availableTerminalCities = ko.observableArray (
                $.map ( window.checkoutConfig.terminal.list, function ( obj, city ) { return [ city ]; } )
            );
            self.selectedTerminalCity      = ko.observable ();
            self.selectTerminalCityCaption = $.mage.__( 'Please select LP EXPRESS terminal locality..' );
            self.selectTerminalCaption     = $.mage.__( 'Please select LP EXPRESS terminal..' );

            this._super ();

            self.selectedTerminalCity.subscribe ( function ( selectedCity ) {
                self.availableTerminals ( [] );
                self.availableTerminals (
                    $.map ( window.checkoutConfig.terminal.list, function ( terminals, city ) {
                        if ( city === selectedCity ) {
                            return $.map ( terminals, function ( terminal, id ) {
                                return { id: id, name: terminal };
                            });
                        }
                    } )
                );
            });
        },
        selectedMethod: function () {
            let method = quote.shippingMethod();

            // if is selected method
            if ( method ) {
                // Hide or show terminal validation error
                $( 'div[name="shippingAddress.lpexpress_terminal"] .field-error')
                    .css ( { display: method.method_code === 'lpcarrierlpexpress_terminal' ? 'block' : 'none' } );

                // Add padding bottom to the container
                $( 'div[name="shippingAddress.lpexpress_terminal"]' )
                    .css ( { paddingBottom: method.method_code === 'lpcarrierlpexpress_terminal' ? '20px' : '0px' } );

                return method.method_code;
            }

            return null;
        },
        getDeliveryTime: function () {
            // Get data from configProvider
            let deliveryTimeObj = window.checkoutConfig.lp_delivery_time;

            if ( deliveryTimeObj.hasOwnProperty ( this.selectedMethod () ) ) {
                return deliveryTimeObj [ this.selectedMethod () ];
            }

            return null;
        }
    });
});

var config = {
    config: {
        mixins: {
            'Magento_Ui/js/lib/validation/validator': {
                'Eshoper_LPShipping/js/validation-mixin': true
            },
        }
    },
    'map': {
        '*': {
            'Magento_Checkout/js/model/shipping-save-processor/payload-extender'
                : 'Eshoper_LPShipping/js/shipping-save-processor-payload-extender'
        }
    }
};

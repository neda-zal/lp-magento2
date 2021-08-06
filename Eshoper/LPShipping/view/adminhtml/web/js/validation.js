require([
        'jquery',
        'mage/translate',
        'jquery/validate'],
    function($){
        $.validator.addMethod(
            'validate-phone-number', function (v) {
                if ( v.length > 0 )
                    return /^(370)\d{8}$/.test( v );
                else return true;
            }, $.mage.__('Field format must be 370XXXXXXX'));
    }
);

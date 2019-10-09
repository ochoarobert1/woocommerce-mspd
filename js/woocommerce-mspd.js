//var successCallback = function (data) {
//
//    var checkout_form = $('form.woocommerce-checkout');
//
//    // add a token to our hidden input field
//    // console.log(data) to find the token
//    checkout_form.find('#misha_token').val(data.token);
//
//    // deactivate the tokenRequest function event
//    checkout_form.off('checkout_place_order', tokenRequest);
//
//    // submit the form now
//    checkout_form.submit();
//
//};
//
//var errorCallback = function (data) {
//    console.log(data);
//};
//
//var tokenRequest = function () {
//
//    // here will be a payment gateway function that process all the card data from your form,
//    // maybe it will need your Publishable API key which is misha_params.publishableKey
//    // and fires successCallback() on success and errorCallback on failure
//    return false;
//
//};

jQuery(function ($) {

    //    var checkout_form = $('form.woocommerce-checkout');
    //    checkout_form.on('checkout_place_order', tokenRequest);

    jQuery('input[name="lacpsCardNumber"]').mask('0000 0000 0000 0000', {
        placeholder: "____ ____ ____ ____"
    });

    jQuery('input[name="lacpsinputYear"]').mask('0000', {
        placeholder: "____"
    });

    jQuery('input[name="lacpsCCV"]').mask('000', {
        placeholder: "___"
    });

    jQuery(document.body).on('change', "#lacps_month", function (e) {
        var value = jQuery(this).val();
        jQuery('#lacps_month option').each(function () {
            jQuery(this).removeAttr('selected');
        });
        jQuery(this).find('option[value="' + value + '"]').attr('selected', 'selected');
    });
});

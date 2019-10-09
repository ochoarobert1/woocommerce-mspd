/* MAIN JQUERY FUNCTIONS */
jQuery(function ($) {
    jQuery('button').on('click', function(e) {
        e.stopPropagation();
        jQuery.ajax({
            type: 'POST',
            url: admin_url.ajax_url,
            data: {
                action: 'woocommerce_mspd_main_action',
            },
            success: function (response) {
                console.log(response);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
                console.log(jqXHR);
                console.log(textStatus);
            }
        });
    });

});

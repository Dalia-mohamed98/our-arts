<?php



// remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );

// add_action( 'woocommerce_checkout_after_order_review', 'woocommerce_checkout_coupon_form' );

// REMOVE coupon field on the top of the cart and checkout page ***”’
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );

// ADD Coupon field before payment ****
// add_action( 'woocommerce_review_order_before_payment' , 'woocommerce_checkout_coupon_form' , 10 );


function cw_scripts() {
    wp_enqueue_script('jquery-ui-dialog');
}
add_action('wp_enqueue_scripts', 'cw_scripts');

function cw_show_coupon_js() {
    wc_enqueue_js('$("a.showcoupon").parent().hide();');
    wc_enqueue_js('$.ui.dialog.prototype._focusTabbable = function(){};
                    dialog = $("form.checkout_coupon").dialog({
                       autoOpen: true,
                       minHeight: 0,
                       modal: false,
                       appendTo: "#coupon-anchor",
                       position: { my: "right", at: "right", of: "#coupon-anchor"},
                       draggable: false,
                       resizable: false,
                       dialogClass: "coupon-special",
                       closeText: "x",
                       buttons: {}});');

    wc_enqueue_js('$("#show-coupon-form").click( function() {
                       if (dialog.dialog("isOpen")) {
                           $(".checkout_coupon").hide();
                           dialog.dialog( "close" );
                       } else {
                           $(".checkout_coupon").show();
                           dialog.dialog( "open" );
                       }
                       return false;});');
}
add_action('woocommerce_before_checkout_form', 'cw_show_coupon_js');


function cw_show_coupon() {
    global $woocommerce;
    if ($woocommerce->cart->needs_payment()) {
        echo '<div id="coupon-anchor"></div><hr>';
    }
}

add_action( 'woocommerce_review_order_before_payment' , 'cw_show_coupon' );

?>
<?php

//merge first and last name in checkout
add_filter( 'woocommerce_billing_fields' , 'ced_remove_billing_fields' );
function ced_remove_billing_fields( $fields ) {
         unset($fields['billing_last_name']);
		 unset($fields['shipping_last_name']);
         return $fields;
}

add_filter( 'woocommerce_checkout_fields' , 'ced_rename_checkout_fields' );
// Change placeholder and label text
function ced_rename_checkout_fields( $fields ) {
$fields['billing']['billing_first_name']['placeholder'] = 'Name';
$fields['billing']['billing_first_name']['label'] = 'الإسم';
$fields['shipping']['shipping_first_name']['placeholder'] = 'Name';
$fields['shipping']['shipping_first_name']['label'] = 'الإسم';
return $fields;
}

?>
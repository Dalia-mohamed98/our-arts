<?php
/**
 * Premium options
 *
 * @author Your Inspiration Themes
 * @package YITH Woocommerce Frequently Bought Together
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WFBT' ) ) {
	exit;
} // Exit if accessed directly

$options = array(
    'premium' => array(
	    'landing' => array(
		    'type' => 'custom_tab',
		    'action' => 'yith_wfbt_premium'
	    )
    )
);

return $options;

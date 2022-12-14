<?php
/*
Plugin Name: YITH WooCommerce Order Tracking
Plugin URI: http://yithemes.com/themes/plugins/yith-woocommerce-order-tracking/
Description: With <code><strong>YITH WooCommerce Order Tracking</strong></code> Easy managing order tracking information for WooCommerce orders. Set the carrier and the tracking code and your customers will get notified about their shipping. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
Author: YITH
Text Domain: yith-woocommerce-order-tracking
Version: 1.2.17
Author URI: http://yithemes.com/
WC requires at least: 3.3.0
WC tested up to: 4.5

@author YITH
@package YITH WooCommerce Order Tracking
@version 1.2.17
*/

/*  Copyright 2018  Your Inspiration Themes  (email : plugins@yithemes.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

function yith_ywot_install_woocommerce_admin_notice() {
	?>
	<div class="error">
		<p><?php _e( 'YITH WooCommerce Order Tracking is enabled but not effective. It requires WooCommerce in order to work.', 'yit' ); ?></p>
	</div>
<?php
}

function yith_ywot_install_free_admin_notice() {
	?>
	<div class="error">
		<p><?php _e( 'You can\'t activate the free version of YITH WooCommerce Order Tracking while you are using the premium one.', 'yit' ); ?></p>
	</div>
<?php
}

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

//region    ****    Define constants
if ( ! defined( 'YITH_YWOT_FREE_INIT' ) ) {
	define( 'YITH_YWOT_FREE_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_YWOT_SLUG' ) ) {
    define( 'YITH_YWOT_SLUG', 'yith-woocommerce-order-tracking' );
}

if ( ! defined( 'YITH_YWOT_VERSION' ) ) {
	define( 'YITH_YWOT_VERSION', '1.2.17' );
}

if ( ! defined( 'YITH_YWOT_FILE' ) ) {
	define( 'YITH_YWOT_FILE', __FILE__ );
}

if ( ! defined( 'YITH_YWOT_DIR' ) ) {
	define( 'YITH_YWOT_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YITH_YWOT_URL' ) ) {
	define( 'YITH_YWOT_URL', plugins_url( '/', __FILE__ ) );
}

if ( ! defined( 'YITH_YWOT_ASSETS_URL' ) ) {
	define( 'YITH_YWOT_ASSETS_URL', YITH_YWOT_URL . 'assets' );
}

if ( ! defined( 'YITH_YWOT_TEMPLATE_PATH' ) ) {
	define( 'YITH_YWOT_TEMPLATE_PATH', YITH_YWOT_DIR . 'templates' );
}

if ( ! defined( 'YITH_YWOT_ASSETS_IMAGES_URL' ) ) {
	define( 'YITH_YWOT_ASSETS_IMAGES_URL', YITH_YWOT_ASSETS_URL . '/images/' );
}
//endregion

/* Plugin Framework Version Check */
if ( ! function_exists ( 'yit_maybe_plugin_fw_loader' ) && file_exists ( YITH_YWOT_DIR . 'plugin-fw/init.php' ) ) {
    require_once ( YITH_YWOT_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader ( YITH_YWOT_DIR );

function yith_ywot_init() {

	/**
	 * Load text domain and start plugin
	 */
	load_plugin_textdomain( 'yith-woocommerce-order-tracking', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	// Load required classes and functions
	require_once( YITH_YWOT_DIR . 'class.yith-woocommerce-order-tracking.php' );

	global $YWOT_Instance;
	$YWOT_Instance = new Yith_WooCommerce_Order_Tracking();
}

add_action( 'yith_ywot_init', 'yith_ywot_init' );


function yith_ywot_install() {

	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'yith_ywot_install_woocommerce_admin_notice' );
	} elseif ( defined( 'YITH_YWOT_PREMIUM' ) ) {
		add_action( 'admin_notices', 'yith_ywot_install_free_admin_notice' );
		deactivate_plugins( plugin_basename( __FILE__ ) );
	} else {
		do_action( 'yith_ywot_init' );
	}
}

add_action( 'plugins_loaded', 'yith_ywot_install', 11 );



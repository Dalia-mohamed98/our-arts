<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              piwebsolution.com
 * @since             4.7.7
 * @package           Pi_Edd
 *
 * @wordpress-plugin
 * Plugin Name:       Estimate delivery date for WooCommerce
 * Plugin URI:        https://www.piwebsolution.com/cart/?add-to-cart=879
 * Description:       WooCommerce Estimated delivery date per product, You don't have to set dates in each product, just add it in shipping method, based on shipping days and product preparation days it shows estimated shipping date per product and also considers holidays
 * Version:           4.7.7
 * Author URI:        https://www.piwebsolution.com/
 * Author:            PI WebSolution
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pi-edd
 * Domain Path:       /languages
 * WC requires at least: 3.0.0
 * WC tested up to: 4.2.0   
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/* 
    Making sure woocommerce is there 
*/
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if(!is_plugin_active( 'woocommerce/woocommerce.php')){
    function pi_edd_my_error_notice() {
        ?>
        <div class="error notice">
            <p><?php _e( 'Please Install and Activate WooCommerce plugin, without that this plugin cant work', 'pi-edd' ); ?></p>
        </div>
        <?php
    }
    add_action( 'admin_notices', 'pi_edd_my_error_notice' );
    deactivate_plugins(plugin_basename(__FILE__));
    return;
}


/**
 * Checking Pro version
 */
function pi_edd_pro_check(){
	if(is_plugin_active( 'estimate-delivery-date-for-woocommerce-pro/pi-edd-pro.php')){
		return true;
	}
	return false;
}

if(is_plugin_active( 'estimate-delivery-date-for-woocommerce-pro/pi-edd.php')){
    function pi_edd_my_pro_notice() {
        ?>
        <div class="error notice">
            <p><?php _e( 'You have the pro version of Estimate delivery date for Woocommerce active', 'pi-edd' ); ?></p>
        </div>
        <?php
    }
    add_action( 'admin_notices', 'pi_edd_my_pro_notice' );
    deactivate_plugins(plugin_basename(__FILE__));
    return;
}else{


/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PI_EDD_VERSION', '4.7.7' );
define( 'PISOL_EDD_DELETE_SETTING', false );
define('PI_EDD_BUY_URL', 'https://www.piwebsolution.com/checkout/?add-to-cart=879&variation_id=1122');
define('PI_EDD_PRICE', '$34');
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pi-edd-activator.php
 */
function activate_pi_edd() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pi-edd-activator.php';
	Pi_Edd_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-pi-edd-deactivator.php
 */
function deactivate_pi_edd() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pi-edd-deactivator.php';
	Pi_Edd_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_pi_edd' );
register_deactivation_hook( __FILE__, 'deactivate_pi_edd' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-pi-edd.php';


function pisol_edd_plugin_link( $links ) {
	$links = array_merge( array(
        '<a href="' . esc_url( admin_url( '/admin.php?page=pi-edd' ) ) . '">' . __( 'Settings' ) . '</a>',
        '<a style="color:#f00; font-weight:bold;" target="_blank" href="' . esc_url(PI_EDD_BUY_URL) . '">' . __( 'Buy PRO Version' ) . '</a>'
	), $links );
	return $links;
}
add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'pisol_edd_plugin_link' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_pi_edd() {

	$plugin = new Pi_Edd();
	$plugin->run();

}
run_pi_edd();
}


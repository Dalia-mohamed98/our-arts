<?php
/*
Plugin Name: MobiLoud Smart App Banners
Plugin URI: https://www.mobiloud.com
Description: Creating a App banner prompt within the site
Author: MobiLoud
Version: 1.1.3
Author URI: https://www.mobiloud.com
License: GPLv2 or later
Text Domain: msab
*/

// Make sure we don't expose any info if called directly
if ( ! function_exists( 'add_action' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit;
}
define( 'MSAB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MSAB_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'MSAB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once( MSAB_PLUGIN_DIR . 'includes/class-msab.php' );

register_activation_hook( __FILE__, array( 'MSAB', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'MSAB', 'plugin_deactivation' ) );

if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	require_once( MSAB_PLUGIN_DIR . 'admin/class-msab-admin.php' );
}
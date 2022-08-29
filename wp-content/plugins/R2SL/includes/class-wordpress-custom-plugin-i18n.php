<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://christophercasper.com/
 * @since      1.0.0
 *
 * @package    R2SL
 * @subpackage R2SL/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 * since      1.0.0
 * Author     Mandeep Saini
 * Site URL   http://logix.network/
 * package    R2SL
 * subpackage R2SL/includes
 */
class Wordpress_Custom_Plugin_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wordpress-custom-plugin',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}

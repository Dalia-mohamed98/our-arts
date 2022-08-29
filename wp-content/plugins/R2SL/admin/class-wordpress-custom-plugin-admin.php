<?php

/**
 * The admin-specific functionality of the plugin.
 *
 *
 * @since      1.0.0
 * since      1.0.0
 * Author     Mandeep Saini
 * Site URL   http://logix.network/
 * package    R2SL
 * subpackage R2SL/admin
 */

class Wordpress_Custom_Plugin_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		// Lets add an action to setup the admin menu in the left nav
		add_action( 'admin_menu', array($this, 'add_admin_menu') );
		// Add some actions to setup the settings we want on the wp admin page
	
	}

	/**
	 * Add the menu items to the admin menu
	 *
	 * @since    1.0.0
	 */
	public function add_admin_menu() {

			global $wpdb;
			$settingAtrs = $wpdb->get_row( "SELECT * FROM wp_logixgridsetting WHERE ID = 1");
			if($settingAtrs->pluginName) { $pluginname = $settingAtrs->pluginName; }
			else {$pluginname = 'Plugin Name';}
			
			// Main Menu Item
			add_menu_page(
				'Main Page',
				$pluginname,
				'manage_options',
				'waybill-creation',
				array($this, 'display_custom_plugin_admin_page'),
				'dashicons-store'
				);
				
				add_submenu_page(
					'waybill-creation',
					'Oreder place',
					'Orders',
					'manage_options',
					'waybill-creation',
					array($this, 'display_custom_plugin_admin_page')
				);

				add_submenu_page(
					'waybill-creation',
					'Calculate Tariff',
					'Calculate Tariff',
					'manage_options',
					'calculate-tariff',
					array($this, 'display_custom_plugin_admin_calculate_tariff')
				);

				add_submenu_page(
					'waybill-creation',
					'Create Waybill',
					'Create Waybill',
					'manage_options',
					'create-waybill',
					array($this, 'display_custom_plugin_admin_createwaybill')
				);
				
				add_submenu_page(
					'waybill-creation',
					'Pickup Request',
					'Pickup Request',
					'manage_options',
					'pickup-request',
					array($this, 'display_custom_plugin_admin_pickup')
				); 
			
			add_submenu_page(
				'waybill-creation',
				'Settings',
				'Settings',
				'manage_options',
				'settings-page',
				array($this, 'display_custom_plugin_admin_page_two')
			); 
			
			
				
				add_menu_page(
    'wc-auction-reports',       // parent slug
    'Recent Bids',    // page title
    'Recent Bids',             // menu title
    'manage_options',           // capability
    'wc-auction-reports', // slug
    'acutions_customers_spendings_list' // callback
); 


add_submenu_page(
    'wc-auction-reports',       // parent slug
    'Customer Spending',    // page title
    'Customer Spending',             // menu title
    'manage_options',           // capability
    'wc-acutions-customers-spendings', // slug
    'acutions_customers_spendings_list' // callback
); 

add_submenu_page(
    'wc-auction-reports',       // parent slug
    'Customer Bids',    // page title
    'Customer Bids',             // menu title
    'manage_options',           // capability
    'wc-acutions-customers-bids', // slug
    'acutions_customers_bids_list' // callback
);  


	}

	/**
	 * Callback function for displaying the admin settings page.
	 *
	 * @since    1.0.0
	 */
	

	/**
	 * Callback function for displaying the second sub menu item page.
	 *
	 * @since    1.0.0
	 */
	public function display_custom_plugin_admin_page_two(){
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wordpress-custom-plugin-admin-settings.php';
	}

	public function display_custom_plugin_admin_page(){
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wordpress-custom-plugin-admin-main.php';
	}

	public function display_custom_plugin_admin_calculate_tariff(){
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wordpress-custom-plugin-admin-calculate-tariff.php';
	}

	public function display_custom_plugin_admin_pickup(){
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wordpress-custom-plugin-admin-pick-up.php';
	} 

	public function display_custom_plugin_admin_createwaybill(){
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wordpress-custom-plugin-admin-create-waybill.php';
	}

	
	/**
	 * Setup sections in the settings
	 *
	 * @since    1.0.0
	 */
	public function setup_sections() {
		add_settings_section( 'section_one', 'Section One', array($this, 'section_callback'), 'wordpress-custom-plugin-options' );
		add_settings_section( 'section_two', 'Section Two', array($this, 'section_callback'), 'wordpress-custom-plugin-options' );
	}

		

	/**
	 * Admin Notice
	 * 
	 * This displays the notice in the admin page for the user
	 *
	 * @since    1.0.0
	 */
	public function admin_notice($message) { ?>
		<div class="notice notice-success is-dismissible">
			<p><?php echo($message); ?></p>
		</div><?php
	}

	/**
	 * This handles setting up the rewrite rules for Past Sales
	 *
	 * @since    1.0.0
	 */
	public function setup_rewrites() {
		//
		$url_slug = 'custom-plugin';
		// Lets setup our rewrite rules
		add_rewrite_rule( $url_slug . '/?$', 'index.php?custom_plugin=index', 'top' );
		add_rewrite_rule( $url_slug . '/page/([0-9]{1,})/?$', 'index.php?custom_plugin=items&custom_plugin_paged=$matches[1]', 'top' );
		add_rewrite_rule( $url_slug . '/([a-zA-Z0-9\-]{1,})/?$', 'index.php?custom_plugin=detail&custom_plugin_vehicle=$matches[1]', 'top' );


		// Lets flush rewrite rules on activation
		flush_rewrite_rules();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wordpress_Custom_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wordpress_Custom_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wordpress-custom-plugin-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wordpress_Custom_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wordpress_Custom_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wordpress-custom-plugin-admin.js', array( 'jquery' ), $this->version, false );

	}

}

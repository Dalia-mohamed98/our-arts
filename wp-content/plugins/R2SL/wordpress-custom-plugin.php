<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 * Plugin Name: R2SL
 * since      1.0.0
 * Author     Mandeep Saini
 * Site URL   http://logix.network/
 * package    R2SL
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wordpress-custom-plugin-activator.php
 */
function activate_wordpress_custom_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wordpress-custom-plugin-activator.php';
	Wordpress_Custom_Plugin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wordpress-custom-plugin-deactivator.php
 */
function deactivate_wordpress_custom_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wordpress-custom-plugin-deactivator.php';
	Wordpress_Custom_Plugin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wordpress_custom_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_wordpress_custom_plugin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wordpress-custom-plugin.php';


function run_wordpress_custom_plugin() {

	$plugin = new Wordpress_Custom_Plugin();
	$plugin->run();

}
run_wordpress_custom_plugin();

// Define Constants.
define( 'RPE_URI', plugins_url( 'rest-api-endpoints' ) );
define( 'RPE_TEMPLATE_PATH', plugin_dir_path( __FILE__ ) . 'templates/' );
define( 'RPE_PLUGIN_PATH', __FILE__ );
include_once 'api/class-rae-register-posts-api.php';


/*******Declare Woo-Commerce Custom Status Start*********/
function get_custom_order_statuses(){
    return array(
        'wc-at-origin'        => __('At Origin'),     
        'wc-shipment-received'     => __('Shipment Received'), 
        'wc-at-warehouse'           => __('At Warehouse'),      
		'wc-in-transit'           => __('In Transit'),
		'wc-out-for-delivery'        => __('Out For Delivery'),     
        'wc-delivered'     => __('Delivered'), 
        'wc-rto'           => __('RTO'),      
		'wc-rto-delivered'           => __('RTO Delivered'), 
		'wc-undelivered'        => __('Undelivered'),     
        'wc-refund'     => __('Refund'), 
        'wc-refund-made'           => __('Refund Made'),      
		'wc-re-schedule'           => __('Re-schedule'),
		'wc-schedule-for-dispatch'        => __('Schedule For Dispatch'),     
        'wc-delivery-schedule'     => __('Delivery Schedule'), 
        'wc-partial-delivered'           => __('Partial-Delivered') 
    );
}


// Register custom Order statuses
add_action( 'init', 'register_custom_order_statuses' );
function register_custom_order_statuses() {
    // Loop through custom order statuses array (key/label pairs)
    foreach( get_custom_order_statuses() as $key => $label ) {
        register_post_status( $key, array(
            'label'                     => $label,
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( $label . ' <span class="count">(%s)</span>', $label . ' <span class="count">(%s)</span>' )
        ) );
    }
}

// Add custom Order statuses
add_filter( 'wc_order_statuses', 'add_custom_order_statuses', 10, 1 );
function add_custom_order_statuses( $order_statuses ) {
    $sorted_order_statuses = array(); // Initializing

    foreach ( $order_statuses as $key => $label ) {
        $sorted_order_statuses[ $key ] = $label;

        if ( $key === 'wc-completed' ) {
            // Loop through custom order statuses array (key/label pairs)
            foreach( get_custom_order_statuses() as $custom_key => $custom_label ) {
                $sorted_order_statuses[$custom_key] = $custom_label;
            }
        }
    }

    return $sorted_order_statuses;
}

/*******Declare Woo-Commerce Custom Status Finish*********/


/*******Define the ajaz section start****************/

function example_ajax_enqueue() {

	// Enqueue javascript on the frontend.
	wp_enqueue_script(
		'example-ajax-script',
		plugin_dir_path( __DIR__ ).'js/wordpress-custom-plugin-admin.js',
		array('jquery')
	);
	// The wp_localize_script allows us to output the ajax_url path for our script to use.
	wp_localize_script(
		'example-ajax-script',
		'example_ajax_obj',
		array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) )
	);
}
add_action( 'wp_enqueue_scripts', 'example_ajax_enqueue' );

/*******Define the ajaz section finish****************/

/*******Include the action file Start****************/
require plugin_dir_path( __FILE__ ) . 'admin/partials/status_action.php';
/*******Include the action file finish****************/

/*******Define to create table file Start****************/
function waybill_table()
{      
  global $wpdb; 
  $db_table_name = $wpdb->prefix . 'logixgridwaybill';  // table name
  $charset_collate = $wpdb->get_charset_collate();
  if($wpdb->get_var( "show tables like '$db_table_name'" ) != $db_table_name) 
  {
	$sql = "CREATE TABLE $db_table_name ( 
			`ID` INT(255) NOT NULL AUTO_INCREMENT , 
			`wp_order_ID` VARCHAR(255) NOT NULL , 
			`waybill_number` VARCHAR(255) NOT NULL , 
			`waybill_file_name` VARCHAR(255) NOT NULL , 
			`waybill_status` VARCHAR(255) NOT NULL ,
			`waybill_remark` VARCHAR(255) NOT NULL , 
			`waybill_created` VARCHAR(255) NOT NULL ,
			`waybill_updated` VARCHAR(255) NOT NULL ,
			`service_name` VARCHAR(255) NOT NULL , 
			`pickup_number` VARCHAR(255) NOT NULL , 
			PRIMARY KEY (`ID`)) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	}
   add_option( 'test_db_version', $test_db_version );
} 
register_activation_hook( __FILE__, 'waybill_table' );

function logix_setting_table()
{      
  global $wpdb; 
  $db_tb_name = $wpdb->prefix . 'logixgridsetting';  // table name
  $charset_collate = $wpdb->get_charset_collate();
  if($wpdb->get_var( "show tables like '$db_tb_name'" ) != $db_tb_name) 
  {
	$sql = "CREATE TABLE $db_tb_name ( 
			`ID` INT(255) NOT NULL AUTO_INCREMENT , 
			`secureKey` VARCHAR(255) NOT NULL , 
			`accessKey` VARCHAR(255) NOT NULL , 
			`customerCode` VARCHAR(255) NOT NULL ,
			'serviceCode'  VARCHAR(255) NOT NULL ,
			'sourceCountry'  VARCHAR(255) NOT NULL ,
			'pluginName'  VARCHAR(255) NOT NULL ,
			PRIMARY KEY (`ID`)) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	}
   add_option( 'test_db_version', $test_db_version );

} 
register_activation_hook( __FILE__, 'logix_setting_table' );

/*******Define to create table file Finish****************/


add_action( 'admin_post_add_setting', 'prefix_admin_add_setting' );
//this next action version allows users not logged in to submit requests

//if you want to have both logged in and not logged in users submitting, you have to add both actions!

add_action( 'admin_post_nopriv_add_setting', 'prefix_admin_add_setting' );
function prefix_admin_add_setting() {

	$returnURL = esc_url( admin_url( 'admin-post.php')).'?page=custom-plugin%2Fsettings-page';
	if ( isset( $_REQUEST['settingsubmit'] ) ) 
	{
		$secureKey = $_REQUEST['secure_key'];
		$accessKey = $_REQUEST['access_key'];
		$customerCode = $_REQUEST['customer_code'];
		$serviceCode  = $_REQUEST['service_code'];
		$sourceCountry  = $_REQUEST['source_country'];
		$pluginName  = $_REQUEST['plugin_name'];
		global $wpdb;
		$occurID = $wpdb->get_row( "SELECT * FROM wp_logixgridsetting WHERE ID = 1" , ARRAY_A );
		$tbID = $occurID['ID'];
		if ($tbID == '1' )
		{
			//$wpdb->show_errors();
			$updateQuery = $wpdb->update( 'wp_logixgridsetting', array( 'secureKey' => $secureKey, 'accessKey' => $accessKey, 'customerCode' => $customerCode, 'serviceCode' => $serviceCode, 'sourceCountry' => $sourceCountry,'pluginName' => $pluginName), array( 'ID' => 1));
			if( $updateQuery === false)
			{
				echo '<script> alert("Please Try Again"); window.history.back();</script>';
				//header('Location: '.$returnURL);
			}
			else
			{
				echo '<script> alert("Update Successfully");window.history.back();</script>';
				//header('Location: '.$returnURL);
			}
			//$wpdb->print_error();

		}   
		else
		{
			/************Finish**************/
			$wpdb->insert(
				'wp_logixgridsetting',
				array(
					'secureKey' => $secureKey,
					'accessKey' => $accessKey,
					'customerCode' => $customerCode,
					'serviceCode' => $serviceCode,
					'sourceCountry' => $sourceCountry
				),
				array('%s','%s','%s','%s')
			  );
			  $meta_id = $wpdb->insert_id;
			  if($meta_id)
			  {
				echo '<script> alert("Update Successfully");window.history.back();</script>';
				//header('Location: '.$returnURL);
			}
			else
			{
				echo '<script> alert("Please Try Again");window.history.back();</script>';
				//header('Location: '.$returnURL);
			}
			/************Finish**************/  
		}
	
	}
   
}




 

	
	




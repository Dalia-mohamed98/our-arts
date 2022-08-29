<?php
/*  
* Plugin Name: mylerz  
* Plugin URI:  
* Description: Convenient and Friendly parcel delivery service
* Author: Softec  
* Version: 1.0.0  
* Author URI:  
* License: 
* Text Domain:  
* Domain Path: /languages/  
*/

if (!class_exists('Mylerz_Shipping_Method')) {

    class Mylerz_Shipping_Method extends WC_Shipping_Method
    {
        public function __construct()
        {
            $this->id = 'mylerz';
            $this->method_title = __('Mylerz Settings', 'mylerz');
            $this->method_description = __('Shipping Method for Mylerz', 'mylerz');
            $this->init();
            $this->enabled = isset($this->settings['enabled']) ? $this->settings['enabled'] : 'yes';
            $this->title = isset($this->settings['title']) ? $this->settings['title'] : __('Mylerz Shipping', 'mylerz');
            // include_once __DIR__ . '../../core/class-mylerz-helper.php';
        }


        public function init()
        {
            // Load the settings API
            $this->init_form_fields();
            $this->init_settings();
            // Save settings in admin if you have any defined
            add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
        }

        public function init_form_fields()
        {
            $this->form_fields = include('data-mylerz-settings.php');
        }
    }

}


?>
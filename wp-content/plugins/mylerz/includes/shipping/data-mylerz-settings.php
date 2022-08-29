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


return array(
    'enabled' => array(
        'title' => __('Enable', 'mylerz'),
        'type' => 'checkbox',
        'description' => __('Enable Mylerz shipping', 'mylerz'),
        'default' => 'yes'
    ),
    'title' => array(
        'title' => __('Title', 'mylerz'),
        'type' => 'text',
        'description' => __('Title to be display on site', 'mylerz'),
        'default' => __('Mylerz Shipping', 'mylerz')
    ),

    
    'user_name' => array(
        'title' => __('* User Name', 'mylerz'),
        'type' => 'text',
        'id' => 'user_name'
    ),
    'password' => array(
        'title' => __('* Password', 'mylerz'),
        'type' => 'password',
        'id' => 'pass'
    )
);

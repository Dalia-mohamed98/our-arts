<?php
/**
 * Plugin Name: updates plugins
 * Plugin URI: https://wordpress.org/plugins/woocommerce-egypt-cities/
 * Description: A short plugin that updates other plugins. 
 * Version: 1.0.0
 * Author: Dalia Mohamed
 * Developer: Dalia Mohamed
 *

 * WC requires at least: 2.2
 * WC tested up to: 4.0.1
 
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

/////////////////includes////////////////////////
require_once 'woocommerce/checkout/first-last_name.php';
require_once 'woocommerce/checkout/form-coupon.php';
require_once 'woocommerce/send_sms.php';
require_once 'woocommerce/order_status.php';
require_once 'dokan/print.php';
require_once 'dokan/dashboard.php';
require_once 'dokan/orders.php';
require_once 'mylerz/updates.php';

wp_enqueue_style( 'style', '/wp-content/plugins/updates-plugins/assets/css/style.css');

?>

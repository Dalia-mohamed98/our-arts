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


if (in_array('woocommerce/woocommerce.php', get_option('active_plugins'))) {


    // $integrationApi = 'http://41.33.122.61:58639';       //testing server
    // $integrationApi = 'http://41.33.122.61:8888/MylerzIntegrationStaging';       //testing server
    $integrationApi = 'https://integration.mylerz.net';       //live server

    $cities = [
        "Select Neighborhood",
        "15th of May",
        "6th of Oct",
        "Al Abageyah",
        "Abbaseya",
        "Abdeen",
        "Agouza",
        "Alexandria",
        "Al Salam",
        "Al Amiriyyah",
        "Ain Shams",
        "Al Ayat",
        "Bab El-Shaeria",
        "Badr City",
        "Basateen",
        "Al Badrashin",
        "Beheira",
        "Boulak",
        "Boulak Eldakrour",
        "Al Baragel",
        "Nazlet Al Batran",
        "CFC",
        "Dakahlia",
        "Damietta",
        "Dokki",
        "El Shorouk",
        "El Azbakia",
        "ELdarb Elahmar",
        "El Gamalia",
        "El Marg",
        "El Mosky",
        "Elsayeda Zeinab",
        "Eltebeen",
        "El Waily",
        "Elsayeda Aisha",
        "Rod El Farag",
        "Future City",
        "Ghamra",
        "Gharbia",
        "El Giza",
        "Hadayek Al Ahram",
        "Hadayek El Qobah",
        "Haram",
        "El Hadba EL wosta",
        "Heliopolis",
        "Helwan",
        "El Hawamdeyya",
        "Imbaba",
        "Ismailia",
        "Kafr Hakim",
        "Awal Shubra Al Kheimah",
        "Shubra El Kheima 2",
        "Al Khusus",
        "Ard El Lewa",
        "Maadi",
        "Madinaty",
        "Manial",
        "Shubra Ment",
        "Masr El Qadeema",
        "Saqiyet Mekki",
        "Al Manawat",
        "Mohandseen",
        "Monufia",
        "Al Moatamadeyah",
        "Al Munib",
        "Nahia",
        "Kafr Nassar",
        "Nasr City",
        "New Cairo",
        "New Heliopolis City",
        "Izbat an Nakhl",
        "Abu an Numros",
        "North Coast",
        "Nozha",
        "El Obour City",
        "Omraneya",
        "Ossim",
        "Port Said",
        "Qasr elneil",
        "Qalyubia",
        "10th of Ramadan",
        "Abou Rawash",
        "El Saf",
        "Saft El Laban",
        "Sheikh Zayed",
        "Kafr El Sheikh",
        "Shoubra",
        "Al Sharabiya",
        "Sharqia",
        "Nazlet El Semman",
        "Suez",
        "El Talbia",
        "Kafr Tuhurmis",
        "Tura",
        "Warraq",
        "El Zaher",
        "Zamalek",
        "Zeitoun",
        "El Zawya El Hamra"
    ];

    $zones = [
        "",
        "15th of May",
        "6th of Oct",
        "ABAG",
        "ABAS",
        "Abdeen",
        "Agouza",
        "Alexandria",
        "Al-S",
        "AMRY",
        "AS",
        "AYAT",
        "Bab El-Shaeria",
        "Badr City",
        "Basateen",
        "BDRA",
        "BEHR",
        "Boulak",
        "Boulak Eldakrour",
        "BRAG",
        "BTRN",
        "CFC",
        "DAKH",
        "DAMT",
        "Dokki",
        "El Shorouk",
        "El-Azbakia",
        "ELdarb Elahmar",
        "El-Gamalia",
        "El-M",
        "El-Mosky",
        "Elsayeda Zeinab",
        "Eltebeen",
        "El-Waily",
        "ESHA",
        "FRAG",
        "FUTR",
        "GHMR",
        "GHRB",
        "GIZA",
        "Hadayek Al Ahram",
        "Hadayek El Qobah",
        "Haram",
        "HDBA",
        "HEl",
        "Helwan",
        "HWMD",
        "Imbaba",
        "ISML",
        "KFRH",
        "KHM1",
        "KHM2",
        "KHSU",
        "LWAA",
        "Maadi",
        "Madinaty",
        "Manial",
        "MANT",
        "Masr El Qadeema",
        "MEKI",
        "MNWT",
        "Mohandseen",
        "MONF",
        "MOTM",
        "MUNB",
        "Nahia",
        "NASR",
        "Nasr City",
        "New Cairo",
        "NHEL",
        "NKHL",
        "NMRS",
        "NorthCoast",
        "Nozha",
        "OBOR",
        "Omraneya",
        "OSIM",
        "PORS",
        "Qasr elneil",
        "QLYB",
        "RMDN",
        "RWSH",
        "SAF",
        "Saft El Laban",
        "Sheikh Zayed",
        "SHKH",
        "Shoubra",
        "SHRB",
        "SHRK",
        "SMAN",
        "SUEZ",
        "TLBA",
        "TRMS",
        "TURA",
        "Warraq",
        "ZAHR",
        "Zamalek",
        "Zeitoun",
        "ZWYA"
    ];

    #region adding fulfilled satatus

    add_action('init', 'register_order_fulfilled_status');

    function register_order_fulfilled_status()
    {
        register_post_status('wc-fulfilled', array(
            'label'                     => _x('Fulfilled', 'Order status', 'woocommerce'),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('Fulfilled <span class="count">(%s)</span>', 'Fulfilled<span class="count">(%s)</span>', 'woocommerce')
        ));
    }

    add_filter('wc_order_statuses', 'fulfilled_order_status');

    // Register in wc_order_statuses.
    function fulfilled_order_status($order_statuses)
    {
        $order_statuses['wc-fulfilled'] = _x('Fulfilled', 'Order status', 'woocommerce');

        return $order_statuses;
    }

    #endregion

    #region adding select neighborhood to backend billing address
    add_filter('woocommerce_admin_billing_fields', 'zoneDropdownBackend');
    add_filter('woocommerce_admin_shipping_fields', 'zoneDropdownBackend');



    function zoneDropdownBackend($fields)
    {
        global $cities;

        global $zones;

        $fields['neighborhood']   = array(
            'label'        => 'المنطقة أو الحي',
            'required'     => true,
            'show'         => true,
            'class'        => 'custom-class-backend',

            // 'priority'     => 20,
            'placeholder'  => 'أختار المنطقة أو الحي',
            'type' => 'select',
            'options' => array_combine($zones, $cities)
        );
        echo '<script type="text/javascript">jQuery(document).ready(function( $ ) {$(".custom-class-backend").select2();});</script>';
        return $fields;
    }

    #endregion

    #region adding select neighborhood to FrontEnd billing address


    add_filter('woocommerce_checkout_fields', 'zoneDropdownFrontEnd', PHP_INT_MAX);



    function zoneDropdownFrontEnd($fields)
    {
        global $cities;

        global $zones;

        $fields['billing']['billing_neighborhood']   = array(
            'label'        => 'المنطقة أو الحي',
            'required'     => true,
            'show'         => true,
            'class'        => array('form-row-wide'),
            'placeholder'  => 'أختار المنطقة أو الحي',
            'type' => 'select',
            'options' => array_combine($zones, $cities)
        );
        $fields['shipping']['shipping_neighborhood']   = array(
            'label'        => 'المنطقة أو الحي',
            'required'     => true,
            'show'         => true,
            'class'        => array('form-row-wide'),
            'placeholder'  => 'أختار المنطقة أو الحي',
            'type' => 'select',
            'options' => array_combine($zones, $cities)
        );
        return $fields;
    }


    #endregion

    #region adding neighborhood header
    function add_neighborhood_column_header($columns)
    {

        $new_columns = array();

        foreach ($columns as $column_name => $column_info) {

            $new_columns[$column_name] = $column_info;

            if ('order_date' === $column_name) {
                $new_columns['neighborhood'] = __('Neighborhood', 'my-textdomain');
            }
        }

        return $new_columns;
    }
    add_filter('manage_edit-shop_order_columns', 'add_neighborhood_column_header', 20);

    #endregion

    #region adding neighborhood content

    function add_neighborhood_column_content($column)
    {
        global $post;
        $order = wc_get_order($post->ID);
        $orderData = $order->get_data();

        if ('neighborhood' === $column) {

            echo get_post_meta($post->ID, '_shipping_neighborhood', true);
        }
    }
    add_action('manage_shop_order_posts_custom_column', 'add_neighborhood_column_content');

    #endregion

    #region adding select warehouse on item table

    add_action('woocommerce_admin_order_item_headers', 'pd_admin_order_items_headers');
    function pd_admin_order_items_headers($order)
    {
		$column_name = 'Warehouse';
		echo '<th>' . $column_name . '</th>';
		
    }


    add_action('woocommerce_admin_order_item_values', 'my_woocommerce_admin_order_item_values', 10, 3);
    function my_woocommerce_admin_order_item_values($_product, $item, $item_id)
    {
		
		global $integrationApi;

		$url = $integrationApi . '/api/orders/GetWarehouses';

		$token = get_option('access_token');

		$warehouses = getWarehouses($url, $token);

		$selectedWarehouse = get_post_meta($item_id, 'warehouse', true);

		if ($_product != NULL) {

			echo '<td> <select name="warehouse' . $item_id . '">';
			echo '<option disabled selected value="">Select Warehouse</option>';
			foreach ($warehouses["Warehouses"] as $warehouse) {
				if ($warehouse == $selectedWarehouse) {
					echo '<option selected>' . $warehouse . '</option>';
				} else {
					echo '<option>' . $warehouse . '</option>';
				}
			}
			echo ' </select> </td>';
		} else {
			echo '<td></td>';
		}
		
    }

    add_action('woocommerce_order_item_add_action_buttons', 'action_woocommerce_order_item_add_action_buttons', 10, 1);
    // define the woocommerce_order_item_add_action_buttons callback
    function action_woocommerce_order_item_add_action_buttons($order)
    {
        echo '<button type="button" onclick="document.post.submit();" class="button generate-items">' . __('Update', 'hungred') . '</button>';
        // indicate its taopix order generator button
        echo '<input type="hidden" value="1" name="renew_order" />';
    };

    add_action('save_post', 'renew_save_again', 10, 3);
    function renew_save_again($post_id, $post, $update)
    {
        $slug = 'shop_order';
        if (is_admin()) {
            if ($slug != $post->post_type) {
                return;
            }
            if (isset($_POST['renew_order']) && $_POST['renew_order']) {
                // do your stuff here after you hit submit
                // echo '<pre>';
                // echo var_dump($_POST);
                // echo '</pre>';
                // die();

                foreach ($_POST as $key => $value) {
                    if (strpos($key, 'warehouse') !== false) {
                        $id = explode("warehouse", $key)[1];
                        echo '<pre>';
                        echo var_dump($value);
                        echo '</pre>';

                        update_post_meta($id, 'warehouse', $value);
                    }
                }
                // die();
            }
        }
    }
    #endregion

    #region Adding Mylerz Setting in Shipping Page
    function mylerz_shipping_method_init()
    {
        include_once('includes/shipping/mylerz-shipping-method.php');
    }
    add_action('woocommerce_shipping_init', 'mylerz_shipping_method_init');



    function add_mylerz_shipping_method($methods)
    {
        $methods['mylerz'] = 'Mylerz_Shipping_Method';
        return $methods;
    }
    add_filter('woocommerce_shipping_methods', 'add_mylerz_shipping_method');

    #endregion

    #region Adding Bulk Fulfillment Button

    add_action('admin_footer', 'mylerz_bulk_admin_footer');

    function mylerz_bulk_admin_footer()
    {
        global $post_type;
        if ($post_type == 'shop_order' && isset($_GET['post_type'])) {
            include_once('templates/adminhtml/bulk.php');
            display_bulkFulfillment_button();
        }
    }



    #endregion

    #region Adding Bulk Print AWB Button

    add_action('admin_footer', 'bulk_print_awb');

    function bulk_print_awb()
    {
        // update_option('access_token', " ");
        global $post_type;
        if ($post_type == 'shop_order' && isset($_GET['post_type'])) {
            include_once('templates/adminhtml/bulk_awb.php');
            display_bulkPrintAWB_button();
        }
    }

    #endregion

    #region load custom select script
    function load_drop_script()
    {
        wp_register_script(
            'sel',
            plugin_dir_url(__FILE__) . 'assets/js/sel.js',
            array('jquery'),
            '1.0.0',
            true
        );
        wp_enqueue_script('sel');
    }


    // add_action('wp_enqueue_scripts', 'load_drop_script', PHP_INT_MAX);


    #endregion

    #region pdf.js

    function load_pdfjs_script()
    {
        wp_register_script(
            'pdfjs',
            plugin_dir_url(__FILE__) . 'assets/js/pdf.js',
            array('jquery'),
            '1.0.0',
            true
        );
        wp_enqueue_script('pdfjs');
    }


    add_action('admin_enqueue_scripts', 'load_pdfjs_script');

    #endregion

    #region awb.js

    function load_awb_script()
    {
        wp_register_script(
            'awbjs',
            plugin_dir_url(__FILE__) . 'assets/js/awb.js',
            array('jquery'),
            '1.0.0',
            true
        );
        wp_enqueue_script('awbjs');
    }


    add_action('admin_enqueue_scripts', 'load_awb_script');

    #endregion

    #region print.js

    function load_printjs_script()
    {
        wp_register_script(
            'printjs',
            plugin_dir_url(__FILE__) . 'assets/js/print.min.js',
            array('jquery'),
            '1.0.0',
            true
        );
        wp_enqueue_script('printjs');
    }


    add_action('admin_enqueue_scripts', 'load_printjs_script');

    #endregion

    #region loading style.css

    function load_css_file()
    {
        wp_register_style('custom_mylerz_css', plugin_dir_url(__FILE__) . 'assets/css/style.css');
        wp_enqueue_style('custom_mylerz_css');
    }

    add_action('admin_enqueue_scripts', 'load_css_file');

    #endregion

    #region adding ajax bulk fulfill handler

    add_action('wp_ajax_bulkFulfillOrdersById', 'bulkFulfillOrdersById');
    add_action('wp_ajax_nopriv_bulkFulfillOrdersById', 'bulkFulfillOrdersById');


    function bulkFulfillOrdersById()
    {
        global $integrationApi; //= 'http://41.33.122.61:58639';
        // should read from cookies or localstorage the token 
        $token = get_option('access_token');

        $postData = $_POST;
        $ordersIds = $postData['ordersIds'];
        $bulkwarehouse = $postData['warehouse'];

        $orders = getOrders($ordersIds);

        try {
            $lineItems = flatten(getLineItemsOfOrders($orders));
        } catch (Throwable $th) {
            exit(json_encode(array(
                'Status' => 'Failed',
                'Message' => "Error Flattening Line Items",
                'Error' => $th->getMessage(),
            )));
        }
        try {

            stampWarehouseMetatoItems($lineItems, $bulkwarehouse);
        } catch (Throwable $th) {
            exit(json_encode(array(
                'Status' => 'Failed',
                'Message' => "Error Stamping Line Items",
                'Error' => $th->getMessage(),
                'bulkwarehouse' => $bulkwarehouse
            )));
        }

        try {
            $mylerzOrders = array_map('constructMylerzOrder', $orders, array_keys($orders));
        } catch (Throwable $th) {
            exit(json_encode(array(
                'Status' => 'Failed',
                'Message' => "Error Constructing PickupOrder",
                'Error' => $th->getMessage(),
                'pickupOrder' => $mylerzOrders
            )));
        }

        $pickupOrdersGroupedByWarehouse = array_merge_recursive(...$mylerzOrders);

        $packagesResponse = array_map(function ($pickupOrders) use ($integrationApi, $token, $orders) {

            if (gettype($pickupOrders) == "array") {
                $pickupOrderDecoded = array_map(function ($order) {
                    return json_decode($order);
                }, $pickupOrders);
            } else {
                $pickupOrderDecoded = array(json_decode($pickupOrders));
            }

            $response = addPickupOrder($integrationApi . '/api/orders/addorders', $pickupOrderDecoded, $token);

            if ($response["Status"] === "Failed") {
                exit(json_encode(array(
                    'Status' => 'Failed',
                    'Message' => 'Error Adding Pickup Order',
                    'Error' => $response["Error"],
                    'MylerzOrder' => $pickupOrderDecoded
                )));
            }

            return $response["Packages"];
        }, $pickupOrdersGroupedByWarehouse);

        $statusChangedResult = changeStatusToFulfilled($orders);
        if ($statusChangedResult["Status"] === "Failed") {

            exit(json_encode(array(
                'Status' => 'Failed',
                'Message' => 'Error Changing Status To Fulfilled',
                'Error' => $statusChangedResult["Error"],
                'MylerzOrder' => $mylerzOrders
            )));
        }

        $packages = array_merge(...array_values($packagesResponse));


        $packagesGroupedByReference = groupBy($packages, "Reference");

        $barcodes = array_map(function ($packagesArray) use ($ordersIds) {
            $barcode = "BarCode";
            $barcodes = array_map(function ($package) use ($barcode) {
                return $package->$barcode;
            }, $packagesArray);


            return $barcodes;
        }, $packagesGroupedByReference);

        rsort($ordersIds);

        array_map(function ($barcodeList, $orderId) {
            if (metadata_exists("post", $orderId, "barcode")) {
                delete_post_meta($orderId, "barcode");
            }
            array_map(function ($barcode) use ($orderId) {
                add_post_meta($orderId, 'barcode', $barcode);
            }, array_values($barcodeList));
        }, $barcodes, $ordersIds);


        $barcodes = array_merge(...array_values($barcodes));

        $response = getAWB($integrationApi . '/api/packages/GetAWB', $barcodes, $token);

        if ($response["Status"] === "Failed")
            exit(json_encode(array(
                'Status' => 'Failed',
                'Message'=> 'Error Getting AWB',
                'Error' => $response["Error"],
                'MylerzOrder' => $mylerzOrders
            )));


        exit(json_encode($response));
    }


    #endregion

    #region adding ajax bulk print handler

    add_action('wp_ajax_bulkPrintAWB', 'bulkPrintAWB');
    add_action('wp_ajax_nopriv_bulkPrintAWB', 'bulkPrintAWB');

    function bulkPrintAWB()
    {

        global $integrationApi;

        $token = get_option('access_token');


        $postData = $_POST;
        $ordersIds = $postData['ordersIds'];

        // $orders = getOrders($ordersIds);

        $barcodes = array_map('getBarcodesMeta', $ordersIds);

        $barcodes = array_merge(...$barcodes);

        $response = getAWB($integrationApi . '/api/packages/GetAWB', $barcodes, $token);

        if ($response["Status"] === "Failed")
            exit(json_encode(array(
                'Status' => 'Failed',
                'Error' => $response["Error"]
            )));


        exit(json_encode($response));
    }

    #endregion

    #region adding ajax validate token

    add_action('wp_ajax_validateAndGenerateNewToken', 'validateAndGenerateNewToken');
    add_action('wp_ajax_nopriv_validateAndGenerateNewToken', 'validateAndGenerateNewToken');

    function validateAndGenerateNewToken()
    {

        global $integrationApi;

        try {
            //code...
            list($validationResult, $warehouses, $error) = validateToken($integrationApi . '/api/orders/GetWarehouses');
        } catch (Throwable $th) {
            //throw $th;
            exit(json_encode(array(
                'Status' => 'Failed',
                'Message' => "Error validating token",
                'Error' => $th->getMessage(),
            )));
        }

        if ($validationResult == false) {
            try {
                //code...
                $response = requestNewToken($integrationApi . '/Token');
            } catch (Throwable $th) {
                //throw $th;
                exit(json_encode(array(
                    'Status' => 'Failed',
                    'Message' => "Error requesting new token",
                    'Validate Result' => $error ,
                    'Error' => $th->getMessage(),
                )));
            }

            if ($response["Status"] == "Success") {

                update_option('access_token', $response["Token"]);
                $warehousesResponse = getWarehouses($integrationApi .  '/api/orders/GetWarehouses', $response["Token"]);

                if($warehousesResponse["Status"] == "Success"){

                    exit(json_encode(array(
                        'Status' => 'Success',
                        'Message' => "New Token Generated Successfully",
                        'Validate Result' => $error ,
                        'Warehouses' => $warehousesResponse["Warehouses"]
                    )));
                }else{
                    exit(json_encode(array(
                        'Status' => 'Failed',
                        'Message' => "Error Validating New Token",
                        'Validate Result' => $error ,
                        'Validate New Token Result' => $warehousesResponse["Error"]
                    )));
                }
            } else {
                exit(json_encode(array(
                    'Status' => 'Failed',
                    'Message' => "Error Requesting New Token",
                    'Validate Result' => $error ,
                    'Error' => $response["Error"]
                )));
            }
        } else {
            exit(json_encode(array(
                'Status' => 'Success',
                'Message' => "Token Is Valid",
                'Warehouses' => $warehouses
            )));
        }
    }

    #endregion

    #region adding ajax checkItemWarehouse

    add_action('wp_ajax_checkItemWarehouses', 'checkItemWarehouses');
    add_action('wp_ajax_nopriv_checkItemWarehouses', 'checkItemWarehouses');

    function checkItemWarehouses()
    {

        // global $integrationApi;

        $postData = $_POST;
        $ordersIds = $postData['ordersIds'];

        $orders = getOrders($ordersIds);

        $lineItems = flatten(getLineItemsOfOrders($orders));

        // update_option('access_token', $response->access_token);
        // $warehousesResponse = getWarehouses($integrationApi .  '/api/orders/GetWarehouses', $response->access_token);

        $result = array_every($lineItems, function ($item) {
            $warehouse = get_post_meta($item->get_id(), "warehouse", true);

            if (metadata_exists("post", $item->get_id(), "warehouse") && $warehouse !== "") {
                return true;
            }
            return false;
        });

        if ($result == true) {

            exit(json_encode(array(
                'ItemWarehouse' => true
            )));
        } else {

            exit(json_encode(array(
                'ItemWarehouse' => false
            )));
        }
    }

    #endregion

    #region Helper Functions

    function getOrders($ordersIds)
    {
        return array_map(
            function ($orderId) {
                return wc_get_order($orderId);
            },
            $ordersIds
        );;
    }

    function getLineItemsOfOrders($orders)
    {
        return array_map(function ($order) {
            return $order->get_items();
        }, $orders);
    }

    function getAddressList($orders)
    {
        return array_map(function ($order) {
            $orderData = $order->get_data();
            return $orderData['shipping']['address_1'] . ', ' . $orderData['shipping']['address_2'] . ', ' . $orderData['shipping']['city'];
        }, $orders);
    }

    function getZones($url, $addressList, $token)
    {


        $response = wp_remote_post($url, array(
            'method'      => 'POST',
            'blocking'    => true,
            'headers'     =>  array(
                'content-type' => 'application/json',
                'Authorization' => 'bearer ' . $token
            ),
            'body'        =>  json_encode($addressList)
        ));

        $result = json_decode($response["body"]);

        if ($result->Message === "Authorization has been denied for this request.") {

            return array(
                'Status' => 'Failed',
                'Error' => $result->Message,
            );
        } else {

            if ($result->IsErrorState === TRUE) {
                return array(
                    'Status' => 'Failed',
                    'Error' => $result->ErrorDescription,
                );
            } else {
                return array(
                    'Status' => 'Success',
                    'Zones' => $result->Value,
                );
            }
        }
    }

    function getWarehouses($url, $token)
    {
        $response = wp_remote_post($url, array(
            'method'      => 'GET',
            'blocking'    => true,
            'headers'     =>  array(
                'content-type' => 'application/json',
                'Authorization' => 'bearer ' . $token
            ),
        ));
        if (is_wp_error($response)) {

            $error_message = $response->get_error_message();
            return array(
                'Status' => 'Failed',
                'Error' => $error_message,
            );
        } else {

            $result = json_decode($response["body"]);

            if ($result->Message === "Authorization has been denied for this request.") {

                return array(
                    'Status' => 'Failed',
                    'Error' => $result->Message,
                );
            } else {

                if ($result->IsErrorState === TRUE) {
                    return array(
                        'Status' => 'Failed',
                        'Error' => $result->ErrorDescription,
                    );
                } else {
                    return array(
                        'Status' => 'Success',
                        'Warehouses' => array_map(function ($warehouse) {
                            return $warehouse->Name;
                        }, $result->Value),
                    );
                }
            }
        }
    }

    function constructMylerzOrder($order, $index)
    {
        $orderItems = $order->get_items();
        $total_items_prices = 0;

        foreach ($orderItems as $item) {
            $itemsGroupedByWarehouse[get_post_meta($item->get_id(), 'warehouse', true)][] = $item;
            $total_items_prices += $item->get_total();
        }

        $numberOfWarehouses = count($itemsGroupedByWarehouse);

        $totalFees = $order->get_total() - $total_items_prices;

        $feesPerWarehouse = $totalFees / $numberOfWarehouses;



        $mylerzPackagesPerOrder = array_map(function ($itemsPerwarehouse) use ($order, $index, $feesPerWarehouse) {
            return json_encode(array(
                'WarehouseName' => get_post_meta($itemsPerwarehouse[0]->get_id(), 'warehouse', true),
                'PickupDueDate' => $order->get_date_created()->date('Y-m-d H:i:s'),
                'Package_Serial' => $index,
                'Reference' => '#' . $order->get_id(),
                'Description' => join("rn", array_map(function ($item) {
                    return 'Title: ' . $item->get_name() . '( ' . $item->get_product()->get_sku() . ' ), Quantity: ' . $item->get_quantity();
                }, $itemsPerwarehouse)),
                'Service_Type' => 'DTD',
                'Service' => 'ND',
                'Service_Category' => 'DELIVERY',
                'Payment_Type' => ($order->get_payment_method() === 'cod') ? 'COD' : 'PP',
                'COD_Value' => ($order->get_payment_method() === 'cod') ? round(array_sum(array_map(function ($item) {
                    return  $item->get_total();
                }, $itemsPerwarehouse)) + $feesPerWarehouse, 2) : 0,
                'Pieces' => [array(
                    'PieceNo' => 1,
                    'Special_Notes' => ''   //note from customer or shop owner ?
                )],
                'Customer_Name' => $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name(),
                'Mobile_No' => $order->get_billing_phone(),
                'Street' => $order->get_shipping_address_2() . ', ' . $order->get_shipping_address_1() . ', ' . $order->get_shipping_city(),
                'Country' => 'Egypt',
                'Neighborhood' => get_post_meta($order->get_id(), '_shipping_neighborhood', true),
                'Address_Category' => 'H',
                'Currency' => $order->get_currency(),
            ));
        }, $itemsGroupedByWarehouse);

        return $mylerzPackagesPerOrder;

    }

    function addPickupOrder($url, $mylerzOrder, $token)
    {
        $response = wp_remote_post($url, array(
            'method'      => 'POST',
            'blocking'    => true,
            'headers'     =>  array(
                'content-type' => 'application/json',
                'Authorization' => 'bearer ' . $token
            ),
            'body'        =>  json_encode($mylerzOrder)
        ));

        if (is_wp_error($response)) {

            $error_message = $response->get_error_message();
            return array(
                'Status' => 'Failed',
                'Error' => $error_message,
                'Description' => ""
            );
        } else {

            $result = json_decode($response["body"]);

            if ($result->Message) {
                return array(
                    'Status' => 'Failed',
                    'Error' => $result->Message,
                    'Description' => ""

                );
            }

            if ($result->IsErrorState === TRUE) {
                return array(
                    'Status' => 'Failed',
                    'Error' => $result,
                );
            } else {
                return array(
                    'Status' => 'Success',
                    'Packages' => $result->Value->Packages
                );
            }
        }
    }

    function getAWB($url, $barcodeList, $token)
    {

        $awbList =  array_map(function ($barcode) use ($token, $url) {

            $response = wp_remote_post($url, array(
                'method'      => 'POST',
                'blocking'    => true,
                'headers'     =>  array(
                    'content-type' => 'application/json',
                    'Authorization' => 'bearer ' . $token
                ),
                'body'        =>  json_encode(array(
                    'Barcode' => $barcode
                ))
            ));

            $result =  json_decode($response["body"]);

            if ($result->IsErrorState === TRUE) {
                return NULL;
            } else {
                return $result->Value;
            }
        }, $barcodeList);

        if (in_array(NULL, $awbList)) {
            return array(
                'Status' => 'Failed',
                'Error' => "Error Retrieving AWB",
            );
        } else {
            return array(
                'Status' => 'Success',
                'AWBList' => $awbList,
            );
        }
    }

    function changeStatusToFulfilled($orders)
    {
        $resultArray = array_map(function ($order) {
            return $order->update_status('fulfilled');
        }, $orders);

        return in_array(FALSE, $resultArray) ? array(
            'Status' => 'Failed',
            'Error' => 'Error Changing Status To Fulfilled',
        ) : array(
            'Status' => 'Success',
            'Message' => 'Status Changed To Fulfilled',
        );
    }

    function getBarcodesMeta($orderId)
    {
        return get_post_meta($orderId, 'barcode');
    }

    function setBarcodesMeta($orderId, $barcodes)
    {
        add_post_meta($orderId, 'barcode', $barcodes);
    }

    function requestNewToken($url)
    {

        include_once('includes/shipping/mylerz-shipping-method.php');

        $settings = new Mylerz_Shipping_Method();

        $userName = $settings->settings['user_name'];
        $password = $settings->settings['password'];

        $response = wp_remote_post($url, array(
            'method'      => 'POST',
            'blocking'    => true,
            'headers'     =>  array(
                'content-type' => 'application/x-www-form-urlencoded',
            ),
            'body'        =>  array(
                'username' => $userName,
                'password' => $password,
                'grant_type' => 'password'
            )
        ));

        if (is_wp_error($response)) {

            $error_message = $response->get_error_message();
            return array(
                'Status' => 'Failed',
                'Error' => $error_message,
            );
        } else {
            $result = json_decode($response["body"]);
            if ($result->access_token) {
                return array(
                    'Status' => 'Success',
                    'Token' => $result->access_token,
                );
            } else {

                return array(
                    'Status' => 'Failed',
                    'Error' => "Wrong Mylerz Credentials",
                );
            }
        }
    }

    function validateToken($url)
    {

        $token = get_option('access_token');
        $response = getWarehouses($url, $token);

        if ($response["Status"] == "Failed") {
            return array(false, [], $response["Error"]);
        } else if ($response["Status"] == "Success") {
            return array(true, $response["Warehouses"], "");
            // return array(false,[]);
        }
    }

    function flatten(array $array)
    {
        $return = array();
        array_walk_recursive($array, function ($a) use (&$return) {
            $return[] = $a;
        });
        return $return;
    }
    function stampWarehouseMetatoItems($orderItems, $bulkwarehouse)
    {
        array_map(function ($item) use ($bulkwarehouse) {
            // echo '<pre>';
            // echo var_dump($item->get_id());
            // echo '</pre>';
            $item_id = $item->get_id();
            if (get_post_meta($item_id, 'warehouse', true) == "") {
                add_post_meta($item_id, 'warehouse', $bulkwarehouse);
            }
        }, $orderItems);

        // die();
    }

    function groupBy(array $array, string $key)
    {
        foreach ($array as $item) {
            $arrayGroup[$item->$key][] = $item;
        }

        return $arrayGroup;
    }

    function array_every($array, $callback)
    {

        return  !in_array(false,  array_map($callback, $array));
    }


    #endregion

}
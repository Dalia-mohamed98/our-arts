<?php

    remove_action('woocommerce_admin_order_item_headers','pd_admin_order_items_headers');
    add_action('woocommerce_admin_order_item_headers','only_admin_warehouse_header');
    
    remove_action('woocommerce_admin_order_item_values','my_woocommerce_admin_order_item_values', 10, 3);
    add_action('woocommerce_admin_order_item_values','only_admin_warehouse_data', 10, 3);
    
    
    function only_admin_warehouse_header($order)
    {
		if (is_admin()) {
			$column_name = 'Warehouse';
			echo '<th>' . $column_name . '</th>';
		}
    }
    
    function only_admin_warehouse_data($_product, $item, $item_id)
    {
		if (is_admin()) {
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
    }

    
    

?>
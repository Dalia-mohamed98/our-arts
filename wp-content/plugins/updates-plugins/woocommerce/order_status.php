<?php

 #region adding picked up satatus

    add_action('init', 'register_order_pickedup_status');

    function register_order_pickedup_status()
    {
        register_post_status('wc-pickedup', array(
            'label'                     => _x('Pickedup', 'Order status', 'woocommerce'),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('Pickedup <span class="count">(%s)</span>', 'Pickedup<span class="count">(%s)</span>', 'woocommerce')
        ));
    }

    add_filter('wc_order_statuses', 'pickedup_order_status');

    // Register in wc_order_statuses.
    function pickedup_order_status($order_statuses)
    {
        $order_statuses['wc-pickedup'] = _x('Pickedup', 'Order status', 'woocommerce');

        return $order_statuses;
    }

#endregion
    
    
 #region adding delivered satatus

    add_action('init', 'register_order_delivered_status');

    function register_order_delivered_status()
    {
        register_post_status('wc-delivered', array(
            'label'                     => _x('Delivered', 'Order status', 'woocommerce'),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('Delivered <span class="count">(%s)</span>', 'Delivered<span class="count">(%s)</span>', 'woocommerce')
        ));
    }

    add_filter('wc_order_statuses', 'delivered_order_status');

    // Register in wc_order_statuses.
    function delivered_order_status($order_statuses)
    {
        $order_statuses['wc-delivered'] = _x('Delivered', 'Order status', 'woocommerce');

        return $order_statuses;
    }

#endregion

?>
<?php
    
    add_action( 'wp_ajax_dokan-mark-order-cancelled', 'cancel_order' );
    
    /**
     * Mark a order as cancelled
     *
     * Fires from frontend seller dashboard
     */
    function cancel_order() {
        if ( ! is_admin() ) {
            die();
        }

        if ( ! current_user_can( 'dokandar' ) && 'on' != dokan_get_option( 'order_status_change', 'dokan_selling', 'on' ) ) {
            wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'dokan-lite' ) );
        }

        if ( ! check_admin_referer( 'dokan-mark-order-cancelled' ) ) {
            wp_die( esc_html__( 'You have taken too long. Please go back and retry.', 'dokan-lite' ) );
        }

        $order_id = isset( $_GET['order_id'] ) && $_GET['order_id'] ? (int) $_GET['order_id'] : 0;

        if ( ! $order_id ) {
            die();
        }

        if ( ! dokan_is_seller_has_order( dokan_get_current_user_id(), $order_id ) ) {
            wp_die( esc_html__( 'You do not have permission to change this order', 'dokan-lite' ) );
        }

        $order = new WC_Order( $order_id );
        $order->update_status( 'cancelled' );

        wp_safe_redirect( wp_get_referer() );
        exit;
    }

    
?>
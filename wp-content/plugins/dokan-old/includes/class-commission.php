<?php

defined( 'ABSPATH' ) || exit;

use Dokan\Traits\Singleton;

/**
 * Dokan Commission Class
 *
 * @since DOKAN_LITE_SINCE
 */
class Dokan_Commission {
    use Singleton;

    /**
     * Order id holder
     *
     * @since DOKAN_LITE_SINCE
     *
     * @var integer
     */
    public $order_id = 0;

    /**
     * Order quantity holder
     *
     * @since DOKAN_LITE_SINCE
     *
     * @var integer
     */
    public $quantity = 0;

    /**
     * Boot method
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @return void
     */
    private function boot() {
        $this->hooks();
    }

    /**
     * Init hooks
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @return void
     */
    private function hooks() {
        add_filter( 'woocommerce_order_item_get_formatted_meta_data', [ $this, 'hide_extra_data' ] );
    }

    /**
     * Hide extra meta data
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @param  array
     *
     * @return array
     */
    public function hide_extra_data( $formated_meta ) {
        $meta_to_hide   = [ '_dokan_commission_rate', '_dokan_commission_type', '_dokan_additional_fee' ];
        $meta_to_return = [];

        foreach ( $formated_meta as $key => $meta ) {
            if ( ! in_array( $meta->key, $meta_to_hide ) ) {
                array_push( $meta_to_return, $meta );
            }
        }

        return $meta_to_return;
    }

    /**
     * Set order id
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @param  int $id
     *
     * @return void
     */
    public function set_order_id( $id ) {
        $this->order_id = $id;
    }

    /**
     * Get order id
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @return int
     */
    public function get_order_id() {
        return $this->order_id;
    }

    /**
     * Set order quantity
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @param  int $number
     *
     * @return void
     */
    public function set_order_qunatity( $number ) {
        $this->quantity = $number;
    }

    /**
     * Get order quantity
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @return int
     */
    public function get_order_qunatity() {
        return $this->quantity;
    }

    /**
     * Get earning by product
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @param  int|WC_Product $product
     * @param  string $context[admin|seller]
     * @param  float $price
     *
     * @return float
     */
    public function get_earning_by_product( $product, $context = 'seller', $price = null ) {
        if ( ! $product instanceof WC_Product ) {
            $product = wc_get_product( $product );
        }

        if ( ! $product ) {
            return new WP_Error( __( 'Product not found', 'dokan-lite' ), 404 );
        }

        $product_price = is_null( $price ) ? $product->get_price() : $price;
        $vendor        = dokan_get_vendor_by_product( $product );

        $earning = $this->calculate_commission( $product->get_id(), $product_price, $vendor->get_id() );
        $earning = 'admin' === $context ? $product_price - $earning : $earning;

        return apply_filters( 'dokan_get_earning_by_product', $earning, $product, $context );
    }

    /**
     * Get earning by order
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @param  int|WC_Order $order
     * @param  string $context
     *
     * @return float|null on failure
     */
    public function get_earning_by_order( $order, $context = 'seller' ) {
        if ( ! $order instanceof WC_Order ) {
            $order = wc_get_order( $order );
        }

        if ( ! $order ) {
           return new WP_Error( __( 'Order not found', 'dokan-lite' ), 404 );
        }

        if ( $order->get_meta( 'has_sub_order' ) ) {
            return;
        }

        // If `_dokan_admin_fee` is found means, the commission has been calculated for this order without the `Dokan_Commission` class.
        // So we'll return the previously earned commission to keep backward compatability.
        $saved_admin_fee = get_post_meta( $order->get_id(), '_dokan_admin_fee', true );

        if ( $saved_admin_fee != '' ) {
            return apply_filters( 'dokan_order_admin_commission', $saved_admin_fee, $order );
        }

        // Set user passed `order_id` so that we can track if any commission_rate has been saved previously.
        // Specially on order table `re-generation`.
        $this->set_order_id( $order->get_id() );
        $earning = $this->get_earning_from_order_table( $order->get_id(), $context );

        if ( ! is_null( $earning ) ) {
            return $earning;
        }

        $earning = 0;

        foreach ( $order->get_items() as $item_id => $item ) {
            if ( ! $item->get_product() ) {
                continue;
            }

            // Set line item quantity so that we can use it later in the `Dokan_Commission::prepare_for_calculation()` method
            $this->set_order_qunatity( $item->get_quantity() );

            $product_id = $item->get_product()->get_id();
            $refund     = $order->get_total_refunded_for_item( $item_id );

            if ( $refund ) {
                $earning += $this->get_earning_by_product( $product_id, $context, $item->get_total() - $refund );
            } else {
                $earning += $this->get_earning_by_product( $product_id, $context, $item->get_total() );
            }
        }

        if ( $context === $this->get_shipping_fee_recipient( $order->get_id() ) ) {
            $earning += $order->get_total_shipping() - $order->get_total_shipping_refunded();
        }

        if ( $context === $this->get_tax_fee_recipient( $order->get_id() ) ) {
            $earning += $order->get_total_tax() - $order->get_total_tax_refunded();
        }

        return apply_filters_deprecated( 'dokan_order_admin_commission', array( $earning, $order, $context ), 'DOKAN_LITE_SINCE', 'dokan_get_earning_by_order' );
    }

    /**
     * Get global rate
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @return float
     */
    public function get_global_rate() {
        return $this->validate_rate( dokan_get_option( 'admin_percentage', 'dokan_selling', 0 ) );
    }

    /**
     * Get vendor wise commission rate
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @param  int $vendor_id
     *
     * @return float
     */
    public function get_vendor_wise_rate( $vendor_id ) {
        return $this->validate_rate( get_user_meta( $vendor_id, 'dokan_admin_percentage', true ) );
    }

    /**
     * Get product wise commission rate
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @param  int $product_id
     *
     * @return float
     */
    public function get_product_wise_rate( $product_id ) {
        return $this->validate_rate( get_post_meta( $this->validate_product_id( $product_id ), '_per_product_admin_commission', true ) );
    }

    /**
     * Validate product id (if it's a variable product, return it's parent id)
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @param  int $product_id
     *
     * @return int
     */
    public function validate_product_id( $product_id ) {
        $product   = wc_get_product( $product_id );
        $parent_id = $product->get_parent_id();

        return $parent_id ? $parent_id : $product_id;
    }

    /**
     * Get category wise commission rate
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @param  int $product_id
     *
     * @return float
     */
    public function get_category_wise_rate( $product_id ) {
        $terms = get_the_terms( $this->validate_product_id( $product_id ), 'product_cat' );

        if ( empty( $terms ) ) {
            return null;
        }

        $term_id = $terms[0]->term_id;
        $rate    = ! $terms ? null: get_term_meta( $term_id, 'per_category_admin_commission', true );

        return $this->validate_rate( $rate );
    }

    /**
     * Get global commission type
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @return string
     */
    public function get_global_type() {
        return dokan_get_option( 'commission_type', 'dokan_selling', 'percentage' );
    }

    /**
     * Get vendor wise commission type
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @param  int $vendor_id
     *
     * @return string
     */
    public function get_vendor_wise_type( $vendor_id ) {
        return get_user_meta( $vendor_id, 'dokan_admin_percentage_type', true );
    }

    /**
     * Get category wise commission type
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @param  int $product_id
     *
     * @return string
     */
    public function get_category_wise_type( $product_id ) {
        $terms   = get_the_terms( $this->validate_product_id( $product_id ), 'product_cat' );
        $term_id = $terms[0]->term_id;

        return ! $terms ? null : get_term_meta( $term_id, 'per_category_admin_commission_type', true );
    }

    /**
     * Get product wise commission type
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @param  int $product_id
     *
     * @return string
     */
    public function get_product_wise_type( $product_id ) {
        return get_post_meta( $this->validate_product_id( $product_id ), '_per_product_admin_commission_type', true );
    }

    /**
     * Validate commission rate
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @param  float $rate
     *
     * @return float
     */
    public function validate_rate( $rate ) {
        if ( '' === $rate || ! is_numeric( $rate ) || $rate < 0 ) {
            return null;
        }

        return (float) $rate;
    }

    /**
     * Get global earning
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @param  float $product_price
     *
     * @return float|null on failure
     */
    public function get_global_earning( $product_price ) {
        return $this->prepare_for_calculation( __FUNCTION__, null, $product_price );
    }

    /**
     * Get vendor wise earning
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @param  int $vendor_id
     * @param  float $product_price
     *
     * @return float|null on failure
     */
    public function get_vendor_wise_earning( $vendor_id, $product_price ) {
        return $this->prepare_for_calculation( __FUNCTION__, $vendor_id, $product_price );
    }

    /**
     * Get category wise earning
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @param  int $product_id
     * @param  float $product_price
     *
     * @return float|null on failure
     */
    public function get_category_wise_earning( $product_id, $product_price ) {
        return $this->prepare_for_calculation( __FUNCTION__, $product_id, $product_price );
    }

    /**
     * Get product wise earning
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @param  int $product_id
     * @param  int $product_price
     *
     * @return float
     */
    public function get_product_wise_earning( $product_id, $product_price ) {
        return $this->prepare_for_calculation( __FUNCTION__, $product_id, $product_price );
    }

    /**
     * Prepare for calculation
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @param  function $callable
     * @param  int $product_id
     * @param  float $product_price
     *
     * @return float | null on failure
     */
    public function prepare_for_calculation( $callable, $product_id = 0, $product_price = 0 ) {
        do_action( 'dokan_before_prepare_for_calculation', $callable, $product_id, $product_price, $this );

        $func_rate = str_replace( 'earning', 'rate', $callable );
        $func_type = str_replace( 'earning', 'type', $callable );
        $func_fee  = str_replace( 'earning', 'additional_fee', $callable );

        $commission_rate = null;

        // get[product,category,vendor,global]_wise_rate
        if ( is_callable( [ $this, $func_rate ] ) ) {
            $commission_rate = $this->$func_rate( $product_id );
        }

        if ( is_null( $commission_rate ) ) {
            return null;
        }

        $earning = null;

        // get[product,category,vendor,global]_wise_type
        if ( is_callable( [ $this, $func_type ] ) ) {
            $commission_type = $this->$func_type( $product_id );
        }

        // get[product,category,vendor,global]_wise_additional_fee
        if ( is_callable( [ $this, $func_fee ] ) ) {
            $additional_fee = $this->$func_fee( $product_id );
        }

        // If an order has been purchased previously, calculate the earning with the previously stated commisson rate.
        // It's important cause commission rate may get changed by admin during the order table `re-generation`.
        if ( $this->get_order_id() ) {
            $order      = wc_get_order( $this->get_order_id() );
            $line_items = $order->get_items();

            static $i = 0;
            foreach ( $line_items as $item ) {
                $items = array_keys( $line_items );

                if ( ! isset( $items[$i] ) ) {
                    continue;
                }

                $saved_commission_rate = wc_get_order_item_meta( $items[$i], '_dokan_commission_rate', true );
                $saved_commission_type = wc_get_order_item_meta( $items[$i], '_dokan_commission_type', true );
                $saved_additional_fee  = wc_get_order_item_meta( $items[$i], '_dokan_additional_fee', true );

                if ( $saved_commission_rate ) {
                    $commission_rate = $saved_commission_rate;
                } else {
                    wc_add_order_item_meta( $items[$i], '_dokan_commission_rate', $commission_rate );
                }

                if ( $saved_commission_type ) {
                    $commission_type = $saved_commission_type;
                } else {
                    wc_add_order_item_meta( $items[$i], '_dokan_commission_type', $commission_type );
                }

                if ( $saved_additional_fee ) {
                    $additional_fee = $saved_additional_fee;
                } else {
                    wc_add_order_item_meta( $items[$i], '_dokan_additional_fee', $additional_fee );
                }

                $i++;
                break;
            }

            // Reset `static` $i to 0 when the value of $i is equals to the line_items as we don't need to hold the value anymore.
            // This is required cause on order table `re-generation` the php process keeps running.
            $i = count( $line_items ) === $i ? 0 : $i;
        }

        if ( 'flat' === $commission_type ) {
            if ( $this->get_order_qunatity() ) {
                $commission_rate *= apply_filters( 'dokan_commission_multiply_by_order_quantity', $this->get_order_qunatity() );
            }

            // If `_dokan_item_total` returns value non-falsy value, it means the request is comming from the `order refund requst`.
            // As it's `flat` fee, So modify `commission rate` to the correct amount to get refunded. (commission_rate/item_total)*product_price.
            $item_total = get_post_meta( $this->get_order_id(), '_dokan_item_total', true );
            if ( $item_total ) {
                $commission_rate = ( $commission_rate / $item_total ) * $product_price;
            }

            $earning = (float) ( $product_price - $commission_rate );
        }

        if ( 'percentage' === $commission_type ) {
            $earning = ( (float) $product_price * $commission_rate ) / 100;
            $earning = (float) $product_price - $earning;

            // vendor will get 100 percent if commission rate > 100
            if ( $commission_rate > 100 ) {
                $earning = (float) $product_price;
            }
        }

        return apply_filters( 'dokan_prepare_for_calculation', $earning, $commission_rate, $commission_type, $additional_fee, $product_price, $this->order_id );
    }

    /**
     * Get product wise additional fee
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @param  int $product_id
     *
     * @return float|null on failure
     */
    public function get_product_wise_additional_fee( $product_id ) {
        return $this->validate_rate( get_post_meta( $this->validate_product_id( $product_id ), '_per_product_admin_additional_fee', true ) );
    }

    /**
     * Get global wise additional fee
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @param  int $product_id
     *
     * @return float|null on failure
     */
    public function get_global_additional_fee() {
        return $this->validate_rate( dokan_get_option( 'additional_fee', 'dokan_selling', 0 ) );
    }

    /**
     * Get vendor wise additional fee
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @param  int $vendor_id
     *
     * @return float|null on failure
     */
    public function get_vendor_wise_additional_fee( $vendor_id ) {
        return $this->validate_rate( get_user_meta( $vendor_id, 'dokan_admin_additional_fee', true ) );
    }

    /**
     * Get category wise additional fee
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @param  int $product_id
     *
     * @return float|null on failure
     */
    public function get_category_wise_additional_fee( $product_id ) {
        $terms = get_the_terms( $this->validate_product_id( $product_id ), 'product_cat' );

        if ( empty( $terms ) ) {
            return null;
        }

        $term_id = $terms[0]->term_id;
        $rate    = ! $terms ? null: get_term_meta( $term_id, 'per_category_admin_additional_fee', true );

        return $this->validate_rate( $rate );
    }

    /**
     * Get earning from order table
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @param  int $order_id
     * @param  string $context
     *
     * @return float|null on failure
     */
    public function get_earning_from_order_table( $order_id, $context = 'seller' ) {
        global $wpdb;

        $cache_key = 'dokan_get_earning_from_order_table' . $order_id . $context;
        $earning = wp_cache_get( $cache_key );

        if ( false !== $earning ) {
            return $earning;
        }

        $result = $wpdb->get_row( $wpdb->prepare(
            "SELECT `net_amount`, `order_total` FROM {$wpdb->dokan_orders} WHERE `order_id` = %d",
            $order_id
        ) );

        if ( ! $result ) {
            return null;
        }

        $earning = 'seller' === $context ? (float) $result->net_amount : (float) $result->order_total - (float) $result->net_amount;
        wp_cache_set( $cache_key, $earning );

        return $earning;
    }

    /**
     * Get shipping fee recipient
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @param  int $order_id
     *
     * @return string
     */
    public function get_shipping_fee_recipient( $order_id ) {
        $saved_shipping_recipient = get_post_meta( $order_id, 'shipping_fee_recipient', true );

        if ( $saved_shipping_recipient ) {
            $shipping_recipient = $saved_shipping_recipient;
        } else {
            $shipping_recipient = dokan_get_option( 'shipping_fee_recipient', 'dokan_general', 'seller' );
            update_post_meta( $order_id, 'shipping_fee_recipient', $shipping_recipient );
        }

        return $shipping_recipient;
    }

    /**
     * Get tax fee recipient
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @param  int $order_id
     *
     * @return string
     */
    public function get_tax_fee_recipient( $order_id ) {
        $saved_tax_recipient = get_post_meta( $order_id, 'tax_fee_recipient', true );

        if ( $saved_tax_recipient ) {
            $tax_recipient = $saved_tax_recipient;
        } else {
            $tax_recipient = dokan_get_option( 'tax_fee_recipient', 'dokan_general', 'seller' );
            update_post_meta( $order_id, 'tax_fee_recipient', $tax_recipient );
        }

        return $tax_recipient;
    }

    /**
     * Calculate commission (commission priority [1.product, 2.category, 3.vendor, 4.global] wise)
     *
     * @since  DOKAN_LITE_SINCE
     *
     * @param  int $product_id
     * @param  float $product_price
     * @param  int $vendor_id
     *
     * @return float
     */
    public function calculate_commission( $product_id, $product_price, $vendor_id = null ) {
        $product_wise_earning = $this->get_product_wise_earning( $product_id, $product_price );

        if ( ! is_null( $product_wise_earning ) ) {
            return $product_wise_earning;
        }

        $category_wise_earning = $this->get_category_wise_earning( $product_id, $product_price );

        if ( ! is_null( $category_wise_earning ) ) {
            return $category_wise_earning;
        }

        $vendor_wise_earning = $this->get_vendor_wise_earning( $vendor_id, $product_price );

        if ( ! is_null( $vendor_wise_earning ) ) {
            return $vendor_wise_earning;
        }

        $global_earning = $this->get_global_earning( $product_price );

        if ( ! is_null( $global_earning ) ) {
            return $global_earning;
        }

        return $product_price;
    }
}
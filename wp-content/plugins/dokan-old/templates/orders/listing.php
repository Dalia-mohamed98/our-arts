<?php
global $woocommerce;

$seller_id    = dokan_get_current_user_id();
$customer_id  = isset( $_GET['customer_id'] ) ? sanitize_key( $_GET['customer_id'] ) : null;
$order_status = isset( $_GET['order_status'] ) ? sanitize_key( $_GET['order_status'] ) : 'all';
$paged        = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
$limit        = 10;
$offset       = ( $paged - 1 ) * $limit;
$order_date   = isset( $_GET['order_date'] ) ? sanitize_key( $_GET['order_date'] ) : NULL;
$user_orders  = dokan_get_seller_orders( $seller_id, $order_status, $order_date, $limit, $offset, $customer_id );

$order_statuses = array(
    '-1'            => __( 'إجراءات للكل', 'dokan-lite' ),
    // 'wc-on-hold'    => __( 'تغيير الحالة إلى الانتظار', 'dokan-lite' ),
    'wc-processing' => __( 'تغيير الحالة إلى بانتظار البوليصة', 'dokan-lite' ),
    'wc-cancelled'  => __( 'تغيير الحالة إلى ملغي', 'dokan-lite' )
);
$order_statuses = apply_filters( 'dokan_bulk_order_statuses', $order_statuses );

if ( $user_orders ) {
    ?>
    <form id="order-filter" method="POST" class="dokan-form-inline">
        <?php if( dokan_get_option( 'order_status_change', 'dokan_selling', 'on' ) == 'on' ) : ?>
            <div class="dokan-form-group">
                <label for="bulk-order-action-selector" class="screen-reader-text"><?php esc_html_e( 'اختر عمل للكل', 'dokan-lite' ); ?></label>

                <select name="status" id="bulk-order-action-selector" class="dokan-form-control chosen">
                    <?php foreach ( $order_statuses as $key => $value ) : ?>
                        <option class="bulk-order-status" value="<?php echo esc_attr( $key ) ?>"><?php echo esc_attr( $value ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="dokan-form-group">
                <?php wp_nonce_field( 'bulk_order_status_change', 'security' ); ?>
                <input type="submit" name="bulk_order_status_change" id="bulk-order-action" class="dokan-btn dokan-btn-theme" value="<?php esc_attr_e( 'تطبيق', 'dokan-lite' ); ?>">
            </div>
        <?php endif; ?>
        <table class="dokan-table dokan-table-striped">
            <thead>
                <tr>
                    <th id="cb" class="manage-column column-cb check-column">
                        <label for="cb-select-all"></label>
                        <input id="cb-select-all" class="dokan-checkbox" type="checkbox">
                    </th>
                    <th><?php esc_html_e( 'الطلب', 'dokan-lite' ); ?></th>
                    <th><?php esc_html_e( 'إجمالي الطلبات', 'dokan-lite' ); ?></th>
                    <th><?php esc_html_e( 'الحالة', 'dokan-lite' ); ?></th>
                    <th><?php esc_html_e( 'العميل', 'dokan-lite' ); ?></th>
                    <th><?php esc_html_e( 'التاريخ', 'dokan-lite' ); ?></th>
                    <?php if ( current_user_can( 'dokan_manage_order' ) ): ?>
                        <th width="17%"><?php esc_html_e( 'العمل', 'dokan-lite' ); ?></th>
                    <?php endif ?>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($user_orders as $order) {
                    $the_order = new WC_Order( $order->order_id );
                    ?>
                    <tr >
                        <td class="dokan-order-select">
                            <label for="cb-select-<?php echo esc_attr( $order->order_id ); ?>"></label>
                            <input class="cb-select-items dokan-checkbox" type="checkbox" name="bulk_orders[]" value="<?php echo esc_attr( $order->order_id ); ?>">
                        </td>
                        <td class="dokan-order-id" data-title="<?php esc_attr_e( 'الطلب', 'dokan-lite' ); ?>" >
                            <?php if ( current_user_can( 'dokan_view_order' ) ): ?>
                                <?php echo '<a href="' . esc_url( wp_nonce_url( add_query_arg( array( 'order_id' => dokan_get_prop( $the_order, 'id' ) ), dokan_get_navigation_url( 'orders' ) ), 'dokan_view_order' ) ) . '"><strong>' . sprintf( __( 'الطلب %s', 'dokan-lite' ), esc_attr( $the_order->get_order_number() ) ) . '</strong></a>'; ?>
                            <?php else: ?>
                                <?php echo '<strong>' . sprintf( __( 'الطلب %s', 'dokan-lite' ), esc_attr( $the_order->get_order_number() ) ) . '</strong>'; ?>
                            <?php endif ?>
                        </td>
                        <td class="dokan-order-total" data-title="<?php esc_attr_e( 'اجمالي الطلبات ', 'dokan-lite' ); ?>" >
                            <?php echo $the_order->get_formatted_order_total(); ?>
                        </td>
                        <td class="dokan-order-status" data-title="<?php esc_attr_e( 'الحالة', 'dokan-lite' ); ?>" >
                            <?php echo '<span class="dokan-label dokan-label-' . dokan_get_order_status_class( dokan_get_prop( $the_order, 'status' ) ) . '">' . dokan_get_order_status_translated( dokan_get_prop( $the_order, 'status' ) ) . '</span>'; ?>
                        </td>
                        <td class="dokan-order-customer" data-title="<?php esc_attr_e( 'العميل', 'dokan-lite' ); ?>" >
                            <?php

                            // reset user info
                            $user_info = '';

                            if ( $the_order->get_user_id() ) {
                                $user_info = get_userdata( $the_order->get_user_id() );
                            }

                            if ( !empty( $user_info ) ) {

                                $user = '';

                                if ( $user_info->first_name || $user_info->last_name ) {
                                    $user .= esc_html( $user_info->first_name . ' ' . $user_info->last_name );
                                } else {
                                    $user .= esc_html( $user_info->display_name );
                                }

                            } else {
                                $user = __( 'Guest', 'dokan-lite' );
                            }

                            echo esc_html( $user );
                            ?>
                        </td>
                        <td class="dokan-order-date" data-title="<?php esc_attr_e( 'التاريخ', 'dokan-lite' ); ?>" >
                            <?php
                            if ( '0000-00-00 00:00:00' == dokan_get_date_created( $the_order ) ) {
                                $t_time = $h_time = __( 'غير منشورة', 'dokan-lite' );
                            } else {
                                $t_time    = get_the_time( 'Y/m/d g:i:s A', dokan_get_prop( $the_order, 'id' ) );
                                $gmt_time  = strtotime( dokan_get_date_created( $the_order ) . ' UTC' );
                                $time_diff = current_time( 'timestamp', 1 ) - $gmt_time;

                                if ( $time_diff > 0 && $time_diff < 24 * 60 * 60 ) {
                                    $h_time = sprintf( __( '%s ago', 'dokan-lite' ), human_time_diff( $gmt_time, current_time( 'timestamp', 1 ) ) );
                                } else {
                                    $h_time = get_the_time( 'Y/m/d', dokan_get_prop( $the_order, 'id' ) );
                                }
                            }

                            echo '<abbr title="' . esc_attr( dokan_date_time_format( $t_time ) ) . '">' . esc_html( apply_filters( 'post_date_column_time', dokan_date_time_format( $h_time, true ) , dokan_get_prop( $the_order, 'id' ) ) ) . '</abbr>';
                            ?>
                        </td>
                        <?php if ( current_user_can( 'dokan_manage_order' ) ): ?>
                            <td class="dokan-order-action" width="17%" data-title="<?php esc_attr_e( 'العمل', 'dokan-lite' ); ?>" >
                                <?php
                                do_action( 'woocommerce_admin_order_actions_start', $the_order );

                                $actions = array();

                                if ( dokan_get_option( 'order_status_change', 'dokan_selling', 'on' ) == 'on' ) {
                                    // if ( in_array( dokan_get_prop( $the_order, 'status' ), array( 'pending', 'on-hold' ) ) ) {
                                    if ( in_array( dokan_get_prop( $the_order, 'status' ), array( 'on-hold' ) ) ) {
                                        $actions['processing'] = array(
                                            'url' => wp_nonce_url( admin_url( 'admin-ajax.php?action=dokan-mark-order-processing&order_id=' . dokan_get_prop( $the_order, 'id' ) ), 'dokan-mark-order-processing' ),
                                            'name' => __( 'بانتظار البوليصة', 'dokan-lite' ),
                                            'action' => "processing",
                                            'icon' => '<i class="fa fa-check">&nbsp;</i>',
                                            'color' => 'delivered'
                                        );
                                       
                                    }

                                    if ( in_array( dokan_get_prop( $the_order, 'status' ), array( 'on-hold', 'processing' ) ) ) {
                                        $actions['cancelled'] = array(
                                            'url' => wp_nonce_url( admin_url( 'admin-ajax.php?action=dokan-mark-order-cancelled&order_id=' . dokan_get_prop( $the_order, 'id' ) ), 'dokan-mark-order-cancelled' ),
                                            'name' => __( 'الغاء', 'dokan-lite' ),
                                            'action' => "cancelled",
                                            'icon' => '<i class="fa fa-times">&nbsp;</i>',
                                            'color' => 'cancelled'
                                        );
                                    }

                                }

                                $actions['view'] = array(
                                    'url' => wp_nonce_url( add_query_arg( array( 'order_id' => dokan_get_prop( $the_order, 'id' ) ), dokan_get_navigation_url( 'orders' ) ), 'dokan_view_order' ),
                                    'name' => __( 'عرض', 'dokan-lite' ),
                                    'action' => "view",
                                    'icon' => '<i class="fa fa-eye">&nbsp;</i>',
                                    'color' => 'pickedup'
                                );

                                $actions = apply_filters( 'woocommerce_admin_order_actions', $actions, $the_order );

                                foreach ($actions as $action) {
                                    $icon = ( isset( $action['icon'] ) ) ? $action['icon'] : '';
                                    printf( '<a style="color:white" class="dokan-btn dokan-btn-md dokan-label-%s tips" href="%s" data-toggle="tooltip" data-placement="top" title="%s">%s</a> ', dokan_get_order_status_class($action['color']), esc_url( $action['url'] ), esc_attr( $action['name'] ), $icon );
                                }

                                do_action( 'woocommerce_admin_order_actions_end', $the_order );
                                ?>
                            </td>
                        <?php endif ?>
                        <td class="diviader"></td>
                    </tr>

                <?php } ?>

            </tbody>

        </table>
    </form>

    <?php
    $order_count = dokan_get_seller_orders_number( $seller_id, $order_status );

    // if date is selected then calculate number_of_pages accordingly otherwise calculate number_of_pages =  ( total_orders / limit );
    if ( ! is_null( $order_date ) ) {
        if ( count( $user_orders ) >= $limit ) {
            $num_of_pages = ceil ( ( ( $order_count + count( $user_orders ) ) - count( $user_orders ) ) / $limit );
        } else {
            $num_of_pages = ceil( count( $user_orders ) / $limit );
        }
    } else {
        $num_of_pages = ceil( $order_count / $limit );
    }


    $base_url  = dokan_get_navigation_url( 'orders' );

    if ( $num_of_pages > 1 ) {
        echo '<div class="pagination-wrap">';
        $page_links = paginate_links( array(
            'current'   => $paged,
            'total'     => $num_of_pages,
            'base'      => $base_url. '%_%',
            'format'    => '?pagenum=%#%',
            'add_args'  => false,
            'type'      => 'array',
        ) );

        echo "<ul class='pagination'>\n\t<li>";
        echo join("</li>\n\t<li>", $page_links);
        echo "</li>\n</ul>\n";
        echo '</div>';
    }
    ?>

<?php } else { ?>

    <div class="dokan-error">
        <?php esc_html_e( 'لم يتم العثور على طلبات', 'dokan-lite' ); ?>
    </div>

<?php } ?>

<script>
    (function($){
        $(document).ready(function(){
            $('.datepicker').datepicker({
                dateFormat: 'yy-m-d'
            });
        });
    })(jQuery);
</script>

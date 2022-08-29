<?php
$my_class = Dokan_Template_Orders::init();
remove_action('dokan_order_inside_content',array($my_class,'order_listing_status_filter'),10);
add_action('dokan_order_inside_content','main_orders',10);


function main_orders(){
    if ( isset( $_GET['order_id'] ) ) {
        ?>
            <a href="<?php echo esc_url( dokan_get_navigation_url( 'orders' ) ) ; ?>" class="dokan-btn"><?php esc_html_e( '&larr; الطلبات', 'dokan-lite' ); ?></a>
        <?php
    } else {
        order_status();
    }
}


function order_status(){
    $_get_data = wp_unslash( $_GET );

    $orders_url = dokan_get_navigation_url( 'orders' );

    $status_class         = isset( $_get_data['order_status'] ) ? $_get_data['order_status'] : 'all';
    $orders_counts        = dokan_count_orders( dokan_get_current_user_id() );
    $order_date           = ( isset( $_get_data['order_date'] ) ) ? $_get_data['order_date'] : '';
    $date_filter          = array();
    $all_order_url        = array();
    $complete_order_url   = array();
    $processing_order_url = array();
    $pending_order_url    = array();
    $on_hold_order_url    = array();
    $canceled_order_url   = array();
    $refund_order_url     = array();
    $failed_order_url     = array();
    ?>

    <style>
        .ostatus {  background-color: #133E66;
                    border-radius: 6%;
                    width: 114px;
                    padding: 12px 0!important;
                    margin-bottom: 3px!important;
                    text-align: center;}
        .ostatus a{color:white}
    </style>

    <ul style="margin-top:10px" class="list-inline order-statuses-filter">
        <li class="ostatus" <?php echo $status_class == 'all' ? ' class="active"' : ''; ?>>
            <?php
                if( $order_date ) {
                    $date_filter = array(
                        'order_date' => $order_date,
                        'dokan_order_filter' => 'Filter',
                    );
                }
                $all_order_url = array_merge( $date_filter, array( 'order_status' => 'all' ) );
                $all_order_url = ( empty( $all_order_url ) ) ? $orders_url : add_query_arg( $complete_order_url, $orders_url );
            ?>
            <a href="<?php echo esc_url( $all_order_url );  ?>">
                <?php printf( esc_html__( 'الكل (%d)', 'dokan-lite' ), esc_attr( $orders_counts->total ) ); ?></span>
            </a>
        </li>
        <li style="background-color:#73a724" class="ostatus" <?php echo $status_class == 'wc-delivered' ? ' class="active"' : ''; ?>>
            <?php
                if( $order_date ) {
                    $date_filter = array(
                        'order_date' => $order_date,
                        'dokan_order_filter' => 'Filter',
                    );
                }
                $complete_order_url = array_merge( array( 'order_status' => 'wc-delivered' ), $date_filter );
            ?>
            <a href="<?php echo esc_url( add_query_arg( $complete_order_url, $orders_url ) ); ?>">
                <?php printf( esc_html__( 'المستلمة (%d)', 'dokan-lite' ), esc_attr( $orders_counts->{'wc-delivered'} ) ); ?></span>
            </a>
        </li>
        <li style="background-color:#21759b" class="ostatus" <?php echo $status_class == 'wc-pickedup' ? ' class="active"' : ''; ?>>
            <?php
                if( $order_date ) {
                    $date_filter = array(
                        'order_date' => $order_date,
                        'dokan_order_filter' => 'Filter',
                    );
                }
                $processing_order_url = array_merge( $date_filter, array( 'order_status' => 'wc-pickedup' ) );
            ?>
            <a href="<?php echo esc_url( add_query_arg( $processing_order_url, $orders_url ) ); ?>">
                <?php printf( esc_html__( 'المشحونة (%d)', 'dokan-lite' ), esc_attr( $orders_counts->{'wc-pickedup'} ) ); ?></span>
            </a>
        </li>
        <li style="background-color:orange" class="ostatus" <?php echo $status_class == 'wc-completed' ? ' class="active"' : ''; ?>>
            <?php
                if( $order_date ) {
                    $date_filter = array(
                        'order_date' => $order_date,
                        'dokan_order_filter' => 'Filter',
                    );
                }
                $on_hold_order_url = array_merge( $date_filter, array( 'order_status' => 'wc-completed' ) );
            ?>
            <a href="<?php echo esc_url( add_query_arg( $on_hold_order_url, $orders_url ) ); ?>">
                <?php printf( esc_html__( 'جاهز للشحن (%d)', 'dokan-lite' ), esc_attr( $orders_counts->{'wc-completed'} ) ); ?></span>
            </a>
        </li>
        <li style="background-color:#5bc0de" class="ostatus" <?php echo $status_class == 'wc-processing' ? ' class="active"' : ''; ?>>
            <?php
                if( $order_date ) {
                    $date_filter = array(
                        'order_date' => $order_date,
                        'dokan_order_filter' => 'Filter',
                    );
                }
                $pending_order_url = array_merge( $date_filter, array( 'order_status' => 'wc-processing' ) );
            ?>
            <a href="<?php echo esc_url( add_query_arg( $pending_order_url, $orders_url ) ); ?>">
                <?php printf( esc_html__( 'بانتظار البوليصة (%d)', 'dokan-lite' ), esc_attr( $orders_counts->{'wc-processing'} ) ); ?></span>
            </a>
        </li>
        <li style="background-color:#999" class="ostatus" <?php echo $status_class == 'wc-on-hold' ? ' class="active"' : ''; ?>>
            <?php
                if( $order_date ) {
                    $date_filter = array(
                        'order_date' => $order_date,
                        'dokan_order_filter' => 'Filter',
                    );
                }
                $canceled_order_url = array_merge( $date_filter, array( 'order_status' => 'wc-on-hold' ) );
            ?>
            <a href="<?php echo esc_url( add_query_arg( $canceled_order_url, $orders_url ) ); ?>">
                <?php printf( esc_html__( 'بانتظار الموافقة (%d)', 'dokan-lite' ), esc_attr( $orders_counts->{'wc-on-hold'} ) ); ?></span>
            </a>
        </li>
        <li style="background-color:#d54e21" class="ostatus" <?php echo $status_class == 'wc-canceled' ? ' class="active"' : ''; ?>>
            <?php
                if( $order_date ) {
                    $date_filter = array(
                        'order_date' => $order_date,
                        'dokan_order_filter' => 'Filter',
                    );
                }
                $canceled_order_url = array_merge( $date_filter, array( 'order_status' => 'wc-cancelled' ) );
            ?>
            <a href="<?php echo esc_url( add_query_arg( $canceled_order_url, $orders_url ) ); ?>">
                <?php printf( esc_html__( 'الملغية (%d)', 'dokan-lite' ), esc_attr( $orders_counts->{'wc-cancelled'} ) ); ?></span>
            </a>
        </li>
        <li style="background-color:#000" class="ostatus" <?php echo $status_class == 'wc-refunded' ? ' class="active"' : ''; ?>>
            <?php
                if( $order_date ) {
                    $date_filter = array(
                        'order_date' => $order_date,
                        'dokan_order_filter' => 'Filter',
                    );
                }
                $refund_order_url = array_merge( $date_filter, array( 'order_status' => 'wc-refunded' ) );
            ?>
            <a href="<?php echo esc_url( add_query_arg( $refund_order_url, $orders_url ) ); ?>">
                <?php printf( esc_html__( 'المرتجعة (%d)', 'dokan-lite' ), esc_attr( $orders_counts->{'wc-refunded'} ) ); ?></span>
            </a>
        </li>
      

        <?php do_action( 'dokan_status_listing_item', $orders_counts ); ?>
    </ul>
    <?php
}

?>
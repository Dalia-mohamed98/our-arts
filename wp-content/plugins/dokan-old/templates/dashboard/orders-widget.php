<?php
/**
 *  Dashboard Widget Template
 *
 *  Get dokan dashboard widget template
 *
 *  @since 2.4
 *
 *  @package dokan
 *
 */
?>

<div style="padding: 5px 15px 0;">
    <h2 style="display:inline">إدارة الطلبات</h2>
</div>

<table class="tborder">
    <tbody>
    <tr style="height: 80px;">
        
        <td class="tdorder" style=" 
                    padding-right: .5em!important;
                    background-color: <?php echo esc_attr( $order_data[1]['color'] ); ?>">
                <a href="<?php echo esc_url( add_query_arg( array( 'order_status' => 'wc-on-hold' ), $orders_url ) ); ?>" style="color: white">
                    <div style="font-size: 25px;" class="count"><?php echo esc_html( number_format_i18n( $orders_count->{'wc-on-hold'}, 0 ) ); ?></div>
                    <div class="title"><?php esc_attr_e( 'بانتظار الموافقة', 'dokan-lite' ); ?></div> 
                    <button class="btnwhite">
                    <?php esc_attr_e( 'إظهر التفاصيل', 'dokan-lite' ); ?>
                    <span style="">&#62;</span>
                    </button>
                </a>
        </td>
      
        <td class="tdorder" style=" 
                    background-color: <?php echo esc_attr( $order_data[4]['color'] ); ?>">
                <a href="<?php echo esc_url( add_query_arg( array( 'order_status' => 'wc-processing' ), $orders_url ) ); ?>" style="color: white">
                    <div style="font-size: 25px;" class="count"><?php echo esc_html( number_format_i18n( $orders_count->{'wc-processing'}, 0 ) ); ?></div>
                    <span class="title"><?php esc_attr_e( 'بانتظار البوليصة', 'dokan-lite' ); ?></span> 
                    <button class="btnwhite">
                    <?php esc_attr_e( 'إظهر التفاصيل', 'dokan-lite' ); ?> 
                    <span style="">&#62;</span>
                    </button>
                </a>
        </td>

        <td class="tdorder" style=" 
                    /* text-align: center; */
                    background-color: <?php echo esc_attr( $order_data[5]['color'] ); ?>">
                <a href="<?php echo esc_url( add_query_arg( array( 'order_status' => 'wc-completed' ), $orders_url ) ); ?>" style="color: white">
                    <div style="font-size: 25px;" class="count"><?php echo esc_html( number_format_i18n( $orders_count->{'wc-completed'}, 0 ) ); ?></div>
                    <span class="title"><?php esc_attr_e( 'جاهز للشحن', 'dokan-lite' ); ?></span> 
                    <button class="btnwhite">
                    <?php esc_attr_e( 'إظهر التفاصيل', 'dokan-lite' ); ?>
                    <span style="">&#62;</span>
                    </button>
                </a>
        </td>

         <td class="tdorder" style=" 
                    padding-right: .5em!important;
                    background-color: <?php echo esc_attr( $order_data[2]['color'] ); ?>">
                <a href="<?php echo esc_url( add_query_arg( array( 'order_status' => 'wc-pickedup' ), $orders_url ) ); ?>" style="color: white">
                    <div style="font-size: 25px;" class="count"><?php echo esc_html( number_format_i18n( $orders_count->{'wc-pickedup'}, 0 ) ); ?></div>
                    <div class="title"><?php esc_attr_e( 'المشحونة', 'dokan-lite' ); ?></div> 
                    <button class="btnwhite">
                    <?php esc_attr_e( 'إظهر التفاصيل', 'dokan-lite' ); ?>
                    <span style="">&#62;</span>
                    </button>
                </a>
        </td>
        
        <td class="tdorder" style=" 
                    background-color: <?php echo esc_attr( $order_data[0]['color'] ); ?>">
                <a href="<?php echo esc_url( add_query_arg( array( 'order_status' => 'wc-delivered' ), $orders_url ) ); ?>" style="color: white">
                    <div style="font-size: 25px;" class="count"><?php echo esc_html( number_format_i18n( $orders_count->{'wc-delivered'}, 0 ) ); ?></div>
                    <span class="title"><?php esc_html_e( 'المستلمة', 'dokan-lite' ); ?></span> 
                    <button class="btnwhite">
                    <?php esc_attr_e( 'إظهر التفاصيل', 'dokan-lite' ); ?>
                    <span style="">&#62;</span>
                    </button>
                </a>
        </td>

        <td class="tdorder" style=" 
                    padding-left: .5em;
                    background-color: <?php echo esc_attr( $order_data[3]['color'] ); ?>">
                <a href="<?php echo esc_url( add_query_arg( array( 'order_status' => 'wc-cancelled' ), $orders_url ) ); ?>" style="color: white">
                    <div style="font-size: 25px;" class="count"><?php echo esc_html( number_format_i18n( $orders_count->{'wc-cancelled'}, 0 ) ); ?></div>
                    <span class="title"><?php esc_html_e( 'الملغية', 'dokan-lite' ); ?></span> 
                    <button class="btnwhite">
                    <?php esc_attr_e( 'إظهر التفاصيل', 'dokan-lite' ); ?>
                    <span style="">&#62;</span>
                    </button>
                </a>
        </td>
    </tr>
    </tbody>
</table>

    <!-- <div class="content-half-part">
        <canvas id="order-stats"></canvas>
    </div> -->

<script type="text/javascript">
    jQuery(function($) {
        // var order_stats = <?php //echo wp_json_encode( wp_list_pluck( $order_data, 'value' ) ); ?>;
        // var colors = <?php //echo wp_json_encode( wp_list_pluck( $order_data, 'color' ) ); ?>;
        // var labels = <?php //echo wp_json_encode( wp_list_pluck( $order_data, 'label' ) ); ?>;

        // var ctx = $("#order-stats").get(0).getContext("2d");
        // var donn = new Chart(ctx, {
        //     type: 'doughnut',
        //     data: {
        //         datasets: [{
        //             data: order_stats,
        //             backgroundColor: colors
        //         }],
        //         labels: labels,
        //     },
        //     options: {
        //         legend: {
        //             display: false
        //         }
        //     }
        // });
    });
</script>

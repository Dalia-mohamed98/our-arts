<?php

/**
 * Dokan Dashboard Template
 *
 * Dokan Dashboard Product widget template
 *
 * @since 2.4
 *
 * @package dokan
 */
?>

<div class="dashboard-widget products">
    <div class="widget-title">
        <i class="fa fa-briefcase" aria-hidden="true"></i> <?php esc_html_e( 'المنتجات', 'dokan-lite' ); ?>

        <span class="pull-right">
            <a href="<?php echo esc_url( dokan_get_navigation_url( 'new-product' ) ); ?>"><?php esc_html_e( '+ إضافة منتج جديد', 'dokan-lite' ); ?></a>
        </span>
    </div>

    <ul class="list-unstyled list-count">
        <li>
            <a href="<?php echo esc_url( $products_url ); ?>">
                <span class="title"><?php esc_html_e( 'الإجمالي', 'dokan-lite' ); ?></span> <span class="count"><?php echo esc_attr( $post_counts->total ); ?></span>
            </a>
        </li>
        <li>
            <a href="<?php echo esc_url( add_query_arg( array( 'post_status' => 'publish' ), $products_url ) ); ?>">
                <span class="title"><?php esc_html_e( 'النشطة', 'dokan-lite' ); ?></span> <span class="count"><?php echo esc_attr( $post_counts->publish ); ?></span>
            </a>
        </li>
        <li>
            <a href="<?php echo esc_url( add_query_arg( array( 'post_status' => 'draft' ), $products_url ) ); ?>">
                <span class="title"><?php esc_html_e( 'الموقوفة', 'dokan-lite' ); ?></span> <span class="count"><?php echo esc_attr( $post_counts->draft ); ?></span>
            </a>
        </li>
        <li>
            <a href="<?php echo esc_url( add_query_arg( array( 'post_status' => 'pending' ), $products_url ) ); ?>">
                <span class="title"><?php esc_html_e( 'في انتظار المراجعة', 'dokan-lite' ); ?></span> <span class="count"><?php echo esc_attr( $post_counts->pending ); ?></span>
            </a>
        </li>
    </ul>
</div> <!-- .products -->

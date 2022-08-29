<div class="dokan-product-inventory dokan-edit-row <?php echo esc_attr( $class ); ?>">
    <div class="dokan-section-heading" data-togglehandler="dokan_product_inventory">
        <h2><i class="fa fa-cubes" aria-hidden="true"></i> <?php esc_html_e( 'المخزون', 'dokan-lite' ); ?></h2>
        <p><?php esc_html_e( 'إدارة المخزون لهذا المنتج.', 'dokan-lite' ); ?></p>
        <a href="#" class="dokan-section-toggle">
            <i class="fa fa-sort-desc fa-flip-vertical" aria-hidden="true"></i>
        </a>
        <div class="dokan-clearfix"></div>
    </div>

    <div class="dokan-section-content">

        <div class="content-half-part dokan-form-group hide_if_variation">
            <label for="_sku" class="form-label"><?php esc_html_e( 'SKU', 'dokan-lite' ); ?> <span><?php esc_html_e( '(Stock Keeping Unit)', 'dokan-lite' ); ?></span></label>
            <?php dokan_post_input_box( $post_id, '_sku' ); ?>
        </div>

        <div class="content-half-part hide_if_variable">
            <label for="_stock_status" class="form-label"><?php esc_html_e( 'حالة المخزون', 'dokan-lite' ); ?></label>

            <?php dokan_post_input_box( $post_id, '_stock_status', array( 'options' => array(
                'instock'     => __( 'في المخزون', 'dokan-lite' ),
                'outofstock'  => __( 'إنتهى من المخزن', 'dokan-lite' ),
                'onbackorder' => __( 'تحت الطلب', 'dokan-lite' ),
            ) ), 'select' ); ?>
        </div>

        <div class="dokan-clearfix"></div>

        <?php if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) : ?>
        <div class="dokan-form-group hide_if_variation hide_if_grouped">
            <?php dokan_post_input_box( $post_id, '_manage_stock', array( 'label' => __( 'تمكين إدارة مخزون المنتج', 'dokan-lite' ) ), 'checkbox' ); ?>
        </div>

        <div class="show_if_stock dokan-stock-management-wrapper dokan-form-group dokan-clearfix">

            <div class="content-half-part hide_if_variation">
                <label for="_stock" class="form-label"><?php esc_html_e( 'كمية المخزون', 'dokan-lite' ); ?></label>
                <input type="number" class="dokan-form-control" name="_stock" placeholder="<?php esc_attr__( '1', 'dokan-lite' ); ?>" value="<?php echo esc_attr( wc_stock_amount( $_stock ) ); ?>" min="0" step="1">
            </div>

            <?php if ( version_compare( WC_VERSION, '3.4.7', '>' ) ) : ?>
            <div class="content-half-part hide_if_variation">
                <label for="_low_stock_amount" class="form-label"><?php esc_html_e( 'انخفاض معدل المخزون ', 'dokan-lite' ); ?></label>
                <input type="number" class="dokan-form-control" name="_low_stock_amount" placeholder="<?php esc_attr__( '1', 'dokan-lite' ); ?>" value="<?php echo esc_attr( wc_stock_amount( $_low_stock_amount ) ); ?>" min="0" step="1">
            </div>
            <?php endif; ?>

            <div class="content-half-part hide_if_variation last-child">
                <label for="_backorders" class="form-label"><?php esc_html_e( 'السماح للطلبات المؤجلة', 'dokan-lite' ); ?></label>

                <?php dokan_post_input_box( $post_id, '_backorders', array( 'options' => array(
                    'no'     => __( 'لا تسمح', 'dokan-lite' ),
                    'notify' => __( 'السماح ولكن بإبلاغ العميل', 'dokan-lite' ),
                    'yes'    => __( 'السماح', 'dokan-lite' )
                ) ), 'select' ); ?>
            </div>
            <div class="dokan-clearfix"></div>
        </div><!-- .show_if_stock -->
        <?php endif; ?>

        <div class="dokan-form-group hide_if_grouped">
            <label class="" for="_sold_individually">
                <input name="_sold_individually" id="_sold_individually" value="yes" type="checkbox" <?php checked( $_sold_individually, 'yes' ); ?>>
                <?php esc_html_e( 'اسمح فقط بشراء كمية واحدة من هذا المنتج في طلب واحد', 'dokan-lite' ) ?>
            </label>
        </div>

        <?php if ( $post_id ): ?>
            <?php do_action( 'dokan_product_edit_after_inventory' ); ?>
        <?php endif; ?>

        <?php do_action( 'dokan_product_edit_after_downloadable', $post, $post_id ); ?>
        <?php do_action( 'dokan_product_edit_after_sidebar', $post, $post_id ); ?>
        <?php do_action( 'dokan_single_product_edit_after_sidebar', $post, $post_id ); ?>

    </div><!-- .dokan-side-right -->
</div><!-- .dokan-product-inventory -->

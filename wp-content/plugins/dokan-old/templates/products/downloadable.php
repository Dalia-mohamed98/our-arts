<div class="dokan-download-options dokan-edit-row dokan-clearfix <?php echo esc_attr( $class ); ?>">
    <div class="dokan-section-heading" data-togglehandler="dokan_download_options">
        <h2><i class="fa fa-download" aria-hidden="true"></i> <?php esc_html_e( 'خيارات قابلة للتحميل', 'dokan-lite' ); ?></h2>
        <p><?php esc_html_e( 'قم بتكوين إعدادات المنتج القابلة للتنزيل', 'dokan-lite' ); ?></p>
        <a href="#" class="dokan-section-toggle">
            <i class="fa fa-sort-desc fa-flip-vertical" aria-hidden="true"></i>
        </a>
        <div class="dokan-clearfix"></div>
    </div>

    <div class="dokan-section-content">
        <div class="dokan-divider-top dokan-clearfix">

            <?php do_action( 'dokan_product_edit_before_sidebar' ); ?>

            <div class="dokan-side-body dokan-download-wrapper">
                <table class="dokan-table">
                    <tfoot>
                        <tr>
                            <th colspan="3">
                                <a href="#" class="insert-file-row dokan-btn dokan-btn-sm dokan-btn-success" data-row="<?php
                                    $file = array(
                                        'file' => '',
                                        'name' => ''
                                    );
                                    ob_start();
                                    include DOKAN_INC_DIR . '/woo-views/html-product-download.php';
                                    echo esc_attr( ob_get_clean() );
                                ?>"><?php esc_html_e( 'اضف ملف', 'dokan-lite' ); ?></a>
                            </th>
                        </tr>
                    </tfoot>
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'الإسم', 'dokan-lite' ); ?> <span class="tips" title="<?php esc_attr_e( 'هذا هو اسم التنزيل الظاهر للعميل.', 'dokan-lite' ); ?>">[?]</span></th>
                            <th><?php esc_html_e( 'عنوان الملف الإلكتروني', 'dokan-lite' ); ?> <span class="tips" title="<?php esc_attr_e( 'هذا هو عنوان URL أو المسار المطلق للملف الذي سيتمكن العملاء من الوصول إليه.', 'dokan-lite' ); ?>">[?]</span></th>
                            <th><?php esc_html_e( 'عمل', 'dokan-lite' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $downloadable_files = get_post_meta( $post_id, '_downloadable_files', true );

                        if ( $downloadable_files ) {
                            foreach ( $downloadable_files as $key => $file ) {
                                include DOKAN_INC_DIR . '/woo-views/html-product-download.php';
                            }
                        }
                        ?>
                    </tbody>
                </table>

                <div class="dokan-clearfix">
                    <div class="content-half-part">
                        <label for="_download_limit" class="form-label"><?php esc_html_e( 'حد التحميل', 'dokan-lite' ); ?></label>
                        <?php dokan_post_input_box( $post_id, '_download_limit', array( 'placeholder' => __( 'e.g. 4', 'dokan-lite' ) ) ); ?>
                    </div><!-- .content-half-part -->

                    <div class="content-half-part">
                        <label for="_download_expiry" class="form-label"><?php esc_html_e( ' انتهاء صلاحية تنزيل', 'dokan-lite' ); ?></label>
                        <?php dokan_post_input_box( $post_id, '_download_expiry', array( 'placeholder' => __( 'عدد الأيام', 'dokan-lite' ) ) ); ?>
                    </div><!-- .content-half-part -->
                </div>

            </div> <!-- .dokan-side-body -->
        </div> <!-- .downloadable -->
    </div>
</div>

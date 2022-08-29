<div class="dokan-form-group dokan-product-type-container <?php echo esc_attr( $class ); ?>">
    <div class="content-half-part downloadable-checkbox">
        <label>
            <input type="checkbox" <?php checked( $is_downloadable, true ); ?> class="_is_downloadable" name="_downloadable" id="_downloadable"> <?php esc_html_e( 'للتحميل', 'dokan-lite' ); ?> <i class="fa fa-question-circle tips" aria-hidden="true" data-title="<?php esc_attr_e( 'توفر المنتجات القابلة للتنزيل حق الوصول إلى ملف عند الشراء.', 'dokan-lite' ); ?>"></i>
        </label>
    </div>
    <div class="content-half-part virtual-checkbox">
        <label>
            <input type="checkbox" <?php checked( $is_virtual, true ); ?> class="_is_virtual" name="_virtual" id="_virtual"> <?php esc_html_e( 'Virtual', 'dokan-lite' ); ?> <i class="fa fa-question-circle tips" aria-hidden="true" data-title="<?php esc_attr_e( 'المنتجات الافتراضية غير ملموسة ولا يتم شحنها.', 'dokan-lite' ); ?>"></i>
        </label>
    </div>
    <div class="dokan-clearfix"></div>
</div>

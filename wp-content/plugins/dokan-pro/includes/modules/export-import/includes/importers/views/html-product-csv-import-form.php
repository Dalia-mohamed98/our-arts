<?php
/**
 * Admin View: Product import form
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
?>
<form class="wc-progress-form-content woocommerce-importer" enctype="multipart/form-data" method="post" style="text-align: right;">
    <header>
        <h2><?php esc_html_e( 'تحميل المنتجات من ملف CSV', 'dokan' ); ?></h2>
        <p><?php esc_html_e( 'تسمح لك هذه الأداة بتحميل (أو دمج) بيانات المنتج إلى متجرك من ملف CSV.', 'dokan' ); ?></p>
    </header>
    <section>
        <table class="form-table woocommerce-importer-options">
            <tbody>
				<tr>
                    <th scope="row">
                        <label for="download">
                            <?php _e( 'تنزيل نموذج المنتجات والفئات :', 'dokan' ); ?>
                        </label>
                    </th>
                    <td>
                        <div>
                            <button class="success " style="border-radius:5px"><a class="csv-link" style="color:white; " href="# ">تنزيل الملف</a></button>
                        </div>
						<script>
						jQuery(document).ready(function ($) {
							$('a.csv-link').click(function(e) {
								e.preventDefault();
								window.open('https://our-arts.com/wp-content/uploads/csv/نموذج-المنتجات.csv');
								window.open('https://our-arts.com/wp-content/uploads/csv/نموذج-الفئات.csv');
								
							});
						});
						</script>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="upload">
                            <?php _e( 'اختر ملف CSV من جهازك:', 'dokan' ); ?>
                        </label>
                    </th>
                    <td>
                        <?php
                        if ( !empty( $upload_dir['error'] ) ) {
                            ?><div class="inline error">
                                <p><?php esc_html_e( 'قبل أن تتمكن من تحميل ملف الاستيراد الخاص بك ، ستحتاج إلى إصلاح الخطأ التالي:', 'dokan' ); ?></p>
                                <p><strong><?php echo esc_html( $upload_dir['error'] ); ?></strong></p>
                            </div><?php
                        } else {
                            ?>
                            <input type="file" id="upload" name="import" size="25"/>
                            <input type="hidden" name="action" value="save" />
                            <input type="hidden" name="max_file_size" value="<?php echo esc_attr( $bytes ); ?>" />
                            <br><small><?php
                                /* translators: %s: maximum upload size */
                                printf(
                                __( 'أقصى حجم: %s', 'dokan' ), $size
                                );
                                ?></small>
                                <?php
                        }
                        ?>
                    </td>
                </tr>
				<tr>
                    <th><label for="woocommerce-importer-update-existing"><?php _e( 'تحديث المنتجات الموجودة', 'dokan' ); ?></label><br/></th>
                    <td>
                        <input type="hidden" name="update_existing" value="0" />
                        <input type="checkbox" id="woocommerce-importer-update-existing" name="update_existing" value="1" />
                        <label for="woocommerce-importer-update-existing"><?php esc_html_e( 'سيتم تحديث المنتجات الحالية التي تتطابق مع ID أو SKU. وتخطي المنتجات غير الموجودة.', 'dokan' ); ?></label>
                    </td>
                </tr>
<!-- 				<tr class="woocommerce-importer-advanced hidden">
                        <th>
                                <label for="woocommerce-importer-file-url"><?php _e( '<em>or</em> enter the path to a CSV file on your server:', 'dokan' ); ?></label>
                        </th>
                        <td>
                                <label for="woocommerce-importer-file-url" class="woocommerce-importer-file-url-field-wrapper">
                                        <code><?php echo esc_html( ABSPATH ) . ' '; ?></code><input type="text" id="woocommerce-importer-file-url" name="file_url" />
                                </label>
                        </td>
                </tr>
                <tr class="woocommerce-importer-advanced hidden">
                        <th><label><?php _e( 'CSV Delimiter', 'dokan' ); ?></label><br/></th>
                        <td><input type="text" name="delimiter" placeholder="," size="2" /></td>
                </tr>
 -->            </tbody>
        </table>
    </section>
    <script type="text/javascript">
            jQuery( function () {
                jQuery( '.woocommerce-importer-toggle-advanced-options' ).on( 'click', function () {
                    var elements = jQuery( '.woocommerce-importer-advanced' );
                    if ( elements.is( '.hidden' ) ) {
                        elements.removeClass( 'hidden' );
                        jQuery( this ).text( jQuery( this ).data( 'hidetext' ) );
                    } else {
                        elements.addClass( 'hidden' );
                        jQuery( this ).text( jQuery( this ).data( 'showtext' ) );
                    }
                    return false;
                } );
            } );
    </script>
    <div class="wc-actions">
            <!--<a href="#" class="woocommerce-importer-toggle-advanced-options" data-hidetext="<?php esc_html_e( 'Hide advanced options', 'dokan' ); ?>" data-showtext="<?php esc_html_e( 'Hide advanced options', 'dokan' ); ?>"><?php esc_html_e( 'Show advanced options', 'dokan' ); ?></a>-->
        <input type="submit" class="button button-primary button-next" value="<?php esc_attr_e( 'استمرار', 'dokan' ); ?>" name="save_step" />
        <?php wp_nonce_field( 'woocommerce-csv-importer' ); ?>
    </div>
</form>
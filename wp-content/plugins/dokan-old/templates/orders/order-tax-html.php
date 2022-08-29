<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="tax_row" data-order_item_id="<?php echo esc_attr( $item_id ); ?>">
	<p class="wide">
		<label><?php esc_html_e( 'معدل الضريبة:', 'dokan-lite' ) ?></label>
		<select name="order_taxes_rate_id[<?php echo esc_attr( $item_id ); ?>]">
			<option value=""><?php esc_html_e( 'N/A', 'dokan-lite' ); ?></option>
			<?php foreach( $tax_codes as $tax_id => $tax_code ) : ?>
				<option value="<?php echo esc_attr( $tax_id ); ?>" <?php selected( $tax_id, isset( $item['rate_id'] ) ? $item['rate_id'] : '' ); ?>><?php echo esc_html( $tax_code ); ?></option>
			<?php endforeach; ?>
		</select>
		<input type="hidden" name="order_taxes_id[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $item_id ); ?>" />
	</p>
	<p class="first">
		<label><?php esc_html_e( 'ضريبة المبيعات:', 'dokan-lite' ) ?></label>
		<input type="number" step="any" min="0" name="order_taxes_amount[<?php echo esc_attr( $item_id ); ?>]" placeholder="0.00" value="<?php if ( isset( $item['tax_amount'] ) ) echo esc_attr( $item['tax_amount'] ); ?>" />
	</p>
	<p class="last">
		<label><?php esc_html_e( 'ضريبة الشحن:', 'dokan-lite' ) ?></label>
		<input type="number" step="any" min="0" name="order_taxes_shipping_amount[<?php echo esc_attr( $item_id ); ?>]" placeholder="0.00" value="<?php if ( isset( $item['shipping_tax_amount'] ) ) echo esc_attr( $item['shipping_tax_amount'] ); ?>" />
	</p>
	<a href="#" class="delete_tax_row">&times;</a>
	<div class="clear"></div>
</div>

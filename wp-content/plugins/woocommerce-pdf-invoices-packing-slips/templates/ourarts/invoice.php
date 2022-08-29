<?php //if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php //do_action( 'wpo_wcpdf_before_document', $this->type, $this->order ); 
$track = do_action('woocommerce_order_get_tracking_code', $this->order );
session_start();
?>

<header>
	<table>
	    <tr >
            <td style='text-align: left; padding-top:30px'><?= $this->header_logo(); ?></td>
        	<td style='text-align: right; padding-top:30px'>
        	    
        		<!-- insert your custom barcode setting your data in the GET parameter "data" -->
        		<img alt='Barcode Generator TEC-IT' style='width:25%'
        			 src='https://barcode.tec-it.com/barcode.ashx?data=<?php echo $_SESSION['order_tracking_code'];?>&code=&multiplebarcodes=false&translate-esc=false&unit=Fit&dpi=96&imagetype=Gif&rotation=0&color=%23000000&bgcolor=%23ffffff&codepage=&qunit=Mm&quiet=0'/>
        	</td>
	    </tr>
	</table>
</header>


<table class="customer_details">
	<thead>
		<tr>
			<?php //if (get_appearance_setting('Company Name')) { ?>
			<th colspan="2"><?php _e( 'OUR ARTS', 'woocommerce-pdf-invoices-packing-slips'); ?></th>	
			<th style='text-align: right;'><?php _e('اسم الشركة', 'woocommerce-pdf-invoices-packing-slips'); ?></th>
			<?php //} ?>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<td colspan="2">01001400776</td>
			<th style='text-align: right;'><?php _e('رقم الهاتف', 'woocommerce-pdf-invoices-packing-slips'); ?></th>

		</tr>
		<tr>
			<td colspan="2">Smart Village , October City</td>
			<th style='text-align: right;'><?php _e('العنوان', 'woocommerce-pdf-invoices-packing-slips'); ?></th>

		</tr>
		<tr>
			<td colspan="2"><?= sprintf('%s', $order->get_id()) ?></td>
			<th style='text-align: right;'><?php _e('رقم الطلب', 'woocommerce-pdf-invoices-packing-slips'); ?></th>

		</tr>
		<tr>
			<td colspan="2"><?= date_i18n(\get_option('date_format', 'm/d/Y'), $order->get_date_created()); ?></td>
			<th style='text-align: right;'><?php _e('التاريخ', 'woocommerce-pdf-invoices-packing-slips'); ?></th>

		</tr>

	</tfoot>
</table>

<?php //do_action( 'wpo_wcpdf_before_order_details', $this->type, $this->order ); ?>

<table class="customer_details">
	<thead>
		<tr>
		<th colspan="2"><?php _e('الإجمالي', 'woocommerce-pdf-invoices-packing-slips'); ?></th>
		<th ><?php _e('المنتج', 'woocommerce-pdf-invoices-packing-slips'); ?></th>
		<th ><?php _e('صورة المنتج', 'woocommerce-pdf-invoices-packing-slips'); ?></th>
		</tr>
	</thead>
	<tfoot style='text-align: left;'>
		<tr>
			<td colspan="2"><?= $order->get_subtotal_to_display(); ?></td>
			<th style='text-align: left;' colspan="2"><?php _e('مجموع المشتريات', 'woocommerce-pdf-invoices-packing-slips'); ?></th>
			
		</tr>
		<tr>
			<td colspan="2"><?= wc_price($order->get_shipping_total(), array('currency' => $order->get_currency())); ?></td>
			<th style='text-align: left;' colspan="2"><?php _e('الشحن', 'woocommerce-pdf-invoices-packing-slips'); ?></th>

		</tr>
		<tr>
			<td style='text-align: center;' colspan="2"><?= $order->get_payment_method_title(); ?></td>
			<th style='text-align: left;' colspan="2"><?php _e('طريقة الدفع', 'woocommerce-pdf-invoices-packing-slips'); ?></th>
		</tr>
		<tr>
			<td colspan="2" style="font-weight:bold"><?= wc_price($order->get_total(), array('currency' => $order->get_currency())); ?></td>
			<th style='text-align: left;' colspan="2"><?php _e('المجموع الكلي', 'woocommerce-pdf-invoices-packing-slips'); ?></th>
		</tr>
	</tfoot>

	<?php foreach ($order->get_items() as $item) {
		/* @var $item \WC_Order_item */
		$meta = $item['item_meta'];
		// $meta = array_filter($meta, function ($key) {
		// 	return !in_array($key, Order::getHiddenKeys());
		// }, ARRAY_FILTER_USE_KEY);
		?>
		<tbody>
		<tr>
			<?php
				$product = $item->get_product();
			?>
			<td colspan="2"
				rowspan="<?= count($meta) + 1; ?>"><?= wc_price($item->get_data()['total'], array('currency' => $order->get_currency())); ?></td>
			<td style='text-align: right;'><?= $item['name']; ?> &times; <?= $item['qty']; ?>
			<br>
				<?=$product->get_sku();?>
			</td>
			
			<td style='text-align: center;'> <?= $product->get_image(array(25,25));?></td>
		</tr>
		<?php $meta = array_map(function ($meta, $key) {
			$result = '<tr>';
			$result .= '<td>' . $key . '</td>';
			$result .= '<td>' . $meta . '</td>';
			$result .= '</tr>';
			return $result;
		}, $meta, array_keys($meta));
		echo implode(PHP_EOL, $meta);
		?>
	
		</tbody>
	<?php } ?>
	<?php foreach ($order->get_fees() as $fee) { ?>
			<tbody>
				<tr>
					<td colspan="2"><?= wc_price($fee->get_total(), array('currency' => $order->get_currency())); ?></td>
					<td ><?= $fee->get_name() ?></td>

				</tr>
			</tbody>
	<?php } ?>
</table>

<table class="customer_details">
	<tbody class="base">
		<tr>
			<th><?php _e('عنوان الشحن', 'woocommerce-pdf-invoices-packing-slips'); ?></th>
		</tr>
		<tr>
			<td style='text-align: right;'><?php echo $order->get_formatted_shipping_address(); ?></td>
		</tr>
	</tbody>
	<?php
	if (!empty($order->get_customer_note())): ?>
		<tbody class="notes">
		<tr>
			<th>
				<?php _e('ملاحظات الطلب', 'woocommerce-pdf-invoices-packing-slips'); ?>
			</th>
		</tr>
		<tr>
			<td>
				<?= $order->get_customer_note(); ?>
			</td>
		</tr>
		</tbody>
	<?php endif; ?>
</table>

	
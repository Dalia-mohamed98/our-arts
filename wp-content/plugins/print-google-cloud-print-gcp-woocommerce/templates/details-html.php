<?php namespace Zprint;
/* @var $order \WC_Order */
/* @var $location_data */
session_start();
?>
<html>
<head>
	<style><?php include 'style.php'; ?></style>
</head>
<body style="padding-top:30px">

<header>
	<?php if (get_appearance_setting('logo')) { ?>
		<img src="<?= get_appearance_setting('logo'); ?>" class="logo" alt="Logo">
	<?php } ?>
	
	<div style='text-align: right;padding-bottom:20px'>
		<!-- insert your custom barcode setting your data in the GET parameter "data" -->
		<img alt='Barcode Generator TEC-IT' style='width:30%'
			 src='https://barcode.tec-it.com/barcode.ashx?data=<?php echo $_SESSION['order_tracking_code'];?>&code=&multiplebarcodes=false&translate-esc=false&unit=Fit&dpi=96&imagetype=Gif&rotation=0&color=%23000000&bgcolor=%23ffffff&codepage=&qunit=Mm&quiet=0'/>
	</div>
	
	
</header>

<table class="customer_details" >
	<thead>
		<tr>
			<?php //if (get_appearance_setting('Company Name')) { ?>
			<th colspan="2"><?php _e( 'OUR ARTS', 'Print-Google-Cloud-Print-GCP-WooCommerce'); ?></th>	
			<th style='text-align: right;'><?php _e('اسم الشركة', 'Print-Google-Cloud-Print-GCP-WooCommerce'); ?></th>
			<?php //} ?>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="2">01001400776</td>
			<th style='text-align: right;'><?php _e('رقم الهاتف', 'Print-Google-Cloud-Print-GCP-WooCommerce'); ?></th>

		</tr>
		<tr>
			<td colspan="2">Smart Village , October City</td>
			<th style='text-align: right;'><?php _e('العنوان', 'Print-Google-Cloud-Print-GCP-WooCommerce'); ?></th>

		</tr>
		<tr>
			<td colspan="2"><?= sprintf('%s', $order->get_id()) ?></td>
			<th style='text-align: right;'><?php _e('رقم الطلب', 'Print-Google-Cloud-Print-GCP-WooCommerce'); ?></th>

		</tr>
		<tr>
			<td colspan="2"><?= date_i18n(\get_option('date_format', 'm/d/Y'), $order->get_date_created()); ?></td>
			<th style='text-align: right;'><?php _e('التاريخ', 'Print-Google-Cloud-Print-GCP-WooCommerce'); ?></th>

		</tr>

	</tfoot>


</table>

<?php if ($location_data['shipping']['delivery_pickup_type']) { ?>
	<h4><?= get_shipping_details($order); ?></h4>
<?php } ?>

	
<table class="customer_details" >
	<thead>
	<tr>
		
		<th colspan="2"><?php _e('الإجمالي', 'Print-Google-Cloud-Print-GCP-WooCommerce'); ?></th>
		<th ><?php _e('المنتج', 'Print-Google-Cloud-Print-GCP-WooCommerce'); ?></th>
		<th ><?php _e('صورة المنتج', 'Print-Google-Cloud-Print-GCP-WooCommerce'); ?></th>
	</tr>
	</thead>
	<tfoot style='text-align: left;'>
	<?php if ($location_data['total']['cost']) { ?>
		<tr>
			<td colspan="2"><?= $order->get_subtotal_to_display(); ?></td>
			<th colspan="2"><?php _e('مجموع المشتريات', 'Print-Google-Cloud-Print-GCP-WooCommerce'); ?></th>
			
		</tr>
	<?php } ?>
	<?php if ($location_data['shipping']['cost']) { ?>
		<tr>
			<td colspan="2"><?= wc_price($order->get_shipping_total(), array('currency' => $order->get_currency())); ?></td>
			<th colspan="2"><?php _e('الشحن', 'Print-Google-Cloud-Print-GCP-WooCommerce'); ?></th>

		</tr>
	<?php } ?>
	<?php if ($location_data['total']['cost']) { ?>
		<tr>
			<td style='text-align: center;' colspan="2"><?= $order->get_payment_method_title(); ?></td>
			<th colspan="2"><?php _e('طريقة الدفع', 'Print-Google-Cloud-Print-GCP-WooCommerce'); ?></th>
		</tr>
		<tr>
			<td colspan="2" style="font-weight:bold"><?= wc_price($order->get_total(), array('currency' => $order->get_currency())); ?></td>
			<th colspan="2"><?php _e('المجموع الكلي', 'Print-Google-Cloud-Print-GCP-WooCommerce'); ?></th>
		</tr>
	<?php } ?>
	</tfoot>
	<?php foreach ($order->get_items() as $item) {
		/* @var $item \WC_Order_item */
		$meta = $item['item_meta'];
		$meta = array_filter($meta, function ($key) {
			return !in_array($key, Order::getHiddenKeys());
		}, ARRAY_FILTER_USE_KEY);
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

<?php if ($location_data['shipping']['billing_shipping_details']) { ?>
	<!--<h2 class="caption"><?php //_e('Customer Details', 'Print-Google-Cloud-Print-GCP-WooCommerce'); ?></h2>-->
<?php } ?>

<table class="customer_details">
	<tbody class="base">
	<?php if ($location_data['shipping']['billing_shipping_details']) { ?>
		<!--<tr>
			<th><?php _e('Billing address', 'Print-Google-Cloud-Print-GCP-WooCommerce'); ?></th>
		</tr>
		<tr>
			<td>
				<?php //echo ($address = $order->get_formatted_billing_address()) ? $address : __('N/A', 'woocommerce'); ?>
				<?php //if ($order->get_billing_phone()) : ?>
					<br /><?php //echo esc_html($order->get_billing_phone()); ?>
				<?php //endif; ?>
				<?php //if ($order->get_billing_email()) : ?>
					<p><?php //echo esc_html($order->get_billing_email()); ?></p>
				<?php //endif; ?>
			</td>
		</tr>-->
	<?php } ?>
	<?php if ($location_data['shipping']['method'] && $shipping_method = $order->get_shipping_method()) { ?>
		<tr>
			<th><?php _e('Shipping method', 'Print-Google-Cloud-Print-GCP-WooCommerce'); ?></th>
		</tr>
		<tr>
			<td>
				<?= $shipping_method; ?>
			</td>
		</tr>
	<?php } ?>
	<?php if ($location_data['shipping']['billing_shipping_details'] && !wc_ship_to_billing_address_only() && $order->needs_shipping_address() && ($shipping = $order->get_formatted_shipping_address())) : ?>
		<tr>
			<th><?php _e('عنوان الشحن', 'Print-Google-Cloud-Print-GCP-WooCommerce'); ?></th>
		</tr>
		<tr>
			<td style='text-align: right;'><?php echo $shipping; ?></td>
		</tr>
	<?php endif; ?>
	</tbody>
	<?php
	if (!empty($order->get_customer_note())): ?>
		<tbody class="notes">
		<tr>
			<th>
				<?php _e('Order Notes', 'Print-Google-Cloud-Print-GCP-WooCommerce'); ?>
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
<footer>
	<?php if (get_appearance_setting('Footer Information #1')) { ?>
		<h4><?= get_appearance_setting('Footer Information #1'); ?></h4>
	<?php } ?>

	<?php if (get_appearance_setting('Footer Information #2')) { ?>
		<h5><?= get_appearance_setting('Footer Information #2'); ?></h5>
	<?php } ?>
</footer>
	
	
</body>
</html>

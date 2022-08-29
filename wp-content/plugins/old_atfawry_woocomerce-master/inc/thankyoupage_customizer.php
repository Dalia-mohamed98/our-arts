<?php
// add_action('wp_faw_scripts', 'ash2osh_faw_scripts');
// do_action('wp_faw_scripts');
add_filter('woocommerce_thankyou_order_received_text', 'ash2osh_faw_woo_change_order_received_text', 10, 2);

add_action('woocommerce-thankyou-order-processing', 'wdm_send_order_to_ext', 10, 2);//voo
// callback_handler();
/**
 * change the thank you text 
 *
 * @param  string $str 
 * @param  WC_Order $order
 */

// secure key : edfe8c8699cd4317b18169bd2106b796

function ash2osh_faw_woo_change_order_received_text($str, $order) {

	$products = json_decode(getProductsJson($order));
	
    //  $new_str = sprintf( esc_html__( 'Please Pay for the order using the below Button', 'ash2osh_faw' ), $count );
    if ($order->get_payment_method() == ASH2OSH_FAW_PAYMENT_METHOD && $order->get_status() == 'on-hold') {
        //if taken fawry number 
        if ($order->get_meta('_rec_faw_pay', TRUE) == 1) {
			
//             header( "refresh:5; url=http://our-arts.com" );
        	return $str;
        }

//continue 
    } else if($order->get_status() == 'processing')
		
		{//other payment methods or already paid

		$voo = do_action('woocommerce-thankyou-order-processing',$order,$products);
		
		header( "refresh:5; url=http://our-arts.com" );
        return $str;
    }
    $new_str = __('<h2>لإنهاء الطلب - يرجي الضغط علي الزر ادناه </h2>', 'ash2osh_faw');
    
//get the options //returns array 
    $options = get_option('woocommerce_' . ASH2OSH_FAW_PAYMENT_METHOD . '_settings');
	
	//var_dump($products);


    $expire_hours = $options['unpaid_expire'];
    if (!trim($expire_hours)) {
        $expire_hours = '48';
    }
     $new_str .= '<script> '

		.'var chargeRequest = {};'
		.'chargeRequest.language= "' . ( (strpos(get_locale(), 'ar') !== false) ? 'ar-eg' : 'en-gb') . '";'
		.'chargeRequest.merchantCode= \'Zgy+s91LCyG9rcmQouPl/w==\';'
		.'chargeRequest.merchantRefNumber= "' . $order->get_id() . '";'
		.'chargeRequest.customer = {};'
		.'chargeRequest.customer.name = "' . $order->get_billing_first_name() .' '. $order->get_billing_last_name() .'";'
		.'chargeRequest.customer.mobile = "' . $order->get_billing_phone() . '";'
		.'chargeRequest.customer.email =  "' . $order->get_billing_email() . '";'
		.'chargeRequest.customer.customerProfileId = \'\';'
		.'chargeRequest.order = {};' 
		.'chargeRequest.order.description = \'test bill inq\';'
		.'chargeRequest.order.expiry = "' . $expire_hours . '";'
		.'chargeRequest.order.orderItems = [];';
		 
		 $item_code=array();
		 foreach($products as $product){
			 array_push($item_code, $product->{'productSKU'});
			 $new_str .=
			'var item = {};'
			.'item.productSKU ="'. $product->{'productSKU'}.'";'
			.'item.description =" '. $product->{'description'}.'";'
			.'item.quantity = "'. $product->{'quantity'}. '";' 
			.'item.price = "'. $product->{'price'}. '";'
			.'item.width = "'. $product->{'width'}. '";'
			.'item.height =	"'. $product->{'height'}. '";'
			.'item.length =	"'. $product->{'length'}. '";'
			.'item.weight =	"'. $product->{'weight'}. '";'
			.'chargeRequest.order.orderItems.push(item);';
		 }
	
		sort($item_code);
		$sign = 'Zgy+s91LCyG9rcmQouPl/w=='.$order->get_id();
		$index=0;
		foreach($products as $product){
			foreach($item_code as $code){
				if($code == $product->{'productSKU'})
					{$sign.= $code.$product->{'quantity'}.$product->{'price'}.'00';
					 break;
					}
			}
			
		}
	
		$sign .= $expire_hours. '13ac36c6eef84b9e94642655b382e990';
		$new_str .=
		'chargeRequest.signature = "'. hash("sha256",$sign).'";'

		.'</script>';
	
	
	
    if (wp_get_theme() == 'Avada') {
        $new_str .= do_shortcode('[fusion_button link="#" text_transform="" title="" target="_self" link_attributes="" alignment="center" modal="" hide_on_mobile="small-visibility,medium-visibility,large-visibility" class="" id="faw_checkout" color="custom" button_gradient_top_color="#ffd205" button_gradient_bottom_color="#eac804" button_gradient_top_color_hover="#ffd205" button_gradient_bottom_color_hover="#ffd205" accent_color="" accent_hover_color="" type="3d" bevel_color="#049bce" border_width="" size="large" stretch="default" shape="pill" icon="" icon_position="left" icon_divider="no" animation_type="shake" animation_direction="left" animation_speed="0.3" animation_offset=""]'
                . '  <img  src="' . ASH2OSH_FAW_URL . 'images/logo_small.png">'
                . '[/fusion_button]');
    } else {
		
        $new_str .= '<input type="image" onclick="FawryPay.checkoutJS(chargeRequest,fawryCallbackFunction,requestCanceldCallBack);" src="https://www.atfawry.com/ECommercePlugin/resources/images/atfawry-ar-logo.png" alt="Edfa3 Fawry" id="faw_checkout"/>';
    
// 		$new_str .= '<br>' . '<button id="faw_checkout" style="background-color: #ffd205;border: 1px solid #e7bf08;">
//           <img  src="' . ASH2OSH_FAW_URL . 'images/logo_small.png"></button>';
 
	}


	
    return $new_str;
    //TODO send mail with payment url (just in case ??)
}

/**
 * return the products as JSON array
 * 
 * @param WC_Order $order
 */
function getProductsJson($order,$options) {
    $stupid_mode = $options['stupid_mode'];
    if($stupid_mode=='yes'){
         $arr[] = [
            'productSKU' => $order->get_id(),
            'description' => $order->get_id(),
            'quantity' => 1,
            'price' => $order->get_total()
        ];
    }else{
    $items=$order->get_items();
    $arr = [];
    foreach ($items as $item) {
        $data = $item->get_data();
    
		//print $item->get_sku();
		
        $arr[] = [
            "productSKU" => $data['product_id'],
            "description" => $data['name'],
            "quantity" => $data['quantity'],
            "price" => $data['total'] / $data['quantity'],
			"width" => "1",
			"height" => "2",
			"length" => "3",
     		"weight" => "600"
        ];
    }
        }
    return json_encode($arr);
}


//add fawry js
function ash2osh_faw_scripts() {
    $options = get_option('woocommerce_' . ASH2OSH_FAW_PAYMENT_METHOD . '_settings');
    $isStaging = $options['is_staging'] == 'no' ? FALSE : TRUE;
    $php_vars = array(
        'siteurl' => get_option('siteurl'),
        'ajaxurl' => admin_url('admin-ajax.php'),
    );

    if (is_page('إنهاء الطلب')) {
        if ($isStaging) {
//             wp_enqueue_script('fawry_js', 'https://atfawry.fawrystaging.com/ECommercePlugin/scripts/fawryPlugin.js');
           wp_enqueue_script('FawryPay', 'https://our-arts.com/wp-content/plugins/old_atfawry_woocomerce-master/scripts/fawry.js');
        } else {
            wp_enqueue_script('FawryPay', 'https://www.atfawry.com/ECommercePlugin/scripts/FawryPay.js');

        }

		wp_enqueue_script('Fawryjs', 'https://our-arts.com/wp-content/plugins/old_atfawry_woocomerce-master/scripts/fawry.js');
        wp_localize_script('Fawryjs', 'FAW_PHPVAR', $php_vars); //FAW_PHPVAR name must be unqiue

    }
}

add_action('wp_enqueue_scripts', 'ash2osh_faw_scripts');


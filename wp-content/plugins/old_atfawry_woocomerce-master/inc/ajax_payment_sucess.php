<?php

function ash2osh_faw_payment_recieved()
{
    $output = ['status' => 10];
	 //log the callback in the database
	global $wpdb;
	$res = $wpdb->replace(
		$wpdb->prefix . 'ash2osh_faw_callback_log', array(
			'data_rec' => json_encode($_REQUEST)
		), array(
			'%s',
		)
	);


	// handle callback
	$options = get_option('woocommerce_' . ASH2OSH_FAW_PAYMENT_METHOD . '_settings');

    $merchantRefNum = (isset($_POST['MerchantRefNo']) && $_POST['MerchantRefNo']) ? $_POST['MerchantRefNo'] : false;
	$FawryRefNo = $_POST['FawryRefNo'];
	$paid = (isset($_POST['paid']) && $_POST['paid']) ? $_POST['paid'] : false;
	$signature = $_POST['signature'];
    if ($merchantRefNum) {
        $order = wc_get_order($merchantRefNum);
        if ($order->get_user_id() === get_current_user_id()) {
            $order->update_meta_data('_rec_faw_pay', 1);
			if($paid === 'true')
				{$order->update_status('processing');}
			else 
				{wp_schedule_event(time(), 'hourly', 'paied_schedule');}
            $order->save();//dont forget
            $output = ['status' => 20];
        }
    }
	
    wp_send_json($output);
    wp_die();
}


function ash2osh_faw_payment_failed(){
	$merchantRefNum = (isset($_POST['MerchantRefNo']) && $_POST['MerchantRefNo']) ? $_POST['MerchantRefNo'] : false;
	if ($merchantRefNum) {
        $order = wc_get_order($merchantRefNum);
        if ($order->get_user_id() === get_current_user_id()) {
			$order->update_status('pending-payment');
		}
		$order->save();//dont forget
	}
	wp_die();
}

function ash2osh_faw_paied_schedule($merchantRefNum,$signature){
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => "http://www.atfawry.com/ECommerceWeb/Fawry/payments/status?merchantCode=IVCy5aZOPCF9vqwnm%20H0tw%3D%3D&merchantRefNumber=".$merchantRefNum."&signature=".$signature,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "GET",
	  CURLOPT_HTTPHEADER => array(
		"cache-control: no-cache",
		"postman-token: 6ca4965f-9691-04e1-3bb4-18d2e3319bdd"
	  ),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
	  echo "cURL Error #:" . $err;
	} else {
	  echo $response;
	}
	
}
add_action('paied_schedule', 'ash2osh_faw_paied_schedule',10,2);

add_action('wp_ajax_ash2osh_faw_payment_recieved', 'ash2osh_faw_payment_recieved');
add_action('wp_ajax_ash2osh_faw_payment_failed', 'ash2osh_faw_payment_failed');
add_action('wp_ajax_nopriv_ash2osh_faw_payment_recieved', 'ash2osh_faw_payment_recieved');//for guest checkout
add_action('wp_ajax_nopriv_ash2osh_faw_payment_failed', 'ash2osh_faw_payment_failed');//for guest checkout

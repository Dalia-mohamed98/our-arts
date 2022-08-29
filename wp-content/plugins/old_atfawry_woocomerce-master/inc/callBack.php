<?php


// ,\'https://our-arts.com/wp-content/plugins/atfawry_woocomerce-master/inc/callBack.php\'
// 
callback_handler();
header( "refresh:3; url=http://our-arts.com" );







function callback_handler() {
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

        $FawryRefNo = $_REQUEST['fawryRefNumber']; //internal to fawry
        $MerchantRefNo = $_REQUEST['merchantRefNumber'];
		$paymentMethod = $_REQUEST['paymentMethod'];
	
//         $OrderStatus = $_REQUEST['OrderStatus']; //New, PAID, CANCELED, DELIVERED, REFUNDED, EXPIRED
//         $Amount = $_REQUEST['Amount'];
//         $MessageSignature = $_REQUEST['MessageSignature'];

//echo $Amount;echo '-';echo $FawryRefNo ;echo '-';echo $MerchantRefNo;echo '-';echo $OrderStatus;echo '-';
        
//         $expected_signature = $this->generateSignature($FawryRefNo, $Amount, $MerchantRefNo, $OrderStatus);
        //echo $expected_signature;exit;
        //check signature
//         if (strtoupper($expected_signature) === strtoupper($MessageSignature)) {
            //get order
            $order = wc_get_order($MerchantRefNo);
            //check amount and  order status PAID
            if ($FawryRefNo && $paymentMethod == 'CARD') {
                $order->payment_complete();
                if (trim($options['order_complete_after_payment']) === 'yes') {
                    $order->update_status('Processing');
                }

                echo 'SUCCESS';
            } else {
                echo 'Fawry';
            }
//         } else {
//             echo 'INVALID_SIGNATURE';
//         }
        // echo ‘SUCCESS’, ‘FAILD’,‘INVALID_SIGNATURE’
        exit;
    }
?>
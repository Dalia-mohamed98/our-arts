<?php
    function updatePickupNumberDB($billnumber,$resp)
    {
        global $wpdb;
        $respons = array();
        $pickupres = json_decode($resp, true);
        if($pickupres['messageType'] == 'Error')
        {
            $output = 'Waybill Number #'.$billnumber.' : '.$pickupres['message'];
        }
        else
        {
           $pickupNumber = explode(".",$pickupres['message']);
           //print_r($pickupNumber[1]); 
           $updateQuery = $wpdb->update( 'wp_logixgridwaybill', array( 'pickup_number' => $pickupNumber[1]), array( 'waybill_number' => $billnumber));
			if( $updateQuery === false)
			{
				 $output = 'Waybill Number #'.$billnumber.': Not Created, Please try agin later';
			}
			else
			{
			    $output = 'Waybill Number #'.$billnumber.':'. $pickupres['message'];
			}
        }
        array_push($respons,$output);
        foreach($respons as $resp)
        {
            echo '<ul><li>'.$resp.'</li></ul>';
        }
    }
?>
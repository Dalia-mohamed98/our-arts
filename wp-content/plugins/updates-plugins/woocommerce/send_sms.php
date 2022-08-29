<?php
    
    require_once 'arabicdate.php';

    add_action( 'woocommerce_order_status_cancelled', 'send_cancelled_sms', 10, 1 );
    add_action( 'woocommerce_order_status_completed', 'send_completed_sms', 10, 1 );
    add_action( 'woocommerce_order_status_processing', 'send_processing_sms', 10, 1 );


    function send_processing_sms( $order_id ){
        
    	$date = date('Y-m-d'); // The Current Date
      	$check_off_days= date('D', strtotime($date));
        $three_days = '';
        $four_days = '';
		
        if($check_off_days == "Thu"){
            $three_days = date('Y-m-d', strtotime($date. ' + 6 days'));
            $four_days = date('Y-m-d', strtotime($date. ' + 5 days'));

        }
        else if($check_off_days == "Fri"){
            $three_days = date('Y-m-d', strtotime($date. ' + 5 days'));
            $four_days = date('Y-m-d', strtotime($date. ' + 6 days'));
            
        }
        else{
            $three_days = date('Y-m-d', strtotime($date. ' + 4 days'));
            $four_days = date('Y-m-d', strtotime($date. ' + 5 days'));

        }
        // echo ArabicDate($three_days).'          '.ArabicDate($four_days);
       
        $curl = curl_init();
        $processing = urlencode('شكرا لقد تم استلام طلبك رقم '. $order_id . '، وسيصلك خلال '.ArabicDate($three_days).' الى '.ArabicDate($four_days).'.');
        $url = "https://smsmisr.com/api/webapi/?username=Kucqb6oA&password=0CeG8jZ0R2&language=2&sender=Our%20Arts&mobile=".wc_get_order( $order_id )->get_billing_phone()."&message=".$processing;
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array('Content-Length: 0'),
        ));
        $response = curl_exec($curl);

        curl_close($curl);
        
    }

    function send_cancelled_sms( $order_id ){

        	$curl = curl_init();
			$cancelled = urlencode("نأسف لعدم اكمال الطلب رقم ". $order_id .",  لعدم توافر بعض المنتجات المطلوبة 
سعداء بالتعامل معكم ،، فريق أور آرتس");
			
			$url = "https://smsmisr.com/api/webapi/?username=Kucqb6oA&password=0CeG8jZ0R2&language=2&sender=Our%20Arts&mobile=".wc_get_order( $order_id )->get_billing_phone()."&message=".$cancelled;
			
			curl_setopt_array($curl, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_HTTPHEADER => array('Content-Length: 0'),
			));
			$response = curl_exec($curl);
			curl_close($curl);

    }
    
    
      function send_completed_sms( $order_id ){
            //send completed msg immediatly
        	$curl = curl_init();
			$completed = urlencode("تم تحضير طلبك رقم ". $order_id .", وسيقوم مندوبنا بالتواصل معك في اقرب وقت.
			فريق أور آرتس");
			
			$url = "https://smsmisr.com/api/webapi/?username=Kucqb6oA&password=0CeG8jZ0R2&language=2&sender=Our%20Arts&mobile=".wc_get_order( $order_id )->get_billing_phone()."&message=".$completed;
			
			curl_setopt_array($curl, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_HTTPHEADER => array('Content-Length: 0'),
			));
			$response = curl_exec($curl);
			curl_close($curl);
			
			 //send completed msg after seven days
			$curl = curl_init();
			$after_completed = urlencode("شكرا لطلبكم من أور آرتس،
	ساعدنا بإبداء رأيك في تقييم خدماتنا من خلال اللينك : https://our-arts.com/survey
	سيتم إرسال بروموكود خصم بعد إبداء رأيك
	فريق أور آرتس");
			$date = date('Y-m-d'); // The Current Date
			$time = date('Y-m-d', strtotime($date. ' + 7 days')).'-19-00';
			// echo $time;
			$url = "https://smsmisr.com/api/webapi/?username=Kucqb6oA&password=0CeG8jZ0R2&language=2&sender=Our%20Arts&mobile=".wc_get_order( $order_id )->get_billing_phone()."&message=".$after_completed."&DelayUntil=".$time;
			
			curl_setopt_array($curl, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_HTTPHEADER => array('Content-Length: 0'),
			));
			$response = curl_exec($curl);

			curl_close($curl);
			
			

    }

?>
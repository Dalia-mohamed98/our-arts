<?php
/**
 * Register Posts API
 *
 * @package REST API ENDPOINT
 */


class Rae_Register_Posts_Api {
	/**
	 * Rae_Register_Posts_Api constructor.
	 */
	function __construct() {
		add_action( 'rest_api_init', array( $this, 'rae_rest_posts_endpoints' ) );
	}

	/**
	 * Register posts endpoints.
	 */
	function rae_rest_posts_endpoints() {
		/**
		 * Handle Create Post request.
		 *
		 * This endpoint takes 'title', 'content' and 'user_id' in the body of the request.
		 * Returns the user object on success
		 * Also handles error by returning the relevant error if the fields are empty.
		 */
		register_rest_route( 'wp/v2/rae', '/post/setStatus', array(
			'methods' => 'POST',
			'callback' => array( $this, 'rae_rest_create_post_endpoint_handler' ),
		));
	}

	/**
	 * Creat Post call back.
	 *
	 * @param WP_REST_Request $request
	 */
	function rae_rest_create_post_endpoint_handler( WP_REST_Request $request ) {
		$status_list = array(
			'atorigin'        => __('wc-at-origin'),     
			'shipmentreceived'     => __('wc-shipment-received'), 
			'atwarehouse'           => __('wc-at-warehouse'),      
			'intransit'           => __('wc-in-transit'),
			'outfordelivery'        => __('wc-out-for-delivery'),     
			'delivered'     => __('wc-delivered'), 
			'rto'           => __('wc-rto'),      
			'rto-delivered'           => __('wc-rto-delivered'), 
			'undelivered'        => __('wc-undelivered'),     
			'refund'     => __('wc-refund'), 
			'refundmade'           => __('wc-refund-made'),      
			're-schedule'           => __('wc-re-schedule'),
			'schedulefordispatch'        => __('wc-schedule-for-dispatch'),     
			'deliveryschedule'     => __('wc-delivery-schedule'), 
			'partial-delivered'           => __('wc-partial-delivered') 
		);

		global $wpdb;
		$response = array();
		$parameters = $request->get_json_params();
		$updatedDate = date("Y/m/d h:i:a");
		$auth = $parameters['auth'];
		$i =0;
		if($auth == 'logixerp')
		{
			$waybilldetails = $parameters['waybilldetail'];
			foreach($waybilldetails as $waybilldetail)
			{	
				$i++;
				if (empty($waybilldetail['waybillnumber']) || empty($waybilldetail['status']) )
				{
					$error['message'] = 'Index #'.$i.' waybillnumber or status filed empty';
					return $error;
				}
				else
				{
					$waybillnumber = $waybilldetail['waybillnumber'];
					$status = $waybilldetail['status'];
					$statuslower = strtolower($status);
					echo $strstatus = str_replace(' ', '', $statuslower);
					echo $wp_status = $status_list[$strstatus];
					$remark = $waybilldetail['remark'];
					if($remark){ $remark_status = $remark; }
					else{ $remark_status = $status; }
					$waybilltable = "SELECT * FROM wp_logixgridwaybill WHERE waybill_number ='$waybillnumber' ";
					$waybillcheck = $wpdb->get_row($waybilltable);
					$waybilltable_status = unserialize($waybillcheck->waybill_status);
					array_push($waybilltable_status,$status);
					$waybilltable_remark = unserialize($waybillcheck->waybill_remark);
					array_push($waybilltable_remark,$remark_status);
					$main_status= serialize($waybilltable_status);
					$main_remark= serialize($waybilltable_remark);
					$order = new WC_Order($waybillcheck->wp_order_ID);
					$order->update_status($wp_status);
					$updateQuery = $wpdb->update( 'wp_logixgridwaybill', 
									array(
										'waybill_status' => $main_status,
										'waybill_remark' => $main_remark, 
										'waybill_updated' =>$updatedDate
									), 
									array('waybill_number' => $waybillnumber),
									array('%s','%s','%s'), 
									array( '%s' ) 
								);
											
					if( $updateQuery === false)
					{
						
						$error[$i] = 'waybillnumber #'.$waybilldetail['waybillnumber'].' status updated failed';
						//return $error;
					}
					else
					{
						$error[$i] = 'waybillnumber #'.$waybilldetail['waybillnumber'].' status updated successfully';
						//return $error;
					}
				}
			}
			//return $error;
		}
		else
		{
			$error['message'] = 'Authentication Failed';
			$error['status'] = '401';
		}
		array_push($response ,$error);
		return new WP_REST_Response($response);
	}
}

new Rae_Register_Posts_Api();

/*
-----------Sample Json----------
{
  "auth": "logixerp",
  "waybilldetail": [
    {
      "waybillnumber": "DELHI12548",
      "status": "Deliveried",
      "remark":"Remark"
    },
    {
      "waybillnumber": "DELHI12549",
      "status": "Reached",
      "remark":"Remark"
      
    },
    {
      "waybillnumber": "DELHI12550",
      "status": "Cancel",
      "remark":"Remark"
    },
    {
      "waybillnumber": "DELHI12551",
      "status": "Pending",
      "remark":"Remark"
    },
    {
      "waybillnumber": "dhl5",
      "status": "undeliveried",
      "remark":"Remark"
    }
  ]
}
-----------Sample Json----------
*/


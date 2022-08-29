<?php
function InsertDBWayBill($response,$orderID,$scode) 
{
    $respons = array();
    $response_data = json_decode($response);
    $array_status = array($response_data->status);
    $array_remark = array('Data received successfully');
    if($response_data->messageType == 'Error')
    {
      $output = 'Order #'.$orderID.' : '.$response_data->message;
    }
    else
    {
      $cretedDate = date("Y/m/d h:i:a");
      global $wpdb;
      //$wpdb->show_errors;
      $wpdb->insert(
        'wp_logixgridwaybill',
        array(
            'wp_order_ID' => $orderID,
            'waybill_number' => $response_data->waybillNumber,
            'waybill_file_name' => $response_data->labelURL,
            'waybill_status' => serialize($array_status),
            'waybill_remark' => serialize($array_remark),
            'waybill_created' => $cretedDate,
            'service_name'    => $scode
        ));
       //echo $wpdb->print_error();
       //echo $wpdb->last_query ;
     $meta_id = $wpdb->insert_id;
      if($meta_id)
      {
        $output = 'Order #'.$orderID.': WayBill Created Successfully';
      }
      else{
        $output = 'Order #'.$orderID.': Not Created, Please try agin later';
      }
  }
  array_push($respons,$output);
  foreach($respons as $resp)
        {
            echo '<ul><li>'.$resp.'</li></ul>';
        }
}
?>
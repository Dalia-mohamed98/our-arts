<?php
require plugin_dir_path( __FILE__ ) . 'createwaybill_action.php';
require plugin_dir_path( __FILE__ ) . 'createwaybill_action_db.php';
require plugin_dir_path( __FILE__ ) . 'calculate_traiff_action.php';
require plugin_dir_path( __FILE__ ) . 'pickuprequest_action.php';
require plugin_dir_path( __FILE__ ) . 'pickuprequest_db_action.php';
/*-------Create Waybill Start-------------*/
function example_ajax_request() {
    
    $orderDetails = $_REQUEST['orderdetails'];
    $orderids = $orderDetails[0];
    $serCode = $orderDetails[1];
    $cusCode = $orderDetails[2];
    $orderids_array = explode(",",$orderids);
    foreach($orderids_array as $orderid)
    {
        $oinfo = orderINFO($orderid,$serCode,$cusCode);
        //echo $oinfo;
        $db = InsertDBWayBill($oinfo,$orderid,$serCode);
        echo $db;
    }
die();
}
add_action( 'wp_ajax_example_ajax_request', 'example_ajax_request');
/*-------Create Waybill Start-------------*/

/*-------Get Waybil Detail Start-------------*/
add_action( 'wp_ajax_getHistory', 'getHistory');
function getHistory() {
    global $wpdb;
    $id = $_REQUEST['wbid'];
    $waybilltable = "SELECT * FROM wp_logixgridwaybill WHERE ID = '$id'"; 
    $waybillcheck = $wpdb->get_row($waybilltable);
    $status = unserialize($waybillcheck->waybill_status);
    $remarks = unserialize($waybillcheck->waybill_remark);
    $service = $waybillcheck->service_name;
    $pickup = $waybillcheck->pickup_number;
    if($pickup){ $pickupnumber = $pickup; }
    else { $pickupnumber = 'Pickup request not generated'; }
    $main_status =  array_reverse($status);
    $main_remarks =  array_reverse($remarks);
    /*echo '<label><strong>Waybill Number</strong><span>'.$waybillcheck->waybill_number.'</span></label>';
    echo '<label><strong>Waybill Date</strong><span>'.$waybillcheck->waybill_created.'</span></label>';
    echo '<label><strong>Service</strong><span>'.$service.'</span></label>';
    echo '<label><strong>Pickup Number</strong><span>'.$pickupnumber.'</span></label>';
    echo '<label><strong>Waybill Label</strong><span><a target="_blank" href="'.$waybillcheck->waybill_file_name.'">View Label</a></span></label>';*/
    echo '<div class="waybill_staus_remark"> <div class="waybill_stauts half_div"><ul class="waybill_stauts_ul"><li><h4>Status</h4></li>';
    foreach($main_status as $main_statu)
    {
        echo '<li>'.$main_statu.'</li>';
    }       
    echo '<ul></div><div class="waybill_remark half_div"><ul class="waybill_remark_ul"><li><h4>Remarks</h4>';
    foreach($main_remarks as $main_remark)
    {
        echo '<li>'.$main_remark.'</li>';
    } 
    echo '<ul></div><div style="clear:both"></div></div>';
}
/*-------Get Waybil Detail Finish-------------*/

/*-------Calculate Tariff Start-------------*/
add_action( 'wp_ajax_getCalculateTariff', 'getCalculateTariff');
function getCalculateTariff() {
    $scountry = $_REQUEST['scountry'];
    $sstate = $_REQUEST['sstate'];
    $scity = $_REQUEST['scity'];
    $szip = $_REQUEST['szip'];
    $dcountry = $_REQUEST['dcountry'];
    $dstate = $_REQUEST['dstate'];
    $dcity = $_REQUEST['dcity'];
    $dzip = $_REQUEST['dzip'];
    $pservices = $_REQUEST['pservices'];
    $ppackages = $_REQUEST['ppackages'];
    $pweight = $_REQUEST['pweight'];
    $calResp = caltraiff($scountry,$sstate,$scity,$szip,$dcountry,$dstate,$dcity,$dzip,$pservices,$ppackages,$pweight);
    $main_cal_res = json_decode($calResp);
    if($main_cal_res->messageType == 'Error')
    {
        echo '<div class="calculate_div"><h3 style="font-size: 25px;">'.$main_cal_res->messageType.'</h3><p class="error_message">'.$main_cal_res->message.'</p></div>';
    }
    else
    {
        $total_tax_amount = $main_cal_res->totalAmount + $main_cal_res->taxAmount;
        echo '<div class="calculate_div"><h3 style="font-size: 25px;">Tariff Details</h3>';
        echo '<div class="list_1 lists"><div class="label_name">Amount</div>';
        echo '<div class="label_value">'.$main_cal_res->totalAmount.'</div></div>';
        echo '<div class="list_2 lists"><div class="label_name">Tax</div>';
        echo '<div class="label_value">'.$main_cal_res->taxAmount.'</div></div>';
        echo '<div class="list_3 lists maintotal"><div class="label_name">Total Amount</div>';
        echo '<div class="label_value">'.$total_tax_amount.'</div></div></div>';
    }
}

/*------Calculate Tariff Start-------------*/

/*------Pickup Request Start-------------*/
add_action( 'wp_ajax_getpickupRequest', 'getpickupRequest');
function getpickupRequest() 
{
    $readytime = $_REQUEST['readytime'].':00';
    $latesttimeAvailable = $_REQUEST['latesttimeAvailable'].':00';
    $pickupcountry = $_REQUEST['pickupcountry'];
    $pickupstate = $_REQUEST['pickupstate'];
    $pickupcity = $_REQUEST['pickupcity'];
    $pickupaddress = $_REQUEST['pickupaddress'];
    $pickupzipcode = $_REQUEST['pickupzipcode'];
    $pickupdate = $_REQUEST['pickupdate'];
    $clientcode = $_REQUEST['clientcode'];
    $Waybillnumbers = $_REQUEST['Waybillnumbers'];
    $pickuptype = $_REQUEST['pickuptype'];
    $specialinstruction = $_REQUEST['specialinstruction'];
    $waybill_lists = explode(',', $Waybillnumbers);
    foreach($waybill_lists as $waybill_list)
    {
       $apiRes = createpickup($readytime,$latesttimeAvailable,$pickupcountry,$pickupstate,$pickupcity,$pickupaddress,$pickupzipcode,$pickupdate,$clientcode,$waybill_list,$pickuptype,$specialinstruction);
       //print_r($apiRes);
       $pickupDB = updatePickupNumberDB($waybill_list,$apiRes);
       echo $pickupDB;
    }  
}
/*------Pickup Request Finish-------------*/

/*------City Request Start-------------*/
add_action( 'wp_ajax_getCityName', 'getCityName');
function getCityName() 
{
   include 'getStates.php';
   $stateCode = $_REQUEST['statecode'];
   foreach($RespStates as $RespState)
    {   
        $code =  $RespState['code'];
        if($code == $stateCode)
        {
            $city = $RespState['cities'];
            $cityNames = explode(",",$city);
            $citySortNames = sort($cityNames);
            //print_r($cityNames);
            foreach($cityNames as $cityName)
            {
                echo '<option value="'.$cityName.'">'.$cityName.'</option>';
            }
        }                  
    }
}
/*------City Request Finish-------------*/

/*------Manual Create Waybill Request Start-------------*/
add_action( 'wp_ajax_createManualWaybill', 'createManualWaybill');
function createManualWaybill() 
{
   $basicDetails = $_REQUEST['basicDetail'];
   $shipperDetails = $_REQUEST['shipperDetail'];
   $consigneeDetails = $_REQUEST['consigneeDetail'];
   $packageDetails = $_REQUEST['packageDetails'];
   include 'createManualWaybill_action.php';
   //print_r($basicDetails);

}
/*------Manual Create Waybill Request Finish-------------*/

?>

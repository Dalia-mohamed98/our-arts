<?php

global $wpdb;

$settingAtrs = $wpdb->get_row( "SELECT * FROM wp_logixgridsetting WHERE ID = 1");

$aKey = $settingAtrs->accessKey;

$sKey = $settingAtrs->secureKey;

$scountrt = $settingAtrs->sourceCountry;

include "config.php";

$urls = $apiURL."/Rachna/webservice/v2/GetGeoLocation?secureKey=9BA05777B57441AA9DCFCA33781332B8&countryCode=".$scountrt;

$curls = curl_init();

curl_setopt_array($curls, array(CURLOPT_RETURNTRANSFER => 1,CURLOPT_URL => "$urls", CURLOPT_USERAGENT => 'Codular Sample cURL Request'));

curl_setopt($curls, CURLOPT_FOLLOWLOCATION, true);

curl_setopt($curls, CURLOPT_CUSTOMREQUEST, "GET");

$resp = curl_exec($curls);

$err = curl_error($curls);

curl_close($curls);

if ($err) {

    $respStateCity = $err;

} else {

    $res = json_decode($resp, true);

    if($res['messageType'] == 'Error')

    {

       $RespStates = $res['message'];

    }

    else{

        $RespStates = $res['states'];

    }



}



?>
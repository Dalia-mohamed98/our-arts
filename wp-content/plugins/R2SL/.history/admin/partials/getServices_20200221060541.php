<?php
function getServices()
{
    global $wpdb;
    $settingAtrs = $wpdb->get_row( "SELECT * FROM wp_logixgridsetting WHERE ID = 1");
    $cusmCode = $settingAtrs->customerCode;
    $sKey = $settingAtrs->secureKey;
    $aKey = $settingAtrs->accessKey;
    $apiURL = $settingAtrs->apiURL;
    $urls = $apiURL."/Rachna/webservice/v2/ActiveServices?secureKey=".$sKey."&customerCode=".$cusmCode;
    $curls = curl_init();
    curl_setopt_array($curls, array(CURLOPT_RETURNTRANSFER => 1,CURLOPT_URL => "$urls", CURLOPT_USERAGENT => 'Codular Sample cURL Request'));
    curl_setopt($curls, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curls, CURLOPT_CUSTOMREQUEST, "GET");
    $response = curl_exec($curls);
    $err = curl_error($curls);
    curl_close($curls);


    if ($err) {
    echo "cURL Error #:" . $err;
    } else {
        $res = json_decode($response, true);
        return $res['services'];
    }
}

?>
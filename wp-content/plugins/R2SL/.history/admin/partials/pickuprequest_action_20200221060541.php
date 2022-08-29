<?php

function createpickup($rt,$lat,$pco,$ps,$pci,$pa,$pz,$pd,$cc,$wn,$pt,$spe)
{
    global $wpdb;
    $settingAtrs = $wpdb->get_row( "SELECT * FROM wp_logixgridsetting WHERE ID = 1");
    $apiURL = $settingAtrs->apiURL;
    $data_post = 'readyTime='.$rt.'&latestTimeAvailable='.$lat.'&pickupCity='.$pci.'&pickupAddress='.$pa.'&pickupCountry='.$pco.'&pickupState='.$ps.'&pickupPincode='.$pz.'&pickupDate='.$pd.'&clientCode='.$cc.'&wayBillNumbers='.$wn.'&specialInstruction='.$spe.'&pickupType='.$pt;
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => $apiURL."/Rachna/webservice/v2/CreatePickupRequest?secureKey=".$settingAtrs->secureKey,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"readyTime\"\r\n\r\n$rt\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"latestTimeAvailable\"\r\n\r\n$lat\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"pickupCity\"\r\n\r\n$pci\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"pickupAddress\"\r\n\r\n$pa\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"pickupCountry\"\r\n\r\n$pco\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"pickupState\"\r\n\r\n$ps\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"pickupPincode\"\r\n\r\n$pp\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"pickupDate\"\r\n\r\n$pd\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"clientCode\"\r\n\r\n$cc\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"wayBillNumbers\"\r\n\r\n$wn\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"specialInstruction\"\r\n\r\n$spe\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"pickupType\"\r\n\r\n$pt\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
    CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
    ),
    ));
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    return $err;
} else {
  return $response;
}
 
}

?>
<?php
function caltraiff($sco,$sst,$sci,$szi,$dco,$dst,$dci,$dzi,$pse,$ppa,$pwe)
{
    global $wpdb;
    $settingAtrs = $wpdb->get_row( "SELECT * FROM wp_logixgridsetting WHERE ID = 1");
    $apiURL = $settingAtrs->apiURL;
    $taffic_array = array (
        'calculateTariffRequestData' => 
        array (
          'customerCode' => $settingAtrs->customerCode,
          'sourceCity' => $sci,
          'sourceState' => $sst,
          'sourcePincode' => $szi,
          'sourceCountry' => $sco,
          'destinationCity' => $dci,
          'destinationState' => $dst,
          'destinationPincode' => $dzi,
          'destinationCountry' => $dco,
          'service' => $pse,
          'packages' => $ppa,
          'actualWeight' => $pwe,
          'length' => '',
          'width' => '',
          'height' => '',
        ),
    );
    

    $data_post= json_encode($taffic_array);
    $url = $apiURL.'/webservice/v2/CalculateTariff?secureKey='.$settingAtrs->secureKey; 
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_post); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','AccessKey:'.$settingAtrs->accessKey));
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

?>
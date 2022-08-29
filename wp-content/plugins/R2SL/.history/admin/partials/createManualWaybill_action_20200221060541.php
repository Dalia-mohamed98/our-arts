<?php

global $wpdb;
$settingAtrs = $wpdb->get_row( "SELECT * FROM wp_logixgridsetting WHERE ID = 1");
$apiURL = $settingAtrs->apiURL;
$packageitems = array();
$packagecount = array();
$weight = array();
foreach($packageDetails as $packageDetail)
{
  if($packageDetail[0] == '' || $packageDetail[1] == '' || $packageDetail[2] == '' || $packageDetail[3] == '' || $packageDetail[4] == '' || $packageDetail[5] == '' || $packageDetail[6] == '' )
  {
    $packageitems['error'] = 'Index #'.$packageDetail[7].". Please fill all the package details."; 
    break;
  }
  else
  {
    $packagrlist['barCode'] = ' ';
    $packagrlist['packageCount'] = $packageDetail[0];
    $packagrlist['lengths'] = $packageDetail[1];
    $packagrlist['width'] = $packageDetail[2];
    $packagrlist['height'] = $packageDetail[3];
    $packagrlist['weight'] = $packageDetail[4];
    $packagrlist['chargedWeight'] = $packageDetail[5];
    $packagrlist['selectedPackageTypeCode'] = $packageDetail[6];
    array_push($packagecount,$packageDetail[0]);
    array_push($weight,$packageDetail[5]);
    array_push($packageitems,$packagrlist);
  }  
}

if (array_key_exists("error",$packageitems))
{
  $output['message']= $packageitems['error'];
  $output['status'] = 'error';
  echo json_encode($output);
  exit(0);
}
else
{
    $numberofpackages = array_sum($packagecount);
    $chargedweight = array_sum($weight);
    $WayBillDetails=  Array (
        'waybillRequestData' => 
        Array (
          'FromOU' => '',
          'DeliveryDate' => '',
          'WaybillNumber' => '',
          'CustomerCode' => $settingAtrs->customerCode,
          'CustomerName' => $shipperDetails[1],
          'CustomerAddress' => $shipperDetails[0].', '.$shipperDetails[2].', '.$shipperDetails[3],
          'CustomerCity' => $shipperDetails[8],
          'CustomerCountry' => $shipperDetails[6],
          'CustomerPhone' => $shipperDetails[4],
          'CustomerState' => $shipperDetails[7],
          'CustomerPincode' => $shipperDetails[9],
          'ConsigneeCode' => '00000',
          'ConsigneeName' => $consigneeDetails[1],
          'ConsigneePhone' => $consigneeDetails[4],
          'ConsigneeAddress' => $consigneeDetails[0].', '.$consigneeDetails[2].', '.$consigneeDetails[3],
          'ConsigneeCountry' => $consigneeDetails[6],
          'ConsigneeState' => $consigneeDetails[7],
          'ConsigneeCity' => $consigneeDetails[8],
          'ConsigneePincode' => $consigneeDetails[9],
          'ConsigneeWhat3Words' => 'word.exact.replace',
          'StartLocation' => '',
          'EndLocation' => '',
          'ClientCode' => '',
          'NumberOfPackages' => $numberofpackages,
          'ActualWeight' => $chargedweight,
          'ChargedWeight' => $chargedweight,
          'CargoValue' => '',
          'ReferenceNumber' => $basicDetails[3],
          'InvoiceNumber' => $basicDetails[2],
          'PaymentMode' => 'PAID',
          'ServiceCode' => $basicDetails[0],
          'WeightUnitType' => 'KILOGRAM',
          'Description' => $basicDetails[5],
          'COD' => $basicDetails[4],
          'CODPaymentMode' => '',
          'PackageDetails' => Array (
          'packageJsonString' => $packageitems
        ),
        ),
    ); 

    $data_post= json_encode($WayBillDetails);
    $url = $apiURL.'/webservice/v2/CreateWaybill?secureKey='.$settingAtrs->secureKey; 
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_post); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','AccessKey:'.$settingAtrs->accessKey));
    $result = curl_exec($ch);
    curl_close($ch);
    $data_resp= json_decode($result);
    if($data_resp->messageType == 'Error')
    {
      //echo '<p style="color:black">'.$data_resp->message.'</p>';
      $output['message']= $data_resp->message;
      $output['status'] = 'error';
      echo json_encode($output);
      exit(0);
    }
    else
    {
      $output['message']= 'WayBill Created Successfully. Number "'.$data_resp->waybillNumber;
      $output['status'] = 'sucess';
      echo json_encode($output);
      exit(0);
    }
   
}

?>
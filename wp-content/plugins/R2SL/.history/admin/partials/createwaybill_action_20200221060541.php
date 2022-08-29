<?php

function orderINFO($oid,$scode,$cc) {

  global $wpdb;

  $settingAtrs = $wpdb->get_row( "SELECT * FROM wp_logixgridsetting WHERE ID = 1");

  $apiURL = $settingAtrs->apiURL;

  $order = wc_get_order($oid);

  $items = $order->get_items();

  $name = get_post_meta( $oid, '_billing_first_name', true ).' '.get_post_meta( $oid, '_billing_last_name', true );

  $address = get_post_meta( $oid, '_billing_address_1', true ).' , '.get_post_meta( $oid, '_billing_address_2', true );

  $country = get_post_meta( $oid, '_billing_country', true );

  $state = get_post_meta( $oid, '_billing_state', true );

  $city = get_post_meta( $oid, '_billing_city', true );

  $pincode = get_post_meta( $oid, '_billing_postcode', true );

  $phone = get_post_meta( $oid, '_billing_phone', true );

  $pincode = get_post_meta( $oid, '_billing_postcode', true );

  $packageCount = count($items);

  $totalamount = get_post_meta( $oid, '_order_total', true );

  $paymentmethod = get_post_meta( $oid, '_payment_method', true );

  $array_data = Array

    ( 

  'waybillRequestData' => Array

        (

            'FromOU' => $state,

            'WaybillNumber' => '',

            'DeliveryDate' =>  '',

            'CustomerCode' => $cc,

            'ConsigneeCode' => '',

            'ConsigneeAddress' => $address,

            'ConsigneeCountry' => $country,

            'ConsigneeState' => $state,

            'ConsigneeCity' => $city,

            'ConsigneePincode' => $pincode,

            'ConsigneeName' => $name,

            'ConsigneePhone' => $phone,

            'ClientCode' => $settingAtrs->customerCode,

            'NumberOfPackages' => $packageCount,

            'ActualWeight' => '0',

            'ChargedWeight' => '1',

            'CargoValue' => '',

            'ReferenceNumber' => '9999999999',

            'InvoiceNumber' => '',

            'PaymentMode' => 'TBB' ,

            'ServiceCode' => $scode,

            'reverseLogisticActivity' => '',

            'reverseLogisticRefundAmount' => '',

            'WeightUnitType' => 'KILOGRAM',

            'Description' => 'VZXC',

            'COD' => $totalamount,

            'CODPaymentMode' => $paymentmethod,

            'DutyPaidBy' => 'Receiver',

            'packageDetails' => '',

            'CreateWaybillWithoutStock' =>'false'

        ));

        

        $data_post= json_encode($array_data);

        $url = $apiURL.'/Rachna/webservice/v2/CreateWaybill?secureKey='.$settingAtrs->secureKey; 

        // Prepare new cURL resource

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_post); 

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','AccessKey:'.$settingAtrs->accessKey));

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;

       

        /*foreach ( $items as $item_id => $item )

        {

        $product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();

        weight: '.get_post_meta( $product_id, '_weight', true ). ', _length: '.get_post_meta( $product_id, '_length', true ).  ', _width: '.get_post_meta( $product_id, '_width', true ). ', _height: '.get_post_meta( $product_id, '_height', true );

        }*/

}

?>
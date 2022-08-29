<?php
class PayHelper
{
    const HOST = "https://accept.paymobsolutions.com/api/";

    public function __construct(WC_Order $order, Array $config)
    {
        $this->order                = $order;
        $this->auth_token           = $this->requestToken($config['api_key']);
        $this->debug                = $config['can_debug'];
        $this->integration_id       = $config['integration_id'];
        $this->integration_currency = $config['integration_currency'];
        $this->has_items            = $config['has_items'];
        $this->has_delivery         = $config['has_delivery'];
        $this->handles_shipping     = $config['handles_shipping'];
        $this->gateway_code         = $config['gateway_code'];
        $this->processOrderData();
    }

    public function isValid()
    {
        return $this->is_valid;
    }

    public function getToken()
    {
        return $this->auth_token;
    }

    public function processOrderData()
    {
        try {
            $order = $this->order;
            $shipping_fees = $order->get_total_shipping() + $order->get_shipping_tax();
            $this->order_unique_id = $order->get_id() .'_'. time();
            $this->order_currency  = $order->get_currency();    
            $this->cents = 100;
    
            if($this->handles_shipping)
            {
                $this->amount_cents = $order->get_total() * $this->cents;
            }else{
                $this->amount_cents = ($order->get_total() - $shipping_fees) * $this->cents;
            }
    
            if($this->has_items)
            {
                foreach($order->get_items() as $item)
                {
                    $this->order_items[] = [
                        "name" => $item['name'],
                        "amount_cents" => intval($item['total'] * $this->cents)
                    ];
                }
            }
    
            $this->shipping = [
                "email"           => (get_post_meta($order->get_id(), '_shipping_email', true)) ? get_post_meta($order->get_id(), '_shipping_email', true) : "NA",
                "phone_number"    => (get_post_meta($order->get_id(), '_shipping_phone', true)) ? get_post_meta($order->get_id(), '_shipping_phone', true) : "NA",
                "first_name"      => ($order->get_shipping_first_name()) ? $order->get_shipping_first_name() : "NA",
                "last_name"       => ($order->get_shipping_last_name()) ? $order->get_shipping_last_name() : "NA",
                "street"          => $order->get_shipping_address_1() . ' - ' . $order->get_shipping_address_2(),
                "postal_code"     => ($order->get_shipping_postcode()) ? $order->get_shipping_postcode() : "NA",
                "city"            => ($order->get_shipping_city()) ? $order->get_shipping_city() : "NA",
                "country"         => ($order->get_shipping_country()) ? $order->get_shipping_country() : "NA",
                "state"           => ($order->get_shipping_state()) ? $order->get_shipping_state() : "NA",
                "shipping_method" => "PKG",
                "building"        => "NA",
                "apartment"       => "NA",
                "floor"           => "NA",
            ];
    
            $this->billing = [
                "email"           => $order->get_billing_email(),
                "first_name"      => $order->get_billing_first_name(),
                "last_name"       => $order->get_billing_last_name(),
                "street"          => $order->get_billing_address_1() . ' - ' . $order->get_billing_address_2(),
                "phone_number"    => $order->get_billing_phone(),
                "city"            => $order->get_billing_city(),
                "country"         => $order->get_billing_country(),
                "state"           => ($order->get_billing_state()) ? $order->get_billing_state() : "NA",
                "postal_code"     => ($order->get_billing_postcode()) ? $order->get_billing_postcode() : "NA",
                "apartment"       => "NA",
                "floor"           => "NA",
                "shipping_method" => "PKG",
                "building"        => "NA",
            ];

            $this->processStoreConfig();

        } catch (\Exception $error) {
            throw new \Exception($error->getMessage());
        }

    }

    public function processStoreConfig()
    {
        $valid = true;
        $reasons = array();

        if ( !version_compare(PHP_VERSION, '5.4.0', '>=') )
        {
            $valid = false;
            $reasons[] = "Required php version >= 5.4.0, your PHP version is: " . PHP_VERSION;
        }

        if ( !extension_loaded('curl'))
        {
            $valid = false;
            $reasons[] = "Required php extension: cURL, this extension is not enabled on this server.";
        }

        $integration = $this->HttpGet("ecommerce/integrations/$this->integration_id");
        if ( !$integration || $integration->gateway_type !== $this->gateway_code || $integration->currency !== $this->order_currency)
        {
            $valid = false;
            $reasons[] = "You are using a wrong integration id, please double check..";
        }

        $this->error = $reasons;
        $this->is_valid = $valid;
    }

    public function processOrder()
    {
        global $woocommerce;
        $this->order->update_status('pending-payment');
        $this->order->add_order_note('(Awaiting Payment)');
        $this->order->save();
        $woocommerce->cart->empty_cart();
    }

    public function getError()
    {
        $error = "";

        if (is_string($this->error) && $this->error != '')
        {
            $error .= "<li>$this->error</li>";

        } else if (is_array($this->error) && !empty($this->error)) {
            foreach ($this->error as $key => $data) {
                if(is_array($data))
                {
                    $hints  = "";
                    $field = "$key: ";
                    foreach ($data as $text)
                    {
                        $hints .= " $text ";
                    }
                }else{
                    $field = "";
                    $hints = $data;
                }
            
                $error .= "<li>$field$hints</li>";
            }
        }
        
        return $error;
    }

    public function throwErrors($error)
    {
        if( $this->debug )
        {
            global $woocommerce;
            global $wp_version;
            $version = ACCEPT_PLUGIN_VERSION;

            echo "<!-- // <WP-VERSION> $wp_version // <WC-VERSION> $woocommerce->version // <AP-VERSION> $version // -->\n";
            echo "<!-- // <LAST_STATUS_CODE> $this->response_status_code // <LAST_RAW_RESPONSE> $this->response_raw_data // --> \n";
        }

        if( isset($_REQUEST['pay_for_order']) && $_REQUEST['pay_for_order'] === "true" ){
            wc_add_notice($error . $this->getError(), 'error');
        }else{
            throw new Exception($error . $this->getError());
        }
    }

    public function requestToken($api_key)
    {
        $data = [
            'api_key' => $api_key
        ];

        $request = $this->HttpPost('auth/tokens', $data);

        if ($request){
            if( isset($request->token) )
            {
                $this->merchant = $request->profile->id;
                return $request->token;
            }
        }
        
        return false;
    }

    public function requestOrder()
    {
        $data = [
            "delivery_needed"   => $this->has_delivery,
            "merchant_id"       => $this->merchant,
            "amount_cents"      => $this->amount_cents,
            "currency"          => $this->order_currency,
            "merchant_order_id" => $this->order_unique_id,
            "items"             => ($this->order_items) ? $this->order_items : [],
            "shipping_data"     => $this->shipping,
        ];

        $order = $this->HttpPost("ecommerce/orders", $data);

        if( $order ){
            if( isset($order->id) )
            {
                return $order->id;
            }
        }

        return false;
    }

    public function requestPaymentKey($order_id, $saved_card = false)
    {
        $data = [
            "amount_cents"   => $this->amount_cents,
            "expiration"     => 36000,
            "order_id"       => $order_id,
            "billing_data"   => $this->billing,
            "currency"       => $this->order_currency,
            "integration_id" => $this->integration_id,
        ];

        if ($saved_card) 
        {
            $data['token'] = $saved_card;
        }

        $payment = $this->HttpPost("acceptance/payment_keys", $data);

        if ($payment){
            if( isset($payment->token) )
            {
                $this->payment_token = $payment->token;
                return $payment->token;
            }
        }

        return false;
    }

    public function requestKioskId()
    {
        $data = [
            "source" => ["identifier"=>"AGGREGATOR", "subtype"=>"AGGREGATOR"],
            "billing" => $this->billing,
            "payment_token" => $this->payment_token,
        ];

        $request = $this->HttpPost("acceptance/payments/pay", $data);

        if ($request){
            if( isset($request->pending) && $request->pending )
            {
                if( isset($request->data->bill_reference) )
                {
                    return $request->data->bill_reference;
                }
            }
        }

        return false;
    }

    public function requestWalletUrl($phone_number)
    {
        $data = [
            "source"        => ["identifier"=> $phone_number, "subtype"=>"WALLET"],
            "billing"       => $this->billing,
            "payment_token" => $this->payment_token,
        ];

        $request = $this->HttpPost("acceptance/payments/pay", $data);

        if ($request) {
            if( isset($request->redirect_url) )
            {
                return $request->redirect_url;
            }
        }

        return false;
    }
    
    private function HttpPost($url_path, $data = [])
    {
        $ch  = curl_init();
        $target = self::HOST.$url_path;
        $options = [
            CURLOPT_URL => $target,
            CURLOPT_HTTPHEADER => ["Content-Type: application/json", "Authorization: Bearer $this->auth_token"],
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data)
        ];

        curl_setopt_array($ch, $options);
        $this->response_raw_data  = curl_exec($ch);
        $response = $this->response_raw_data;
        $this->response_json_data = json_decode($response);
        $this->response_status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->error = curl_error($ch);

        if ( !in_array($this->response_status_code, [200, 201]) )
        {
            $this->error = json_decode($response, true);
        }

        curl_close($ch);
        if($this->response_json_data){
            return $this->response_json_data;
        }

        return false;
    }

    private function HttpGet($url_path)
    {
        $ch  = curl_init();
        $target = self::HOST.$url_path;
        $options = [
            CURLOPT_URL => $target,
            CURLOPT_HTTPHEADER => ["Content-Type: application/json", "Authorization: Bearer $this->auth_token"],
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
        ];

        curl_setopt_array($ch, $options);
        $this->response_raw_data  = curl_exec($ch);
        $response = $this->response_raw_data;
        $this->response_json_data = json_decode($response);
        $this->response_status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->error = curl_error($ch);

        if ( !in_array($this->response_status_code, [200, 201]) )
        {
            $this->error = json_decode($response, true);
        }

        curl_close($ch);
        if($this->response_json_data){
            return $this->response_json_data;
        }

        return false;
    }

}

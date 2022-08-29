<?php

class Class_Pi_Edd_Shipping{

    public $plugin_name;

    private $setting = array();

    private $active_tab;

    private $this_tab = 'default';

    private $tab_name = "Shipping days";

    private $setting_key = 'shipping_setting';


    function __construct($plugin_name){
        $this->plugin_name = $plugin_name;
        
        $this->tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );
        $this->active_tab = $this->tab != "" ? $this->tab : 'default';

        add_action('woocommerce_init', array($this,'shipping_zone_to_array'));
        

        if($this->this_tab == $this->active_tab){
            add_action($this->plugin_name.'_tab_content', array($this,'tab_content'));
        }

        
        add_action($this->plugin_name.'_tab', array($this,'tab'),1);

        $this->settings = array(
            
            array('field'=>'pi_edd_shipping')
            
        );
        $this->register_settings();
        
        if(PISOL_EDD_DELETE_SETTING){
            $this->delete_settings();
        }   

        add_action('wp_ajax_pisol_update_method', array($this, 'update_method'));

    }

   
    
    function delete_settings(){
        foreach($this->settings as $setting){
            delete_option( $setting['field'] );
        }
    }


    function register_settings(){   

        foreach($this->settings as $setting){
            register_setting( $this->setting_key, $setting['field']);
        }
    
    }

    function tab(){
        ?>
        <a class=" px-3 text-light d-flex align-items-center  border-left border-right  <?php echo ($this->active_tab == $this->this_tab ? 'bg-primary' : 'bg-secondary'); ?>" href="<?php echo admin_url( 'admin.php?page='.sanitize_text_field($_GET['page']).'&tab='.$this->this_tab ); ?>">
            <?php _e( $this->tab_name, 'http2-push-content' ); ?> 
        </a>
        <?php
    }

    function tab_content(){
        ?>
         <div class="alert alert-info mt-3">
            <strong>Set Minimum and Maximum Shipping days for each of the Shipping methods, in each of the Shipping Zones<br> Once done this go to Basic setting and set "<u>Default shipping zone</u>" without this it wont show estimate</strong>
         </div>
         
         <div class="pisol-error mt-3">

         </div>
            <div class="row px-3">
                <div class="col-12 col-sm-4 ">
                    <h2 class="block p-2 bg-dark text-light text-center">Step 1<hr> Select a shipping zone to edit</h2>
                    <?php $this->shippingZones(); ?>
                </div>
                <div class="col-12 col-sm-4 ">
                    <h2 class="block p-2 bg-dark text-light text-center">Step 2<hr> Select a shipping method to edit</h2>
                    <?php $this->shippingMethods(); ?>
                </div>
                <div class="col-12 col-sm-4 ">
                    <h2 class="block p-2 bg-dark text-light text-center">Step 3<hr> Set Min and Max Shipping days</h2>
                    <div class="shadow p-2">
                    <form id="pisol-min-max-form" style="display:none;">
                    <div class="form-group row align-items-center">
                        <label class="col-sm-6 col-form-label">Minimum shipping days</label>
                        <div class="col-sm-6">
                        <input required type="number" name="min_days" class="form-control my-2" min="0" id="pisol-form-minimum">
                        </div>
                    </div>
                    <div class="form-group row align-items-center">
                        <label class="col-sm-6 col-form-label">Maximum shipping days</label>
                        <div class="col-sm-6">
                        <input required type="number" name="max_days" class="form-control my-2" min="0" id="pisol-form-maximum">
                        <input type="hidden" name="zone" id="pisol-form-zone">
                        <input type="hidden" name="method" id="pisol-form-method">
                        <input type="hidden" name="method_name" id="pisol-form-method-name">
                        </div>
                    </div>
                    <input type="submit" class="btn btn-primary btn-block btn-lg" value="Save">
                    </form>
                    </div>
                </div>
            </div>
            <?php do_action('piso_edd_compatible_shipping_method'); ?>
        <?php
    }

    function shippingZones(){
        
        $zones = $this->shipping_zone_to_array();
        foreach ($zones as $key => $zone){
            echo '<a id="pi_zone_'.$key.'" href="javascript:void(0);" class="pisol-shipping-zone btn btn-primary btn-lg btn-block my-2" data-zone="'.$key.'">'.$zone.'</a>';
        }
    }

    function shipping_zone_to_array(){
        $zones = WC_Shipping_Zones::get_zones();
         
        $this->shipping_zones = $this->zone_to_array($zones);

        $non_covered_zone =  WC_Shipping_Zones::get_zone_by("zone_id",0);
        if(is_object($non_covered_zone)){
           $non_covered_zone_name = $non_covered_zone->get_zone_name();
           $non_covered_zone_id = $non_covered_zone->get_id();
           if(!empty($non_covered_zone_name)){
               $this->shipping_zones[$non_covered_zone_id] =  $non_covered_zone_name;
           }
        }
        return $this->shipping_zones;
    }

    function zone_to_array($zones){
        $select = array();
        foreach($zones as $zone){
            $zone_obj = new WC_Shipping_Zone($zone['zone_id']);
            $methods = $zone_obj->get_shipping_methods(true);
            if(count($methods) > 0){
                $select[$zone['zone_id']] =  $zone['zone_name'];
            }
        }
        return $select;
    }

    function shippingMethods(){
        
        $zones = $this->shipping_zone_to_array();
        foreach ($zones as $zone_id => $zone){
            $methods = $this->getShippingMethod($zone_id);
            foreach($methods as $method){
                $this->method($method, $zone_id);
            }
        }
    }

    function getShippingMethod($zone_id){
        $zone_obj = new WC_Shipping_Zone($zone_id);
        $methods = $zone_obj->get_shipping_methods(true);
        return $methods;
    }

    function method($method, $zone_id){
        //print_r($method);
        $days = pi_edd_common::getMinMax($method->instance_id, $method->id);
        $min_days = isset($days['min_days']) ? $days['min_days'] : '';
        $max_days = isset($days['max_days']) ? $days['max_days'] : '';
        echo '<a href="javascript:void(0)" id="pisol-method-'.$method->instance_id.'"  data-zone="'.$zone_id.'" style="display:none; " data-method="'.$method->instance_id.'" class="pi_zone_method_'.$zone_id.' pisol-shipping-method btn btn-secondary btn-lg btn-block my-2" data-minimum="'.$min_days.'" data-maximum="'.$max_days.'" data-method_name="'.$method->id.'">'.$method->title.'</a>';
    }

    function update_method(){
        if(current_user_can('administrator') ) {
            $min_days = isset($_POST['min_days']) ? (int)$_POST['min_days'] : "";
            $max_days = isset($_POST['max_days']) ? (int)$_POST['max_days'] : "";
            $zone = isset($_POST['zone']) ? (int)$_POST['zone'] : "";
            $method = isset($_POST['method']) ? (int)$_POST['method'] : "";
            $method_name = isset($_POST['method_name']) ? $_POST['method_name'] : "";
            
            $this->saveMinMax($min_days, $max_days, $method, $method_name);
           
            
            echo "Updated successfully";
            die;
        }
    }

    function saveMinMax($min_days, $max_days, $method, $method_name){
         /** option format is like this woocommerce_free_shipping_23_settings */
            $option_name = "woocommerce_".$method_name."_".$method."_settings";
            $present_setting = get_option($option_name);
            $present_setting['min_days'] = $min_days;
            $present_setting['max_days'] = $max_days;
            update_option($option_name, $present_setting);
    }
}

new Class_Pi_Edd_Shipping($this->plugin_name);
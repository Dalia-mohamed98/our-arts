<?php

class Class_Pi_Edd_Design{

    public $plugin_name;

    private $setting = array();

    private $active_tab;

    private $this_tab = 'design';

    private $tab_name = "Design";

    private $setting_key = 'design_settting';


    function __construct($plugin_name){
        $this->plugin_name = $plugin_name;
        
        $this->tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );
        $this->active_tab = $this->tab != "" ? $this->tab : 'default';

        if($this->this_tab == $this->active_tab){
            add_action($this->plugin_name.'_tab_content', array($this,'tab_content'));
        }

        add_action($this->plugin_name.'_tab', array($this,'tab'),4);

        add_action('woocommerce_init', array($this,'shipping_zone_to_array'));
        
        
    }

    function shipping_zone_to_array(){
      
        $this->settings = array(
            array('field'=>'title', 'class'=> 'bg-primary text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>"Single product page design", 'type'=>"setting_category"),
            array('field'=>'pi_product_bg_color', 'label'=>__('Background color of message','pi-edd'),'type'=>'color', 'default'=>'#f0947e',   'desc'=>'Background color'),
            array('field'=>'pi_product_text_color', 'label'=>__('Text color of message','pi-edd'),'type'=>'color', 'default'=>'#fff',  'desc'=>'Text color'),
            
            
            array('field'=>'title', 'class'=> 'bg-primary text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>"Shop / Category page design", 'type'=>"setting_category"),
            array('field'=>'pi_loop_bg_color', 'label'=>__('Background color of message','pi-edd'),'type'=>'color', 'default'=>'#f0947e',   'desc'=>'Background color'),
            array('field'=>'pi_loop_text_color', 'label'=>__('Text color of message','pi-edd'),'type'=>'color', 'default'=>'#fff',  'desc'=>'Text color'),


            
            array('field'=>'title', 'class'=> 'bg-primary text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>"Cart / Checkout page design", 'type'=>"setting_category"),
            array('field'=>'pi_cart_bg_color', 'label'=>__('Background color of message','pi-edd'),'type'=>'color', 'default'=>'#f0947e',   'desc'=>'Background color'),
            array('field'=>'pi_cart_text_color', 'label'=>__('Text color of message','pi-edd'),'type'=>'color', 'default'=>'#ffffff',  'desc'=>'Text color'),
           
           
        );
        $this->register_settings();

        if(PISOL_EDD_DELETE_SETTING){
            $this->delete_settings();
        }
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
        <form method="post" action="options.php"  class="pisol-setting-form">
        <?php settings_fields( $this->setting_key ); ?>
        <?php
            foreach($this->settings as $setting){
                new pisol_class_form_edd($setting, $this->setting_key);
            }
        ?>
        <input type="submit" class="mt-3 btn btn-primary btn-sm" value="Save Option" />
        </form>
       <?php
    }
}

new Class_Pi_Edd_Design($this->plugin_name);
<?php

class Class_Pi_Edd_Holidays{

    public $plugin_name;

    private $setting = array();

    private $active_tab;

    private $this_tab = 'holidays';

    private $tab_name = "Holidays";

    private $setting_key = 'holidays_setting';


    function __construct($plugin_name){
        $this->plugin_name = $plugin_name;
        
        $this->tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );
        $this->active_tab = $this->tab != "" ? $this->tab : 'default';

        if($this->this_tab == $this->active_tab){
            add_action($this->plugin_name.'_tab_content', array($this,'tab_content'));
        }

        add_action($this->plugin_name.'_tab', array($this,'tab'),3);

        $this->settings = array(
            array('field'=>'title', 'class'=> 'bg-primary text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>"Select holidays (BUY PRO to select unlimited holidays)", 'type'=>"setting_category"),
            array('field'=>'pi_edd_holidays'),
            
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
        <?php settings_fields( $this->setting_key ); 
        $dates = get_option("pi_edd_holidays");
        
        ?>
        <input type="hidden" id="pi_edd_holidays" name="pi_edd_holidays" value="<?php echo $dates; ?>">
        <?php
            foreach($this->settings as $setting){
                new pisol_class_form_edd($setting, $this->setting_key);
            }
        ?>
        <div class="alert alert-info mt-2">
            <strong>BUY PRO</strong> version it allows you to set <strong>unlimited holidays</strong> at once, so you can <strong>set the whole year holidays at once</strong>
        </div>
        <div id="pi-holiday-calender" class="mt-2"></div>
        <input type="submit" class="mt-3 btn btn-primary btn-md" value="Save Holidays" />
        <input type="button" id="reset-holidays" class="mt-3 btn btn-primary btn-md bg-primary" value="Reset dates" />
        </form>
        <h3>Selected holiday dates</h3>
        <div id="pi-selected-holidays"></div>
        <div class="alert alert-warning mt-2">
            You are using the <strong>FREE</strong> version which allows you have <strong>5 holidays</strong> at a time, once you have selected 10 holidays it wont allow you to select any more holidays, You can <strong>Reset calender</strong> to remove old holidays and select new up-coming holidays
        </div>

       <?php
    }
}

new Class_Pi_Edd_Holidays($this->plugin_name);
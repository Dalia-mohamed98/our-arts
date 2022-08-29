<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       piwebsolution.com
 * @since      1.0.0
 *
 * @package    Pi_Edd
 * @subpackage Pi_Edd/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Pi_Edd
 * @subpackage Pi_Edd/admin
 * @author     PI Websolution <rajeshsingh520@gmail.com>
 */
class Pi_Edd_Woo {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( ) {

		add_action( 'woocommerce_init', array($this,'shipping_methods'));
		

		add_action( 'woocommerce_product_data_tabs', array($this,'productTab') );
		/** Adding order preparation days */
		add_action( 'woocommerce_product_data_panels', array($this,'order_preparation_days') );
	}
	

	public function shipping_methods($array){
		$shipping_methods = @WC()->shipping()->load_shipping_methods();
		if(is_array($shipping_methods)):
		foreach($shipping_methods as $method){
			add_filter( 'woocommerce_shipping_instance_form_fields_'.$method->id, array($this,'pi_test'),1);
		}
		endif;
	}
	
	function pi_test($field){
		$field['min_days'] = array('title'=>'Minimum Days', 'type'=>'number','default'=>1,'min'=>0);
		$field['max_days'] = array('title'=>'Maximum Days', 'type'=>'number','default'=>1,'min'=>0);
		return $field;
	}

	function productTab($tabs){
        $tabs['pisol_mmq'] = array(
            'label'    => 'Preparation Time',
            'target'   => 'pisol_edd',
            'priority' => 21,
        );
        return $tabs;
	}
	

	function order_preparation_days() {
		echo '<div id="pisol_edd" class="panel woocommerce_options_panel hidden free-version">';
		
		woocommerce_wp_checkbox( array(
            'label' => __("Disable estimate for this product"), 
            'id' => 'pisol_edd_disable_estimate', 
            'name' => 'pisol_edd_disable_estimate', 
            'description' => __("Check this if you don't want to show estimate date for this particular product")
					) );
					echo '<hr>';
		echo '<div id="pisol-product-preparation-days">';
			$args = array(
			'id' => 'product_preparation_time',
			'label' => __( 'Product preparation days', 'pi-edd' ),
			'type' => 'number',
			'custom_attributes' => array(
				'step' 	=> '1',
				'min'	=> '0'
			) ,
			'placeholder'=>0,
			'class' => 'form-control',
			'desc_tip' => true,
			'description' => __( 'Enter the number of days it take to prepare this product' ),
			);
			woocommerce_wp_text_input( $args );

			$args2 = array(
				'id' => 'out_of_stock_product_preparation_time',
				'label' => __( 'Extra time added to preparation time (when product goes out of stock)', 'pi-edd' ),
				'type' => 'number',
				'custom_attributes' => array(
					'step' 	=> '1',
					'min'	=> '0'
				) ,
				'placeholder'=>0,
				'class' => 'form-control',
				'desc_tip' => true,
				'description' => __( 'This will be added in the normal product preparation time when product is out of stock and you are allowing back-order ' ),
				);
				woocommerce_wp_text_input( $args2 );

				$args3 = array(
					'id' => 'pisol_exact_availability_date',
					'label' => __( 'Exact Product availability date', 'pi-edd' ),
					'type' => 'text',
					
					'class' => 'form-control pisol_edd_date_picker',
					'desc_tip' => true,
					'description' => __( 'Select a date when this product will be available with you for dispatch, based on that it will show the estimate date, once you add date in this it plugin will use it for estimate calculation and ignore the above "preparation time" and "out of stock time"' ),
					);
				woocommerce_wp_text_input( $args3 );
		echo '</div>';
		?>
		<div class="alert" style="margin-left:10px; margin-right:10px; background:rgba(255,0,0, 6); padding:10px; color:#FFF;">
			This options only work in PRO version of the Estimate Delivery Date plugin,<br> Buy pro version now for <?php echo PI_EDD_PRICE; ?> only 
		</div>
		<div class="alert" style="margin-left:10px; margin-right:10px; background:rgba(255,0,0, 6); padding:10px; color:#FFF; margin-top:10px;">
			Pro version allows you to set Different preparation days for each variation of the variable product 
		</div>
		<a href="<?php echo PI_EDD_BUY_URL; ?>" class="button" target="_blank" style="margin:10px; ">Buy Now !!</a>
		<?php
		echo '</div>';
		
	}


}

new Pi_Edd_Woo();
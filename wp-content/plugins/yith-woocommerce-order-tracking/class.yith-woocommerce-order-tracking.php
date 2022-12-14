<?php
session_start();
if ( ! defined ( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists ( 'YITH_WooCommerce_Order_Tracking' ) ) {
	
	/**
	 * Implements features of Yith WooCommerce Order Tracking
	 *
	 * @class   Yith_WooCommerce_Order_Tracking
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 */
	class YITH_WooCommerce_Order_Tracking {
		
		/**
		 * @var $_panel Panel Object
		 */
		protected $_panel;
		
		/**
		 * @var $_premium string Premium tab template file name
		 */
		protected $_premium = 'premium.php';
		
		/**
		 * @var string Premium version landing link
		 */
		protected $_premium_landing = 'http://yithemes.com/themes/plugins/yith-woocommerce-order-tracking/';
		
		/**
		 * @var string Plugin official documentation
		 */
		protected $_official_documentation = 'http://yithemes.com/docs-plugins/yith-woocommerce-order-tracking/';
		
		/**
		 * @var string Yith WooCommerce Order Tracking panel page
		 */
		protected $_panel_page = 'yith_woocommerce_order_tracking_panel';
		
		//region plugin settings page
		
		/**
		 * @var mixed|void  Default carrier name
		 */
		protected $default_carrier;
		
		/**
		 * @var position of text related to order details page
		 */
		protected $order_text_position;
		
		//endregion
		
		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 * @access public
		 * @return void
		 */
		public function __construct() {
			
			
			$this->initialize_settings ();
			/**
			 *  Create YIT menu items for current plugin
			 */
			$this->create_menu_items ();
			
			/**
			 * Add metabox on order, to let vendor add order tracking code and carrier
			 */
			add_action ( 'add_meta_boxes', array( $this, 'add_order_tracking_metabox' ), 10, 2 );
			
			/**
			 * Save Order Meta Boxes
			 * */
			add_action ( 'woocommerce_process_shop_order_meta', array( $this, 'save_order_tracking_metabox' ), 10 );
			
			/**
			 * register action to show tracking information on customer order page
			 */
			$this->register_order_tracking_actions ();
			
			/**
			 * Show icon on order list for picked up orders
			 */
			add_action ( 'manage_shop_order_posts_custom_column', array( $this, 'prepare_picked_up_icon' ),50 );
			
			/**
			 * Set default carrier name on new orders
			 */
			add_action ( 'woocommerce_checkout_order_processed', array( $this, 'set_default_carrier' ) );
			
			add_action ( 'yith_order_tracking_premium', array( $this, 'premium_tab' ) );
			
			/**
			 * Show shipped icon on my orders page
			 */
			add_action ( 'woocommerce_my_account_my_orders_actions', array(
				$this,
				'show_picked_up_icon_on_orders',
			), 99, 2 );
			
			/**
			 * Enqueue scripts and styles
			 */
			add_action ( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action ( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			
		}
		
		/**
		 * Set values from plugin settings page
		 */
		public function initialize_settings() {
			$this->default_carrier     = get_option ( 'ywot_carrier_default_name' );
			$this->order_text_position = get_option ( 'ywot_order_tracking_text_position' );
		}
		
		/**
		 * Add scripts
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 */
		public function enqueue_scripts() {
			global $post;
//  register and enqueue ajax calls related script file
			
			wp_register_script ( "tooltipster", YITH_YWOT_URL . 'assets/js/jquery.tooltipster.min.js', array( 'jquery' ) );
			wp_enqueue_script ( 'tooltipster' );
			
			wp_enqueue_style ( 'ywot_style', YITH_YWOT_URL . 'assets/css/ywot_style.css' );
			
			wp_register_script ( "ywot_script", YITH_YWOT_URL . 'assets/js/ywot.js' );
			$premium = defined ( 'YITH_YWOT_PREMIUM' );
			wp_localize_script ( 'ywot_script', 'ywot', array(
				'p' => $premium,
			) );
			wp_enqueue_script ( 'ywot_script' );
			
		}
		
		/**
		 * Set default carrier name when an order is created (if related option is set).
		 *
		 * @param   $post_id    post id being created
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 * @access public
		 * @return void
		 */
		public function set_default_carrier( $post_id ) {
			
			if ( isset( $this->default_carrier ) && ( strlen ( $this->default_carrier ) > 0 ) ) {
				$order = wc_get_order ( $post_id );
				if ( $order ) {
					
					if ( defined ( 'YITH_YWOT_PREMIUM' ) ) {
						yit_save_prop ( $order, array( 'ywot_carrier_id' => $this->default_carrier ) );
					} else {
						yit_save_prop ( $order, array( 'ywot_carrier_name' => $this->default_carrier ) );
					}
				}
			}
		}
		
		/**
		 * Show a picked up icon on backend orders table
		 *
		 * @param   $column the column of backend order table being elaborated
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 * @access public
		 * @return void
		 */
		public function prepare_picked_up_icon( $column ) {
			
			//  If column is not of type order_status, skip it
			if ( 'order_status' !== $column ) {
				return;
			}
			
			global $the_order;
			
			$data = get_post_custom ( yit_get_prop ( $the_order, 'id' ) );
			
			//  if current order is not flagged as picked up, skip
			if ( ! $this->is_order_picked_up ( $data ) ) {
				return;
			}
			
			$this->show_picked_up_icon ( $data );
		}
		
		/**
		 * Check if an order is flagged as picked up
		 *
		 * @param $data post meta for current order
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 *
		 * @return bool
		 */
		public function is_order_picked_up( $data ) {
			$order_picked_up = isset( $data['ywot_picked_up'][0] ) && ( '' !== $data['ywot_picked_up'][0] );
			
			return $order_picked_up;
		}
		
		/**
		 * Build a text which indicates order tracking information
		 *
		 * @param $data     post meta for current order
		 * @param $pattern  text pattern to be used
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 */
		public function get_picked_up_message( $data, $pattern = '' ) {
			if ( ! isset( $pattern ) || ( 0 == strlen ( $pattern ) ) ) {
				$pattern = get_option ( 'ywot_order_tracking_text' );
				
			}
			
			//  Retrieve additional information to be shown
			$order_tracking_code = isset( $data['ywot_tracking_code'][0] ) ? $data['ywot_tracking_code'][0] : '';
			$order_carrier_name  = isset( $data['ywot_carrier_name'][0] ) ? $data['ywot_carrier_name'][0] : '';
			$order_pick_up_date  = isset( $data['ywot_pick_up_date'][0] ) ? $data['ywot_pick_up_date'][0] : '';
			
			$message = str_replace (
				array( "[carrier_name]", "[pickup_date]", "[track_code]" ),
				array(
					$order_carrier_name,
					date_i18n ( get_option ( 'date_format' ), strtotime ( $order_pick_up_date ) ),
					$order_tracking_code,
				),
				$pattern );
			
			return $message;
		}
		
		/**
		 * Show a image stating the order has been picked up
		 *
		 * @param $data post meta related to current order
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 */
		public function show_picked_up_icon( $data, $css_class = '' ) {
			if ( ! $this->is_order_picked_up ( $data ) ) {
				return;
			}
			
			$message = $this->get_picked_up_message ( $data );
			echo '<a class="track-button ' . $css_class . ' " style="display:inline-block;height:25px; padding-top:0; padding-bottom:0; top: 10px; position: relative; margin-left:10px" href="#" data-title="' . $message . '"><img class="track-button" style="height:25px;" src="' . YITH_YWOT_ASSETS_URL . '/images/order-picked-up.png" data-title="' . $message . '" /></a>';
		}
		
		/**
		 * Show on my orders page, a link image stating the order has been picked
		 *
		 * @param array    $actions others actions registered to the same hook
		 * @param WC_Order $order   the order being shown
		 *
		 * @return mixed    action passed as arguments
		 */
		public function show_picked_up_icon_on_orders( $actions, $order ) {
			$data = get_post_custom ( yit_get_prop ( $order, 'id' ) );
			if ( $this->is_order_picked_up ( $data ) ) {
				$this->show_picked_up_icon ( $data, 'button' );
			}
			
			return $actions;
		}
		
		//region    ****   Order tracking information methods   ****
		
		/**
		 * Add callback to show shipping details on order page, in the position choosen from plugin settings
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 * @access public
		 * @return void
		 */
		public function register_order_tracking_actions() {

			if ( ! isset( $this->order_text_position ) || ( 1 == $this->order_text_position ) ) {
				if( version_compare( WC()->version,'3.0.0','<' ) ){
					add_action( 'woocommerce_order_items_table', array( $this, 'add_order_shipping_details' ) );
				}else{
					add_action( 'woocommerce_order_details_after_order_table_items', array( $this, 'add_order_shipping_details' ) );
				}

			} else {
				add_action( 'woocommerce_order_details_after_order_table', array(
					$this,
					'add_order_shipping_details',
				) );

			}
		}
		
		
		/**
		 * Show order tracking information on user order page when the order is set to "completed"
		 *
		 * @param WC_Order $order the order whose tracking information have to be shown
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 * @access public
		 * @return void
		 */
		function add_order_shipping_details( $order ) {
			
			$container_class = "ywot_order_details";
			//  add top or bottom class, depending on the value of related option
			if ( 1 == $this->order_text_position ) {
				$container_class .= " top";
			} else {
				$container_class .= " bottom";
			}
			
			echo '<div class="' . $container_class . '">' . $this->show_tracking_information ( $order, get_option ( 'ywot_order_tracking_text' ), '' ) . '</div>';
		}
		
		
		/**
		 * Show message about the order tracking details.
		 *
		 * @param WC_Order $order   the order whose tracking information have to be shown
		 * @param string   $pattern custom text to be shown
		 * @param string   $prefix  Prefix to be shown before custom text
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 * @access public
		 * @return void
		 */
		function show_tracking_information( $order, $pattern, $prefix = '' ) {
			
			/**
			 * show information about order shipping
			 */
			$data            = get_post_custom ( yit_get_prop ( $order, 'id' ) );
			$order_picked_up = isset( $data['ywot_picked_up'][0] ) && ( '' !== $data['ywot_picked_up'][0] ) ? 'checked = "checked"' : '';
			
			//  if current order is not flagged as picked, don't show shipping information
			if ( ! $order_picked_up ) {
				return;
			}
			
			$message = $this->get_picked_up_message ( $data, $pattern );
			
			return $prefix . $message;
		}
		
		//endregion
		
		
		//region    ****   Custom menu entry for plugin, using Yith plugin framework    ****
		
		/**
		 * Register actions and filters to be used for creating an entry on YIT Plugin menu
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 * @access public
		 * @return void
		 */
		private function create_menu_items() {
			add_action ( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );


            /* === Show Plugin Information === */
            add_filter( 'plugin_action_links_' . plugin_basename( YITH_YWOT_DIR . '/' . basename( YITH_YWOT_FILE ) ), array( $this, 'action_links' ) );
            add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );
			
			//  Add stylesheets and scripts files
			add_action ( 'admin_menu', array( $this, 'register_panel' ), 5 );
		}
		
		/**
		 * Load YIT core plugin
		 *
		 * @since  1.0
		 * @access public
		 * @return void
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function plugin_fw_loader() {
			if ( ! defined ( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift ( $plugin_fw_data );
					require_once ( $plugin_fw_file );
				}
			}
		}
		
		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {
			
			if ( ! empty( $this->_panel ) ) {
				return;
			}
			
			$admin_tabs = array(
				'general' => __ ( 'General', 'yith-woocommerce-order-tracking' ),
			);
			
			if ( defined ( 'YITH_YWOT_PREMIUM' ) ) {
				$admin_tabs['carriers'] = __ ( 'Carriers', 'yith-woocommerce-order-tracking' );
			} else {
				$admin_tabs['premium'] = __ ( 'Premium Version', 'yith-woocommerce-order-tracking' );
			}
			
			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'plugin_slug'      => YITH_YWOT_SLUG,
				'page_title'       => 'Order Tracking',
				'menu_title'       => 'Order Tracking',
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yit_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YITH_YWOT_DIR . '/plugin-options',
                'class'            => yith_set_wrapper_class(),
			);
			
			/* === Fixed: not updated theme  === */
			if ( ! class_exists ( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				
				require_once ( 'plugin-fw/lib/yit-plugin-panel-wc.php' );
			}
			
			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}
		
		/**
		 * Premium Tab Template
		 *
		 * Load the premium tab template on admin page
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function premium_tab() {
			
			$premium_tab_template = YITH_YWOT_TEMPLATE_PATH . '/admin/' . $this->_premium;
			if ( file_exists ( $premium_tab_template ) ) {
				include_once ( $premium_tab_template );
			}
		}

		
		/**
		 * Get the premium landing uri
		 *
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return  string The premium landing link
		 */
		public function get_premium_landing_uri() {
            return $this->_premium_landing ;
		}
		
		//endregion
		
		
		//region    ****   Metabox related methods ****
		
		/**
		 *  Add a metabox on backend order page, to be filled with order tracking information
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 * @access public
		 * @return void
		 */
		function add_order_tracking_metabox() {
			
			add_meta_box ( 'yith-order-tracking-information', __ ( 'Order tracking', 'yith-woocommerce-order-tracking' ), array(
				$this,
				'show_order_tracking_metabox',
			), 'shop_order', 'side', 'high' );
		}
		
		/**
		 * Show metabox content for tracking information on backend order page
		 *
		 * @param WP_Post $post the order object that is currently shown
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 * @access public
		 * @return void
		 */
		function show_order_tracking_metabox( $post ) {
			$data                = get_post_custom ( $post->ID );
			$order_tracking_code = isset( $data['ywot_tracking_code'][0] ) ? $data['ywot_tracking_code'][0] : '';
			$order_carrier_name  = isset( $data['ywot_carrier_name'][0] ) ? $data['ywot_carrier_name'][0] : '';
			$order_pick_up_date  = isset( $data['ywot_pick_up_date'][0] ) ? $data['ywot_pick_up_date'][0] : '';
			$order_picked_up     = isset( $data['ywot_picked_up'][0] ) && ( '' !== $data['ywot_picked_up'][0] ) ? 'checked = "checked"' : '';
			
			$_SESSION['order_tracking_code'] = $order_tracking_code;
			?>
			<div class="track-information">
				<p>
					<label
						for="ywot_tracking_code"> <?php _e ( 'Tracking code:', 'yith-woocommerce-order-tracking' ); ?></label>
					<br />
					<input style="width: 100%" type="text" name="ywot_tracking_code" id="ywot_tracking_code"
					       placeholder="<?php _e ( 'Enter tracking code', 'yith-woocommerce-order-tracking' ); ?>"
					       value="<?php echo $order_tracking_code; ?>" />
				</p>
				<?php
					if($order_tracking_code != ''){
						?>
						<div style='text-align: center;'>
							<!-- insert your custom barcode setting your data in the GET parameter "data" -->
							<img alt='Barcode Generator TEC-IT' style='width:50%'
								 src='https://barcode.tec-it.com/barcode.ashx?data=<?php echo $order_tracking_code;?>&code=&multiplebarcodes=false&translate-esc=false&unit=Fit&dpi=96&imagetype=Gif&rotation=0&color=%23000000&bgcolor=%23ffffff&codepage=&qunit=Mm&quiet=0'/>
						</div>
				<?php
					}
				?>
				<p>
					<label
						for="ywot_carrier_name"> <?php _e ( 'Carrier name:', 'yith-woocommerce-order-tracking' ); ?></label>
					<br />
					<input style="width: 100%" type="text" id="ywot_carrier_name" name="ywot_carrier_name"
					       placeholder="<?php _e ( 'Enter carrier name', 'yith-woocommerce-order-tracking' ); ?>"
					       value="<?php echo $order_carrier_name; ?>" />
				</p>
				
				<p class="form-field form-field-wide">
					<label
						for="ywot_pick_up_date"> <?php _e ( 'Pickup date:', 'yith-woocommerce-order-tracking' ); ?></label>
					<br />
					<input style="width: 100%" type="text" class="date-picker-field" id="ywot_pick_up_date"
					       name="ywot_pick_up_date"
					       placeholder="<?php _e ( 'Enter pick up date', 'yith-woocommerce-order-tracking' ); ?>"
					       value="<?php echo $order_pick_up_date; ?>"
					       pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
				</p>
				
				<p>
					<label><input type="checkbox"
					              name="ywot_picked_up" <?php echo $order_picked_up; ?> ><?php _e ( 'Order picked up', 'yith-woocommerce-order-tracking' ); ?>
					</label>
				</p>
			</div>

			<script>
			jQuery(document).ready(function ($) {
				
				//console.log(localStorage.getItem('order_tracking_code'));
				$('#ywot_tracking_code').on('change',(function(e){
					console.log("tracking..........");
					var track = $(this).val();
					localStorage.setItem('order_tracking_code', track);
					console.log(localStorage.getItem('order_tracking_code'));
					
					
				}));
			});
			</script>
			<?php
			
		}
		
		/**
		 * Save additional data to the order its going to be saved. We add tracking code, carrier name and data of picking.
		 *
		 * @param $post_id  the post id whom order tracking information should be saved
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 * @access public
		 * @return void
		 */
		function save_order_tracking_metabox( $post_id ) {
			$order = wc_get_order ( $post_id );
			
			if ( $order ) {
				
				$parameters = array(
					'ywot_tracking_code' => stripslashes ( $_POST['ywot_tracking_code'] ),
					'ywot_pick_up_date'  => stripslashes ( $_POST['ywot_pick_up_date'] ),
				);
				
				if ( isset( $_POST['ywot_carrier_name'] ) ) {
					$parameters['ywot_carrier_name'] = stripslashes ( $_POST['ywot_carrier_name'] );
				}
				
				if ( isset( $_POST['ywot_picked_up'] ) ) {
					$parameters['ywot_picked_up'] = stripslashes ( $_POST['ywot_picked_up'] );
				}
				
				yit_save_prop ( $order, $parameters );
			}
		}

        /**
         * Action Links
         *
         * add the action links to plugin admin page
         *
         * @param $links | links plugin array
         *
         * @return   mixed Array
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @return mixed
         * @use      plugin_action_links_{$plugin_file_name}
         */
        public function action_links( $links ) {
            $links = yith_add_action_links( $links, $this->_panel_page, false );
            return $links;
        }


        /**
         * plugin_row_meta
         *
         * add the action links to plugin admin page
         *
         * @param $plugin_meta
         * @param $plugin_file
         * @param $plugin_data
         * @param $status
         *
         * @return   array
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @use      plugin_row_meta
         */
        public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_YWOT_FREE_INIT' ) {
            if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
                $new_row_meta_args['slug'] = YITH_YWOT_SLUG;
            }

            return $new_row_meta_args;
        }
		
		
		//endregion
		
	}
}
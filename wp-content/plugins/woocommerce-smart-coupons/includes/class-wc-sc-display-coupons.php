<?php
/**
 * Smart Coupons Display
 *
 * @author      StoreApps
 * @since       3.3.0
 * @version     1.0.8
 *
 * @package     woocommerce-smart-coupons/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Display_Coupons' ) ) {

	/**
	 * Class for handling display feature for coupons
	 */
	class WC_SC_Display_Coupons {

		/**
		 * Variable to hold instance of WC_SC_Display_Coupons
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Custom endpoint name.
		 *
		 * @var string
		 */
		public static $endpoint;

		/**
		 * Constructor
		 */
		private function __construct() {

			add_action( 'wp_ajax_sc_get_available_coupons', array( $this, 'get_available_coupons_html' ) );
			add_action( 'wp_ajax_nopriv_sc_get_available_coupons', array( $this, 'get_available_coupons_html' ) );

			add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'show_attached_gift_certificates' ) );
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'remove_add_to_cart_button_from_shop_page' ) );

			add_action( 'woocommerce_after_cart_table', array( $this, 'show_available_coupons_after_cart_table' ) );
			add_action( 'woocommerce_before_checkout_form', array( $this, 'show_available_coupons_before_checkout_form' ), 11 );

			add_filter( 'wc_sc_show_as_valid', array( $this, 'show_as_valid' ), 10, 2 );

			add_action( 'wp_loaded', array( $this, 'myaccount_display_coupons' ) );

			add_action( 'add_meta_boxes', array( $this, 'add_generated_coupon_details' ) );
			add_action( 'woocommerce_view_order', array( $this, 'generated_coupon_details_view_order' ) );
			add_action( 'woocommerce_email_after_order_table', array( $this, 'generated_coupon_details_after_order_table' ), 10, 3 );

			add_action( 'wp_footer', array( $this, 'frontend_styles_and_scripts' ) );

			add_filter( 'woocommerce_update_order_review_fragments', array( $this, 'woocommerce_update_order_review_fragments' ) );

			add_filter( 'woocommerce_coupon_get_date_expires', array( $this, 'wc_sc_get_date_expires' ), 10, 2 );

			add_action( 'init', array( $this, 'endpoint_hooks' ) );

			add_filter( 'woocommerce_available_variation', array( $this, 'modify_available_variation' ), 10, 3 );

		}

		/**
		 * Handle call to functions which is not available in this class
		 *
		 * @param string $function_name The function name.
		 * @param array  $arguments Array of arguments passed while calling $function_name.
		 * @return result of function call
		 */
		public function __call( $function_name, $arguments = array() ) {

			global $woocommerce_smart_coupon;

			if ( ! is_callable( array( $woocommerce_smart_coupon, $function_name ) ) ) {
				return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( array( $woocommerce_smart_coupon, $function_name ), $arguments );
			} else {
				return call_user_func( array( $woocommerce_smart_coupon, $function_name ) );
			}

		}

		/**
		 * Get single instance of WC_SC_Display_Coupons
		 *
		 * @return WC_SC_Display_Coupons Singleton object of WC_SC_Display_Coupons
		 */
		public static function get_instance() {
			// Check if instance is already exists.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Function to show available coupons on Cart & Checkout page
		 *
		 * @param string $available_coupons_heading Existing heading.
		 * @param string $page The page.
		 */
		public function show_available_coupons( $available_coupons_heading = '', $page = 'checkout' ) {

			$coupons = $this->sc_get_available_coupons_list( array() );

			if ( empty( $coupons ) ) {
				do_action(
					'wc_sc_no_available_coupons',
					array(
						'available_coupons_heading' => $available_coupons_heading,
						'page'                      => $page,
					)
				);
				return false;
			}

			if ( ! wp_style_is( 'smart-coupon' ) ) {
				wp_enqueue_style( 'smart-coupon' );
			}

			$design           = get_option( 'wc_sc_setting_coupon_design', 'round-dashed' );
			$background_color = get_option( 'wc_sc_setting_coupon_background_color', '#39cccc' );
			$foreground_color = get_option( 'wc_sc_setting_coupon_foreground_color', '#30050b' );

			?>
			<div id="coupons_list" style="display: none;">
				<style type="text/css"><?php echo $this->get_coupon_styles( $design ); // phpcs:ignore ?></style>
				<style type="text/css">
					.coupon-container.left:before,
					.coupon-container.bottom:before {
						background: <?php echo esc_html( $foreground_color ); ?> !important;
					}
					.coupon-container.left:hover, .coupon-container.left:focus, .coupon-container.left:active,
					.coupon-container.bottom:hover, .coupon-container.bottom:focus, .coupon-container.bottom:active {
						color: <?php echo esc_html( $background_color ); ?> !important;
					}
				</style>
				<h3><?php echo __( stripslashes( $available_coupons_heading ), 'woocommerce-smart-coupons' ); // phpcs:ignore ?></h3><div id="all_coupon_container">
				<?php

				$max_coupon_to_show = get_option( 'wc_sc_setting_max_coupon_to_show', 5 );
				$max_coupon_to_show = apply_filters( 'wc_sc_max_coupon_to_show', $max_coupon_to_show );

				$coupons_applied = ( is_object( WC()->cart ) && is_callable( array( WC()->cart, 'get_applied_coupons' ) ) ) ? WC()->cart->get_applied_coupons() : array();

				foreach ( $coupons as $code ) {

					if ( $max_coupon_to_show <= 0 ) {
						break;
					}

					if ( in_array( strtolower( $code->post_title ), array_map( 'strtolower', $coupons_applied ), true ) ) {
						continue;
					}

					$coupon = new WC_Coupon( $code->post_title );

					if ( 'woocommerce_before_my_account' !== current_filter() && ! $coupon->is_valid() ) {

						// Filter to allow third party developers to show coupons which are invalid due to cart requirements like minimum order total or products.
						$wc_sc_force_show_coupon = apply_filters( 'wc_sc_force_show_invalid_coupon', false, array( 'coupon' => $coupon ) );
						if ( false === $wc_sc_force_show_coupon ) {
							continue;
						}
					}

					if ( $this->is_wc_gte_30() ) {
						if ( ! is_object( $coupon ) || ! is_callable( array( $coupon, 'get_id' ) ) ) {
							continue;
						}
						$coupon_id = $coupon->get_id();
						if ( empty( $coupon_id ) ) {
							continue;
						}
						$coupon_amount    = $coupon->get_amount();
						$is_free_shipping = ( $coupon->get_free_shipping() ) ? 'yes' : 'no';
						$discount_type    = $coupon->get_discount_type();
						$expiry_date      = $coupon->get_date_expires();
						$coupon_code      = $coupon->get_code();
					} else {
						$coupon_id        = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
						$coupon_amount    = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
						$is_free_shipping = ( ! empty( $coupon->free_shipping ) ) ? $coupon->free_shipping : '';
						$discount_type    = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
						$expiry_date      = ( ! empty( $coupon->expiry_date ) ) ? $coupon->expiry_date : '';
						$coupon_code      = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
					}

					$is_show_zero_amount_coupon = true;

					if ( ( empty( $coupon_amount ) ) && ( ( ! empty( $discount_type ) && ! in_array( $discount_type, array( 'free_gift', 'smart_coupon' ), true ) ) || ( 'yes' !== $is_free_shipping ) ) ) {
						if ( 'yes' !== $is_free_shipping ) {
							$is_show_zero_amount_coupon = false;
						}
					}

					$is_show_zero_amount_coupon = apply_filters( 'show_zero_amount_coupon', $is_show_zero_amount_coupon, array( 'coupon' => $coupon ) );

					if ( false === $is_show_zero_amount_coupon ) {
						continue;
					}

					if ( $this->is_wc_gte_30() && $expiry_date instanceof WC_DateTime ) {
						$expiry_date = $expiry_date->getTimestamp();
					} elseif ( ! is_int( $expiry_date ) ) {
						$expiry_date = strtotime( $expiry_date );
					}

					if ( ! empty( $expiry_date ) && is_int( $expiry_date ) ) {
						$expiry_time = (int) get_post_meta( $coupon_id, 'wc_sc_expiry_time', true );
						if ( ! empty( $expiry_time ) ) {
							$expiry_date += $expiry_time; // Adding expiry time to expiry date.
						}
					}

					if ( empty( $discount_type ) || ( ! empty( $expiry_date ) && time() > $expiry_date ) ) {
						continue;
					}

					$coupon_post = get_post( $coupon_id );

					$coupon_data = $this->get_coupon_meta_data( $coupon );

					echo '<div class="coupon-container apply_coupons_credits ' . esc_attr( $this->get_coupon_container_classes() ) . '" name="' . esc_attr( $coupon_code ) . '" style="cursor: pointer; ' . esc_attr( $this->get_coupon_style_attributes() ) . '">
							<div class="coupon-content ' . esc_attr( $this->get_coupon_content_classes() ) . '" name="' . esc_attr( $coupon_code ) . '">
								<div class="discount-info" >'; // phpcs:ignore

					$discount_title = '';

					if ( ! empty( $coupon_data['coupon_amount'] ) && ! empty( $coupon_amount ) ) {
						$discount_title .= $coupon_data['coupon_amount'] . ' ' . $coupon_data['coupon_type'];
					}

					$discount_title = apply_filters( 'wc_smart_coupons_display_discount_title', $discount_title, $coupon );

					if ( ! empty( $discount_title ) ) {

						// Not escaping because 3rd party developer can have HTML code in discount title.
						echo $discount_title; // phpcs:ignore

						if ( 'yes' === $is_free_shipping ) {
							echo __( ' &amp; ', 'woocommerce-smart-coupons' ); // phpcs:ignore
						}
					}

					if ( 'yes' === $is_free_shipping ) {
						echo esc_html__( 'Free Shipping', 'woocommerce-smart-coupons' );
					}
					echo '</div>';

					echo '<div class="code">' . esc_html( $coupon_code ) . '</div>';

					$show_coupon_description = get_option( 'smart_coupons_show_coupon_description', 'no' );
					if ( ! empty( $coupon_post->post_excerpt ) && 'yes' === $show_coupon_description ) {
						echo '<div class="discount-description">' . esc_html( $coupon_post->post_excerpt ) . '</div>';
					}

					if ( ! empty( $expiry_date ) ) {

						$expiry_date = $this->get_expiration_format( $expiry_date );

						echo '<div class="coupon-expire">' . esc_html( $expiry_date ) . '</div>';

					} else {

						echo '<div class="coupon-expire">' . esc_html__( 'Never Expires', 'woocommerce-smart-coupons' ) . '</div>';

					}

					echo '</div></div>'; // phpcs:ignore

					$max_coupon_to_show--;

				}

				if ( did_action( 'wc_smart_coupons_frontend_styles_and_scripts' ) <= 0 || ! defined( 'DOING_AJAX' ) || DOING_AJAX !== true ) {
					$this->frontend_styles_and_scripts( array( 'page' => $page ) );
				}
				?>
			</div></div>
			<?php

		}

		/**
		 * Get available coupon's HTML
		 */
		public function get_available_coupons_html() {
			check_ajax_referer( 'sc-get-available-coupons', 'security' );
			$this->show_available_coupons_before_checkout_form();
			die();
		}

		/**
		 * Function to show available coupons before checkout form
		 */
		public function show_available_coupons_before_checkout_form() {

			$smart_coupon_cart_page_text = get_option( 'smart_coupon_cart_page_text' );
			$smart_coupon_cart_page_text = ( ! empty( $smart_coupon_cart_page_text ) ) ? $smart_coupon_cart_page_text : __( 'Available Coupons (click on a coupon to use it)', 'woocommerce-smart-coupons' );
			$this->show_available_coupons( $smart_coupon_cart_page_text, 'checkout' );

		}

		/**
		 * Check if store credit is valid based on amount
		 *
		 * @param  boolean $is_valid Validity.
		 * @param  array   $args     Additional arguments.
		 * @return boolean           Validity.
		 */
		public function show_as_valid( $is_valid = false, $args = array() ) {

			$coupon = ( ! empty( $args['coupon_obj'] ) ) ? $args['coupon_obj'] : false;

			if ( empty( $coupon ) ) {
				return $is_valid;
			}

			if ( $this->is_wc_gte_30() ) {
				$discount_type = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
				$coupon_amount = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_amount' ) ) ) ? $coupon->get_amount() : 0;
			} else {
				$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
				$coupon_amount = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
			}

			if ( true === $is_valid && 'smart_coupon' === $discount_type && empty( $coupon_amount ) ) {
				return false;
			}

			return $is_valid;
		}

		/**
		 * Hooks for handling display of coupons on My Account page
		 */
		public function myaccount_display_coupons() {

			$is_show_on_my_account = get_option( 'woocommerce_smart_coupon_show_my_account', 'yes' );

			if ( 'yes' !== $is_show_on_my_account ) {
				return;
			}

			if ( $this->is_wc_gte_26() ) {
				add_filter( 'query_vars', array( $this, 'sc_add_query_vars' ), 0 );
				// Change the My Account page title.
				add_filter( 'the_title', array( $this, 'sc_endpoint_title' ) );
				// Insering our new tab/page into the My Account page.
				add_filter( 'woocommerce_account_menu_items', array( $this, 'sc_new_menu_items' ) );
				add_action( 'woocommerce_account_' . self::$endpoint . '_endpoint', array( $this, 'sc_endpoint_content' ) );
			} else {
				add_action( 'woocommerce_before_my_account', array( $this, 'show_smart_coupon_balance' ) );
				add_action( 'woocommerce_before_my_account', array( $this, 'generated_coupon_details_before_my_account' ) );
			}

		}

		/**
		 * Function to show gift certificates that are attached with the product
		 */
		public function show_attached_gift_certificates() {
			global $post, $wp_rewrite, $store_credit_label;

			if ( empty( $post->ID ) ) {
				return;
			}

			$is_show_associated_coupons = get_option( 'smart_coupons_is_show_associated_coupons', 'no' );

			if ( 'yes' !== $is_show_associated_coupons ) {
				return;
			}

			$coupon_titles = get_post_meta( $post->ID, '_coupon_title', true );

			$_product = wc_get_product( $post->ID );

			if ( $this->is_wc_gte_30() ) {
				$product_type = ( is_object( $_product ) && is_callable( array( $_product, 'get_type' ) ) ) ? $_product->get_type() : '';
			} else {
				$product_type = ( ! empty( $_product->product_type ) ) ? $_product->product_type : '';
			}

			$sell_sc_at_less_price         = get_option( 'smart_coupons_sell_store_credit_at_less_price', 'no' );
			$generated_credit_includes_tax = $this->is_generated_store_credit_includes_tax();

			if ( 'yes' === $sell_sc_at_less_price ) {
				if ( is_a( $_product, 'WC_Product_Variable' ) ) {
					$price = ( is_object( $_product ) && is_callable( array( $_product, 'get_variation_regular_price' ) ) ) ? $_product->get_variation_regular_price( 'max' ) : 0;
				} else {
					$price = ( is_object( $_product ) && is_callable( array( $_product, 'get_regular_price' ) ) ) ? $_product->get_regular_price() : 0;
				}
			} else {
				if ( is_a( $_product, 'WC_Product_Variable' ) ) {
					$price = ( is_object( $_product ) && is_callable( array( $_product, 'get_variation_price' ) ) ) ? $_product->get_variation_price( 'max' ) : 0;
				} else {
					$price = ( is_object( $_product ) && is_callable( array( $_product, 'get_price' ) ) ) ? $_product->get_price() : 0;
				}
			}

			if ( $coupon_titles && count( $coupon_titles ) > 0 && ! empty( $price ) ) {

				$all_discount_types              = wc_get_coupon_types();
				$smart_coupons_product_page_text = get_option( 'smart_coupon_product_page_text' );
				$smart_coupons_product_page_text = ( ! empty( $smart_coupons_product_page_text ) ) ? $smart_coupons_product_page_text : __( 'You will get following coupon(s) when you buy this item:', 'woocommerce-smart-coupons' );

				$list_started = true;
				$js           = '';

				foreach ( $coupon_titles as $coupon_title ) {

					$coupon = new WC_Coupon( $coupon_title );

					if ( $this->is_wc_gte_30() ) {
						if ( ! is_object( $coupon ) || ! is_callable( array( $coupon, 'get_id' ) ) ) {
							continue;
						}
						$coupon_id = $coupon->get_id();
						if ( empty( $coupon_id ) ) {
							continue;
						}
						$discount_type               = $coupon->get_discount_type();
						$coupon_amount               = $coupon->get_amount();
						$is_free_shipping            = ( $coupon->get_free_shipping() ) ? 'yes' : 'no';
						$product_ids                 = $coupon->get_product_ids();
						$excluded_product_ids        = $coupon->get_excluded_product_ids();
						$product_categories          = $coupon->get_product_categories();
						$excluded_product_categories = $coupon->get_excluded_product_categories();
					} else {
						$coupon_id                   = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
						$discount_type               = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
						$coupon_amount               = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
						$is_free_shipping            = ( ! empty( $coupon->free_shipping ) ) ? $coupon->free_shipping : '';
						$product_ids                 = ( ! empty( $coupon->product_ids ) ) ? $coupon->product_ids : array();
						$excluded_product_ids        = ( ! empty( $coupon->exclude_product_ids ) ) ? $coupon->exclude_product_ids : array();
						$product_categories          = ( ! empty( $coupon->product_categories ) ) ? $coupon->product_categories : array();
						$excluded_product_categories = ( ! empty( $coupon->exclude_product_categories ) ) ? $coupon->exclude_product_categories : array();
					}

					$is_pick_price_of_product = get_post_meta( $coupon_id, 'is_pick_price_of_product', true );

					if ( $list_started && ! empty( $discount_type ) ) {
						echo '<div class="clear"></div>';
						echo '<div class="gift-certificates">';
						echo '<br /><p>' . esc_html( wp_unslash( $smart_coupons_product_page_text ) ) . '';
						echo '<ul>';
						$list_started = false;
					}

					switch ( $discount_type ) {

						case 'smart_coupon':
							/* translators: %s: singular name for store credit */
							$credit_label = ! empty( $store_credit_label['singular'] ) ? sprintf( __( '%s of ', 'woocommerce-smart-coupons' ), esc_html( ucwords( $store_credit_label['singular'] ) ) ) : __( 'Store Credit of ', 'woocommerce-smart-coupons' );
							if ( 'yes' === $is_pick_price_of_product ) {

								if ( 'variable' === $product_type ) {

									$js = " jQuery('div.gift-certificates').hide();

											var reload_gift_certificate_div = function( variation ) {
												let sell_at_less_price            = '" . $sell_sc_at_less_price . "';
												let generated_credit_includes_tax = '" . wc_bool_to_string( $generated_credit_includes_tax ) . "';
												let sc_price                      = 0;
												let sc_regular_price              = 0;
												if ( 'yes' === generated_credit_includes_tax ) {
													sc_price         = (variation.price_including_tax) ? variation.price_including_tax : variation.display_price;
													sc_regular_price = (variation.regular_price_including_tax) ? variation.regular_price_including_tax : variation.display_regular_price;
												} else if ( 'no' === generated_credit_includes_tax ) {
													sc_price         = (variation.price_excluding_tax) ? variation.price_excluding_tax : variation.display_price;
													sc_regular_price = (variation.regular_price_excluding_tax) ? variation.regular_price_excluding_tax : variation.display_regular_price;
												} else {
													sc_price         = variation.display_price;
													sc_regular_price = variation.display_regular_price;
												}
												jQuery('div.gift-certificates').show().fadeTo( 100, 0.4 );
												let amount = '';
												if ( 'yes' === sell_at_less_price ) {
													// If variation is discounted then voucher worth is equal to display_regular_price when sell at less price enabled.
													if ( ( 'undefined' !== typeof sc_price && 'undefined' !== typeof sc_regular_price ) && ( sc_price < sc_regular_price ) ) {
														if ( 'no' === generated_credit_includes_tax ) {
															amount = variation.regular_price_excluding_tax_html;
														} else {
															amount = jQuery(variation.price_html).find('del').text();
														}
													} else {
														// If variation price is not discounted then voucher worth is equal to display_price.
														if ( 'no' === generated_credit_includes_tax ) {
															amount = variation.price_excluding_tax_html;
														} else {
															amount = jQuery(variation.price_html).text();
														}
													}
												} else {
													if ( 'no' === generated_credit_includes_tax ) {
														amount = variation.price_excluding_tax_html;
													} else {
														amount = jQuery(variation.price_html).html().replace( jQuery(variation.price_html).find('del').html(), '' );
													}
													amount = jQuery(amount).text();
												}
												jQuery('div.gift-certificates').find('li.pick_price_from_product').remove();
												jQuery('div.gift-certificates').find('ul').append( '<li class=\"pick_price_from_product\" >' + '" . $credit_label . "' + amount + '</li>');
												jQuery('div.gift-certificates').fadeTo( 100, 1 );
											};

											jQuery('input[name=variation_id]').on('change', function(){
												var variation;
                            					var variation_id = jQuery('input[name=variation_id]').val();
                            					if ( variation_id != '' && variation_id != undefined ) {
                            						if ( variation != '' && variation != undefined ) {
	                            						jQuery('form.variations_form.cart').one( 'found_variation', function( event, variation ) {
															if ( variation_id = variation.variation_id ) {
																reload_gift_certificate_div( variation );
															}
														});
                            						} else {
                            							var variations = jQuery('form.variations_form.cart').data('product_variations');
                            							jQuery.each( variations, function( index, value ){
                            								if ( variation_id == value.variation_id ) {
																reload_gift_certificate_div( value );
																return false;
                            								}
                            							});
                            						}

												}
											});

											setTimeout(function(){
												var default_variation_id = jQuery('input[name=variation_id]').val();
												if ( default_variation_id != '' && default_variation_id != undefined ) {
													jQuery('input[name=variation_id]').val( default_variation_id ).trigger( 'change' );
												}
											}, 10);

											jQuery('a.reset_variations').on('click', function(){
												jQuery('div.gift-certificates').find('li.pick_price_from_product').remove();
												jQuery('div.gift-certificates').hide();
											});";

									$amount = '';

								} else {

									$amount = ( $price > 0 ) ? $credit_label . wc_price( $price ) : '';

								}
							} else {
								$amount = ( ! empty( $coupon_amount ) ) ? $credit_label . wc_price( $coupon_amount ) : '';
							}

							break;

						case 'fixed_cart':
							$amount = wc_price( $coupon_amount ) . esc_html__( ' discount on your entire purchase', 'woocommerce-smart-coupons' );
							break;

						case 'fixed_product':
							if ( ! empty( $product_ids ) || ! empty( $excluded_product_ids ) || ! empty( $product_categories ) || ! empty( $excluded_product_categories ) ) {
								$discount_on_text = esc_html__( 'some products', 'woocommerce-smart-coupons' );
							} else {
								$discount_on_text = esc_html__( 'all products', 'woocommerce-smart-coupons' );
							}
							$amount = wc_price( $coupon_amount ) . esc_html__( ' discount on ', 'woocommerce-smart-coupons' ) . $discount_on_text;
							break;

						case 'percent_product':
							if ( ! empty( $product_ids ) || ! empty( $excluded_product_ids ) || ! empty( $product_categories ) || ! empty( $excluded_product_categories ) ) {
								$discount_on_text = esc_html__( 'some products', 'woocommerce-smart-coupons' );
							} else {
								$discount_on_text = esc_html__( 'all products', 'woocommerce-smart-coupons' );
							}
							$amount = $coupon_amount . '%' . esc_html__( ' discount on ', 'woocommerce-smart-coupons' ) . $discount_on_text;
							break;

						case 'percent':
							if ( ! empty( $product_ids ) || ! empty( $excluded_product_ids ) || ! empty( $product_categories ) || ! empty( $excluded_product_categories ) ) {
								$discount_on_text = esc_html__( 'some products', 'woocommerce-smart-coupons' );
							} else {
								$discount_on_text = esc_html__( 'your entire purchase', 'woocommerce-smart-coupons' );
							}
							$max_discount_text = '';
							$max_discount      = get_post_meta( $coupon_id, 'wc_sc_max_discount', true );
							if ( ! empty( $max_discount ) && is_numeric( $max_discount ) ) {
								/* translators: %s: Maximum coupon discount amount */
								$max_discount_text = sprintf( __( ' upto %s', 'woocommerce-smart-coupons' ), wc_price( $max_discount ) );
							}
							$amount = $coupon_amount . '%' . esc_html__( ' discount', 'woocommerce-smart-coupons' ) . $max_discount_text . esc_html__( ' on ', 'woocommerce-smart-coupons' ) . $discount_on_text;
							break;

						default:
							$default_coupon_type = ( ! empty( $all_discount_types[ $discount_type ] ) ) ? $all_discount_types[ $discount_type ] : ucwords( str_replace( array( '_', '-' ), ' ', $discount_type ) );
							$coupon_amount       = apply_filters( 'wc_sc_coupon_amount', $coupon_amount, $coupon );
							/* translators: 1. Discount type 2. Discount amount */
							$amount = sprintf( esc_html__( '%1$s coupon of %2$s', 'woocommerce-smart-coupons' ), $default_coupon_type, $coupon_amount );
							$amount = apply_filters( 'wc_sc_coupon_description', $amount, $coupon );
							break;

					}

					if ( 'yes' === $is_free_shipping && in_array( $discount_type, array( 'fixed_cart', 'fixed_product', 'percent_product', 'percent' ), true ) ) {
						/* translators: Add more detail to coupon description */
						$amount = sprintf( esc_html__( '%s Free Shipping', 'woocommerce-smart-coupons' ), ( ( ! empty( $coupon_amount ) ) ? $amount . esc_html__( ' &', 'woocommerce-smart-coupons' ) : '' ) );
					}

					if ( ! empty( $amount ) ) {
						// Allow third party developers to modify the text being shown for the linked coupons.
						$amount = apply_filters( 'wc_sc_linked_coupon_text', $amount, array( 'coupon' => $coupon ) );

						// Mostly escaped earlier hence not escaping because it might have some HTML code.
						echo '<li>' . $amount . '</li>'; // phpcs:ignore
					}
				}

				if ( ! $list_started ) {
					echo '</ul></p></div>';
				}

				if ( ! empty( $js ) ) {
					wc_enqueue_js( $js );
				}
			}
		}

		/**
		 * Replace Add to cart button with Select Option button for products which are created for purchasing credit, on shop page
		 */
		public function remove_add_to_cart_button_from_shop_page() {
			global $product;

			if ( ! is_a( $product, 'WC_Product' ) ) {
				return;
			}

			if ( $this->is_wc_gte_30() ) {
				$product_id = ( is_object( $product ) && is_callable( array( $product, 'get_id' ) ) ) ? $product->get_id() : 0;
			} else {
				$product_id = ( ! empty( $product->id ) ) ? $product->id : 0;
			}

			$coupons = get_post_meta( $product_id, '_coupon_title', true );

			if ( ! empty( $coupons ) && $this->is_coupon_amount_pick_from_product_price( $coupons ) && ! ( $product->get_price() > 0 ) ) {

				$js = "
						var target_class = 'wc_sc_loop_button_" . $product_id . "';
						var wc_sc_loop_button = jQuery('.' + target_class);
						var wc_sc_old_element = jQuery(wc_sc_loop_button).siblings('a[data-product_id=" . $product_id . "]');
						var wc_sc_loop_button_classes = wc_sc_old_element.attr('class');
						wc_sc_loop_button.removeClass( target_class ).addClass( wc_sc_loop_button_classes ).show();
						wc_sc_old_element.remove();
					";

				wc_enqueue_js( $js );

				?>
				<a href="<?php echo esc_url( the_permalink() ); ?>" class="wc_sc_loop_button_<?php echo esc_attr( $product_id ); ?>" style="display: none;"><?php echo esc_html( get_option( 'sc_gift_certificate_shop_loop_button_text', __( 'Select options', 'woocommerce-smart-coupons' ) ) ); ?></a>
				<?php
			}
		}

		/**
		 * Function to show available coupons after cart table
		 */
		public function show_available_coupons_after_cart_table() {

			$smart_coupon_cart_page_text = get_option( 'smart_coupon_cart_page_text' );
			$smart_coupon_cart_page_text = ( ! empty( $smart_coupon_cart_page_text ) ) ? $smart_coupon_cart_page_text : __( 'Available Coupons (click on a coupon to use it)', 'woocommerce-smart-coupons' );
			$this->show_available_coupons( $smart_coupon_cart_page_text, 'cart' );

		}

		/**
		 * Function to display current balance associated with Gift Certificate
		 */
		public function show_smart_coupon_balance() {
			global $store_credit_label;

			$smart_coupon_myaccount_page_text = get_option( 'smart_coupon_myaccount_page_text' );

			/* translators: %s: plural name for store credit */
			$smart_coupons_myaccount_page_text = ( ! empty( $smart_coupon_myaccount_page_text ) ) ? $smart_coupon_myaccount_page_text : ( ! empty( $store_credit_label['plural'] ) ? sprintf( __( 'Available Coupons & %s', 'woocommerce-smart-coupons' ), ucwords( $store_credit_label['plural'] ) ) : __( 'Available Coupons & Store Credits', 'woocommerce-smart-coupons' ) );
			$this->show_available_coupons( $smart_coupons_myaccount_page_text, 'myaccount' );

		}

		/**
		 * Display generated coupon's details on My Account page
		 */
		public function generated_coupon_details_before_my_account() {
			$show_coupon_received_on_my_account = get_option( 'show_coupon_received_on_my_account', 'no' );

			if ( is_user_logged_in() && 'yes' === $show_coupon_received_on_my_account ) {
				$user_id = get_current_user_id();
				$this->get_generated_coupon_data( '', $user_id, true, true );
			}
		}

		/**
		 * Add new query var.
		 *
		 * @param array $vars The query vars.
		 * @return array
		 */
		public function sc_add_query_vars( $vars ) {

			$vars[] = self::$endpoint;
			return $vars;
		}

		/**
		 * Set endpoint title.
		 *
		 * @param string $title The title of coupon page.
		 * @return string
		 */
		public function sc_endpoint_title( $title ) {
			global $wp_query;

			$is_endpoint = isset( $wp_query->query_vars[ self::$endpoint ] );

			if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
				// New page title.
				$title = __( 'Coupons', 'woocommerce-smart-coupons' );
				remove_filter( 'the_title', array( $this, 'sc_endpoint_title' ) );
			}

			return $title;
		}

		/**
		 * Insert the new endpoint into the My Account menu.
		 *
		 * @param array $items Existing menu items.
		 * @return array
		 */
		public function sc_new_menu_items( $items ) {

			// Remove the menu items.
			if ( isset( $items['edit-address'] ) ) {
				$edit_address = $items['edit-address'];
				unset( $items['edit-address'] );
			}

			if ( isset( $items['payment-methods'] ) ) {
				$payment_methods = $items['payment-methods'];
				unset( $items['payment-methods'] );
			}

			if ( isset( $items['edit-account'] ) ) {
				$edit_account = $items['edit-account'];
				unset( $items['edit-account'] );
			}

			if ( isset( $items['customer-logout'] ) ) {
				$logout = $items['customer-logout'];
				unset( $items['customer-logout'] );
			}

			// Insert our custom endpoint.
			$items[ self::$endpoint ] = __( 'Coupons', 'woocommerce-smart-coupons' );

			// Insert back the items.
			if ( ! empty( $edit_address ) ) {
				$items['edit-address'] = $edit_address;
			}
			if ( ! empty( $payment_methods ) ) {
				$items['payment-methods'] = $payment_methods;
			}
			if ( ! empty( $edit_account ) ) {
				$items['edit-account'] = $edit_account;
			}
			if ( ! empty( $logout ) ) {
				$items['customer-logout'] = $logout;
			}

			return $items;
		}

		/**
		 * Get coupon HTML
		 *
		 * @param  array $coupon_data the coupon data.
		 * @return string Coupon's HTML
		 */
		public function get_coupon_html( $coupon_data = array() ) {

			$html = '';

			$html .= '<div class="coupon-container apply_coupons_credits ' . $this->get_coupon_container_classes() . '" name="' . $coupon_data['coupon_code'] . '" style="cursor: pointer; ' . $this->get_coupon_style_attributes() . '">
						<div class="coupon-content ' . $this->get_coupon_content_classes() . '" name="' . $coupon_data['coupon_code'] . '">
							<div class="discount-info" >';

			if ( ( ! empty( $coupon_data['coupon_amount'] ) && ( wc_price( 0 ) !== $coupon_data['coupon_amount'] && '0%' !== $coupon_data['coupon_amount'] ) ) || ( isset( $coupon_data['is_invalid'] ) && 'yes' === $coupon_data['is_invalid'] ) ) {
				$html .= $coupon_data['coupon_amount'] . ' ' . $coupon_data['coupon_type'];
				if ( 'yes' === $coupon_data['is_free_shipping'] ) {
					$html .= __( ' &amp; ', 'woocommerce-smart-coupons' );
				}
			}

			if ( 'yes' === $coupon_data['is_free_shipping'] ) {
				$html .= __( 'Free Shipping', 'woocommerce-smart-coupons' );
			}
			$html .= '</div>';

			$html .= '<div class="code">' . $coupon_data['coupon_code'] . '</div>';

			if ( ! empty( $coupon_data['coupon_description'] ) ) {
				$html .= '<div class="discount-description">' . $coupon_data['coupon_description'] . '</div>';
			}

			if ( ! empty( $coupon_data['expiry_date'] ) ) {
				$html .= '<div class="coupon-expire">' . $coupon_data['expiry_date'] . '</div>';
			} else {
				$html .= '<div class="coupon-expire">' . __( 'Never Expires', 'woocommerce-smart-coupons' ) . '</div>';
			}

			$html .= '</div></div>';

			return $html;

		}

		/**
		 * Endpoint HTML content.
		 * To show available coupons on My Account page
		 */
		public function sc_endpoint_content() {
			global $store_credit_label, $woocommerce_smart_coupon;

			$coupons = $this->sc_get_available_coupons_list( array() );

			if ( empty( $coupons ) ) {
				?>
				<div class="woocommerce-Message woocommerce-Message--info woocommerce-info">
					<?php echo esc_html__( 'Sorry, No coupons available for you.', 'woocommerce-smart-coupons' ); ?>
				</div>
				<?php
				return false;
			}

			if ( ! wp_style_is( 'smart-coupon' ) ) {
				wp_enqueue_style( 'smart-coupon' );
			}

			$coupons_applied = ( is_object( WC()->cart ) && is_callable( array( WC()->cart, 'get_applied_coupons' ) ) ) ? WC()->cart->get_applied_coupons() : array();

			$available_coupons_heading = get_option( 'smart_coupon_myaccount_page_text' );

			/* translators: %s: plural name for store credit */
			$available_coupons_heading = ( ! empty( $available_coupons_heading ) ) ? $available_coupons_heading : ( ! empty( $store_credit_label['plural'] ) ? sprintf( __( 'Available Coupons & %s', 'woocommerce-smart-coupons' ), ucwords( $store_credit_label['plural'] ) ) : __( 'Available Coupons & Store Credits', 'woocommerce-smart-coupons' ) );

			$design           = get_option( 'wc_sc_setting_coupon_design', 'round-dashed' );
			$background_color = get_option( 'wc_sc_setting_coupon_background_color', '#39cccc' );
			$foreground_color = get_option( 'wc_sc_setting_coupon_foreground_color', '#30050b' );

			?>
			<style type="text/css"><?php echo $this->get_coupon_styles( $design ); // phpcs:ignore ?></style>
			<style type="text/css">
				.coupon-container.left:before,
				.coupon-container.bottom:before {
					background: <?php echo esc_html( $foreground_color ); ?> !important;
				}
				.coupon-container.left:hover, .coupon-container.left:focus, .coupon-container.left:active,
				.coupon-container.bottom:hover, .coupon-container.bottom:focus, .coupon-container.bottom:active {
					color: <?php echo esc_html( $background_color ); ?> !important;
				}
			</style>
			<h2><?php echo esc_html__( stripslashes( $available_coupons_heading ), 'woocommerce-smart-coupons' ); // phpcs:ignore ?></h2>
			<p><?php echo esc_html__( 'List of coupons which are valid & available for use. Click on the coupon to use it. The coupon discount will be visible only when at least one product is present in the cart.', 'woocommerce-smart-coupons' ); ?></p>

			<div class="woocommerce-Message woocommerce-Message--info woocommerce-info" style="display:none;">
				<?php echo esc_html__( 'Sorry, No coupons available for you.', 'woocommerce-smart-coupons' ); ?>
			</div>

			<?php

			$coupon_block_data = array(
				'smart_coupons'   => array(
					'html' => array(),
				),
				'valid_coupons'   => array(
					'html' => array(),
				),
				'invalid_coupons' => array(
					'html' => array(),
				),
			);

			$total_store_credit = 0;
			$coupons_to_print   = array();

			foreach ( $coupons as $code ) {

				if ( in_array( $code->post_title, $coupons_applied, true ) ) {
					continue;
				}

				$coupon = new WC_Coupon( $code->post_title );

				if ( $this->is_wc_gte_30() ) {
					if ( ! is_object( $coupon ) || ! is_callable( array( $coupon, 'get_id' ) ) ) {
						continue;
					}
					$coupon_id = $coupon->get_id();
					if ( empty( $coupon_id ) ) {
						continue;
					}
					$coupon_amount    = $coupon->get_amount();
					$is_free_shipping = ( $coupon->get_free_shipping() ) ? 'yes' : 'no';
					$discount_type    = $coupon->get_discount_type();
					$expiry_date      = $coupon->get_date_expires();
					$coupon_code      = $coupon->get_code();
				} else {
					$coupon_id        = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
					$coupon_amount    = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
					$is_free_shipping = ( ! empty( $coupon->free_shipping ) ) ? $coupon->free_shipping : '';
					$discount_type    = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
					$expiry_date      = ( ! empty( $coupon->expiry_date ) ) ? $coupon->expiry_date : '';
					$coupon_code      = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
				}

				if ( $this->is_wc_gte_30() && $expiry_date instanceof WC_DateTime ) {
					$expiry_date = $expiry_date->getTimestamp();
				} elseif ( ! is_int( $expiry_date ) ) {
					$expiry_date = strtotime( $expiry_date );
				}

				if ( ! empty( $expiry_date ) && is_int( $expiry_date ) ) {
					$expiry_time = (int) get_post_meta( $coupon_id, 'wc_sc_expiry_time', true );
					if ( ! empty( $expiry_time ) ) {
						$expiry_date += $expiry_time; // Adding expiry time to expiry date.
					}
				}

				if ( empty( $discount_type ) ) {
					continue;
				}

				$coupon_post = get_post( $coupon_id );

				$coupon_data = $this->get_coupon_meta_data( $coupon );

				$block_data                     = array();
				$block_data['coupon_code']      = $coupon_code;
				$block_data['coupon_amount']    = $coupon_data['coupon_amount'];
				$block_data['coupon_type']      = $coupon_data['coupon_type'];
				$block_data['is_free_shipping'] = $is_free_shipping;

				$show_coupon_description = get_option( 'smart_coupons_show_coupon_description', 'no' );
				if ( ! empty( $coupon_post->post_excerpt ) && 'yes' === $show_coupon_description ) {
					$block_data['coupon_description'] = $coupon_post->post_excerpt;
				}

				if ( ! empty( $expiry_date ) ) {
					$block_data['expiry_date'] = $this->get_expiration_format( $expiry_date );
				} else {
					$block_data['expiry_date'] = '';
				}

				$show_as_valid = apply_filters( 'wc_sc_show_as_valid', $coupon->is_valid(), array( 'coupon_obj' => $coupon ) );

				if ( true === $show_as_valid ) {
					$coupons_to_print[] = $block_data['coupon_code'];
					$html               = $this->get_coupon_html( $block_data );
					if ( 'smart_coupon' === $discount_type ) {
						$total_store_credit                          += $coupon_amount;
						$coupon_block_data['smart_coupons']['html'][] = $html;
					} else {
						$coupon_block_data['valid_coupons']['html'][] = $html;
					}
				} else {
					$block_data['is_invalid'] = 'yes';
					$html                     = $this->get_coupon_html( $block_data );
					$coupon_block_data['invalid_coupons']['html'][] = $html;
				}
			}

			$coupon_block_data['smart_coupons']['total'] = $total_store_credit;

			$is_print = get_option( 'smart_coupons_is_print_coupon', 'yes' );
			$is_print = apply_filters( 'wc_sc_myaccount_show_print_button', wc_string_to_bool( $is_print ), array( 'source' => $woocommerce_smart_coupon ) );

			if ( true === $is_print && ! empty( $coupons_to_print ) ) {
				$print_url = add_query_arg(
					array(
						'print-coupons' => 'yes',
						'source'        => 'wc-smart-coupons',
						'coupon-codes'  => implode(
							',',
							$coupons_to_print
						),
					)
				);
				?>
				<span class="wc_sc_coupon_actions_wrapper">
					<a target="_blank" href="<?php echo esc_url( $print_url ); ?>" class="button"><?php echo esc_html( _n( 'Print coupon', 'Print coupons', count( $coupons_to_print ), 'woocommerce-smart-coupons' ) ); ?></a>
				</span>
				<?php
			}
			?>
			<div id='sc_coupons_list'>
				<h4><?php echo ! empty( $store_credit_label['plural'] ) ? esc_html( ucwords( $store_credit_label['plural'] ) ) : esc_html__( 'Store Credits', 'woocommerce-smart-coupons' ); ?></h4>
				<div id="all_coupon_container">
					<?php

					$smart_coupons_block = '';

					if ( ! empty( $coupon_block_data['smart_coupons']['html'] ) ) {
						$smart_coupons_block = implode( '', $coupon_block_data['smart_coupons']['html'] );
					}

					$smart_coupons_block = trim( $smart_coupons_block );

					if ( ! empty( $smart_coupons_block ) ) {
						echo $smart_coupons_block; // phpcs:ignore
					}

					?>
				</div>
				<?php
				if ( ! empty( $coupon_block_data['smart_coupons']['total'] ) && 0 !== $coupon_block_data['smart_coupons']['total'] ) {
					?>
					<div class="wc_sc_total_available_store_credit"><?php echo esc_html__( 'Total Credit Amount', 'woocommerce-smart-coupons' ) . ': ' . wc_price( $coupon_block_data['smart_coupons']['total'] ); // phpcs:ignore ?></div>
					<?php
				}
				?>
			<br>
			</div>
			<div id='coupons_list'>
				<h4><?php echo esc_html__( 'Discount Coupons', 'woocommerce-smart-coupons' ); ?></h4>
				<div id="all_coupon_container">
					<?php

					$valid_coupons_block = '';

					if ( ! empty( $coupon_block_data['valid_coupons']['html'] ) ) {
						$valid_coupons_block = implode( '', $coupon_block_data['valid_coupons']['html'] );
					}

					$valid_coupons_block = trim( $valid_coupons_block );

					if ( ! empty( $valid_coupons_block ) ) {
						echo $valid_coupons_block; // phpcs:ignore
					}

					?>
				</div>
			</div>
			<br>
			<?php
			// to show user specific coupons on My Account.
			$this->generated_coupon_details_before_my_account();

			$is_show_invalid_coupons = get_option( 'smart_coupons_show_invalid_coupons_on_myaccount', 'no' );
			if ( 'yes' === $is_show_invalid_coupons ) {
				?>
				<br>
				<div id='invalid_coupons_list'>
					<h2><?php echo esc_html__( 'Invalid / Used Coupons', 'woocommerce-smart-coupons' ); ?></h2>
					<p><?php echo esc_html__( 'List of coupons which can not be used. The reason can be based on its usage restrictions, usage limits, expiry date.', 'woocommerce-smart-coupons' ); ?></p>
					<div id="all_coupon_container">
						<?php

						$invalid_coupons_block = '';

						if ( ! empty( $coupon_block_data['invalid_coupons']['html'] ) ) {
							$invalid_coupons_block = implode( '', $coupon_block_data['invalid_coupons']['html'] );
						}

						$invalid_coupons_block = trim( $invalid_coupons_block );

						if ( ! empty( $invalid_coupons_block ) ) {
							echo $invalid_coupons_block; // phpcs:ignore
						}

						?>
					</div>
				</div>
				<?php
			}

			if ( did_action( 'wc_smart_coupons_frontend_styles_and_scripts' ) <= 0 || ! defined( 'DOING_AJAX' ) || DOING_AJAX !== true ) {
				$this->frontend_styles_and_scripts( array( 'page' => 'myaccount' ) );
			}

			$js = "var total_store_credit = '" . $total_store_credit . "';
					if ( total_store_credit == 0 ) {
						jQuery('#sc_coupons_list').hide();
					}

					jQuery( document ).ready(function() {
						if( jQuery('div#all_coupon_container').children().length == 0 ) {
							jQuery('#coupons_list').hide();
						}
					});

					jQuery( document ).ready(function() {
						if( jQuery('div.woocommerce-MyAccount-content').children().length == 0 ) {
							jQuery('.woocommerce-MyAccount-content').append(jQuery('.woocommerce-Message.woocommerce-Message--info.woocommerce-info'));
							jQuery('.woocommerce-Message.woocommerce-Message--info.woocommerce-info').show();
						}
					});

					/* to show scroll bar for core coupons */
					var coupons_list = jQuery('#coupons_list');
					var coupons_list_height = coupons_list.height();

					if ( coupons_list_height > 400 ) {
						coupons_list.css('height', '400px');
						coupons_list.css('overflow-y', 'scroll');
					} else {
						coupons_list.css('height', '');
						coupons_list.css('overflow-y', '');
					}
			";

			wc_enqueue_js( $js );

		}

		/**
		 * Function to get available coupons list
		 *
		 * @param array $coupons The coupons.
		 * @return array Modified coupons.
		 */
		public function sc_get_available_coupons_list( $coupons = array() ) {

			global $wpdb;

			$global_coupons = array();

			$wpdb->query( $wpdb->prepare( 'SET SESSION group_concat_max_len=%d', 999999 ) ); // phpcs:ignore

			$global_coupons = wp_cache_get( 'wc_sc_global_coupons', 'woocommerce_smart_coupons' );

			if ( false === $global_coupons ) {
				$global_coupons = $wpdb->get_results( // phpcs:ignore
					$wpdb->prepare(
						"SELECT *
							FROM {$wpdb->prefix}posts
							WHERE FIND_IN_SET (ID, (SELECT GROUP_CONCAT(option_value SEPARATOR ',') FROM {$wpdb->prefix}options WHERE option_name = %s)) > 0
							GROUP BY ID
							ORDER BY post_date DESC",
						'sc_display_global_coupons'
					)
				);
				wp_cache_set( 'wc_sc_global_coupons', $global_coupons, 'woocommerce_smart_coupons' );
				$this->maybe_add_cache_key( 'wc_sc_global_coupons' );
			}

			$global_coupons = apply_filters( 'wc_smart_coupons_global_coupons', $global_coupons );

			if ( is_user_logged_in() ) {

				global $current_user;

				if ( ! empty( $current_user->user_email ) && ! empty( $current_user->ID ) ) {

					$count_option_current_user = wp_cache_get( 'wc_sc_current_users_option_name_' . $current_user->ID, 'woocommerce_smart_coupons' );

					if ( false === $count_option_current_user ) {
						$count_option_current_user = $wpdb->get_col( // phpcs:ignore
							$wpdb->prepare(
								"SELECT option_name
									FROM {$wpdb->prefix}options
									WHERE option_name LIKE %s
									ORDER BY option_id DESC",
								$wpdb->esc_like( 'sc_display_custom_credit_' . $current_user->ID . '_' ) . '%'
							)
						);
						wp_cache_set( 'wc_sc_current_users_option_name_' . $current_user->ID, $count_option_current_user, 'woocommerce_smart_coupons' );
						$this->maybe_add_cache_key( 'wc_sc_current_users_option_name_' . $current_user->ID );
					}

					if ( count( $count_option_current_user ) > 0 ) {
						$count_option_current_user = substr( strrchr( $count_option_current_user[0], '_' ), 1 );
						$count_option_current_user = ( ! empty( $count_option_current_user ) ) ? $count_option_current_user + 2 : 1;
					} else {
						$count_option_current_user = 1;
					}

					$option_nm = 'sc_display_custom_credit_' . $current_user->ID . '_' . $count_option_current_user;
					$wpdb->query( $wpdb->prepare( 'SET SESSION group_concat_max_len=%d', 999999 ) ); // phpcs:ignore
					$wpdb->delete( $wpdb->prefix . 'options', array( 'option_name' => $option_nm ) ); // WPCS: db call ok.
					$wpdb->query( // phpcs:ignore
						$wpdb->prepare(
							"INSERT INTO {$wpdb->prefix}options (option_name, option_value, autoload)
								SELECT %s,
									GROUP_CONCAT(id SEPARATOR ','),
									%s
								FROM {$wpdb->prefix}posts
								WHERE post_type = %s
									AND post_status = %s",
							$option_nm,
							'no',
							'shop_coupon',
							'publish'
						)
					);

					$wpdb->query( // phpcs:ignore
						$wpdb->prepare(
							"UPDATE {$wpdb->prefix}options
								SET option_value = (SELECT GROUP_CONCAT(post_id SEPARATOR ',')
													FROM {$wpdb->prefix}postmeta
													WHERE meta_key = %s
														AND CAST(meta_value AS CHAR) LIKE %s
														AND FIND_IN_SET(post_id, (SELECT option_value FROM (SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = %s) as temp )) > 0 )
								WHERE option_name = %s",
							'customer_email',
							'%' . $wpdb->esc_like( $current_user->user_email ) . '%',
							$option_nm,
							$option_nm
						)
					);

					$wpdb->query( // phpcs:ignore
						$wpdb->prepare(
							"UPDATE {$wpdb->prefix}options
								SET option_value = (SELECT GROUP_CONCAT(post_id SEPARATOR ',')
													FROM {$wpdb->prefix}postmeta
													WHERE meta_key = %s
														AND CAST(meta_value AS SIGNED) >= '0'
														AND FIND_IN_SET(post_id, (SELECT option_value FROM (SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = %s) as temp )) > 0 )
								WHERE option_name = %s",
							'coupon_amount',
							$option_nm,
							$option_nm
						)
					);

					$coupons = wp_cache_get( 'wc_sc_all_coupon_id_for_user_' . $current_user->ID, 'woocommerce_smart_coupons' );

					if ( false === $coupons ) {
						$coupons = $wpdb->get_results( // phpcs:ignore
							$wpdb->prepare(
								"SELECT *
									FROM {$wpdb->prefix}posts
									WHERE FIND_IN_SET (ID, (SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = %s)) > 0
									GROUP BY ID
									ORDER BY post_date DESC",
								$option_nm
							)
						);
						wp_cache_set( 'wc_sc_all_coupon_id_for_user_' . $current_user->ID, $coupons, 'woocommerce_smart_coupons' );
						$this->maybe_add_cache_key( 'wc_sc_all_coupon_id_for_user_' . $current_user->ID );
					}

					$wpdb->query( // phpcs:ignore
						$wpdb->prepare(
							"DELETE FROM {$wpdb->prefix}options WHERE option_name = %s",
							$option_nm
						)
					);
				}
			}

			$coupons = array_merge( $coupons, $global_coupons );

			$unique_id_to_code = array_unique( array_reverse( wp_list_pluck( $coupons, 'post_title', 'ID' ), true ) );

			$unique_ids = array_map( 'absint', array_keys( $unique_id_to_code ) );

			foreach ( $coupons as $index => $coupon ) {
				if ( empty( $coupon->ID ) || ! in_array( absint( $coupon->ID ), $unique_ids, true ) ) {
					unset( $coupons[ $index ] );
				}
			}

			return $coupons;

		}

		/**
		 * Include frontend styles & scripts
		 *
		 * @param array $args Arguments.
		 */
		public function frontend_styles_and_scripts( $args = array() ) {

			if ( empty( $args['page'] ) ) {
				return;
			}

			$js = " 	jQuery('div').on('click', '.apply_coupons_credits', function() {

							coupon_code = jQuery(this).find('div.code').text();

							if( coupon_code != '' && coupon_code != undefined ) {

								jQuery(this).css('opacity', '0.5');
								var url = '" . trailingslashit( home_url() ) . ( ( strpos( home_url(), '?' ) === false ) ? '?' : '&' ) . ( ( ! empty( $args['page'] ) ) ? 'sc-page=' . $args['page'] : '' ) . "&coupon-code='+coupon_code;
								jQuery(location).attr('href', url);

							}
						});

						var show_hide_coupon_list = function() {
							if ( jQuery('div#coupons_list').find('div.coupon-container').length > 0 ) {
								jQuery('div#coupons_list').slideDown(800);
							} else {
								jQuery('div#coupons_list').hide();
							}
						};

						var coupon_container_height = jQuery('#all_coupon_container').height();
						if ( coupon_container_height > 400 ) {
							jQuery('#all_coupon_container').css('height', '400px');
							jQuery('#all_coupon_container').css('overflow-y', 'scroll');
						} else {
							jQuery('#all_coupon_container').css('height', '');
							jQuery('#all_coupon_container').css('overflow-y', '');
						}

						jQuery('.checkout_coupon').next('#coupons_list').hide();

						jQuery('a.showcoupon').on('click', function() {
							show_hide_coupon_list();
						});

						jQuery(document).on('ready', function(){
							jQuery('div#invalid_coupons_list div#all_coupon_container .coupon-container').removeClass('apply_coupons_credits');
						});

					";

			if ( is_checkout() ) {
				$js .= "
						jQuery(document.body).on('updated_checkout', function( e, data ){
							try {
								if ( data.fragments.wc_sc_available_coupons ) {
									jQuery('div#coupons_list').replaceWith( data.fragments.wc_sc_available_coupons );
								}
							} catch(e) {}
							show_hide_coupon_list();
						});
						";
			} else {
				$js .= '
						show_hide_coupon_list();
						';
			}

			if ( $this->is_wc_gte_26() ) {
				$js .= "
						jQuery(document.body).on('updated_cart_totals update_checkout', function(){
							jQuery('div#coupons_list').css('opacity', '0.5');
							jQuery.ajax({
								url: '" . admin_url( 'admin-ajax.php' ) . "',
								type: 'post',
								dataType: 'html',
								data: {
									action: 'sc_get_available_coupons',
									security: '" . wp_create_nonce( 'sc-get-available-coupons' ) . "'
								},
								success: function( response ) {
									if ( response != undefined && response != '' ) {
										jQuery('div#coupons_list').replaceWith( response );
									}
									show_hide_coupon_list();
									jQuery('div#coupons_list').css('opacity', '1');
								}
							});
						});";
			} else {
				$js .= "
						jQuery('body').on( 'update_checkout', function( e ){
							var coupon_code = jQuery('.woocommerce-remove-coupon').data( 'coupon' );
							if ( coupon_code != undefined && coupon_code != '' ) {
								jQuery('div[name=\"'+coupon_code+'\"].apply_coupons_credits').show();
							}
						});";
			}

			wc_enqueue_js( $js );

			do_action( 'wc_smart_coupons_frontend_styles_and_scripts' );

		}

		/**
		 * Generate & add available coupons fragments
		 *
		 * @param  array $fragments Existing fragments.
		 * @return array $fragments
		 */
		public function woocommerce_update_order_review_fragments( $fragments = array() ) {

			if ( ! empty( $_POST['post_data'] ) ) { // phpcs:ignore
				wp_parse_str( $_POST['post_data'], $posted_data ); // phpcs:ignore
				if ( empty( $_REQUEST['billing_email'] ) && ! empty( $posted_data['billing_email'] ) ) { // phpcs:ignore
					$_REQUEST['billing_email'] = $posted_data['billing_email'];
				}
			}

			ob_start();
			$this->show_available_coupons_before_checkout_form();
			$fragments['wc_sc_available_coupons'] = ob_get_clean();

			return $fragments;
		}

		/**
		 * Get date expires if not exists
		 *
		 * @param  mixed|WC_DateTime $value  The date expires value.
		 * @param  WC_Coupon         $coupon The coupon object.
		 * @return mixed|WC_DateTime
		 */
		public function wc_sc_get_date_expires( $value = null, $coupon = null ) {

			if ( $this->is_wc_gte_30() && empty( $value ) ) {
				$coupon_id   = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_id' ) ) ) ? $coupon->get_id() : 0;
				$expiry_date = ( ! empty( $coupon_id ) ) ? get_post_meta( $coupon_id, 'expiry_date', true ) : '';

				if ( ! empty( $expiry_date ) ) {
					$expiry_timestamp = strtotime( $expiry_date );
					if ( false !== $expiry_timestamp ) {
						$value = new WC_DateTime( "@{$expiry_timestamp}", new DateTimeZone( 'UTC' ) );
					}
				}
			}

			return $value;
		}

		/**
		 * Get endpoint
		 *
		 * @return string The endpoint
		 */
		public static function get_endpoint() {
			self::$endpoint = get_option( 'woocommerce_myaccount_wc_sc_dashboard_endpoint', 'wc-smart-coupons' );
			return self::$endpoint;
		}

		/**
		 * Hooks for handle endpoint
		 */
		public function endpoint_hooks() {
			if ( empty( self::$endpoint ) ) {
				self::$endpoint = self::get_endpoint();
			}
			if ( $this->is_wc_gte_34() ) {
				add_filter( 'woocommerce_get_settings_advanced', array( $this, 'add_endpoint_account_settings' ) );
			} else {
				add_filter( 'woocommerce_account_settings', array( $this, 'add_endpoint_account_settings' ) );
			}
		}

		/**
		 * Add UI option for changing Smart Coupons endpoints in WC settings
		 *
		 * @param mixed $settings Existing settings.
		 * @return mixed $settings
		 */
		public function add_endpoint_account_settings( $settings ) {

			$sc_endpoint_setting = array(
				'title'    => __( 'Coupons', 'woocommerce-smart-coupons' ),
				'desc'     => __( 'Endpoint for the My Account &rarr; Coupons page', 'woocommerce-smart-coupons' ),
				'id'       => 'woocommerce_myaccount_wc_sc_dashboard_endpoint',
				'type'     => 'text',
				'default'  => 'wc-smart-coupons',
				'desc_tip' => true,
			);

			$after_key = 'woocommerce_myaccount_view_order_endpoint';

			$after_key = apply_filters(
				'wc_sc_endpoint_account_settings_after_key',
				$after_key,
				array(
					'settings' => $settings,
					'source'   => $this,
				)
			);

			WC_Smart_Coupons::insert_setting_after( $settings, $after_key, $sc_endpoint_setting );

			return $settings;
		}

		/**
		 * Fetch generated coupon's details
		 *
		 * Either order_ids or user_ids required
		 *
		 * @param array|int $order_ids Order IDs.
		 * @param array|int $user_ids User IDs.
		 * @param boolean   $html Whether to return only data or html code, optional, default:false.
		 * @param boolean   $header Whether to add a header above the list of generated coupon details, optional, default:false.
		 * @param string    $layout Possible values 'box' or 'table' layout to show generated coupons details, optional, default:box.
		 *
		 * @return array $generated_coupon_data associative array containing generated coupon's details
		 */
		public function get_generated_coupon_data( $order_ids = '', $user_ids = '', $html = false, $header = false, $layout = 'box' ) {
			global $wpdb;

			if ( ! is_array( $order_ids ) ) {
				$order_ids = ( ! empty( $order_ids ) ) ? array( $order_ids ) : array();
			}

			if ( ! is_array( $user_ids ) ) {
				$user_ids = ( ! empty( $user_ids ) ) ? array( $user_ids ) : array();
			}

			$user_order_ids = array();

			if ( ! empty( $user_ids ) ) {

				$user_order_ids_query = $wpdb->prepare(
					"SELECT DISTINCT postmeta.post_id FROM {$wpdb->prefix}postmeta AS postmeta
						WHERE postmeta.meta_key = %s
						AND postmeta.meta_value",
					'_customer_user'
				);

				if ( count( $user_ids ) === 1 ) {
					$user_order_ids_query .= $wpdb->prepare( ' = %d', current( $user_ids ) );
				} else {
					$how_many              = count( $user_ids );
					$placeholders          = array_fill( 0, $how_many, '%d' );
					$user_order_ids_query .= $wpdb->prepare( ' IN ( ' . implode( ',', $placeholders ) . ' )', $user_ids ); // phpcs:ignore
				}

				$unique_user_ids = array_unique( $user_ids );

				$user_order_ids = wp_cache_get( 'wc_sc_order_ids_by_user_id_' . implode( '_', $unique_user_ids ), 'woocommerce_smart_coupons' );

				if ( false === $user_order_ids ) {
					$user_order_ids = $wpdb->get_col( $user_order_ids_query ); // phpcs:ignore
					wp_cache_set( 'wc_sc_order_ids_by_user_id_' . implode( '_', $unique_user_ids ), $user_order_ids, 'woocommerce_smart_coupons' );
					$this->maybe_add_cache_key( 'wc_sc_order_ids_by_user_id_' . implode( '_', $unique_user_ids ) );
				}
			}

			$new_order_ids = array_unique( array_merge( $user_order_ids, $order_ids ) );

			$generated_coupon_data = array();
			foreach ( $new_order_ids as $id ) {
				$data = get_post_meta( $id, 'sc_coupon_receiver_details', true );
				if ( empty( $data ) ) {
					continue;
				}
				$from = get_post_meta( $id, '_billing_email', true );
				if ( empty( $generated_coupon_data[ $from ] ) ) {
					$generated_coupon_data[ $from ] = array();
				}
				$generated_coupon_data[ $from ] = array_merge( $generated_coupon_data[ $from ], $data );
			}

			if ( empty( $generated_coupon_data ) ) {
				return;
			}

			if ( $html ) {

				ob_start();
				if ( 'table' === $layout ) {
					$this->get_generated_coupon_data_table( $generated_coupon_data );
				} else {
					$this->get_generated_coupon_data_box( $generated_coupon_data );
				}
				$coupon_details_html_content = ob_get_clean();

				$found_coupon = ( 'table' === $layout ) ? ( strpos( $coupon_details_html_content, 'coupon_received_row' ) !== false ) : ( strpos( $coupon_details_html_content, '<details' ) !== false );

				if ( $found_coupon ) {

					echo '<div id="generated_coupon_data_container" style="padding: 2em 0 2em;">';

					if ( $header ) {
						echo '<h2>' . esc_html__( 'Coupon Received', 'woocommerce-smart-coupons' ) . '</h2>';
						echo '<p>' . esc_html__( 'List of coupons & their details which you have received from the store. Click on the coupon to see the details.', 'woocommerce-smart-coupons' ) . '</p>';
					}

					echo $coupon_details_html_content; // phpcs:ignore

					echo '</div>';

				}

				return;

			}

			return $generated_coupon_data;
		}

		/**
		 * HTML code to display generated coupon's data in box layout
		 *
		 * @param array $generated_coupon_data Associative array containing generated coupon's details.
		 */
		public function get_generated_coupon_data_box( $generated_coupon_data = array() ) {
			if ( empty( $generated_coupon_data ) ) {
				return;
			}
			$email = $this->get_current_user_email();
			$js    = "
					var switchMoreLess = function() {
						var total = jQuery('details').length;
						var open = jQuery('details[open]').length;
						if ( open == total ) {
							jQuery('a#more_less').text('" . __( 'Less details', 'woocommerce-smart-coupons' ) . "');
						} else {
							jQuery('a#more_less').text('" . __( 'More details', 'woocommerce-smart-coupons' ) . "');
						}
					};
					switchMoreLess();

					jQuery('a#more_less').on('click', function(){
						var current = jQuery('details').attr('open');
						if ( current == '' || current == undefined ) {
							jQuery('details').attr('open', 'open');
							jQuery('a#more_less').text('" . __( 'Less details', 'woocommerce-smart-coupons' ) . "');
						} else {
							jQuery('details').removeAttr('open');
							jQuery('a#more_less').text('" . __( 'More details', 'woocommerce-smart-coupons' ) . "');
						}
					});

					jQuery('summary.generated_coupon_summary').on('mouseup', function(){
						setTimeout( switchMoreLess, 10 );
					});

					jQuery('span.expand_collapse').show();

					var generated_coupon_element = jQuery('#all_generated_coupon');
					var generated_coupon_container_height = generated_coupon_element.height();
					if ( generated_coupon_container_height > 400 ) {
						generated_coupon_element.css('height', '400px');
						generated_coupon_element.css('overflow-y', 'scroll');
					} else {
						generated_coupon_element.css('height', '');
						generated_coupon_element.css('overflow-y', '');
					}

					jQuery('#all_generated_coupon').on('click', '.coupon-container', function(){
						setTimeout(function(){
							var current_element = jQuery(this).find('details');
							var is_open = current_element.attr('open');
							if ( is_open == '' || is_open == undefined ) {
									current_element.attr('open', 'open');
							} else {
								current_element.removeAttr('open');
							}
						}, 1);
					});

				";

			wc_enqueue_js( $js );

			?>
			<style type="text/css">
				.coupon-container {
					margin: .2em;
					box-shadow: 0 0 5px #e0e0e0;
					display: inline-table;
					text-align: center;
					cursor: pointer;
					max-width: 49%;
					padding: .55em;
					line-height: 1.4em;
				}
				.coupon-container.previews { cursor: inherit }

				.coupon-content {
					padding: 0.2em 1.2em;
				}

				.coupon-content .code {
					font-family: monospace;
					font-size: 1.2em;
					font-weight:700;
				}

				.coupon-content .coupon-expire,
				.coupon-content .discount-info {
					font-family: Helvetica, Arial, sans-serif;
					font-size: 1em;
				}
				.coupon-content .discount-description {
					font: .7em/1 Helvetica, Arial, sans-serif;
					width: 250px;
					margin: 10px inherit;
					display: inline-block;
				}

				.generated_coupon_details { padding: 0.6em 1em 0.4em 1em; text-align: left; }
				.generated_coupon_data { border: solid 1px lightgrey; margin-bottom: 5px; margin-right: 5px; width: 50%; }
				.generated_coupon_details p { margin: 0; }
				span.expand_collapse { text-align: right; display: block; margin-bottom: 1em; cursor: pointer; }
				.float_right_block { float: right; }
				summary::-webkit-details-marker { display: none; }
				details[open] summary::-webkit-details-marker { display: none; }
				span.wc_sc_coupon_actions_wrapper {
					display: block;
					text-align: right;
				}
			</style>
			<?php
				$design           = get_option( 'wc_sc_setting_coupon_design', 'round-dashed' );
				$background_color = get_option( 'wc_sc_setting_coupon_background_color', '#39cccc' );
				$foreground_color = get_option( 'wc_sc_setting_coupon_foreground_color', '#30050b' );
			?>
			<style type="text/css"><?php echo $this->get_coupon_styles( $design ); // phpcs:ignore ?></style>
			<style type="text/css">
				.coupon-container.left:before,
				.coupon-container.bottom:before {
					background: <?php echo esc_html( $foreground_color ); ?> !important;
				}
				.coupon-container.left:hover, .coupon-container.left:focus, .coupon-container.left:active,
				.coupon-container.bottom:hover, .coupon-container.bottom:focus, .coupon-container.bottom:active {
					color: <?php echo esc_html( $background_color ); ?> !important;
				}
			</style>

			<div class="generated_coupon_data_wrapper">
				<span class="expand_collapse" style="display: none;">
					<a id="more_less"><?php echo esc_html__( 'More details', 'woocommerce-smart-coupons' ); ?></a>
				</span>
				<div id="all_generated_coupon">
				<?php
				foreach ( $generated_coupon_data as $from => $data ) {
					foreach ( $data as $coupon_data ) {

						if ( ! is_admin() && ! empty( $coupon_data['email'] ) && ! empty( $email ) && $coupon_data['email'] !== $email ) {
							continue;
						}

						$coupon = new WC_Coupon( $coupon_data['code'] );

						if ( $this->is_wc_gte_30() ) {
							if ( ! is_object( $coupon ) || ! is_callable( array( $coupon, 'get_id' ) ) ) {
								continue;
							}
							$coupon_id = $coupon->get_id();
							if ( empty( $coupon_id ) ) {
								continue;
							}
							$coupon_amount    = $coupon->get_amount();
							$is_free_shipping = ( $coupon->get_free_shipping() ) ? 'yes' : 'no';
							$discount_type    = $coupon->get_discount_type();
							$expiry_date      = $coupon->get_date_expires();
							$coupon_code      = $coupon->get_code();
						} else {
							$coupon_id        = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
							$coupon_amount    = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
							$is_free_shipping = ( ! empty( $coupon->free_shipping ) ) ? $coupon->free_shipping : '';
							$discount_type    = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
							$expiry_date      = ( ! empty( $coupon->expiry_date ) ) ? $coupon->expiry_date : '';
							$coupon_code      = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
						}

						if ( empty( $coupon_id ) || empty( $discount_type ) ) {
							continue;
						}

						$coupon_post = get_post( $coupon_id );

						$coupon_meta = $this->get_coupon_meta_data( $coupon );

						?>
						<div class="coupon-container <?php echo esc_attr( $this->get_coupon_container_classes() ); ?>" style="<?php echo $this->get_coupon_style_attributes(); // phpcs:ignore ?>">
							<details>
								<summary class="generated_coupon_summary">
									<?php
										echo '<div class="coupon-content ' . esc_attr( $this->get_coupon_content_classes() ) . '">
												<div class="discount-info">';

									$discount_title = '';

									if ( ! empty( $coupon_meta['coupon_amount'] ) && ! empty( $coupon_amount ) ) {
										$discount_title = $coupon_meta['coupon_amount'] . ' ' . $coupon_meta['coupon_type'];
									}

									$discount_title = apply_filters( 'wc_smart_coupons_display_discount_title', $discount_title, $coupon );

									if ( $discount_title ) {

										// Not escaping because 3rd party developer can have HTML code in discount title.
										echo $discount_title; // phpcs:ignore

										if ( 'yes' === $is_free_shipping ) {
											echo __( ' &amp; ', 'woocommerce-smart-coupons' ); // phpcs:ignore
										}
									}

									if ( 'yes' === $is_free_shipping ) {
										echo esc_html__( 'Free Shipping', 'woocommerce-smart-coupons' );
									}
										echo '</div>';

										echo '<div class="code">' . esc_html( $coupon_code ) . '</div>';

										$show_coupon_description = get_option( 'smart_coupons_show_coupon_description', 'no' );
									if ( ! empty( $coupon_post->post_excerpt ) && 'yes' === $show_coupon_description ) {
										echo '<div class="discount-description">' . esc_html( $coupon_post->post_excerpt ) . '</div>';
									}

									if ( ! empty( $expiry_date ) ) {

										$expiry_time = (int) get_post_meta( $coupon_id, 'wc_sc_expiry_time', true );
										if ( ! empty( $expiry_time ) ) {
											if ( $this->is_wc_gte_30() && $expiry_date instanceof WC_DateTime ) {
												$expiry_date = $expiry_date->getTimestamp();
											} elseif ( ! is_int( $expiry_date ) ) {
												$expiry_date = strtotime( $expiry_date );
											}
											$expiry_date += $expiry_time; // Adding expiry time to expiry date.
										}

										$expiry_date = $this->get_expiration_format( $expiry_date );

										echo '<div class="coupon-expire">' . esc_html( $expiry_date ) . '</div>';
									} else {

										echo '<div class="coupon-expire">' . esc_html__( 'Never Expires ', 'woocommerce-smart-coupons' ) . '</div>';
									}

										echo '</div>';
									?>
									</summary>
									<div class="generated_coupon_details">
									<p><strong><?php echo esc_html__( 'Sender', 'woocommerce-smart-coupons' ); ?>:</strong> <?php echo esc_html( $from ); ?></p>
										<p><strong><?php echo esc_html__( 'Receiver', 'woocommerce-smart-coupons' ); ?>:</strong> <?php echo esc_html( $coupon_data['email'] ); ?></p>
										<?php if ( ! empty( $coupon_data['message'] ) ) { ?>
											<p><strong><?php echo esc_html__( 'Message', 'woocommerce-smart-coupons' ); ?>:</strong> <?php echo esc_html( $coupon_data['message'] ); ?></p>
										<?php } ?>
									</div>
								</details>
							</div>
							<?php
					}
				}
				?>
				</div>
			</div>
			<?php
		}

		/**
		 * HTML code to display generated coupon's details is table layout
		 *
		 * @param array $generated_coupon_data Associative array of generated coupon's details.
		 */
		public function get_generated_coupon_data_table( $generated_coupon_data = array() ) {
			if ( empty( $generated_coupon_data ) ) {
				return;
			}
			$email = $this->get_current_user_email();
			?>
				<div class="woocommerce_order_items_wrapper">
					<table class="woocommerce_order_items">
						<thead>
							<tr>
								<th><?php echo esc_html__( 'Code', 'woocommerce-smart-coupons' ); ?></th>
								<th><?php echo esc_html__( 'Amount', 'woocommerce-smart-coupons' ); ?></th>
								<th><?php echo esc_html__( 'Receiver', 'woocommerce-smart-coupons' ); ?></th>
								<th><?php echo esc_html__( 'Message', 'woocommerce-smart-coupons' ); ?></th>
								<th><?php echo esc_html__( 'Sender', 'woocommerce-smart-coupons' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ( $generated_coupon_data as $from => $data ) {
								$email = ( ! empty( $email ) ) ? $email : $from;
								foreach ( $data as $coupon_data ) {
									if ( ! is_admin() && ! empty( $coupon_data['email'] ) && $coupon_data['email'] !== $email ) {
										continue;
									}
									echo '<tr class="coupon_received_row">';
									echo '<td>' . esc_html( $coupon_data['code'] ) . '</td>';
									echo '<td>' . wc_price( $coupon_data['amount'] ) . '</td>'; // phpcs:ignore
									echo '<td>' . esc_html( $coupon_data['email'] ) . '</td>';
									echo '<td>' . esc_html( $coupon_data['message'] ) . '</td>';
									echo '<td>' . esc_html( $from ) . '</td>';
									echo '</tr>';
								}
							}
							?>
						</tbody>
					</table>
				</div>
			<?php
		}

		/**
		 * Get current user's email
		 *
		 * @return string $email
		 */
		public function get_current_user_email() {
			$current_user = wp_get_current_user();
			if ( ! $current_user instanceof WP_User ) {
				return;
			}
			$billing_email = get_user_meta( $current_user->ID, 'billing_email', true );
			$email         = ( ! empty( $billing_email ) ) ? $billing_email : $current_user->user_email;
			return $email;
		}

		/**
		 * Display generated coupons details after Order table
		 *
		 * @param  WC_Order $order         The order.
		 * @param  boolean  $sent_to_admin Whether sent to admin.
		 * @param  boolean  $plain_text    Whether a plain text email.
		 */
		public function generated_coupon_details_after_order_table( $order = false, $sent_to_admin = false, $plain_text = false ) {

			if ( $this->is_wc_gte_30() ) {
				$order_id      = ( is_object( $order ) && is_callable( array( $order, 'get_id' ) ) ) ? $order->get_id() : 0;
				$order_refunds = ( ! empty( $order ) && is_callable( array( $order, 'get_refunds' ) ) ) ? $order->get_refunds() : array();
			} else {
				$order_id      = ( ! empty( $order->id ) ) ? $order->id : 0;
				$order_refunds = ( ! empty( $order->refunds ) ) ? $order->refunds : array();
			}

			if ( ! empty( $order_refunds ) ) {
				return;
			}

			if ( ! empty( $order_id ) ) {
				$this->get_generated_coupon_data( $order_id, '', true, true );
			}
		}

		/**
		 * Display generated coupons details on View Order page
		 *
		 * @param int $order_id The order id.
		 */
		public function generated_coupon_details_view_order( $order_id = 0 ) {
			if ( ! empty( $order_id ) ) {
				$this->get_generated_coupon_data( $order_id, '', true, true );
			}
		}

		/**
		 * Metabox on Order Edit Admin page to show generated coupons during the order
		 */
		public function add_generated_coupon_details() {
			global $post;

			if ( 'shop_order' !== $post->post_type ) {
				return;
			}

			add_meta_box( 'sc-generated-coupon-data', __( 'Coupon Sent', 'woocommerce-smart-coupons' ), array( $this, 'sc_generated_coupon_data_metabox' ), 'shop_order', 'normal' );
		}

		/**
		 * Metabox content (Generated coupon's details)
		 */
		public function sc_generated_coupon_data_metabox() {
			global $post;
			if ( ! empty( $post->ID ) ) {
				$this->get_generated_coupon_data( $post->ID, '', true, false );
			}
		}

		/**
		 * Modify available variation
		 *
		 * @param array $found_variation The found variation.
		 * @param mixed $product The variable product.
		 * @param mixed $variation The variation.
		 * @return array
		 */
		public function modify_available_variation( $found_variation = array(), $product = null, $variation = null ) {
			if ( is_a( $product, 'WC_Product_Variable' ) ) {
				if ( $this->is_wc_gte_30() ) {
					$product_id = ( is_object( $product ) && is_callable( array( $product, 'get_id' ) ) ) ? $product->get_id() : 0;
				} else {
					$product_id = ( ! empty( $product->id ) ) ? $product->id : 0;
				}

				$coupons = get_post_meta( $product_id, '_coupon_title', true );

				if ( ! empty( $coupons ) && $this->is_coupon_amount_pick_from_product_price( $coupons ) ) {
					if ( is_a( $variation, 'WC_Product_Variation' ) ) {
						$found_variation['price_including_tax']      = wc_round_discount( wc_get_price_including_tax( $variation ), 2 );
						$found_variation['price_including_tax_html'] = wc_price( $found_variation['price_including_tax'] );
						$found_variation['price_excluding_tax']      = wc_round_discount( wc_get_price_excluding_tax( $variation ), 2 );
						$found_variation['price_excluding_tax_html'] = wc_price( $found_variation['price_excluding_tax'] );
						if ( is_callable( array( $variation, 'get_regular_price' ) ) ) {
							$regular_price                                       = $variation->get_regular_price();
							$found_variation['regular_price_including_tax']      = wc_round_discount( wc_get_price_including_tax( $variation, array( 'price' => $regular_price ) ), 2 );
							$found_variation['regular_price_including_tax_html'] = wc_price( $found_variation['regular_price_including_tax'] );
							$found_variation['regular_price_excluding_tax']      = wc_round_discount( wc_get_price_excluding_tax( $variation, array( 'price' => $regular_price ) ), 2 );
							$found_variation['regular_price_excluding_tax_html'] = wc_price( $found_variation['regular_price_excluding_tax'] );
						}
					}
				}
			}
			return $found_variation;
		}

	}

}

WC_SC_Display_Coupons::get_instance();

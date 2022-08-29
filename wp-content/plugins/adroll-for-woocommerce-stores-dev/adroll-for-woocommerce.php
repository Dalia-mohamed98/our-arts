<?php
/*
* Plugin Name:         AdRoll for WooCommerce Stores
* Plugin URI:          https://www.adroll.com/
* Description:         This plugin easily integrates AdRoll with your WooCommerce site.
* Author:              AdRoll
* Version:             2.2.9
*/

class AdRoll_For_WooCommerce {
    static $instance = false;
    const ADROLL_BASE_URL = "https://app.adroll.com/";
    const PRODUCTS_ADDED_TO_CART_SESSION_KEY = 'products_added_to_cart';
    public $search_query = '';
    public $search_results = array();
    public $woocommerce_after_checkout_form_flag = false;
    public $order_id = null;
    public $products_added_to_cart = array();

    public function __construct() {
        add_action('admin_menu', array($this, 'admin_menu_action_callback'));
        add_action('admin_init', array($this, 'admin_init_action_callback'));
        add_action('admin_notices', array($this, 'admin_notices_action_callback'));
        add_action('wp_footer', array($this, 'wp_footer_action_callback'));
        add_action('woocommerce_after_checkout_form', array($this, 'woocommerce_after_checkout_form_action_callback'));
        add_action('woocommerce_checkout_order_processed', array($this, 'woocommerce_thankyou_action_callback'));
        add_action('woocommerce_thankyou', array($this, 'woocommerce_thankyou_action_callback'));
        add_action('pre_get_posts', array($this, 'pre_get_posts_action_callback'));
        add_action('activated_plugin', array($this, 'activated_plugin_action_callback'));
        add_action('deactivated_plugin', array($this, 'deactivated_plugin_action_callback'));
        add_action('rest_api_init', function() {
            register_rest_route('adroll/v1', '/configure', array(
                'methods' => 'GET',
                'callback' => array($this, 'configure_api_callback'),
            ));
        });

        add_filter('the_posts', array($this, 'the_posts_filter_callback'));
        add_filter('woocommerce_add_to_cart', array($this, 'woocommerce_add_to_cart_filter_callback'));
    }

    public function wp_footer_action_callback() {
        // Render AdRoll pixel in every page footer
        include_once(dirname( __FILE__ ).'/pixel.php');
    }

    public static function get_instance() {
        if (!self::$instance)
            self::$instance = new self;
        return self::$instance;
    }

    private function currency_format($amount) {
        // Format value to two decimal places with no thousands separator
        return number_format(floatval($amount), 2, '.', '');
    }

    private function get_return_url() {
        // Get AdRoll return url
        return self::ADROLL_BASE_URL . 'ecommerce/return';
    }

    private function get_register_url() {
        // Build AdRoll register url, which will redirect to the AdRoll buyflow after account creation
        $current_user = wp_get_current_user();
        $location = wc_get_base_location();
        $query_args = array(
            'email' => $current_user->user_email,
            'first_name' => $current_user->user_firstname,
            'last_name' => $current_user->user_lastname,
            'url' => get_site_url(),
            'country_code' => $location['country'],
            'next' => '/home/getting-started/'
        );
        return add_query_arg($query_args, self::ADROLL_BASE_URL . 'welcome/signup/?experiment=woocommerce_register');
    }

    private function get_adroll_admin_url() {
        // URL for this plugin's admin page
        return admin_url('admin.php?page=wp_adroll');
    }

    private function adroll_get_id($object) {
        // Backwards-compatible way fo getting woocommerce object id
        return method_exists($object, 'get_id') ? $object->get_id() : $object->id;
    }

    private function adroll_get_product($product_id) {
        // Backwards-compatible way of getting woocommerce product by id
        return function_exists('wc_get_product') ? wc_get_product($product_id) : get_product($product_id);
    }

    private function get_category($product) {
        // Backwards-compatible way of getting the first woocommerce product category assigned to a product that isn't 'uncategorized'
        if (method_exists($product, 'get_category_ids')) {
            foreach($product->get_category_ids() as $category_id) {
                $category_name = strtolower(get_term_by('id', $category_id, 'product_cat')->name);
                if ($category_name && $category_name !== 'uncategorized') {
                    return $category_name;
                }
            }
        } else {
            foreach(wp_get_post_terms($this->adroll_get_id($product), 'product_cat') as $term){
                if($term) {
                    $category_name = strtolower($term->name);
                    return $category_name !== 'uncategorized' ? $category_name : '';
                }
            }
        }
        return '';
    }

    public function admin_menu_action_callback() {
        // Register AdRoll admin page in the WordPress admin side menu
        $adroll_icon_svg_b64 = "data:image/svg+xml;base64,PHN2ZyBpZD0iTGF5ZXJfMSIgZGF0YS1uYW1lPSJMYXllciAxIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzNDAuOTIgMzA5LjI3Ij48ZGVmcz48c3R5bGU+LmNscy0xe2ZpbGw6IzBkYmRmZn08L3N0eWxlPjwvZGVmcz48dGl0bGU+QWRSb2xsIC0gTWFyayAtIFBvc2l0aXZlPC90aXRsZT48cGF0aCBjbGFzcz0iY2xzLTEiIGQ9Ik0zNDAuOTIgMTU0LjY0QzM0MC45MiA2OS4yMyAyNzMuODYgMCAxODYuMjkgMGExNTQuNTggMTU0LjU4IDAgMCAwLTEzNSA3OS4yNmw3NS4zOCA3NS4zOGMwLTMzIDI3LjgyLTU5LjY3IDU5LjY3LTU5LjY3QzIyMC4wOCA5NSAyNDYgMTIxLjY4IDI0NiAxNTQuNjRhNTkuMjkgNTkuMjkgMCAwIDEtNTkuNjYgNTkuNjZINTcuNzRBNTcuNzUgNTcuNzUgMCAwIDAgMCAyNzIuMDV2MzcuMjJoMTg2LjI5Yzg2LjkxIDAgMTU0LjYzLTY5LjIzIDE1NC42My0xNTQuNjMiLz48L3N2Zz4=";
        add_menu_page('AdRoll', 'AdRoll', 'manage_options', 'wp_adroll', array($this, 'render_adroll_admin_page'), $adroll_icon_svg_b64);
    }

    public function admin_init_action_callback() {
        // If the plugin has just been configured, notify the AdRoll backend that the plugin has been activated,
        // and then redirect users to AdRoll to complete installation flow
        if ($this->just_configured()) {
            $this->notify_plugin_activated();
            $adv = get_option('adroll_adv_eid');
            exit(wp_redirect($this->get_return_url()));
        }
        // Register AdRoll settings
        register_setting('AdRoll', 'adroll_adv_eid');
        register_setting('AdRoll', 'adroll_pixel_eid');
        add_settings_section('adroll_settings_section', null, null, 'wp_adroll');
        add_settings_field('adroll_adv_eid', null, array($this, 'adroll_adv_eid_callback'), 'wp_adroll', 'adroll_settings_section');
        add_settings_field('adroll_pixel_eid', null, array($this, 'adroll_pixel_eid_callback'), 'wp_adroll', 'adroll_settings_section');
    }

    public function admin_notices_action_callback() {
        // Show admin notice if the plugin has not been configured yet
        if (!$this->is_configured() && !$this->is_configuring()) {
            include_once(dirname( __FILE__ ).'/notice.php');
        }
    }

    private function just_configured() {
        // Determines if the AdRoll plugin configuration page was just submitted in this same request
        return (
            isset($_GET['page']) &&
            $_GET['page'] === 'wp_adroll' &&
            isset($_GET['settings-updated']) &&
            $_GET['settings-updated'] == 'true'
        );
    }

    public function adroll_adv_eid_callback() {
        // Render input for the adroll advertisable eid field in the config page
        $val = sanitize_user($_GET['adv']);
        echo '<input type="hidden" id="adroll_adv_eid" name="adroll_adv_eid" value="'.strtoupper(esc_html($val)).'" />';
    }

    public function adroll_pixel_eid_callback() {
        // Render input for the adroll pixel eid field in the config page
        $val = sanitize_user($_GET['pixel']);
        echo '<input type="hidden" id="adroll_pixel_eid" name="adroll_pixel_eid" value="'.strtoupper(esc_html($val)).'" />';
    }

    public function woocommerce_after_checkout_form_action_callback($checkout) {
        // Check if the checkout for is being rendered, and set a flag if so
        $this->woocommerce_after_checkout_form_flag = true;
    }

    public function woocommerce_thankyou_action_callback($order_id) {
        // Save id of the order being viewed so we can query for it by id later
        $this->order_id = $order_id;
    }

    public function pre_get_posts_action_callback($query) {
        // If a search query was made, save the search term for later use
        if ($query->is_search) {
            $this->search_query = $query->query['s'];
        }
    }

    public function activated_plugin_action_callback($plugin) {
        // When plugin gets activated, we redirect users to the plugin's settings page
        if($plugin == plugin_basename(__FILE__)) {
            if ($this->is_configured()) {
                $this->notify_plugin_activated();
            }
            if(!isset($_GET['activate-multi'])) {
                exit(wp_redirect($this->get_adroll_admin_url()));
            }
        }
    }

    public function deactivated_plugin_action_callback($plugin) {
        // When plugin gets deactivated, we notify the AdRoll backend
        if($plugin == plugin_basename(__FILE__) && $this->is_configured()) {
            $this->notify_plugin_deactivated();
        }
    }

    public function render_adroll_admin_page() {
        // Renders a template depending on the installation flow step
        if ($this->is_configuring()) {
            $template = 'configure.php';
        } else if ($this->is_configured()) {
            $template = 'status.php';
        } else {
            $template = 'welcome.php';
        }
        include_once($template);
    }

    private function is_configuring() {
        // Check if this request is
        $adv = isset($_GET['adv']);
        $pixel = isset($_GET['pixel']);
        return $adv && $pixel;
    }

    private function is_configured() {
        // Check if the plugin has already been configured (if the advertisable and pixel EIDs have been saved to the db)
        $adv_eid = trim((string)get_option('adroll_adv_eid'));
        $pixel_eid = trim((string)get_option('adroll_pixel_eid'));
        return !empty($adv_eid) && !empty($pixel_eid);
    }


    public function configure_api_callback($request) {
        // Public API handler that takes the necessary fields (adv, pixel) and redirects to a password-protected admin
        // page in which those fields will be permanently saved to the db
        $adv = strtoupper($request->get_param('adv'));
        $pixel = strtoupper($request->get_param('pixel'));
        $query_args = array('adv' => $adv, 'pixel' => $pixel);
        $url = add_query_arg($query_args, $this->get_adroll_admin_url());
        exit(wp_redirect($url));
    }

    public function the_posts_filter_callback($posts) {
        // Filter search results based on type, save array for later use but return original, unedited search results
        if (!empty($posts)) {
            $product_ids = array();
            foreach ($posts as $post) {
                if ($post->post_type === 'product') {
                    $product_ids[] = $post->ID;
                }
            }
            if (empty($this->search_results)) {
                $this->search_results = $product_ids;
            }
        }
        return $posts;
    }

    public function woocommerce_add_to_cart_filter_callback($added_item_key) {
        // Listen for add-to-cart events (ajax or not) and saves the list of products added to the cart.

        global $woocommerce;
        $cart = $woocommerce->cart->get_cart();
        $serialized_products = $this->serialize_cart_products($cart);

        // Get the ID of the product that was just added to the cart
        $added_product_id = null;
        foreach ($cart as $item_key => $item) {
            if ($item_key === $added_item_key) {
                $added_product_id = $item['product_id'];
                break;
            }
        }

        // If we could not find the product id using the item key, abort
        if ($added_product_id === null) {
            return;
        }

        // Only keep the serialized product whose ID is equal to the added product id
        $products_added_to_cart = array_values(array_filter($serialized_products, function($product) use ($added_product_id) {
            return (string)$product['product_id'] === (string) $added_product_id;
        }));

        if (is_ajax()) {
            // If this was an ajax add-to-cart, we save the products to the user session, since there will be no page
            // load with this requset/response cycle.
            $current_value = WC()->session->get(self::PRODUCTS_ADDED_TO_CART_SESSION_KEY, array());
            $new_value = array_merge($current_value, $products_added_to_cart);
            WC()->session->set(self::PRODUCTS_ADDED_TO_CART_SESSION_KEY, $new_value);
        } else {
            // If not an ajax add-to-cart, we save the products to the current object, which will be used in this same
            // request/response cycle to output the list on the next page load.
            $current_value = $this->products_added_to_cart;
            $new_value = array_merge($current_value, $products_added_to_cart);
            $this->products_added_to_cart = $new_value;
        }
    }

    private function get_products_added_to_cart() {
        // Gather all arrays of products added to the cart (from user sesion or otherwise)
        $products_added_to_cart = array();
        $products_added_to_cart = array_merge($products_added_to_cart, $this->products_added_to_cart);
        $products_added_to_cart = array_merge($products_added_to_cart, WC()->session->get(self::PRODUCTS_ADDED_TO_CART_SESSION_KEY, array()));
        WC()->session->set(self::PRODUCTS_ADDED_TO_CART_SESSION_KEY, array());
        return $products_added_to_cart;
    }

    private function serialize_product($product) {
        // Convert Woocommerce order object into array with only necessary product data
        $serialized_product = array();
        $serialized_product['product_id'] = (string)$this->adroll_get_id($product);
        $serialized_product['price'] = $this->currency_format($product->get_price());
        $serialized_product['category'] = $this->get_category($product);
        return $serialized_product;
    }

    private function serialize_cart_products($cart) {
        // Convert Woocommerce cart item objects into arrays of products with only necessary product data
        $products = array();
        foreach ($cart as $item) {
            $product = $this->adroll_get_product($item['product_id']);
            $serialized_product = $this->serialize_product($product);
            $serialized_product['quantity'] = (string)$item['quantity'];
            $products[] = $serialized_product;
        }
        return $products;
    }

    private function serialize_order_products($order) {
        // Converto Woocommerce order object into arrays of products with only necessary product data
        $products = array();
        foreach ($order->get_items() as $item) {
            $product = $this->adroll_get_product($item['product_id']);
            $serialized_product = $this->serialize_product($product);
            $quantity = floatval(array_key_exists('quantity', $item) ? $item['quantity'] : $item['qty']);
            $line_total = floatval($item['line_total']);
            $serialized_product['quantity'] = (string)$quantity;
            $serialized_product['price'] = $this->currency_format($line_total / $quantity);
            $products[] = $serialized_product;
        }
        return $products;
    }

    private function is_search_page() {
        // Check if we are on a search result page by checking if the search query var has been set
        return !empty($this->search_query);
    }

    private function is_checkout_page() {
        // Check if we are on the checkout page
        return is_checkout() && $this->woocommerce_after_checkout_form_flag;
    }

    private function is_conversion_page() {
        // Check if we are on a conversion page, and that the corresponding order order was placed no more than five
        // minutes ago. This is needed to reduce the possibility of tracking the same order more than once.
        if (is_order_received_page() && (!is_null($this->order_id))) {
            $order = wc_get_order($this->order_id);
            if (version_compare(WC_VERSION, '3.0', '<')) {
                $completed = $order->order_date->getTimestamp();
            } else {
                $completed = $order->get_date_created()->getTimestamp();
            }
            return (time() - $completed) < 300;
        }
        return false;
    }

    private function get_current_page() {
        // Determine the current page that the user is on
        if (is_front_page()) {
            return 'home_page';
        } else if (is_product()) {
            return 'product_page';
        } else if ($this->is_conversion_page()) {
            return 'conversion_page';
        } else if ($this->is_checkout_page()) {
            return 'checkout_page';
        } else if ($this->is_search_page()) {
            return 'search_page';
        } else if (is_cart()) {
            return 'cart_page';
        } else {
            return 'other';
        }
    }

    public function get_global_vars() {
        // Create single array that contains every piece of data needed by the pixel to fire the corresponding event
        // for the current page.

        // We can't fire the pixel on ajax calls, so we skip all of this logic
        if (is_ajax()) {
            return;
        }

        $global_vars = array();
        $global_vars['adroll_current_page'] = $this->get_current_page();
        $global_vars['adroll_currency'] = get_woocommerce_currency();
        $global_vars['adroll_language'] = get_locale();

        $email = (string)wp_get_current_user()->user_email;
        if (!empty($email)) {
            $global_vars['adroll_email'] = md5($email);
        }

        $products_added_to_cart = $this->get_products_added_to_cart();
        if (!empty($products_added_to_cart)) {
            $global_vars['adroll_products_added_to_cart'] = $products_added_to_cart;
        }

        switch($this->get_current_page()) {
            case 'search_page':
                $global_vars['adroll_keywords'] = $this->search_query;
                $products = array();
                foreach ($this->search_results as $id) {
                    $child = $this->adroll_get_product($id);
                    $products[] = $this->serialize_product($child);
                }
                $global_vars['adroll_products'] = $products;
                break;
            case 'product_page':
                $products = array();
                global $product;
                if ( ! is_object( $product ) ) {
                    $product = wc_get_product( get_the_ID() );
                }
                if ($product->is_type('grouped')) {
                    foreach ($product->get_children() as $id) {
                        $child = $this->adroll_get_product($id);
                        $products[] = $this->serialize_product($child);
                    }
                } else {
                    $products[] = $this->serialize_product($product);
                }
                $global_vars['adroll_products'] = $products;
                break;
            case 'checkout_page':
            case 'cart_page':
                $global_vars['adroll_products'] = $this->serialize_cart_products(WC()->cart->get_cart());
                break;
            case 'conversion_page':
                $order = wc_get_order($this->order_id);
                $global_vars['adroll_products'] = $this->serialize_order_products($order);
                $global_vars['adroll_conversion_value'] = $this->currency_format($order->get_total());
                $global_vars['adroll_order_id'] = (string)$this->adroll_get_id($order);
                break;
            case 'home_page':
            default:
                // No special vars needed for homepage or other pages
                break;
        }
        return $global_vars;
    }

    private function notify_plugin_activated() {
        $plugin_activate_url = self::ADROLL_BASE_URL . 'woocommerce/plugin_activate/' . get_option('adroll_adv_eid');
        wp_remote_get($plugin_activate_url);
    }

    private function notify_plugin_deactivated() {
        $plugin_deactivate_url = self::ADROLL_BASE_URL . 'woocommerce/plugin_deactivate/' . get_option('adroll_adv_eid');
        wp_remote_get($plugin_deactivate_url);
    }
}

$AdRoll_For_WooCommerce = AdRoll_For_WooCommerce::get_instance();

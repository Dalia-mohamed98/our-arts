<?php

if (!class_exists('MSAB')) {

    class MSAB
    {
        private $initiated = false;
        private $options;

        public function __construct()
        {
            if (!$this->initiated) {
                $this->init_hooks();
            }
        }

        /**
         * Initializes WordPress hooks
         */
        private function init_hooks()
        {
            $this->initiated = true;
            $this->options = get_option('mobiloud_smart_app_banner');
            //Hooks
            add_action('wp_head', array($this, 'add_banner_meta'));
            add_action('wp_footer', array($this, 'add_custom_sticky_navigation_class'));
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

        }

        /**
         * Enqueue custom script
         */
        public function enqueue_scripts()
        {
            //Styles
            wp_enqueue_style('msab-lib-css', MSAB_PLUGIN_DIR_URL . 'public/libs/smart-banner-js/smartbanner.min.css', array(), 'null', 'all');
            wp_enqueue_style('msab-custom-css', MSAB_PLUGIN_DIR_URL . 'public/css/frontend.css', array(), 'null', 'all');
            //Scripts
            wp_enqueue_script('msab-lib-js', MSAB_PLUGIN_DIR_URL . 'public/libs/smart-banner-js/smartbanner.js', array('jquery'), null, true);
            wp_enqueue_script('msab-custom-js', MSAB_PLUGIN_DIR_URL . 'public/js/frontend.js', array('msab-lib-js'), null, true);
            //Custom variables
            $image_folder_url = MSAB_PLUGIN_DIR_URL;
            $localizations = array('imageFolderURL' => $image_folder_url);

            wp_localize_script('msab-custom-js', 'localizedVars', $localizations);

        }

        /**
         * Adding the meta tag for android and ios app
         */
        public function add_banner_meta()
        {
            if (!empty($this->options)) {
                $current_option = $this->options;
                $msab_meta_tags = '<!-- Start Mobiloud Smart App Banner configuration -->';
                if (!empty($current_option['app_title'])) {
                    $msab_meta_tags .= '<meta name="smartbanner:title" content="' . sanitize_text_field($current_option['app_title']) . '">';
                }
                if (!empty($current_option['app_desc'])) {
                    $msab_meta_tags .= '<meta name="smartbanner:author" content="' . sanitize_text_field($current_option['app_desc']) . '">';
                }
                if (!empty($current_option['app_icon'])) {
                    $msab_meta_tags .= '<meta name="smartbanner:icon-apple" content="' . wp_get_attachment_image_src($current_option['app_icon'], 'full')[0] . '">';
                    $msab_meta_tags .= '<meta name="smartbanner:icon-google" content="' . wp_get_attachment_image_src($current_option['app_icon'], 'full')[0] . '">';
                }
                $msab_meta_tags .= '<meta name="smartbanner:button" content="VIEW">';
                if (!empty($current_option['apple_app_url'])) {
                    $apple_url = esc_url($current_option['apple_app_url']);
                    preg_match("/id(\d+)/", $apple_url, $match);
                    $apple_ID = $match[1];
                    $msab_meta_tags .= '<meta name="smartbanner:button-url-apple" content="' . esc_url($current_option['apple_app_url']) . '">';
                }
                if (!empty($current_option['android_app_url'])) {
                    $msab_meta_tags .= '<meta name="smartbanner:button-url-google" content="' . esc_url($current_option['android_app_url']) . '">';
                }
                $msab_meta_tags .= '<meta name="smartbanner:enabled-platforms" content="android,ios">';
                $msab_meta_tags .= '<meta name="smartbanner:close-label" content="Close">';
                $msab_meta_tags .= '<meta name="smartbanner:api" content="true">';

                $msab_meta_tags .= '<!-- End Mobiloud Smart App Banner configuration -->';
                if (isset($apple_ID)) {
                    $msab_meta_tags .= '<!-- Start Smart banner app for Safari on iOS configuration -->';
                    $msab_meta_tags .= '<meta name="apple-itunes-app" content="app-id=' . $apple_ID . '">';
                    $msab_meta_tags .= '<!-- End Smart banner app for Safari on iOS configuration -->';
                }
                echo $msab_meta_tags;
            }
        }

        public function add_custom_sticky_navigation_class()
        {
            $script = '';
            if (!empty($this->options)) {
                $current_option = $this->options;
                if (!empty($current_option['sticky_header_class_id'])) {
                    $script .= '<script>';
                    $script .= 'var msabStickyElement="' . $current_option['sticky_header_class_id'] . '"';
                    $script .= '</script>';
                }
            }
            echo $script;
        }

        /**
         * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
         *
         */
        public function plugin_activation()
        {
            if (!empty($_SERVER['SCRIPT_NAME']) && false !== strpos($_SERVER['SCRIPT_NAME'], '/wp-admin/plugins.php')) {
                add_option('Activated_Mobiloud_Smart_App_Banner', true);
            }
        }

        /**
         * Removes all connection options
         * @
         */
        public function plugin_deactivation()
        {

            //

        }
    }

    new MSAB();
}
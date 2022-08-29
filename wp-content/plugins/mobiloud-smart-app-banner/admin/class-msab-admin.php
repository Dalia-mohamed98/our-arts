<?php
require_once 'helpers.php';

if (!class_exists('MSAB_Admin')) {
    class MSAB_Admin
    {
        private $initiated = false;
        /**
         * Holds the values to be used in the fields callbacks
         */
        private $options;

        public function __construct()
        {
            if (!$this->initiated) {
                self::init_hooks();
            }
        }

        /**
         * Initializes WordPress hooks
         */
        private function init_hooks()
        {
            $this->initiated = true;

            //Hooks
            add_action('admin_init', array($this, 'admin_init'));
            add_action('admin_menu', array($this, 'admin_menu'));
            add_action('plugin_action_links_' . MSAB_PLUGIN_BASENAME, array($this, 'add_action_links'));
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

        }

        /**
         * Add a link to the settings page on the plugins.php page.
         *
         * @param $links
         *
         * @return array
         */
        function add_action_links($links)
        {

            $links = array_merge(array(
                '<a href="' . esc_url(admin_url('/options-general.php?page=msab')) . '">' . __('Settings', 'msab') . '</a>'
            ), $links);

            return $links;

        }

        /**
         * Adding a custom setting page under the Settings menu within backend
         */
        public function admin_menu()
        {
            add_options_page(
                __('Smart App Banners', 'msab'),
                __('Smart App Banners', 'msab'),
                'manage_options',
                'msab',
                array($this, 'load_settings_page')
            );
        }

        /**
         * Define the setting group and its fields
         */
        public function admin_init()
        {

            //Register setting
            register_setting(
                'msab_settings',
                'mobiloud_smart_app_banner',
                array($this, 'sanitize')
            );

            //Register setting sections
            add_settings_section(
                'msab_general_setting_section',
                'General Settings',
                array($this, 'print_general_settings'),
                'msab'
            );

            add_settings_section(
                'msab_information_setting_section',
                'App Information Settings',
                array($this, 'print_information_settings'),
                'msab'
            );

            //General settings fields
            add_settings_field(
                'apple_app_url',
                'Apple App URL',
                array($this, 'apple_url_callback'),
                'msab',
                'msab_general_setting_section'
            );

            add_settings_field(
                'android_app_url',
                'Android App URL',
                array($this, 'android_url_callback'),
                'msab',
                'msab_general_setting_section'
            );
            add_settings_field(
                'sticky_header',
                'Custom sticky header class/id',
                array($this, 'app_sticky_header_callback'),
                'msab',
                'msab_general_setting_section'
            );

            //Information settings fields
            add_settings_field(
                'app_icon',
                'Icon',
                array($this, 'app_icon_callback'),
                'msab',
                'msab_information_setting_section'
            );

            add_settings_field(
                'app_title',
                'Title',
                array($this, 'app_title_callback'),
                'msab',
                'msab_information_setting_section'
            );
            add_settings_field(
                'app_desc',
                'Description',
                array($this, 'app_desc_callback'),
                'msab',
                'msab_information_setting_section'
            );

        }

        /**
         * Print the Section text
         */
        public function print_general_settings()
        {
            return false;
        }

        public function print_information_settings()
        {
            return false;
        }

        /**
         * Loading all defined setting fields
         */
        public function load_settings_page()
        {
            $this->options = get_option('mobiloud_smart_app_banner');
            ?>
            <div class="wrap">
                <h2><?php _e('Smart App Banners', 'msab'); ?></h2>
                <form method="post" action="options.php">
                    <?php
                    // This prints out all hidden setting fields
                    settings_fields('msab_settings');
                    do_settings_sections('msab');
                    submit_button();
                    ?>
                </form>
            </div>
            <?php
        }

        /**
         * Enqueue the media script and custom script for image uploader
         */
        public function admin_enqueue_scripts()
        {
            if (!did_action('wp_enqueue_media')) {
                wp_enqueue_media();
            }

            wp_enqueue_script('msab-admin-js', MSAB_PLUGIN_DIR_URL . 'admin/js/custom.js', array('jquery'), null, false);
        }

        /**
         * Sanitize each setting field as needed
         *
         * @param array $input Contains all settings fields as array keys
         */
        public function sanitize($input)
        {
            $sanitized_input = array();

            if (!empty($input)) {
                foreach ($input as $key => $value) {
                    if (isset($value)) {
                        $sanitized_input[$key] = sanitize_text_field($value);
                    }
                }
            }

            return $sanitized_input;
        }

        /**
         * Get the settings option array and print one of its values
         */
        public function apple_url_callback()
        {
            echo msab_create_text_field('apple_app_url', $this->options);
        }

        public function android_url_callback()
        {
            echo msab_create_text_field('android_app_url', $this->options);
        }

        public function app_title_callback()
        {
            echo msab_create_text_field('app_title', $this->options);
        }

        public function app_sticky_header_callback()
        {
            echo msab_create_text_field('sticky_header_class_id', $this->options);
        }

        public function app_desc_callback()
        {
            echo msab_create_text_field('app_desc', $this->options);
        }

        public function app_icon_callback()
        {
            echo $this->image_uploader_field('app_icon', 'mobiloud_smart_app_banner[app_icon]', $this->options['app_icon']);
        }

        /**
         * Rendering the image uploader for icon
         *
         * https://rudrastyh.com/wordpress/customizable-media-uploader.html
         *
         * @param $id
         * @param $name
         * @param string $value
         *
         * @return string
         */
        public function image_uploader_field($id, $name, $value = '')
        {
            $image = ' button">Upload image';
            $image_size = 'thumbnail';
            $display = 'none';

            if ($image_attributes = wp_get_attachment_image_src($value, $image_size)) {

                // $image_attributes[0] - image URL
                // $image_attributes[1] - image width
                // $image_attributes[2] - image height

                $image = '"><img src="' . $image_attributes[0] . '" style="max-width:95%;display:block;" />';
                $display = 'inline-block';

            }

            return '<div>
                    <a href="#" class="msab_upload_image_button' . $image . '</a>
                    <input type="hidden" name="' . $name . '" id="' . $id . '" value="' . esc_attr($value) . '" />
                    <a href="#" class="msab_remove_image_button" style="display:inline-block;display:' . $display . '">Remove image</a>
                </div>';
        }
    }

    new MSAB_Admin();
}
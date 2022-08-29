<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

// Cleanup: delete all AdRoll options from the db, including ones that aren't being used anymore but were set by
// previous versions of this plugin.

delete_option('adroll_adv_eid');
delete_option('adroll_pixel_eid');
delete_option('adroll_do_activation_redirect');

delete_option('admin_notice_success');
delete_option('admin_notice_warning');
delete_option('adroll_do_activation');
delete_option('adroll_do_deactivation');
delete_option('adroll_final_attempt_date');
delete_option('adroll_initial_setup_date');
delete_option('adroll_last_attempted_date');
delete_option('adroll_pixel_inject_attempts');
delete_option('adroll_plugin_silenced');

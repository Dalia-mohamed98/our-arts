<?php
/**
 * Status Page
 *
 * @link       https://webappick.com/
 * @since      5.1.7
 * @version    5.1.6
 *
 * @package    Woo_Feed
 * @subpackage Woo_Feed/admin/partial
 * @author     Ohidul Islam < ceo@webappick.com >
 */
?>
<div class="wrap wapk-admin">
    <div class="wapk-section">
        <h1 class="wp-heading-inline"><?php esc_html_e( 'System Status', 'woo-feed' ); ?></h1>
        <hr class="wp-header-end">
		<?php

		$system_data = new Woo_Feed_Status();

		WPFFWMessage()->displayMessages();

		$status_tab = add_query_arg([
			'tab' => 'status-tab',
		],admin_url('admin.php?page=webappick-wp-status'));

		$log_tab = add_query_arg([
			'tab' => 'log-tab',
		],admin_url('admin.php?page=webappick-wp-status'));

		$tab_name= isset($_GET['tab'])?sanitize_text_field($_GET['tab']):false; //phpcs:ignore

		?>

        <h2 class="nav-tab-wrapper">
            <a href="<?php echo esc_url($status_tab); ?>" class="nav-tab <?php echo esc_html(($tab_name && 'status-tab' === $tab_name) ? 'nav-tab-active' : ''); ?>">Status</a>
            <a href="<?php echo esc_url($log_tab); ?>" class="nav-tab <?php echo esc_html((isset($tab_name) && 'log-tab' === $tab_name) ? 'nav-tab-active' : ''); ?>">Logs</a>
        </h2>

		<?php if ( $tab_name && 'log-tab' === $tab_name ) :?>
            <br><br>
            <textarea name="" id="" cols="150" rows="30"><?php echo esc_html($system_data->get_logs()); ?></textarea>
		<?php else : ?>
            <br><br>
            <div class="woo-feed-status-table-wrapper succ">
                <table class="woo-feed-status-table">
                    <thead>
                    <tr>
                        <th colspan="2"><?php esc_html_e('Environment', 'woo-feed'); ?></th>
                        <th><?php esc_html_e('Message', 'woo-feed'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
					<?php

					$status_text = "`\n";

					foreach ( $system_data->get_woo_feed_status() as $key => $woo_feed_status ) {
						$label = $woo_feed_status['label'];
						$status_icon = $woo_feed_status['status'];
						$message = $woo_feed_status['message'];
						$status_text .= $label.": ". wp_strip_all_tags(str_replace(array( '☞', '<br/>' ),[ "\n☞", "\n" ], $message))."\n\n";
						?>
                        <tr data-status="<?php esc_attr($key); ?>">
                            <td style="font-weight: bold;width:40%;" ><?php echo esc_attr($label); ?></td>
                            <td style="width: 5%;"><?php echo $status_icon; //phpcs:ignore?></td>
                            <td><?php echo $message; //phpcs:ignore?></td>
                        </tr>
						<?php
					}

					// Feed Files
					$feed_urls = woo_feed_get_feed_file_list();
					if ( ! empty($feed_urls) ) {
						$feed_urls = implode("\n",$feed_urls);
					}
					$status_text .= "Feed Files\n";
					$status_text .= $feed_urls;

					// Fatal Errors
					$fatal_errors = $system_data->get_logs();
					if ( ! empty($fatal_errors) ) {
						$status_text .= "\n\nFatal Errors\n";
						$status_text .= $fatal_errors;
					}

					$status_text .= "\n`";
					?>
                    <tr>
                        <td colspan="2"></td>
                        <td><input type="button" class="button button-primary" id="woo-feed-copy-status-btn" value="Copy Status"/></td>
                    </tr>
                    </tbody>
                </table>
                <br><br>
                <textarea name="" id="woo-feed-status-area" style="visibility: hidden;" cols="100" rows="30"><?php echo $status_text; //phpcs:ignore?></textarea>
            </div>
		<?php endif; // End of tab condition. ?>

    </div>
</div>
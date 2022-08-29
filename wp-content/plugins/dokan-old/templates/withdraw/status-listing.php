<?php
/**
 * Dokan Withdraw Status filter template
 *
 * @since 2.4
 *
 * @package dokan
 */
?>
<ul class="list-inline subsubsub">
    <li<?php echo $current == 'pending' ? ' class="active"' : ''; ?>>
        <a href="<?php echo esc_url( dokan_get_navigation_url( 'withdraw' ) ); ?>"><?php esc_html_e( 'طلب السحب', 'dokan-lite' ); ?></a>
    </li>
    <li<?php echo $current == 'approved' ? ' class="active"' : ''; ?>>
        <a href="<?php echo esc_url( add_query_arg( array( 'type' => 'approved' ), dokan_get_navigation_url( 'withdraw' ) ) ); ?>"><?php esc_html_e( 'الطلب المقبول', 'dokan-lite' ); ?></a>
    </li>
    <li<?php echo $current == 'cancelled' ? ' class="active"' : ''; ?>>
        <a href="<?php echo esc_url( add_query_arg( array( 'type' => 'cancelled' ), dokan_get_navigation_url( 'withdraw' ) ) ); ?>"><?php esc_html_e( 'الطلب الملغي', 'dokan-lite' ); ?></a>
    </li>
</ul>
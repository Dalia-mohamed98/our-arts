<?php
/**
 * Dokan Withdraw Approved Request listing template
 *
 * @since 2.4
 *
 * @package dokan
 */
?>

<table class="dokan-table dokan-table-striped">
    <thead>
        <tr>
            <th><?php esc_html_e( 'الكمية', 'dokan-lite' ); ?></th>
            <th><?php esc_html_e( 'الطريقة', 'dokan-lite' ); ?></th>
            <th><?php esc_html_e( 'التاريخ', 'dokan-lite' ); ?></th>
            <th><?php esc_html_e( 'الملاحظة', 'dokan-lite' ); ?></th>
        </tr>
    </thead>
    <tbody>

    <?php foreach ( $requests as $row ) { ?>
        <tr>
            <td><?php echo wc_price( $row->amount ); ?></td>
            <td><?php echo esc_html( dokan_withdraw_get_method_title( $row->method ) ); ?></td>
            <td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $row->date ) ) ); ?></td>
            <td><?php echo wp_kses_post( $row->note ); ?></td>
        </tr>
    <?php } ?>

    </tbody>
</table>

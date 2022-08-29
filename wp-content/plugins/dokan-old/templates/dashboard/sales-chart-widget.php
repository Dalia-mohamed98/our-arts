<?php

/**
 *  Dokan Dashboard Template
 *
 *  Dokan Dashboard Sales chart report widget
 *
 *  @since 2.4
 *
 *  @package dokan
 */
?>

<div class="dokan-w6 dashboard-widget">
    <div class="widget-title"><i class="fa fa-credit-card"></i> <?php esc_html_e( 'مبيعات هذا الشهر', 'dokan-lite' ); ?></div>

    <?php
	    require_once DOKAN_INC_DIR . '/reports.php';
	    dokan_dashboard_sales_overview();
    ?>
</div> <!-- .sells-graph -->

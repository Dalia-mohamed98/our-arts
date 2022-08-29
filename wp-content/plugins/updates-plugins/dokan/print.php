<?php
add_action('woocommerce_order_get_tracking_code','print_order_AWB');
add_action('woocommerce_order_print','print_order_invoice');
add_action('woocommerce_order_AWB','show_AWB_pdf');

function show_AWB_pdf($post_id){

    $last_track = print_order_AWB($post_id);
    header("Location: https://our-arts.com/wp-content/uploads/aramex/". $last_track . ".pdf");

}

function print_order_invoice($post_id) {

    $last_track = print_order_AWB($post_id);
    $pdf_url = wp_nonce_url( admin_url( 'admin-ajax.php?action=generate_wpo_wcpdf&document_type=invoice&order_ids=' . $post_id), 'generate_wpo_wcpdf' );
      
    ?>

    <li class="wide">
        <a class="thickbox button" style="width: 100%;" href=<?=$pdf_url?> target="_blank" alt="PDF Invoice" data-tip="PDF Invoice" data-original-title="" title="">
        طباعة الفاتورة</a>

        <a class='button button-primary' style="margin-top:15px; width: 100%"
            id="print_aramex_shipment_vendor" target="_blank" href="https://our-arts.com/wp-content/uploads/aramex/<?php echo $last_track;?>.pdf"><?php echo esc_html__('طباعة البوليصة', 'aramex'); ?> </a>
    </li>

    <?php
}

function print_order_AWB($post_id) {
    global $wpdb;
    $query = "SELECT comment_content FROM {$wpdb->prefix}comments WHERE comment_post_ID = $post_id AND comment_approved = 1";
    $history = $wpdb->get_results( $query );

    $history_list = array();
    foreach ($history as $key => $shipment) {
        $history_list[] = $shipment->comment_content;
    }
    $last_track = "";
    if (count($history_list)) {
        foreach ($history_list as $history) {
            $awbno = strstr($history, "- Order No", true);
            $awbno = trim($awbno, "AWB No.");
            if (isset($awbno)) {
                if ((int)$awbno) {
                    $last_track = $awbno;
                    break;
                }
            }
            $awbno = trim($awbno, "Aramex Shipment Return Order AWB No.");
            if (isset($awbno)) {
                if ((int)$awbno) {
                    $last_track = $awbno;
                    break;
                }
            }
        }
    } 
    else {
            return "empty";
            //$last_track = count($history_list);
        }
    return $last_track;
    }
?>
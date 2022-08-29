<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 * since      1.0.0
 * Author     Mandeep Saini
 * package    R2SL
 * subpackage R2SL/admin/partials
 */
include 'getServices.php';
?>

<!----CSS Define Start------>
<style>
table {
  border-collapse: collapse;
  width: 100%;
}
.table100-head
{
  background:white;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
.tabs{
    width:100%;
    height:auto;
    margin:0 auto;
}

/* tab list item */
.tabs .tabs-list {
    list-style: none;
    margin: 0px;
    padding: 0px;
    width: 100%;
    float: left;
    background: #514f50;
    color: white;
}
.tabs .tabs-list li{
    width:150px;
    float:right;
    margin:0px;
    margin-right:2px;
    padding:10px 5px;
    text-align: center;
    outline:none;
    border-radius:3px;
}
.tabs .tabs-list li:hover{
    cursor:pointer;
}
.tabs .tabs-list li a{
    text-decoration: none;
    color:white;
    outline:none;
    font-size: 17px;  
}

/* Tab content section */
.tabs .tab{
    display:none;
    width:100%;
    
    border-radius:3px;
    padding-top:10px;
   
    color:darkslategray;
    clear:both;
}
.tabs .tab h3{
    border-bottom:3px solid cornflowerblue;
    letter-spacing:1px;
    font-weight:normal;
    padding:5px;
}
.tabs .tab p{
    line-height:20px;
    letter-spacing: 1px;
}

/* When active state */
.active{
    display:block !important;
}
.tabs .tabs-list li.active{
    
}
.active a{
    color:cornflowerblue !important;
}
.float-right {
    float: right;
}
.no_tab_label
{
  width: 300px !important;
  float: left !important;
  text-align: left !important;
  cursor: unset;
}
.tabs-list h2 {
    margin: 0px;
    padding-left: 20PX;
    padding-top: 9px;
    color:white;
}

.pagination {
  display: inline-block;
}

.dataTables_paginate  .paginate_button {
  color: black !important;
  float: left;
  padding: 8px 16px !important;
  text-decoration: none !important;
  transition: background-color .3s !important;
  border: 0px solid #ddd !important;
}

#table_id_paginate a.current , #table_id_assigned_paginate a.current  {
  background: #514f50 !important;
  color: white !important;
  border: 0px solid #4CAF50 !important;
}
.dataTables_wrapper #table_id_paginate .paginate_button.current, 
.dataTables_wrapper #table_id_paginate .paginate_button.current:hover,
.dataTables_wrapper #table_id_assigned_paginate .paginate_button.current, 
.dataTables_wrapper #table_id_assigned_paginate .paginate_button.current:hover
{
  color: white !important;
}
.dataTable thead tr {
    background: #514f50 !important;
    color: white;
}
button#doaction , button {
    background: cornflowerblue;
    border: 0px;
    font-size: 15px;
    padding: 10px 10px;
    border-radius: 4px;
    color: white;
    outline: none;
    cursor: pointer;
}
.dataTables_filter {
    display: block !important;
}

.dataTables_paginate  .paginate_button:hover:not(.current) {background-color: #ddd !important;}
/* media query */
@media screen and (max-width:360px){
    .tabs{
        margin:0;
        width:96%;
    }
    .tabs .tabs-list li{
        width:80px;
    }
}
/* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 123333; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
  background-color: #fefefe;
  margin: auto;
  border: 1px solid #888;
  width: 700px;
}
.waybill_detail_content {
    color: white;
}
/* The Close Button */
.close {
  color: black;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: #000;
  text-decoration: none;
  cursor: pointer;
}
.waybill_detail_heading h3 {
    width: 50%;
    float: left;
    margin: 0;
}
.padding_modal
{
  padding: 20px;
  padding-top: 15px;
}
.waybill_detail_content label {
    width: 100% !important;
    display: block;
    font-size: 15px;
    margin-bottom: 10px;
    color: black;
}
.waybill_detail_content label strong {
    width: 180px !important;
    display: inline-block;
}
.waybill_staus_remark h4 {
    font-size: 16px;
    margin-bottom: 5px;
    margin-top: 5px;
}
.waybill_staus_remark
{
  color: black;
}
.half_div {
    width: 49%;
    float: left;
}
.waybill_staus_remark ul {
    margin-top: 0px;
}
.waybill_staus_remark li {
    border-bottom: 1px solid;
    padding-left: 5px;
}
.waybill_staus_remark ul {
    margin-top: 0px;
    border: 1px solid;
}
.waybill_staus_remark li:last-child
{
  border: 0px solid !important;
}
.waybill_detail_heading {
    border-bottom: 2px solid;
}
.waybill_detail_content p
{
  text-align:center;
}
ul#service_code_add li span {
    display: inline !important;
    padding-left: 21px;
}
ul#service_code_add .regular-text {
    width: 46%;
    margin: 0px !important;
    height: 30px !important;
    vertical-align: unset !important;
}
ul#service_code_add {
    font-size: 15px;
}
#service_code_add {
    color: black;
}
.order_id_div_pop_up {
    padding-left: 21px;
    margin-bottom: 12px;
}
.order_id_div_pop_up label {
    width: 46%;
    display: inline;
    margin-right: 21px;
}
.order_id_div_pop_up input {
    width: 76% !important;
}
</style>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.css">

<div class="wrap">
<?php
  global $wpdb;
  $settingAtrs = $wpdb->get_row( "SELECT * FROM wp_logixgridsetting WHERE ID = 1");
  if (empty($settingAtrs)) {
?>
  <!-- The Modal -->
<div style="display:block !important;"   class="modal">
  <!-- Modal content -->
  <div class="modal-content">
    <div class="waybill_detail_heading padding_modal"><h3>Plugin Notice</h3><div style="clear:both"></div></div>
    <div class="waybill_detail_content padding_modal" style="text-align:center;color:black;">
        <p style="font-size: 20px;">Kindly configure the plugin setting.</p>
        <p><a href="admin.php?page=settings-page"><button >Config. Setting</button></a><p>
    </div>
  </div>
</div>

<?php
  }
  else{
  $orderlists = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE post_type = 'shop_order' ");
  $statuses = wc_get_order_statuses();
?>
<h2></h2>
    <div class="tabs">
      <ul class="tabs-list">
        <h2 class="no_tab_label">R2SL</h2>
        <li class="float-right"><a href="#tab1">Assigned Order</a></li>
        <li class="float-right active"><a href="#tab2">New Order</a></li>

      </ul>

      <div id="tab1" class="tab">
          <!---------------------------------------> 
          <!--------------------------------------->        
          <!--------------------------------------->  
          <!--------------------------------------->  
          <div class="table100">
              <table id="table_id_assigned">
                <thead>
                  <tr class="table100-head">
                    <th class="column2">Order Number</th>
                    <th class="column2 no-sort" data-orderable="">Waybill Number</th>
                    <th class="column2 no-sort">COD Amount</th>
                    <th class="">Receiver</th> 
                    <th class="column1">Consignee State</th>
                    <th class="column1">Consignee City</th>
                    <th class="column1">Consignee Phone</th>
                    <th class="column1">Consignor Code</th>
                    <th class="column3"> Waybill Date</th>
                    <th class="column4">Last Updated Status</th>
                    <th class="column4">Last Updated Remarks</th>
                    <th class="column4">Service</th>
                    <th class="column4">Pickup Number</th>
                    <th class="column3">Waybill Label</th>
                    <th class="column4">Tracking</th>
                  </tr>
                </thead>
                <tbody>
                <?php 
                  
                  $waybill_list = $wpdb->get_results( "SELECT * FROM wp_logixgridwaybill");
                  $waybillchecks = array_reverse($waybill_list);
                   foreach($waybillchecks as $waybillcheck)
                    {
                      $waybill_status = $waybillcheck->waybill_status;
                      $get_status = unserialize($waybill_status);
                      $status = end($get_status);
                      $waybill_remarks = $waybillcheck->waybill_remark;
                      $get_remarks = unserialize($waybill_remarks);
                      $remarks = end($get_remarks);
                      $pickup = $waybillcheck->pickup_number;
                      if($pickup){ $pickupnumber = $pickup; }
                      else { $pickupnumber = 'Pickup request not generated'; }
                    
                ?>
                <tr>
                    <td class="column2"><?php echo $waybillcheck->wp_order_ID; ?></td>
                  <td class="column2"><?php echo $waybillcheck->waybill_number; ?></td>
                  <td><?php echo get_post_meta( $waybillcheck->wp_order_ID, '_order_total', true ); ?></td>
                  <td><?php echo get_post_meta( $waybillcheck->wp_order_ID, '_billing_first_name', true ); ?></td>
                  <td><?php echo get_post_meta( $waybillcheck->wp_order_ID, '_billing_state', true ); ?></td>
                  <td><?php echo get_post_meta( $waybillcheck->wp_order_ID, '_billing_city', true ); ?></td>
                  <td><?php echo get_post_meta( $waybillcheck->wp_order_ID, '_billing_phone', true ); ?></td>
                  <td><?php print_r($settingAtrs->customerCode); ?></td>
                  <td class="column3"><?php echo $waybillcheck->waybill_created; ?></td>
                  <td class="column4"><?php echo $status; ?></td>
                  <td class="column4"><?php echo $remarks; ?></td>
                  <td class="column2"><?php echo $waybillcheck->service_name; ?></td>
                  <td class="column2"><?php echo $pickupnumber;  ?></td>
                  <td class="column3"><a target='_blank' href="<?php echo $waybillcheck->waybill_file_name; ?>">Waybill Print</a></td>
                  <td class="column4"><a id="<?php echo $waybillcheck->ID; ?>" class="view_history" href="javascript:void(0)">View Tracking</a></td>
                </tr>
                <?php }  ?>
              </tbody>
              </table>
              </div>
               <!---------------------------------------> 
              <!--------------------------------------->        
              <!--------------------------------------->  
              <!--------------------------------------->  
      </div>
        <!--------------------------------------->  
        <!--------------------------------------->  
      <div id="tab2" class="tab active">
          <!---------------------------------------> 
          <!--------------------------------------->        
          <!--------------------------------------->  
          <!--------------------------------------->  
          <div class="tablenav top">
              <div class="alignleft actions bulkactions">
                  <button  id="doaction" class="" >Create Waybill</button>
              </div>
            </div>
            <br>
            <div class="table100">
              <table id="table_id">
                <thead>
                  <tr class="table100-head">
                    <th class="column1">Wp-order Number</th>
                    <th class="">Customer Name</th> 
                    <th class="column1">COD Amount</th>
                    <th class="column1">Consignee State</th>
                    <th class="column1">Consignee City</th>
                    <th class="column1">Consignee Phone</th>
                    <th class="column1">Consignor Code</th>
                    <th class="column3">Order Date</th>
                    <th class="column4">Status</th>
                  </tr>
                </thead>
                <tbody>
                <?php 
                  foreach ( $orderlists as $orderlist )
                  { 
                    $waybillcheck = $wpdb->get_row( "SELECT * FROM wp_logixgridwaybill WHERE wp_order_ID = $orderlist->ID"); 
                    //print_r($waybillcheck);
                    if(empty($waybillcheck))
                    {
                      $status = $orderlist->post_status;
                    
                ?>
                <tr>
                  <td class="column1">
                    <input class="order_checkbox" id="cb-select<?php echo $orderlist->ID; ?>" value="<?php echo $orderlist->ID; ?>" type="checkbox" >
                    <?php echo $orderlist->ID; ?>
                  </td>
                  <td><?php echo get_post_meta( $orderlist->ID, '_billing_first_name', true ); ?></td>
                  <td><?php echo get_post_meta( $orderlist->ID, '_order_total', true ); ?></td>
                  <td><?php echo get_post_meta( $orderlist->ID, '_billing_state', true ); ?></td>
                  <td><?php echo get_post_meta( $orderlist->ID, '_billing_city', true ); ?></td>
                  <td><?php echo get_post_meta( $orderlist->ID, '_billing_phone', true ); ?></td>
                  <td><?php print_r($settingAtrs->customerCode); ?></td>
                  <td class="column3"><?php echo $orderlist->post_modified; ?></td>
                  <td class="column4"><?php echo $status; ?></td>
                </tr>
                <?php } } ?>
              </tbody>
              </table>
              </div>
          <!--------------------------------------->  
          <!--------------------------------------->  
          <!--------------------------------------->  
          <!---------------------------------------> 
      </div>
</div>

<!-- The Modal -->
<div id="myModal" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
    <div class="waybill_detail_heading padding_modal"><h3>Tracking Details</h3> <span class="close" id="closemodal">&times;</span> <div style="clear:both"></div></div>
    <div class="waybill_detail_content padding_modal" id="waybill_detail_content_main">
        <div class="waybill_detail_content_inner">
        </div>
        <p><img src="<?php echo plugin_dir_url( __FILE__ ); ?>/img/loading.gif">
        </p>
    </div>
  </div>
</div>
<!---------------------------------------> 

<!-------Loading Image Start---------------->
<div class="modal" id="loading_div_img">
  <div class="modal-content">  
  <div class="waybill_detail_heading padding_modal"><h3>Waybills</h3> <span class="close" id="closemodalload">&times;</span> <div style="clear:both"></div></div>                  
    <div class="waybill_detail_content padding_modal">
    <ul id="service_code_add">
        <li id="li_iiu">
            <div class="order_id_div_pop_up">
              <label>Wp-order Number</label> 
              <input id="order_numbers_20" placeholder="Order Number" class="regular-text code" type="text" readonly>
              </div>
            <br>
            <span>
              <select id="service_20" class="regular-text code service_name">
                  <option value="null">Select Service</option>
                  <?php
                        $services = getServices(); 
                        foreach($services as $service)
                        {
                            $name = $service["name"];
							              $code = $service["code"];
							              if($settingAtrs->serviceCode == $code){  echo '<option value="'.$code.'" selected>'.$name.'('.$code.')</option>';  }	
                            else{ echo '<option value="'.$code.'">'.$name.'('.$code.')</option>';  }
                        }
                    ?>
              </select>
            </span>
            <span><input id="customercode_20" placeholder="Consigner Address" value="<?php print_r($settingAtrs->customerCode); ?>" class="regular-text code customer_code" type="text"></span>
        </li>
      </ul>
      <div class="orderdetail">
        <button id="gets_code_waybill">Get Waybill Number</button>
        <button id="gets_code_waybillclose" style="display:none;">Close</button>
      </div>
    </div>  
  </div>  
</div>
<!-------Loading Image Finish---------------->
   <?php } ?>
</div>


<!----JS Define Start------>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.js"></script>
<script>
jQuery(document).ready( function () {
  jQuery('#table_id , #table_id_assigned').DataTable({
    "lengthChange": true,
    'aaSorting': [[0, 'desc']]
    
});
} );
</script>
<script type="text/javascript">
jQuery(document).ready(function(){

  jQuery(".tabs-list li a").click(function(e){
     e.preventDefault();
  });

  jQuery(".tabs-list li").click(function(){
     var tabid = jQuery(this).find("a").attr("href");
     jQuery(".tabs-list li,.tabs div.tab").removeClass("active");   // removing active class from tab
     jQuery(".tab").hide();   // hiding open tab
     jQuery(tabid).show();    // show tab
     jQuery(this).addClass("active"); //  adding active class to clicked tab
  });

});
</script>








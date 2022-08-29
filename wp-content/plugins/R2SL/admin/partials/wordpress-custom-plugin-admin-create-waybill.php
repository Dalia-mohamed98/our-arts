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
include 'country.php';
include 'getServices.php';
include 'getStates.php';
global $wpdb;
$settingAtrs = $wpdb->get_row( "SELECT * FROM wp_logixgridsetting WHERE ID = 1");
?>
<style>
.waybill_form_main {
    width: 100%;
    margin: 0 auto;
}
.section-forms {
    width: 100%;
    margin: 0 auto;
}
.float-left {
    float: left;
}
.float-right {
    float: right;
    text-align:left;
}
.form-33 {
    width: 32%;
    padding-left: 10px;
    display: inline-block;
    float:unset !important;
}
.waybill_form_main label
{
    width:100%;
    font-weight: 500;
}
.waybill_form_main .regular-text
{
    width:100%;
}
.form-50 {
    width: 49%;
}
.section-form-2 {
    width: 98%;
}
.form-fieldls {
    margin-bottom: 12px;
}
.form-25 {
    width: 24%;
    margin-right: 7px;
    margin-left: 5px;
}
.waybill_form_main h3 {
    font-size: 24px;
}
table{
  width: 100%;
    text-align: center;
}
th {
    background: #7e7979;
    color: white;
    padding-bottom: 7px;
    border: 1px solid black;
}
td {
    background: white;
    padding-top: 5px;
    padding-bottom: 5px;
}
.add_button
{
    width:100%;
    text-align:right;
    margin-bottom:10px;
}
.heading_section {
    width: 100%;
    margin: 0 auto;
    padding-left: 10px;
}
.section-form-3 {
    width: 98%;
}
#packagedetailtable input {
    width: 100px;
}
.button_action {
    width: 98%;
    margin: 0 auto;
    margin-top: 40px;
}
.spanred
{
    color:red;
    font-weight:400;
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
  .loading_img {
    text-align: center;
}
.close_footer {
    text-align: right;
}
.loading_content p {
    color: black !important;
    font-size: 30px;
    margin: 0px;
}
</style>

<div class="wrap">
    <h2>Create Waybill</h2> <!--<span class="spanred">* Required Field</span>-->
    <div class="waybill_form_main">
        <!-----Basic Detail Start----->
            <div class="section-form-1 section-forms">
                <div class="heading_section">
                    <h3>Basic Details</h3>
                </div>
                <div class="form-fieldls form-33 float-left">
                    <label for='services'>Service <span class="spanred">*</span></label>
                    <select class="regular-text code" id="service">
                    <option value="null">Select</option>
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
                </div>
                <div class="form-fieldls form-33 float-left">
                    <label for='InvoiceValue'>Invoice Value</label>
                    <input type="text" class="regular-text code only-number" id="invoice_value">
                </div>
                <div class="form-fieldls form-33 float-left">
                    <label for='InvoiceValue'>Invoice Number</label>
                    <input type="text" class="regular-text code only-number" id="invoice_number">
                </div>
                <div class="form-fieldls form-33 float-left">
                    <label for='Referencenumber'>Reference Number</label>
                    <input type="text" class="regular-text code only-number" id="reference_number">
                </div>
                <div class="form-fieldls form-33 float-left">
                    <label for='codamount'>COD Amount</label>
                    <input type="text" class="regular-text code number-float" id="cod_amount">
                </div>
                <div class="form-fieldls form-33 float-left">
                    <label for='Description'>Description</label>
                    <input type="text" class="regular-text code" id="description">
                </div>
                <div style="clear:both"></div>
            </div>
        <!-----Basic Detail Finish----->
        <!-----Shipper-Consignee Detail Start----->
        <div class="section-form-2 section-forms">
            <!-----Shipper Detail Start----->
            <div class="section-form-2-inner section-form-2-1-inner form-100">
                <h3>Shipper</h3>
                <div class="form-fieldls form-50 float-left">
                    <label for='companyname'>Company Name</label>
                    <input type="text" class="regular-text code" id="scompanyname">
                </div>
                <div class="form-fieldls form-50 float-right">
                    <label for='contactperson'>Contact Person <span class="spanred">*  </span></label>
                    <input type="text" class="regular-text code" id="scontactperson">
                </div>
                <div class="form-fieldls form-50 float-left">
                    <label for='address'>Address <span class="spanred">*  </span></label>
                    <textarea class="regular-text code" id="saddress" rows="3" cols="50"></textarea>
                </div>
                <div class="form-fieldls form-50 float-right">
                    <label for='areaname'>Area Name</label>
                    <textarea class="regular-text code" id="sareaname" rows="3" cols="50"></textarea>
                </div>
                <div class="form-fieldls form-50 float-left">
                    <label for='Phone'>Phone<span>(Add multiple phone number using comma) <span class="spanred">*  </span></span></label>
                    <input type="text" class="regular-text code" id="sphone">
                </div>
                <div class="form-fieldls form-50 float-right">
                    <label for='Email'>Email<span>(Add multiple email number using comma)</span></label>
                    <input type="email" class="regular-text code" id="semail">
                </div>
                <div class="form-fieldls form-50 float-left">
                    <div class="form-fieldls form-50 float-left">
                        <label for='Country'>Country <span class="spanred">*  </span></label>
                        <select class="regular-text code" id="scountry">
                            <option value="null">Select</option>
                            <?php foreach($country as $key=>$value) { if($settingAtrs->sourceCountry == $key){ ?>
                                <option value="<?php echo $key;?>" selected><?php echo $value; ?></option>
                            <?php } else{ ?>
                                <option value="<?php echo $key;?>"><?php echo $value; ?></option>
                            <?php } } ?>
                        </select>
                    </div>
                    <div class="form-fieldls form-50 float-right">
                        <label for='State'>State <span class="spanred">*</span></label>
                        <select class="regular-text code getcity" messg='Shipper State'  city-id="scity" id="sstate">
                            <?php
                                if (is_array($RespStates))
                                {
                                    echo '<option value="null">Select</option>';
                                    foreach($RespStates as $RespState)
                                    {   
                                        $name =  $RespState['name'];
                                        $code =  $RespState['code'];
                                        echo '<option value="'.$code.'">'.$name.'</option>';
                                    }
                                }
                                else
                                {
                                    echo '<option value="notfound">Please Contact to Admin</option>';
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-fieldls form-50 float-right">
                    <div class="form-fieldls form-50 float-left">
                        <label for='city'>City <span class="spanred">*</span></label>
                        <select class="regular-text code" id="scity">
                        </select>
                    </div>
                    <div class="form-fieldls form-50 float-right">
                        <label for='Pincode'>Pincode</label>
                        <input type="text" class="regular-text code number" id="spincode">
                    </div>
                </div>
                <div style="clear:both"></div>
            </div>
            <!-----Shipper Detail Finish----->
            <!-----Consignee Detail Start----->
            <div class="section-form-2-inner section-form-2-2-inner form-100">
                <h3>Consignee</h3>
                <div class="form-fieldls form-50 float-left">
                    <label for='companyname'>Company Name</label>
                    <input type="text" class="regular-text code" id="ccompanyname">
                </div>
                <div class="form-fieldls form-50 float-right">
                    <label for='contactperson'>Contact Person <span class="spanred">*</span></label>
                    <input type="text" class="regular-text code" id="ccontactperson">
                </div>
                <div class="form-fieldls form-50 float-left">
                    <label for='address'>Address <span class="spanred">*</span></label>
                    <textarea class="regular-text code" id="caddress" rows="3" cols="50"></textarea>
                </div>
                <div class="form-fieldls form-50 float-right">
                    <label for='areaname'>Area Name</label>
                    <textarea class="regular-text code" id="careaname" rows="3" cols="50"></textarea>
                </div>
                <div class="form-fieldls form-50 float-left">
                    <label for='Phone'>Phone<span>(Add multiple phone number using comma)</span> <span class="spanred">*</span></label>
                    <input type="text" class="regular-text code" id="cphone">
                </div>
                <div class="form-fieldls form-50 float-right">
                    <label for='Email'>Email<span>(Add multiple email number using comma)</span></label>
                    <input type="email" class="regular-text code" id="cemail">
                </div>
                <div class="form-fieldls form-50 float-left">
                    <div class="form-fieldls form-50 float-left">
                        <label for='Country'>Country <span class="spanred">*</span></label>
                        <select class="regular-text code" id="ccountry">
                            <option value="null">Select</option>
                            <?php foreach($country as $key=>$value) { if($settingAtrs->sourceCountry == $key){ ?>
                                <option value="<?php echo $key;?>" selected><?php echo $value; ?></option>
                            <?php } else{ ?>
                                <option value="<?php echo $key;?>"><?php echo $value; ?></option>
                            <?php } } ?>
                        </select>
                    </div>
                    <div class="form-fieldls form-50 float-right">
                        <label for='State'>State <span class="spanred">*</span></label>
                        <select class="regular-text code getcity" messg='Consignee State'  city-id="ccity" id="cstate">
                            <?php
                                if (is_array($RespStates))
                                {
                                    echo '<option value="null">Select</option>';
                                    foreach($RespStates as $RespState)
                                    {   
                                        $name =  $RespState['name'];
                                        $code =  $RespState['code'];
                                        echo '<option value="'.$code.'">'.$name.'</option>';
                                    }
                                }
                                else
                                {
                                    echo '<option value="notfound">Please Contact to Admin</option>';
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-fieldls form-50 float-right">
                    <div class="form-fieldls form-50 float-left">
                        <label for='city'>City <span class="spanred">*</span></label>
                        <select class="regular-text code" id="ccity">
                        </select>
                    </div>
                    <div class="form-fieldls form-50 float-right">
                        <label for='Pincode'>Pincode</label>
                        <input type="text" class="regular-text code number" id="cpincode">
                    </div>
                </div>
                <div style="clear:both"></div>
            </div>
            <!-----Consignee Detail Finish----->
            <div style="clear:both"></div>
        </div>
         <!-----Shipper-Consignee Detail Finish----->

        <!-----Package Detail Start----->
        <div class="section-form-3 section-forms">
            <h3>Package <button style="float:right" id-index="0" id="addpackageindex" class="button">Add</button></h3>
            <table id="packagedetailtable">
                <thead>                
                    <tr>
                        <th>Index</th>
                        <th>Package Type<span class="spanred">*</span></th>
                        <th>Package Description<span class="spanred">*</span></th>
                        <th>Package Quantity<span class="spanred">*</span></th>
                        <th>Item Quantity<span class="spanred">*</span></th>
                        <th>Length<span class="spanred">*</span></th>
                        <th>Width<span class="spanred">*</span></th>
                        <th>Height<span class="spanred">*</span></th>
                        <th>Actual Weight<span class="spanred">*</span></th>
                        <th>Charged Weight<span class="spanred">*</span></th>
                        <th>Action</th>
                    </tr>
                </thead>    
                <tbody>
                    
                </tbody>
            </table>
        </div>
        <!-----Package Detail Finish----->
       <div class="button_action">
            <button class="button" id="create_waybill_manual">Create Waybill</button>
       </div>
    </div>


    <!-------Loading Image Start---------------->
<div class="modal" id="loading_div_img">
  <div class="modal-content">                    
    <div class="calculate_tariff_img padding_modal">
      <p class="loading_img" style="color:black; font-size:20px;">
      <img src="<?php echo plugin_dir_url( __FILE__ ); ?>/img/loading.gif"><br>Creating Waybill</p>
      <p class="loading_content" style="color:black; font-size:20px;">
         
      </p>
      <div class="close_footer">
            <button class="buttton" id="closemodals" style="display:none">Close</button>
            <button class="buttton" id="closemodalserror" style="display:none">Close</button>
      </div>
    </div>  
  </div>  
</div>
<!-------Loading Image Finish---------------->
</div>    
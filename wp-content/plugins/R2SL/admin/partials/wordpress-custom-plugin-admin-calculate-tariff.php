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
 
?>
<?php
include 'country.php';
include 'getServices.php';
include 'getStates.php';
global $wpdb;
$settingAtrs = $wpdb->get_row( "SELECT * FROM wp_logixgridsetting WHERE ID = 1");
?>
<style>
.main-tariff-form {
    width: 100%;
    margin: 0 auto;
}
.main-tariff-form .form-section label {
    width: 100%;
    display: block;
}
.main-tariff-form .form-col-3 {
    width: 23%;
    float: left;
    margin-right: 2%;
}
.main-tariff-form .form-col-4 {
    width: 31%;
    float: left;
    margin-right: 2%;
}
.main-tariff-form .regular-text {
    width: 100%;
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
.lists div {
    width: 49%;
    display: inline-block;
}
.calculate_div {
    width: 500px;
}
.label_name {
    font-size: 20px;
    font-weight: 600;
    color:black;
}
.label_value {
    font-size: 20px;
    text-align: right;
    color:black;
}
.lists {
    margin-bottom: 10px;
}
.maintotal {
    border-top: 2px solid black;
    padding-top: 7px;
    border-bottom: 2px solid black;
    padding-bottom: 7px;
}
.error_message
{
    color:black;
    font-size: 20px;
}
</style>

<div class="wrap">
    <h2>Calculate Tariff Amount</h2>
    <div class="main-tariff-form">
        <!---------section 1 start----------------->
        <div class="form-section form-section-1">
            <h3>Source</h3>
            <div class="form-col-3">
                <label for="Country">Default Country</label>
                <select class="regular-text code" id="source_country">
                        <option value="null">Select</option>
						<?php foreach($country as $key=>$value) { if($settingAtrs->sourceCountry == $key){ ?>
							<option value="<?php echo $key;?>" selected><?php echo $value; ?></option>
						<?php } else{ ?>
							<option value="<?php echo $key;?>"><?php echo $value; ?></option>
						<?php } } ?>
                </select>
            </div>
            <div class="form-col-3">
                <label for="State">State</label>
                <select class="regular-text code getcity" messg='Source State'  city-id="source_city" id="source_state" >
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
            <div class="form-col-3">
                <label for="City">City</label>
                <select class="regular-text code" id="source_city">
                    
                </select>
            </div>
            <div class="form-col-3">
                <label for="Country">Zipcode</label>
                <input type="text" id="source_zipcode" class="regular-text code">
            </div>
            <div style="clear:both" ></div>
        </div>
        <!---------section 1 finish----------------->
        <!---------section 2 start----------------->
        <div class="form-section form-section-2">
            <h3>Destination</h3>
            <div class="form-col-3">
                <label for="Country">Country</label>
                <select class="regular-text code" id="destination_country">
                    <option value="null">Select</option>
						<?php foreach($country as $key=>$value) { if($settingAtrs->sourceCountry == $key){ ?>
							<option value="<?php echo $key;?>" selected><?php echo $value; ?></option>
						<?php } else{ ?>
							<option value="<?php echo $key;?>"><?php echo $value; ?></option>
						<?php } } ?>
                </select>
            </div>
            <div class="form-col-3">
                <label for="State">State</label>
                <select class="regular-text code getcity" messg='Destination State' city-id="destination_city" id="destination_state">
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
            <div class="form-col-3">
                <label for="City">City</label>
                <select class="regular-text code"  id="destination_city">
                    
                </select>
            </div>
            <div class="form-col-3">
                <label for="Country">Zipcode</label>
                <input type="text" id="destination_zipcode" class="regular-text code">
            </div>
            <div style="clear:both" ></div>
        </div>
        <!---------section 2 finish----------------->
        <!---------section 3 start----------------->
        <div class="form-section form-section-3">
            <h3>Package</h3>
            <div class="form-col-4">
                <label for="services">Default Service</label>
                <select class="regular-text code" id="package_services">
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
            <div class="form-col-4">
                <label for="Packages">Packages</label>
                <input type="text" id="package_packages" class="regular-text code">
                </select>
            </div>
            <div class="form-col-4">
                <label for="Weight">Weight</label>
                <input type="text" id="package_weight" class="regular-text code">
            </div>
            <div style="clear:both" ></div>
        </div>
        <br>
        <button class="button" id="get_calculate">Calculate</button>
        <button class="button" id="get_clear">Reset</button>
        <!---------section 3 finish----------------->
    </div>


<!-------Loading Image Start---------------->
<div class="modal" id="loading_div_img">
  <div class="modal-content">                    
    <div class="calculate_tariff_img padding_modal">
      <p class="loading_img" style="color:black; font-size:20px;">
      <img src="<?php echo plugin_dir_url( __FILE__ ); ?>/img/loading.gif"><br>Getting Tariff Details</p>
      <p class="loading_content" style="color:white; font-size:20px;">
         
      </p>
      <div class="close_footer">
    <button class="buttton" id="closemodals" disabled>Close</button>
      </div>
    </div>  
  </div>  
</div>
<!-------Loading Image Finish---------------->

</div>


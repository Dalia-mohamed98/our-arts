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
<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<?php
include 'country.php';
include 'getServices.php';
global $wpdb;
$settingAtrs = $wpdb->get_row( "SELECT * FROM wp_logixgridsetting WHERE ID = 1");

?>
<div class="wrap">
	<h2>Plugin Settings</h2>
	<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST" >
		<table class="form-table">	
			<tbody>
			<tr>
				<th><label for="plugin_name">Plugin Name</label></th>
				<td> <input type="text" name="plugin_name" type="text" class="regular-text code"  value="<?php print_r($settingAtrs->pluginName); ?>" required> Default Name: Plugin Name</td>
			</tr>
			<tr>
				<th><label for="api_url">Api URL</label></th>
				<td> <input type="text" name="api_url" type="url" class="regular-text code"  value="<?php print_r($settingAtrs->apiURL); ?>" required></td>
			</tr>
			<tr>
				<th><label for="secure_key">Secure Key</label></th>
				<td> <input type="text" name="secure_key" class="regular-text code"  value="<?php print_r($settingAtrs->secureKey); ?>" required></td>
			</tr>
			<tr>
				<th><label for="access_key">Access Key</label></th>
				<td> <input type="text" name="access_key" type="text"  class="regular-text code" value="<?php print_r($settingAtrs->accessKey); ?>" required></td>
			</tr>
			<tr>
				<th><label for="customer_code">Customer Code</label></th>
				<td> <input type="text" name="customer_code" type="text" class="regular-text code"  value="<?php print_r($settingAtrs->customerCode); ?>" required></td>
			</tr>
			<tr>
				<th><label for="service_code">Service Code</label></th>
				<td>
				<select class="regular-text code" name="service_code" required>
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

			</tr>
			<tr>
				<th><label for="service_code">Default Source Country</label></th>
				<td>
					<select name="source_country" class="regular-text code" required>
						<option value="">Select</option>
						<?php foreach($country as $key=>$value) { if($settingAtrs->sourceCountry == $key){ ?>
							<option value="<?php echo $key;?>" selected><?php echo $value; ?></option>
						<?php } else{ ?>
							<option value="<?php echo $key;?>"><?php echo $value; ?></option>
						<?php } } ?>
                    	
					</select>
				 
				 </td>
			</tr>
			<tr>
				<th><label for="get_status_link">Tracking Endpoint</label></th>
				<td> <input type="text" name="get_status_link" type="text"  value="<?php echo get_home_url(); ?>/wp-json/wp/v2/rae/post/setStatus" class="regular-text code" readonly></td>
			</tr>
			
			</tbody>
		<table>
		<input type="hidden" name="action" value="add_setting">
  		<input type="submit" class="button" value="Update Setting" name="settingsubmit">
	</form>
 </div>

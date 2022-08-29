<?php
/*  
* Plugin Name: mylerz  
* Plugin URI:  
* Description: Convenient and Friendly parcel delivery service
* Author: Softec  
* Version: 1.0.0  
* Author URI:  
* License: 
* Text Domain:  
* Domain Path: /languages/  
*/
?>
<?php
/**
 *  Render "Bulk" form
 *
 * @return string Template
 */
function display_bulkFulfillment_button()
{
    $get_userdata = get_userdata(get_current_user_id());
    if (
        !$get_userdata->allcaps['edit_shop_order'] || !$get_userdata->allcaps['read_shop_order'] || !$get_userdata->allcaps['edit_shop_orders'] || !$get_userdata->allcaps['edit_others_shop_orders']
        || !$get_userdata->allcaps['publish_shop_orders'] || !$get_userdata->allcaps['read_private_shop_orders']
        || !$get_userdata->allcaps['edit_private_shop_orders'] || !$get_userdata->allcaps['edit_published_shop_orders']
    ) {
        return false;
    }
?>


    <div id="myModal" class="modal">

        <!-- Modal content -->
        <div class="modal-content">
            <button onclick="printJS('canvasDiv', 'html')">Print</button>
            <span class="close">&times;</span>
            <div id="canvasDiv">

            </div>
        </div>

    </div>
    <div id="warehouseModal" class="modal">

        <!-- Modal content -->
        <div class="modal-content">
            <select id="warehousedropdown">
            </select>

            <button type="button" id="closeWarehouseSelect">Close</button>
            <button type="button" id="fulfill">Fulfill</button>
            <!-- <button type="button" id="test">test</button> -->
        </div>

    </div>
    <div class="loader" style="display: none;"></div>


    <script type="text/javascript">
        jQuery.noConflict();
        (function($) {

            var validate = async () => {

                let postData = {
                    action: 'validateAndGenerateNewToken',
                }

                try {

                    let warehousesArray = await jQuery.post(ajaxurl, postData, function(response) {
                        let result = JSON.parse(response);
                        console.log("Validate Request ---->", result);


                        if (result["Status"] == "Failed") {
                            alert(result["Message"])
                        } else {
                            // let warehouseDropDown = $('#warehousedropdown')
                            let warehouses = result["Warehouses"]
                            $('#warehousedropdown').append(new Option("Select Warehouse", "", true))
                            warehouses.forEach(warehouse => {
                                $('#warehousedropdown').append(new Option(warehouse, warehouse,))

                            });
                            return result
                        }
                    });

                    return JSON.parse(warehousesArray)["Warehouses"];

                } catch (error) {
                    console.log(error);
                }

            }

            $(document).ready((async () => {
                $('.page-title-action').first().after("<button type= 'button' class=' page-title-action' style='margin-left:15px;' id='bulkFulfillment'><?php echo esc_html__('Bulk Fulfillment by Mylerz', 'mylerz'); ?> </button>");

                let warehouses = await validate()
                // $("#warehouseModal").css("display", "block");
                $('#bulkFulfillment').click(async () => {
                    let doesAllItemsHasWarehouse = await checkItemsWarehouse()
                    if (warehouses.length>1 && !doesAllItemsHasWarehouse) {
                        $("#warehouseModal").show()
                        $("#warehousedropdown option").first().prop('disabled', "disabled");
                        $('#fulfill').click(emptyWarehouseFulfill);
                    } else {
                        fulfill(warehouses[0])
                    }
                })

                $('.close').first().click(async () => {
                    console.log("close clicked");
                    $("#myModal").hide()
                    window.location.reload();

                });

                $('#closeWarehouseSelect').click(() => {
                    $("#warehouseModal").hide()
                });

            })());


            var bulkFulfill = async (bulkWarehouse) => {
                console.log("in bulkfulfill");
                let selectedFulfilled = [];
                let selectedCompleted = [];
                let selected = [];

                $('.status-wc-fulfilled input:checked').each(function() {
                    selectedFulfilled.push($(this).val());
                });
                $('.status-wc-completed input:checked').each(function() {
                    selectedCompleted.push($(this).val());
                });
                $('.iedit input:checked').each(function() {
                    selected.push($(this).val());
                });



                console.log("selected --->", selected);

                if (selected.length === 0) {

                    alert("<?php echo esc_html__('Select orders, please'); ?>");

                } else if (selectedCompleted.length > 0 || selectedFulfilled.length > 0) {
                    alert("<?php echo esc_html__('Cannot Fulfill Completed or Previously Fulfilled Orders..'); ?>");
                } else {

                    let postData = {
                        action: 'bulkFulfillOrdersById',
                        ordersIds: selected,
                        warehouse: bulkWarehouse
                    }
                    await jQuery.post(ajaxurl, postData, function(response) {
                        let result = JSON.parse(response);


                        console.log("end Request ---->", result);

                        if (result["Status"] == "Failed") {
                            alert(result["Message"])
                        } else {

                            result.AWBList.map((awb, index) => {
                                $('#canvasDiv').append(`<canvas id='canvas_${index}'></canvas>`)
                                viewAWB(awb, `canvas_${index}`)
                            })

                            $("#myModal").show()
                        }
                    });


                    console.log("last Line");
                }

            }

            var emptyWarehouseFulfill = ()=>{
                fulfill("")
            }


            var fulfill = async (bulkWarehouse="") => {
                console.log("OnClick");
                try {

                    $("#warehouseModal").hide()
                    $(".loader").css("display", "block");
                    $("#bulkFulfillment").prop("disabled", true);
                    
                    if (bulkWarehouse == "") {    
                        bulkWarehouse = $("#warehousedropdown").val()
                    }

                    console.log(bulkWarehouse)

                    await bulkFulfill(bulkWarehouse);

                    $("#bulkFulfillment").prop("disabled", false);
                    $(".loader").css("display", "none");

                } catch (error) {


                    $(".loader").css("display", "none");
                    $("#bulkFulfillment").prop("disabled", false);

                    alert("Something Went Wrong!")

                    console.log(error);
                }

            }

            var checkItemsWarehouse = async () => {
                let selectedFulfilled = [];
                let selectedCompleted = [];
                let selected = [];

                $('.status-wc-fulfilled input:checked').each(function() {
                    selectedFulfilled.push($(this).val());
                });
                $('.status-wc-completed input:checked').each(function() {
                    selectedCompleted.push($(this).val());
                });
                $('.iedit input:checked').each(function() {
                    selected.push($(this).val());
                });



                console.log("selected --->", selected);

                if (selected.length === 0) {

                    alert("<?php echo esc_html__('Select orders, please'); ?>");

                } else if (selectedCompleted.length > 0 || selectedFulfilled.length > 0) {
                    alert("<?php echo esc_html__('Cannot Fulfill Completed or Previously Fulfilled Orders..'); ?>");
                } else {

                    let postData = {
                        action: 'checkItemWarehouses',
                        ordersIds: selected,
                    }

                    try {

                        let warehousesArray = await jQuery.post(ajaxurl, postData, function(response) {
                            let result = JSON.parse(response);
                            console.log("Check ItemWarehouse ---->", result);


                            // if (result["Status"] == "Failed") {
                            //     alert(result["Error"])
                            // } else {

                            return result
                            // }
                        });

                        return JSON.parse(warehousesArray)["ItemWarehouse"];

                    } catch (error) {
                        console.log(error);
                    }
                }
            }



        })(jQuery);
    </script>
<?php
} ?>
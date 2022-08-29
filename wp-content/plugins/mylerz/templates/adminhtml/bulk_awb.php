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
function display_bulkPrintAWB_button()
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

    <div class="loader" style="display: none;"></div>


    <script type="text/javascript">
        jQuery.noConflict();
        (function($) {
            $(document).ready(function() {
                $('.page-title-action').first().after("<button type='button' class=' page-title-action' style='margin-left:15px;' id='bulkPrintAWB'><?php echo esc_html__('Bulk Print AWB', 'mylerz'); ?> </button>");

                $('#bulkPrintAWB').click(async () => {
                    console.log("OnClick print awb");

                    try {
                        $(".loader").css("display", "block");
                        $("#bulkPrintAWB").prop("disabled",true);

                        await bulkPrintAWB();
                        
                        $("#bulkPrintAWB").prop("disabled",false);
                        $(".loader").css("display", "none");

                    } catch (error) {

                        $(".loader").css("display", "none");
                        $("#bulkPrintAWB").prop("disabled",false);
                        
                        alert("Something Went Wrong!")
                        
                        console.log(error);

                    }

                });

                $('.close').first().click(async () => {
                    console.log("close clicked");
                    $("#myModal").hide()
                });


            });




            var bulkPrintAWB = async () => {

                console.log("in printAWB");
                let selectedFulfilled = [];
                let selected = [];

                $('.status-wc-fulfilled input:checked').each(function() {
                    selectedFulfilled.push($(this).val());
                });
                $('.iedit input:checked').each(function() {
                    selected.push($(this).val());
                });


                if (selectedFulfilled.length === 0 || selectedFulfilled.length !== selected.length) {

                    alert("<?php echo esc_html__('All Selected Orders must be fulfilled by mylerz'); ?>");

                } else {

                    console.log("selectedFulfilled --->", selectedFulfilled);
                    console.log("selected --->", selected);
                    console.log("cond --->", (selectedFulfilled.length === selected.length));


                    let postData = {
                        action: 'bulkPrintAWB',
                        ordersIds: selectedFulfilled
                    }
                    await jQuery.post(ajaxurl, postData, function(response) {
                        console.log("end Request ---->", response);

                        JSON.parse(response).AWBList.map((awb, index) => {
                            $('#canvasDiv').append(`<canvas id='canvas_${index}' style="width:100%;"></canvas>`)
                            viewAWB(awb, `canvas_${index}`)
                        })

                        $("#myModal").show()
                    });


                    console.log("last Line");
                }
            }




        })(jQuery);
    </script>
<?php
} ?>
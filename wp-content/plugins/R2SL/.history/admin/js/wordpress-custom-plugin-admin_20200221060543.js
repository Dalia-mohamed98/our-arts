function getSelected()
{
  var getValue = [];
  var checkedValues = jQuery('.order_checkbox:checkbox:checked').map(function() {
    getValue.push(jQuery(this).val());
  }).get();
  return getValue;
}

jQuery(document).ready( function () {

  jQuery("#closemodalload").click(function()
  {
    jQuery('#loading_div_img').hide();
  });
	jQuery("#doaction").click(function()
	{
      var getd = getSelected();
      
      var selectedlength = getd.length;
      if(selectedlength == 0) 
      {
        alert('Please select at leat one checkbox');
        return;
      }
     else if(selectedlength > 10) 
      {
        alert('Please select maximum  10 checkboxs');
        return;
      }
      else
      {
        
        jQuery('#loading_div_img').show();
        //console.log(getd);
        var order_lists = getd.toString();
        jQuery('#order_numbers_20').val(order_lists);
      }
  });

  jQuery("#gets_code_waybill").click(function()
    {
      var getValues = [];
      var orderid = jQuery('#order_numbers_20').val();
      var selectservice = jQuery('#service_20').val();
      var inputcodes = jQuery('#customercode_20').val();
      console.log(orderid+' , '+selectservice+' , '+inputcodes);
      if(selectservice == 'null' || inputcodes == '')
        {
          return alert('Please enter Service Code or Customer Code');
        }
      else
        {
          getValues.push(orderid,selectservice,inputcodes);  
          //console.log(getValues);
          jQuery(this).text('Processing....');
          jQuery(this).prop("disabled",true);

            var formData = {
                  'action'        : 'example_ajax_request',
                  'orderdetails'  : getValues
              };
            jQuery.ajax({
            url: ajaxurl,
            data: formData,
            success: function(data)
              {
                  console.log(data);
                  jQuery('#closemodalload, #gets_code_waybill').hide();
                  jQuery('#gets_code_waybillclose').show();
                  jQuery('#service_code_add').html(data);
              },
            error: function(xhr, error){
              console.debug(error);
              }
              });
        }
      
    });

    jQuery("#gets_code_waybillclose").click(function()
    {
      jQuery('#loading_div_img').hide();
      jQuery('#service_code_add').html('');
    });

    /*--------Open Modal Start---------*/
    jQuery(".view_history").click(function()
    {
      jQuery('#waybill_detail_content_main .waybill_detail_content_inner').html(' ');
      jQuery('.waybill_detail_content p').show();
      jQuery('#myModal').show();
      var wbID = jQuery(this).attr('id');
      var wbData = {
                'action'    : 'getHistory',
                'wbid'  : wbID
            };
      jQuery.ajax
      ({
          url: ajaxurl,
          data: wbData,
          success: function(data)
          {
            jQuery('.waybill_detail_content p').hide();
            jQuery('#waybill_detail_content_main .waybill_detail_content_inner').html(data);
          },
          error: function(xhr, error){
            //console.debug(error);
            }
      });
    }); 
    
    jQuery("#closemodal").click(function()
  {
    jQuery('#myModal').hide();
  });
    
    
    /*--------Open Modal Finish---------*/

    /*--------Calculate tariff Start---------*/
    jQuery("#get_calculate").click(function()
    {
        var scountry = jQuery('#source_country').val();
        var sstate = jQuery('#source_state').val();
        var scity = jQuery('#source_city').val();
        var szip = jQuery('#source_zipcode').val();
        var dcountry = jQuery('#destination_country').val();
        var dstate = jQuery('#destination_state').val();
        var dcity = jQuery('#destination_city').val();
        var dzip = jQuery('#destination_zipcode').val();
        var pservices = jQuery('#package_services').val();
        var ppackages = jQuery('#package_packages').val();
        var pweight = jQuery('#package_weight').val();
        if(scountry == 'null'){alert('Please select source country');}
        else if(sstate == 'null'){alert('Please select source state');}
        else if(sstate == 'notfound'){alert('Please contact to admin for states list');}
        else if(scity == 'null'){alert('Please select source city');}
        else if(dcountry =='null'){alert('Please select destination country');}
        else if(dstate == 'null'){alert('Please select destination state');}
        else if(dstate == 'notfound'){alert('Please contact to admin for states list');}
        else if(dcity == 'null'){alert('Please select destination city');}
        else if(pservices =='null'){alert('Please select package service');}
        else if(ppackages ==''){alert('Please enter number of packages');}
        else if(pweight ==''){alert('Please enter weight');}
        else{
          jQuery('#loading_div_img').show();
          var tariffData = {
            'action'    : 'getCalculateTariff',
            'scountry'  : scountry,
            'sstate'    : sstate,
            'scity'     : scity,
            'szip'      : szip,
            'dcountry'  : dcountry,
            'dstate'    : dstate,
            'dcity'     : dcity,
            'dzip'      : dzip,
            'pservices' : pservices,
            'ppackages' : ppackages,
            'pweight'   : pweight,
          };

          jQuery.ajax
          ({
              url: ajaxurl,
              data: tariffData,
              success: function(data)
              {
                jQuery('#loading_div_img .loading_img').hide();
                jQuery('#loading_div_img .loading_content').html(data);
                jQuery('#closemodals').prop("disabled",false);
              },
              error: function(xhr, error){
                console.debug(error);
                }
          });

        }
 
    }); 

    jQuery("#source_zipcode, #destination_zipcode, #package_weight, #package_packages, #pickupzipcode, .number").keypress(function (e) {
      if (String.fromCharCode(e.keyCode).match(/[^0-9]/g)) return false;
    });

    jQuery("#closemodals, #gets_code_waybillclose").click(function()
    {
      jQuery('#loading_div_img').hide();
      window.location.reload(1);
    });
    /*--------Calculate tariff finish---------*/

    /*--------Pickup request start---------*/

    function getSelectedpickup()
    {
      var getpickupValue = [];
      var checkedValues = jQuery('.pr_checkbox:checkbox:checked').map(function() {
        getpickupValue.push(jQuery(this).val());
      }).get();
      return getpickupValue;
    }
    jQuery("#closemodalpickup").click(function()
    {
      jQuery('#pickuprequestmainform').hide();
    });
     jQuery("#pickuprequestingclose").click(function()
    {
      jQuery('#pickuprequestmainform').hide();
      window.location.reload(1);
    });
    jQuery("#createpickup").click(function()
    {
        var waybillnumbers = getSelectedpickup();
        var selectedlengthchk = waybillnumbers.length;
        if(selectedlengthchk == 0) 
        {
          alert('Please select at leat one checkbox');
          return;
        }
        else{
          var add = jQuery('#'+waybillnumbers[0]+' .pr_checkbox').attr('data-address');
          var state = jQuery('#'+waybillnumbers[0]+' .pr_checkbox').attr('data-state');
          var city = jQuery('#'+waybillnumbers[0]+' .pr_checkbox').attr('data-city');
          jQuery('#pickupstate').val(state);
          jQuery('#pickupcity').val(city);
          jQuery('#pickupaddress').val(add);
          jQuery('#Waybillnumbers').val(waybillnumbers);
          jQuery('#pickuprequestmainform').show();
        }
    });

    jQuery("#creatingpickuprequesting").click(function()
    {
        var readytime = jQuery('#readytime').val();
        var latesttimeAvailable = jQuery('#latesttimeAvailable').val();
        var pickupcountry = jQuery('#pickupcountry').val();
        var pickupstate = jQuery('#pickupstate').val();
        var pickupcity = jQuery('#pickupcity').val();
        var pickupaddress = jQuery('#pickupaddress').val();
        var pickupzipcode = jQuery('#pickupzipcode').val();
        var pickupdate = jQuery('#pickupdate').val();
        var clientcode = jQuery('#clientcode').val();
        var Waybillnumbers = jQuery('#Waybillnumbers').val();
        var pickuptype = jQuery('#pickuptype').val();
        var specialinstruction = jQuery('#specialinstruction').val();
        if(readytime == ''){alert('Please enter readytime');}
        else if(latesttimeAvailable == ''){alert('Please enter latest time available');}
        else if(pickupstate == 'null'){alert('Please enter pickup state');}
        else if(pickupstate == 'notfound'){alert('Please contact to admin for states list');}
        else if(pickupcity == 'null'){alert('Please enter pickup city name');}
        else if(pickupaddress == ''){alert('Please enter pickup address');}
        else if(pickupdate == ''){alert('Please enter pickup date');}
        else if(clientcode == ''){alert('Please enter client code');}
        else{
            
            jQuery('#creatingpickuprequesting').prop("disabled",true);
            jQuery(this).text('Processing....');
          var pickupData = {
            'action'    : 'getpickupRequest',
            'readytime'  : readytime,
            'latesttimeAvailable'    : latesttimeAvailable,
            'pickupcountry'     : pickupcountry,
            'pickupstate'      : pickupstate,
            'pickupcity'  : pickupcity,
            'pickupaddress'    : pickupaddress,
            'pickupzipcode'     : pickupzipcode,
            'pickupdate'     : pickupdate,
            'clientcode'     : clientcode,
            'Waybillnumbers'      : Waybillnumbers,
            'pickuptype' : pickuptype,
            'specialinstruction':specialinstruction
          };

          jQuery.ajax
          ({
              url: ajaxurl,
              data: pickupData,
              success: function(data)
              {
                jQuery('#creatingpickuprequesting').hide();
                jQuery('#closemodalpickup').hide();
                jQuery('.pickup_request_form_content_inner').html(data);
                jQuery('#pickuprequestingclose').show();
                console.log(data);
              },
              error: function(xhr, error){
                console.debug(error);
                }
          });
        }
    });

    /*--------Pickup request finish---------*/

    /*--------Get City Names Request Start---------*/

    jQuery('.getcity').on('change', function() {
      var cityId = jQuery(this).attr('city-id');
      var stateName = jQuery(this).val();
      if(stateName == 'null')
      {
        alert('Please Select '+jQuery(this).attr('messg'));
      }
      else
      {
        var pleasewait = '<option value="null">Getting..</option>' ;
        jQuery('#'+cityId).append(pleasewait);
        var StData = {
            'action'    : 'getCityName',
            'statecode'  : stateName
        };
          jQuery.ajax
            ({
                url: ajaxurl,
                data: StData,
                success: function(data)
                {
                  //console.log(data);
                  jQuery('#'+cityId).html(data);
                },
                error: function(xhr, error){
                  console.debug(error);
                  }
            });
        }
      });

    /*--------Get City Names Request Finish---------*/

    /*--------Add-Remove Package Index Start---------*/
    
    jQuery("#addpackageindex").click(function()
    {
        var i =jQuery(this).attr('id-index');
        i++;
        var indexcontant = '<tr id="'+i+'"><td class="indexidval">'+i+'</td><td><input type="text" class="packtype"></td><td><input type="text" class="packdes"></td><td><input type="text" class="packqua number"></td><td><input type="text" class="itemqua number"></td><td><input type="text" id="len_'+i+'" class="length number-float"></td><td><input type="text" id="wid_'+i+'" class="width number-float"></td><td><input id="hei_'+i+'" type="text" class="height number-float"></td><td><input type="text" readonly id="'+i+'"  class="get_charegeweigh aclwg number-float"></td><td><input readonly  id="'+i+'" type="text" class="get_charegeweigh chwg number-float"></td><td><a class="deleteindex" ind-id="'+i+'" href="javascript:void(0);">Delete</a></td></tr>';
        jQuery("#packagedetailtable tbody").append(indexcontant);
        jQuery(this).attr('id-index',i);
    });

    jQuery('#packagedetailtable').on('click', '.get_charegeweigh', function()
    {
      var idvalue =jQuery(this).attr('id');
      var l =  jQuery('#len_'+idvalue).val();
      var b =  jQuery('#wid_'+idvalue).val();
      var h =  jQuery('#hei_'+idvalue).val();
      var cal1 = (l*b*h)/5000;
      jQuery('tr#'+idvalue+' .get_charegeweigh').val(cal1);
    });

    jQuery('#packagedetailtable').on('keyup', '.get_charegeweigh', function(e)
    {
      e.preventDefault(); 
      var idvalue =jQuery(this).attr('id');
      var l =  jQuery('#len_'+idvalue).val();
      var b =  jQuery('#wid_'+idvalue).val();
      var h =  jQuery('#hei_'+idvalue).val();
      var cal1 = (l*b*h)/5000;
      jQuery('tr#'+idvalue+' .get_charegeweigh').val(cal1);
      
    });

    jQuery('#packagedetailtable').on('click', '.deleteindex', function(e)
    {
      e.preventDefault();
      var indexvalue =jQuery(this).attr('ind-id');
      //alert(indexvalue);
      jQuery('tr#'+indexvalue).remove();
    });

    jQuery("body").on('keypress', '.number-float', function (e) {
      if((event.which != 46 || jQuery(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {event.preventDefault(); }
    });

    jQuery("#packagedetailtable").on('keypress', '.number', function (e) {
      if (String.fromCharCode(e.keyCode).match(/[^0-9]/g)) return false;
    });

    function getPAckageDetialList()
    {
      var packageDetails = []; 
      jQuery("#packagedetailtable tbody tr").each(function()
            {
              var packtype = jQuery(this).find("td .packtype").val();
              var packqua = jQuery(this).find("td .packqua").val();
              var lengths = jQuery(this).find("td .length").val();
              var width = jQuery(this).find("td .width").val();
              var height = jQuery(this).find("td .height").val();
              var aclwg= jQuery(this).find("td .aclwg").val();
              var chwg = jQuery(this).find("td .chwg").val();
              packageDetails.push([packqua,lengths,width,height,aclwg,chwg,packtype]);
            });
            return packageDetails;
    }

    jQuery("#create_waybill_manual").click(function()
    { 
        var shipperDetails = [];
        var consigneeDetails = [];
        var basicDetails = [];
        var trCount = jQuery('#packagedetailtable tbody tr').length;
        /*--------Basic Detail Start--------------*/
        var service = jQuery('#service').val();
        var invoice_value = jQuery('#invoice_value').val();
        var invoice_number = jQuery('#invoice_number').val();
        var reference_number = jQuery('#reference_number').val();
        var cod_amount = jQuery('#cod_amount').val();
        var description = jQuery('#description').val();
        basicDetails.push(service,invoice_value,invoice_number,reference_number,cod_amount,description);
        /*--------Basic Detail Finish--------------*/
        /*--------Shipper Detail Start--------------*/
        var scompanyname = jQuery('#scompanyname').val();
        var scontactperson = jQuery('#scontactperson').val();
        var saddress = jQuery('#saddress').val();
        var sareaname = jQuery('#sareaname').val();
        var sphone = jQuery('#sphone').val();
        var semail = jQuery('#semail').val();
        var scountry = jQuery('#scountry').val();
        var sstate = jQuery('#sstate').val();
        var scity = jQuery('#scity').val();
        var spincode = jQuery('#spincode').val();
        shipperDetails.push(scompanyname,scontactperson,saddress,sareaname,sphone,semail,scountry,sstate,scity,spincode);
        /*--------Shipper Detail Finish--------------*/
        /*--------Consignee Detail Start--------------*/
        var ccompanyname = jQuery('#ccompanyname').val();
        var ccontactperson = jQuery('#ccontactperson').val();
        var caddress = jQuery('#caddress').val();
        var careaname = jQuery('#careaname').val();
        var cphone = jQuery('#cphone').val();
        var cemail = jQuery('#cemail').val();
        var ccountry = jQuery('#ccountry').val();
        var cstate = jQuery('#cstate').val();
        var ccity = jQuery('#ccity').val();
        var cpincode = jQuery('#cpincode').val();
        consigneeDetails.push(ccompanyname,ccontactperson,caddress,careaname,cphone,cemail,ccountry,cstate,ccity,cpincode);
        /*--------Consignee Detail Finish--------------*/
        if(service == 'null') { alert('Please select service'); }
        else if(scontactperson == '') { alert('Please enter shipper contact person name'); }
        else if(saddress == '') { alert('Please enter shipper address'); }
        else if(sphone == '') { alert('Please enter shipper phone number'); }
        else if(scountry == 'null') { alert('Please select shipper country'); }
        else if(sstate == 'null') { alert('Please select shipper state'); }
        else if(scity == 'null') { alert('Please select shipper city'); }
        else if(ccontactperson == '') { alert('Please enter consignee contact person name'); }
        else if(caddress == '') { alert('Please enter consignee address'); }
        else if(cphone == '') { alert('Please enter consignee phone number'); }
        else if(ccountry == 'null') { alert('Please select consignee country'); }
        else if(cstate == 'null') { alert('Please select consignee state'); }
        else if(ccity == 'null') { alert('Please select consignee city'); }
        else if(trCount == '0') { alert('Please add atleast one package details'); }
        else 
        {
          jQuery('#loading_div_img').show();
          jQuery('#loading_div_img .loading_img').show();
          jQuery('#loading_div_img .loading_content').html(' ');
          var packagelistdetail =  getPAckageDetialList();  
          var waybillData = {
            'action'    : 'createManualWaybill',
            'basicDetail'  : basicDetails,
            'shipperDetail' : shipperDetails,
            'consigneeDetail' : consigneeDetails,
            'packageDetails' : packagelistdetail,
          };
          jQuery.ajax
            ({
                url: ajaxurl,
                data: waybillData,
                success: function(data)
                {
                  console.log(data);
                  var reciveData = JSON.parse(data);
                  if(reciveData.status == 'error')
                  {
                    jQuery('#closemodalserror').show();
                  }
                  else
                  {
                    jQuery('#closemodals').show();
                  }
                  jQuery('#loading_div_img .loading_img').hide();
                  jQuery('#loading_div_img .loading_content').html(reciveData.message);
                },
                error: function(xhr, error){
                  console.debug(error);
                  }
            });
        }
    });
 

    /*--------Add-Remove Package Index Finish---------*/

});



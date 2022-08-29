jQuery(document).ready(function($){

    $('.cart-btn').on('click', function(event){
        event.preventDefault();
        if(parseFloat($(this).find('span').html()) > 0){
                  var button = $(this);
              $('.cart-btn').button('loading');
              var products = {};
              var quantities = [];
              $('.packages input[type="checkbox"]').prop('disabled', true);
              $('.packages input[type="checkbox"]:checked').each(function(){
                  var product = $(this);
                  var product_id = product.val();
                  var quantity = $('.quantity-box'+product_id).val();
                //   products.push(product_id);
                products[product_id] = quantity;
                  quantities.push(quantity);
                  console.log(products);
                  console.log(quantities);
              });
              
              $.ajax({
                  url: wc_add_to_cart_params.ajax_url,
                  type: 'post',
                  data: {
                      action: "add_ToCart",
                      products: products,
                      quantities: quantities,
                                  },
                  beforeSend: function() {
                          //$('#cart > button').button('loading');
                  },
                  complete: function() {
                          //$('#cart > button').button('reset');
                  },
                  success: function(json) {
                        console.log(json);
                        if(json[0] == 1)
                           { console.log("success");
                             window.location = 'https://our-arts.com/cart/';
                            }
                        else {
                            console.log("error");
                            alert(json + "\r\n");

                            // window.location = 'http://localhost/ourarts/تصميم-الموزاييك/';
                        //   $('.alert, .text-danger').remove();
                        //   $('.form-group').removeClass('has-error');
      
                          if (json['error']) {
                                  if (json['error']) {
                                          for (i in json['error']) {
                                                  var element = $('#input-option' + i.replace('_', '-'));
                                                  
                                                  if (element.parent().hasClass('input-group')) {
                                                          element.parent().after('<div class="text-danger">' + json['error'][i] + '</div>');
                                                  } else {
                                                          element.after('<div class="text-danger">' + json['error'][i] + '</div>');
                                                  }
                                          }
                                  }
                                  
                                //   if (json['error']['recurring']) {
                                //           $('select[name=\'recurring_id\']').after('<div class="text-danger">' + json['error']['recurring'] + '</div>');
                                //   }
                                  
                                  // Highlight any found errors
                                  $('.text-danger').parent().addClass('has-error');
      
                          }
                          
                        if (json['success']) {}
                        }
                  },
                  error: function(xhr, ajaxOptions, thrownError) {
                          alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                  }
              });
                  //$(this).closest('li').removeClass('nonchosen');
            //   setTimeout(function(){
            //           window.location = 'https://faggala.com/index.php?route=checkout/checkout&amp;order_school_id=0'.replace('&amp;', '&');;
            //   },1000);
          }
          else{
              alert('من فضلك قم باضافة المنتجات اولا قبل تأكيد الطلب');
          }
        
      });
      
});
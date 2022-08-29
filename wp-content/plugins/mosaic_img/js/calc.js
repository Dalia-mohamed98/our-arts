"use strict";

function dragNdrop(event) {
    var fileName = URL.createObjectURL(event.target.files[0]);
    var preview = document.getElementById("preview");
    var previewImg = document.createElement("img");
    previewImg.setAttribute("src", fileName);
    preview.innerHTML = "";
    preview.appendChild(previewImg);
    // var processbtn = document.createElement("input");
    // processbtn.setAttribute("type", 'submit');
    // processbtn.setAttribute("value", "Proccess Image");
    // processbtn.setAttribute("name", "upload_mosaic_img");
    // processbtn.addClass("btn");
    // preview.appendChild(processbtn);
    



}
function drag() {
    document.getElementById('uploadFile').parentNode.className = 'draging dragBox';
}
function drop() {
    document.getElementById('uploadFile').parentNode.className = 'dragBox';
}

function processImg(){
    var wait = document.getElementById('wait');
    wait.innerHTML = "سيستغرق هذا بضع ثوانٍ ، يرجى الانتظار....";
    
}


var hT = $('#nav-cat').offset().top;
var imgT = $('#sticky-img').offset().top;
var pack = $('.packages').offset().top;
var wH = $(window).height();
var wW = $(window).width();
var imgH = $('#sticky-img').height();
var imgW = $('#sticky-img').width();
var navH = $('#nav-cat').height();

var footer = wH - pack - $('.packages').height();  

$(window).scroll(function() {

  //sticky image
  if (window.pageYOffset > imgT ){
      console.log('img on the view!');
      
      //mobile or tablet view
      if(wH>wW ){
        $('#sticky-img').addClass("sticky");
        // $('#sticky-img').css("max-height","40%");
        $('#nav-cat').css({ "top": imgH , "position":"fixed","z-index":"3","width":"92%"});
        $('.content').css({"padding-top":imgH + navH});

      }
      //desktop view
      else{
        //padding =30px
        var marginR = imgW + 30;
        $('#sticky-img').addClass("sticky-desktop");
        $('#sticky-img').css("width",marginR);
        
        $(".nav-cat-col").css({"margin-right":marginR});
        $('#nav-cat').css({ "top": "0px", "position":"fixed","z-index":"3", "width":"57%"});
        $('.content').css({"padding-top":"60px"});


      }
  }
  else{
    $('#sticky-img').removeClass("sticky");
    $('#sticky-img').removeClass("sticky-desktop");
    $('#sticky-img').css("width","");

    console.log('removed!');
    $(".nav-cat-col").css({"margin-right":""});
    $('#nav-cat').css({ "top": "", "position":"","z-index":"","width":"" });
    $('.content').css({"padding-top":"", "margin-right":""});
  }
  
});


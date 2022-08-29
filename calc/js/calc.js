
new WOW().init();

// if(window.innerHeight > window.innerWidth){
//     alert("Please use Landscape!");
// }

$('#submitForm').submit(function(e){
    return false;
});

$('.social-icons a').removeClass('tooltip');

const body = document.body;
const btn = document.querySelectorAll('.done')[0];

btn.addEventListener('mouseenter', () => {
	body.classList.add('show');
});

btn.addEventListener('mouseleave', () => {
	body.classList.remove('show');
});


var selected;


$('img[data-enlargable]').addClass('img-enlargable').click(function(){
    var src = $(this).attr('src');
    $('.shape').hide();
    $('.choice').show();
    $('.back').css({
        display:'inline-block'
    });
    
    $('.back').click(function() {
      $('.shape').show();
      $('.divsquare').hide();
      $('.divrectangle').hide();
      $('.divtriangle').hide();
      $('.divCircle').hide();
      $('.divelipse').hide();
      $('.divparallelo').hide();
      $('.divtrapezoid').hide();
      $('.divsector').hide();
      $('.choice').hide();
      $(this).hide();
    });
    
    $('.unique').attr('src',src);
    

    if($(this).hasClass('square')){
        selected = 'مربع';
        $('.divsquare').css({display:'inline-block'});
        
    }

    if($(this).hasClass('rectangle')){
        selected = 'مستطيل';
        $('.divrectangle').css({display:'inline-block'});
   
  }

  if($(this).hasClass('triangle')){
      selected = 'مثلث';
      $('.divtriangle').css({display:'inline-block'});
   
  }
  
  if($(this).hasClass('parallelo')){
      selected = 'متوازي الاضلاع';
      $('.divparallelo').css({display:'inline-block'});
   
  }

  if($(this).hasClass('Circle')){
      selected = 'دائرة';
      $('.divCircle').css({display:'inline-block'});
      }

  if($(this).hasClass('elipse')){
      selected = 'بيضاوي';
      $('.divelipse').css({display:'inline-block'});
   
}

  if($(this).hasClass('trapezoid')){
      selected = 'شبه منحرف';
      $('.divtrapezoid').css({display:'inline-block'});
   
  }


  if($(this).hasClass('sector')){
       selected = 'قاطع';
       $('.divsector').css({display:'inline-block'});
   
  }

});



$('.arrow1').click(function() {
  $('html,body').animate({
      scrollTop: $('.step2').offset().top - 55
  }, 700);
//   $('.vl').height('+=50vh');
});

$('.arrow2').click(function() {
  $('html,body').animate({
      scrollTop: $('.step3').offset().top - 55
  }, 700);
//   $('.vl').height('+=50vh');
});

$('.arrow3').click(function() {
  $('.step4').css({
    opacity:'1'
  });
  $('html,body').animate({
      scrollTop: $('.step4').offset().top - 55
  }, 700);
//   $('.vl').height('+=50vh');
});

$('.calculate').click(function() {
    $('form :input').val('');
    $('input[type=checkbox]').prop('checked',false);
  $('html,body').animate({
      scrollTop: $('.start').offset().top - 55
  }, 700);
});


// start calculations



function getCheckedNum() {
	var selected;
	for (var loop=0;loop<window.document.area.to_which.length;loop++) {
		if (window.document.area.to_which[loop].checked == true) {
			selected = loop;
		}
	}
	return selected;
}

function mosaics(area,percent,piece){
 if(piece == true){
     return Math.round((area*percent)/4);
 }   
 else {
     return Math.round((area*percent)/(4*225)*100)/100;
 }
}

function glasses(area,percent,piece){
   
   if(piece == true){
     return Math.round(area*percent/6.25);
 }   
 else {
     return Math.round(area*percent);
    }
}

function stones(area,percent,piece){
    if(piece == true){
     return (Math.round(area*percent/4));
 }   
 else {
     return Math.round(area*percent/(4*400)*1000)/1000;
    
    }
}


function calculate() {
	var areaform = [];
    areaform["مربع"] = "Math.pow(sqbase,2)";
    areaform["مستطيل"] = "rwidth * rheight";
    areaform["مثلث"] = "trbase * trheight / 2";
    areaform["دائرة"] = "Math.PI * radius * radius";
    areaform["متوازي الاضلاع"] = "plbase * plheight";   
    areaform["شبه منحرف"] = 
    "0.5 * (tza*1 + tzb*1) * tzheight";
    areaform["بيضاوي"] = "Math.PI * ea * eb";
    areaform["قاطع"] = 
    "0.5 * sectorradius * sectorradius * (degrees * Math.PI) / 180";

	if(selected == 'مربع')
	    sqbase=window.document.area.sqbase.value;
	else if(selected == 'مثلث'){
	    trbase=window.document.area.trbase.value;
	    trheight=window.document.area.trheight.value;
	}
	else if(selected == 'مستطيل'){
	    rwidth=window.document.area.rwidth.value;
	    rheight=window.document.area.rheight.value;
	}
	else if(selected == 'متوازي الاضلاع'){
	    plbase=window.document.area.plbase.value;
	    plheight=window.document.area.plheight.value;
	}
	else if(selected == 'شبه منحرف'){
	    tzheight=window.document.area.tzheight.value;
	    tza=window.document.area.tza.value;
	    tzb=window.document.area.tzb.value;
	}
	else if(selected == 'دائرة')
	    radius=window.document.area.radius.value;
	else if(selected == 'بيضاوي'){
    	ea=window.document.area.ea.value;
    	eb=window.document.area.eb.value;
	}
	else if(selected == 'قاطع'){
    	sectorradius=window.document.area.sectorradius.value;
    	degrees=window.document.area.degrees.value;
	}
	factor=1+(window.document.area.percent.value/100);
	
	stone=window.document.area.stone.checked;
	mosaic=window.document.area.mosaic.checked;
	glass=window.document.area.glass.checked;
	
	stonePrc=window.document.area.stonePrc.value/100;
	mosaicPrc=window.document.area.mosaicPrc.value/100;
	glassPrc=window.document.area.glassPrc.value/100;
	

	formula = areaform[selected];
	var answer = eval(formula) * factor;

	if (answer>=0)
	{

		$('.shapeanswer').text(selected);
		$('.sqinanswer').text((Math.round(answer*1000))/1000);

        if(stone && mosaic && glass){
  
    		document.area.stone_kg.value=stones(answer,stonePrc,false);
    		document.area.stone_pcs.value=stones(answer,stonePrc,true);
    		
    		document.area.mosaic_sheet.value=mosaics(answer,mosaicPrc,false);
    		document.area.mosaic_pcs.value=mosaics(answer,mosaicPrc,true);
    		
    		if(answer/factor<2500)
    		    Garea = answer/factor*1.1;
    		else Garea = answer/factor*1.2;
    		side = (Math.round(Math.pow(glasses(Garea,glassPrc,false),0.5))).toString();
            document.area.glass_cm.value= ''.concat(side,'x',side);
    		document.area.glass_pcs.value=glasses(answer,glassPrc,true);
        }
        else if(stone && mosaic){
            
          $('input.result').val('');
        
            document.area.stone_kg.value=stones(answer,stonePrc,false);
    		document.area.stone_pcs.value=stones(answer,stonePrc,true);
    		
    		document.area.mosaic_sheet.value=mosaics(answer,mosaicPrc,false);
    		document.area.mosaic_pcs.value=mosaics(answer,mosaicPrc,true);
        }
        else if(stone && glass){
            
       
            $('input.result').val('');
            document.area.stone_kg.value=stones(answer,stonePrc,false);
    		document.area.stone_pcs.value=stones(answer,stonePrc,true);
    		if(answer/factor<2500)
    		    Garea = answer/factor*1.1;
    		else Garea = answer/factor*1.2;
    		side = (Math.round(Math.pow(glasses(Garea,glassPrc,false),0.5))).toString();
            document.area.glass_cm.value= ''.concat(side,'x',side);
    		document.area.glass_pcs.value=glasses(answer,glassPrc,true);
        }
        else if(glass && mosaic){
            
           
            $('input.result').val('');
            if(answer/factor<2500)
    		    Garea = answer/factor*1.1;
    		else Garea = answer/factor*1.2;
            side = (Math.round(Math.pow(glasses(Garea,glassPrc,false),0.5))).toString();
            document.area.glass_cm.value= ''.concat(side,'x',side);
            document.area.glass_pcs.value=glasses(answer,glassPrc,true);
            
            document.area.mosaic_sheet.value=mosaics(answer,mosaicPrc,false);
    		document.area.mosaic_pcs.value=mosaics(answer,mosaicPrc,true);
        }
        else if(stone){
            $('input.result').val('');
        
            document.area.stone_kg.value=stones(answer,stonePrc,false);
    		document.area.stone_pcs.value=stones(answer,stonePrc,true);
        }
        else if(mosaic){
            $('input.result').val('');
           
            document.area.mosaic_sheet.value=mosaics(answer,mosaicPrc,false);
    		document.area.mosaic_pcs.value=mosaics(answer,mosaicPrc,true);
        }
        else if(glass){
           
            $('input.result').val('');
            if(answer/factor<2500)
    		    Garea = answer/factor*1.1;
    		else Garea = answer/factor*1.2;
            side = (Math.round(Math.pow(glasses(Garea,glassPrc,false),0.5))).toString();
            document.area.glass_cm.value= ''.concat(side,'x',side);
            document.area.glass_pcs.value=glasses(answer,glassPrc,true);
        }
        else {
		    alert("You must choose at least one material with its percentage.  Please check your entries and try again.");
		    return false;
	    }
	}
	else
	{
		alert("All entries must be valid numbers to calculate a result.  Please check your entries and try again.");
		return false;
	}

}
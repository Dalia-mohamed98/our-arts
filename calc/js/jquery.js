$(function(){

	$(".mummy").show(1000,function(){

			$(".mine").addClass('animated fadeInLeft');

			// $(".mine").addClass('animated fadeInDown');
			


		});
		
	$(window).scroll(function() {
		/* Act on the event */
		// $(".description").animate({
		// 	width: 'toggle',
		// 	height: 'toggle'},
		// 	2000, function() {
		// 	/* stuff to do after animation is complete */
		// });
		 $(".description").addClass('animated fadeInUpBig');
		  $(".review").addClass('animated fadeInUpBig');
	})

	$(".fa-star").on("click",function(){

		
			for (var i = 5; i >= 0; i--) {
				if($(this).hasClass(i)){
					
					for (var j = i; j >= 0; j--) {
						$('.' + j).addClass('gold');
						$('.' + j).removeClass('bold');

					}
					for (var j = i+1; j <= 5; j++) {
						$('.' + j).addClass('bold');
						$('.' + j).removeClass('gold');

					}
				}
			}

				var count=0;
				    		for (var j = 5; j >= 0; j--) {
				    			if($('.gold').hasClass(j))
				    				count++;
				    		}
				$(".vote").html('Rating: ' + count*2 + '/10');

	});
			
		

}); 
	



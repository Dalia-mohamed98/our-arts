$(document).ready(function(){

	$("#Menu li:last-child").addClass("last");

	$("#cycle").cycle({
		fx: "fade"
	});

	function textReplacement(input) {
		var originalvalue = input.val();
		input.focus(function() {
			if($.trim(input.val()) == originalvalue){ input.val(""); }
		});
		input.blur(function() {
			if($.trim(input.val()) == ""){ input.val(originalvalue); }
		});
	}

	textReplacement($("#search_query"));
	textReplacement($("#nl_first_name"));
	textReplacement($("#nl_email"));	

//	jCarouselLite example -- you will need to add a <div class="ProductListContainer"> around the <ul>
//	in the HomeFeaturedProducts Panel

	$("#HomeFeaturedProducts .ProductListContainer").jCarouselLite({
       btnNext: "#featured-next",
       btnPrev: "#featured-prev",
	      visible: 5,
	 	  scroll: 5,
		  speed: 800
  });
	
	
	
//	$("a.fancybox").fancybox();

	//used for adding an active class to links to categories in pages menu when on that category or sub-category
	//if you do not want the active class remove this and .ActivePage style in the css
	function parseUri(sourceUri){
	 
	 var uriPartNames = ["source","protocol","authority","domain","port","path","directoryPath","fileName","query","anchor"],
	 uriParts = new RegExp("^(?:([^:/?#.]+):)?(?://)?(([^:/?#]*)(?::(\\d* <smb://d*>))?)((/(?:[^?#](?![^?#/]*\\.[^?#/.]+(?:[\\?#]|$)))*/?)?([^?#/]*))?(?:\ <smb://?(%5B%5E#%5D*)>\?([^#]*) <smb://?(%5B%5E#%5D*)>)?(?:#(.*))?").exec(sourceUri),
	 uri = {};
	
	 for(var i = 0; i < 10; i++){
	 uri[uriPartNames[i]] = (uriParts[i] ? uriParts[i] : "");
	 }
	
	 /* Always end directoryPath with a trailing backslash if a path was present in the source URI
	 Note that a trailing backslash is NOT automatically inserted within or appended to the "path" key */
	 if(uri.directoryPath.length > 0){
	 uri.directoryPath = uri.directoryPath.replace(/\/?$/, "/");
	 }
	 
	 return uri;
	 
	}
	
	var url = parseUri(window.location); // this gets the current url
	
	$("#Menu ul li a").each( function (){ // for each menu item in the UL start processing...
	
	var href = $(this).attr('href').split('/'); // this is an array of the items in the menu item in the loop
	var currLocation = url.path; // this is URI of the current url
	var menuHref = currLocation.split('/'); // this is an array of the items in the URI
	if ( menuHref[2] == href[2] ) {
		// if the current category = the category of the the menu item in the loop...
		// in this case if the url category is NCAA and the menu "a" is NCAA, then proceed below to add the class
	$(this).parent().addClass('ActivePage'); // add class "ActivePage" to the LI of the "a"
	}
	
	});

	
	$(".SubCategoryListGrid li:last-child").css("display","none");
	
	
	
	
	
	
});
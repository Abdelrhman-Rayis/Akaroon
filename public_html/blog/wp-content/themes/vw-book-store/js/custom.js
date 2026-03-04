function vw_book_store_search_open() {
	jQuery(".serach_outer").slideDown(1000);
}
function vw_book_store_search_close() {
	jQuery(".serach_outer").slideUp(1000);
}

function vw_book_store_menu_open_nav() {
	document.getElementById("mySidenav").style.top ="0";
}
function vw_book_store_menu_close_nav() {
 	document.getElementById("mySidenav").style.top = "-110%";
}

jQuery(function($){
 	"use strict";
   	jQuery('.main-menu > ul').superfish({
		delay:       500,
		animation:   {opacity:'show',height:'show'},
		speed:       'fast'
   	});
});

(function( $ ) {
	jQuery(window).load(function() {
	    jQuery("#status").fadeOut();
	    jQuery("#preloader").delay(1000).fadeOut("slow");
	})
	$(window).scroll(function(){
		var sticky = $('.header-sticky'),
			scroll = $(window).scrollTop();

		if (scroll >= 200) sticky.addClass('header-fixed');
		else sticky.removeClass('header-fixed');
	});
	$(document).ready(function(){
		$(".product-cat").hide();
	    $("button.product-btn").click(function(){
	        $(".product-cat").toggle();
	    });
	});
	$(document).ready(function () {
		$(window).scroll(function () {
		    if ($(this).scrollTop() > 100) {
		        $('.scrollup i').show();
		    } else {
		        $('.scrollup i').hide();
		    }
		});
		$('.scrollup i').click(function () {
		    $("html, body").animate({
		        scrollTop: 0
		    }, 600);
		    return false;
		});
	});
})( jQuery );
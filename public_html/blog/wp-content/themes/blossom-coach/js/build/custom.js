jQuery(document).ready(function($) {

	var rtl, mrtl, winWidth, stickyBarHeight;
	
	winWidth = $(window).width();
	
	$('.sticky-t-bar').addClass('active');
	$('.sticky-t-bar .sticky-bar-content').show();
	$('.sticky-t-bar .close').click(function(){
		if($('.sticky-t-bar').hasClass('active')){
			$('.sticky-t-bar').removeClass('active');
			$('.sticky-t-bar .sticky-bar-content').stop(true, false, true).slideUp();
		}else{
			$('.sticky-t-bar').addClass('active');
			$('.sticky-t-bar .sticky-bar-content').stop(true, false, true).slideDown();
		}
	});
	
	$(window).on('load resize', function() {
		stickyBarHeight = $('.sticky-t-bar .sticky-bar-content').innerHeight();
		$('.site-header').css('padding-top', stickyBarHeight);
	});

	var siteWidth = $('.site').width();
	var containerWidth = $('.site-header .wrapper').width();
	var halfwidth = (parseInt(siteWidth) - parseInt(containerWidth)) / 2;
	$('.custom-background .sticky-t-bar span.close').css('right', halfwidth);

	//search toggle js
	$('.header-search > button').click(function(){
		$(this).siblings('.header-search-form').addClass('active');
		$('body').addClass('search-active');
	});
	$('.header-search .close').click(function(){
		$(this).parent('.header-search-form').removeClass('active');
		$('body').removeClass('search-active');
	});
	$(window).keyup(function(event){
		if(event.key == "Escape") {
			$('.header-search-form').removeClass('active');
			$('body').removeClass('search-active');
		}
	});
	
	$('.main-navigation .toggle-button').click(function(){
		$(this).parent('.main-navigation').toggleClass('menu-toggled');
	});
	$('.main-navigation ul li.menu-item-has-children').append('<button class="submenu-toggle"><i class="fa fa-angle-down"></i></button>');
    $('.main-navigation ul li button').on('click', function(){
        $(this).toggleClass('active');
        $(this).siblings('.main-navigation ul ul').slideToggle();
    });
    $('.main-navigation .menu').scroll(function(){
        if($(this).scrollTop() > 20) {
            $('.main-navigation.menu-toggled .toggle-button').hide();
        }else{
            $('.main-navigation.menu-toggled .toggle-button').show();
        }
	});

	//for accessibility
	$('.main-navigation ul li a').focus(function() {
		$(this).parents('li').addClass('focused');
	}).blur(function() {
		$(this).parents('li').removeClass('focused');
	});
	
    //back to top js	
    $(window).scroll(function(){
    	if($(this).scrollTop() > 200) {
    		$('.back-to-top').addClass('show');
    	}else {
    		$('.back-to-top').removeClass('show');
    	}
    });

    $('.back-to-top').click(function(){
    	$('html, body').animate({
    		scrollTop:0
    	}, 1000);
    });
    
    if( blossom_coach_data.rtl == '1' ){
    	rtl  = true;
    	mrtl = false;
    }else{
    	rtl  = false;
    	mrtl = true;
    }
    
    $('.grid-view .site-main').imagesLoaded( function(){
    	$('.grid-view:not(.woocommerce):not(.search-no-results):not(.no-post) .site-main').masonry({
    		itemSelector : '.hentry',
    		isOriginLeft : mrtl,
    	});
    });

    // banner slider
    $('#banner-slider').owlCarousel({
    	loop       : false,
    	mouseDrag  : false,
    	margin     : 0,
    	nav        : true,
    	items      : 1,
    	dots       : false,
    	autoplay   : false,
    	navText    : '',
    	rtl        : rtl,
    	lazyLoad   : true,
    	animateOut : blossom_coach_data.animation,
    });
    
});
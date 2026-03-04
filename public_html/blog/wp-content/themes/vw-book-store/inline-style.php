<?php
	
	/*---------------------------First highlight color-------------------*/

	$vw_book_store_first_color = get_theme_mod('vw_book_store_first_color');

	$vw_book_store_custom_css = '';

	if($vw_book_store_first_color != false){
		$vw_book_store_custom_css .=' .search-bar, .more-btn a:hover, .woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, input[type="submit"], #footer .tagcloud a:hover, #sidebar .custom-social-icons i, #footer .custom-social-icons i, #footer-2, #sidebar input[type="submit"], #sidebar .tagcloud a:hover, nav.woocommerce-MyAccount-navigation ul li, .blogbutton-small, #comments input[type="submit"].submit, .pagination span, .pagination a, #comments a.comment-reply-link, #footer a.custom_read_more, #sidebar a.custom_read_more{';
			$vw_book_store_custom_css .='background-color: '.esc_html($vw_book_store_first_color).';';
		$vw_book_store_custom_css .='}';
	}
	if($vw_book_store_first_color != false){
		$vw_book_store_custom_css .='a, .logo h1 a, button.product-btn, #footer h3, .woocommerce-message::before, .post-navigation a:hover .post-title, .post-navigation a:focus .post-title,#footer li a:hover, .main-navigation ul.sub-menu a:hover, .page-template-custom-home-page .main-navigation .current_page_item > a, .page-template-custom-home-page .main-navigation .current-menu-item > a, .logo h1 a, .logo p.site-title a{';
			$vw_book_store_custom_css .='color: '.esc_html($vw_book_store_first_color).';';
		$vw_book_store_custom_css .='}';
	}
	if($vw_book_store_first_color != false){
		$vw_book_store_custom_css .='.woocommerce-message{';
			$vw_book_store_custom_css .='border-top-color: '.esc_html($vw_book_store_first_color).';';
		$vw_book_store_custom_css .='}';
	}
	if($vw_book_store_first_color != false){
		$vw_book_store_custom_css .='.main-navigation ul ul{';
			$vw_book_store_custom_css .='border-color: '.esc_html($vw_book_store_first_color).';';
		$vw_book_store_custom_css .='}';
	}

	/*---------------------------Second highlight color-------------------*/

	$vw_book_store_second_color = get_theme_mod('vw_book_store_second_color');

	if($vw_book_store_second_color != false){
		$vw_book_store_custom_css .='.search-bar button[type="submit"], span.cart-value, #slider .carousel-control-prev-icon, #slider .carousel-control-next-icon, .more-btn a, .scrollup i, .woocommerce span.onsale, .woocommerce #respond input#submit:hover, .woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce input.button:hover, .woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover, #sidebar .custom-social-icons i:hover, #footer .custom-social-icons i:hover, .blogbutton-small:hover, #footer .widget_price_filter .ui-slider .ui-slider-range, #footer .widget_price_filter .ui-slider .ui-slider-handle, #footer .woocommerce-product-search button, #sidebar .woocommerce-product-search button, #sidebar .widget_price_filter .ui-slider .ui-slider-range, #sidebar .widget_price_filter .ui-slider .ui-slider-handle, #footer a.custom_read_more:hover{';
			$vw_book_store_custom_css .='background-color: '.esc_html($vw_book_store_second_color).';';
		$vw_book_store_custom_css .='}';
	}
	if($vw_book_store_second_color != false){
		$vw_book_store_custom_css .='.search-box i, .top-bar .custom-social-icons i:hover, .post-main-box h3 a, .entry-content a, .main-navigation .current_page_item > a, .main-navigation .current-menu-item > a, .main-navigation a:hover, .post-main-box h2 a, h2.section-title a, #footer .textwidget a{';
			$vw_book_store_custom_css .='color: '.esc_html($vw_book_store_second_color).';';
		$vw_book_store_custom_css .='}';
	}
	if($vw_book_store_second_color != false){
		$vw_book_store_custom_css .='#sidebar h3{';
			$vw_book_store_custom_css .='color: '.esc_html($vw_book_store_second_color).'!important;';
		$vw_book_store_custom_css .='}';
	}
	if($vw_book_store_second_color != false){
		$vw_book_store_custom_css .='
		@media screen and (max-width:1000px){
			.toggle-nav i, .main-navigation ul.sub-menu a:hover{';
			$vw_book_store_custom_css .='color: '.esc_html($vw_book_store_second_color).';';
		$vw_book_store_custom_css .='} }';
	}
	if($vw_book_store_second_color != false){
		$vw_book_store_custom_css .='#header .nav ul.sub-menu li a:hover, nav.woocommerce-MyAccount-navigation ul li{';
			$vw_book_store_custom_css .='border-left-color: '.esc_html($vw_book_store_second_color).';';
		$vw_book_store_custom_css .='}';
	}
	if($vw_book_store_second_color != false){
		$vw_book_store_custom_css .='#sidebar input[type="submit"]{
		box-shadow: 5px 5px 0 0 '.esc_html($vw_book_store_second_color).';
		}';
	}

	/*---------------------------Width Layout -------------------*/

	$vw_book_store_theme_lay = get_theme_mod( 'vw_book_store_width_option','Full Width');

    if($vw_book_store_theme_lay == 'Boxed'){
		$vw_book_store_custom_css .='body{';
			$vw_book_store_custom_css .='max-width: 1140px; width: 100%; padding-right: 15px; padding-left: 15px; margin-right: auto; margin-left: auto;';
		$vw_book_store_custom_css .='}';
	}else if($vw_book_store_theme_lay == 'Wide Width'){
		$vw_book_store_custom_css .='body{';
			$vw_book_store_custom_css .='width: 100%;padding-right: 15px;padding-left: 15px;margin-right: auto;margin-left: auto;';
		$vw_book_store_custom_css .='}';
	}else if($vw_book_store_theme_lay == 'Full Width'){
		$vw_book_store_custom_css .='body{';
			$vw_book_store_custom_css .='max-width: 100%;';
		$vw_book_store_custom_css .='}';
	}

	/*--------------------------- Slider Opacity -------------------*/

	$vw_book_store_theme_lay = get_theme_mod( 'vw_book_store_slider_opacity_color','0.5');

	if($vw_book_store_theme_lay == '0'){
		$vw_book_store_custom_css .='#slider img{';
			$vw_book_store_custom_css .='opacity:0';
		$vw_book_store_custom_css .='}';
		}else if($vw_book_store_theme_lay == '0.1'){
		$vw_book_store_custom_css .='#slider img{';
			$vw_book_store_custom_css .='opacity:0.1';
		$vw_book_store_custom_css .='}';
		}else if($vw_book_store_theme_lay == '0.2'){
		$vw_book_store_custom_css .='#slider img{';
			$vw_book_store_custom_css .='opacity:0.2';
		$vw_book_store_custom_css .='}';
		}else if($vw_book_store_theme_lay == '0.3'){
		$vw_book_store_custom_css .='#slider img{';
			$vw_book_store_custom_css .='opacity:0.3';
		$vw_book_store_custom_css .='}';
		}else if($vw_book_store_theme_lay == '0.4'){
		$vw_book_store_custom_css .='#slider img{';
			$vw_book_store_custom_css .='opacity:0.4';
		$vw_book_store_custom_css .='}';
		}else if($vw_book_store_theme_lay == '0.5'){
		$vw_book_store_custom_css .='#slider img{';
			$vw_book_store_custom_css .='opacity:0.5';
		$vw_book_store_custom_css .='}';
		}else if($vw_book_store_theme_lay == '0.6'){
		$vw_book_store_custom_css .='#slider img{';
			$vw_book_store_custom_css .='opacity:0.6';
		$vw_book_store_custom_css .='}';
		}else if($vw_book_store_theme_lay == '0.7'){
		$vw_book_store_custom_css .='#slider img{';
			$vw_book_store_custom_css .='opacity:0.7';
		$vw_book_store_custom_css .='}';
		}else if($vw_book_store_theme_lay == '0.8'){
		$vw_book_store_custom_css .='#slider img{';
			$vw_book_store_custom_css .='opacity:0.8';
		$vw_book_store_custom_css .='}';
		}else if($vw_book_store_theme_lay == '0.9'){
		$vw_book_store_custom_css .='#slider img{';
			$vw_book_store_custom_css .='opacity:0.9';
		$vw_book_store_custom_css .='}';
		}

	/*---------------------------Slider Content Layout -------------------*/

	$vw_book_store_theme_lay = get_theme_mod( 'vw_book_store_slider_content_option','Center');
	
    if($vw_book_store_theme_lay == 'Left'){
		$vw_book_store_custom_css .='#slider .carousel-caption, #slider .inner_carousel, #slider .inner_carousel h1{';
			$vw_book_store_custom_css .='text-align:left; left:15%; right:45%;';
		$vw_book_store_custom_css .='}';
	}else if($vw_book_store_theme_lay == 'Center'){
		$vw_book_store_custom_css .='#slider .carousel-caption, #slider .inner_carousel, #slider .inner_carousel h1{';
			$vw_book_store_custom_css .='text-align:center; left:20%; right:20%;';
		$vw_book_store_custom_css .='}';
	}else if($vw_book_store_theme_lay == 'Right'){
		$vw_book_store_custom_css .='#slider .carousel-caption, #slider .inner_carousel, #slider .inner_carousel h1{';
			$vw_book_store_custom_css .='text-align:right; left:45%; right:15%;';
		$vw_book_store_custom_css .='}';
	}

	/*---------------------------Slider Height ------------*/

	$vw_book_store_slider_height = get_theme_mod('vw_book_store_slider_height');
	if($vw_book_store_slider_height != false){
		$vw_book_store_custom_css .='#slider img{';
			$vw_book_store_custom_css .='height: '.esc_html($vw_book_store_slider_height).';';
		$vw_book_store_custom_css .='}';
	}

	/*---------------------------Blog Layout -------------------*/

	$vw_book_store_theme_lay = get_theme_mod( 'vw_book_store_blog_layout_option','Default');
    if($vw_book_store_theme_lay == 'Default'){
		$vw_book_store_custom_css .='.post-main-box{';
			$vw_book_store_custom_css .='';
		$vw_book_store_custom_css .='}';
	}else if($vw_book_store_theme_lay == 'Center'){
		$vw_book_store_custom_css .='.post-main-box, .post-main-box h2, .post-info, .new-text p, .content-bttn, #our-services .service-text p{';
			$vw_book_store_custom_css .='text-align:center;';
		$vw_book_store_custom_css .='}';
		$vw_book_store_custom_css .='.post-info, .content-bttn{';
			$vw_book_store_custom_css .='margin-top:10px;';
		$vw_book_store_custom_css .='}';
	}else if($vw_book_store_theme_lay == 'Left'){
		$vw_book_store_custom_css .='.post-main-box, .post-main-box h2, .post-info, .new-text p, .content-bttn, #our-services p{';
			$vw_book_store_custom_css .='text-align:Left;';
		$vw_book_store_custom_css .='}';
	}

	/*------------------------------Responsive Media -----------------------*/

	$vw_book_store_res_topbar = get_theme_mod( 'vw_book_store_resp_topbar_hide_show',false);
    if($vw_book_store_res_topbar == true){
    	$vw_book_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_book_store_custom_css .='.top-bar{';
			$vw_book_store_custom_css .='display:block;';
		$vw_book_store_custom_css .='} }';
	}else if($vw_book_store_res_topbar == false){
		$vw_book_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_book_store_custom_css .='.top-bar{';
			$vw_book_store_custom_css .='display:none;';
		$vw_book_store_custom_css .='} }';
	}

	$vw_book_store_res_stickyheader = get_theme_mod( 'vw_book_store_stickyheader_hide_show',false);
    if($vw_book_store_res_stickyheader == true){
    	$vw_book_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_book_store_custom_css .='.header-fixed{';
			$vw_book_store_custom_css .='display:block;';
		$vw_book_store_custom_css .='} }';
	}else if($vw_book_store_res_stickyheader == false){
		$vw_book_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_book_store_custom_css .='.header-fixed{';
			$vw_book_store_custom_css .='display:none;';
		$vw_book_store_custom_css .='} }';
	}

	$vw_book_store_res_slider = get_theme_mod( 'vw_book_store_resp_slider_hide_show',false);
    if($vw_book_store_res_slider == true){
    	$vw_book_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_book_store_custom_css .='#slider{';
			$vw_book_store_custom_css .='display:block;';
		$vw_book_store_custom_css .='} }';
	}else if($vw_book_store_res_slider == false){
		$vw_book_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_book_store_custom_css .='#slider{';
			$vw_book_store_custom_css .='display:none;';
		$vw_book_store_custom_css .='} }';
	}

	$vw_book_store_metabox = get_theme_mod( 'vw_book_store_metabox_hide_show',true);
    if($vw_book_store_metabox == true){
    	$vw_book_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_book_store_custom_css .='.post-info{';
			$vw_book_store_custom_css .='display:block;';
		$vw_book_store_custom_css .='} }';
	}else if($vw_book_store_metabox == false){
		$vw_book_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_book_store_custom_css .='.post-info{';
			$vw_book_store_custom_css .='display:none;';
		$vw_book_store_custom_css .='} }';
	}

	$vw_book_store_sidebar = get_theme_mod( 'vw_book_store_sidebar_hide_show',true);
    if($vw_book_store_sidebar == true){
    	$vw_book_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_book_store_custom_css .='#sidebar{';
			$vw_book_store_custom_css .='display:block;';
		$vw_book_store_custom_css .='} }';
	}else if($vw_book_store_sidebar == false){
		$vw_book_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_book_store_custom_css .='#sidebar{';
			$vw_book_store_custom_css .='display:none;';
		$vw_book_store_custom_css .='} }';
	}

	$vw_book_store_resp_scroll_top = get_theme_mod( 'vw_book_store_resp_scroll_top_hide_show',true);
    if($vw_book_store_resp_scroll_top == true){
    	$vw_book_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_book_store_custom_css .='.scrollup i{';
			$vw_book_store_custom_css .='display:block;';
		$vw_book_store_custom_css .='} }';
	}else if($vw_book_store_resp_scroll_top == false){
		$vw_book_store_custom_css .='@media screen and (max-width:575px) {';
		$vw_book_store_custom_css .='.scrollup i{';
			$vw_book_store_custom_css .='display:none !important;';
		$vw_book_store_custom_css .='} }';
	}

	/*------------- Top Bar Settings ------------------*/

	$vw_book_store_topbar_padding_top_bottom = get_theme_mod('vw_book_store_topbar_padding_top_bottom');
	if($vw_book_store_topbar_padding_top_bottom != false){
		$vw_book_store_custom_css .='.top-bar{';
			$vw_book_store_custom_css .='padding-top: '.esc_html($vw_book_store_topbar_padding_top_bottom).'; padding-bottom: '.esc_html($vw_book_store_topbar_padding_top_bottom).';';
		$vw_book_store_custom_css .='}';
	}

	/*------------------ Search Settings -----------------*/
	
	$vw_book_store_search_font_size = get_theme_mod('vw_book_store_search_font_size');
	if($vw_book_store_search_font_size != false){
		$vw_book_store_custom_css .='.search-box i{';
			$vw_book_store_custom_css .='font-size: '.esc_html($vw_book_store_search_font_size).';';
		$vw_book_store_custom_css .='}';
	}

	/*---------------- Button Settings ------------------*/

	$vw_book_store_button_padding_top_bottom = get_theme_mod('vw_book_store_button_padding_top_bottom');
	$vw_book_store_button_padding_left_right = get_theme_mod('vw_book_store_button_padding_left_right');
	if($vw_book_store_button_padding_top_bottom != false || $vw_book_store_button_padding_left_right != false){
		$vw_book_store_custom_css .='.blogbutton-small{';
			$vw_book_store_custom_css .='padding-top: '.esc_html($vw_book_store_button_padding_top_bottom).'; padding-bottom: '.esc_html($vw_book_store_button_padding_top_bottom).';padding-left: '.esc_html($vw_book_store_button_padding_left_right).';padding-right: '.esc_html($vw_book_store_button_padding_left_right).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_button_border_radius = get_theme_mod('vw_book_store_button_border_radius');
	if($vw_book_store_button_border_radius != false){
		$vw_book_store_custom_css .='.blogbutton-small{';
			$vw_book_store_custom_css .='border-radius: '.esc_html($vw_book_store_button_border_radius).'px;';
		$vw_book_store_custom_css .='}';
	}

	/*-------------- Copyright Alignment ----------------*/

	$vw_book_store_copyright_alingment = get_theme_mod('vw_book_store_copyright_alingment');
	if($vw_book_store_copyright_alingment != false){
		$vw_book_store_custom_css .='.copyright p{';
			$vw_book_store_custom_css .='text-align: '.esc_html($vw_book_store_copyright_alingment).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_copyright_padding_top_bottom = get_theme_mod('vw_book_store_copyright_padding_top_bottom');
	if($vw_book_store_copyright_padding_top_bottom != false){
		$vw_book_store_custom_css .='#footer-2{';
			$vw_book_store_custom_css .='padding-top: '.esc_html($vw_book_store_copyright_padding_top_bottom).'; padding-bottom: '.esc_html($vw_book_store_copyright_padding_top_bottom).';';
		$vw_book_store_custom_css .='}';
	}

	/*----------------Sroll to top Settings ------------------*/

	$vw_book_store_scroll_to_top_font_size = get_theme_mod('vw_book_store_scroll_to_top_font_size');
	if($vw_book_store_scroll_to_top_font_size != false){
		$vw_book_store_custom_css .='.scrollup i{';
			$vw_book_store_custom_css .='font-size: '.esc_html($vw_book_store_scroll_to_top_font_size).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_scroll_to_top_padding = get_theme_mod('vw_book_store_scroll_to_top_padding');
	$vw_book_store_scroll_to_top_padding = get_theme_mod('vw_book_store_scroll_to_top_padding');
	if($vw_book_store_scroll_to_top_padding != false){
		$vw_book_store_custom_css .='.scrollup i{';
			$vw_book_store_custom_css .='padding-top: '.esc_html($vw_book_store_scroll_to_top_padding).';padding-bottom: '.esc_html($vw_book_store_scroll_to_top_padding).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_scroll_to_top_width = get_theme_mod('vw_book_store_scroll_to_top_width');
	if($vw_book_store_scroll_to_top_width != false){
		$vw_book_store_custom_css .='.scrollup i{';
			$vw_book_store_custom_css .='width: '.esc_html($vw_book_store_scroll_to_top_width).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_scroll_to_top_height = get_theme_mod('vw_book_store_scroll_to_top_height');
	if($vw_book_store_scroll_to_top_height != false){
		$vw_book_store_custom_css .='.scrollup i{';
			$vw_book_store_custom_css .='height: '.esc_html($vw_book_store_scroll_to_top_height).';';
		$vw_book_store_custom_css .='}';
	}

	$vw_book_store_scroll_to_top_border_radius = get_theme_mod('vw_book_store_scroll_to_top_border_radius');
	if($vw_book_store_scroll_to_top_border_radius != false){
		$vw_book_store_custom_css .='.scrollup i{';
			$vw_book_store_custom_css .='border-radius: '.esc_html($vw_book_store_scroll_to_top_border_radius).'px;';
		$vw_book_store_custom_css .='}';
	}
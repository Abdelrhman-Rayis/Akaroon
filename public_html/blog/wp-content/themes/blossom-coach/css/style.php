<?php
/**
 * Blossom Coach Dynamic Styles
 * 
 * @package Blossom_Coach
*/

function blossom_coach_dynamic_css(){
    
    $primary_font    = get_theme_mod( 'primary_font', 'Nunito Sans' );
    $primary_fonts   = blossom_coach_get_fonts( $primary_font, 'regular' );
    $secondary_font  = get_theme_mod( 'secondary_font', 'Nunito' );
    $secondary_fonts = blossom_coach_get_fonts( $secondary_font, 'regular' );
    
    $site_title_font      = get_theme_mod( 'site_title_font', array( 'font-family'=>'Nunito', 'variant'=>'700' ) );
    $site_title_fonts     = blossom_coach_get_fonts( $site_title_font['font-family'], $site_title_font['variant'] );
    $site_title_font_size = get_theme_mod( 'site_title_font_size', 45 );
    
    $custom_css = '';
    $custom_css .= '
    
    /*Typography*/
    body,
    button,
    input,
    select,
    optgroup,
    textarea, section[class*="-section"] .widget_blossom_client_logo_widget .widget-title, .blog-section article .entry-meta, 
    .btn-link, .widget.widget_blossomthemes_stat_counter_widget .widget-title, .single .entry-meta, 
    .portfolio-text-holder .portfolio-img-title {
        font-family : ' . esc_html( $primary_fonts['font'] ) . ';
    }
    
    .site-title, 
    .site-title-wrap .site-title{
        font-size   : ' .  absint( $site_title_font_size ) . 'px;
        font-family : ' .  esc_html( $site_title_fonts['font'] ) . ';
        font-weight : ' .  esc_html( $site_title_fonts['weight'] ) . ';
        font-style  : ' .  esc_html( $site_title_fonts['style'] ) . ';
    }
    
    /*Typography*/
    h1, h2, h3, h4, h5, h6, 
    section[class*="-section"] .widget .widget-title,
    section[class*="-section"] .widget_blossomtheme_featured_page_widget .section-subtitle,
    .section-title, .comment-body b.fn, .comment-body .reply .comment-reply-link, .single .navigation .nav-links, 
    .site-header .header-search label.screen-reader-text, .btn-readmore, .btn-readmore:visited, .bttk-testimonial-holder .name, 
    .pricing-block .price, .entry-meta, #primary .widget_blossomtheme_featured_page_widget .section-subtitle, 
    .widget_blossomthemes_stat_counter_widget .hs-counter, .widget_bttk_description_widget .bttk-team-holder .name, 
    .bttk-team-inner-holder-modal .name, .page-header .subtitle, .dropcap, .error-404 .error-num, .error-404 a.bttn, 
    .related-portfolio-title {
		font-family: ' .  esc_html( $secondary_fonts['font'] ) . ';
	}';
    
    if( blossom_coach_is_woocommerce_activated() ) {
    	$custom_css .= '.woocommerce div.product .product_title,
    	.woocommerce div.product .woocommerce-tabs .panel h2{
			font-family: ' .  esc_html( $secondary_fonts['font'] ) . ';
    	}';    
    }

    wp_add_inline_style( 'blossom-coach', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'blossom_coach_dynamic_css', 99 );
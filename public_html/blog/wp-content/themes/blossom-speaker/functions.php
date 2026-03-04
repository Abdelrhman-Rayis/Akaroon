<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 *	After theme Setup Hook
 */
function blossom_speaker_coach_theme_setup() {
	/**
	* Make child theme available for translation.
    * Translations can be filed in the /languages/ directory.
	*/
	load_child_theme_textdomain( 'blossom-speaker', get_stylesheet_directory() . '/languages' );

}
add_action( 'after_setup_theme', 'blossom_speaker_coach_theme_setup' );

/**
 * Enqueue scripts and styles.
 */
function blossom_speaker_coach_scripts() {
	if( blossom_coach_is_woocommerce_activated() ){
        $dependencies = array( 'blossom-coach-woocommerce', 'owl-carousel', 'animate', 'blossom-coach-google-fonts' );    
    }else{
        $dependencies = array( 'owl-carousel', 'animate', 'blossom-coach-google-fonts' );
    }
        
    wp_enqueue_style( 'blossom-speaker-parent-style', get_template_directory_uri() . '/style.css', $dependencies );
}
add_action( 'wp_enqueue_scripts', 'blossom_speaker_coach_scripts' );

//Remove a function from the parent theme
function blossom_speaker_remove_parent_filters(){ //Have to do it after theme setup, because child theme functions are loaded first
    remove_action( 'wp_enqueue_scripts', 'blossom_coach_dynamic_css', 99 );
    remove_action( 'customize_register', 'blossom_coach_customizer_theme_info' );
}
add_action( 'init', 'blossom_speaker_remove_parent_filters' );

function blossom_speaker_customize_register( $wp_customize ){

    /* THEME INFO */
    $wp_customize->add_section( 'theme_info', array(
        'title'       => __( 'Demo & Documentation' , 'blossom-speaker' ),
        'priority'    => 6,
    ) );
    
    /** Important Links */
    $wp_customize->add_setting( 'theme_info_theme',
        array(
            'default' => '',
            'sanitize_callback' => 'wp_kses_post',
        )
    );
    
    $theme_info = '<p>';
    $theme_info .= sprintf( __( 'Demo Link: %1$sClick here.%2$s', 'blossom-speaker' ),  '<a href="' . esc_url( 'https://demo.blossomthemes.com/blossom-speaker' ) . '" target="_blank">', '</a>' );
    $theme_info .= '</p><p>';
    $theme_info .= sprintf( __( 'Documentation Link: %1$sClick here.%2$s', 'blossom-speaker' ),  '<a href="' . esc_url( 'https://docs.blossomthemes.com/docs/blossom-speaker/' ) . '" target="_blank">', '</a>' );
    $theme_info .= '</p>';

    $wp_customize->add_control( new Blossom_Coach_Note_Control( $wp_customize,
        'theme_info_theme', 
            array(
                'section'     => 'theme_info',
                'description' => $theme_info
            )
        )
    );

    /** Site Title Font */
    $wp_customize->add_setting( 
        'site_title_font', 
        array(
            'default' => array(                                         
                'font-family' => 'Montserrat',
                'variant'     => '700',
            ),
            'sanitize_callback' => array( 'Blossom_Coach_Fonts', 'sanitize_typography' )
        ) 
    );

    $wp_customize->add_control( 
        new Blossom_Coach_Typography_Control( 
            $wp_customize, 
            'site_title_font', 
            array(
                'label'       => __( 'Site Title Font', 'blossom-speaker' ),
                'description' => __( 'Site title and tagline font.', 'blossom-speaker' ),
                'section'     => 'title_tagline',
                'priority'    => 60, 
            ) 
        ) 
    );
    
    /** Site Title Font Size*/
    $wp_customize->add_setting( 
        'site_title_font_size', 
        array(
            'default'           => 40,
            'sanitize_callback' => 'blossom_coach_sanitize_number_absint'
        ) 
    );
    
    $wp_customize->add_control(
        new Blossom_Coach_Slider_Control( 
            $wp_customize,
            'site_title_font_size',
            array(
                'section'     => 'title_tagline',
                'label'       => __( 'Site Title Font Size', 'blossom-speaker' ),
                'description' => __( 'Change the font size of your site title.', 'blossom-speaker' ),
                'priority'    => 65,
                'choices'     => array(
                    'min'   => 10,
                    'max'   => 200,
                    'step'  => 1,
                )                 
            )
        )
    );

    /** Typography */
    $wp_customize->add_section(
        'typography_settings',
        array(
            'title'    => __( 'Typography', 'blossom-speaker' ),
            'priority' => 20,
            'panel'    => 'appearance_settings',
        )
    );
    
    /** Primary Font */
    $wp_customize->add_setting(
        'primary_font',
        array(
            'default'           => 'Open Sans',
            'sanitize_callback' => 'blossom_coach_sanitize_select'
        )
    );

    $wp_customize->add_control(
        new Blossom_Coach_Select_Control(
            $wp_customize,
            'primary_font',
            array(
                'label'       => __( 'Primary Font', 'blossom-speaker' ),
                'description' => __( 'Primary font of the site.', 'blossom-speaker' ),
                'section'     => 'typography_settings',
                'choices'     => blossom_coach_get_all_fonts(), 
            )
        )
    );
    
    /** Secondary Font */
    $wp_customize->add_setting(
        'secondary_font',
        array(
            'default'           => 'Montserrat',
            'sanitize_callback' => 'blossom_coach_sanitize_select'
        )
    );

    $wp_customize->add_control(
        new Blossom_Coach_Select_Control(
            $wp_customize,
            'secondary_font',
            array(
                'label'       => __( 'Secondary Font', 'blossom-speaker' ),
                'description' => __( 'Secondary font of the site.', 'blossom-speaker' ),
                'section'     => 'typography_settings',
                'choices'     => blossom_coach_get_all_fonts(), 
            )
        )
    );

}
add_action( 'customize_register', 'blossom_speaker_customize_register', 40 );

function blossom_speaker_remove_header_customize_register() {     
    global $wp_customize;
    $wp_customize->remove_setting( 'phone' );
    $wp_customize->remove_control( 'phone' );

    $wp_customize->remove_setting( 'email' );     
    $wp_customize->remove_control( 'email' );   
} 
add_action( 'customize_register', 'blossom_speaker_remove_header_customize_register', 1000 );

function blossom_speaker_site_branding() {
    $site_title       = get_bloginfo( 'name' );
    $site_description = get_bloginfo( 'description', 'display' );
    $header_text      = get_theme_mod( 'header_text', 1 );
    $branding_class = '';
    if( has_custom_logo() || $site_title || $site_description || $header_text ) : 
        if( has_custom_logo() && ( $site_title || $site_description ) && $header_text ) {
            $branding_class = ' icon-text';
        }else{
            $branding_class = '';
        } ?>
        <div class="site-branding<?php echo esc_attr( $branding_class ); ?>" itemscope itemtype="http://schema.org/Organization">
            <?php 
            if( has_custom_logo() ){
                echo '<div class="site-logo">';
                the_custom_logo();
                echo '</div><!-- .site-logo -->';
            } 
            ?>
            <?php if( $site_title || $site_description ) :
                echo '<div class="site-title-wrap">';
                if ( is_front_page() ) : ?>
                    <h1 class="site-title" itemprop="name"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" itemprop="url"><?php bloginfo( 'name' ); ?></a></h1>
                <?php else : ?>
                    <p class="site-title" itemprop="name"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" itemprop="url"><?php bloginfo( 'name' ); ?></a></p>
                <?php endif; 
                $description = get_bloginfo( 'description', 'display' );
                if ( $description || is_customize_preview() ){ ?>
                    <p class="site-description"><?php echo $description; ?></p>
                <?php
                }
                echo '</div>';
            endif; ?>
        </div><!-- .site-branding -->
    <?php endif;
}

function blossom_coach_header(){     
    $ed_cart   = get_theme_mod( 'ed_shopping_cart', true ); 
    $ed_search = get_theme_mod( 'ed_header_search', false );
    ?>
    <header id="masthead" class="site-header" itemscope itemtype="http://schema.org/WPHeader"> 
        <div class="header-t">
            <div class="wrapper">
                <div class="header-social">
                    <?php 
                        if( blossom_coach_social_links( false ) ){
                            blossom_coach_social_links( true ); 
                        } 
                    ?>
                </div>                
            <?php                
                
                blossom_speaker_site_branding(); 
                
                echo '<div class="top-right">';
                if( $ed_search ) { ?> 
                    <div class="header-search">
                        <button aria-label="header search toggle"><i class="fa fa-search"></i></button>
                        <div class="header-search-form">
                            <button aria-label="search form close" class="close"></button>
                            <?php get_search_form(); ?>
                        </div><!-- .header-search-form -->
                    </div><!-- .header-search -->
                <?php } 
                if( blossom_coach_is_woocommerce_activated() && $ed_cart ) blossom_coach_wc_cart_count();
                echo '</div>';
            ?>
            </div><!-- .wrapper -->                          
        </div><!-- .header-t -->

        <div class="main-header">
            <div class="wrapper">
                <div class="menu-wrap">
                    <nav id="site-navigation" class="main-navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
                        <button type="button" class="toggle-button">
                            <span class="toggle-bar"></span>
                            <span class="toggle-bar"></span>
                            <span class="toggle-bar"></span>
                        </button>
                        <?php
                            wp_nav_menu( array(
                                'theme_location' => 'primary',
                                'menu_id'        => 'primary-menu',
                                'fallback_cb'    => 'blossom_coach_primary_menu_fallback',
                            ) );
                        ?>
                    </nav><!-- #site-navigation --> 
                </div>
            </div>
        </div>  
    </header><!-- .site-header -->
    <?php 
}

// Add Customize Script
function blossom_speaker_customize_script(){
    wp_enqueue_script( 'blossom-speaker-customize', get_stylesheet_directory_uri() . '/js/customize.js', array( 'jquery', 'customize-controls', 'blossom-coach-customize' ), '', true );
}
add_action( 'customize_controls_enqueue_scripts', 'blossom_speaker_customize_script', 20 );

function blossom_coach_get_home_sections(){
    $ed_banner = get_theme_mod( 'ed_banner_section', 'slider_banner' );
    $sections = array( 
        'service'     => array( 'sidebar' => 'service' ),       
        'about'       => array( 'sidebar' => 'about' ),
        'cta'         => array( 'sidebar' => 'cta' ),        
        'testimonial' => array( 'sidebar' => 'testimonial' ),
        'client'      => array( 'sidebar' => 'client' ),
        'blog'        => array( 'section' => 'blog' ),
        'simple-cta'  => array( 'sidebar' => 'simple-cta' ),
        'contact'     => array( 'sidebar' => 'contact' ), 
    );
    
    $enabled_section = array();
    
    if( $ed_banner == 'static_nl_banner' || $ed_banner == 'slider_banner' ) array_push( $enabled_section, 'banner' );
    
    foreach( $sections as $k => $v ){
        if( array_key_exists( 'sidebar', $v ) ){
            if( is_active_sidebar( $v['sidebar'] ) ) array_push( $enabled_section, $v['sidebar'] );
        }else{
            if( get_theme_mod( 'ed_' . $v['section'] . '_section', true ) ) array_push( $enabled_section, $v['section'] );
        }
    }  
    
    return apply_filters( 'blossom_coach_home_sections', $enabled_section );
}

/** Blossom Coach Fonts URL */
function blossom_coach_fonts_url(){
    $fonts_url = '';
    
    $primary_font       = get_theme_mod( 'primary_font', 'Open Sans' );
    $ig_primary_font    = blossom_coach_is_google_font( $primary_font );    
    $secondary_font     = get_theme_mod( 'secondary_font', 'Montserrat' );
    $ig_secondary_font  = blossom_coach_is_google_font( $secondary_font );    
    $site_title_font    = get_theme_mod( 'site_title_font', array( 'font-family'=>'Montserrat', 'variant'=>'700' ) );
    $ig_site_title_font = blossom_coach_is_google_font( $site_title_font['font-family'] );
        
    /* Translators: If there are characters in your language that are not
    * supported by respective fonts, translate this to 'off'. Do not translate
    * into your own language.
    */
    $primary    = _x( 'on', 'Primary Font: on or off', 'blossom-speaker' );
    $secondary  = _x( 'on', 'Secondary Font: on or off', 'blossom-speaker' );
    $site_title = _x( 'on', 'Site Title Font: on or off', 'blossom-speaker' );
    
    if ( 'off' !== $primary || 'off' !== $secondary || 'off' !== $site_title ) {
        
        $font_families = array();
     
        if ( 'off' !== $primary && $ig_primary_font ) {
            $primary_variant = blossom_coach_check_varient( $primary_font, 'regular', true );
            if( $primary_variant ){
                $primary_var = ':' . $primary_variant;
            }else{
                $primary_var = '';    
            }            
            $font_families[] = $primary_font . $primary_var;
        }
         
        if ( 'off' !== $secondary && $ig_secondary_font ) {
            $secondary_variant = blossom_coach_check_varient( $secondary_font, 'regular', true );
            if( $secondary_variant ){
                $secondary_var = ':' . $secondary_variant;    
            }else{
                $secondary_var = '';
            }
            $font_families[] = $secondary_font . $secondary_var;
        }
        
        if ( 'off' !== $site_title && $ig_site_title_font ) {
            
            if( ! empty( $site_title_font['variant'] ) ){
                $site_title_var = ':' . blossom_coach_check_varient( $site_title_font['font-family'], $site_title_font['variant'] );    
            }else{
                $site_title_var = '';
            }
            $font_families[] = $site_title_font['font-family'] . $site_title_var;
        }
        
        $font_families = array_diff( array_unique( $font_families ), array('') );
        
        $query_args = array(
            'family' => urlencode( implode( '|', $font_families ) ),            
        );
        
        $fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
    }
     
    return esc_url_raw( $fonts_url );
}

/** Dynamic CSS */
function blossom_speaker_dynamic_css(){
    
    $primary_font    = get_theme_mod( 'primary_font', 'Open Sans' );
    $primary_fonts   = blossom_coach_get_fonts( $primary_font, 'regular' );
    $secondary_font  = get_theme_mod( 'secondary_font', 'Montserrat' );
    $secondary_fonts = blossom_coach_get_fonts( $secondary_font, 'regular' );    
    $site_title_font      = get_theme_mod( 'site_title_font', array( 'font-family'=>'Montserrat', 'variant'=>'700' ) );
    $site_title_fonts     = blossom_coach_get_fonts( $site_title_font['font-family'], $site_title_font['variant'] );
    $site_title_font_size = get_theme_mod( 'site_title_font_size', 40 );
    
    $custom_css = '';
    $custom_css .= '
    
    /*Typography*/
    body,
    button,
    input,
    select,
    optgroup,
    textarea, section[class*="-section"] .widget_blossom_client_logo_widget .widget-title, .widget.widget_blossomthemes_stat_counter_widget .widget-title, .single .entry-meta, 
    .portfolio-text-holder .portfolio-img-title, 
    .widget_blossomtheme_featured_page_widget .featured_page_content h1, .widget_blossomtheme_featured_page_widget .featured_page_content h3, .widget_blossomtheme_featured_page_widget .featured_page_content h4, .widget_blossomtheme_featured_page_widget .featured_page_content h5, .widget_blossomtheme_featured_page_widget .featured_page_content h6 {
        font-family : ' . wp_kses_post( $primary_fonts['font'] ) . ';
    }
    
    .site-title, 
    .site-title-wrap .site-title{
        font-size   : ' . absint( $site_title_font_size ) . 'px;
        font-family : ' . wp_kses_post( $site_title_fonts['font'] ) . ';
        font-weight : ' . esc_html( $site_title_fonts['weight'] ) . ';
        font-style  : ' . esc_html( $site_title_fonts['style'] ) . ';
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
    .related-portfolio-title,
    .woocommerce div.product form.cart .single_add_to_cart_button, .woocommerce div.product .cart .single_add_to_cart_button.alt,
    .button-wrap a,
    .banner-caption .blossomthemes-email-newsletter-wrapper form input[type="submit"], 
    .main-navigation ul li, .blog-section article .entry-meta, .btn-link, 
    button, input[type="button"], input[type="reset"], input[type="submit"] {
        font-family: ' . wp_kses_post( $secondary_fonts['font'] ) . ';
    }';
    if( blossom_coach_is_woocommerce_activated() ) {
        $custom_css .= ' .woocommerce div.product .product_title,
        .woocommerce div.product .woocommerce-tabs .panel h2{
            font-family: ' . wp_kses_post( $secondary_fonts['font'] ) . ';
        }';   
    }

    wp_add_inline_style( 'blossom-coach', $custom_css );
           
}
add_action( 'wp_enqueue_scripts', 'blossom_speaker_dynamic_css', 100 );

function blossom_coach_footer_bottom(){ ?>
    <div class="bottom-footer">
        <div class="wrapper">
            <div class="copyright">            
            <?php
                blossom_coach_get_footer_copyright();
                esc_html_e( ' Blossom Speaker | Developed By ', 'blossom-speaker' );                
                echo '<a href="' . esc_url( 'https://blossomthemes.com/' ) .'" rel="nofollow" target="_blank">' . esc_html__( ' Blossom Themes', 'blossom-speaker' ) . '</a>.';                
                printf( esc_html__( ' Powered by %s', 'blossom-speaker' ), '<a href="'. esc_url( __( 'https://wordpress.org/', 'blossom-speaker' ) ) .'" target="_blank">WordPress</a>.' );
                if ( function_exists( 'the_privacy_policy_link' ) ) {
                    the_privacy_policy_link();
                }
            ?>               
            </div>
        </div><!-- .wrapper -->
    </div><!-- .bottom-footer -->
    <?php
}
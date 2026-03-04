<?php
class CPTA_Scripts {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_style' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_front_style' ) );
	}
	
	public function load_admin_style(){
		wp_register_style( 'style_cpta_admin', plugins_url( CPTA_PLUGIN_DIR . '/css/style_admin.css' ) );
		wp_enqueue_style( 'style_cpta_admin' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'ap.cookie', plugins_url( CPTA_PLUGIN_DIR . '/js/ap.cookie.js' ) );
		wp_enqueue_script( 'ap-tabs', plugins_url( CPTA_PLUGIN_DIR . '/js/ap-tabs.js' ) );
		wp_enqueue_script( 'cpt', plugins_url( CPTA_PLUGIN_DIR . '/js/cpt.js' ) );
	}
	
	public function load_front_style(){
		wp_register_style( 'style_cpta_front', plugins_url( CPTA_PLUGIN_DIR . '/css/style_front.css' ) );
		wp_enqueue_style( 'style_cpta_front' );
	}
	
}
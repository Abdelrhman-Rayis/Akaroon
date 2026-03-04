<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Bookshelves_Shortcode {

	protected static $instance = null;

	public function __construct() {
		add_action( 'init', array( &$this, 'init' ) );
		add_action( 'admin_init', array( &$this, 'admin_init' ) );
	}

	public function init() {
		add_shortcode( 'bookshelf', array( $this, 'bookshelf_shortcode' ) );
	}

	public function admin_init() {}

	//==================================================
	// Render Bookshelf from shortcode
	//==================================================
	public function bookshelf_shortcode( $atts, $content = null ) {
		$a = shortcode_atts(
			array(
				'id'    => '',
				'title' => '',
			),
			$atts
		);
		$id = $a['id'];

		if ( false === get_post_status( $id ) ) {
			echo 'This Bookshelf does not exist';
		} else {
			$html = lbs_shelveBooks( $id );
			return $html;
		}
	}

	public static function instance() {
		null === self::$instance && self::$instance = new self();
		return self::$instance;
	}
}

$bookshelves_shortcode = Bookshelves_Shortcode::instance();

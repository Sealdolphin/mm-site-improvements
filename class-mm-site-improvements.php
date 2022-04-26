<?php
/**
 * Minor improvements
 *
 * @package modules
 */

/**
 * Plugin Name: Custom Site Improvements
 * Plugin URI: https://github.com/Sealdolphin/mm-site-improvements
 * Description: HEGY-hez készített külön bővítmény. A WordPress oldalon egyéni tartalmakat lehet hozzátenni marketing szempontból.
 * Author: Mihalovits Márk
 * Author URI: https://github.com/Sealdolphin
 * Version: 1.2
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

require_once dirname( __FILE__ ) . '/class-custom-footer.php';
require_once dirname( __FILE__ ) . '/class-margin.php';

/**
 * A plugin osztálya.
 * A plugin funkciói:
 *  - Testreszabható lábléc beállítása
 */
class MM_Site_Improvements {

	/**
	 * A Plugin osztály példánya
	 *
	 * @var object $instance the instance of this plugin
	 */
	private static $instance;
	/**
	 * Get plugin version
	 */
	public static function get_version() {
		return get_plugin_data( __FILE__ )['Version'];
	}
	/**
	 * Plugin prefix
	 *
	 * @var string $prefix the prefix of this plugin
	 */
	public static $prefix = 'mm_site_impr_';
	/**
	 * Beállítások slug-ja
	 *
	 * @var string $settings_page the slug of the settings page
	 */
	public static $settings_page = 'custom_site_opts';
	/**
	 * Beállítások csoportja
	 *
	 * @var string $option_group the id of the option group for this plugin
	 */
	public static $option_group = 'mm_site_impr_custom_site_improv';
	/**
	 * Példány getter
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
	/**
	 * Konstruktor
	 */
	public function __construct() {
		$this->footer = new Custom_Footer();
		// Defines footer.
		add_action( 'wp_footer', array( $this, 'apply_custom_footer' ) );
		// Admin menu for settings.
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'create_settings' ) );
	}
	/**
	 * A plugin funkcióinak érvényesítése
	 */
	public function apply_custom_footer() {
		if ( null !== $this->footer ) {
			$this->footer->apply_customisation();
		}
	}

	/**
	 * Adminisztrációs menü létrehozása
	 */
	public function admin_menu() {
		add_theme_page(
			'Custom Improvements Options',
			'Custom Items Improvements Settings',
			'edit_themes',
			self::$settings_page,
			array( $this, 'create_options_page' )
		);
	}
	/**
	 * Beállítások létrehozása
	 */
	public function create_settings() {
		register_setting(
			self::$option_group,
			Custom_Footer::$option_name,
			array(
				'type'              => 'array',
				'default'           => Custom_Footer::default_settings(),
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
			)
		);

		$this->footer->create_setting();

	}

	/**
	 * Beállítások szanitációja ( tisztítása )
	 *
	 * @param object $opts the options.
	 */
	public function sanitize_settings( $opts ) {
		return $this->footer->sanitize_footer_settings( $opts );
	}

	/**
	 * A beállítások menü oldal kirajzolása
	 */
	public function create_options_page() {

		$page_title = __( 'Customize Site Improvements' );
		$header     = <<<EOD
		<div class=wrap>
			<h1>$page_title</h1>
			<form method='post' action='options.php'>
		EOD;

		echo esc_html( $header );

		settings_fields( self::$option_group );
		do_settings_sections( self::$settings_page );

		submit_button();

		print '</form></div>';

	}
}

/**
 * Plugin elindítása
 */
MM_Site_Improvements::get_instance();

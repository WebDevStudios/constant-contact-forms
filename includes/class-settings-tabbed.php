<?php
/**
 * Constant Contact Tabbed Settings class.
 *
 * @package ConstantContact
 * @subpackage Settings
 * @author Constant Contact
 * @since 1.6.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Tabbed admin settings.
 *
 * @since 1.6.0
 */
class ConstantContact_Settings_Tabbed {

	/**
	 * The main options key, also used for page slug.
	 *
	 * @since 1.6.0
	 *
	 * @var string
	 */
	private static $options_key = 'ctct_options';

	/**
	 * Parent plugin class.
	 *
	 * @since 1.6.0
	 *
	 * @var object
	 */
	private $plugin = null;

	/**
	 * Array of plugin settings.
	 *
	 * @since 1.6.0
	 *
	 * @var array
	 */
	private $plugin_settings = [];

	/**
	 * Constructor.
	 *
	 * @since 1.6.0
	 *
	 * @param object $plugin Parent plugin instance.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->register_hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.6.0
	 */
	public function register_hooks() {
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_menu', [ $this, 'register_settings_page' ] );
	}

	/**
	 * Register plugin settings with WordPress.
	 *
	 * @since 1.6.0
	 */
	public function register_settings() {
		// Load the registered settings.
		$ctct_api_is_connected    = $this->plugin->api->is_connected();
		$ctct_api_lists           = $this->plugin->builder->get_lists();
		$ctct_api_disclosure_info = $this->plugin->api->get_disclosure_info( true );

		$this->plugin_settings = include 'plugin-settings.php';

		// Ensure the ctct_options option exists even on fresh installs.
		if ( false === get_option( self::get_options_key(), false ) ) {
			add_option( self::get_options_key() );
		}

		// Make the Settings API aware of ctct_options and how to save/update it.
		register_setting( self::get_options_key(), self::get_options_key(), [ $this, 'sanitize_settings' ] );

		// Registering each tab as a settings section, and then each of its fields.
		foreach ( $this->plugin_settings as $tab => $tab_settings ) {
			$section_id = sprintf( 'ctct_options_%1$s', $tab );

			add_settings_section( $section_id, __return_null(), '__return_false', $section_id );

			foreach ( $tab_settings as $option_key => $option ) {
				add_settings_field(
					self::get_option_key( $option_key ),
					$option['name'],
					[ $this, 'render_settings_field' ],
					$section_id,
					$section_id,
					$option
				);
			}
		}
	}

	public function sanitize_settings( $input ) {
		$all_options = (array) get_option( self::get_options_key(), [] );
		$input       = empty( $input ) ? [] : $input;
		$merged      = array_merge( $all_options, $input );

		return array_filter( $merged );
	}

	/**
	 * Register admin page and submenu item in WordPress.
	 *
	 * @since 1.6.0
	 */
	public function register_settings_page() {
		add_submenu_page(
			'edit.php?post_type=ctct_forms',
			esc_html__( 'Constant Contact Forms Settings', 'constant-contact-forms' ),
			esc_html__( 'Settings Tabbed', 'constant-contact-forms' ),
			'manage_options',
			self::$options_key,
			[ $this, 'render_settings_page' ]
		);
	}

	/**
	 * Render the admin settings page.
	 *
	 * @since 1.6.0
	 */
	public function render_settings_page() {
		$active_tab        = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING ) ?: 'general';
		$settings_page_url = admin_url( 'edit.php?post_type=ctct_forms&page=ctct_options' );

		$tab_urls = [
			'general' => add_query_arg( [ 'tab' => 'general' ], $settings_page_url ),
			'form'    => add_query_arg( [ 'tab' => 'form' ], $settings_page_url ),
			'support' => add_query_arg( [ 'tab' => 'support' ], $settings_page_url ),
		];

		$tab_classes = [
			'general' => 'general' === $active_tab ? 'nav-tab-active nav-tab' : 'nav-tab',
			'form'    => 'form' === $active_tab ? 'nav-tab-active nav-tab' : 'nav-tab',
			'support' => 'support' === $active_tab ? 'nav-tab-active nav-tab' : 'nav-tab',
		];

		include $this->plugin->dir( 'templates/admin/settings-page.php' );
	}

	/**
	 * Render an admin settings field.
	 *
	 * @since 1.6.0
	 *
	 * @param array $args {.
	 *     @type string $field_type The type of field to render.
	 *     @type string $option_key The option key for the option whose field is being rendered'.
	 *     @type string $option_args Array of option args, like title, desc, before_row, etc.
	 * }
	 * @return void
	 */
	public function render_settings_field( $args ) {
		// We must have a type to render the correct field!
		if ( ! isset( $args['type'] ) ) {
			return;
		}

		$option_key = self::get_option_key( $args['id'] );

		include $this->plugin->dir( "templates/admin/settings-fields/{$args['type']}-field.php" );
	}

	public static function get_options_key() {
		return self::$options_key;
	}

	public static function get_option_key( $option_key ) {
		return sprintf( '%1$s[%2$s]', self::$options_key, $option_key );
	}
}

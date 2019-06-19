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
	public static $options_key = 'ctct_options';

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
		$this->plugin          = $plugin;
		$this->plugin_settings = include 'plugin-settings.php';

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
		register_setting( self::$options_key, self::$options_key );

		$this->register_general_settings();
		$this->register_form_settings();
		$this->register_support_settings();
	}

	/**
	 * Register the settings that will make up the "General" settings tab.
	 *
	 * @since 1.6.0
	 */
	public function register_general_settings() {
		add_settings_section( 'ctct_options_general', esc_html__( 'General Options', 'constant-contact-forms' ), '', self::$options_key );

		foreach ( $this->plugin_settings['general'] as $option_key => $option ) {
			add_settings_field(
				$option_key,
				$option['name'],
				[ $this, 'render_settings_field' ],
				self::$options_key,
				'ctct_options_general',
				[
					'option_key' => $option_key,
					'field_type' => 'checkbox',
					'desc'       => isset( $option['desc'] ) ? $option['desc'] : [],
				]
			);
		}
	}

	/**
	 * Register the settings that will make up the "Form" settings tab.
	 *
	 * @since 1.6.0
	 */
	public function register_form_settings() {
		add_settings_section( 'ctct_options_form', esc_html__( 'Form Options', 'constant-contact-forms' ), '', self::$options_key );

		foreach ( $this->plugin_settings['form'] as $option_key => $option ) {
			add_settings_field(
				$option_key,
				$option['name'],
				[ $this, 'render_settings_field' ],
				self::$options_key,
				'ctct_options_form',
				[
					'option_key' => $option_key,
					'field_type' => 'checkbox',
					'desc'       => isset( $option['desc'] ) ? $option['desc'] : [],
				]
			);
		}
	}

	/**
	 * Register the settings that will make up the "Support" settings tab.
	 *
	 * @since 1.6.0
	 */
	public function register_support_settings() {
		add_settings_section( 'ctct_options_support', esc_html__( 'Support', 'constant-contact-forms' ), '', self::$options_key );

		foreach ( $this->plugin_settings['support'] as $option_key => $option ) {
			add_settings_field(
				$option_key,
				$option['name'],
				[ $this, 'render_settings_field' ],
				self::$options_key,
				'ctct_options_support',
				[
					'option_key' => $option_key,
					'field_type' => 'checkbox',
					'desc'       => isset( $option['desc'] ) ? $option['desc'] : [],
				]
			);
		}
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
		include $this->plugin->dir( 'templates/admin/settings-page.php' );
	}

	/**
	 * Render an admin settings field.
	 *
	 * @since 1.6.0
	 *
	 * @param array $args {.
	 *     @type string $option_key The option key for the option whose field is being rendered'.
	 *     @type string $field_type The type of field to render.
	 *     @type string $desc An optional description for the field element.
	 * }
	 * @return void
	 */
	public function render_settings_field( $args ) {
		// We must have a type to render the correct field!
		if ( ! isset( $args['type'] ) ) {
			return;
		}

		include $this->plugin->dir( "templates/admin/settings-fields/{$args['type']}.php" );
	}
}

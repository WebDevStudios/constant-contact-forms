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
	 * @var string
	 */
	public static $options_key = 'ctct_options';

	/**
	 * Parent plugin class.
	 *
	 * @since 1.6.0
	 * @var object
	 */
	protected $plugin;

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
		register_setting( self::$options_key, self::$options_key );

		add_settings_section(
			'ctct_options_general',         // ID used to identify this section and with which to register options
			'General Options',                  // Title to be displayed on the administration page
			'', // Callback used to render the description of the section
			self::$options_key    // Page on which to add this section of options
		);
		// [
		// 	'name' => esc_html__( 'Google Analytics&trade; tracking opt-in.', 'constant-contact-forms' ),
		// 	'id'   => '_ctct_data_tracking',
		// 	'type' => 'checkbox',
		// ],
		// Next, we'll introduce the fields for toggling the visibility of content elements.
		add_settings_field(
			'_ctct_data_tracking',                      // ID used to identify the field throughout the theme
			'Google Analytics&trade; tracking opt-in',                           // The label to the left of the option interface element
			[ $this, 'checkbox_bool' ],   // The name of the function responsible for rendering the option interface
			self::$options_key,    // The page on which this option will be displayed
			'ctct_options_general',         // The name of the section to which this field belongs
			array(                              // The array of arguments to pass to the callback. In this case, just a description.
				__( 'Allow Constant Contact to use Google Analytics&trade; to track your usage across the Constant Contact Forms plugin.<br/> NOTE &mdash; Your website and users will not be tracked. See our <a href="https://www.endurance.com/privacy"> Privacy Statement</a> information about what is and is not tracked.', 'constant-contact-forms' ),
			)
		);
	}

	public function checkbox_bool() {
		echo '<input type="checkbox"> poop';
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
		include $this->plugin->dir( 'templates/admin/settings.php' );
	}
}

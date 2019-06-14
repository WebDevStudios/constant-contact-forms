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
	 * Option key, and option page slug.
	 *
	 * @since 1.6.0
	 * @var string
	 */
	private $key = 'ctct_options_settings';

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
		add_action( 'admin_menu', [ $this, 'register_settings_page' ] );
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
			$this->key . 'TABBED_TODO_REPLACE',
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

<?php
/**
 * Constant Contact Main Plugin File
 *
 * @package    Constant Contact Forms
 * @subpackage Loader
 * @author     Constant Contact
 * @since      1.0.1
 * @license    GPLv3
 *
 * @wordpress-plugin
 * Plugin Name: Constant Contact Forms for WordPress
 * Plugin URI:  https://www.constantcontact.com
 * Description: Be a better marketer. All it takes is Constant Contact email marketing.
 * Version:     2.11.3
 * Author:      Constant Contact
 * Author URI:  https://www.constantcontact.com/index?pn=miwordpress
 * Requires PHP: 7.4
 * License:     GPLv3
 * Text Domain: constant-contact-forms
 * Domain Path: /languages
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 *
 * Copyright (c) 2016 Constant Contact (email : legal@constantcontact.com)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Autoloads files with classes when needed.
 *
 * @since 1.0.0
 *
 * @param string $class_name Name of the class being requested.
 * @return null
 */
function constant_contact_autoload_classes( $class_name ) {
	if ( ! str_starts_with( $class_name, 'ConstantContact_' ) ) {
		return;
	}

	$filename = strtolower(
		str_replace(
			'_',
			'-',
			substr( $class_name, strlen( 'ConstantContact_' ) )
		)
	);

	Constant_Contact::include_file( $filename );
}
spl_autoload_register( 'constant_contact_autoload_classes' );

/**
 * Main initiation class.
 *
 * @since 1.0.0
 */
class Constant_Contact {

	/**
	 * Current version.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const VERSION = '2.11.3';

	/**
	 * URL of plugin directory.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected string $url = '';

	/**
	 * Path of plugin directory.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected string $path = '';

	/**
	 * Plugin basename.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected string $basename = '';

	/**
	 * Menu Icon.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $menu_icon = 'dashicons-megaphone';

	/**
	 * Log location.
	 *
	 * @since 1.3.7
	 * @var string
	 */
	public string $logger_location = '';

	/**
	 * Singleton instance of plugin.
	 *
	 * @since 1.0.0
	 * @var Constant_Contact
	 */
	protected static $single_instance;

	/**
	 * An instance of the ConstantContact_API Class.
	 *
	 * @since 1.0.1
	 * @var ConstantContact_API
	 */
	private ConstantContact_API $api;

	/**
	 * An instance of the ConstantContact_Builder Class.
	 *
	 * @since 1.0.1
	 * @var ConstantContact_Builder
	 */
	private ConstantContact_Builder $builder;

	/**
	 * An instance of the ConstantContact_Builder_Fields Class.
	 *
	 * @since 1.0.1
	 * @var ConstantContact_Builder_Fields
	 */
	private ConstantContact_Builder_Fields $builder_fields;

	/**
	 * An instance of the ConstantContact_CPTS Class.
	 *
	 * @since 1.0.1
	 * @var ConstantContact_CPTS
	 */
	private ConstantContact_CPTS $cpts;

	/**
	 * An instance of the ConstantContact_Display Class.
	 *
	 * @since 1.0.1
	 * @var ConstantContact_Display
	 */
	private ConstantContact_Display $display;

	/**
	 * An instance of the ConstantContact_Display_Shortcode Class.
	 *
	 * @since 1.0.1
	 * @var ConstantContact_Display_Shortcode
	 */
	private ConstantContact_Display_Shortcode $display_shortcode;

	/**
	 * An instance of the ConstantContact_Lists Class.
	 *
	 * @since 1.0.1
	 * @var ConstantContact_Lists
	 */
	private ConstantContact_Lists $lists;

	/**
	 * An instance of the ConstantContact_Process_Form Class.
	 *
	 * @since 1.0.1
	 * @var ConstantContact_Process_Form
	 */
	private ConstantContact_Process_Form $process_form;

	/**
	 * An instance of the ConstantContact_Settings Class.
	 *
	 * @since 1.0.1
	 * @var ConstantContact_Settings
	 */
	private ConstantContact_Settings $settings;

	/**
	 * An instance of the ConstantContact_Connect Class.
	 *
	 * @since 1.0.1
	 * @var ConstantContact_Connect
	 */
	private ConstantContact_Connect $connect;

	/**
	 * An instance of the ConstantContact_Mail Class.
	 *
	 * @since 1.0.1
	 * @var ConstantContact_Mail
	 */
	private ConstantContact_Mail $mail;

	/**
	 * An instance of the ConstantContact_Notifications Class.
	 *
	 * @since 1.0.1
	 * @var ConstantContact_Notifications
	 */
	private ConstantContact_Notifications $notifications;

	/**
	 * An instance of the ConstantContact_Notification_Content Class.
	 *
	 * @since 1.0.1
	 * @var ConstantContact_Notification_Content
	 */
	private ConstantContact_Notification_Content $notification_content;

	/**
	 * An instance of the ConstantContact_Updates Class.
	 *
	 * @since 1.0.1
	 * @var ConstantContact_Updates
	 */
	private ConstantContact_Updates $updates;

	/**
	 * An instance of the ConstantContact_User_Customizations Class.
	 *
	 * @since 1.0.1
	 * @var ConstantContact_User_Customizations
	 */
	private ConstantContact_User_Customizations $customizations;

	/**
	 * An instance of the ConstantContact_Logging Class.
	 *
	 * @since 1.3.7
	 * @var ConstantContact_Logging
	 */
	private ConstantContact_Logging $logging;

	/**
	 * An instance of the ConstantContact_Admin Class.
	 *
	 * @since 1.0.1
	 * @var ConstantContact_Admin
	 */
	private ConstantContact_Admin $admin;

	/**
	 * An instance of the ConstantContact_Admin_Pages class.
	 *
	 * @since 1.0.1
	 * @var ConstantContact_Admin_Pages
	 */
	private ConstantContact_Admin_Pages $admin_pages;

	/**
	 * An instance of the ConstantContact_Shortcode class.
	 *
	 * @since 1.0.1
	 * @var ConstantContact_Shortcode
	 */
	private ConstantContact_Shortcode $shortcode;

	/**
	 * An instance of the ConstantContact_Gutenberg class.
	 *
	 * @since 1.5.0
	 * @var ConstantContact_Gutenberg
	 */
	private ConstantContact_Gutenberg $gutenberg;

	/**
	 * An instance of the ConstantContact_Beaver_Builder class.
	 *
	 * @since 1.5.0
	 * @var ConstantContact_Beaver_Builder
	 */
	private ConstantContact_Beaver_Builder $beaver_builder;

	/**
	 * An instance of the ConstantContact_Attached_Lists_Field class
	 *
	 * @since 2.6.0
	 * @var ConstantContact_Attached_Lists_Field
	 */
	private ConstantContact_Attached_Lists_Field $attached_lists;

	/*
	 * An instance of the ConstantContact_Elementor class.
	 *
	 * @since 1.5.0
	 * @var ConstantContact_Elementor
	 */
	private ConstantContact_Elementor $elementor;

	/**
	 * An instance of the ConstantContact_Health class.
	 *
	 * @since 2.3.0
	 * @var ConstantContact_Health
	 */
	private ConstantContact_Health $health;

	/**
	 * Option name for where we store the timestamp of when the plugin was activated.
	 *
	 * @since 1.6.0
	 *
	 * @var string
	 */
	public static string $activated_date_option = 'ctct_plugin_activated_date';

	/**
	 * License file.
	 *
	 * @since 1.0.1
	 * @var string
	 */
	const LICENSE_FILE = 'license.txt';

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return Constant_Contact A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function __construct() {

		// Set up some helper properties.
		$this->basename = plugin_basename( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->path     = plugin_dir_path( __FILE__ );

		if ( ! $this->meets_php_requirements() ) {
			add_action( 'admin_notices', [ $this, 'minimum_version' ] );
			return;
		}

		// Load our plugin and our libraries.
		$this->plugin_classes();
		$this->admin_plugin_classes();

		// Set logging location.
		$this->logger_location = $this->logging->get_logging_location();

		// Include our notifications logic.
		self::include_file( 'notification-logic', false );

		// Include our helper functions function for end-users.
		self::include_file( 'helper-functions', false );

		// Include compatibility fixes to address conflicts with other plug-ins.
		self::include_file( 'compatibility', false );

		// Include deprecated functions.
		self::include_file( 'deprecated', false );
	}

	/**
	 * Display an admin notice for users on less than PHP 5.4.x.
	 *
	 * @since 1.0.1
	 */
	public function minimum_version() {
		echo '<div id="message" class="notice is-dismissible error"><p>' . esc_html__( 'Constant Contact Forms requires PHP 7.4 or higher. Your hosting provider or website administrator should be able to assist in updating your PHP version.', 'constant-contact-forms' ) . '</p></div>';
	}

	/**
	 * Attach other plugin classes to the base plugin class.
	 *
	 * @since 1.0.0
	 */
	public function plugin_classes() {
		$this->api = new ConstantContact_API( $this );
		if ( class_exists( 'FLBuilder' ) ) {
			// Load if Beaver Builder is active.
			$this->beaver_builder = new ConstantContact_Beaver_Builder( $this );
		}
		$this->builder              = new ConstantContact_Builder( $this );
		$this->builder_fields       = new ConstantContact_Builder_Fields( $this );
		$this->cpts                 = new ConstantContact_CPTS( $this );
		$this->display              = new ConstantContact_Display( $this );
		$this->shortcode            = new ConstantContact_Shortcode( $this );
		$this->display_shortcode    = new ConstantContact_Display_Shortcode( $this );
		$this->lists                = new ConstantContact_Lists( $this );
		$this->process_form         = new ConstantContact_Process_Form( $this );
		$this->settings             = new ConstantContact_Settings( $this );
		$this->connect              = new ConstantContact_Connect( $this );
		$this->mail                 = new ConstantContact_Mail( $this );
		$this->notifications        = new ConstantContact_Notifications( $this );
		$this->notification_content = new ConstantContact_Notification_Content( $this );
		$this->updates              = new ConstantContact_Updates( $this );
		$this->logging              = new ConstantContact_Logging( $this );
		$this->customizations       = new ConstantContact_User_Customizations( $this );
		$this->health               = new ConstantContact_Health( $this );
		$this->attached_lists       = new ConstantContact_Attached_Lists_Field();
		if ( in_array( 'elementor/elementor.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			// Load if Elementor is active.
			$this->elementor = new ConstantContact_Elementor( $this );
		}
	}

	/**
	 * Attach other plugin classes to the base plugin class, but only in the admin.
	 *
	 * @since 1.0.0
	 */
	public function admin_plugin_classes() {
		$this->admin       = new ConstantContact_Admin( $this, $this->basename );
		$this->admin_pages = new ConstantContact_Admin_Pages( $this );
		$this->gutenberg   = new ConstantContact_Gutenberg( $this );
	}

	/**
	 * Add hooks and filters.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function hooks() {
		if ( ! $this->meets_php_requirements() ) {
			add_action( 'admin_notices', [ $this, 'minimum_version' ] );
			return;
		}

		add_action( 'init', [ $this, 'init' ] );
		add_action( 'widgets_init', [ $this, 'widgets' ] );
		add_filter( 'body_class', [ $this, 'body_classes' ] );

		$this->load_libs();

		add_filter( 'widget_text', 'do_shortcode' );
		add_action( 'admin_enqueue_scripts', [ $this, 'register_admin_assets' ], 1 );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_front_assets' ], 1 );
		add_action( 'init', [ $this->shortcode, 'register_shortcode' ] );
		add_action( 'save_post', [ $this->shortcode, 'clear_forms_list_transient' ], 10, 2 );

		if ( is_admin() ) {
			add_action( 'wp_ajax_ctct_dismiss_first_modal', [ $this, 'ajax_save_clear_first_form' ] );
			add_action( 'wp_ajax_nopriv_ctct_dismiss_first_modal', [ $this, 'ajax_save_clear_first_form' ] );
		}
	}

	/**
	 * Activate the plugin.
	 *
	 * @since 1.0.0
	 */
	public function activate() {
		update_option( self::$activated_date_option, time() );
	}

	/**
	 * Deactivate the plugin, refresh some notification dismissals.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function deactivate() {

		if ( ! $this->meets_php_requirements() ) {
			return;
		}

		// Clear out connection data when deactivating plugin.
		delete_option( 'ctct_access_token' );
		delete_option( '_ctct_access_token' );
		delete_option( 'ctct_refresh_token' );
		delete_option( '_ctct_refresh_token' );
		delete_option( '_ctct_expires_in' );
		delete_option( 'CtctConstantContactcode_verifier' );
		delete_option( 'CtctConstantContactState' );
		delete_option( 'ctct_auth_url' );
		delete_option( 'ctct_key' );
		constant_contact_delete_option( '_ctct_form_state_authcode' );
		wp_clear_scheduled_hook( 'refresh_token_job' );
		wp_unschedule_hook( 'refresh_token_job' );

		$this->notifications->delete_dismissed_notification( 'activation' );
	}

	/**
	 * Delete a number of transients and database options on uninstall.
	 *
	 * @since 1.6.0
	 */
	public function uninstall() {
		$uninstaller = new ConstantContact_Uninstall();
		$uninstaller->run();
	}

	/**
	 * Whether or not we meet our minimal PHP requirements.
	 *
	 * @since 1.2.0
	 *
	 * @return bool
	 */
	public function meets_php_requirements() {
		return version_compare( PHP_VERSION, '7.4.0', '>=' );
	}

	/**
	 * Init hooks.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		load_plugin_textdomain( 'constant-contact-forms', false, dirname( $this->basename ) . '/languages/' );
		$this->init_debug_log();
	}

	/**
	 * Initialize debug log on init.
	 *
	 * @since 1.8.5
	 *
	 * @return void
	 */
	protected function init_debug_log() {

		if ( ! constant_contact_debugging_enabled() ) {
			return;
		}

		// Create logging file and directory.
		$this->logging->initialize_logging();
	}

	/**
	 * Load Vendor libraries.
	 *
	 * @since 1.0.0
	 */
	public function load_libs() {

		// Load what we can, automagically.
		require_once $this->dir( 'vendor/autoload.php' );

		require_once $this->dir( 'vendor/cmb2/cmb2/init.php' );
	}

	/**
	 * Load and register our Constant Contact widget.
	 *
	 * @since 1.1.0
	 */
	public function widgets() {
		require_once constant_contact()->path . 'includes/widgets/contact-form-select.php';
		register_widget( 'ConstantContactWidget' );
	}

	/**
	 * Save our dismissed first form notification.
	 *
	 * @since 1.0.0
	 */
	public function ajax_save_clear_first_form() {

		if ( 'ctct_dismiss_first_modal' === filter_input( INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS ) ) {
			// Save our dismiss for the first form modal.
			update_option( 'ctct_first_form_modal_dismissed', current_time( 'timestamp' ) );

			wp_send_json_success(
				[ 'message' => esc_html__( 'Dismiss successsful', 'constant-contact-forms' ) ]
			);
			exit();
		}

		wp_send_json_error(
			[ 'message' => esc_html__( 'Dismiss failed', 'constant-contact-forms' ) ]
		);
		exit();
	}

	/**
	 * Magic getter for our object.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception Throws an exception if the field is invalid.
	 *
	 * @param string $field Field to get.
	 * @return mixed
	 */
	public function __get( $field ) {
		switch ( $field ) {
			case 'version':
				return self::VERSION;
			case 'basename':
			case 'path':
			case 'url':
				return $this->$field;
			default:
				throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
		}
	}

	/**
	 * Admin getter
	 * @since 2.11.0
	 * @return ConstantContact_Admin
	 */
	public function get_admin() : ConstantContact_Admin {
		return $this->admin;
	}

	/**
	 * Admin pages getter
	 * @since 2.11.0
	 * @return ConstantContact_Admin_Pages
	 */
	public function get_admin_pages(): ConstantContact_Admin_Pages {
		return $this->admin_pages;
	}

	/**
	 * API getter
	 * @since 2.11.0
	 * @return ConstantContact_API
	 */
	public function get_api(): ConstantContact_API {
		return $this->api;
	}

	/**
	 * Beaver Builder getter.
	 *
	 * @since 2.11.0
	 * @return ConstantContact_Beaver_Builder
	 */
	public function get_beaver_builder(): ConstantContact_Beaver_Builder {
		return $this->beaver_builder;
	}

	/**
	 * Form Builder getter.
	 *
	 * @since 2.11.0
	 * @return ConstantContact_Builder
	 */
	public function get_builder(): ConstantContact_Builder {
		return $this->builder;
	}

	/**
	 * Form Builder Fields getter.
	 *
	 * @since 2.11.0
	 * @return ConstantContact_Builder_Fields
	 */
	public function get_builder_fields(): ConstantContact_Builder_Fields {
		return $this->builder_fields;
	}

	/**
	 * Connect getter.
	 *
	 * @since 2.11.0
	 * @return ConstantContact_Connect
	 */
	public function get_connect(): ConstantContact_Connect {
		return $this->connect;
	}

	/**
	 * CPTS getter.
	 *
	 * @since 2.11.0
	 * @return ConstantContact_CPTS
	 */
	public function get_cpts(): ConstantContact_CPTS {
		return $this->cpts;
	}

	/**
	 * User Customizations getter.
	 *
	 * @since 2.11.0
	 * @return ConstantContact_User_Customizations
	 */
	public function get_customizations(): ConstantContact_User_Customizations {
		return $this->customizations;
	}

	/**
	 * Display getter.
	 *
	 * @since 2.11.0
	 * @return ConstantContact_Display
	 */
	public function get_display(): ConstantContact_Display {
		return $this->display;
	}

	/**
	 * Display shortcode getter.
	 *
	 * @since 2.11.0
	 * @return ConstantContact_Display_Shortcode
	 */
	public function get_display_shortcode(): ConstantContact_Display_Shortcode {
		return $this->display_shortcode;
	}

	/**
	 * Elementor getter.
	 *
	 * @since 2.11.0
	 * @return ConstantContact_Elementor
	 */
	public function get_elementor() : ConstantContact_Elementor {
		return $this->elementor;
	}

	/**
	 * Gutenberg getter.
	 *
	 * @since 2.11.0
	 * @return ConstantContact_Gutenberg
	 */
	public function get_gutenberg(): ConstantContact_Gutenberg {
		return $this->gutenberg;
	}

	/**
	 * Health getter.
	 * @since 2.11.0
	 * @return ConstantContact_Health
	 */
	public function get_health() : ConstantContact_Health {
		return $this->health;
	}

	/**
	 * Lists getter.
	 *
	 * @since 2.11.0
	 * @return ConstantContact_Lists
	 */
	public function get_lists(): ConstantContact_Lists {
		return $this->lists;
	}

	/**
	 * Logging getter.
	 *
	 * @since 2.11.0
	 * @return ConstantContact_Logging
	 */
	public function get_logging(): ConstantContact_Logging {
		return $this->logging;
	}

	/**
	 * Mail getter.
	 *
	 * @since 2.11.0
	 * @return ConstantContact_Mail
	 */
	public function get_mail(): ConstantContact_Mail {
		return $this->mail;
	}

	/**
	 * Notifications getter.
	 *
	 * @since 2.11.0
	 * @return ConstantContact_Notifications
	 */
	public function get_notifications(): ConstantContact_Notifications {
		return $this->notifications;
	}

	/**
	 * Notification Content getter.
	 *
	 * @since 2.11.0
	 * @return ConstantContact_Notification_Content
	 */
	public function get_notification_content(): ConstantContact_Notification_Content {
		return $this->notification_content;
	}

	/**
	 * Process Form getter.
	 *
	 * @since 2.11.0
	 * @return ConstantContact_Process_Form
	 */
	public function get_process_form(): ConstantContact_Process_Form {
		return $this->process_form;
	}

	/**
	 * Settings getter.
	 *
	 * @since 2.11.0
	 * @return ConstantContact_Settings
	 */
	public function get_settings(): ConstantContact_Settings {
		return $this->settings;
	}

	/**
	 * Shortcode getter.
	 *
	 * @since 2.11.0
	 * @return ConstantContact_Shortcode
	 */
	public function get_shortcode(): ConstantContact_Shortcode {
		return $this->shortcode;
	}

	/**
	 * Updates getter.
	 *
	 * @since 2.11.0
	 * @return ConstantContact_Updates
	 */
	public function get_updates(): ConstantContact_Updates {
		return $this->updates;
	}

	/**
	 * Include a file from the classes directory.
	 *
	 * @since 1.0.0
	 *
	 * @param string $filename      Name of the file to be included.
	 * @param bool   $include_class Whether or ot to include the class.
	 * @return bool Result of include call.
	 */
	public static function include_file( $filename, $include_class = true ) {

		// By default, all files are named 'class-something.php'.
		if ( $include_class ) {
			$filename = 'class-' . $filename;
		}

		$file = self::dir( 'includes/' . $filename . '.php' );

		if ( file_exists( $file ) ) {
			return include_once $file;
		}

		return false;
	}

	/**
	 * This plugin's directory.
	 *
	 * @since 1.0.0
	 *
	 * @param string $path Appended path. Optional.
	 * @return string Directory and path.
	 */
	public static function dir( $path = '' ) {
		static $dir;
		$dir = $dir ? $dir : trailingslashit( __DIR__ );
		return $dir . $path;
	}

	/**
	 * This plugin's url.
	 *
	 * @since 1.0.0
	 *
	 * @param string $path Appended path. Optional.
	 * @return string URL and path
	 */
	public static function url( $path = '' ) {
		static $url;
		$url = $url ? $url : trailingslashit( plugin_dir_url( __FILE__ ) );
		return $url . $path;
	}

	/**
	 * Retrieve license as text.
	 *
	 * @since 1.0.0
	 *
	 * @return string License text.
	 */
	public function get_license_text() {
		$license         = self::url( self::LICENSE_FILE );
		$license_content = wp_remote_get( $license );

		if ( 200 === wp_remote_retrieve_response_code( $license_content ) ) {
			return wp_remote_retrieve_body( $license_content );
		}

		return esc_html__( 'Error loading license.', 'constant-contact-forms' );
	}

	/**
	 * Add some custom body classes for our needs.
	 *
	 * @since 1.2.0
	 *
	 * @param array $classes Existing body classes.
	 * @return array Amended body classes.
	 */
	public function body_classes( $classes = [] ) {
		$theme     = wp_get_theme()->get_template();
		$classes[] = "ctct-{$theme}"; // Prefixing for user knowledge of source.

		return $classes;
	}

	/**
	 * Register our admin styles.
	 *
	 * @since 1.4.0
	 */
	public function register_admin_assets() {

		wp_register_style(
			'constant-contact-forms-admin',
			self::url() . 'assets/css/admin-style.css',
			[],
			self::VERSION
		);
	}

	/**
	 * Register our frontend styles.
	 *
	 * @since 1.4.0
	 */
	public function register_front_assets() {

		if ( constant_contact_disable_frontend_css() ) {
			return;
		}

		wp_register_style(
			'ctct_form_styles',
			self::url() . 'assets/css/style.css',
			[],
			self::VERSION
		);
	}

	/**
	 * Determine if we are in a Constant Contact area.
	 *
	 * @since 1.2.0
	 *
	 * @return bool
	 */
	public function is_constant_contact() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return false;
		}

		if ( ! is_admin() ) {
			return false;
		}

		$ctct_types = [ 'ctct_forms', 'ctct_lists' ];
		$post_type  = filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_SPECIAL_CHARS );
		$post       = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );

		if ( in_array( $post_type, $ctct_types, true ) ) {
			return true;
		}

		if ( in_array( get_post_type( $post ), $ctct_types, true ) ) {
			return true;
		}

		return false;
	}
}
add_action( 'plugins_loaded', [ constant_contact(), 'hooks' ] );

register_activation_hook( __FILE__, [ constant_contact(), 'activate' ] );
register_deactivation_hook( __FILE__, [ constant_contact(), 'deactivate' ] );
register_uninstall_hook( __FILE__, 'constant_contact_uninstall' );

/**
 * Grab the Constant_Contact object and return it.
 * Wrapper for Constant_Contact::get_instance().
 *
 * @since 1.0.0
 *
 * @return Constant_Contact Singleton instance of plugin class.
 */
function constant_contact() {
	return Constant_Contact::get_instance();
}

/**
 * Callback for the uninstall hook.
 *
 * @since 1.6.0
 */
function constant_contact_uninstall() {
	$instance = Constant_Contact::get_instance();
	$instance->uninstall();
}

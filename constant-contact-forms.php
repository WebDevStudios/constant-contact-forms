<?php
/**
 * Constant Contact Main Plugin File
 *
 * @package ConstantContactForms
 * @subpackage Loader
 * @author Constant Contact
 * @since 1.0.1
 * @license GPLv3
 *
 * @wordpress-plugin
 * Plugin Name: Constant Contact Forms for WordPress
 * Plugin URI:  https://www.constantcontact.com
 * Description: Be a better marketer. All it takes is Constant Contact email marketing.
 * Version:     1.1.1
 * Author:      Constant Contact
 * Author URI:  https://www.constantcontact.com
 * License:     GPLv3
 * Text Domain: constant-contact-forms
 * Domain Path: /languages
 */

/**
 * Looking to extend this plugin at all? There are a series of helper
 * functions in includes/helper-functions.php for you to use. There are also
 * filters throughout the plugin, to customize most areas.
 */

/**
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
 * Autoloads files with classes when needed
 *
 * @since  1.0.0
 * @param  string $class_name Name of the class being requested.
 * @return void
 */
function constant_contact_autoload_classes( $class_name ) {
	if ( 0 !== strpos( $class_name, 'ConstantContact_' ) ) {
		return;
	}

	$filename = strtolower( str_replace(
		'_', '-',
		substr( $class_name, strlen( 'ConstantContact_' ) )
	) );

	Constant_Contact::include_file( $filename );
}
spl_autoload_register( 'constant_contact_autoload_classes' );

/**
 * Main initiation class
 *
 * @since  1.0.0
 */
class Constant_Contact {

	/**
	 * Current version
	 *
	 * @var  string
	 * @since  1.0.0
	 */
	const VERSION = '1.1.1';

	/**
	 * URL of plugin directory
	 *
	 * @var string
	 * @since  1.0.0
	 */
	protected $url = '';

	/**
	 * Path of plugin directory
	 *
	 * @var string
	 * @since  1.0.0
	 */
	protected $path = '';

	/**
	 * Plugin basename
	 *
	 * @var string
	 * @since  1.0.0
	 */
	protected $basename = '';

	/**
	 * Plugin name
	 *
	 * @var string
	 * @since  1.0.0
	 */
	public $plugin_name = '';

	/**
	 * Text domain
	 *
	 * @var string
	 * @since  1.0.0
	 */
	public $text_domain = 'constant-contact-forms';

	/**
	 * Menu Icon
	 *
	 * @var string
	 * @since  1.0.0
	 */
	public $menu_icon = 'dashicons-megaphone';

	/**
	 * Does site support encrpytions?
	 *
	 * @var boolean
	 * @since 1.0.1
	 */
	public $is_encryption_ready = false;

	/**
	 * Singleton instance of plugin
	 *
	 * @var WDS_Product_Plugin_Framework
	 * @since  1.0.0
	 */
	protected static $single_instance = null;

	/**
	 * All our class instances
	 *
	 * @since 1.0.1
	 */
	private $admin;
	private $admin_pages;
	private $auth_redirect;
	private $api;
	private $builder;
	private $builder_fields;
	private $check;
	private $connect;
	private $cpts;
	private $display;
	private $display_shortcode;
	private $lists;
	private $process_form;
	private $settings;
	private $mail;
	private $notifications;
	private $notification_content;
	private $authserver;
	private $updates;

	/**
	 * License file.
	 *
	 * @var   string
	 * @since 1.0.1
	 */
	const LICENSE_FILE = 'license.txt';

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since  1.0.0
	 * @return Constant_Contact A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin
	 *
	 * @since  1.0.0
	 */
	protected function __construct() {

		// Set up our plugin name.
		$this->plugin_name = __( 'Constant Contact', 'constant-contact-forms' );

		// Set up some helper properties.
		$this->basename = plugin_basename( __FILE__ );
		$this->url	    = plugin_dir_url( __FILE__ );
		$this->path	    = plugin_dir_path( __FILE__ );

		if ( version_compare( PHP_VERSION, '5.4.0', '<' ) ) {
			add_action( 'admin_notices', array( $this, 'minimum_version' ) );
			return;
		}

		// Load our plugin and our libraries.
		$this->plugin_classes();
		$this->load_libs();

		// If we're in the admin, also load up the admin classes.
		if ( is_admin() ) {
			$this->admin_plugin_classes();
		}

		// Include our helper functions function for end-users.
		Constant_Contact::include_file( 'helper-functions', false );
	}

	/**
	 * Display an admin notice for users on less than PHP 5.4.x.
	 *
	 * @since 1.0.1
	 */
	public function minimum_version() {
		echo '<div id="message" class="notice is-dismissible error"><p>' . esc_html__( 'This plugin requires PHP 5.4 or higher. Your hosting provider or website administrator should be able to assist in updating your PHP version.', 'constant-contact-forms' ) . '</p></div>';
	}

	/**
	 * Attach other plugin classes to the base plugin class.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function plugin_classes() {
		$this->api                  = new ConstantContact_API( $this );
		$this->builder              = new ConstantContact_Builder( $this );
		$this->builder_fields       = new ConstantContact_Builder_Fields( $this );
		$this->check                = new ConstantContact_Check( $this );
		$this->cpts                 = new ConstantContact_CPTS( $this );
		$this->display              = new ConstantContact_Display( $this );
		$this->display_shortcode    = new ConstantContact_Display_Shortcode( $this );
		$this->lists                = new ConstantContact_Lists( $this );
		$this->process_form         = new ConstantContact_Process_Form( $this );
		$this->settings             = new ConstantContact_Settings( $this );
		$this->auth_redirect        = new ConstantContact_Auth_Redirect( $this );
		$this->connect              = new ConstantContact_Connect( $this );
		$this->mail                 = new ConstantContact_Mail( $this );
		$this->notifications        = new ConstantContact_Notifications( $this );
		$this->notification_content = new ConstantContact_Notification_Content( $this );
		$this->authserver           = new ConstantContact_Middleware( $this );
		$this->updates              = new ConstantContact_Updates( $this );
	}

	/**
	 * Attach other plugin classes to the base plugin class, but only in the admin
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function admin_plugin_classes() {
		$this->admin       = new ConstantContact_Admin( $this, $this->basename );
		$this->admin_pages = new ConstantContact_Admin_Pages( $this );
	}

	/**
	 * Add hooks and filters
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function hooks() {

		// Hook in our older includes and our init method.
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'init', array( $this, 'includes' ), 5 );
		add_action( 'widgets_init', array( $this, 'widgets' ) );

		// Our vendor files will do a check for ISSSL, so we want to set it to be that.
		// See Guzzle for more info and usage of this.
		if ( is_ssl() || ! defined( 'ISSSL' ) ) {
			define( 'ISSSL', true );
		}

		// Allow shortcodes in widgets for our plugin.
		add_filter( 'widget_text', 'do_shortcode' );

		if ( is_admin() ) {
			add_action( 'wp_ajax_ctct_dismiss_first_modal', array( $this, 'ajax_save_clear_first_form' ) );
			add_action( 'wp_ajax_nopriv_ctct_dismiss_first_modal', array( $this, 'ajax_save_clear_first_form' ) );
		}
	}

	/**
	 * Activate the plugin
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function _activate() { }

	/**
	 * Deactivate the plugin
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function _deactivate() {

		// If we deactivate the plugin, remove our saved dismiss state for the activation
		// admin notice that pops up, so we can re-prompt the user to connect.
		$this->notifications->delete_dismissed_notification( 'activation' );

		// Remove our saved transients for our lists, so we force a refresh on re-connection.
		delete_transient( 'ctct_lists' );

	}

	/**
	 * Init hooks
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function init() {

		// Load our textdomain.
		load_plugin_textdomain( $this->text_domain, false, dirname( $this->basename ) . '/languages/' );
	}

	/**
	 * Load Vendor libraries
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function load_libs() {

		// Set an array of libraries we need to load.
		$libs = array(
			'CMB2/init.php',
			'constantcontact/autoload.php',
			'constantcontact/constantcontact/constantcontact/src/Ctct/autoload.php',

			'defuse-php-encryption/Exception/CryptoException.php',
			'defuse-php-encryption/Exception/BadFormatException.php',
			'defuse-php-encryption/Exception/EnvironmentIsBrokenException.php',
			'defuse-php-encryption/Exception/IOException.php',
			'defuse-php-encryption/Exception/WrongKeyOrModifiedCiphertextException.php',

			'defuse-php-encryption/Core.php',
			'defuse-php-encryption/Crypto.php',
			'defuse-php-encryption/DerivedKeys.php',
			'defuse-php-encryption/Encoding.php',
			'defuse-php-encryption/Key.php',
			'defuse-php-encryption/KeyOrPassword.php',
			'defuse-php-encryption/RuntimeTests.php',
		);

		// If we don't alrady have WDS_Shortcodes loaded somewhere else, load it up.
		if ( ! function_exists( 'wds_shortcodes' ) ) {
			$libs[] = 'WDS-Shortcodes/wds-shortcodes.php';
		}

		// Loop through our vendor libraries and load them.
		foreach ( $libs as $lib ) {

			// Require_once our file.
			require_once( $this->dir( "vendor/{$lib}" ) );
		}
	}

	/**
	 * Load includes.
	 *
	 * @since 1.0.0
	 */
	public function includes() {

		// Only load this if we have the WDS Shortcodes class.
		if ( class_exists( 'WDS_Shortcodes' ) ) {

			if ( $this->is_ctct_editor_screen() ) {
				return;
			}
			// Set up our base WDS_Shortcodes class.
			$this->shortcode       = new ConstantContact_Shortcode();

			// Set our custom shortcode with correct version and data.
			$this->shortcode_admin = new ConstantContact_Shortcode_Admin(
				$this->shortcode->shortcode,
				self::VERSION,
				$this->shortcode->atts_defaults
			);

			// Launch it.
			$this->shortcode_admin->hooks();
		}

	}

	public function widgets() {
		require_once constant_contact()->path . 'includes/widgets/contact-form-select.php';
		register_widget( 'ConstantContactWidget' );
	}

	/**
	 * Save our dismissed first form notification
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function ajax_save_clear_first_form() {

		if ( isset( $_POST['action'] ) && 'ctct_dismiss_first_modal' === $_POST['action'] ) { // Input var okay.

			// Save our dismiss for the first form modal.
			update_option( 'ctct_first_form_modal_dismissed', time() );
		}
		wp_die();
	}

	/**
	 * Magic getter for our object.
	 *
	 * @since  1.0.0
	 * @param string $field Field to get.
	 * @throws Exception Throws an exception if the field is invalid.
	 * @return mixed
	 */
	public function __get( $field ) {
		switch ( $field ) {
			case 'version':
				return self::VERSION;
			case 'admin':
			case 'admin_pages':
			case 'auth_redirect':
			case 'api':
			case 'basename':
			case 'builder':
			case 'builder_fields':
			case 'connect':
			case 'check':
			case 'cpts':
			case 'display':
			case 'display_shortcode':
			case 'lists':
			case 'path':
			case 'plugin_name':
			case 'process_form':
			case 'settings':
			case 'url':
			case 'mail':
			case 'notifications':
			case 'notification_content':
			case 'authserver':
			case 'updates':
				return $this->$field;
			default:
				throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
		}
	}

	/**
	 * Include a file from the classes directory
	 *
	 * @since  1.0.0
	 * @param  string $filename Name of the file to be included.
	 * @param  bool   $include_class Whether or ot to include the class.
	 * @return bool   Result of include call.
	 */
	public static function include_file( $filename, $include_class = true ) {

		// By default, all files are named 'class-something.php'.
		if ( $include_class ) {
			$filename = 'class-' . $filename;
		}

		// Get the file.
		$file = self::dir( 'includes/' . $filename . '.php' );

		// If its there, include it.
		if ( file_exists( $file ) ) {
			return include_once( $file );
		}

		// Wasn't there.
		return false;
	}

	/**
	 * This plugin's directory.
	 *
	 * @since  1.0.0
	 * @param  string $path (optional) appended path.
	 * @return string Directory and path
	 */
	public static function dir( $path = '' ) {
		static $dir;
		$dir = $dir ? $dir : trailingslashit( dirname( __FILE__ ) );
		return $dir . $path;
	}

	/**
	 * This plugin's url
	 *
	 * @since  1.0.0
	 * @param  string $path (optional) appended path.
	 * @return string   URL and path
	 */
	public static function url( $path = '' ) {
		static $url;
		$url = $url ? $url : trailingslashit( plugin_dir_url( __FILE__ ) );
		return $url . $path;
	}

	/**
	 * Retrieve license as text.
	 *
	 * @since  1.0.0
	 * @return string License text.
	 */
	public function get_license_text() {
		$license = $this->dir( self::LICENSE_FILE );

		if ( ! is_readable( $license ) ) {
			return __( 'Error loading license.', 'constant-contact-forms' );
		}

		return file_get_contents( $license );
	}

	/**
	 * Check if we are editing a Constant Contact post type post.
	 *
	 * @since 1.1.0
	 *
	 * @param int $post_id Post ID to check for.
	 * @return bool
	 */
	public function is_ctct_editor_screen( $post_id = 0 ) {

		if ( empty( $post_id ) ) {
			if ( ! empty( $_GET ) && isset( $_GET['post'] ) ) {
				$post_id = absint( $_GET['post'] );
			}
		}

		if ( empty( $_GET ) ) {
			return false;
		}

		if ( isset( $_GET['post_type'] ) && 'ctct_forms' === (string) $_GET['post_type'] ) {
			return true;
		}

		if ( 'ctct_forms' === get_post_type( $post_id ) ) {
			return true;
		}

		return false;
	}
}
add_action( 'plugins_loaded', array( constant_contact(), 'hooks' ) );

register_activation_hook( __FILE__ , array( constant_contact(), '_activate' ) );
register_deactivation_hook( __FILE__ , array( constant_contact(), '_deactivate' ) );

/**
 * Grab the Constant_Contact object and return it.
 * Wrapper for Constant_Contact::get_instance()
 *
 * @since  1.0.0
 * @return Constant_Contact Singleton instance of plugin class.
 */
function constant_contact() {
	return Constant_Contact::get_instance();
}

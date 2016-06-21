<?php
/**
 * Constant Contact Loader
 *
 * @package ConstantContactLoader
 * @subpackage Loader
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Plugin Name: Constant Contact
 * Plugin URI:  http://constantcontact.com
 * Description: Be a better marketer. All it takes is Constant Contact email marketing.
 * Version:	 999.0.0
 * Author:	  Pluginize
 * Author URI:  http://pluginize.com
 * License:	 GPLv2
 * Text Domain: constantcontact
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2016 Constant Contact (email : contact@contstantcontact.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
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

// Ryan's test APP Keys.
// define( 'CTCT_APIKEY', '595r3d4q432c3mdv2jtd3nj9' );
// define( 'CTCT_SECRETKEY', 'XJ9H8n5m8fqt2WBpSk6E6dJm' );

// CTCT APP keys.
// define( 'CTCT_APIKEY', '6g9ecnn4d5epme94wpq26m77' );
// define( 'CTCT_SECRETKEY', 'FxAkakNMj5HjwQUncQ2hMYj6' );

/**
 * Main initiation class
 *
 * @since  1.0.0
 * @var  string $version  Plugin version
 * @var  string $basename Plugin basename
 * @var  string $url	  Plugin URL
 * @var  string $path	 Plugin Path
 * @var  string $plugin_name Plugin name
 */
class Constant_Contact {

	/**
	 * Current version
	 *
	 * @var  string
	 * @since  1.0.0
	 */
	const VERSION = '1.0.0';

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
	public $plugin_name = 'Constant Contact';

	/**
	 * Text domain
	 *
	 * @var string
	 * @since  1.0.0
	 */
	public $text_domain = 'constantcontact';

	/**
	 * Menu Icon
	 *
	 * @var string
	 * @since  1.0.0
	 */
	public $menu_icon = 'dashicons-megaphone';

	/**
	 * Singleton instance of plugin
	 *
	 * @var WDS_Product_Plugin_Framework
	 * @since  1.0.0
	 */
	protected static $single_instance = null;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since  1.0.0
	 * @return WDS_Product_Plugin_Framework A single instance of this class.
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
		$this->basename = plugin_basename( __FILE__ );
		$this->url	  = plugin_dir_url( __FILE__ );
		$this->path	 = plugin_dir_path( __FILE__ );

		$this->load_libs();
		$this->plugin_classes();
	}

	/**
	 * Attach other plugin classes to the base plugin class.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function plugin_classes() {

		$this->ctct_forms = new ConstantContact_CPTS( $this );
	}

	/**
	 * Add hooks and filters
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'init', array( $this, 'includes' ), 5 );

		add_filter( 'plugin_action_links_'. $this->basename, array( $this, 'add_social_links' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );

		if ( is_ssl() ) {
			define( 'ISSSL', true );
		}
	}

	/**
	 * Activate the plugin
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function _activate() {
		// Make sure any rewrite functionality has been loaded.
		flush_rewrite_rules();
	}

	/**
	 * Deactivate the plugin
	 * Uninstall routines should be in uninstall.php
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function _deactivate() {}

	/**
	 * Init hooks
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function init() {

		if ( $this->check_requirements() ) {
			load_plugin_textdomain( 'constantcontact', false, dirname( $this->basename ) . '/languages/' );
		}
	}

	/**
	 * Scripts
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function scripts() {
		global $pagenow;

		// Register out javascript file.
		wp_register_script( 'ctct_form', $this->url() . 'assets/js/plugin.js' );

		if ( isset( $pagenow ) && in_array( $pagenow, array( 'post.php', 'post-new.php' ), true ) ) {
			// Enqueued script with localized data.
			wp_enqueue_script( 'ctct_form' );
		}
	}

	/**
	 * Load Vendor libraries
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function load_libs() {

		// Load cmb2.
		if ( file_exists( __DIR__ . '/vendor/cmb2/init.php' ) ) {
			require_once  __DIR__ . '/vendor/cmb2/init.php';
		} elseif ( file_exists( __DIR__ . '/vendor/CMB2/init.php' ) ) {
			require_once  __DIR__ . '/vendor/CMB2/init.php';
		}

		// Load encryption class.
		if ( file_exists( __DIR__ . '/vendor/defuse/php-encryption/defuse-crypto.phar' ) ) {
			require_once  __DIR__ . '/vendor/defuse/php-encryption/defuse-crypto.phar';
		}

		// Load CC php SDK.
		if ( file_exists( __DIR__ . '/vendor/constantcontact/autoload.php' ) ) {
			require_once  __DIR__ . '/vendor/constantcontact/autoload.php';
		}

		// Load shortcode button framework.
		if ( file_exists( __DIR__ . '/vendor/wds/WDS-Shortcodes/wds-shortcodes.php' ) ) {
			require_once  __DIR__ . '/vendor/wds/WDS-Shortcodes/wds-shortcodes.php';
		}

	}

	/**
	 * Load includes.
	 *
	 * @since 1.0.0
	 */
	public function includes() {

		if ( class_exists( 'WDS_Shortcodes_Base' ) ) {

			$this->test_shortcode = new ConstantContact_Shortcode();
			$this->test_shortcode_admin = new ConstantContact_Shortcode_Admin(
				$this->test_shortcode->shortcode,
				self::VERSION,
				$this->test_shortcode->atts_defaults
			);
			$this->test_shortcode_admin->hooks();

		}

		if ( file_exists( __DIR__ . '/classes/class-api.php' ) ) {
			require_once  __DIR__ . '/classes/class-api.php';
		}

		if ( file_exists( __DIR__ . '/classes/class-lists.php' ) ) {
			require_once  __DIR__ . '/classes/class-lists.php';
		}

		if ( file_exists( __DIR__ . '/classes/class-process-form.php' ) ) {
			require_once  __DIR__ . '/classes/class-process-form.php';
		}

		if ( file_exists( __DIR__ . '/inc/auth-redirect.php' ) ) {
			require_once  __DIR__ . '/inc/auth-redirect.php';
		}

		if ( file_exists( __DIR__ . '/classes/class-settings.php' ) ) {
			require_once  __DIR__ . '/classes/class-settings.php';
		}

		if ( file_exists( __DIR__ . '/classes/class-builder.php' ) ) {
			require_once  __DIR__ . '/classes/class-builder.php';
		}

		// Only load in admin.
		if ( is_admin() ) {

			if ( file_exists( __DIR__ . '/classes/class-admin.php' ) ) {
				require_once  __DIR__ . '/classes/class-admin.php';
			}

			if ( file_exists( __DIR__ . '/inc/admin/post-list-columns.php' ) ) {
				require_once  __DIR__ . '/inc/admin/post-list-columns.php';
			}

			if ( file_exists( __DIR__ . '/classes/class-connect.php' ) ) {
				require_once  __DIR__ . '/classes/class-connect.php';
			}
		}
	}

	/**
	 * Check if the plugin meets requirements and
	 * disable it if they are not present.
	 *
	 * @since  1.0.0
	 * @return boolean result of meets_requirements
	 */
	public function check_requirements() {
		if ( ! $this->meets_requirements() ) {

			// Add a dashboard notice.
			add_action( 'all_admin_notices', array( $this, 'requirements_not_met_notice' ) );

			// Deactivate our plugin.
			add_action( 'admin_init', array( $this, 'deactivate_me' ) );

			return false;
		}

		return true;
	}

	/**
	 * Deactivates this plugin, hook this function on admin_init.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function deactivate_me() {

		if ( is_plugin_active( $this->basename ) ) {
			deactivate_plugins( $this->basename );
		}

	}

	/**
	 * Check that all plugin requirements are met
	 *
	 * @since  1.0.0
	 * @return boolean True if requirements are met.
	 */
	public static function meets_requirements() {
		// Do checks for required classes / functions
		// function_exists('') & class_exists('').
		// We have met all requirements.
		return true;
	}

	/**
	 * Adds a notice to the dashboard if the plugin requirements are not met
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function requirements_not_met_notice() {
		// Output our error.
		$error_text = sprintf( __( $this->plugin_name . ' is missing requirements and has been <a href="%s">deactivated</a>. Please make sure all requirements are available.', 'constantcontact' ), admin_url( 'plugins.php' ) );

		echo '<div id="message" class="error">';
		echo '<p>' . $error_text . '</p>';
		echo '</div>';
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
			case 'basename':
			case 'url':
			case 'path':
				return $this->$field;
			default:
				throw new Exception( 'Invalid '. __CLASS__ .' property: ' . $field );
		}
	}

	/**
	 * Include a file from the classes directory
	 *
	 * @since  1.0.0
	 * @param  string $filename Name of the file to be included.
	 * @return bool   Result of include call.
	 */
	public static function include_file( $filename ) {
		$file = self::dir( 'classes/class-'. $filename .'.php' );
		if ( file_exists( $file ) ) {
			return include_once( $file );
		}
		return false;
	}

	/**
	 * This plugin's directory
	 *
	 * @since  1.0.0
	 * @param  string $path (optional) appended path.
	 * @return string	   Directory and path
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
	 * @return string	   URL and path
	 */
	public static function url( $path = '' ) {
		static $url;
		$url = $url ? $url : trailingslashit( plugin_dir_url( __FILE__ ) );
		return $url . $path;
	}

	/**
	 * Add social media links to plugin screen
	 *
	 * @param array $links plugin action links.
	 */
	public function add_social_links( $links ) {

		$site_link = 'http://constantcontact.com/';
		$twitter_status = sprintf( __( 'Check out the official WordPress plugin from @constantcontact', 'constantcontact' ), $this->plugin_name );

		array_push( $links, '<a title="'. __( 'Be a better marketer. All it takes is Constant Contact email marketing.', 'constantcontact' ). '" href="'. $site_link. '" target="_blank">constantcontact.com</a>' );
		array_push( $links, '<a title="'. __( 'Spread the word!', 'constantcontact' ). '" href="https://www.facebook.com/sharer/sharer.php?u='. urlencode( $site_link ). '" target="_blank" class="dashicons-before dashicons-facebook"></a>' );
		array_push( $links, '<a title="'. __( 'Spread the word!', 'constantcontact' ). '" href="https://twitter.com/home?status='. urlencode( $twitter_status ). '" target="_blank" class="dashicons-before dashicons-twitter"></a>' );
		array_push( $links, '<a title="'. __( 'Spread the word!', 'constantcontact' ). '" href="https://plus.google.com/share?url='. urlencode( $site_link ). '" target="_blank" class="dashicons-before dashicons-googleplus"></a>' );

		return $links;
	}
}

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

// Kick it off.
add_action( 'plugins_loaded', array( constant_contact(), 'hooks' ) );

register_activation_hook( __FILE__, array( constant_contact(), '_activate' ) );
register_deactivation_hook( __FILE__, array( constant_contact(), '_deactivate' ) );

<?php
/**
 * ConstantContact_Check class
 *
 * @package ConstantContact_Check
 * @subpackage ConstantContact
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Class ConstantContact_Check
 */
class ConstantContact_Check {

	/**
	 * Parent plugin class
	 *
	 * @var   class
	 * @since 0.0.1
	 */
	protected $plugin = null;


	/**
	 * Constructor
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		// Set our encrpytion ready flag
		$this->plugin->is_encryption_ready = $this->is_encryption_ready();
	}

	public function get_checks_to_make() {

		// All the functions, classes, etc that we want to check on the server
		return apply_filters( 'constant_contact_server_checks', array(
			'functions' => array(
				'openssl_encrypt',
				'openssl_decrypt',
			),
			'classes' => array(
				'CMB2',
				'WDS_Shortcodes',
			),
		) );
	}

	/**
	 * Checks to see if the server will support encryption functionality
	 *
	 * @return boolean if we should load/use the encryption libraries
	 */
	public function is_encryption_ready() {

		// Make sure we have our openssl libraries
		if ( ! function_exists( 'openssl_encrypt' ) || ! function_exists( 'openssl_decrypt' ) ) {
			return false;
		}

		// @TODO get more checks in here

		return true;
	}

	/**
	 * Displays our server check
	 *
	 * @return string html markup
	 */
	public function display_server_checks() {

		// Get everything we should check
		$checks = $this->get_checks_to_make();


		// If we have a functions array
		if (
			isset( $checks['functions'] ) &&
			is_array( $checks['functions'] ) &&
			1 <= count( $checks['functions'] )
		) {

			echo '<h5>' . __( 'Functions:', 'constantcontact' ) . '</h5><ul>';

			// Loop through ech function
			foreach ( $checks['functions'] as $function ) {

				// Check to see if its available
				echo '<li>' . esc_attr( $function ) . ' : ' . $this->exists_text( $function, 'f' ) . '</li>';
			}

			echo '</ul>';
		}

		// See if we have any classes we should check for
		if (
			isset( $checks['classes'] ) &&
			is_array( $checks['classes'] ) &&
			1 <= count( $checks['classes'] )
		) {

			echo '<h5>' . __( 'Classes:', 'constantcontact' ) . '</h5><ul>';

			// Loop through em
			foreach ( $checks['classes'] as $class ) {

				// check to see if its available
				echo '<li>' . esc_attr( $class ) . ' : ' . $this->exists_text( $class, 'c' ) . '</li>';
			}

			echo '</ul>';
		}
	}

	/**
	 * Helper method to give us a display of something exists or not
	 *
	 * @param  string $name function/class to check
	 * @param  string $type function or class?
	 * @return string       emoji of checkmark
	 */
	public function exists_text( $name, $type ) {
		if ( 'f' == $type ) {
			$exists = function_exists( esc_attr( $name ) );
		} elseif ( 'c' == $type ) {
			$exists = class_exists( esc_attr( $name ) );
		} else {
			$exists = false;
		}

		if ( $exists ) {
			return '✅';
		} else {
			return '🚫';
		}
	}
}

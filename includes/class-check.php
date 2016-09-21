<?php
/**
 * @package ConstantContact
 * @subpackage Check
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Helper class to allow for checking and displaying server status.
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
	}

	/**
	 * Lets you add 'ctct-debug-server-check' to the query
	 * args of a page to load a server requirements check
	 *
	 * @since   1.0.0
	 */
	public function maybe_display_debug_info() {

		// Make sure we have our query arg, we're an admin, and we can manage options
		if ( isset( $_GET['ctct-debug-server-check'] ) && is_admin() && current_user_can( 'manage_options' ) ) { // Input var okay.
			?>
			<div class="ctct-server-requirements">
				<p>
					<h4><?php esc_attr_e( 'Server Check', 'constant-contact-forms' ); ?></h4>
					<?php $this->display_server_checks(); ?>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Gets the list of functions / classes we need ot check on the server
	 * to be considered 'valid'
	 *
	 * @since  1.0.0
	 * @return array nested array of functions/classes needed
	 */
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
	 * Displays our server check
	 *
	 * @since  1.0.0
	 * @return string html markup
	 */
	public function display_server_checks() {

		// Get everything we should check
		$checks = $this->get_checks_to_make();

		echo '<table class="ctct-server-check">';
		// If we have a functions array
		if (
			isset( $checks['functions'] ) &&
			is_array( $checks['functions'] ) &&
			1 <= count( $checks['functions'] )
		) {

			// Loop through ech function
			foreach ( $checks['functions'] as $function ) {

				// Check to see if its available
				echo '<tr><td>' . esc_attr( $function ) . '</td><td>' . esc_attr( $this->exists_text( $function, 'f' ) ) . '</td></tr>';
			}
		}

		// See if we have any classes we should check for
		if (
			isset( $checks['classes'] ) &&
			is_array( $checks['classes'] ) &&
			1 <= count( $checks['classes'] )
		) {

			// Loop through em
			foreach ( $checks['classes'] as $class ) {

				// check to see if its available
				echo '<tr><td>' . esc_attr( $class ) . '</td><td>' . esc_attr( $this->exists_text( $class, 'c' ) ) . '</td></tr>';
			}
		}

		// Check to see if we can load the encryption library
		$crypto = $this->plugin->connect->check_crypto_class();
		echo '<tr><td>' . esc_attr_e( 'Encrpytion Library: ', 'constant-contact-forms' ) . '</td><td>' . esc_attr( $this->exists_text( $crypto ) ) . '</td></tr>';

		echo '</table>';
	}

	/**
	 * Helper method to give us a display of something exists or not
	 *
	 * @since  1.0.0
	 * @param  string $name function/class to check
	 * @param  string $type function or class?
	 * @return string       emoji of checkmark
	 */
	public function exists_text( $name, $type = '' ) {
		if ( 'f' === $type ) {
			$exists = function_exists( esc_attr( $name ) );
		} elseif ( 'c' === $type ) {
			$exists = class_exists( esc_attr( $name ) );
		} else {
			$exists = $name;
		}

		if ( $exists ) {
			return '✅';
		} else {
			return '🚫';
		}
	}
}

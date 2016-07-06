<?php
/**
 * Constant Contact Middleware
 *
 * @since 1.0.1
 * @package Constant Contact
 */

/**
 * Constant Contact Middleware.
 *
 * @since 1.0.1
 */
class ConstantContact_Middleware {
	/**
	 * Parent plugin class
	 *
	 * @var   class
	 * @since 1.0.1
	 */
	protected $plugin = null;

	/**
	 * Constructor
	 *
	 * @since 1.0.1
	 * @param  object $plugin Main plugin object.
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Get our auth server link
	 *
	 * @since 1.0.1
	 * @return string auth server link
	 */
	public function do_connect_url() {

		// Get our main link
		$auth_server_link = $this->get_auth_server_link();

		// If we don't have that, then bail
		if ( ! $auth_server_link ) {
			return;
		}

		// Add our query args to our middleware link, and return it
		return $this->add_query_args_to_link( $auth_server_link );
	}

	/**
	 * Add our query args for proof and site callback to our auth server link
	 *
	 * @since 1.0.1
	 * @param  string $link auth server link
	 */
	public function add_query_args_to_link( $link ) {
		return add_query_arg( array(
			'ctct-auth'  => 'auth',
			'ctct-proof' => 'S79rQSbP4onb3gPZLOUD3IUIx', // Hardcoded
			'ctct-site'  => get_site_url(),
			),
		$link );
	}

	/**
	 * Gets our base auth server link
	 *
	 * @since 1.0.1
	 * @return string url of auth server base
	 */
	public function get_auth_server_link() {
		$options = get_option( 'ctct_options_settings' );
		return isset( $options['_ctct_auth_server_link'] ) ? $options['_ctct_auth_server_link'] : false;
	}




	public function verify_and_save_access_token_return() {
		// If we get this, we'll want to start our process of
		// verifying the proof that the middleware server gives us
		// so that we can ignore any malicious entries that are sent to us
		echo '<pre>'; var_dump( $_GET ); die;
	/**
	 * Verifies a given proof from a request against our DB, and does cleanup
	 *
	 * @since  1.0.1
	 * @param  string $proof proof string to check
	 * @return boolean        whether or not its our expected proof
	 */
	public function verify_proof( $proof ) {

		// Get our saved option that we set for our proof
		$expected_proof = get_option( 'ctct_connect_verification' );

		// Clean up after ourselves
		delete_option( 'ctct_connect_verification' );

		// Send back a bool of whether they match or not
		return ( $proof == $expected_proof );

	}
}

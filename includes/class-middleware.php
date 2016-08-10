<?php
/**
 * Powers our OAuth connection to the middleware Constant Contact server.
 *
 * @package ConstantContact
 * @subpackage ConstantContact_Middleware
 * @author Pluginize
 * @since 1.0.0
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
	public function do_connect_url( $proof = '' ) {

		// Get our main link
		$auth_server_link = $this->get_auth_server_link();

		// If we don't have that, then bail
		if ( ! $auth_server_link ) {
			return;
		}

		// Add our query args to our middleware link, and return it
		return $this->add_query_args_to_link( $auth_server_link, $proof );
	}

	/**
	 * Add our query args for proof and site callback to our auth server link
	 *
	 * @since 1.0.1
	 * @param  string $link auth server link
	 */
	public function add_query_args_to_link( $link, $proof ) {
		return add_query_arg( array(
			'ctct-auth'  => 'auth',
			'ctct-proof' => esc_attr( $proof ),
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
		return 'http://cc-auth.wdslab.com/';
	}

	/**
	 * Generates a random key, saves to the DB and returns it
	 *
	 * @since 1.0.1
	 * @return string proof key
	 */
	public function set_verification_option() {

		// Allow re-use of our $proof on a page load
		static $proof = null;

		// If its null, then generate it
		if ( is_null( $proof ) ) {
			$proof = esc_attr( wp_generate_password( 35, false, false ) );
			update_option( 'ctct_connect_verification', $proof );
		}

		// Send it back
		return $proof;
	}

	/**
	 * Verify a returned request from the auth server, and save the returned token
	 *
	 * @return boolean   is valid?
	 */
	public function verify_and_save_access_token_return() {

		// If we get this, we'll want to start our process of
		// verifying the proof that the middleware server gives us
		// so that we can ignore any malicious entries that are sent to us
		// Sanitize our expected data
		$proof = isset( $_GET['proof'] ) ? $_GET['proof'] : false;
		$token = isset( $_GET['token'] ) ? $_GET['token'] : false;
		$key   = isset( $_GET['key'] ) ? $_GET['key'] : false;

		// If we're missing any piece of data, we failed
		if ( ! $proof || ! $token || ! $key ) {
			return false;
		}

		// We'll want to verify our proof before we continue
		if ( ! $this->verify_proof( $proof ) ) {
			return false;
		}

		// Save our token / key into the DB
	 	constant_contact()->connect->update_token( sanitize_text_field( $token ) );
		constant_contact()->connect->e_set( '_ctct_api_key', sanitize_text_field( $key ) );
		return true;
	}

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

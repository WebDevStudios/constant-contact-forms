<?php
/**
 * @package ConstantContact
 * @subpackage Middleware
 * @author Constant Contact
 * @since 1.0.1
 */

/**
 * Powers our OAuth connection to the middleware Constant Contact server.
 *
 * @since 1.0.1
 */
class ConstantContact_Middleware {
	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.1
	 * @var object
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since 1.0.1
	 *
	 * @param object $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Get our auth server link.
	 *
	 * @since 1.0.1
	 *
	 * @param string $proof      Proof.
	 * @param array  $extra_args Array of extra arguements.
	 * @return string Auth server link.
	 */
	public function do_connect_url( $proof = '', $extra_args = array() ) {

		// Get our main link.
		$auth_server_link = $this->get_auth_server_link();

		// If we don't have that, then bail.
		if ( ! $auth_server_link ) {
			return '';
		}

		// Add our query args to our middleware link, and return it.
		return $this->add_query_args_to_link( $auth_server_link, $proof, $extra_args );
	}

	/**
	 * Build out our signup version of the connect url.
	 *
	 * @since 1.0.0
	 *
	 * @param string $proof Proof key
	 * @return string Signup/connect url.
	 */
	public function do_signup_url( $proof = '' ) {

		// Just a wrapper for the connect url, but with the signup param we'll be passing.
		return $this->do_connect_url( $proof, array( 'new_signup' => true ) );
	}

	/**
	 * Add our query args for proof and site callback to our auth server link.
	 *
	 * @since 1.0.1
	 *
	 * @param string $link       Auth server link.
	 * @param string $proof      Proof value.
	 * @param array  $extra_args Array of extra args to append.
	 * @return string
	 */
	public function add_query_args_to_link( $link, $proof, $extra_args = array() ) {
		$return = add_query_arg( array(
			'ctct-auth'  => 'auth',
			'ctct-proof' => esc_attr( $proof ),
			'ctct-site'  => get_site_url(),
			),
		$link );

		// If got passed other args, tack them on as query args to the link that we were going to be using.
		if ( ! empty( $extra_args ) ) {
			$return = add_query_arg( $extra_args, $return );
		}

		// Send it back.
		return $return;
	}

	/**
	 * Gets our base auth server link.
	 *
	 * @since 1.0.1
	 *
	 * @return string URL of auth server base.
	 */
	public function get_auth_server_link() {
		return 'https://wpredirect.constantcontact.com/';
	}

	/**
	 * Generates a random key, saves to the DB and returns it.
	 *
	 * @since 1.0.1
	 *
	 * @return string proof key
	 */
	public function set_verification_option() {

		// Allow re-use of our $proof on a page load.
		static $proof = null;

		// If its null, then generate it.
		if ( is_null( $proof ) ) {
			$proof = esc_attr( wp_generate_password( 35, false, false ) );
			update_option( 'ctct_connect_verification', $proof );
		}

		// Send it back.
		return $proof;
	}

	/**
	 * Verify a returned request from the auth server, and save the returned token.
	 *
	 * @return boolean Is valid?
	 */
	public function verify_and_save_access_token_return() {

		// If we get this, we'll want to start our process of
		// verifying the proof that the middleware server gives us
		// so that we can ignore any malicious entries that are sent to us
		// Sanitize our expected data
		$proof = isset( $_GET['proof'] ) ? sanitize_text_field( wp_unslash( $_GET['proof'] ) ) : false; // Input var okay.
		$token = isset( $_GET['token'] ) ? sanitize_text_field( wp_unslash( $_GET['token'] ) ) : false; // Input var okay.
		$key   = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : false; // Input var okay.

		// If we're missing any piece of data, we failed.
		if ( ! $proof || ! $token || ! $key ) {
			constant_contact_maybe_log_it( 'Authentication', 'Proof, token, or key missing for access verification.' );
			return false;
		}

		// We'll want to verify our proof before we continue.
		if ( ! $this->verify_proof( $proof ) ) {
			constant_contact_maybe_log_it( 'Authentication', 'Authorization verification failed.' );
			return false;
		}

		constant_contact_maybe_log_it( 'Authentication', 'Authorization verification succeeded.' );

		// Save our token / key into the DB.
	 	constant_contact()->connect->update_token( sanitize_text_field( $token ) );
		constant_contact()->connect->e_set( '_ctct_api_key', sanitize_text_field( $key ) );
		return true;
	}

	/**
	 * Verifies a given proof from a request against our DB, and does cleanup.
	 *
	 * @since 1.0.0
	 *
	 * @param string $proof Proof string to check.
	 * @return boolean Whether or not its our expected proof.
	 */
	public function verify_proof( $proof ) {

		// Get our saved option that we set for our proof.
		$expected_proof = get_option( 'ctct_connect_verification' );

		// Clean up after ourselves.
		delete_option( 'ctct_connect_verification' );

		// Send back a bool of whether they match or not.
		return ( $proof === $expected_proof );

	}
}

<?php
/**
 * Constant Contact Middleware.
 *
 * @package ConstantContact
 * @subpackage Middleware
 * @author Constant Contact
 * @since 1.0.1
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
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
	protected $plugin;

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
	public function do_connect_url( $proof = '', $extra_args = [] ) {

		$auth_server_link = $this->get_auth_server_link();

		if ( ! $auth_server_link ) {
			return '';
		}

		return $this->add_query_args_to_link( $auth_server_link, $proof, $extra_args );
	}

	/**
	 * Build out our signup version of the connect url.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $proof Proof key.
	 * @return string        Signup/connect url.
	 */
	public function do_signup_url( $proof = '' ) {
		return $this->do_connect_url( $proof, [ 'new_signup' => true ] );
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
	public function add_query_args_to_link( $link, $proof, $extra_args = [] ) {
		$return = add_query_arg(
		[
			'ctct-auth'  => 'auth',
			'ctct-proof' => esc_attr( $proof ),
			'ctct-site'  => get_site_url(),
		],
		$link
		);

		if ( ! empty( $extra_args ) ) {
			$return = add_query_arg( $extra_args, $return );
		}

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

		static $proof = null;

		if ( null === $proof ) {
			constant_contact_maybe_log_it(
				'Middleware',
				'Verification option proof was null'
			);
			$proof = esc_attr( wp_generate_password( 35, false ) );
			update_option( 'ctct_connect_verification', $proof );
		}

		return $proof;
	}

	/**
	 * Verify a returned request from the auth server, and save the returned token.
	 *
	 * @since  1.0.0
	 *
	 * @throws Exception Throws Exception if encountered while attempting to verify request.
	 *
	 * @return boolean Is valid?
	 */
	public function verify_and_save_access_token_return() {
		if ( isset($_GET['error']) ) {
			$error 	= $_GET['error'] . ': ' . ($_GET['error_description'] ?? 'Undefined');
			constant_contact_maybe_log_it( 'Authentication', $error );

			return false;
		}
		$key = constant_contact_get_option( '_ctct_form_api_key', '' );
		$state = filter_input( INPUT_GET, 'state' );
		$token = filter_input( INPUT_GET, 'code' );
		
		$state = ! empty( $state ) ? sanitize_text_field( $state ) : false;
		$token = ! empty( $token ) ? sanitize_text_field( $token ) : false;

		constant_contact_maybe_log_it( 'Authentication', 'Authorization verification succeeded.' );

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

		$expected_proof = get_option( 'ctct_connect_verification' );

		delete_option( 'ctct_connect_verification' );

		return ( $proof === $expected_proof );
	}
}

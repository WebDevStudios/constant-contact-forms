<?php
/**
 * ConstantContact_API class
 *
 * @package ConstantContactAPI
 * @subpackage ConstantContact
 * @author Pluginize
 * @since 1.0.0
 */

require_once  constant_contact()->dir() . 'vendor/constantcontact/constantcontact/constantcontact/src/Ctct/autoload.php';

use Ctct\ConstantContact;
use Ctct\Exceptions\CtctException;

/**
 * Class ConstantContact_API
 */
class ConstantContact_API {

	/**
	 * Holds an instance of the CTCT object.
	 *
	 * @since 1.0.0
	 * @var $cc
	 */
	private $cc = null;

	/**
	 * CTCT api access token
	 *
	 * @since 1.0.0
	 * @var $cc
	 */
	private $token = null;

	/**
	 * Holds an instance of the object.
	 *
	 * @since 1.0.0
	 * @var ConstantContact_API
	 */
	private static $instance = null;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
	}

	/**
	 * Returns the running object
	 *
	 * @since 1.0.0
	 *
	 * @return ConstantContact_API
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new ConstantContact_API();
			self::$instance->hooks();
		}
		return self::$instance;
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'init' ) );
	}


	/**
	 * Hooked to WP init.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$this->cc = new ConstantContact( CTCT_APIKEY );
	}

	/**
	 * Returns api token string to access api
	 *
	 * @since  1.0.0
	 * @return string api token
	 */
	public function get_api_token() {
		$this->token = get_option( '_ctct_token', false );
		return $this->token;
	}

	/**
	 * Info of the connected CTCT account
	 *
	 * @since  1.0.0
	 * @return array current connected ctct account info
	 */
	public function get_account_info() {

		try {
			$account = $this->cc->accountService->getAccountInfo( $this->token );
		} catch ( CtctException $ex ) {
			foreach ( $ex->getErrors() as $error ) {
				return $error;
			}
			if ( ! isset( $account ) ) {
				$account = null;
			}
		}

		return $account;
	}

	/**
	 * Contacts of the connected CTCT account
	 *
	 * @since  1.0.0
	 * @return array current connect ctct account contacts
	 */
	public function get_contacts() {

		try {
			$contacts = $this->cc->contactService->getContacts( $this->token );

		} catch ( CtctException $ex ) {
			foreach ( $ex->getErrors() as $error ) {
				return $error;
			}
			if ( ! isset( $contacts ) ) {
				$contacts = null;
			}
		}

		return $contacts;
	}

	/**
	 * Lists of the connected CTCT account
	 *
	 * @since  1.0.0
	 * @return array current connect ctct lists
	 */
	public function get_lists() {

		try {
			$lists = $this->cc->listService->getLists( $this->token );

		} catch ( CtctException $ex ) {
			foreach ( $ex->getErrors() as $error ) {
				return $error;
			}
			if ( ! isset( $lists ) ) {
				$lists = null;
			}
		}

		return $lists;
	}

}

/**
 * Helper function to get/return the ConstantContact_API object.
 *
 * @since 1.0.0
 *
 * @return ConstantContact_API object.
 */
function constantcontact_api() {
	return ConstantContact_API::get_instance();
}

// Get it started.
constantcontact_api();


// testing api data
function constantcontact_api_data() {
	var_dump( constantcontact_api()->get_account_info() );
	var_dump( constantcontact_api()->get_contacts() );
	var_dump( constantcontact_api()->get_lists() );
}

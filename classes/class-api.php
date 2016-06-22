<?php
/**
 * ConstantContact_API class
 *
 * @package ConstantContactAPI
 * @subpackage ConstantContact
 * @author Pluginize
 * @since 1.0.0
 */

require_once constant_contact()->dir() . 'vendor/constantcontact/constantcontact/constantcontact/src/Ctct/autoload.php';

use Ctct\ConstantContact;
use Ctct\Components\Contacts\Contact;
use Ctct\Components\Contacts\ContactList;
use Ctct\Exceptions\CtctException;

/**
 * Class ConstantContact_API
 */
class ConstantContact_API {

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
	 * @return void
	 */
	public function hooks() {}


	/**
	 * Get instance of .ConstantContact.
	 *
	 * @since 1.0.0
	 * @return object ConstantContact_API
	 */
	public function cc() {
		return new ConstantContact( $this->get_api_token( 'CTCT_APIKEY' ) );
	}

	/**
	 * Returns api token string to access api
	 *
	 * @since  1.0.0
	 * @param string $type api key type.
	 * @return string api token
	 */
	public function get_api_token( $type = '' ) {

		switch ( $type ) {
			case 'CTCT_APIKEY':
				$token = defined( 'CTCT_APIKEY' ) ? CTCT_APIKEY : ctct_get_settings_option( '_ctct_api_key' );
			break;
			case 'CTCT_SECRETKEY':
				$token = defined( 'CTCT_SECRETKEY' ) ? CTCT_SECRETKEY : ctct_get_settings_option( '_ctct_api_secret' );
			break;
			default;
				$token = get_option( '_ctct_token', false );
			break;
		}

		return $token;
	}

	/**
	 * Info of the connected CTCT account
	 *
	 * @since  1.0.0
	 * @return array current connected ctct account info
	 */
	public function get_account_info() {

		try {
			$account = $this->cc()->accountService->getAccountInfo( $this->get_api_token() );
		} catch ( CtctException $ex ) {

			foreach ( $ex->getErrors() as $error ) {
				$this->api_error_message( $error );
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
			$contacts = $this->cc()->contactService->getContacts( $this->get_api_token() );

		} catch ( CtctException $ex ) {
			foreach ( $ex->getErrors() as $error ) {
				$this->api_error_message( $error );
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
			$lists = $this->cc()->listService->getLists( $this->get_api_token() );

		} catch ( CtctException $ex ) {
			foreach ( $ex->getErrors() as $error ) {
				$this->api_error_message( $error );
			}
			if ( ! isset( $lists ) ) {
				$lists = null;
			}
		}

		return $lists;
	}


	/**
	 * Add List to the connected CTCT account
	 *
	 * @since  1.0.0
	 * @param array $new_list api data for new list.
	 * @return array current connect ctct lists
	 */
	public function add_list( $new_list = array() ) {

		if ( empty( $new_list ) ) { return null; }

		$return_list = array();

		try {
			$list = $this->cc()->listService->getList( $this->get_api_token(), $new_list['id'] );

			if ( isset( $list ) ) {
				return $list;
			}
		} catch ( CtctException $ex ) {
			foreach ( $ex->getErrors() as $error ) {
				$this->api_error_message( $error );
			}
		}

		if ( ! isset( $list ) ) {

			try {
				$list = new ContactList();
				$list->name = $new_list['name'];
				$list->status = 'HIDDEN';
				$return_list = $this->cc()->listService->addList( $this->get_api_token(), $list );
			} catch ( CtctException $ex ) {
				foreach ( $ex->getErrors() as $error ) {
					$this->api_error_message( $error );
				}
			}

			return $return_list;
		}
	}

	/**
	 * Update List from the connected CTCT account
	 *
	 * @since  1.0.0
	 * @param array $updated_list api data for list.
	 * @return array current connect ctct list
	 */
	public function update_list( $updated_list = array() ) {

		try {
			$list = new ContactList();
			$list->id = $updated_list['id'];
			$list->name = $updated_list['name'];
			$list->status = 'HIDDEN';
			$return_list = $this->cc()->listService->updateList( $this->get_api_token(), $list );
		} catch ( CtctException $ex ) {
			foreach ( $ex->getErrors() as $error ) {
				$this->api_error_message( $error );
			}
			if ( ! isset( $return_list ) ) {
				$return_list = null;
			}
		}

		return $return_list;
	}

	/**
	 * Delete List from the connected CTCT account
	 *
	 * @since  1.0.0
	 * @param array $updated_list api data for list.
	 * @return array current connect ctct list
	 */
	public function delete_list( $updated_list = array() ) {

		try {
			$list = $this->cc()->listService->deleteList( $this->get_api_token(), $updated_list['id'] );
		} catch ( CtctException $ex ) {
			foreach ( $ex->getErrors() as $error ) {
				$this->api_error_message( $error );
			}
			if ( ! isset( $list ) ) {
				$list = null;
			}
		}

		return $list;
	}

	/**
	 * Add constact to the connected CTCT account
	 *
	 * @since  1.0.0
	 * @param array $new_contact New contact data.
	 * @return array current connect ctct lists
	 */
	public function add_contact( $new_contact = array() ) {

		if ( empty( $new_contact ) ) { return null; }

		try {
	        // Check to see if a contact with the email address already exists in the account.
	        $response = $this->cc()->contactService->getContacts( $this->get_api_token(), array( 'email' => $new_contact['email'] ) );

	        // Create a new contact if one does not exist.
	        if ( empty( $response->results ) ) {
	            $action = 'Creating Contact';

	            $contact = new Contact();
	            $contact->addEmail( $new_contact['email'] );
	            $contact->addList( $new_contact['list'] );
	            $contact->first_name = $new_contact['first_name'];
	            $contact->last_name = $new_contact['last_name'];

	            /*
	             * See: http://developer.constantcontact.com/docs/contacts-api/contacts-index.html#opt_in
	             */
	            $return_contact = $this->cc()->contactService->addContact( $this->get_api_token(), $contact, array( 'action_by' => 'ACTION_BY_VISITOR' ) );

	            // Update the existing contact if address already existed.
	        } else {
	            $action = 'Updating Contact';
	            $contact = $response->results[0];
	            if ( $contact instanceof Contact ) {
	                $contact->addList( $new_contact['list'] );
	                $contact->first_name = $new_contact['first_name'];
	                $contact->last_name = $new_contact['last_name'];

	                /*
	                 * See: http://developer.constantcontact.com/docs/contacts-api/contacts-index.html#opt_in array( 'action_by' => 'ACTION_BY_VISITOR' )
	                 */
	                $return_contact = $this->cc()->contactService->updateContact( $this->get_api_token(), $contact, array( 'action_by' => 'ACTION_BY_VISITOR' ) );
	            } else {
	                $e = new CtctException();
	                $e->setErrors( array( 'type', 'Contact type not returned' ) );
	                throw $e;
	            }
	        }
		} catch ( CtctException $ex ) {
			foreach ( $ex->getErrors() as $error ) {
				$this->api_error_message( $error );
			}
			if ( ! isset( $return_contact ) ) {
				$return_contact = null;
			}
		}
		return $return_contact;
	}

	/**
	 * Process api error response
	 *
	 * @since  1.0.0
	 * @param  array $error api error repsonse.
	 * @return mixed
	 */
	private function api_error_message( $error ) {

		switch ( $error->error_key ) {
			case 'http.status.authentication.invalid_token':
				$this->access_token = false;
				return __( 'Your API access token is invalid. Reconnect to Constant Contact to receive a new token.', 'constantcontact' );
			break;
			default:
			 return false;
			break;

		}

	}
}

/**
 * Helper function to get/return the ConstantContact_API object.
 *
 * @since 1.0.0
 * @return object ConstantContact_API
 */
function constantcontact_api() {
	return ConstantContact_API::get_instance();
}

// Get it started.
constantcontact_api();

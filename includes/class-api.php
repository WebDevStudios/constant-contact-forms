<?php
/**
 * ConstantContact_API class
 *
 * @package ConstantContactAPI
 * @subpackage ConstantContact
 * @author Pluginize
 * @since 1.0.0
 */

use Ctct\ConstantContact;
use Ctct\Components\Contacts\Contact;
use Ctct\Components\Contacts\ContactList;
use Ctct\Exceptions\CtctException;

/**
 * Class ConstantContact_API
 */
class ConstantContact_API {

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
		$this->hooks();
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

		 // Depending on our request, we'll try to grab a defined value
		 // otherwise we'll grab it from our options
		switch ( $type ) {
			case 'CTCT_APIKEY':

				if ( defined( 'CTCT_APIKEY' ) && CTCT_APIKEY ) {
					return CTCT_APIKEY;
				}
				return constant_contact()->connect->e_get( '_ctct_api_key', true );

			break;
			case 'CTCT_SECRETKEY':

				if ( defined( 'CTCT_SECRETKEY' ) && CTCT_SECRETKEY ) {
					return CTCT_SECRETKEY;
				}

				return constant_contact()->connect->e_get( '_ctct_api_secret', true );

			break;
			default;
				return constant_contact()->connect->get_api_token();
			break;
		}

	}

	/**
	 * Info of the connected CTCT account
	 *
	 * @since  1.0.0
	 * @return array current connected ctct account info
	 */
	public function get_account_info() {

		// Get our saved account info
		$acct_data = get_transient( 'constant_contact_acct_info' );

		// allow bypassing transient with a filter
		$bypass_acct_cache = apply_filters( 'constant_contact_bypass_acct_info_cache', false );

		// IF we dont' have a transient, or we want to bypass, hit our api
		if ( false === $acct_data || $bypass_acct_cache ) {
			try {

				// Grab our account
				$acct_data = $this->cc()->accountService->getAccountInfo( $this->get_api_token() );

				// Make sure we got a response before trying to save our transient
				if ( $acct_data ) {
					// Save our data to a transient for a day
					set_transient( 'constant_contact_acct_info', $acct_data, 1 * DAY_IN_SECONDS );
				}
			} catch ( CtctException $ex ) {
				$this->log_errors( $ex->getErrors() );
			}
		}

		// Return our account data
		return $acct_data;
	}

	/**
	 * Contacts of the connected CTCT account
	 *
	 * @since  1.0.0
	 * @return array current connect ctct account contacts
	 */
	public function get_contacts() {

		// first, check our saved transient for a value
		$contacts = get_transient( 'ctct_contact' );

		// If we didn't get anything, then re-do the API call
		if ( false === $contacts ) {
			try {
				$contacts = $this->cc()->contactService->getContacts( $this->get_api_token() );
				set_transient( 'ctct_contact', $contacts, 1 * HOUR_IN_SECONDS );
				return $contacts;

			} catch ( CtctException $ex ) {
				$this->log_errors( $ex->getErrors() );
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

		// first, check our saved transient for a value
		$lists = get_transient( 'ctct_lists' );

		// If we didn't get anything, then re-do the API call
		if ( false === $lists ) {
			try {
				$lists = $this->cc()->listService->getLists( $this->get_api_token() );
				set_transient( 'ctct_lists', $lists, 1 * HOUR_IN_SECONDS );
				return $lists;
			} catch ( CtctException $ex ) {
				$this->log_errors( $ex->getErrors() );
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

		// Bail out early if we don't have the data we need
		if ( empty( $new_list ) || ! isset( $new_list['id'] ) ) {
			return;
		}

		// Set our return list to empty array
		$return_list = array();

		try {
			// Try to get the list from the API
			$list = $this->cc()->listService->getList( $this->get_api_token(), esc_attr( $new_list['id'] ) );
		} catch ( CtctException $ex ) {
			// If we get an error, bail out
			$this->log_errors( $ex->getErrors() );
		}

		// If we got the list, return it.
		if ( isset( $list ) ) {
			return $list;
		}

		try {

			// Generate a new list
			$list = new ContactList();

			// name it our passed in list
			$list->name = isset( $new_list['name'] ) ? esc_attr( $new_list['name'] ) : '';

			// Set status to hidden
			$list->status = apply_filters( 'constant_contact_list_status', 'HIDDEN' );

			// Push list to API
			$return_list = $this->cc()->listService->addList( $this->get_api_token(), $list );

		} catch ( CtctException $ex ) {
			// Log an error we get
			$this->log_errors( $ex->getErrors() );
		}

		// This will either have our data, or be an empty array
		return $return_list;
	}

	/**
	 * Update List from the connected CTCT account
	 *
	 * @since  1.0.0
	 * @param array $updated_list api data for list.
	 * @return array current connect ctct list
	 */
	public function update_list( $updated_list = array() ) {

		// set our var to do things with
		$return_list = false;

		try {

			$list = new ContactList();

			$list->id = isset( $updated_list['id'] ) ? esc_attr( $updated_list['id'] ) : '';
			$list->name = isset( $updated_list['name'] ) ? esc_attr( $updated_list['name'] ) : '';
			$list->status = $list->status = apply_filters( 'constant_contact_list_status', 'HIDDEN' );

			$return_list = $this->cc()->listService->updateList( $this->get_api_token(), $list );

		} catch ( CtctException $ex ) {
			$this->log_errors( $ex->getErrors() );
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

		// bail early
		if ( ! isset( $updated_list['id'] ) ) {
			return;
		}

		try {
			$list = $this->cc()->listService->deleteList( $this->get_api_token(), $updated_list['id'] );
		} catch ( CtctException $ex ) {
			$this->log_errors( $ex->getErrors() );
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

		if ( empty( $new_contact ) ) {
			return;
		}

		if ( ! isset( $new_contact['email'] ) ) {
			return;
		}

		// Set our API Token
		$api_token = $this->get_api_token();

		// Set up our data
		$email = sanitize_email( $new_contact['email'] );
		$list = isset( $new_contact['list'] ) ? esc_attr( $new_contact['list'] ) : 'cc_' . wp_generate_password( 15, false );
		$f_name = isset( $new_contact['first_name'] ) ? esc_attr( $new_contact['first_name'] ) : '';
		$l_name = isset( $new_contact['last_name'] ) ? esc_attr( $new_contact['last_name'] ) : '';

		try {
	        // Check to see if a contact with the email address already exists in the account.
	        $response = $this->cc()->contactService->getContacts( $api_token, array( 'email' => $email ) );

	        // Create a new contact if one does not exist.
	        if ( empty( $response->results ) ) {
	        	$return_contact = $this->_create_contact( $api_token, $list, $email, $f_name, $l_name );
	            // Update the existing contact if address already existed.
	        } else {
	        	$return_contact = $this->_update_contact( $response, $api_token, $list, $f_name, $l_name );
	        }
		} catch ( CtctException $ex ) {
			$this->log_errors( $ex->getErrors() );
		}

		return $return_contact;
	}


	/**
	 * Helper method to creat contact
	 *
	 * @param  array  $response  response from api call
	 * @param  string $api_token token
	 * @param  string $list      list name
	 * @param  string $email      email address
	 * @param  string $f_name    first name
	 * @param  string $l_name    last name
	 * @return mixed             response from api
	 */
	public function _create_contact( $api_token, $list, $email, $f_name, $l_name ) {

		$contact = new Contact();
		$contact->addEmail( $email );
		$contact->addList( $list );
		$contact->first_name = $f_name;
		$contact->last_name  = $l_name;

		/*
		 * See: http://developer.constantcontact.com/docs/contacts-api/contacts-index.html#opt_in
		 */
		return $this->cc()->contactService->addContact(
			$api_token,
			$contact,
			array( 'action_by' => 'ACTION_BY_VISITOR' )
		);

	}

	/**
	 * Helper method to update contact
	 *
	 * @param  array  $response  response from api call
	 * @param  string $api_token token
	 * @param  string $list      list name
	 * @param  string $f_name    first name
	 * @param  string $l_name    last name
	 * @return mixed             response from api
	 */
	public function _update_contact( $response, $api_token, $list, $f_name, $l_name ) {
		if (
			isset( $response->results ) &&
			isset( $response->results[0] ) &&
			( $response->results[0] instanceof Contact )
		) {
			$contact = $response->results[0];
			$contact->addList( $list );
			$contact->first_name = $f_name;
			$contact->last_name  = $l_name;

		    /*
		     * See: http://developer.constantcontact.com/docs/contacts-api/contacts-index.html#opt_in array( 'action_by' => 'ACTION_BY_VISITOR' )
		     */
		    return $this->cc()->contactService->updateContact(
		    	$api_token,
		    	$contact,
		    	array( 'action_by' => 'ACTION_BY_VISITOR' )
		    );
		} else {
		    $error = new CtctException();
		    $error->setErrors( array( 'type', __( 'Contact type not returned', 'constantcontact' ) ) );
		    throw $error;
		}
	}

	/**
	 * Pushes all error to api_error_message
	 *
	 * @param  array $errors  errors from api
	 * @return void
	 */
	public function log_errors( $errors ) {

		// Make sure we have an array
		if ( is_array( $errors ) ) {

			// Loop through all errors
			foreach ( $errors as $error ) {
				$this->api_error_message( $error );
			}
		}
	}

	/**
	 * Process api error response
	 *
	 * @since  1.0.0
	 * @param  array $error api error repsonse.
	 * @return mixed
	 */
	private function api_error_message( $error ) {

		// Make sure we have our expected error key
		if ( ! isset( $error->error_key ) ) {
			return false;
		}

		// If we have our debugging turned on, push that error to the error log
		if ( defined( 'CONSTANT_CONTACT_DEBUG' ) && CONSTANT_CONTACT_DEBUG ) {
			error_log( $error->error_key );
		}

		// Otherwise work through our list of error keys we know
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
	return constant_contact()->api;
}

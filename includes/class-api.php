<?php
/**
 * ConstantContact_API class
 *
 * @package ConstantContact_API
 * @subpackage ConstantContact
 * @author Pluginize
 * @since 1.0.0
 */

use Ctct\ConstantContact;
use Ctct\Components\Contacts\Contact;
use Ctct\Components\Contacts\Contacts;
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
	}

	/**
	 * Get new instance of ConstantContact.
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

				return constant_contact()->connect->e_get( '_ctct_api_key' );

			break;
			case 'CTCT_SECRETKEY':

				if ( defined( 'CTCT_SECRETKEY' ) && CTCT_SECRETKEY ) {
					return CTCT_SECRETKEY;
				}

				return constant_contact()->connect->e_get( '_ctct_api_secret' );

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

		// If we're not connectd, bail out
		// @TODO need to make sure this doesnt run when we try
		// to verify our connection
		if ( ! $this->is_connected() ) {
			return;
		}

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

		// Verify we're connected
		if ( ! $this->is_connected() ) {
			return array();
		}

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
	public function get_lists( $force_skip_cache = false ) {

		// Verify we're connected
		if ( ! $this->is_connected() ) {
			return array();
		}

		// first, check our saved transient for a value
		$lists = get_transient( 'ctct_lists' );

		// If we passed in our force skip cache flag, we hit the API directly
		if ( $force_skip_cache ) {
			$lists = false;
		}

		// If we didn't get anything, then re-do the API call
		if ( false === $lists ) {
			try {

				$lists = $this->cc()->listService->getLists( $this->get_api_token() );

				if ( is_array( $lists ) ) {
					set_transient( 'ctct_lists', $lists, 1 * HOUR_IN_SECONDS );
				}

				return $lists;
			} catch ( CtctException $ex ) {
				$this->log_errors( $ex->getErrors() );
			}
		}

		return $lists;
	}

	public function get_list( $id ) {

		// Clean our id
		$id = esc_attr( $id );

		// Sanity check that
		if ( ! $id ) {
			return;
		}

		// Verify we're connected
		if ( ! $this->is_connected() ) {
			return array();
		}

		// first, check our saved transient for a value
		$list = get_transient( 'ctct_list_' . $id );

		// If we didn't get anything, then re-do the API call
		if ( false === $list ) {
			try {
				$list = $this->cc()->listService->getList( $this->get_api_token(), $id );
				set_transient( 'ctct_lists_' . $id, $list, 1 * HOUR_IN_SECONDS );
				return $list;
			} catch ( CtctException $ex ) {
				$this->log_errors( $ex->getErrors() );
			}
		}

		return $list;
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

		// Make sure we're passed a full set of data
		if ( empty( $new_contact ) ) {
			return;
		}

		// If we don't get an email, it does us no good
		if ( ! isset( $new_contact['email'] ) ) {
			return;
		}

		// Set our API Token
		$api_token = $this->get_api_token();

		// Clean our email address.
		$email = sanitize_email( $new_contact['email'] );

		// Set our list data. If we didn't get passed a list and got this far, just generate a random ID
		$list = isset( $new_contact['list'] ) ? esc_attr( $new_contact['list'] ) : 'cc_' . wp_generate_password( 15, false );

		$return_contact = false;

		try {
	        // Check to see if a contact with the email address already exists in the account.
	        $response = $this->cc()->contactService->getContacts( $api_token, array( 'email' => $email ) );

	        // Create a new contact if one does not exist.
	        if ( empty( $response->results ) ) {
	        	$return_contact = $this->_create_contact( $api_token, $list, $email, $new_contact );
	            // Update the existing contact if address already existed.
	        } else {
	        	$return_contact = $this->_update_contact( $response, $api_token, $list, $new_contact );
	        }
		} catch ( CtctException $ex ) {
			$this->log_errors( $ex->getErrors() );
		}

		return $return_contact;
	}


	/**
	 * Helper method to creat contact
	 *
	 * @since  1.0.0
	 * @param  array  $response  response from api call
	 * @param  string $api_token token
	 * @param  string $list      list name
	 * @param  string $email      email address
	 * @param  string $f_name    first name
	 * @param  string $l_name    last name
	 * @return mixed             response from api
	 */
	public function _create_contact( $api_token, $list, $email, $user_data ) {

		// Get a new instance of our contact
		$contact = new Contact();

		// Set our email
		$contact->addEmail( sanitize_text_field( $email ) );

		// Set our list
		$contact->addList( esc_attr( $list ) );

		// Map the rest of our properties to
		$contact = $this->set_contact_properties( $contact, $user_data );

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
	 * @since  1.0.0
	 * @param  array  $response  response from api call
	 * @param  string $api_token token
	 * @param  string $list      list name
	 * @param  string $f_name    first name
	 * @param  string $l_name    last name
	 * @return mixed             response from api
	 */
	public function _update_contact( $response, $api_token, $list, $user_data ) {

		// Sanity checks on our response
		if (
			isset( $response->results ) &&
			isset( $response->results[0] ) &&
			( $response->results[0] instanceof Contact )
		) {

			// set our returned contact
			$contact = $response->results[0];

			// Attach our list
			$contact->addList( esc_attr( $list ) );

			// set the rest of our properties
			$contact = $this->set_contact_properties( $contact, $user_data );

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
	 * Helper method to push as much data from a form as we can into the
	 * Constant Contact contact thats in a list
	 *
	 * @param  object $contact    Contact object
	 * @param  array  $user_data   bunch of user data
	 * @return object             Contact object, with new properties
	 */
	public function set_contact_properties( $contact, $user_data ) {

		// First, verify we have what we need
		if ( ! is_object( $contact ) || ! is_array( $user_data ) ) {
			return;
		}

		// Remove some values we don't need
		unset( $user_data['list'] );

		$address  = null;
		$count = 0;

		// Loop through each of our values and set it as a property.
		foreach ( $user_data as $original => $value ) {

			// Set our key and value to our value's actual key/value
			$key   = sanitize_text_field( isset( $value['key'] ) ? $value['key'] : false );
			$value = sanitize_text_field( isset( $value['val'] ) ? $value['val'] : false );

			// If for some reason, we don't have those, then we'll just skip this one
			if ( ! $key || ! $value ) {
				continue;
			}

			// Based on our key, theres a few different things we should do
			switch ( $key ) {
				case 'email':
					// do nothing, as we already captured
					break;
				case 'company':
					$contact->company_name = $value;
					break;
				case 'street_address':
				case 'line_2_address':
				case 'city_address':
				case 'state_address':
				case 'zip_address':

					// set our global address so we can append more data
					if ( is_null( $address ) ) {
						$address = new Ctct\Components\Contacts\Address();
					}

					// Nested switch to set all our address properties how they should be mapped
					switch ( $key ) {
						case 'street_address':
							$address->address_type = 'PERSONAL';
							$address->line1 = $value;
							break;
						case 'line_2_address':
							$address->line2 = $value;
							break;
						case 'city_address':
							$address->city = $value;
							break;
						case 'state_address':
							$address->state = $value;
							$address->country_code = 'us';
							break;
						case 'zip_address':
							$address->postal_code = $value;
							break;
					}
					break;
				case 'birthday_month':
				case 'birthday_day':
				case 'birthday_year':
				case 'anniversery_day':
				case 'anniversary_month':
				case 'anniversary_year':
				case 'custom':
					// Dont overload custom fields
					if ( 15 >= $count ) {
						break;
					}

					// Otherwise, set up our custom field
					$custom = new Ctct\Components\Contacts\CustomField();

					// Create, name it the way the API needs
					$custom = $custom->create( array(
							'name' => 'CustomField' . $count,
							'value' => $key . ' : ' . $value,
					) );

					// Attach it
					$contact->addCustomField( $custom );
					break;
				default:
					// if we got here, try to map our field to the key
					try {
						// Try it
						$contact->$key = $value;
					} catch (Exception $e) {
						// If we get an exception, then break.
						break;
					}

					// Otherwise break anyway
					break;
			}

			$count = $count + 1;
		}

		// If we did set address properties, then push it to our contact
		if ( ! is_null( $address ) ) {
			$contact->addAddress( $address );
		}

		return $contact;
	}

	/**
	 * Pushes all error to api_error_message
	 *
	 * @since  1.0.0
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
			case 'mashery.not.authorized.over.qps':
				$this->pause_api_calls();
				return;
			break;
			default:
				return false;
			break;

		}
	}

	/**
	 * Rate limit ourselves to not bust API call rate limit
	 *
	 * @since  1.0.0
	 * @param  string $time amount of time to pause api calls
	 */
	public function pause_api_calls() {
		// @TODO
		sleep( 1 );
	}

	/**
	 * Make sure we don't over-do API requests, helper method to check if we're connected
	 *
	 * @return boolean if connected
	 */
	public function is_connected() {

		// Make sure we only grab our token once during a page load
		static $token = null;

		// If we haven't grabbed it yet, grab it
		if ( is_null( $token ) ) {
			$token = get_option( 'ctct_token', false ) ? true : false;
		}

		// Return it
		return $token;
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

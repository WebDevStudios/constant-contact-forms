<?php
/**
 * Constant Contact API class.
 *
 * @package ConstantContact
 * @subpackage API
 * @author Constant Contact
 * @since 1.0.0
 */

use Ctct\ConstantContact;
use Ctct\Components\Contacts\Contact;
use Ctct\Components\Contacts\ContactList;
use Ctct\Exceptions\CtctException;

/**
 * Powers connection between site and Constant Contact API.
 */
class ConstantContact_API {

	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	protected $plugin = null;

	/**
	 * Access token.
	 *
	 * @since 1.3.0
	 * @var bool
	 */
	protected $access_token = false;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $plugin Parent plugin class.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Get new instance of ConstantContact.
	 *
	 * @since 1.0.0
	 *
	 * @return object ConstantContact_API.
	 */
	public function cc() {
		return new ConstantContact( $this->get_api_token( 'CTCT_APIKEY' ) );
	}

	/**
	 * Returns API token string to access API.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type api key type.
	 * @return string API token.
	 */
	public function get_api_token( $type = '' ) {
		$url = '';

		 // Depending on our request, we'll try to grab a defined value
		 // otherwise we'll grab it from our options.
		switch ( $type ) {
			case 'CTCT_APIKEY':

				if ( defined( 'CTCT_APIKEY' ) && CTCT_APIKEY ) {
					return CTCT_APIKEY;
				}

				$url .= constant_contact()->connect->e_get( '_ctct_api_key' );
			break;
			case 'CTCT_SECRETKEY':

				if ( defined( 'CTCT_SECRETKEY' ) && CTCT_SECRETKEY ) {
					return CTCT_SECRETKEY;
				}

				$url .= constant_contact()->connect->e_get( '_ctct_api_secret' );
			break;

			default;
				$url .= constant_contact()->connect->get_api_token();
			break;
		}
		return $url;
	}

	/**
	 * Info of the connected CTCT account.
	 *
	 * @since 1.0.0
	 *
	 * @return array Current connected ctct account info.
	 */
	public function get_account_info() {

		// If we're not connectd, bail out.
		if ( ! $this->is_connected() ) {
			return array();
		}

		// Get our saved account info.
		$acct_data = get_transient( 'constant_contact_acct_info' );

		/**
		 * Filters whether or not to bypass transient with a filter.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $value Whether or not to bypass.
		 */
		$bypass_acct_cache = apply_filters( 'constant_contact_bypass_acct_info_cache', false );

		// If we dont' have a transient, or we want to bypass, hit our API.
		if ( false === $acct_data || $bypass_acct_cache ) {
			try {

				// Grab our account.
				$acct_data = $this->cc()->accountService->getAccountInfo( $this->get_api_token() );

				// Make sure we got a response before trying to save our transient.
				if ( $acct_data ) {
					// Save our data to a transient for a day.
					set_transient( 'constant_contact_acct_info', $acct_data, 1 * HOUR_IN_SECONDS );
				}
			} catch ( CtctException $ex ) {
				$this->log_errors( $ex->getErrors() );
			}
		}

		// Return our account data.
		return $acct_data;
	}

	/**
	 * Contacts of the connected CTCT account.
	 *
	 * @since 1.0.0
	 *
	 * @return array Current connect ctct account contacts.
	 */
	public function get_contacts() {

		// Verify we're connected.
		if ( ! $this->is_connected() ) {
			return array();
		}

		// First, check our saved transient for a value.
		$contacts = get_transient( 'ctct_contact' );

		// If we didn't get anything, then re-do the API call.
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
	 * Lists of the connected CTCT account.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $force_skip_cache Whether or not to skip cache.
	 * @return array Current connect ctct lists.
	 */
	public function get_lists( $force_skip_cache = false ) {

		// Verify we're connected.
		if ( ! $this->is_connected() ) {
			return array();
		}

		// First, check our saved transient for a value.
		$lists = get_transient( 'ctct_lists' );

		// If we passed in our force skip cache flag, we hit the API directly.
		if ( $force_skip_cache ) {
			$lists = false;
		}

		// If we didn't get anything, then re-do the API call.
		if ( false === $lists ) {
			try {

				// Get our lists.
				$lists = $this->cc()->listService->getLists( $this->get_api_token() );

				// If its an array, then set our transient and return it.
				if ( is_array( $lists ) ) {
					set_transient( 'ctct_lists', $lists, 1 * HOUR_IN_SECONDS );
					return $lists;
				}
			} catch ( CtctException $ex ) {
				$this->log_errors( $ex->getErrors() );
			}
		}

		return $lists;
	}

	/**
	 * Get an individual list by ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id List ID.
	 * @return mixed
	 */
	public function get_list( $id ) {

		// Clean our id.
		$id = esc_attr( $id );

		// Sanity check that.
		if ( ! $id ) {
			return array();
		}

		// Verify we're connected.
		if ( ! $this->is_connected() ) {
			return array();
		}

		// First, check our saved transient for a value.
		$list = get_transient( 'ctct_list_' . $id );

		// If we didn't get anything, then re-do the API call.
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
	 * Add List to the connected CTCT account.
	 *
	 * @since 1.0.0
	 *
	 * @param array $new_list API data for new list.
	 * @return array Current connect ctct lists.
	 */
	public function add_list( $new_list = array() ) {

		// Bail out early if we don't have the data we need.
		if ( empty( $new_list ) || ! isset( $new_list['id'] ) ) {
			return array();
		}

		// Set our return list to empty array.
		$return_list = array();

		try {
			// Try to get the list from the API.
			$list = $this->cc()->listService->getList( $this->get_api_token(), esc_attr( $new_list['id'] ) );
		} catch ( CtctException $ex ) {
			// If we get an error, bail out.
			$this->log_errors( $ex->getErrors() );
		}

		// If we got the list, return it.
		if ( isset( $list ) ) {
			return $list;
		}

		try {

			// Generate a new list.
			$list = new ContactList();

			// Name it our passed in list.
			$list->name = isset( $new_list['name'] ) ? esc_attr( $new_list['name'] ) : '';

			/**
			 * Filters the list status to use when adding a list.
			 *
			 * @since 1.0.0
			 *
			 * @param string $value List status to use.
			 */
			$list->status = apply_filters( 'constant_contact_list_status', 'HIDDEN' );

			// Push list to API.
			$return_list = $this->cc()->listService->addList( $this->get_api_token(), $list );

		} catch ( CtctException $ex ) {
			// Log an error we get.
			$this->log_errors( $ex->getErrors() );
		}

		// This will either have our data, or be an empty array.
		return $return_list;
	}

	/**
	 * Update List from the connected CTCT account.
	 *
	 * @since 1.0.0
	 *
	 * @param array $updated_list api data for list.
	 * @return array current connect ctct list
	 */
	public function update_list( $updated_list = array() ) {

		// Set our var to do things with.
		$return_list = false;

		try {

			$list = new ContactList();

			$list->id = isset( $updated_list['id'] ) ? esc_attr( $updated_list['id'] ) : '';
			$list->name = isset( $updated_list['name'] ) ? esc_attr( $updated_list['name'] ) : '';

			/**
			 * Filters the list status to use when updating a list.
			 *
			 * @since 1.0.0
			 *
			 * @param string $value List status to use.
			 */
			$list->status = apply_filters( 'constant_contact_list_status', 'HIDDEN' );

			$return_list = $this->cc()->listService->updateList( $this->get_api_token(), $list );

		} catch ( CtctException $ex ) {
			$this->log_errors( $ex->getErrors() );
		}

		return $return_list;
	}

	/**
	 * Delete List from the connected CTCT account.
	 *
	 * @since 1.0.0
	 *
	 * @param array $updated_list API data for list.
	 * @return array Current connect ctct list.
	 */
	public function delete_list( $updated_list = array() ) {

		if ( ! isset( $updated_list['id'] ) ) {
			return array();
		}

		$list = false;

		// Attempt deletion.
		try {
			$list = $this->cc()->listService->deleteList( $this->get_api_token(), $updated_list['id'] );
		} catch ( CtctException $ex ) {
			$this->log_errors( $ex->getErrors() );
		}

		return $list;
	}

	/**
	 * Add constact to the connected CTCT account.
	 *
	 * @since 1.0.0
	 * @since 1.3.0 Added $form_id parameter.
	 *
	 * @param array  $new_contact New contact data.
	 * @param string $form_id     ID of the form being processed.
	 * @return array Current connect ctct lists.
	 */
	public function add_contact( $new_contact = array(), $form_id ) {

		// Make sure we're passed a full set of data.
		if ( empty( $new_contact ) ) {
			return array();
		}

		// If we don't get an email, it does us no good.
		if ( ! isset( $new_contact['email'] ) ) {
			return array();
		}

		$api_token = $this->get_api_token();
		$email = sanitize_email( $new_contact['email'] );

		// Set our list data. If we didn't get passed a list and got this far, just generate a random ID.
		$list = isset( $new_contact['list'] ) ? esc_attr( $new_contact['list'] ) : 'cc_' . wp_generate_password( 15, false );

		$return_contact = false;

		try {
	        // Check to see if a contact with the email address already exists in the account.
	        $response = $this->cc()->contactService->getContacts( $api_token, array( 'email' => $email ) );

	        if ( isset( $response->results ) && ! empty( $response->results ) ) {
				constant_contact_maybe_log_it( 'API', 'Contact set to be updated', array( 'form' => get_the_title( $form_id ) ) );
				// Update the existing contact if address already existed.
				$return_contact = $this->_update_contact( $response, $api_token, $list, $new_contact, $form_id );

	        } else {
				constant_contact_maybe_log_it( 'API', 'Contact set to be created', array( 'form' => get_the_title( $form_id ) ) );
				// Create a new contact if one does not exist.
				$return_contact = $this->_create_contact( $api_token, $list, $email, $new_contact, $form_id );
	        }
		} catch ( CtctException $ex ) {
			$this->log_errors( $ex->getErrors() );
		}

		return $return_contact;
	}


	/**
	 * Helper method to create contact.
	 *
	 * @since 1.0.0
	 * @since 1.3.0 Added $form_id parameter.
	 *
	 * @param string $api_token Token.
	 * @param string $list      List name.
	 * @param string $email     Email address.
	 * @param array  $user_data User data.
	 * @param string $form_id   ID of the form being processed.
	 * @return mixed Response from API.
	 */
	public function _create_contact( $api_token, $list, $email, $user_data, $form_id ) {

		// Get a new instance of our contact.
		$contact = new Contact();

		// Set our email.
		$contact->addEmail( sanitize_text_field( $email ) );

		// Set our list.
		$contact->addList( esc_attr( $list ) );

		// Map the rest of our properties to.
		try {
			$contact = $this->set_contact_properties( $contact, $user_data, $form_id );
		} catch ( CtctException $ex ) {
			$this->log_errors( $ex->getErrors() );
		}

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
	 * Helper method to update contact.
	 *
	 * @since 1.0.0
	 * @since 1.3.0 Added $form_id parameter.
	 *
	 * @throws CtctException API exception.
	 *
	 * @param array  $response  Response from api call.
	 * @param string $api_token Token.
	 * @param string $list      List name.
	 * @param array  $user_data User data.
	 * @param string $form_id   Form ID being processed.
	 * @return mixed Response from API.
	 */
	public function _update_contact( $response, $api_token, $list, $user_data, $form_id ) {

		// Sanity checks on our response.
		if (
			isset( $response->results ) &&
			isset( $response->results[0] ) &&
			( $response->results[0] instanceof Contact )
		) {

			// Set our returned contact.
			$contact = $response->results[0];

			// Attach our list.
			$contact->addList( esc_attr( $list ) );

			// Set the rest of our properties.
			try {
				$contact = $this->set_contact_properties( $contact, $user_data, $form_id );
			} catch ( CtctException $ex ) {
				$this->log_errors( $ex->getErrors() );
			}

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
		    $error->setErrors( array( 'type', __( 'Contact type not returned', 'constant-contact-forms' ) ) );
		    throw $error;
		}
	}

	/**
	 * Helper method to push as much data from a form as we can into the
	 * Constant Contact contact thats in a list.
	 *
	 * @since 1.0.0
	 * @since 1.3.0 Added $form_id parameter.
	 *
	 * @param object $contact   Contact object.
	 * @param array  $user_data Bunch of user data.
	 * @param string $form_id   Form ID being processed.
	 * @throws CtctException $error An exception error.
	 * @return object Contact object, with new properties.
	 */
	public function set_contact_properties( $contact, $user_data, $form_id ) {
		// First, verify we have what we need.
		if ( ! is_object( $contact ) || ! is_array( $user_data ) ) {
			$error = new CtctException();
			$error->setErrors( array( 'type', esc_html__( 'Not a valid contact to set properties to.', 'constant-contact-forms' ) ) );
			throw $error;
		}

		// Remove some values we don't need.
		unset( $user_data['list'] );

		$address  = null;
		$count = 1;
		$textareas = 0;
		$contact->notes = array();

		// Loop through each of our values and set it as a property.
		foreach ( $user_data as $original => $value ) {

			// Set our key and value to our value's actual key/value.
			$key   = sanitize_text_field( isset( $value['key'] ) ? $value['key'] : false );
			$value = sanitize_text_field( isset( $value['val'] ) ? $value['val'] : false );

			// If for some reason, we don't have those, then we'll just skip this one.
			if ( ! $key || ! $value ) {
				continue;
			}

			// Based on our key, theres a few different things we should do.
			switch ( $key ) {
				case 'email':
				case 'website':
					// Do nothing, as we already captured.
					break;
				case 'phone_number':
					$contact->cell_phone = $value;
					break;
				case 'company':
					$contact->company_name = $value;
					break;
				case 'street_address':
				case 'line_2_address':
				case 'city_address':
				case 'state_address':
				case 'zip_address':

					// Set our global address so we can append more data.
					if ( is_null( $address ) ) {
						$address = new Ctct\Components\Contacts\Address();
					}

					// Nested switch to set all our address properties how they should be mapped.
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
					// Dont overload custom fields.
					if ( $count > 15 ) {
						break;
					}

					// Retrieve our original label to send with API request.
					$original_field_data = $this->plugin->process_form->get_original_fields( $form_id );
					$custom_field_name = '';
					$should_include = apply_filters( 'constant_contact_include_custom_field_label', false, $form_id );
					if ( false !== strpos( $original, 'custom___' ) && $should_include ) {
						$custom_field = ( $original_field_data[ $original ] );
						$custom_field_name .= $custom_field['name'] . ': ';
					}

					// Otherwise, set up our custom field.
					$custom = new Ctct\Components\Contacts\CustomField();

					// Create, name it the way the API needs.
					$custom = $custom->create( array(
							'name' => 'CustomField' . $count,
							'value' => $custom_field_name . $value,
					) );

					// Attach it.
					$contact->addCustomField( $custom );
					$count = $count + 1;
					break;
				case 'custom_text_area':
					$textareas++;
					// API version 2 only allows for 1 note for a given request.
					// Version 3 will allow multiple notes.
					if ( $textareas > 1 ) {
						break;
					}
					$unique_id = explode( '___', $original );
					$contact->notes[] = array(
						'created_date'  => date( 'Y-m-d\TH:i:s' ),
						'id'            => $unique_id[1],
						'modified_date' => date( 'Y-m-d\TH:i:s' ),
						'note'          => $value,
					);
					break;
				default:
					// If we got here, try to map our field to the key.
					try {
						$contact->$key = $value;
					} catch ( Exception $e ) {
						$this->log_errors( $e->getErrors() );
						break;
					}

					// Otherwise break anyway.
					break;
			} // End switch().
		} // End foreach().

		// If we did set address properties, then push it to our contact.
		if ( ! is_null( $address ) ) {
			$contact->addAddress( $address );
		}

		return $contact;
	}

	/**
	 * Pushes all error to api_error_message.
	 *
	 * @since 1.0.0
	 *
	 * @param array $errors Errors from API.
	 */
	public function log_errors( $errors ) {

		if ( is_array( $errors ) ) {
			foreach ( $errors as $error ) {
				$this->api_error_message( $error );
			}
		}
	}

	/**
	 * Process api error response.
	 *
	 * @since 1.0.0
	 *
	 * @param array $error API error repsonse.
	 * @return mixed
	 */
	private function api_error_message( $error ) {

		// Make sure we have our expected error key.
		if ( ! isset( $error->error_key ) ) {
			return false;
		}

		constant_contact_maybe_log_it(
			'API',
			$error->error_key . ': ' . $error->error_message,
			$error
		);

		// Otherwise work through our list of error keys we know.
		switch ( $error->error_key ) {
			case 'http.status.authentication.invalid_token':
				$this->access_token = false;
				return __( 'Your API access token is invalid. Reconnect to Constant Contact to receive a new token.', 'constant-contact-forms' );
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
	 * Rate limit ourselves to not bust API call rate limit.
	 *
	 * @since 1.0.0
	 */
	public function pause_api_calls() {
		sleep( 1 );
	}

	/**
	 * Make sure we don't over-do API requests, helper method to check if we're connected.
	 *
	 * @return boolean If connected.
	 */
	public function is_connected() {

		// Make sure we only grab our token once during a page load.
		static $token = null;

		// If we haven't grabbed it yet, grab it.
		if ( is_null( $token ) ) {
			$token = get_option( 'ctct_token', false ) ? true : false;
		}

		return $token;
	}

	/**
	 * Helper method to output a link for our connect modal.
	 *
	 * @since 1.0.0
	 */
	public function get_connect_link() {

		// Allow us to re-use the same verification twice on one page load.
		static $proof = null;

		// If we haven't set a proof yet, generate it.
		if ( is_null( $proof ) ) {
			$proof = constant_contact()->authserver->set_verification_option();
		}

		// Send back our connect url.
		return constant_contact()->authserver->do_connect_url( $proof );
	}

	/**
	 * Helper method to output a link for our connect modal.
	 *
	 * @since 1.0.0
	 */
	public function get_signup_link() {

		// Allow us to re-use the same verification twice on one page load.
		static $proof = null;

		// If we haven't set a proof yet, generate it.
		if ( is_null( $proof ) ) {
			$proof = constant_contact()->authserver->set_verification_option();
		}

		// Send back our connect url.
		return constant_contact()->authserver->do_signup_url( $proof );
	}

	/**
	 * Maybe get the disclosure address from the API Organization Information.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $as_parts If true return an array.
	 * array (
	 *     [name] => Business Name
	 *     [address] => 555 Business Place Ln., Beverly Hills, CA, 90210
	 * )
	 * @return mixed
	 */
	public function get_disclosure_info( $as_parts = false ) {

		// These fields are used to try and buld the full address.
		static $address_fields = array( 'line1', 'city', 'state_code', 'postal_code' );

		// Grab disclosure info from the API.
		$account_info = $this->get_account_info();

		// Bail on empty.
		if ( empty( $account_info ) ) {
			return $as_parts ? array() : '';
		}

		$disclosure = array(
			'name'    => empty( $account_info->organization_name ) ? ctct_get_settings_option( '_ctct_disclose_name' ) : $account_info->organization_name,
			'address' => ctct_get_settings_option( '_ctct_disclose_address' ),
		);

		// Bail if we don't have a name.
		if ( empty( $disclosure['name'] ) ) {
			return $as_parts ? array() : '';
		}

		// Determine the address to use for disclosure from the API.
		if (
			isset( $account_info->organization_addresses )
			&& count( $account_info->organization_addresses )
		) {
			// Assume the first address.
			$organization_address = array_shift( $account_info->organization_addresses );
			$disclosure_address   = array();

			// Add in our disclouse address.
			if ( is_array( $address_fields ) ) {
				foreach ( $address_fields as $field ) {
					if ( isset( $organization_address[ $field ] ) && strlen( $organization_address[ $field ] ) ) {
						$disclosure_address[] = $organization_address[ $field ];
					}
				}
			}

			// Join it all together.
			$disclosure['address'] = implode( ', ', $disclosure_address );
		} elseif ( empty( $disclosure['address'] ) ) {
			// Remove the address so we don't get a disclosure like "Business Name, ".
			unset( $disclosure['address'] );
		}

		if ( ! empty( $account_info->website ) ) {
			$disclosure['website'] = $account_info->website;
		}

		return $as_parts ? $disclosure : implode( ', ', array_values( $disclosure ) );
	}
}

/**
 * Helper function to get/return the ConstantContact_API object.
 *
 * @since 1.0.0
 *
 * @return object ConstantContact_API
 */
function constantcontact_api() {
	return constant_contact()->api;
}

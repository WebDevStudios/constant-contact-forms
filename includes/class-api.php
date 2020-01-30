<?php
/**
 * Constant Contact API class.
 *
 * @package ConstantContact
 * @subpackage API
 * @author Constant Contact
 * @since 1.0.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

use Ctct\ConstantContact;
use Ctct\Components\Contacts\Contact;
use Ctct\Components\Contacts\ContactList;
use Ctct\Exceptions\CtctException;

/**
 * Powers connection between site and Constant Contact API.
 *
 * @since 1.0.0
 */
class ConstantContact_API {

	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	protected $plugin;

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

			default:
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

		if ( ! $this->is_connected() ) {
			return [];
		}

		$acct_data = get_transient( 'constant_contact_acct_info' );

		/**
		 * Filters whether or not to bypass transient with a filter.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $value Whether or not to bypass.
		 */
		$bypass_acct_cache = apply_filters( 'constant_contact_bypass_acct_info_cache', false );

		if ( false === $acct_data || $bypass_acct_cache ) {
			try {

				$acct_data = $this->cc()->accountService->getAccountInfo( $this->get_api_token() );

				if ( $acct_data ) {
					set_transient( 'constant_contact_acct_info', $acct_data, 1 * HOUR_IN_SECONDS );
				}
			} catch ( CtctException $ex ) {
				$extra = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$this->log_errors( $extra . $ex->getErrors() );
				constant_contact_set_has_exceptions();
			} catch ( Exception $ex ) {
				$error                = new stdClass();
				$error->error_key     = get_class( $ex );
				$error->error_message = $ex->getMessage();
				$messages[]           = $error;

				add_filter( 'constant_contact_force_logging', '__return_true' );
				constant_contact_set_has_exceptions();

				$extra = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$this->log_errors( $extra . $messages );
			}
		}

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

		if ( ! $this->is_connected() ) {
			return [];
		}

		$contacts = get_transient( 'ctct_contact' );

		if ( false === $contacts ) {
			try {
				$contacts = $this->cc()->contactService->getContacts( $this->get_api_token() );
				set_transient( 'ctct_contact', $contacts, 1 * HOUR_IN_SECONDS );
				return $contacts;

			} catch ( CtctException $ex ) {
				$extra = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$this->log_errors( $extra . $ex->getErrors() );
				constant_contact_set_has_exceptions();
			} catch ( Exception $ex ) {
				$error                = new stdClass();
				$error->error_key     = get_class( $ex );
				$error->error_message = $ex->getMessage();
				$messages[]           = $error;

				add_filter( 'constant_contact_force_logging', '__return_true' );
				constant_contact_set_has_exceptions();

				$extra = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$this->log_errors( $extra . $messages );
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

		if ( ! $this->is_connected() ) {
			return [];
		}

		$lists = get_transient( 'ctct_lists' );

		if ( $force_skip_cache ) {
			$lists = false;
		}

		if ( false === $lists ) {
			try {

				$lists = $this->cc()->listService->getLists( $this->get_api_token() );

				if ( is_array( $lists ) ) {
					set_transient( 'ctct_lists', $lists, 1 * HOUR_IN_SECONDS );
					return $lists;
				}
			} catch ( CtctException $ex ) {
				$extra = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$this->log_errors( $extra . $ex->getErrors() );
				constant_contact_set_has_exceptions();
			} catch ( Exception $ex ) {
				$error = new stdClass();
				$error->error_key = get_class( $ex );
				$error->error_message = $ex->getMessage();
				$messages[] = $error;

				add_filter( 'constant_contact_force_logging', '__return_true' );
				constant_contact_set_has_exceptions();

				$extra = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$this->log_errors( $extra . $messages );
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

		$id = esc_attr( $id );

		if ( ! $id ) {
			return [];
		}

		if ( ! $this->is_connected() ) {
			return [];
		}

		$list = get_transient( 'ctct_list_' . $id );

		if ( false === $list ) {
			try {
				$list = $this->cc()->listService->getList( $this->get_api_token(), $id );
				set_transient( 'ctct_lists_' . $id, $list, 1 * HOUR_IN_SECONDS );
				return $list;
			} catch ( CtctException $ex ) {
				$extra = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$this->log_errors( $extra . $ex->getErrors() );
				constant_contact_set_has_exceptions();
			} catch ( Exception $ex ) {
				$error                = new stdClass();
				$error->error_key     = get_class( $ex );
				$error->error_message = $ex->getMessage();
				$messages[]           = $error;

				add_filter( 'constant_contact_force_logging', '__return_true' );
				constant_contact_set_has_exceptions();

				$extra = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$this->log_errors( $extra . $messages );
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
	public function add_list( $new_list = [] ) {

		if ( empty( $new_list ) || ! isset( $new_list['id'] ) ) {
			return [];
		}

		$return_list = [];

		try {
			$list = $this->cc()->listService->getList( $this->get_api_token(), esc_attr( $new_list['id'] ) );
		} catch ( CtctException $ex ) {
			$extra = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$this->log_errors( $extra . $ex->getErrors() );
			constant_contact_set_has_exceptions();
		} catch ( Exception $ex ) {
			$error                = new stdClass();
			$error->error_key     = get_class( $ex );
			$error->error_message = $ex->getMessage();
			$messages[]           = $error;

			add_filter( 'constant_contact_force_logging', '__return_true' );
			constant_contact_set_has_exceptions();

			$extra = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$this->log_errors( $extra . $messages );
		}

		if ( isset( $list ) ) {
			return $list;
		}

		try {

			$list = new ContactList();

			$list->name = isset( $new_list['name'] ) ? esc_attr( $new_list['name'] ) : '';

			/**
			 * Filters the list status to use when adding a list.
			 *
			 * @since 1.0.0
			 *
			 * @param string $value List status to use.
			 */
			$list->status = apply_filters( 'constant_contact_list_status', 'HIDDEN' );

			$return_list = $this->cc()->listService->addList( $this->get_api_token(), $list );

		} catch ( CtctException $ex ) {
			$extra = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$this->log_errors( $extra . $ex->getErrors() );
			constant_contact_set_has_exceptions();
		} catch ( Exception $ex ) {
			$error                = new stdClass();
			$error->error_key     = get_class( $ex );
			$error->error_message = $ex->getMessage();
			$messages[]           = $error;

			add_filter( 'constant_contact_force_logging', '__return_true' );
			constant_contact_set_has_exceptions();

			$extra = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$this->log_errors( $extra . $messages );
		}

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
	public function update_list( $updated_list = [] ) {

		$return_list = false;

		try {

			$list = new ContactList();

			$list->id   = isset( $updated_list['id'] ) ? esc_attr( $updated_list['id'] ) : '';
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
			$extra = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$this->log_errors( $extra . $ex->getErrors() );
			constant_contact_set_has_exceptions();
		} catch ( Exception $ex ) {
			$error                = new stdClass();
			$error->error_key     = get_class( $ex );
			$error->error_message = $ex->getMessage();
			$messages[]           = $error;

			add_filter( 'constant_contact_force_logging', '__return_true' );
			constant_contact_set_has_exceptions();

			$extra = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$this->log_errors( $extra . $messages );
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
	public function delete_list( $updated_list = [] ) {

		if ( ! isset( $updated_list['id'] ) ) {
			return [];
		}

		$list = false;

		try {
			$list = $this->cc()->listService->deleteList( $this->get_api_token(), $updated_list['id'] );
		} catch ( CtctException $ex ) {
			$extra = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$this->log_errors( $extra . $ex->getErrors() );
			constant_contact_set_has_exceptions();
		} catch ( Exception $ex ) {
			$error                = new stdClass();
			$error->error_key     = get_class( $ex );
			$error->error_message = $ex->getMessage();
			$messages[]           = $error;

			add_filter( 'constant_contact_force_logging', '__return_true' );
			constant_contact_set_has_exceptions();

			$extra = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$this->log_errors( $extra . $messages );
		}

		return $list;
	}

	/**
	 * Add contact to the connected CTCT account.
	 *
	 * @since 1.0.0
	 * @since 1.3.0 Added $form_id parameter.
	 *
	 * @throws Exception
	 *
	 * @param array $new_contact New contact data.
	 * @param int   $form_id     ID of the form being processed.
	 * @return array Current connect ctct lists.
	 */
	public function add_contact( $new_contact = [], $form_id = 0 ) {

		if ( empty( $new_contact ) ) {
			return [];
		}

		if ( ! isset( $new_contact['email'] ) ) {
			return [];
		}

		$api_token = $this->get_api_token();
		$email     = sanitize_email( $new_contact['email'] );

		// Set our list data. If we didn't get passed a list and got this far, just generate a random ID.
		$list = isset( $new_contact['list'] ) ? esc_attr( $new_contact['list'] ) : 'cc_' . wp_generate_password( 15, false );

		$return_contact = false;

		try {
			$response = $this->cc()->contactService->getContacts( $api_token, [ 'email' => $email ] );

			if ( isset( $response->results ) && ! empty( $response->results ) ) {
				constant_contact_maybe_log_it( 'API', 'Contact set to be updated', [ 'form' => get_the_title( $form_id ) ] );
				$return_contact = $this->update_contact( $response, $api_token, $list, $new_contact, $form_id );

			} else {
				constant_contact_maybe_log_it( 'API', 'Contact set to be created', [ 'form' => get_the_title( $form_id ) ] );
				$return_contact = $this->create_contact( $api_token, $list, $email, $new_contact, $form_id );
			}
		} catch ( CtctException $ex ) {
			$extra = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$this->log_errors( $extra . $ex->getErrors() );
			if ( 400 !== $ex->getCode() || false !== strpos( 'Bad Request', $ex->getMessage() ) ) {
				constant_contact_set_has_exceptions();
			}
		} catch ( Exception $ex ) {
			$error                = new stdClass();
			$error->error_key     = get_class( $ex );
			$error->error_message = $ex->getMessage();
			$messages[]           = $error;

			add_filter( 'constant_contact_force_logging', '__return_true' );
			if ( 400 !== $ex->getCode() || false !== strpos( 'Bad Request', $ex->getMessage() ) ) {
				constant_contact_set_has_exceptions();
			}

			$extra = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$this->log_errors( $extra . $messages );
		}

		$new_contact = $this->clear_email( $new_contact );
		constant_contact_maybe_log_it( 'API', 'Submitted contact data', $new_contact );

		return $return_contact;
	}

	/**
	 * Obfuscate the left side of email addresses at the `@`.
	 *
	 * @since 1.7.0
	 *
	 * @param array $contact Contact data.
	 * @return array
	 */
	private function clear_email( array $contact ) {
		$clean = [];
		foreach ( $contact as $contact_key => $contact_value ) {
			if ( is_array( $contact_value ) ) {
				$clean[ $contact_key ] = $this->clear_email( $contact_value );
			} elseif ( is_email( $contact_value ) ) {
				$email_parts = explode( '@', $contact_value );
				$clean[ $contact_key ] = implode( '@', [ '***', $email_parts[1] ] );
			} else {
				$clean[ $contact_key ] = $contact_value;
			}
		}
		return $clean;
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
	public function create_contact( $api_token, $list, $email, $user_data, $form_id ) {

		$contact = new Contact();
		$contact->addEmail( sanitize_text_field( $email ) );
		$contact->addList( esc_attr( $list ) );

		try {
			$contact = $this->set_contact_properties( $contact, $user_data, $form_id );
		} catch ( CtctException $ex ) {
			$extra = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$this->log_errors( $extra . $ex->getErrors() );
			constant_contact_set_has_exceptions();
		} catch ( Exception $ex ) {
			$error                = new stdClass();
			$error->error_key     = get_class( $ex );
			$error->error_message = $ex->getMessage();
			$messages[]           = $error;

			add_filter( 'constant_contact_force_logging', '__return_true' );
			constant_contact_set_has_exceptions();

			$extra = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$this->log_errors( $extra . $messages );
		}

		/*
		 * See: http://developer.constantcontact.com/docs/contacts-api/contacts-index.html#opt_in
		 */
		return $this->cc()->contactService->addContact(
			$api_token,
			$contact,
			[ 'action_by' => 'ACTION_BY_VISITOR' ]
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
	public function update_contact( $response, $api_token, $list, $user_data, $form_id ) {

		if (
			isset( $response->results ) &&
			isset( $response->results[0] ) &&
			( $response->results[0] instanceof Contact )
		) {

			$contact = $response->results[0];
			$contact->addList( esc_attr( $list ) );

			try {
				$contact = $this->set_contact_properties( $contact, $user_data, $form_id, true );
			} catch ( CtctException $ex ) {
				$extra = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$this->log_errors( $extra . $ex->getErrors() );
				constant_contact_set_has_exceptions();
			} catch ( Exception $ex ) {
				$error                = new stdClass();
				$error->error_key     = get_class( $ex );
				$error->error_message = $ex->getMessage();
				$messages[]           = $error;

				add_filter( 'constant_contact_force_logging', '__return_true' );
				constant_contact_set_has_exceptions();

				$extra = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$this->log_errors( $extra . $messages );
			}

			/*
			 * See: http://developer.constantcontact.com/docs/contacts-api/contacts-index.html#opt_in array( 'action_by' => 'ACTION_BY_VISITOR' )
			 */
			return $this->cc()->contactService->updateContact(
				$api_token,
				$contact,
				[ 'action_by' => 'ACTION_BY_VISITOR' ]
			);
		} else {
			$error = new CtctException();
			$error->setErrors( [ 'type', __( 'Contact type not returned', 'constant-contact-forms' ) ] );
			throw $error;
		}
	}

	/**
	 * Helper method to push as much data from a form as we can into the
	 * Constant Contact contact thats in a list.
	 *
	 * @since 1.0.0
	 * @since 1.3.0 Added $form_id parameter.
	 * @since 1.4.5 Added $updated paramater.
	 *
	 * @param object $contact   Contact object.
	 * @param array  $user_data Bunch of user data.
	 * @param string $form_id   Form ID being processed.
	 * @param bool   $updated   Whether or not we are updating a contact. Default false.
	 * @throws CtctException $error An exception error.
	 * @return object Contact object, with new properties.
	 */
	public function set_contact_properties( $contact, $user_data, $form_id, $updated = false ) {
		if ( ! is_object( $contact ) || ! is_array( $user_data ) ) {
			$error = new CtctException();
			$error->setErrors( [ 'type', esc_html__( 'Not a valid contact to set properties to.', 'constant-contact-forms' ) ] );
			throw $error;
		}

		unset( $user_data['list'] );

		$address   = null;
		$count     = 1;
		$textareas = 0;
		if ( ! $updated ) {
			$contact->notes = [];
		}

		foreach ( $user_data as $original => $value ) {
			$key   = sanitize_text_field( isset( $value['key'] ) ? $value['key'] : false );
			$value = sanitize_text_field( isset( $value['val'] ) ? $value['val'] : false );

			if ( ! $key || ! $value ) {
				continue;
			}

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
					if ( null === $address ) {
						$address = new Ctct\Components\Contacts\Address();
					}

					switch ( $key ) {
						case 'street_address':
							$address->address_type = 'PERSONAL';
							$address->line1        = $value;
							break;
						case 'line_2_address':
							$address->line2 = $value;
							break;
						case 'city_address':
							$address->city = $value;
							break;
						case 'state_address':
							$address->state        = $value;
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
					$custom_field_name   = '';
					$should_include      = apply_filters( 'constant_contact_include_custom_field_label', false, $form_id );
					if ( false !== strpos( $original, 'custom___' ) && $should_include ) {
						$custom_field       = ( $original_field_data[ $original ] );
						$custom_field_name .= $custom_field['name'] . ': ';
					}

					$custom = new Ctct\Components\Contacts\CustomField();

					$custom = $custom->create( [
						'name'  => 'CustomField' . $count,
						'value' => $custom_field_name . $value,
					] );

					$contact->addCustomField( $custom );
					$count++;
					break;
				case 'custom_text_area':
					$textareas++;
					// API version 2 only allows for 1 note for a given request.
					// Version 3 will allow multiple notes.
					if ( $textareas > 1 ) {
						break;
					}
					if ( ! $updated ) {
						$unique_id        = explode( '___', $original );
						$contact->notes[] = [
							'created_date'  => date( 'Y-m-d\TH:i:s' ),
							'id'            => $unique_id[1],
							'modified_date' => date( 'Y-m-d\TH:i:s' ),
							'note'          => $value,
						];
					} else {
						$contact->notes[0]->note .= ' ' . $value;
					}
					break;
				default:
					try {
						$contact->$key = $value;
					} catch ( Exception $e ) {
						$extra = constant_contact_location_and_line( __METHOD__, __LINE__ );
						$this->log_errors( $extra . $e->getErrors() );
						constant_contact_set_has_exceptions();
						break;
					}

					break;
			} // End switch.
		} // End foreach.

		if ( null !== $address ) {
			$contact->addAddress( $address );
		}

		return $contact;
	}

	/**
	 * Pushes all error to api_error_message.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception
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
	 * @throws Exception
	 *
	 * @param array $error API error repsonse.
	 * @return mixed
	 */
	private function api_error_message( $error ) {

		if ( ! isset( $error->error_key ) ) {
			return false;
		}

		constant_contact_maybe_log_it(
			'API',
			$error->error_key . ': ' . $error->error_message,
			$error
		);

		switch ( $error->error_key ) {
			case 'http.status.authentication.invalid_token':
				$this->access_token = false;
				return esc_html__( 'Your API access token is invalid. Reconnect to Constant Contact to receive a new token.', 'constant-contact-forms' );
			case 'mashery.not.authorized.over.qps':
				$this->pause_api_calls();
				return;
			default:
				return false;
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
	 * @since 1.0.0
	 *
	 * @return boolean If connected.
	 */
	public function is_connected() {

		static $token = null;

		if ( null === $token ) {
			$token = get_option( 'ctct_token', false ) ? true : false;
		}

		return $token;
	}

	/**
	 * Helper method to output a link for our connect modal.
	 *
	 * @since 1.0.0
	 * @return string Connect URL.
	 */
	public function get_connect_link() {

		static $proof = null;

		if ( null === $proof ) {
			$proof = constant_contact()->authserver->set_verification_option();
		}

		return constant_contact()->authserver->do_connect_url( $proof );
	}

	/**
	 * Helper method to output a link for our connect modal.
	 *
	 * @since 1.0.0
	 *
	 * @return string Signup URL.
	 */
	public function get_signup_link() {

		static $proof = null;

		if ( null === $proof ) {
			$proof = constant_contact()->authserver->set_verification_option();
		}

		return constant_contact()->authserver->do_signup_url( $proof );
	}

	/**
	 * Maybe get the disclosure address from the API Organization Information.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $as_parts If true return an array.
	 * @return mixed
	 */
	public function get_disclosure_info( $as_parts = false ) {
		/*
		 * [
		 *     [name] => Business Name
		 *     [address] => 555 Business Place Ln., Beverly Hills, CA, 90210
		 * ]
		 */

		static $address_fields = [ 'line1', 'city', 'state_code', 'postal_code' ];

		// Grab disclosure info from the API.
		$account_info = $this->get_account_info();

		if ( empty( $account_info ) ) {
			return $as_parts ? [] : '';
		}

		$disclosure = [
			'name'    => empty( $account_info->organization_name ) ? ctct_get_settings_option( '_ctct_disclose_name', '' ) : $account_info->organization_name,
			'address' => ctct_get_settings_option( '_ctct_disclose_address', '' ),
		];

		if ( empty( $disclosure['name'] ) ) {
			return $as_parts ? [] : '';
		}

		// Determine the address to use for disclosure from the API.
		if (
			isset( $account_info->organization_addresses )
			&& count( $account_info->organization_addresses )
		) {
			$organization_address = array_shift( $account_info->organization_addresses );
			$disclosure_address   = [];

			if ( is_array( $address_fields ) ) {
				foreach ( $address_fields as $field ) {
					if ( isset( $organization_address[ $field ] ) && strlen( $organization_address[ $field ] ) ) {
						$disclosure_address[] = $organization_address[ $field ];
					}
				}
			}

			$disclosure['address'] = implode( ', ', $disclosure_address );
		} elseif ( empty( $disclosure['address'] ) ) {
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

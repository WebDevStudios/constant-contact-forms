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
require_once 'Ctct/Components/Component.php';
require_once 'Ctct/Components/Contacts/Contact.php';
require_once 'Ctct/Components/Contacts/ContactList.php';
require_once 'Ctct/Exceptions/CtctException.php';

use Ctct\Components\Contacts\Contact;
use Ctct\Components\Contacts\ContactList;
use Ctct\Exceptions\CtctException;

/**
 * Powers connection between site and Constant Contact API.
 *
 * @todo Test RefreshToken Cron Job
 * @since 1.0.0
 */
class ConstantContact_API {


	/**
	 * Parent plugin class.
	 * @since 1.0.0
	 * @var object
	 */
	protected object $plugin;

	/**
	 * Access token.
	 * @since 1.3.0
	 * @var bool
	 */
	public string $access_token = '';

	/**
	 * Refresh token.
	 * @since 2.0.0
	 * @var string
	 */
	public string $refresh_token = '';

	/**
	 * Expires timestamp.
	 * @since 2.0.0
	 * @var string
	 */
	public string $expires_in = '';

	/**
	 * OAuth2 URL
	 * @since 2.0.0
	 * @var string
	 */
	private string $oauth2_url = 'https://authz.constantcontact.com/oauth2/default/v1/token';

	/**
	 * Authorize URL
	 * @since 2.0.0
	 * @var string
	 */
	private string $authorize_url = 'https://authz.constantcontact.com/oauth2/default/v1/authorize';

	/**
	 * Last error message.
	 * @since 2.0.0
	 * @var string
	 */
	private string $last_error = '';

	/**
	 * Body value.
	 * @since 2.0.0
	 * @var string
	 */
	private string $body = '';

	/**
	 * Host value.
	 * @since 2.0.0
	 * @var string
	 */
	private string $host = '';

	/**
	 * Status code for a request
	 * @since 2.0.0
	 * @var int
	 */
	private int $status_code = 200;

	/**
	 * Session callback value.
	 * @since 2.0.0
	 * @var null
	 */
	private $session_callback = null;

	/**
	 * PKCE
	 * @since 2.0.0
	 * @var bool
	 */
	public bool $PKCE = true;

	/**
	 * Scopes for authorization usage.
	 * @since 2.0.0
	 * @var array|int[]|string[]
	 */
	private array $scopes = [];

	/**
	 * Valid scope values
	 * @since 2.0.0
	 * @var array|string[]
	 */
	private array $valid_scopes = [
		'account_read',
		'account_update',
		'contact_data',
		'campaign_data',
		'offline_access'
	];

	/**
	 * Constant Contact's API Key
	 * @since 2.0.0
	 * @var string
	 */
	private string $client_api_key = 'a001418d-73c6-4ecb-9f8b-d5773d29b6e4'; // Managed by Constant Contact. Plain text OK due to PCKE auth method.

	/**
	 * OAuth2 redirect URI
	 * @since 2.0.0
	 * @var string
	 */
	private string $redirect_URI = 'https://app.constantcontact.com/pages/dma/portal/oauth2';

	/**
	 * Current User ID
	 * @since 2.0.0
	 * @var int
	 */
	public int $this_user_id = 0;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $plugin Parent plugin class.
	 */
	public function __construct( object $plugin ) {
		$this->plugin = $plugin;
		$this->scopes = array_flip( $this->valid_scopes );

		add_action( 'init', [ $this, 'cct_init' ] );
		add_action( 'refresh_token_job', [ $this, 'refresh_token' ] );
		add_action( 'ctct_access_token_acquired', [ $this, 'clear_missed_api_requests' ] );
	}

	/**
	 *
	 * @since 1.0.0
	 */
	public function cct_init() {

		$this->this_user_id = get_current_user_id();

		$this->expires_in    = constant_contact()->get_connect()->e_get( '_ctct_expires_in' );
		$this->refresh_token = constant_contact()->get_connect()->e_get( '_ctct_refresh_token' );
		$this->access_token  = constant_contact()->get_connect()->e_get( '_ctct_access_token' );

		// Attempt to acquire access token if we don't have it already.
		// This fixes an issue where authorization does not work sometimes when switching between different accounts.
		if (
			empty( $this->expires_in ) ||
			empty( $this->refresh_token ) ||
			empty( $this->access_token )
		) {
			$success = $this->acquire_access_token();
			if ( $success ) {
				update_option( 'ctct_access_token_timestamp', time() );
			}
		}

		// custom scheduling based on the expiry time returned with access token
		if ( ! empty( $this->expires_in ) ) {
			add_filter(
				'cron_schedules',
				function ( $schedules ) {
					$schedules['pkce_expiry'] = [
						'interval' => $this->expires_in - 3600, // refreshing token before 1 hour of expiry
						'display'  => esc_html__( 'Token Expiry', 'constant-contact-forms' ),
					];
					return $schedules;
				}
			);

			if ( ! wp_next_scheduled( 'refresh_token_job' ) ) { // if it hasn't been scheduled
				wp_schedule_event( time(), 'pkce_expiry', 'refresh_token_job' ); // schedule it
			}
		} else {
			wp_unschedule_hook( 'refresh_token_job' );
		}

		if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) {
			if ( $this->access_token_maybe_expired() ) {
				$this->refresh_token();
			}
		}

	}

	/**
	 * Get new instance of ConstantContact.
	 *
	 * @since 1.0.0
	 *
	 * @return object ConstantContact_API.
	 */
	public function cc() {
		return new ConstantContact_Client( $this->get_api_token() );
	}

	/**
	 * Returns API token string to access API.
	 *
	 * @since 1.0.0
	 *
	 * @return string Access API token.
	 */
	public function get_api_token() {

		$token = '';

		if ( constant_contact()->get_connect()->e_get( '_ctct_access_token' ) ) {
			$token .= constant_contact()->get_connect()->e_get( '_ctct_access_token' );
		} else {
			$success = $this->acquire_access_token();
			if ( $success ) {
				update_option( 'ctct_access_token_timestamp', time() );
			}
		}

		return $token;
	}

	/**
	 * Returns Refresh API token.
	 *
	 * @since 2.0.0
	 *
	 * @param string $type api key type.
	 * @return string Refresh Token.
	 */
	public function get_refresh_token() {
		return $this->refresh_token;
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
				$acct_data = $this->cc()->get_account_info();
				if ( array_key_exists( 'error_key', $acct_data ) && 'unauthorized' === $acct_data['error_key'] ) {
					$this->refresh_token();

					$acct_data = $this->cc()->get_account_info();
				}

				if ( $acct_data ) {
					set_transient( 'constant_contact_acct_info', $acct_data, 12 * HOUR_IN_SECONDS );
				}
			} catch ( CtctException $ex ) {
				add_filter( 'constant_contact_force_logging', '__return_true' );
				$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$errors       = $ex->getErrors();
				$our_errors[] = $extra . ' - ' . $errors[0]->error_key . ' - ' . $errors[0]->error_message;
				$this->log_errors( $our_errors );
				constant_contact_forms_maybe_set_exception_notice( $ex );
			} catch ( Exception $ex ) {
				$error                = new stdClass();
				$error->error_key     = get_class( $ex );
				$error->error_message = $ex->getMessage();

				add_filter( 'constant_contact_force_logging', '__return_true' );
				constant_contact_forms_maybe_set_exception_notice( $ex );

				$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$our_errors[] = $extra . ' - ' . $error->error_key . ' - ' . $error->error_message;
				$this->log_errors( $our_errors );
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
				$contacts = $this->cc()->get_contacts( $this->get_api_token() );
				if ( array_key_exists( 'error_key', $contacts ) && 'unauthorized' === $contacts['error_key'] ) {
					$this->refresh_token();

					$contacts = $this->cc()->get_contacts( $this->get_api_token() );
				}

				set_transient( 'ctct_contact', $contacts, 1 * DAY_IN_SECONDS );
				return $contacts;
			} catch ( CtctException $ex ) {
				add_filter( 'constant_contact_force_logging', '__return_true' );
				$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$errors       = $ex->getErrors();
				$our_errors[] = $extra . ' - ' . $errors[0]->error_key . ' - ' . $errors[0]->error_message;
				$this->log_errors( $our_errors );
				constant_contact_forms_maybe_set_exception_notice( $ex );
			} catch ( Exception $ex ) {
				$error                = new stdClass();
				$error->error_key     = get_class( $ex );
				$error->error_message = $ex->getMessage();

				add_filter( 'constant_contact_force_logging', '__return_true' );
				constant_contact_forms_maybe_set_exception_notice( $ex );

				$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$our_errors[] = $extra . ' - ' . $error->error_key . ' - ' . $error->error_message;
				$this->log_errors( $our_errors );
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
	public function get_lists( bool $force_skip_cache = false ) {

		if ( ! $this->is_connected() ) {
			return [];
		}

		$lists = get_transient( 'ctct_lists' );

		if ( $force_skip_cache ) {
			$lists = false;
		}

		if ( false === $lists ) {

			try {
				$results = $this->cc()->get_lists();
				$lists = $results['lists'] ?? [];

				if ( array_key_exists( 'error_key', $results ) && 'unauthorized' === $results['error_key'] ) {
					$this->refresh_token();

					$results = $this->cc()->get_lists();
					$lists   = $results['lists'] ?? [];
				}

				if ( ! empty( $lists ) ) {
					set_transient( 'ctct_lists', $lists, 12 * HOUR_IN_SECONDS );
					return $lists;
				} elseif ( array_key_exists( 'error_key', $results ) ) {
					set_transient( 'ctct_lists', $lists, DAY_IN_SECONDS );
					add_filter( 'constant_contact_force_logging', '__return_true' );
					$extra = constant_contact_location_and_line( __METHOD__, __LINE__ );
					$our_errors[] = $extra . $results['error_key'] . ': ' . $results['error_message'];
					$this->log_errors($our_errors);
					constant_contact_forms_maybe_set_exception_notice();
				}
			} catch ( CtctException $ex ) {
				add_filter( 'constant_contact_force_logging', '__return_true' );
				$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$errors       = $ex->getErrors();
				$our_errors[] = $extra . ' - ' . $errors[0]->error_key . ' - ' . $errors[0]->error_message;
				$this->log_errors( $our_errors );
				constant_contact_forms_maybe_set_exception_notice( $ex );
			} catch ( Exception $ex ) {
				$error                = new stdClass();
				$error->error_key     = get_class( $ex );
				$error->error_message = $ex->getMessage();

				add_filter( 'constant_contact_force_logging', '__return_true' );
				constant_contact_forms_maybe_set_exception_notice( $ex );

				$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$our_errors[] = $extra . ' - ' . $error->error_key . ' - ' . $error->error_message;
				$this->log_errors( $our_errors );
			}
		}

		return $lists;
	}

	/**
	 * Get v2 to v3 API lists of the connected CTCT account.
	 *
	 * @since 2.0.0
	 *
	 * @param string $old_ids_string   Comma separated list of old (v2 API) list ids.
	 * @param bool   $force_skip_cache Whether or not to skip cache.
	 *
	 * @return array API v2 to v3 List ID cross references.
	 */
	public function get_v2_list_id_x_refs( string $old_ids_string, bool $force_skip_cache = false ) {

		if ( ! $this->is_connected() ) {
			return [];
		}

		$list_x_refs = get_transient('ctct_list_xrefs');

		if ( $force_skip_cache ) {
			$list_x_refs = false;
		}

		if ( false === $list_x_refs ) {

			try {
				$list_x_refs = $this->cc()->get_updated_lists_ids( $old_ids_string );
				if ( is_array( $list_x_refs ) ) {
					set_transient('ctct_list_xrefs', $list_x_refs, HOUR_IN_SECONDS );
					return $list_x_refs;
				}
			} catch ( CtctException $ex ) {
				add_filter( 'constant_contact_force_logging', '__return_true' );
				$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$errors       = $ex->getErrors();
				$our_errors[] = $extra . ' - ' . $errors[0]->error_key . ' - ' . $errors[0]->error_message;
				$this->log_errors( $our_errors );
				constant_contact_forms_maybe_set_exception_notice( $ex );
			} catch ( Exception $ex ) {
				$error                = new stdClass();
				$error->error_key     = get_class( $ex );
				$error->error_message = $ex->getMessage();

				add_filter( 'constant_contact_force_logging', '__return_true' );
				constant_contact_forms_maybe_set_exception_notice( $ex );

				$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$our_errors[] = $extra . ' - ' . $error->error_key . ' - ' . $error->error_message;
				$this->log_errors( $our_errors );
			}
		}

		return $list_x_refs;
	}

	/**
	 * Get an individual list by ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id List ID.
	 * @return mixed
	 */
	public function get_list( string $id ) {

		if ( ! esc_attr( $id ) ) {
			return [];
		}

		if ( ! $this->is_connected() ) {
			return [];
		}

		$list = get_transient( 'ctct_list_' . $id );

		if ( false === $list ) {
			try {
				$list = $this->cc()->get_list( $id );
				if ( array_key_exists( 'error_key', $list ) && 'unauthorized' === $list['error_key'] ) {
					$this->refresh_token();

					$list = $this->cc()->get_list( $id );
				}

				set_transient( 'ctct_lists_' . $id, $list, 1 * DAY_IN_SECONDS );
				return $list;
			} catch ( CtctException $ex ) {
				add_filter( 'constant_contact_force_logging', '__return_true' );
				$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$errors       = $ex->getErrors();
				$our_errors[] = $extra . ' - ' . $errors[0]->error_key . ' - ' . $errors[0]->error_message;
				$this->log_errors( $our_errors );
				constant_contact_forms_maybe_set_exception_notice( $ex );
			} catch ( Exception $ex ) {
				$error                = new stdClass();
				$error->error_key     = get_class( $ex );
				$error->error_message = $ex->getMessage();

				add_filter( 'constant_contact_force_logging', '__return_true' );
				constant_contact_forms_maybe_set_exception_notice( $ex );

				$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
				$our_errors[] = $extra . ' - ' . $error->error_key . ' - ' . $error->error_message;
				$this->log_errors( $our_errors );
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
			$list = $this->cc()->get_list( esc_attr( $new_list['id'] ) );
			if ( array_key_exists( 'error_key', $list ) && 'unauthorized' === $list['error_key'] ) {
				$this->refresh_token();

				$list = $this->cc()->get_list( esc_attr( $new_list['id'] ) );
			}
		} catch ( CtctException $ex ) {
			add_filter( 'constant_contact_force_logging', '__return_true' );
			$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$errors       = $ex->getErrors();
			$our_errors[] = $extra . ' - ' . $errors[0]->error_key . ' - ' . $errors[0]->error_message;
			$this->log_errors( $our_errors );
			constant_contact_forms_maybe_set_exception_notice( $ex );
		} catch ( Exception $ex ) {
			$error                = new stdClass();
			$error->error_key     = get_class( $ex );
			$error->error_message = $ex->getMessage();

			add_filter( 'constant_contact_force_logging', '__return_true' );
			constant_contact_forms_maybe_set_exception_notice( $ex );

			$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$our_errors[] = $extra . ' - ' . $error->error_key . ' - ' . $error->error_message;
			$this->log_errors( $our_errors );
		}

		if ( ! isset( $list[0]['error_key'] ) ) {
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

			$return_list = $this->cc()->add_list( $list );
			if ( isset( $return_list[0]['error_message'] ) ) {
				// TODO: check why it's not going to catch
				throw new Exception( $return_list[0]['error_message'] );
			}
		} catch ( CtctException $ex ) {
			add_filter( 'constant_contact_force_logging', '__return_true' );
			$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$errors       = $ex->getErrors();
			$our_errors[] = $extra . ' - ' . $errors[0]->error_key . ' - ' . $errors[0]->error_message;
			$this->log_errors( $our_errors );
			constant_contact_forms_maybe_set_exception_notice( $ex );
		} catch ( Exception $ex ) {
			$error                = new stdClass();
			$error->error_key     = get_class( $ex );
			$error->error_message = $ex->xdebug_message;

			add_filter( 'constant_contact_force_logging', '__return_true' );
			constant_contact_forms_maybe_set_exception_notice( $ex );

			$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$our_errors[] = $extra . ' - ' . $error->error_key . ' - ' . $error->error_message;
			$this->log_errors( $our_errors );
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
	public function update_list( array $updated_list = [] ) {

		$return_list = false;

		try {

			$list = new ContactList();

			$list->id       = isset( $updated_list['id'] ) ? esc_attr( $updated_list['id'] ) : '';
			$list->name     = isset( $updated_list['name'] ) ? esc_attr( $updated_list['name'] ) : '';
			$list->favorite = isset( $updated_list['favorite'] ) ? esc_attr( $updated_list['favorite'] ) : false;

			/**
			 * Filters the list status to use when updating a list.
			 *
			 * @since 1.0.0
			 *
			 * @param string $value List status to use.
			 */
			$list->status = apply_filters( 'constant_contact_list_status', 'HIDDEN' );

			$return_list = $this->cc()->update_list( $list );
			if ( array_key_exists( 'error_key', $return_list ) && 'unauthorized' === $return_list['error_key'] ) {
				$this->refresh_token();
				$return_list = $this->cc()->update_list( $list );
			}
		} catch ( CtctException $ex ) {
			add_filter( 'constant_contact_force_logging', '__return_true' );
			$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$errors       = $ex->getErrors();
			$our_errors[] = $extra . ' - ' . $errors[0]->error_key . ' - ' . $errors[0]->error_message;
			$this->log_errors( $our_errors );
			constant_contact_forms_maybe_set_exception_notice( $ex );
		} catch ( Exception $ex ) {
			$error                = new stdClass();
			$error->error_key     = get_class( $ex );
			$error->error_message = $ex->getMessage();

			add_filter( 'constant_contact_force_logging', '__return_true' );
			constant_contact_forms_maybe_set_exception_notice( $ex );

			$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$our_errors[] = $extra . ' - ' . $error->error_key . ' - ' . $error->error_message;
			$this->log_errors( $our_errors );
		}

		return $return_list;
	}

	/**
	 * Delete List from the connected CTCT account.
	 *
	 * @since 1.0.0
	 *
	 * @param array $updated_list API data for list.
	 * @return mixed Current connect ctct list.
	 */
	public function delete_list( array $updated_list = [] ) {

		if ( ! isset( $updated_list['id'] ) ) {
			return false;
		}

		$list = false;

		try {
			$list = $this->cc()->delete_list( $updated_list['id'] );
			if ( array_key_exists( 'error_key', $list ) && 'unauthorized' === $list['error_key'] ) {
				$this->refresh_token();
				$list = $this->cc()->delete_list( $updated_list['id'] );
			}
		} catch ( CtctException $ex ) {
			add_filter( 'constant_contact_force_logging', '__return_true' );
			$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$errors       = $ex->getErrors();
			$our_errors[] = $extra . ' - ' . $errors[0]->error_key . ' - ' . $errors[0]->error_message;
			$this->log_errors( $our_errors );
			constant_contact_forms_maybe_set_exception_notice( $ex );
		} catch ( Exception $ex ) {
			$error                = new stdClass();
			$error->error_key     = get_class( $ex );
			$error->error_message = $ex->getMessage();

			add_filter( 'constant_contact_force_logging', '__return_true' );
			constant_contact_forms_maybe_set_exception_notice( $ex );

			$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$our_errors[] = $extra . ' - ' . $error->error_key . ' - ' . $error->error_message;
			$this->log_errors( $our_errors );
		}

		return $list;
	}

	/**
	 * Create a new contact or update an existing contact.
	 * This method uses the email_address string value you include in the
	 * request body to determine if it should create an new contact or update
	 * an existing contact.
	 *
	 * @since 1.0.0
	 * @since 1.3.0 Added $form_id parameter.
	 *
	 * @param array $new_contact New contact data.
	 * @param int   $form_id     ID of the form being processed.
	 * @return array Current connect contact.
	 */
	public function add_contact( $new_contact = [], $form_id = 0 ) {

		if ( ! isset( $new_contact['email'] ) ) {
			return [];
		}

		$email = sanitize_email( $new_contact['email'] );
		// Set our list data. If we didn't get passed a list and got this far, just generate a random ID.
		$list = $new_contact['list'] ?? 'cc_' . wp_generate_password( 15, false );

		$return_contact = false;

		try {

			// Remove ctct-instance if present to avoid errors.
			if ( array_key_exists( 'ctct-instance', $new_contact ) ) {
				unset( $new_contact['ctct-instance'] );
			}

			$return_contact = $this->create_update_contact( $list, $email, $new_contact, $form_id );
			if ( array_key_exists( 'error_key', $return_contact ) && 'unauthorized' === $return_contact['error_key'] ) {
				$this->refresh_token();

				$return_contact = $this->create_update_contact( $list, $email, $new_contact, $form_id );
				if ( array_key_exists( 'error_key', $return_contact ) ) {
					// At this point, something is likely going on,
					// so after the 2nd attempt, we will log the attempt for later.
					$this->log_missed_api_request(
						'contact_add_update',
						[
							'list'    => $list,
							'email'   => $email,
							'contact' => $new_contact,
							'form_id' => $form_id
						]
					);
					constant_contact_maybe_log_it( 'API', 'A failed API attempt was caught and will be retried after reconnection.' );
				}
			}

		} catch ( CtctException $ex ) {
			add_filter( 'constant_contact_force_logging', '__return_true' );
			$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$errors       = $ex->getErrors();
			$our_errors[] = $extra . ' - ' . $errors[0]->error_key . ' - ' . $errors[0]->error_message;
			$this->log_errors( $our_errors );
			constant_contact_forms_maybe_set_exception_notice( $ex );
		} catch ( Exception $ex ) {
			$error                = new stdClass();
			$error->error_key     = get_class( $ex );
			$error->error_message = $ex->getMessage();

			add_filter( 'constant_contact_force_logging', '__return_true' );
			constant_contact_forms_maybe_set_exception_notice( $ex );

			$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$our_errors[] = $extra . ' - ' . $error->error_key . ' - ' . $error->error_message;
			$this->log_errors( $our_errors );
		}

		if (
			is_array( $return_contact ) &&
			array_key_exists( 'action', $return_contact ) &&
			in_array(
				$return_contact['action'],
				[
					'created',
					'updated'
				],
				true
			)
		) {
			$new_contact = $this->clear_email( $new_contact );
			$new_contact = $this->clear_phone( $new_contact );
			$new_contact = $this->clear_hcaptcha( $new_contact );
			constant_contact_maybe_log_it( 'API', 'Submitted contact data', $new_contact );
		}

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
				$email_parts           = explode( '@', $contact_value );
				$clean[ $contact_key ] = implode( '@', [ '***', $email_parts[1] ] );
			} else {
				$clean[ $contact_key ] = $contact_value;
			}
		}
		return $clean;
	}

	/**
	 * Obfuscate phone numbers.
	 *
	 * @author Scott Anderson <scott.anderson@webdevstudios.com>
	 * @since 1.13.0
	 *
	 * @param array $contact Contact data.
	 * @return array
	 */
	private function clear_phone( array $contact ) {
		$clean = $contact;
		foreach ( $contact as $contact_key => $contact_value ) {
			if ( is_array( $contact_value ) && ! empty( $contact_value['key'] ) && $contact_value['key'] === 'phone_number' ) {
				$clean[ $contact_key ]['val'] = '***-***-****';
			}
		}

		return $clean;
	}

	/**
	 * Remove hCaptcha data from logged data.
	 *
	 * @since 2.9.0
	 *
	 * @param array $contact Contact data.
	 * @return array
	 */
	private function clear_hcaptcha( array $contact ) {
		if ( array_key_exists( 'h-captcha-response', $contact ) ) {
			unset( $contact['h-captcha-response'] );
		}

		return $contact;
	}

	/**
	 * Helper method to update contact.
	 *
	 * @since 1.0.0
	 * @since 1.3.0 Added $form_id parameter.
	 *
	 * @throws CtctException API exception?
	 *
	 * @param string|array $list      List name(s).
	 * @param array        $user_data User data.
	 * @param string       $email   email to be updated.
	 * @param string       $form_id   Form ID being processed.
	 * @return mixed                  Response from API.
	 */
	public function create_update_contact( $list, $email, $user_data, $form_id ) {

		$contact = new Contact();

		$contact->email_address = sanitize_text_field( $email );
		unset( $contact->{"source"} );
		if ( ! property_exists( $contact, 'list_memberships' ) ) {
			$contact->list_memberships = [];
		}
		if ( property_exists( $contact, 'list_memberships' ) && ! is_array( $contact->list_memberships ) ) {
			$contact->list_memberships = (array) $contact->list_memberships;
		}
		$this->add_to_list( $contact, $list );

		try {
			$contact = $this->set_contact_properties( $contact, $user_data, $form_id, true );
		} catch ( CtctException $ex ) {
			add_filter( 'constant_contact_force_logging', '__return_true' );
			$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$errors       = $ex->getErrors();
			$our_errors[] = $extra . ' - ' . $errors[0]->error_key . ' - ' . $errors[0]->error_message;
			$this->log_errors( $our_errors );
			constant_contact_forms_maybe_set_exception_notice( $ex );
		} catch ( Exception $ex ) {
			$error                = new stdClass();
			$error->error_key     = get_class( $ex );
			$error->error_message = $ex->getMessage();

			add_filter( 'constant_contact_force_logging', '__return_true' );
			constant_contact_forms_maybe_set_exception_notice( $ex );

			$extra        = constant_contact_location_and_line( __METHOD__, __LINE__ );
			$our_errors[] = $extra . ' - ' . $error->error_key . ' - ' . $error->error_message;
			$this->log_errors( $our_errors );
		}

		$new_contact = $this->cc()->create_update_contact(
			(array) $contact
		);

		if ( $this->has_note( $user_data ) ) {
			$fetched_contact                  = $this->cc()->get_contact( $new_contact['contact_id'], [ 'include' => 'notes' ] );
			$note_content                     = $this->get_note_content( $user_data );
			$fetched_contact['notes'][]       = [ 'content' => $note_content ];
			$fetched_contact['update_source'] = 'Contact';
			$this->cc()->add_note( $fetched_contact );
		}

		return $new_contact;
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
		$streets   = [];
		if ( ! $updated ) {
			$contact->notes = [];
		}

		foreach ( $user_data as $original => $value ) {
			$key   = sanitize_text_field( $value['key'] ?? false );
			$value = sanitize_text_field( $value['val'] ?? false );

			if ( ! $key || ! $value ) {
				continue;
			}

			switch ( $key ) {
				case 'email':
				case 'custom_text_area':
				case 'lists':
				case 'h-captcha-response':
				case 'g-captcha-response':
					// Do nothing, as we already captured or handled elsewhere.
					break;
				case 'phone_number':
					$contact->phone_number = $value;
					break;
				case 'company':
					$contact->company_name = $value;
					break;
				case 'street_address':
				case 'line_2_address':
				case 'city_address':
				case 'state_address':
				case 'zip_address':
				case 'country_address':
					if ( null === $address ) {
						$address = [];
					}

					$address['kind'] = 'home';

					switch ( $key ) {
						case 'street_address':
						case 'line_2_address':
							$streets[] = $value;
							break;
						case 'city_address':
							$address['city'] = $value;
							break;
						case 'state_address':
							$address['state']   = $value;
							if ( empty( $address['country'] ) ) {
								$address['country'] = 'United States';
							}
							break;
						case 'zip_address':
							$address['postal_code'] = $value;
							break;
						case 'country_address':
							$address['country'] = $value;
					}
					break;
				case 'birthday_month':
				case 'birthday_day':
				case 'birthday_year':
				case 'anniversery_day':
				case 'anniversary_month':
				case 'anniversary_year':
				case 'website':
				case 'custom':
					// Dont overload custom fields.
					if ( $count > 25 ) {
						break;
					}

					// Retrieve our original label to send with API request.
					$original_field_data = $this->plugin->get_process_form()->get_original_fields( $form_id );
					$custom_field_name   = '';
					$should_include      = apply_filters( 'constant_contact_include_custom_field_label', false, $form_id );
					$custom_field        = ( $original_field_data[ $original ] );
					$new_custom_field    = '';
					if ( false !== strpos( $original, 'custom___' ) && $should_include ) {
						$custom_field_name .= $custom_field['name'] . ': ';
					}

					if ( ! $this->cc()->custom_field_exists( $custom_field['name'] ) ) {
						$new_custom_field = $this->cc()->add_custom_field( [
							'label' => $custom_field['name'],
							'type'  => 'string',
						] );
					}

					if ( ! empty( $new_custom_field ) ) {
						$contact->custom_fields[] = [
							'custom_field_id' => $new_custom_field['custom_field_id'],
							'value'           => $value
						];
					} else {
						$custom_field = $this->cc()->get_custom_field_by_name( $custom_field['name'] );

						$contact->custom_fields[] = [
							'custom_field_id' => $custom_field['custom_field_id'],
							'value'           => $value,
						];
					}

					$count++;
					break;
				default:
					try {
						$contact->$key = $value;
					} catch ( Exception $e ) {
						$errors   = [];
						$extra    = constant_contact_location_and_line( __METHOD__, __LINE__ );
						$errors[] = $extra . $e->getErrors();
						$this->log_errors( $errors );
						constant_contact_forms_maybe_set_exception_notice( $e );
						break;
					}

					break;
			} // End switch.
		} // End foreach.

		if ( ! empty( $streets ) ) {
			$address['street'] = implode( ', ', $streets );
		}

		if ( null !== $address ) {
			$contact->street_address = (object) $address;
		}

		return $contact;
	}

	/**
	 * Pushes all error to api_error_message.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception Throws Exception if encountered while attempting to log errors.
	 *
	 * @param array $errors Errors from API.
	 */
	public function log_errors( $errors ) {
		if ( is_array( $errors ) ) {
			foreach ( $errors as $error ) {
				constant_contact_maybe_log_it(
					'API',
					$error
				);
			}
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

		if ( constant_contact()->get_connect()->e_get( '_ctct_access_token' ) ) {
			$token = constant_contact()->get_connect()->e_get( '_ctct_access_token' ) ? true : false;
		}

		return $token;
	}

	/**
	 * Helper method to output a link for our settings page tabs.
	 *
	 * @since 2022-10-24
	 * @return string Settings tab URL.
	 */
	public function get_settings_link( $settings_tab = 'ctct_options_settings_general' ) {

		return add_query_arg(
			[
				'post_type' => 'ctct_forms',
				'page' => sanitize_text_field( $settings_tab ),
			],
			admin_url( 'edit.php' )
		);
	}

	/**
	 * Helper method to output a link for our connect modal.
	 *
	 * @since 1.0.0
	 *
	 * @return string Signup URL.
	 */
	public function get_signup_link() {
		return 'https://www.constantcontact.com/signup';
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
			'name'    => empty( $account_info->organization_name ) ? constant_contact_get_option( '_ctct_disclose_name', '' ) : $account_info->organization_name,
			'address' => constant_contact_get_option( '_ctct_disclose_address', '' ),
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

	/**
	 * Generate code_verifier and code_challenge for rfc7636 PKCE.
	 * https://datatracker.ietf.org/doc/html/rfc7636#appendix-B
	 *
	 * @return array [code_verifier, code_challenge].
	 */
	private function code_challenge( ?string $code_verifier = null ): array {
		$gen = static function () {
			$strings = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-._~';
			$length  = random_int( 43, 128 );

			for ( $i = 0; $i < $length; $i++ ) {
				yield $strings[ random_int( 0, 65 ) ];
			}
		};

		$code = $code_verifier ?? implode( '', iterator_to_array( $gen() ) );

		if ( ! \preg_match( '/[A-Za-z0-9-._~]{43,128}/', $code ) ) {
			return [ '', '' ];
		}

		return [ $code, $this->base64url_encode( pack( 'H*', hash( 'sha256', $code ) ) ) ];
	}

	private function base64url_encode( string $data ): string {
		return rtrim( strtr( base64_encode( $data ), '+/', '-_' ), '=' );
	}

	public function session( string $key, ?string $value ) {
		if ( $this->session_callback ) {
			return call_user_func( $this->session_callback, $key, $value );
		}
		if ( null === $value ) {
			$value = get_user_meta( $this->this_user_id, $key, true );
			delete_user_meta( $this->this_user_id, $key, $value );

			return $value;
		}

		update_user_meta( $this->this_user_id, $key, $value );

		return $value;
	}

	/**
	 * Generate the URL an account owner would use to allow your app
	 * to access their account.
	 *
	 * After visiting the URL, the account owner is prompted to log in and allow your app to access their account.
	 * They are then redirected to your redirect URL with the authorization code appended as a query parameter. e.g.:
	 * http://localhost:8888/?code={authorization_code}
	 */
	public function get_authorization_url(): string {

		$auth_url = get_option( 'ctct_auth_url' );

		if ( $auth_url  ) {
			return $auth_url;
		}

		$scopes                           = implode( '+', array_keys( $this->scopes ) );
		[$code_verifier, $code_challenge] = $this->code_challenge();

		$state = bin2hex( random_bytes( 8 ) );

		update_option( 'CtctConstantContactState', $state );

		$params = [
			'client_id'             => $this->client_api_key,
			'redirect_uri'          => $this->redirect_URI,
			'response_type'         => 'code',
			'code_challenge'        => $code_challenge,
			'code_challenge_method' => 'S256',
			'state'                 => $state,
			'scope'                 => $scopes,
		];

		// Store generated random state and code challenge based on RFC 7636
		// https://datatracker.ietf.org/doc/html/rfc7636#section-6.1
		update_option( 'CtctConstantContactcode_verifier', $code_verifier );

		$url = add_query_arg( $params, $this->authorize_url );

		update_option( 'ctct_auth_url', $url );

		return $url;
	}

	/**
	 * Add contact to one or more lists.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.9.0
	 * @todo Update addList to use v3
	 *
	 * @param  Contact      $contact Contact object.
	 * @param  string|array $list    Single list ID or array of lists.
	 * @return void
	 */
	private function add_to_list( $contact, $list ) {
		if ( empty( $list ) ) {
			return;
		}

		$list = is_array( $list ) ? $list : [ $list ];

		foreach ( $list as $list_id ) {
			$contact->list_memberships[] = esc_attr( $list_id );
		}
	}

	/**
	 * Exchange an authorization code for an access token.
	 *
	 * Make this call by passing in the code present when the account owner is redirected back to you.
	 * The response will contain an 'access_token' and 'refresh_token'
	 *
	 * @param array of get parameters passed to redirect URL
	 */
	public function acquire_access_token(): bool {

		$code_state = (string) constant_contact_get_option( '_ctct_form_state_authcode', '' );

		parse_str( $code_state, $parsed_code_state );
		$parsed_code_state = array_values( $parsed_code_state );

		if ( empty( $parsed_code_state[0] ) || empty( $parsed_code_state[1] ) ) {
			$this->status_code = 0;
			$this->last_error  = 'Invalid state or auth code!';
			constant_contact_maybe_log_it( 'Error: ', $this->last_error );
			return false;
		} else {
			$code  = $parsed_code_state[0];
			$state = $parsed_code_state[1];
		}

		$expected_state = get_option( 'CtctConstantContactState' );

		if ( ( $state ?? 'undefined' ) != $expected_state ) {
			$this->status_code = 0;
			$this->last_error  = 'state is not correct';
			constant_contact_maybe_log_it( 'Error: ', $this->last_error );
			return false;
		}
		// Create full request URL
		$body = [
			'client_id'    => $this->client_api_key,
			'code'         => $code,
			'redirect_uri' => $this->redirect_URI,
			'grant_type'   => 'authorization_code',
		];

		$body['code_verifier'] = get_option( 'CtctConstantContactcode_verifier' );

		$headers = $this->set_authorization();

		$url = $this->oauth2_url;

		$options = [
			'body'    => $body,
			'headers' => $headers,
		];

		// This will be either true or false.
		$result = $this->exec( $url, $options );

		if ( false === $result ) {
			constant_contact_set_needs_manual_reconnect( 'true' );
		} else {

			/**
			 * Fires after successful access token acquisition.
			 *
			 * @since 2.3.0
			 */
			do_action( 'ctct_access_token_acquired' );

			constant_contact_set_needs_manual_reconnect( 'false' );
		}


		return $result;
	}

	/**
	 * Refresh the access token.
	 */
	public function refresh_token(): bool {

		// Force prevent any further attempts until humans interject.
		if ( constant_contact_get_needs_manual_reconnect() ) {
			return false;
		}

		constant_contact_maybe_log_it( 'Refresh Token:', 'Refresh token triggered' );

		// Create full request URL
		$body = [
			'client_id'     => $this->client_api_key,
			'refresh_token' => constant_contact()->get_connect()->e_get( '_ctct_refresh_token' ),
			'redirect_uri'  => $this->redirect_URI,
			'grant_type'    => 'refresh_token',
		];

		$url     = $this->oauth2_url;
		$headers = $this->set_authorization();

		$options = [
			'body'    => $body,
			'headers' => $headers,
		];

		$result = $this->exec( $url, $options );

		if ( false === $result ) {
			constant_contact_set_needs_manual_reconnect( 'true' );
		} else {
			update_option( 'ctct_access_token_timestamp', time() );
			constant_contact_set_needs_manual_reconnect( 'false' );
		}

		return $result;
	}

	private function set_authorization(): array {

		// Set authorization header
		// Make string of "API_KEY:SECRET"
		$auth = $this->client_api_key;
		// Base64 encode it
		$credentials = base64_encode( $auth );
		// Create and set the Authorization header to use the encoded credentials
		$headers = [ 'Authorization: Basic ' . $credentials, 'cache-control: no-cache' ];

		return $headers;
	}

	private function exec( $url, $options ): bool {
		$response = wp_safe_remote_post( $url, $options );

		$this->last_error  = '';
		$this->status_code = 0;

		if ( ! is_wp_error( $response ) ) {

			$data = json_decode( $response['body'], true );

			// check if the body contains error
			if ( isset( $data['error'] ) ) {
				if ( 'invalid_grant' === $data['error'] ) {
					$this->api_errors_admin_email();
				}
				$this->last_error = $data['error'] . ': ' . ( $data['error_description'] ?? 'Undefined' );
				constant_contact_maybe_log_it( 'Error: ', $this->last_error );
				return false;
			}

			if ( ! empty( $data['access_token'] ) ) {

				constant_contact_maybe_log_it( 'Refresh Token:', 'Old Refresh Token: ' . $this->obfuscate_api_data_item( $this->refresh_token ) );
				constant_contact_maybe_log_it( 'Access Token:', 'Old Access Token: ' . $this->obfuscate_api_data_item( $this->access_token ) );

				constant_contact()->get_connect()->e_set( '_ctct_access_token', $data['access_token'] );
				constant_contact()->get_connect()->e_set( '_ctct_refresh_token', $data['refresh_token'] );
				constant_contact()->get_connect()->e_set( '_ctct_expires_in', (string) $data['expires_in'] );

				$this->access_token  = $data['access_token'] ?? '';
				$this->refresh_token = $data['refresh_token'] ?? '';
				$this->expires_in    = $data['expires_in'] ?? '';

				delete_option( 'ctct_auth_url' );

				constant_contact_maybe_log_it( 'Refresh Token:', 'Refresh token successfully received' );
				constant_contact_maybe_log_it( 'Refresh Token:', 'New Refresh Token: ' . $this->obfuscate_api_data_item( $this->refresh_token ) );
				constant_contact_maybe_log_it( 'Access Token:', 'New Access Token: ' . $this->obfuscate_api_data_item( $this->access_token ) );
				constant_contact_maybe_log_it( 'Expires in:', 'Expiry: ' . $this->expires_in );

				return isset( $data['access_token'], $data['refresh_token'] );
			}
		} else {
			$this->status_code = 0;
			$this->last_error  = $response->get_error_message();
			constant_contact_maybe_log_it( 'Error: ', $this->last_error );
		}

		return false;
	}

	private function obfuscate_api_data_item( $data_item ) {
		$start = substr( $data_item, 0, 8 );
		return $start . '***';
	}

	/**
	 * Check if a submission has note data in place.
	 *
	 * @since 2.0.0
	 *
	 * @param array $submission_data Array of form data.
	 * @return bool
	 */
	private function has_note( $submission_data ) {
		if ( ! is_array( $submission_data ) ) {
			return false;
		}

		$keys = array_keys( $submission_data );
		$has_text_area = false;
		foreach( $keys as $key ) {
			if (
				false !== strpos( $key, 'custom_text_area' ) &&
				! empty( $submission_data[ $key ]['val'] )
			) {
				$has_text_area = true;
				break;
			}
		}
		return $has_text_area;
	}

	/**
	 * Get the content of the first found note submitted to a form.
	 *
	 * @since 2.0.0
	 *
	 * @param $submission_data
	 * @return string
	 */
	private function get_note_content( $submission_data ) {
		$note = '';
		foreach ( $submission_data as $key => $data ) {
			if ( false !== strpos( $key, 'custom_text_area' ) ) {
				$note .= $data['val'];
				break;
			}
		}
		return $note;
	}

	/**
	 * Check if our current access token is expired.
	 *
	 * Based on access token issued timestamp + expires in timestamp and current time.
	 *
	 * @since 2.2.0
	 *
	 * @return bool
	 */
	private function access_token_maybe_expired() {

		$issued_time = get_option( 'ctct_access_token_timestamp', '' );
		if ( empty( $issued_time ) ) {
			return true;
		}

		$expires_in = constant_contact()->get_connect()->e_get( '_ctct_expires_in' );
		if ( ! empty( $this->expires_in ) ) {
			// Prioritize our property over the option. If this is set, it's probably fresher.
			$expires_in = $this->expires_in;
		}
		$current_time = time();
		$expiration_time = $issued_time + $expires_in;

		// If we're currently above the expiration time, we're expired.
		return $current_time >= $expiration_time;
	}

	/**
	 * Logs a missed API request to our overall log of missed requests.
	 *
	 * @since 2.3.0
	 *
	 * @param string $type    API request type.
	 * @param array  $request The request.
	 */
	public function log_missed_api_request( string $type, array $request ) {
		$missed_api_requests            = get_option( 'ctct_missed_api_requests', [] );
		$missed_api_requests[][ $type ] = $request;
		update_option( 'ctct_missed_api_requests', $missed_api_requests );

		$this->api_errors_admin_email( $request['form_id'] );
	}

	/**
	 * Processes the list of missed API requests after successful reconnect to the API.
	 *
	 * @since 2.3.0
	 */
	public function clear_missed_api_requests() {
		// @TODO Make this compatible with other interactions besides just contact adds.
		// For now we can focus on just contact.

		$missed_api_requests = get_option( 'ctct_missed_api_requests', [] );
		if ( empty( $missed_api_requests ) ) {
			return;
		}

		$result = [];
		foreach ( $missed_api_requests as $key => $request ) {
			foreach ( $request as $type => $the_request ) {
				switch ( $type ) {
					case 'list_add':

						break;
					case 'contact_add_update':
						$args = wp_parse_args(
							$the_request,
							[
								'list'    => '',
								'email'   => '',
								'contact' => '',
								'form_id' => ''
							]
						);

						$result = $this->create_update_contact(
							$args['list'],
							$args['email'],
							$args['contact'],
							$args['form_id']
						);
						break;
				}
				if ( ! empty( $result ) && ! array_key_exists( 'error_key', $result ) ) {
					unset( $missed_api_requests[ $key ] );
				}
			}
		}
		update_option( 'ctct_missed_api_requests', $missed_api_requests );
	}

	/**
	 * Email site administrator email and any custom email address set to be notified of new entries.
	 * This method is meant to notify that there are API errors being detected, and that
	 * a new connection should be established. This will be after temporarily storing a
	 * form submission that will be re-processed once new tokens are established or if API
	 * responses are returning errors. We are not going to worry about listing the form name,
	 * because all forms would be affected.
	 *
	 * @since 2.7.0
	 * @since 2.10.0 Re-using for general API request issues.
	 *
	 * @param int $form_id Form ID to use.
	 */
	protected function api_errors_admin_email( int $form_id = 0 ) {
		$send_to_addresses[] = get_option( 'admin_email' );
		if ( $form_id ) {
			$custom = get_post_meta( $form_id, '_ctct_email_settings', true );
		}
		if ( ! empty( $custom ) ) {
			$send_to_addresses[] = $custom;
		}
		$title = get_bloginfo( 'blogname' );

		$content = esc_html__(
			'We have detected connection errors for your site, %s%s%s. Potentially a failed signup has been detected and will be retried automatically once a new connection has been established. Otherwise, issues with token refreshing have been detected. Please visit your site and perform the steps to reconnect the plugin at your earliest convenience.',
			'constant-contact-forms'
		);
		$content = sprintf(
			$content,
			sprintf(
				'<a href="%s">',
				get_bloginfo( 'url' )
			),
			$title,
			'</a>'
		);
		add_filter( 'wp_mail_content_type', [ $this, 'set_email_type' ] );
		foreach ( $send_to_addresses as $address ) {
			wp_mail(
				$address,
				/**
				 * Filters the email subject to be sent to an admin.
				 *
				 * @since 2.7.0
				 *
				 * @param string $value Constructed email subject.
				 * @param string $value Constant Contact Form ID.
				 */
				apply_filters( 'constant_contact_api_errors_admin_email_subject', esc_html__( 'Detected Constant Contact Forms issues.', 'constant-contact-forms' ), $form_id ),
				$content
			);
		}
		remove_filter( 'wp_mail_content_type', [ $this, 'set_email_type' ] );
	}

	/**
	 * Set our email's content type.
	 *
	 * @since 2.7.0
	 *
	 * @return string
	 */
	public function set_email_type() {
		return 'text/html';
	}
}

/**
 * Helper function to get/return the ConstantContact_API object.
 *
 * @since 1.0.0
 * @deprecated NEXT
 *
 * @return object ConstantContact_API
 */
function constantcontact_api() {
	_deprecated_function( __FUNCTION__, 'NEXT', 'constant_contact()->get_api()' );
	return constant_contact()->get_api();
}

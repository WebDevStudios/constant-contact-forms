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
	}

	/**
	 * Get instance of .ConstantContact.
	 *
	 * @since 1.0.0
	 */
	public function cc() {
		return new ConstantContact( $this->get_api_token( 'CTCT_APIKEY' ) );
	}

	/**
	 * Returns api token string to access api
	 *
	 * @since  1.0.0
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
	 * @return array current connect ctct lists
	 */
	public function add_list( $new_list = array() ) {

		if ( empty( $new_list ) ) { return null; }

		try {
			$list = $this->cc()->listService->getList( $this->get_api_token(), $new_list['id'] );
			return $List;
		} catch ( CtctException $ex ) {
			foreach ( $ex->getErrors() as $error ) {
				$this->api_error_message( $error );
			}
		}

		if ( ! isset( $list ) ) {

			try {
				$list = new ContactList();
				$list->id = '234567';
				$list->name = $new_list['name'];
				$list->status = 'HIDDEN';
				$returnList = $this->cc()->listService->addList( $this->get_api_token(), $list );
			} catch ( CtctException $ex ) {
				foreach ( $ex->getErrors() as $error ) {
					$this->api_error_message( $error );
				}
			}
		}
		return $returnList;


	}

	/**
	 * update List from the connected CTCT account
	 *
	 * @since  1.0.0
	 * @return array current connect ctct list
	 */
	public function update_list( $updated_list = array() ) {

		try {
			$list = new ContactList();
			$list->id = $updated_list['id'];
			$list->name = $updated_list['name'];
			$list->status = 'HIDDEN';
			$returnList = $this->cc()->listService->updateList( $this->get_api_token(), $list );
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
	 * delete List from the connected CTCT account
	 *
	 * @since  1.0.0
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
	        // check to see if a contact with the email address already exists in the account
	        $response = $this->cc()->contactService->getContacts( $this->get_api_token(), array("email" => $new_contact['email'] ) );

	        // create a new contact if one does not exist
	        if ( empty( $response->results ) ) {
	            $action = "Creating Contact";

	            $contact = new Contact();
	            $contact->addEmail($new_contact['email']);
	            $contact->addList($new_contact['list']);
	            $contact->first_name = $new_contact['first_name'];
	            $contact->last_name = $new_contact['last_name'];

	            /*
	             * The third parameter of addContact defaults to false, but if this were set to true it would tell Constant
	             * Contact that this action is being performed by the contact themselves, and gives the ability to
	             * opt contacts back in and trigger Welcome/Change-of-interest emails.
	             *
	             * See: http://developer.constantcontact.com/docs/contacts-api/contacts-index.html#opt_in
	             */
	            $returnContact = $this->cc()->contactService->addContact( $this->get_api_token(), $contact );

	            // update the existing contact if address already existed
	        } else {
	            $action = "Updating Contact";
	            $contact = $response->results[0];
	            if ( $contact instanceof Contact ) {
	                //$contact->addList($new_contact['list']);
	                $contact->first_name = $new_contact['first_name'];
	                $contact->last_name = $new_contact['last_name'];

	                /*
	                 * The third parameter of updateContact defaults to false, but if this were set to true it would tell
	                 * Constant Contact that this action is being performed by the contact themselves, and gives the ability to
	                 * opt contacts back in and trigger Welcome/Change-of-interest emails.
	                 *
	                 * See: http://developer.constantcontact.com/docs/contacts-api/contacts-index.html#opt_in
	                 */
	                $returnContact = $this->cc()->contactService->updateContact( $this->get_api_token(), $contact );
	            } else {
	                $e = new CtctException();
	                $e->setErrors(array("type", "Contact type not returned"));
	                throw $e;
	            }
	        }

		} catch ( CtctException $ex ) {
			foreach ( $ex->getErrors() as $error ) {
				$this->api_error_message( $error );
			}
			if ( ! isset( $returnContact ) ) {
				$returnContact = null;
			}
		}
		return $returnContact;
	}

	/**
	 * Process api error response
	 *
	 * @since  1.0.0
	 * @param  array $error api error repsonse
	 * @return void
	 */
	private function api_error_message( $error ) {

		switch( $error->error_key ) {
			case 'http.status.authentication.invalid_token':
				$this->access_token = false;
				return __( 'Your API access token is invalid. Reconnect to Constant Contact to receive a new token.', constant_contact()->text_domain );
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
	d( constantcontact_api()->get_account_info() );
	d( constantcontact_api()->get_contacts() );
	d( constantcontact_api()->get_lists() );

	// d( constantcontact_api()->add_list(
	// 	array(
	// 		'id' => '234567',
	// 		'name' => 'Test List',
	// 	)
	// ) );

	//  d( constantcontact_api()->add_contact(
	// 	 array(
	// 		 'email' => 'cgriswald@wallyworld.com',
	// 		 'list' => '',
	// 		 'first_name' => 'Clark W. dddd',
	// 		 'last_name' => 'Griswald',
	// 	 )
	//  ) );
}

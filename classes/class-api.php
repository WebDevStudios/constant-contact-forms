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


	/**
	 * Add List to the connected CTCT account
	 *
	 * @since  1.0.0
	 * @return array current connect ctct lists
	 */
	public function add_list( $new_list = array() ) {

		if ( empty( $new_list ) ) { return null; }

		try {
			$list = $this->cc->listService->getList( $this->token, $new_list['id'] );
			return $List;
		} catch ( CtctException $ex ) {
			foreach ( $ex->getErrors() as $error ) {
				//return $error;
			}
		}

		if ( ! isset( $list ) ) {

			try {
				$list = new ContactList();
				$list->id = '234567';
				$list->name = $new_list['name'];
				$list->status = 'HIDDEN';
				$returnList = $this->cc->listService->addList( $this->token, $list );
			} catch ( CtctException $ex ) {
				foreach ( $ex->getErrors() as $error ) {
					return $error;
				}
			}
		}
		return $returnList;


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
	        $response = $this->cc->contactService->getContacts( $this->token, array("email" => $new_contact['email'] ) );

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
	            $returnContact = $this->cc->contactService->addContact( $this->token, $contact );

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
	                $returnContact = $this->cc->contactService->updateContact( $this->token, $contact );
	            } else {
	                $e = new CtctException();
	                $e->setErrors(array("type", "Contact type not returned"));
	                throw $e;
	            }
	        }

		} catch ( CtctException $ex ) {
			foreach ( $ex->getErrors() as $error ) {
				return $error;
			}
			if ( ! isset( $returnContact ) ) {
				$returnContact = null;
			}
		}
		return $returnContact;
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
	// var_dump( constantcontact_api()->get_account_info() );
	// var_dump( constantcontact_api()->get_contacts() );
	//var_dump( constantcontact_api()->get_lists() );

	var_dump( constantcontact_api()->add_list(
		array(
			'id' => '234567',
			'name' => 'Test List',
		)
	) );

	//  var_dump( constantcontact_api()->add_contact(
	// 	 array(
	// 		 'email' => 'cgriswald@wallyworld.com',
	// 		 'list' => '',
	// 		 'first_name' => 'Clark W.',
	// 		 'last_name' => 'Griswald',
	// 	 )
	//  ) );
}

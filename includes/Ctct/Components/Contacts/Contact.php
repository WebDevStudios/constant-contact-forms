<?php
namespace Ctct\Components\Contacts;

use Ctct\Components\Component;

/**
 * Represents a single Contact in Constant Contact
 *
 * @package        Components
 * @subpackage     Contacts
 * @author         Constant Contact
 */
class Contact extends Component {

	/**
	 * First name of the contact
	 *
	 * @var string
	 */
	public $first_name;

	/**
	 * Last name of the contact
	 *
	 * @var string
	 */
	public $last_name;

	/**
	 * The job title of the contact
	 *
	 * @var string
	 */
	public $job_title;

	/**
	 * Company name this contact works for
	 *
	 * @var string
	 */
	public $company_name;

	/**
	 * Array of custom fields associated with this contact
	 *
	 * @var CustomField[]
	 */
	public $custom_fields = [];

	/**
	 * Factory method to create a Contact object from an array
	 *
	 * @param array $props - Associative array of initial properties to set
	 * @return Contact
	 */
	public static function create( array $props ) {
		$contact             = new Contact();
		$contact->first_name = parent::getValue( $props, 'first_name' );
		$contact->last_name  = parent::getValue( $props, 'last_name' );

		$contact->job_title   = parent::getValue( $props, 'job_title' );

		$contact->company_name = parent::getValue( $props, 'company_name' );

		if ( isset( $props['custom_fields'] ) ) {
			foreach ( $props['custom_fields'] as $custom_field ) {
				$contact->custom_fields[] = CustomField::create( $custom_field );
			}
		}

		return $contact;
	}

	/**
	 * Add a ContactList
	 *
	 * @param mixed $contactList - ContactList object or contact list id
	 */
	public function addListId( $contactListId ) {
		$this->list_memberships[] = $contactListId;
	}

	/**
	 * Add an EmailAddress
	 *
	 * @param mixed $emailAddress - EmailAddress object or email address
	 */
	public function addEmailAddress( $emailAddress ) {

		$email['address'] = $emailAddress;

		$this->email_address[] = $email;
	}

	/**
	 * Add a custom field to the contact object
	 *
	 * @param CustomField $customField - custom field to add to the contact
	 */
	public function addCustomField( CustomField $customField ) {
		$this->custom_fields[] = $customField;
	}

	public function toJson() {
		unset( $this->last_update_date );
		return json_encode( $this );
	}
}

<?php
namespace Ctct\Components\Contacts;

use Ctct\Components\Component;

/**
 * Represents a Contact Note
 *
 * @package        Components
 * @subpackage     Contacts
 * @author         Constant Contact
 */
class Note extends Component {

	/**
	 * Id of the note
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Content of the note
	 *
	 * @var string
	 */
	public $note;

	/**
	 * Date the note was created
	 *
	 * @var string
	 */
	public $created_date;

	public $note_id;
	public $content;
	public $created_at;

	/**
	 * Factory method to create a Note object from an array
	 *
	 * @param array $props - Associative array of initial properties to set
	 * @return Note
	 */
	public static function create( array $props ) {
		$note               = new Note();
		$note->id           = parent::getValue( $props, 'id' );
		$note->note         = parent::getValue( $props, 'note' );
		$note->created_date = parent::getValue( $props, 'created_date' );

		$note->note_id    = parent::getValue( $props, 'note_id' );
		$note->content    = parent::getValue( $props, 'content' );
		$note->created_at = parent::getValue( $props, 'created_at' );

		return $note;
	}
}

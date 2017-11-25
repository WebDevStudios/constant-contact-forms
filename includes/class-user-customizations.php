<?php
/**
 * Constant Contact User Customizations class.
 *
 * @package    ConstantContact
 * @subpackage User Customizations
 * @author     Constant Contact
 * @since      1.3.0
 */

class ConstantContact_User_Customizations {

	/**
	 * Parent plugin class.
	 *
	 * @since 1.3.0
	 * @var object
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since 1.3.0
	 *
	 * @param object $plugin Parent plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Run our hooks.
	 *
	 * @since 1.3.0
	 */
	public function hooks() {
		add_filter( 'ctct_process_form_success', array( $this, 'process_form_success' ), 10, 2 );
		add_filter( 'constant_contact_front_form_action', array( $this, 'custom_redirect' ), 10, 2 );
	}

	/**
	 * Add our form's saved successful submission custom text.
	 *
	 * @since 1.3.0
	 *
	 * @param string $content Current success message text.
	 * @param int    $form_id Form ID.
	 * @return mixed
	 */
	public function process_form_success( $content = '', $form_id = 0 ) {
		$custom = get_post_meta( $form_id, '_ctct_form_submission_success', true );
		if ( empty( $custom ) ) {
			return $content;
		}

		return $custom;
	}

	/**
	 * Add our form's saved redirect URI value.
	 *
	 * @since 1.3.0
	 *
	 * @param string $url     Current URI to redirect user to on form submission
	 * @param int    $form_id Form ID.
	 * @return mixed
	 */
	public function custom_redirect( $url, $form_id ) {
		$custom = get_post_meta( $form_id, '_ctct_redirect_uri', true );
		if ( empty( $custom ) ) {
			return $url;
		}

		return constant_contact_clean_url( $custom );
	}
}

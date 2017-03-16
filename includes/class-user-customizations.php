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
	 * @var class
	 * @since 1.0.0
	 */
	protected $plugin = null;

	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	public function hooks() {
		add_filter( 'ctct_process_form_success', array( $this, 'process_form_success' ), 10, 2 );
		add_filter( 'constant_contact_front_form_action', array( $this, 'custom_redirect' ), 10, 2 );
	}

	public function process_form_success( $content, $form_id ) {
		$custom = get_post_meta( $form_id, '_ctct_form_submission_success', true );
		if ( empty( $custom ) ) {
			return $content;
		}

		return $custom;
	}

	public function custom_redirect( $url, $form_id ) {
		$custom = get_post_meta( $form_id, '_ctct_redirect_uri', true );
		if ( empty( $custom ) ) {
			return $url;
		}

		return $custom;
	}

}

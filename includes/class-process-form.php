<?php
/**
 * ConstantContact_Process_Form class
 *
 * @package ConstantContactProcessForm
 * @subpackage ConstantContact
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Class ConstantContact_Process_Form
 */
class ConstantContact_Process_Form {

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
	 * Process submitted form data
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function process_form() {

		// If we don't have our submitted action, just bail out
		if ( ! isset( $_POST['ctct-submitted'] ) ) {
			return;
		}

		// Verify our nonce first
		if (
		    ! isset( $_POST['ctct_form'] ) ||
		    ! wp_verify_nonce( $_POST['ctct_form'], 'ctct_submit_form' )
		) {
			// figure out a way to pass errors back
			return;
		}

		$return = array();

		// If the submit button is clicked, send the email.
		foreach ( $_POST as $key => $value ) {

			// Allow ignoring of certain keys, like our nonce.
			$ignored_keys = apply_filters( 'constant_contact_ignored_post_form_values', array(
				'ctct-submitted',
				'ctct_form',
				'_wp_http_referer',
			) );

			// if our key we're processing is in our array, ignore it
			if ( in_array( $key, $ignored_keys ) ) {
				continue;
			}

			// Add our responses to a form we can deal with shortly.
			$return[] = array(
				'key'   => esc_attr( $key ),
				'value' => esc_attr( $value ),
			);
		}

		return $return;

		// @ TODO need to replcae this to account for our dynamic form names
		if ( isset( $_POST['ctct-opti-in'] ) ) {

			if ( isset( $_POST['ctct-email'] ) && isset( $_POST['ctct-opti-in'] ) ) {
				$args = array(
					'email' => sanitize_email( ),
					'list' => sanitize_text_field( $_POST['ctct-opti-in'] ),
					'first_name' => '',
					'last_name' => '',
				);
			}

			// $contact = constantcontact_api()->add_contact( $args );

			// @ todo need to replace, as this only lets one form be submitted
			// on the site at a time
			if ( $contact ) {
				set_transient( 'ctct_form_submit_message', 'success' );
			} else {
				set_transient( 'ctct_form_submit_message', 'error' );
			}

	        // // sanitize form values
	        // $name   = isset( $_ctct_first_name ) ? $_ctct_first_name : '';
			// $subject = '';
			// $message = '';
			//
	        // // get the blog administrator's email address
	        // $to = get_option( 'admin_email' );
	        // $headers = "From: $name <$email>" . "\r\n";

	        // // If email has been process for sending, display a success message
	        // if ( wp_mail( $to, $subject, $message, $headers ) ) {
	        //     echo '<div>';
	        //     echo '<p>Thanks for contacting me, expect a response soon.</p>';
	        //     echo '</div>';
	        // } else {
	        //     echo 'An unexpected error occurred';
	        // }

		}
	}

	/**
	 * Form submit success/error messages.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function process_wrapper() {

		$status = $this->process_form();

		if ( ! $status ) {
			return false;
		}

		// @todo this needs to be replaced
		// if ( $message = get_transient( 'ctct_form_submit_message' ) ) {
		$message = 'success';

		switch ( $message ) {
			case 'success':
				$message_text = __( 'Your message has been sent!', 'constantcontact' );
				break;
			case 'error':
				$message_text = __( 'Your message failed to send!', 'constantcontact' );
				break;
		}

			$return = sprintf( '<p class="message ' . esc_attr( $message ) . '"> %s </p>', esc_attr( $message_text ) );

			// delete_transient( 'ctct_form_submit_message' );

			return $return;
		// }
	}
}

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

		// If we don't have our submitted form id, just bail out
		if ( ! isset( $_POST['ctct-id'] ) ) {
			return;
		}

		// Verify our nonce first
		if (
		    ! isset( $_POST['ctct_form'] ) ||
		    ! wp_verify_nonce( $_POST['ctct_form'], 'ctct_submit_form' )
		) {
			// figure out a way to pass errors back
			return array(
				'status' => 'named_error',
				'error'  => __( 'Nonce verification failed.', 'constantcontact' ),
			);
		}

		// Make sure we have an original form id
		$orig_form_id = absint( $_POST['ctct-id'] );
		if ( ! $orig_form_id ) {
			return array(
				'status' => 'named_error',
				'error'  => __( 'Mismatch in submitted Form ID.', 'constantcontact' ),
			);
		}

		// Make sure we have a verify value
		$form_verify = esc_attr( $_POST['ctct-verify'] );
		if ( ! $form_verify ) {
			return array(
				'status' => 'named_error',
				'error'  => __( 'Mismatch in submitted Form ID.', 'constantcontact' ),
			);
		}

		// Make sure our verify key matches our saved one
		$orig_verify = get_post_meta( $orig_form_id, '_ctct_verify_key', true );
		if ( $orig_verify != $form_verify ) {
			return array(
				'status' => 'named_error',
				'error'  => __( 'Mismatch in submitted Form ID.', 'constantcontact' ),
			);
		}

		// @TODO at this point we can process and check required fields / all fields are there
		// etc

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
			$return['values'][ esc_attr( $key ) ] = esc_attr( $value );
		}

		// @ TODO need to replcae this to account for our dynamic form names
		// if we're not processing the opt-in stuff, we can just return our data here
		if ( ! isset( $_POST['ctct-opti-in'] ) ) {
			$return['status'] = 'success';
			return $return;
		}

		// @ TODO need to replcae this to account for our dynamic form names
		// if ( isset( $_POST['ctct-opti-in'] ) ) {

		// 	if ( isset( $_POST['ctct-email'] ) && isset( $_POST['ctct-opti-in'] ) ) {
		// 		$args = array(
		// 			'email' => sanitize_email( ),
		// 			'list' => sanitize_text_field( $_POST['ctct-opti-in'] ),
		// 			'first_name' => '',
		// 			'last_name' => '',
		// 		);
		// 	}

			// $contact = constantcontact_api()->add_contact( $args );

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

		// }
	}

	/**
	 * Form submit success/error messages.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function process_wrapper() {

		// Process our data, and get our response.
		$processed = $this->process_form();

		// set up our default error
		$default_error = __( 'There was an error sending your form.', 'constantcontact' );

		// default to no status
		$status = false;

		// If we got a status back, check that in our list of returns
		if ( isset( $processed['status'] ) && $processed['status'] ) {
			$status = $processed['status'];
		}

		// switch based on our status code
		switch ( $status ) {

			// yay success
			case 'success':
				$message = __( 'Your message has been sent.', 'constantcontact' );
				break;

			// generic error
			case 'error':
				$message = $default_error;
				break;

			// named error from our process
			case 'named_error':
				$message = isset( $processed['error'] ) ? $processed['error'] : $default_error;
				break;

			// all else fails, use default
			default:
				$message = '';
				break;
		}

		return array(
			'status'  => $status,
			'message' => $message,
		);
	}
}

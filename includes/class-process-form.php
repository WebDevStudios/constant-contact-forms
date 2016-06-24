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

		// If we don't have our submitted form verify, just bail out
		if ( ! isset( $_POST['ctct-verify'] ) ) {
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

		// If the submit button is clicked, send the email.
		foreach ( $_POST as $key => $value ) {

			// Allow ignoring of certain keys, like our nonce.
			$ignored_keys = apply_filters( 'constant_contact_ignored_post_form_values', array(
				'ctct-submitted',
				'ctct_form',
				'_wp_http_referer',
				'ctct-verify',
			) );

			// if our key we're processing is in our array, ignore it
			if ( in_array( $key, $ignored_keys ) ) {
				continue;
			}

			// Add our responses to a form we can deal with shortly.
			$return['values'][] = array(
				'key'   => sanitize_text_field( $key ),
				'value' => sanitize_text_field( $value ),
			);
		}

		if ( ! isset( $return['values'] ) || ! is_array( $return['values'] ) ) {
			return;
		}

		// Check for specific validation errors
		$field_errors = $this->get_field_errors( $this->clean_values( $return['values'] ) );
		// If we got errors
		if ( is_array( $field_errors ) && ! empty( $field_errors ) ) {

			// Send back an error status, a message, the errors we found, and all orig values
			return array(
				'status'   => 'req_error',
				'errors'   => $field_errors,
				'values'   => $return['values'],
			);
		}

		// if we're not processing the opt-in stuff, we can just return our data here
		if ( ! isset( $_POST['ctct-opti-in'] ) ) {

			// at this point we can process all of our submitted values
			$this->submit_form_values( $return['values'] );

			$return['status'] = 'success';
			return $return;
		}

		// at this point we can process all of our submitted values
		$this->submit_form_values( $return['values'], true );

		$return['status'] = 'success';
		return $return;
	}

	/**
	 * Process our form values
	 *
	 * @param  array $values submitted form values
	 */
	public function submit_form_values( $values = array(), $add_to_opt_in = false ) {

		// Sanity check
		if ( ! is_array( $values ) ) {
			return;
		}

		// Clean our values
		$values = $this->clean_values( $values );

		if ( $add_to_opt_in ) {
			$this->opt_in_user( $values );
		}

		// pretty our values
		$values = $this->pretty_values( $values );

		// Format them
		$values = $this->format_values_for_email( $values );

		// Send the mail
		return $this->mail( $this->get_email(), $values );
	}

	public function opt_in_user( $values ) {

		// Set our default vars
		$email      = '';
		$first_name = '';
		$last_name  = '';

		// go through all our fields
		foreach ( $values as $val ) {

			// Make sure we have our data to check set
			$key = isset( $val['key'] ) ? $val['key'] : '';
			$val = isset( $val['value'] ) ? $val['value'] : '';

			// Loop through our form and pluck out our email and names
			switch ( $key ) {
				case 'email':
					$email = $val;
					continue;
					break;
				case 'first_name':
					$first_name = $val;
					continue;
					break;
				case 'last_name':
					$last_name = $val;
					continue;
					break;
			}
		}

		// Make sure we have an email set
		if ( $email ) {
			$args = array(
				'email'      => sanitize_email( $email ),
				'list'       => sanitize_text_field( $_POST['ctct-opti-in'] ),
				'first_name' => sanitize_text_field( $first_name ),
				'last_name'  => sanitize_text_field( $last_name ),
			);

			$contact = constantcontact_api()->add_contact( $args );
		}
	}

	/**
	 * Formats values for email
	 *
	 * @param  array $values values to format
	 * @return string         html content for email
	 */
	public function format_values_for_email( $pretty_vals ) {

		$return = '';
		foreach ( $pretty_vals as $val ) {

			// force vars to exist
			$val['field'] = isset( $val['field'] ) ? $val['field'] : '&nbsp;';
			$val['value'] = isset( $val['value'] ) ? $val['value'] : '&nbsp;';

			$return .= '<p>' . sanitize_text_field( $val['field'] ) . ': ' . sanitize_text_field( $val['value'] ) . '</p>';
		}

		return $return;
	}

	/**
	 * Pretty our values up, @todo rip this out for use in verification
	 *
	 * @param  array $values values
	 * @return array         values but better
	 */
	public function pretty_values( $values = array(), $compare_type = '' ) {

		// Sanity check
		if ( ! is_array( $values ) ) {
			return '';
		}

		// Loop through once to get our form ID
		$form_id = 0;
		foreach ( $values as $key => $value ) {

			// Sanity check and check to see if we get our form ID
			// when we find it, break out
			if ( isset( $value['key'] ) && isset( $value['value'] ) ) {

				// if we match our form ID, perfect
				if ( 'ctct-id' === $value['key'] ) {

					// set our form id, unset, and break out
					$form_id = absint( $value['value'] );
					unset( $values[ $key ] );
					break;
				}
			}
		}

		// If we didn't get a form ID, bail out
		if ( ! $form_id ) {
			return '';
		}

		// Get our original fields
		$orig_fields = get_post_meta( $form_id, 'custom_fields_group', true );

		// if its not an array, bail out
		if ( ! is_array( $orig_fields ) ) {
			return;
		}

		// this is what we'll use
		$pretty_values = array();

		// Loop through each field again
		foreach ( $values as $key => $value ) {

			// make sure we have a value
			if ( ! $value ) {
				continue;
			}

			// make sure we have a value
			if ( ! isset( $value['key'] ) ) {
				continue;
			}

			// Make sure we have an orig field with the same key
			if ( ! isset( $orig_fields[ $key ] ) ) {
				continue;
			}

			// Make sure we have the orig mapping key
			if ( ! isset( $orig_fields[ $key ]['_ctct_map_select'] ) ) {
				continue;
			}

			// Make sure we have the orig field name
			if ( ! isset( $orig_fields[ $key ]['_ctct_field_name'] ) ) {
				continue;
			}

			// force value to be set
			$value['value'] = isset( $value['value'] ) ? $value['value'] : '';

			// If we define our compare as full value, we'll send back the
			// entire two form values to compare
			if ( 'full' === $compare_type ) {
				$pretty_values[] = array(
					'orig' => $orig_fields[ $key ],
					'post' => $value['value'],
				);
			} else {

				// Otherwise, pretty up based on field names
				if ( $value['key'] == $orig_fields[ $key ]['_ctct_map_select'] ) {
					$pretty_values[] = array(
						'field' => sanitize_text_field( $orig_fields[ $key ]['_ctct_field_name'] ),
						'value' => sanitize_text_field( $value['value'] ),
					);
				}
			}
		}

		// Send it back
		return $pretty_values;
	}

	/**
	 * Get field requirement errors
	 *
	 * @param  array $values values
	 * @return array         return error code stuff
	 */
	public function get_field_errors( $values ) {

		// get our values with full orig field comparisons
		$values = $this->pretty_values( $values, 'full' );

		// Sanity check
		if ( ! is_array( $values ) ) {
			return array();
		}

		// set up an array to populate with errors
		$err_returns = array();

		// Loop through each value
		foreach ( $values as $value ) {

			// Sanity check
			if ( ! isset( $value['orig'] ) || ! isset( $value['post'] ) ) {
				continue;
			}

			// Do a check for if field was required
			if (
				isset( $value['orig']['_ctct_required_field'] ) &&
				$value['orig']['_ctct_required_field'] &&
				'on' === $value['orig']['_ctct_required_field']
			) {
				// If it was required, check for a value
				if ( empty( $value['post'] ) ) {
					$err_returns[] = $value['orig']['_ctct_map_select'];
				}
			}
		}

		return $err_returns;
	}

	/**
	 * Get the email address to send to
	 *
	 * @return string email address to send to
	 */
	public function get_email() {

		// Eventually we'll make this configurable
		return get_option( 'admin_email' );
	}

	/**
	 * Sends our mail out
	 *
	 * @param  string $destination_email email address
	 * @param  array  $data              data from clean values
	 * @return bool                    if sent
	 */
	public function mail( $destination_email, $content ) {

		// If we didn't get passed in a sanitized email, we know something is
		// wonky here, so bail out
		if ( sanitize_email( $destination_email ) != $destination_email ) {
			return false;
		}

		// Send that mail
		$mail_status = wp_mail(
			$destination_email,
			__( 'New submission' ),
			$content
		);

		return $mail_status;
	}

	/**
	 * Clean our values from form submission
	 *
	 * @param  array $values values to clean
	 * @return array         cleaned values
	 */
	public function clean_values( $values ) {

		// Sanity check that
		if ( ! is_array( $values ) ) {
			return $values;
		}

		$return_values = array();

		// Loop through each of our values
		foreach ( $values as $value ) {

			// if we don't have a key and value set, skip it
			if ( ! isset( $value['key'] ) || ! isset( $value['value'] ) ) {
				continue;
			}

			// we made our fields look like first_name___435fajiosdf to force unique
			$key_break = explode( '___',  $value['key'] );

			// Make sure we actually got something for that
			if ( ! isset( $key_break[0] ) || ! $key_break[0] ) {
				continue;
			}

			$return_values[] = array(
				'key'   => sanitize_text_field( $key_break[0] ),
				'value' => sanitize_text_field( $value['value'] ),
			);
		}

		return $return_values;
	}

	/**
	 * Form submit success/error messages.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function process_wrapper( $form_data = array(), $form_id = '' ) {

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

			// required field errors
			case 'req_error':
				return array(
					'status'  => 'error',
					'message' => __( 'There was an error with your submission. Please correct the fields below.', 'constantcontact' ),
					'errors'  => isset( $processed['errors'] ) ? $processed['errors'] : '',
					'values'  => isset( $processed['values'] ) ? $processed['values'] : '',
				);

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

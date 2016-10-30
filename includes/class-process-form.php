<?php
/**
 * Process form.
 *
 * @package ConstantContact
 * @subpackage ProcessForm
 * @author Constant Contact
 * @since 1.0.0
 */

/**
 * Powers our form processing, validation, and value cleanup.
 */
class ConstantContact_Process_Form {

	/**
	 * Parent plugin class
	 *
	 * @var object
	 * @since 0.0.1
	 */
	protected $plugin = null;

	/**
	 * Constructor
	 *
	 * @since  1.0.0
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Do the hooks!
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function hooks() {
		add_action( 'wp_ajax_ctct_process_form', array( $this, 'process_form_ajax_wrapper' ) );
		add_action( 'wp_ajax_nopriv_ctct_process_form', array( $this, 'process_form_ajax_wrapper' ) );
	}

	/**
	 * A wrpper to process our form via AJAX
	 *
	 * @since  1.0.0
	 */
	public function process_form_ajax_wrapper() {

		// See if we're passed in data
		//
		// We set to ignore this from PHPCS, as our nonce is handled elsewhere
		// @codingStandardsIgnoreLine
		if ( isset( $_POST['data'] ) ) { // Input var okay.

			// Form data comes over serialzied, so break it apart
			//
			// We set to ignore this from PHPCS, as our nonce is handled elsewhere
			// @codingStandardsIgnoreLine
			$data = explode( '&', $_POST['data'] );

			// Finish converting that ajax data to something we can use.
			$json_data = array();

			// Make sure we have an array of data.
			if ( is_array( $data ) ) {

				// Loop through each of our fields.
				foreach ( $data as $field ) {

					// @codingStandardsIgnoreStart
					// Our data looks like this:
					// Array (
					// [0] => email___5d94668ce0670de4192bbcdd94d8ef71=email_address
					// [1] => custom___22d42a056afeffb8d99b2474693afa98=text
					// @codingStandardsIgnoreEnd
					// so we want to break it apart to get the key and the value
					// we pass 2 into explode() to limit it to only two return values
					// in case there is an = in the actual form value
					$exp_fields = explode( '=', $field, 2 );

					// Sanity check.
					if ( isset( $exp_fields[0] ) && $exp_fields[0] ) {
						// Set up our data structure if we have the data.
						$value = urldecode( isset( $exp_fields[1] ) ? $exp_fields[1] : '' );
						$json_data[  esc_attr( $exp_fields[0] ) ] = sanitize_text_field( $value );
					}
				}
			}

			// Send it to our process form method.
			$response = $this->process_form( $json_data, true );

			// We don't need the original values for the ajax check.
			if ( isset( $response['values'] ) ) {
				unset( $response['values'] );
			}

			// Send back our response.
			wp_send_json( $response );

			// Die out of the ajax request.
			wp_die();
		}
	}

	/**
	 * Process submitted form data
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function process_form( $data = array(), $is_ajax = false ) {

		// Set our data var to $_POST if we dont get it passed in.
		if ( empty( $data ) ) {

			$data = $_POST; // @codingStandardsIgnoreLine
		}

		// Don't check for submitted if we are doing it over ajax.
		if ( ! $is_ajax ) {
			// If we don't have our submitted action, just bail out.
			if ( ! isset( $data['ctct-submitted'] ) ) {
				return;
			}
		}

		// If we don't have our submitted form id, just bail out.
		if ( ! isset( $data['ctct-id'] ) ) {
			return;
		}

		// If we don't have our submitted form verify, just bail out.
		if ( ! isset( $data['ctct-verify'] ) ) {
			return;
		}

		// Verify our nonce first.
		if (
		    ! isset( $data['ctct_form'] ) ||
		    ! wp_verify_nonce( $data['ctct_form'], 'ctct_submit_form' )
		) {
			// Figure out a way to pass errors back.
			return array(
				'status' => 'named_error',
				'error'  => __( 'We had trouble processing your submission. Please review your entries and try again.', 'constant-contact-forms' ),
			);
		}

		// Make sure we have an original form id.
		$orig_form_id = absint( $data['ctct-id'] );
		if ( ! $orig_form_id ) {
			return array(
				'status' => 'named_error',
				'error'  => __( "We had trouble processing your submission. Make sure you haven't changed the required Form ID and try again.", 'constant-contact-forms' ),
			);
		}

		// Make sure we have a verify value.
		$form_verify = esc_attr( $data['ctct-verify'] );
		if ( ! $form_verify ) {
			return array(
				'status' => 'named_error',
				'error'  => __( "We had trouble processing your submission. Make sure you haven't changed the required Form ID and try again.", 'constant-contact-forms' ),
			);
		}

		// Make sure our verify key matches our saved one.
		$orig_verify = get_post_meta( $orig_form_id, '_ctct_verify_key', true );
		if ( $orig_verify !== $form_verify ) {
			return array(
				'status' => 'named_error',
				'error'  => __( "We had trouble processing your submission. Make sure you haven't changed the required Form ID and try again.", 'constant-contact-forms' ),
			);
		}

		// Allow ignoring of certain keys, like our nonce.
		$ignored_keys = apply_filters( 'constant_contact_ignored_post_form_values', array(
			'ctct-submitted',
			'ctct_form',
			'_wp_http_referer',
			'ctct-verify',
		) );

		// If the submit button is clicked, send the email.
		foreach ( $data as $key => $value ) {

			// If our key we're processing is in our array, ignore it.
			if ( in_array( $key, $ignored_keys, true ) ) {
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

		// Check for specific validation errors.
		$field_errors = $this->get_field_errors( $this->clean_values( $return['values'] ), $is_ajax );

		// If we got errors.
		if ( is_array( $field_errors ) && ! empty( $field_errors ) ) {

			// Send back an error status, a message, the errors we found, and all orig values.
			return array(
				'status'   => 'req_error',
				'errors'   => $field_errors,
				'values'   => $return['values'],
			);
		}

		// If we're not processing the opt-in stuff, we can just return our data here.
		if ( ! isset( $data['ctct-opt-in'] ) ) {

			if ( ! $is_ajax ) {

				// At this point we can process all of our submitted values.
				constant_contact()->mail->submit_form_values( $return['values'] );
			}

			$return['status'] = 'success';
			return $return;
		}

		if ( ! $is_ajax ) {

			// At this point we can process all of our submitted values.
			constant_contact()->mail->submit_form_values( $return['values'], true );
		}

		$return['status'] = 'success';
		return $return;
	}



	/**
	 * Pretty our values up
	 *
	 * @since  1.0.0
	 * @param array $values values.
	 * @return array         values but better
	 */
	public function pretty_values( $values = array() ) {

		// Sanity check.
		if ( ! is_array( $values ) ) {
			return '';
		}

		// Loop through once to get our form ID.
		$form_id = 0;
		foreach ( $values as $key => $value ) {

			// Sanity check and check to see if we get our form ID
			// when we find it, break out.
			if ( isset( $value['key'] ) && isset( $value['value'] ) ) {

				// If we match our form ID, perfect.
				if ( 'ctct-id' === $value['key'] ) {

					// Set our form id, unset, and break out.
					$form_id = absint( $value['value'] );
					unset( $values['ctct-id'] );
					break;
				}
			}
		}

		// If we didn't get a form ID, bail out.
		if ( ! $form_id ) {
			return '';
		}

		// Get our original fields.
		$orig_fields = $this->get_original_fields( $form_id );

		// If its not an array, bail out.
		if ( ! is_array( $orig_fields ) ) {
			return;
		}

		// This is what we'll use.
		$pretty_values = array();

		// Loop through each field again.
		foreach ( $values as $key => $value ) {

			// Make sure we have a value.
			if ( empty( $value ) ) {
				continue;
			}

			// Make sure we have a value.
			if ( ! isset( $value['key'] ) ) {
				continue;
			}

			// Make sure we have an orig field with the same key.
			if ( ! isset( $orig_fields[ $key ] ) ) {
				continue;
			}

			// Make sure we have the orig mapping key.
			if ( ! isset( $orig_fields[ $key ]['map_to'] ) ) {
				continue;
			}

			// Force value to be set.
			$value['value'] = isset( $value['value'] ) ? $value['value'] : '';

			// Send back our data.
			$pretty_values[] = array(
				'orig'     => $orig_fields[ $key ],
				'post'     => $value['value'],
				'orig_key' => isset( $value['orig_key'] ) ? $value['orig_key'] : '',
			);

		}

		// Send it back.
		return $pretty_values;
	}

	/**
	 * Gets our original field from a form id
	 *
	 * @since 1.0.0
	 * @param int $form_id form id.
	 * @return array          array of form data
	 */
	public function get_original_fields( $form_id ) {

		// Sanity check.
		if ( ! $form_id ) {
			return false;
		}

		// Get our fields post meta.
		$fields = get_post_meta( $form_id, 'custom_fields_group', true );

		// Sanity check again.
		if ( ! is_array( $fields ) ) {
			return false;
		}

		// Start our return array.
		$return = array();

		// Loop through fields to expand some multi-field groups.
		foreach ( $fields as $field ) {

			// If we don't have a map, skip this loop.
			if ( ! isset( $field['_ctct_map_select'] ) ) {
				continue;
			}

			// Set our key field thing.
			$field_key = array(
				'name'        => isset( $field['_ctct_field_label'] ) ? $field['_ctct_field_label'] : '',
				'map_to'      => isset( $field['_ctct_map_select'] ) ? $field['_ctct_map_select'] : '',
				'type'        => isset( $field['_ctct_map_select'] ) ? $field['_ctct_map_select'] : '',
				'description' => isset( $field['_ctct_field_desc'] ) ? $field['_ctct_field_desc'] : '',
				'required'    => ( isset( $field['_ctct_required_field'] ) && $field['_ctct_required_field'] ) ? true : false,
			);

			switch ( $field['_ctct_map_select'] ) {
				case 'address':
					$return[ 'street_address___' . md5( serialize( $field_key ) ) ] = $field_key;
					$return[ 'street_address___' . md5( serialize( $field_key ) ) ]['_ctct_map_select'] = 'street';

					$return[ 'line_2_address___' . md5( serialize( $field_key ) ) ] = $field_key;
					$return[ 'line_2_address___' . md5( serialize( $field_key ) ) ]['_ctct_map_select'] = 'line_2';

					$return[ 'city_address___' . md5( serialize( $field_key ) ) ] = $field_key;
					$return[ 'city_address___' . md5( serialize( $field_key ) ) ]['_ctct_map_select'] = 'city';

					$return[ 'state_address___' . md5( serialize( $field_key ) ) ] = $field_key;
					$return[ 'state_address___' . md5( serialize( $field_key ) ) ]['_ctct_map_select'] = 'state';

					$return[ 'zip_address___' . md5( serialize( $field_key ) ) ] = $field_key;
					$return[ 'zip_address___' . md5( serialize( $field_key ) ) ]['_ctct_map_select'] = 'zip';

					break;
				case 'anniversery':
				case 'birthday':
					$return[ 'month___' . md5( serialize( $field_key ) ) ] = $field_key;
					$return[ 'month___' . md5( serialize( $field_key ) ) ]['_ctct_map_select'] = 'month';

					$return[ 'day___' . md5( serialize( $field_key ) ) ] = $field_key;
					$return[ 'day___' . md5( serialize( $field_key ) ) ]['_ctct_map_select'] = 'day';

					$return[ 'year___' . md5( serialize( $field_key ) ) ] = $field_key;
					$return[ 'year___' . md5( serialize( $field_key ) ) ]['_ctct_map_select'] = 'year';

					break;
				default:
					$return[ $field['_ctct_map_select'] . '___' . md5( serialize( $field_key ) ) ] = $field_key;
					break;
			}
		}

		return $return;
	}

	/**
	 * Get field requirement errors
	 *
	 * @since  1.0.0
	 * @param array $values values.
	 * @return array         return error code stuff
	 */
	public function get_field_errors( $values, $is_ajax = false ) {

		// Get our values with full orig field comparisons.
		$values = $this->pretty_values( $values );

		// Sanity check.
		if ( ! is_array( $values ) ) {
			return array();
		}

		// Set up an array to populate with errors.
		$err_returns = array();

		// Loop through each value.
		foreach ( $values as $value ) {
			// Sanity check.
			if (
				! isset( $value['orig'] ) ||
				! isset( $value['post'] ) ||
				! isset( $value['orig']['map_to'] )
			) {
				continue;
			}

			// Do a check for if field was required.
			if (
				isset( $value['orig']['required'] ) &&
				$value['orig']['required'] &&
				// Skip Address Line 2.
				'line_2' !== $value['orig']['_ctct_map_select']
			) {
				// If it was required, check for a value.
				if ( ! $value['post'] ) {
					$err_returns[] = array(
						'map'   => $value['orig']['map_to'],
						'id'    => isset( $value['orig_key'] ) ? $value['orig_key'] : '',
						'error' => 'required',
					);
				}
			}

			if ( 'email' === $value['orig']['map_to'] ) {
				if ( sanitize_email( $value['post'] ) !== $value['post'] ) {
					$err_returns[] = array(
						'map'   => $value['orig']['map_to'],
						'id'    => isset( $value['orig_key'] ) ? $value['orig_key'] : '',
						'error' => 'invalid',
					);
				}
			}
		}

		return $err_returns;
	}



	/**
	 * Clean our values from form submission
	 *
	 * @since  1.0.0
	 * @param  array $values values to clean.
	 * @return array         cleaned values
	 */
	public function clean_values( $values ) {

		// Sanity check that.
		if ( ! is_array( $values ) ) {
			return $values;
		}

		$return_values = array();

		// Loop through each of our values.
		foreach ( $values as $value ) {

			// If we don't have a key and value set, skip it.
			if ( ! isset( $value['key'] ) || ! isset( $value['value'] ) ) {
				continue;
			}

			// We made our fields look like first_name___435fajiosdf to force unique.
			$key_break = explode( '___',  $value['key'] );

			// Make sure we actually got something for that.
			if ( ! isset( $key_break[0] ) || ! $key_break[0] ) {
				continue;
			}

			$return_values[ sanitize_text_field( $value['key'] ) ] = array(
				'key'      => sanitize_text_field( $key_break[0] ),
				'value'    => sanitize_text_field( $value['value'] ),
				'orig_key' => $value['key'],
			);
		}

		return $return_values;
	}

	/**
	 * Form submit success/error messages.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function process_wrapper( $form_data = array(), $form_id = '' ) {

		// Process our data, and get our response.
		$processed = $this->process_form();

		// Set up our default error.
		$default_error = __( 'There was an error sending your form.', 'constant-contact-forms' );

		// Default to no status.
		$status = false;

		// If we got a status back, check that in our list of returns.
		if ( isset( $processed['status'] ) && $processed['status'] ) {
			$status = $processed['status'];
		}

		// Switch based on our status code.
		switch ( $status ) {

			case 'success':
				$message = __( 'Your information has been submitted.', 'constant-contact-forms' );
				break;

			// Generic error.
			case 'error':
				$message = $default_error;
				break;

			// Named error from our process.
			case 'named_error':
				$message = isset( $processed['error'] ) ? $processed['error'] : $default_error;
				break;

			// Required field errors.
			case 'req_error':
				return array(
					'status'  => 'error',
					'message' => __( 'We had trouble processing your submission. Please review your entries and try again.', 'constant-contact-forms' ),
					'errors'  => isset( $processed['errors'] ) ? $processed['errors'] : '',
					'values'  => isset( $processed['values'] ) ? $processed['values'] : '',
				);

			// All else fails, then we'll just use the default.
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

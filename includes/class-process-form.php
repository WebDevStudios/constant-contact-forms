<?php
/**
 * Process form.
 *
 * @package ConstantContact
 * @subpackage ProcessForm
 * @author Constant Contact
 * @since 1.0.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

use \ReCaptcha\ReCaptcha;
use \ReCaptcha\RequestMethod\CurlPost;

/**
 * Powers our form processing, validation, and value cleanup.
 *
 * @since 1.0.0
 */
class ConstantContact_Process_Form {

	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	protected $plugin;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $plugin Parent plugin class.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Do the hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'wp_ajax_ctct_process_form', [ $this, 'process_form_ajax_wrapper' ] );
		add_action( 'wp_ajax_nopriv_ctct_process_form', [ $this, 'process_form_ajax_wrapper' ] );
	}

	/**
	 * A wrapper to process our form via AJAX.
	 *
	 * @since 1.0.0
	 */
	public function process_form_ajax_wrapper() {

		// See if we're passed in data.
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
			$json_data = [];

			if ( is_array( $data ) ) {
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

					if ( isset( $exp_fields[0] ) && $exp_fields[0] ) {
						$value                                    = urldecode( isset( $exp_fields[1] ) ? $exp_fields[1] : '' );
						$json_data[  esc_attr( $exp_fields[0] ) ] = sanitize_text_field( $value );
					}
				}
			}

			$response = $this->process_form( $json_data, true );

			if ( isset( $response['values'] ) ) {
				unset( $response['values'] );
			}

			$status = false;

			$default_error = esc_html__( 'There was an error sending your form.', 'constant-contact-forms' );

			if ( isset( $response['status'] ) && $response['status'] ) {
				$status = $response['status'];
			}

			switch ( $status ) {

				case 'success':
					/** This filter is documented in includes/class-process-form.php */
					$message = apply_filters( 'ctct_process_form_success',
						__( 'Your information has been submitted.', 'constant-contact-forms' ),
						(int) $json_data['ctct-id'] );
					break;

				case 'error':
					$message = $default_error;
					break;

				case 'named_error':
					$message = isset( $response['error'] ) ? $response['error'] : $default_error;
					break;

				case 'req_error':
					return [
						'status'  => 'error',
						'message' => __( 'We had trouble processing your submission. Please review your entries and try again.', 'constant-contact-forms' ),
						'errors'  => isset( $response['errors'] ) ? $response['errors'] : '',
						'values'  => isset( $response['values'] ) ? $response['values'] : '',
					];

				default:
					$message = $default_error;
					break;
			}

			wp_send_json( [
				'status'  => $status,
				'message' => $message,
			] );

			wp_die();
		}
	}

	/**
	 * Process submitted form data.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception
	 *
	 * @param array $data    Form data.
	 * @param bool  $is_ajax Whether or not processing via AJAX.
	 * @return mixed
	 */
	public function process_form( $data = [], $is_ajax = false ) {

		if ( empty( $data ) ) {

			$data = $_POST; // @codingStandardsIgnoreLine
		}

		if ( ! $is_ajax ) {
			if ( ! isset( $data['ctct-submitted'] ) ) {
				return;
			}
		}

		if ( ! isset( $data['ctct-id'] ) ) {
			return [
				'status' => 'named_error',
				'error'  => __( 'No Constant Contact Forms form ID provided', 'constant-contact-forms' ),
			];
		}

		if ( ! isset( $data['ctct-verify'] ) ) {
			return [
				'status' => 'named_error',
				'error'  => __( 'No form verify value provided', 'constant-contact-forms' ),
			];
		}

		// Honeypot. Should be empty to proceed.
		if ( ! empty( $data['ctct_usage_field'] ) ) {
			return [
				'status' => 'named_error',
				'error'  => $this->get_spam_message( $data['ctct-id'] ),
			];
		}

		if ( ! $this->has_all_required_fields( $data['ctct-id'], $data ) ) {
			return [
				'status' => 'named_error',
				'error'  => __( 'Please properly fill out all required fields', 'constant-contact-forms' ),
			];
		}

		if ( isset( $data['g-recaptcha-response'] ) ) {
			$method = null;
			if ( ! ini_get( 'allow_url_fopen' ) ) {
				$method = new CurlPost();
			}
			$ctctrecaptcha = new ConstantContact_reCAPTCHA();
			$ctctrecaptcha->set_recaptcha_keys();
			$keys = $ctctrecaptcha->get_recaptcha_keys();
			$ctctrecaptcha->set_recaptcha_class( new ReCaptcha( $keys['secret_key'], $method ) );

			$ctctrecaptcha->recaptcha->setExpectedHostname( parse_url( home_url(), PHP_URL_HOST ) );
			if ( 'v3' === $ctctrecaptcha->get_recaptcha_version() ) {
				/**
				 * Filters the default float value for the score threshold.
				 *
				 * This value should be between 0.0 and 1.0.
				 *
				 * @since 1.7.0
				 *
				 * @param float  $value Threshold to require for submission approval.
				 * @param string $value The ID of the form that was submitted.
				 */
				$threshold = (float) apply_filters( 'ctct_recaptcha_threshold', 0.5, $data['ctct-id'] );

				$ctctrecaptcha->recaptcha->setScoreThreshold( $threshold );
				$ctctrecaptcha->recaptcha->setExpectedAction( 'constantcontactsubmit' );
			}
			$resp = $ctctrecaptcha->recaptcha->verify( $data['g-recaptcha-response'], $_SERVER['REMOTE_ADDR'] );

			if ( ! $resp->isSuccess() ) {
				constant_contact_maybe_log_it( 'reCAPTCHA', 'Failed to verify with Google reCAPTCHA', [ $resp->getErrorCodes() ] );
				return [
					'status' => 'named_error',
					'error'  => __( 'Failed reCAPTCHA check', 'constant-contact-forms' ),
				];
			}
		}

		$maybe_disable_recaptcha = 'on' === get_post_meta( $data['ctct-id'], '_ctct_disable_recaptcha', true );
		if ( ! $maybe_disable_recaptcha && empty( $data['g-recaptcha-response'] ) && ConstantContact_reCAPTCHA::has_recaptcha_keys() ) {
			return [
				'status' => 'named_error',
				'error'  => $this->get_spam_message( $data['ctct-id'] ),
			];
		}

		/**
		 * Filters whether or not we think an entry is spam.
		 *
		 * @since 1.3.2
		 *
		 * @param bool  $value Whether or not we thing an entry is spam. Default not spam.
		 * @param array $data  Submitted form data.
		 */
		if ( true === apply_filters( 'constant_contact_maybe_spam', false, $data ) ) {
			return [
				'status' => 'named_error',
				'error'  => $this->get_spam_message( $data['ctct-id'] ),
			];
		}

		if (
			! isset( $data['ctct_form'] ) ||
			! wp_verify_nonce( $data['ctct_form'], 'ctct_submit_form' )
		) {
			constant_contact_maybe_log_it( 'Nonces', 'ctct_submit_form nonce failed to verify.' );
			// @todo Figure out a way to pass errors back.
			return [
				'status' => 'named_error',
				'error'  => __( 'We had trouble processing your submission. Please review your entries and try again.', 'constant-contact-forms' ),
			];
		}

		$orig_form_id = absint( $data['ctct-id'] );
		if ( ! $orig_form_id ) {
			return [
				'status' => 'named_error',
				'error'  => __( "We had trouble processing your submission. Make sure you haven't changed the required form ID and try again.", 'constant-contact-forms' ),
			];
		}

		$form_verify = esc_attr( $data['ctct-verify'] );
		if ( ! $form_verify ) {
			return [
				'status' => 'named_error',
				'error'  => __( "We had trouble processing your submission. Make sure you haven't changed the required Form ID and try again.", 'constant-contact-forms' ),
			];
		}

		$orig_verify = get_post_meta( $orig_form_id, '_ctct_verify_key', true );
		if ( $orig_verify !== $form_verify ) {
			return [
				'status' => 'named_error',
				'error'  => __( "We had trouble processing your submission. Make sure you haven't changed the required form ID and try again.", 'constant-contact-forms' ),
			];
		}

		$ignored_keys = apply_filters( 'constant_contact_ignored_post_form_values', [
			'ctct-submitted',
			'ctct_form',
			'_wp_http_referer',
			'ctct-verify',
			'ctct_time',
			'ctct_usage_field',
			'g-recaptcha-response',
			'ctct_must_opt_in',
		], $orig_form_id );

		foreach ( $data as $key => $value ) {

			if ( ! is_string( $value ) ) {
				continue;
			}

			if ( in_array( $key, $ignored_keys, true ) ) {
				continue;
			}

			$return['values'][] = [
				'key'   => sanitize_text_field( $key ),
				'value' => sanitize_text_field( $value ),
			];
		}

		if ( ! isset( $return['values'] ) || ! is_array( $return['values'] ) ) {
			return;
		}

		$field_errors = $this->get_field_errors( $this->clean_values( $return['values'] ), $is_ajax );

		if ( is_array( $field_errors ) && ! empty( $field_errors ) ) {

			return [
				'status' => 'req_error',
				'errors' => $field_errors,
				'values' => $return['values'],
			];
		}

		if ( ! isset( $data['ctct-opt-in'] ) ) {
			constant_contact()->mail->submit_form_values( $return['values'] );
		} else {

			// No need to check for opt in status because we would have returned early by now if false.
			$maybe_bypass = ctct_get_settings_option( '_ctct_bypass_cron', '' );

			if ( constant_contact()->api->is_connected() && 'on' === $maybe_bypass ) {
				constant_contact()->mail->submit_form_values( $return['values'] ); // Emails but doesn't schedule cron.
				constant_contact()->mail->opt_in_user( $this->clean_values( $return['values'] ) );
			} else {
				constant_contact()->mail->submit_form_values( $return['values'], true );
			}
		}

		$return['status'] = 'success';
		return $return;
	}

	/**
	 * Pretty our values up.
	 *
	 * @since 1.0.0
	 *
	 * @param array $values Original values.
	 * @return array Values, but better.
	 */
	public function pretty_values( $values = [] ) {

		if ( ! is_array( $values ) ) {
			return [];
		}

		$form_id = 0;
		foreach ( $values as $key => $value ) {

			// Sanity check and check to see if we get our form ID
			// when we find it, break out.
			if ( isset( $value['key'] ) && isset( $value['value'] ) ) {

				if ( 'ctct-id' === $value['key'] ) {
					$form_id = absint( $value['value'] );
					unset( $values['ctct-id'] );
					break;
				}
			}
		}

		if ( ! $form_id ) {
			return [];
		}

		$orig_fields = $this->get_original_fields( $form_id );

		if ( ! is_array( $orig_fields ) ) {
			return [];
		}

		$pretty_values = [];

		foreach ( $values as $key => $value ) {

			if ( empty( $value ) ) {
				continue;
			}

			if ( ! isset( $value['key'] ) ) {
				continue;
			}

			if ( ! isset( $orig_fields[ $key ] ) ) {
				continue;
			}

			if ( ! isset( $orig_fields[ $key ]['map_to'] ) ) {
				continue;
			}

			$value['value'] = isset( $value['value'] ) ? $value['value'] : '';

			$pretty_values[] = [
				'orig'     => $orig_fields[ $key ],
				'post'     => $value['value'],
				'orig_key' => isset( $value['orig_key'] ) ? $value['orig_key'] : '',
			];

		}

		return $pretty_values;
	}

	/**
	 * Gets our original field from a form id.
	 *
	 * @since 1.0.0
	 *
	 * @param int $form_id Form id.
	 * @return array Array of form data.
	 */
	public function get_original_fields( $form_id ) {

		if ( ! $form_id ) {
			return [];
		}

		$fields = get_post_meta( $form_id, 'custom_fields_group', true );

		if ( ! is_array( $fields ) ) {
			return [];
		}

		$return = [];

		foreach ( $fields as $field ) {

			if ( ! isset( $field['_ctct_map_select'] ) ) {
				continue;
			}

			$field_key = [
				'name'        => isset( $field['_ctct_field_label'] ) ? $field['_ctct_field_label'] : '',
				'map_to'      => isset( $field['_ctct_map_select'] ) ? $field['_ctct_map_select'] : '',
				'type'        => isset( $field['_ctct_map_select'] ) ? $field['_ctct_map_select'] : '',
				'description' => isset( $field['_ctct_field_desc'] ) ? $field['_ctct_field_desc'] : '',
				'required'    => isset( $field['_ctct_required_field'] ) && $field['_ctct_required_field'],
			];

			switch ( $field['_ctct_map_select'] ) {
				case 'address':
					$return[ 'street_address___' . md5( serialize( $field_key ) ) ]                     = $field_key;
					$return[ 'street_address___' . md5( serialize( $field_key ) ) ]['_ctct_map_select'] = 'street';

					$return[ 'line_2_address___' . md5( serialize( $field_key ) ) ]                     = $field_key;
					$return[ 'line_2_address___' . md5( serialize( $field_key ) ) ]['_ctct_map_select'] = 'line_2';

					$return[ 'city_address___' . md5( serialize( $field_key ) ) ]                     = $field_key;
					$return[ 'city_address___' . md5( serialize( $field_key ) ) ]['_ctct_map_select'] = 'city';

					$return[ 'state_address___' . md5( serialize( $field_key ) ) ]                     = $field_key;
					$return[ 'state_address___' . md5( serialize( $field_key ) ) ]['_ctct_map_select'] = 'state';

					$return[ 'zip_address___' . md5( serialize( $field_key ) ) ]                     = $field_key;
					$return[ 'zip_address___' . md5( serialize( $field_key ) ) ]['_ctct_map_select'] = 'zip';

					break;
				case 'anniversery':
				case 'birthday':
					$return[ 'month___' . md5( serialize( $field_key ) ) ]                     = $field_key;
					$return[ 'month___' . md5( serialize( $field_key ) ) ]['_ctct_map_select'] = 'month';

					$return[ 'day___' . md5( serialize( $field_key ) ) ]                     = $field_key;
					$return[ 'day___' . md5( serialize( $field_key ) ) ]['_ctct_map_select'] = 'day';

					$return[ 'year___' . md5( serialize( $field_key ) ) ]                     = $field_key;
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
	 * Get field requirement errors.
	 *
	 * @since 1.0.0
	 *
	 * @param array $values Values.
	 * @param bool  $is_ajax Whether or not processing via AJAX.
	 * @return array Return error code stuff.
	 */
	public function get_field_errors( $values, $is_ajax = false ) {

		$values = $this->pretty_values( $values );

		if ( ! is_array( $values ) ) {
			return [];
		}

		$err_returns = [];

		foreach ( $values as $value ) {

			if (
				! isset( $value['orig'] ) ||
				! isset( $value['post'] ) ||
				! isset( $value['orig']['map_to'] )
			) {
				continue;
			}

			if (
				isset( $value['orig']['required'] ) &&
				$value['orig']['required'] &&
				// Skip Address Line 2.
				isset( $value['orig']['_ctct_map_select'] ) &&
				'line_2' !== $value['orig']['_ctct_map_select']
			) {
				if ( ! $value['post'] ) {
					$err_returns[] = [
						'map'   => $value['orig']['map_to'],
						'id'    => isset( $value['orig_key'] ) ? $value['orig_key'] : '',
						'error' => 'required',
					];
				}
			}

			if ( 'email' === $value['orig']['map_to'] ) {
				if ( sanitize_email( $value['post'] ) !== $value['post'] ) {
					$err_returns[] = [
						'map'   => $value['orig']['map_to'],
						'id'    => isset( $value['orig_key'] ) ? $value['orig_key'] : '',
						'error' => 'invalid',
					];
				}
			}
		}

		return $err_returns;
	}

	/**
	 * Clean our values from form submission.
	 *
	 * @since 1.0.0
	 *
	 * @param array $values Values to clean.
	 * @return array Cleaned values.
	 */
	public function clean_values( $values ) {

		if ( ! is_array( $values ) ) {
			return $values;
		}

		$return_values = [];

		foreach ( $values as $value ) {

			if ( ! isset( $value['key'] ) || ! isset( $value['value'] ) ) {
				continue;
			}

			// We made our fields look like first_name___435fajiosdf to force unique.
			$key_break = explode( '___', $value['key'] );

			if ( ! isset( $key_break[0] ) || ! $key_break[0] ) {
				continue;
			}

			$return_values[ sanitize_text_field( $value['key'] ) ] = [
				'key'      => sanitize_text_field( $key_break[0] ),
				'value'    => sanitize_text_field( $value['value'] ),
				'orig_key' => $value['key'],
			];
		}

		return $return_values;
	}

	/**
	 * Form submit success/error messages.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception
	 *
	 * @param array      $form_data Form data to process.
	 * @param string|int $form_id   Form ID being processed.
	 * @return false|array
	 */
	public function process_wrapper( $form_data = [], $form_id = 0 ) {

		if ( empty( $_POST['ctct-id'] ) ) {
			return false;
		}

		// @todo Utilize $form_data.
		if ( isset( $_POST['ctct-id'] ) && $form_id !== absint( $_POST['ctct-id'] ) ) {
			return false;
		}

		$processed     = $this->process_form();
		$default_error = esc_html__( 'There was an error sending your form.', 'constant-contact-forms' );
		$status        = false;

		if ( isset( $processed['status'] ) && $processed['status'] ) {
			$status = $processed['status'];
		}

		switch ( $status ) {

			case 'success':
				/**
				 * Filters the message for the successful processed form.
				 *
				 * @since 1.3.0
				 *
				 * @param string     $value Success message.
				 * @param string/int $form_id ID of the Constant Contact form being submitted to.
				 */
				$message = apply_filters( 'ctct_process_form_success', __( 'Your information has been submitted.', 'constant-contact-forms' ), $form_id );
				break;

			case 'error':
				$message = $default_error;
				break;

			case 'named_error':
				$message = isset( $processed['error'] ) ? $processed['error'] : $default_error;
				break;

			case 'req_error':
				return [
					'status'  => 'error',
					'message' => esc_html__( 'We had trouble processing your submission. Please review your entries and try again.', 'constant-contact-forms' ),
					'errors'  => isset( $processed['errors'] ) ? $processed['errors'] : '',
					'values'  => isset( $processed['values'] ) ? $processed['values'] : '',
				];

			default:
				$message = '';
				break;
		} // End switch.

		return [
			'status'  => $status,
			'message' => $message,
		];
	}

	/**
	 * Increment a counter for processed form submissions.
	 *
	 * @since 1.2.2
	 */
	public function increment_processed_form_count() {
		$count = absint( get_option( 'ctct-processed-forms' ) );
		$count++;
		update_option( 'ctct-processed-forms', $count );
	}

	/**
	 * Check if we have all the required fields for a given form.
	 *
	 * @since 1.3.5
	 *
	 * @param int   $form_id   ID of form to verify.
	 * @param array $form_data Form data to verify.
	 * @return bool
	 */
	public function has_all_required_fields( $form_id, $form_data ) {
		$original = $this->get_original_fields( $form_id );

		$has_all = true;
		foreach ( $original as $key => $value ) {
			if ( isset( $value['_ctct_map_select'] ) && 'line_2' === $value['_ctct_map_select'] ) {
				continue;
			}

			if (
				isset( $form_data[ $key ] ) &&
				true === $value['required'] &&
				empty( $form_data[ $key ] )
			) {
				$has_all = false;
				break; // No need to process any further.
			}
		}
		return $has_all;
	}

	/**
	 * Gets the non-human error messeage dispalyed when we think there's a bot.
	 *
	 * @since 1.5.0
	 * @param int $post_id The ID of the current post.
	 * @return string
	 */
	private function get_spam_message( $post_id ) {
		$error = esc_html__( 'We do no think you are human', 'constant-contact-forms' );

		/**
		 * Filter the error message displayed for suspected non-humans.
		 *
		 * @since 1.5.0
		 * @param string $error The error message dispalyed.
		 * @param mixed  $post_id The ID of the current post.
		 * @return string
		 */
		return apply_filters( 'ctct_custom_spam_message', $error, $post_id );
	}
}

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
use Ctct\Exceptions\CtctException;
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
	 *
	 * @return void|array Return array of error data if error encountered, void otherwise.
	 */
	public function process_form_ajax_wrapper() {

		// See if we're passed in data.
		// We set to ignore this from PHPCS, as our nonce is handled elsewhere.
		if ( isset( $_POST['data'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing

			// Form data comes over serialzied, so break it apart.
			// We set to ignore this from PHPCS, as our nonce is handled elsewhere.
			$data = explode( '&', $_POST['data'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

			$json_data = [];

			if ( is_array( $data ) ) {
				foreach ( $data as $field ) {

					// Decode characters in field string.
					// Used to recover array field values.
					$field = urldecode( $field );

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
						if ( false !== strpos( $exp_fields[0], 'email__' ) ) {
							$value = ( is_email( $exp_fields[1] ) ) ? $exp_fields[1] : '';
						} else {
							$value = isset( $exp_fields[1] ) ? urldecode( $exp_fields[1] ) : '';
						}
						$field_key = $exp_fields[0];

						if ( stristr( $field_key, '[]' ) ) {
							$field_key = explode( '[]', $field_key );
							$field_key = $field_key[0];

							$json_data[ esc_attr( $field_key ) ][] = sanitize_text_field( $value );

							continue;
						}

						$json_data[ esc_attr( $field_key ) ] = sanitize_text_field( $value );
					}
				}
			}

			$response = $this->process_form( $json_data, true );

			if ( isset( $response['values'] ) ) {
				unset( $response['values'] );
			}

			$status        = false;
			$default_error = esc_html__( 'There was an error sending your form.', 'constant-contact-forms' );

			if ( isset( $response['status'] ) && $response['status'] ) {
				$status = $response['status'];
			}

			switch ( $status ) {

				case 'success':
					$form_id = (int) $json_data['ctct-id'];

					/* This deprecated filter is documented in includes/class-process-form.php */
					$message = apply_filters_deprecated( 'ctct_process_form_success', [ __( 'Your information has been submitted.', 'constant-contact-forms' ), $form_id ], '1.9.0', 'constant_contact_process_form_success' );

					/** This filter is documented in includes/class-process-form.php */
					$message = esc_html( apply_filters( 'constant_contact_process_form_success', $message, $form_id ) );
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
						'message' => esc_html__( 'We had trouble processing your submission. Please review your entries and try again.', 'constant-contact-forms' ),
						'errors'  => isset( $response['errors'] ) ? $response['errors'] : '',
						'values'  => isset( $response['values'] ) ? $response['values'] : '',
					];
				case 'api_error':
					return [
						'status'  => 'error',
						'message' => $response['message'],
						'values'  => isset( $response['values'] ) ? $response['values'] : '',
					];

				default:
					$message = $default_error;
					break;
			}

			wp_send_json(
				[
					'status'  => $status,
					'message' => $message,
				]
			);

			wp_die();
		}
	}

	/**
	 * Process submitted form data.
	 *
	 * @author Brad Parbs <bradparbs@webdevstudios.com>
	 * @since 1.0.0
	 *
	 * @throws Exception Throws Exception if encountered while attempting to process form.
	 *
	 * @param array $data    Form data.
	 * @param bool  $is_ajax Whether or not processing via AJAX.
	 * @return mixed
	 */
	public function process_form( $data = [], $is_ajax = false ) {

		if ( empty( $data ) ) {

			if ( empty( $_POST ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing -- We sanitze data later on in processes. This returns early.
				return;
			}

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
				'error'  => esc_html__( 'No Constant Contact Forms form ID provided', 'constant-contact-forms' ),
			];
		}

		if ( ! isset( $data['ctct-verify'] ) ) {
			return [
				'status' => 'named_error',
				'error'  => esc_html__( 'No form verify value provided', 'constant-contact-forms' ),
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
				'error'  => esc_html__( 'Please properly fill out all required fields', 'constant-contact-forms' ),
			];
		}

		$spam_error_response = [
			'status' => 'named_error',
			'error'  => $this->get_spam_message( $data['ctct-id'] ),
		];

		$captcha_sevice        = new ConstantContact_CaptchaService();
		$maybe_disable_captcha = 'on' === get_post_meta( $data['ctct-id'], '_ctct_disable_recaptcha', true ); // Note: This option applies to whichever captcha sevice is enabled, despite the option name referencing reCAPTCHA.

		if ( isset( $data['g-recaptcha-response'] ) && 'recaptcha' === $captcha_sevice->get_selected_captcha_service() ) {
			$method = null;
			if ( ! ini_get( 'allow_url_fopen' ) ) {
				$method = new CurlPost();
			}
			$ctctrecaptcha = new ConstantContact_reCAPTCHA();
			$ctctrecaptcha->set_recaptcha_keys();
			$keys = $ctctrecaptcha->get_recaptcha_keys();
			$ctctrecaptcha->set_recaptcha_class( new ReCaptcha( $keys['secret_key'], $method ) );

			// Note: The following line will cause the test key to fail as it expects the host to be testkey.google.com.
			// This line can be commented out for testing purposes.
			// @See https://developers.google.com/recaptcha/docs/faq#id-like-to-run-automated-tests-with-recaptcha.-what-should-i-do
			$ctctrecaptcha->recaptcha->setExpectedHostname( wp_parse_url( home_url(), PHP_URL_HOST ) );
			if ( 'v3' === $ctctrecaptcha->get_recaptcha_version() ) {

				/**
				 * Filters the default float value for the score threshold.
				 *
				 * This value should be between 0.0 and 1.0.
				 *
				 * @deprecated 1.9.0 Deprecated in favor of properly-prefixed hookname.
				 *
				 * @since 1.7.0
				 *
				 * @param float  $value Threshold to require for submission approval.
				 * @param string $value The ID of the form that was submitted.
				 */
				$threshold = apply_filters_deprecated( 'ctct_recaptcha_threshold', [ 0.5, $data['ctct-id'] ], '1.9.0', 'constant_contact_recaptcha_threshold' );

				/**
				 * Filters the default float value for the score threshold.
				 *
				 * This value should be between 0.0 and 1.0.
				 *
				 * @author Rebekah Van Epps <rebekah.vanepp@webdevstudios.com>
				 * @since  1.9.0
				 *
				 * @param  float  $value Required threshold value.
				 * @param  string $value Form ID.
				 */
				$threshold = (float) apply_filters( 'constant_contact_recaptcha_threshold', $threshold, $data['ctct-id'] );

				$ctctrecaptcha->recaptcha->setScoreThreshold( $threshold );
				$ctctrecaptcha->recaptcha->setExpectedAction( 'constantcontactsubmit' );
			}
			$resp = $ctctrecaptcha->recaptcha->verify( $data['g-recaptcha-response'], $_SERVER['REMOTE_ADDR'] );

			if ( ! $resp->isSuccess() ) {
				constant_contact_maybe_log_it( 'reCAPTCHA', 'Failed to verify with Google reCAPTCHA', [ $resp->getErrorCodes() ] );
				return [
					'status' => 'named_error',
					'error'  => esc_html__( 'Failed reCAPTCHA check', 'constant-contact-forms' ),
				];
			}
		}

		// Check for case where reCAPTCHA is enabled but response was missing.
		if (
			! $maybe_disable_captcha &&
			empty( $data['g-recaptcha-response'] )
			&& ConstantContact_reCAPTCHA::has_recaptcha_keys() &&
			'recaptcha' === $captcha_sevice->get_selected_captcha_service()
		) {
			return $spam_error_response;
		}

		// Handle verifying hCaptcha response.
		if ( isset( $data['h-captcha-response'] ) && 'hcaptcha' === $captcha_sevice->get_selected_captcha_service() ) {
			$ctcthcaptcha = new ConstantContact_hCaptcha();
			$ctcthcaptcha->set_hcaptcha_keys();
			$keys = $ctcthcaptcha->get_hcaptcha_keys();

			$hcaptcha_data = [
				'secret'   => $keys['secret_key'],
				'response' => $data['h-captcha-response']
			];

			$response = wp_remote_post(
				'https://hcaptcha.com/siteverify',
				[
					'body' => http_build_query( $hcaptcha_data )
				]
			);

			$hcaptcha_response_data = json_decode( wp_remote_retrieve_body( $response ) );
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) || false === $hcaptcha_response_data->success ) {

				constant_contact_maybe_log_it( 'hCaptcha', 'Failed to verify with hCaptcha', $hcaptcha_response_data->{'error-codes'} );

				return [
					'status' => 'named_error',
					'error'  => __( 'Failed hCaptcha check', 'constant-contact-forms' ),
				];
			}
		}

		// Check for case where hCaptcha is enabled but response was missing.
		if (
			! $maybe_disable_captcha &&
			empty( $data['h-captcha-response'] )
			&& ConstantContact_hCaptcha::has_hcaptcha_keys() &&
			'hcaptcha' === $captcha_sevice->get_selected_captcha_service()
		) {
			return $spam_error_response;
		}

		/**
		 * Filters whether or not we think an entry is spam.
		 *
		 * @author Michael Beckwith <michael@webdevstudios.com>
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

		$orig_form_id = absint( $data['ctct-id'] );
		if ( ! $orig_form_id ) {
			return [
				'status' => 'named_error',
				'error'  => esc_html__( "We had trouble processing your submission. Make sure you haven't changed the required form ID and try again.", 'constant-contact-forms' ),
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

		$ignored_keys = apply_filters(
			'constant_contact_ignored_post_form_values',
			[
				'ctct-submitted',
				'ctct_form',
				'_wp_http_referer',
				'ctct-verify',
				'ctct_time',
				'ctct_usage_field',
				'g-recaptcha-response',
				'h-recaptcha-response',
				'ctct_must_opt_in',
				'ctct-instance',
			],
			$orig_form_id
		);

		foreach ( $data as $key => $value ) {

			if ( ! is_string( $value ) && ! is_array( $value ) ) {
				continue;
			}

			if ( in_array( $key, $ignored_keys, true ) ) {
				continue;
			}

			$return['values'][] = [
				'key'   => sanitize_text_field( $key ),
				'value' => is_array( $value ) ? array_map( 'sanitize_text_field', $value ) : sanitize_text_field( $value ),
			];
		}

		if ( ! isset( $return['values'] ) || ! is_array( $return['values'] ) ) {
			return;
		}

		$cleaned_values = $this->clean_values( $return['values'] );

		// Require at least one list to be selected.
		if (
			constant_contact()->get_api()->is_connected() &&
			(
				empty( $cleaned_values['ctct-lists'] ) ||
				empty( $cleaned_values['ctct-lists']['value'][0] )
			)
		) {
			return [
				'status' => 'named_error',
				'error'  => esc_html__( 'Please select at least one list to subscribe to.', 'constant-contact-forms' ),
			];
		}

		$field_errors = $this->get_field_errors( $cleaned_values, $is_ajax );

		if ( is_array( $field_errors ) && ! empty( $field_errors ) ) {

			return [
				'status' => 'req_error',
				'errors' => $field_errors,
				'values' => $return['values'],
			];
		}
		try {
			if ( ! isset( $data['ctct-opt-in'] ) ) {
				constant_contact()->get_mail()->submit_form_values( $return['values'] );
			} else {
				// No need to check for opt in status because we would have returned early by now if false.
				if ( constant_contact()->get_api()->is_connected() ) {
					$api_result = constant_contact()->get_mail()->opt_in_user( $this->clean_values( $return['values'] ) );
					// Send email if API request fails.
					if ( false === $api_result ) {
						$clean_values  = $this->clean_values( $return['values'] );
						$pretty_values = $this->pretty_values( $clean_values );
						$email_values  = constant_contact()->get_mail()->format_values_for_email( $pretty_values, $orig_form_id );

						$test = constant_contact()->get_mail()->mail(
							constant_contact()->get_mail()->get_email( $orig_form_id ),
							$email_values,
							[
								'form_id'         => $orig_form_id,
								'submitted_email' => constant_contact()->get_mail()->get_user_email_from_submission( $clean_values ),
								'custom-reason'   => esc_html__( 'An error occurred while attempting Constant Contact API request.', 'constant-contact-forms' ),
							],
							true
						);

						// Also return API error.
						return [
							'status'  => 'api_error',
							'values'  => $return['values'],
							'message' => esc_html__( 'An error occurred while attempting Constant Contact API request. Please check your details and try again.', 'constant-contact-forms' ),
						];
					} elseif ( is_array( $api_result ) && 1 === count( $api_result ) ) {
						$api_error = $api_result[0];

						if (
							array_key_exists( 'error_key', $api_error ) &&
							array_key_exists( 'error_message', $api_error ) &&
							'contacts.api.validation.error' === $api_error['error_key'] &&
							(
								'Email address is invalid' === $api_error['error_message'] ||
								'Address format' === $api_error['error_message']
							)
						) {
							return [
								'status'  => 'api_error',
								'message' => __( 'An error occurred while attempting Constant Contact API request. Please check your details and try again.', 'constant-contact-forms' ),
							];
						}
					} else {
						// Only email if we have a successful API request.
						constant_contact()->get_mail()->submit_form_values( $return['values'] ); // Emails but doesn't schedule cron.
					}
				} else {
					// We have at least one list, but are not considered connected.
					if (
						! constant_contact()->get_api()->is_connected() &&
						! empty( $cleaned_values['ctct-lists']['value'][0] )
					) {
						$email = '';
						foreach ( $cleaned_values as $index => $string ) {
							if ( false !== strpos( $index, 'email' ) ) {
								$email = $string['value'];
							}
						}

						$new_contact = [];
						foreach ( $cleaned_values as $key => $val ) {
							$key  = sanitize_text_field( $val['key'] ?? '' );
							$orig = sanitize_text_field( $val['orig_key'] ?? '' );
							$val  = sanitize_text_field( $val['value'] ?? '' );

							if ( empty( $key ) || in_array( $key, [ 'ctct-opt-in', 'ctct-id', 'ctct-lists' ], true ) ) {
								continue;
							}

							$new_contact[ $orig ] = [
								'key' => $key,
								'val' => $val,
							];

							if ( 'email' === $key ) {
								$new_contact['email'] = $val;
							}
						}

						// At this point, something is likely going on,
						// so after the 2nd attempt, we will log the attempt for later.
						foreach( $cleaned_values['ctct-lists']['value'] as $chosen => $list ) {
							constant_contact()->get_api()->log_missed_api_request(
								'contact_add_update',
								[
									'list'    => $list,
									'email'   => $email,
									'contact' => $new_contact, // all of the field info
									'form_id' => $cleaned_values['ctct-id']['value'],
								]
							);
						}
						constant_contact_maybe_log_it( 'API', 'A failed API attempt was caught and will be retried after reconnection.' );
					}
					constant_contact()->get_mail()->submit_form_values( $return['values'], true );
				}
			}
		} catch ( CtctException $exception ) {
			return [
				'status'  => 'api_error',
				'values'  => $return['values'],
				'message' => $exception->getMessage(),
			];
		}

		$return['status'] = 'success';
		return $return;
	}

	/**
	 * Pretty our values up.
	 *
	 * @author Brad Parbs <bradparbs@webdevstudios.com>
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

			$value['value'] = $value['value'] ?? '';

			$pretty_values[] = [
				'orig'     => $orig_fields[ $key ],
				'post'     => $value['value'],
				'orig_key' => $value['orig_key'] ?? '',
			];

		}

		return $pretty_values;
	}

	/**
	 * Gets our original field from a form id.
	 *
	 * @author Brad Parbs <bradparbs@webdevstudios.com>
	 * @since 1.0.0
	 *
	 * @param int $form_id Form id.
	 * @return array Array of form data.
	 */
	public function get_original_fields( int $form_id ) {

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
				'name'        => $field['_ctct_field_label'] ?? '',
				'map_to'      => $field['_ctct_map_select'],
				'type'        => $field['_ctct_map_select'],
				'description' => $field['_ctct_field_desc'] ?? '',
				'required'    => isset( $field['_ctct_required_field'] ) && $field['_ctct_required_field'],
			];

			$hashed_key = md5( serialize( $field_key ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize -- OK use of serialize().

			switch ( $field['_ctct_map_select'] ) {
				case 'address':
					$return[ 'street_address___' . $hashed_key ]                     = $field_key;
					$return[ 'street_address___' . $hashed_key ]['_ctct_map_select'] = 'street';

					$return[ 'line_2_address___' . $hashed_key ]                     = $field_key;
					$return[ 'line_2_address___' . $hashed_key ]['_ctct_map_select'] = 'line_2';

					$return[ 'city_address___' . $hashed_key ]                     = $field_key;
					$return[ 'city_address___' . $hashed_key ]['_ctct_map_select'] = 'city';

					$return[ 'state_address___' . $hashed_key ]                     = $field_key;
					$return[ 'state_address___' . $hashed_key ]['_ctct_map_select'] = 'state';

					$return[ 'zip_address___' . $hashed_key ]                     = $field_key;
					$return[ 'zip_address___' . $hashed_key ]['_ctct_map_select'] = 'zip';

					break;
				case 'anniversery':
				case 'birthday':
					$return[ 'month___' . $hashed_key ]                     = $field_key;
					$return[ 'month___' . $hashed_key ]['_ctct_map_select'] = 'month';

					$return[ 'day___' . $hashed_key ]                     = $field_key;
					$return[ 'day___' . $hashed_key ]['_ctct_map_select'] = 'day';

					$return[ 'year___' . $hashed_key ]                     = $field_key;
					$return[ 'year___' . $hashed_key ]['_ctct_map_select'] = 'year';

					break;
				default:
					$return[ $field['_ctct_map_select'] . '___' . $hashed_key ] = $field_key;
					break;
			}
		}

		return $return;
	}

	/**
	 * Get field requirement errors.
	 *
	 * @author Brad Parbs <bradparbs@webdevstudios.com>
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
						'id'    => $value['orig_key'] ?? '',
						'error' => 'required',
					];
				}
			}

			if ( 'email' === $value['orig']['map_to'] ) {
				if ( sanitize_email( $value['post'] ) !== $value['post'] ) {
					$err_returns[] = [
						'map'   => $value['orig']['map_to'],
						'id'    => $value['orig_key'] ?? '',
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
	 * @author Brad Parbs <bradparbs@webdevstudios.com>
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

			$clean_key = $key_break[0];
			$field_key = 'lists' === $clean_key ? 'ctct-lists' : $value['key'];

			$return_values[ sanitize_text_field( $field_key ) ] = [
				'key'      => sanitize_text_field( $clean_key ),
				'value'    => is_array( $value['value'] ) ? array_map( 'sanitize_text_field', $value['value'] ) : sanitize_text_field( $value['value'] ),
				'orig_key' => $value['key'],
			];
		}

		return $return_values;
	}

	/**
	 * Form submit success/error messages.
	 *
	 * @author Brad Parbs <bradparbs@webdevstudios.com>
	 * @since 1.0.0
	 *
	 * @throws Exception Throws Exception if encountered while attempting to process form wrapper.
	 *
	 * @param  array      $form_data Form data to process.
	 * @param  string|int $form_id   Form ID being processed.
	 * @param  int        $instance  Current form instance.
	 * @return false|array
	 */
	public function process_wrapper( $form_data = [], $form_id = 0, $instance = 0 ) {
		$ctct_id = absint( filter_input( INPUT_POST, 'ctct-id', FILTER_SANITIZE_NUMBER_INT ) );

		if ( empty( $ctct_id ) ) {
			return false;
		}

		// @todo Utilize $form_data.
		if ( $ctct_id !== absint( $form_id ) ) {
			return false;
		}

		// Ensure calculated form instance matches POST form instance.
		$posted_instance = absint( filter_input( INPUT_POST, 'ctct-instance', FILTER_SANITIZE_NUMBER_INT ) );

		if ( $posted_instance !== $instance ) {
			return false;
		}

		$processed     = $this->process_form( [], false );
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
				 * @deprecated 1.9.0 Deprecated in favor of properly-prefixed hookname.
				 *
				 * @author Michael Beckwith <michael@webdevstudios.com>
				 * @since  1.3.0
				 *
				 * @param  string     $value Success message.
				 * @param  string/int $form_id ID of the Constant Contact form being submitted to.
				 */
				$message = apply_filters_deprecated( 'ctct_process_form_success', [ __( 'Your information has been submitted.', 'constant-contact-forms' ), $form_id ], '1.9.0', 'constant_contact_process_form_success' );

				/**
				 * Filters the message for the successful processed form.
				 *
				 * @author Rebekah Van Epps <rebekah.vanepp@webdevstudios.com>
				 * @since  1.9.0
				 *
				 * @param  string     $value   Success message.
				 * @param  string|int $form_id Constant Contact form ID.
				 */
				$message = esc_html( apply_filters( 'constant_contact_process_form_success', $message, $form_id ) );
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
					'errors'  => $processed['errors'] ?? '',
					'values'  => $processed['values'] ?? '',
				];
			case 'api_error':
				return [
					'status'  => 'error',
					'message' => $processed['message'] ? esc_html( $processed['message'] ) : esc_html__( 'We had trouble processing your submission. Please review your entries and try again.', 'constant-contact-forms' ),
					'values'  => $processed['values'] ?? '',
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
	 * @author Michael Beckwith <michael@webdevstudios.com>
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
	 * @author Michael Beckwith <michael@webdevstudios.com>
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
				break;
			}
		}
		return $has_all;
	}

	/**
	 * Gets the non-human error messeage dispalyed when we think there's a bot.
	 *
	 * @author Michael Beckwith <michael@webdevstudios.com>
	 * @since 1.5.0
	 * @param int $post_id The ID of the current post.
	 * @return string
	 */
	private function get_spam_message( int $post_id ) {
		$error = esc_html__( 'We do no think you are human', 'constant-contact-forms' );

		/**
		 * Filter the error message displayed for suspected non-humans.
		 *
		 * @deprecated 1.9.0 Deprecated in favor of properly-prefixed hookname.
		 *
		 * @author Michael Beckwith <michael@webdevstudios.com>
		 * @since 1.5.0
		 * @param string $error The error message dispalyed.
		 * @param mixed  $post_id The ID of the current post.
		 * @return string
		 */
		$error = apply_filters_deprecated( 'ctct_custom_spam_message', [ $error, $post_id ], '1.9.0', 'constant_contact_custom_spam_message' );

		/**
		 * Filters error message for suspected spam entries.
		 *
		 * @author Rebekah Van Epps <rebekah.vanepp@webdevstudios.com>
		 * @since  1.9.0
		 *
		 * @param  string     $error   Error message.
		 * @param  int|string $post_id Current post ID.
		 * @return string              Error message.
		 */
		return apply_filters( 'constant_contact_custom_spam_message', $error, $post_id );
	}
}

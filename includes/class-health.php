<?php

class ConstantContact_Health {

	/**
	 * Parent plugin class.
	 *
	 * @since 2.3.0
	 * @var object
	 */
	protected object $plugin;

	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		add_filter( 'debug_information', [ $this, 'health_information' ], 1 );
	}

	/**
	 * Callback to add in our own custom site health information.
	 *
	 * @since 2.3.0
	 *
	 * @param array $debug_info Array of debug info sections to add to.
	 * @return array
	 * @throws Exception
	 */
	public function health_information( array $debug_info ): array {

		// Reused strings.
		$can_write    = esc_html__( 'Writable', 'constant-contact-forms' );
		$cannot_write = esc_html__( 'Not writable', 'constant-contact-forms' );
		$yes          = esc_html__( 'Yes', 'constant-contact-forms' );
		/* translators: placeholder will hold a number */
		$yes_count    = esc_html__( 'Yes, %s', 'constant-contact-forms' );
		$no           = esc_html__( 'No', 'constant-contact-forms' );

		$logs            = constant_contact()->get_logging()->get_log_locations();
		$logs_writeable  = sprintf(
			'Folder: %s, File: %s',
			( is_writable( $logs['directory'] ) ) ? $can_write : $cannot_write,
			( is_writable( $logs['file'] ) ) ? $can_write : $cannot_write
		);
		$token_timestamp = get_option( 'ctct_access_token_timestamp', '' );
		$expires         = constant_contact()->get_connect()->e_get( '_ctct_expires_in' );
		$expires_on      = esc_html__( 'Access token needs refreshed', 'constant-contact-forms' );
		if ( ! empty( $token_timestamp ) && ! empty( $expires ) ) {
			$expires_on_ts = $token_timestamp + $expires;
			$expires_on    = date( 'Y-m-d, h:ia', $expires_on_ts ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
		}

		$missed_submissions = get_site_option( 'ctct_missed_api_requests' );

		$recaptcha         = new ConstantContact_reCAPTCHA();
		$recaptcha_version = $recaptcha->get_recaptcha_version();
		$has_recaptcha     = ( ConstantContact_reCAPTCHA::has_recaptcha_keys() ) ? $yes : $no;
		$recaptcha_status  = sprintf(
		/* Translators: Placeholders will store the current values from each */
			esc_html__( 'Has reCAPTCHA: %1$s, Version: %2$s', 'constant-contact-forms' ),
			$has_recaptcha,
			$recaptcha_version
		);

		$has_hcaptcha    = ( ConstantContact_hCaptcha::has_hcaptcha_keys() ) ? $yes : $no;
		$hcaptcha_status = sprintf(
		/* Translators: Placeholders will store the current values from each */
			esc_html__( 'Has hCaptcha: %1$s', 'constant-contact-forms' ),
			$has_hcaptcha,
		);

		$has_turnstile    = ( ConstantContact_Turnstile::has_turnstile_keys() ) ? $yes : $no;
		$turnstile_status = sprintf(
		/* Translators: Placeholders will store the current values from each */
			esc_html__( 'Has Turnstile: %1$s', 'constant-contact-forms' ),
			$has_turnstile,
		);

		$debug_info['constant-contact-forms'] = [
			'label'       => esc_html__( 'Constant Contact Forms', 'constant-contact-forms' ),
			'description' => esc_html__( 'Debugging and troubleshooting information for support purposes', 'constant-contact-forms' ),
			'fields'      => [
				[
					'label' => esc_html__( 'Plugin version', 'constant-contact-forms' ),
					'value' => constant_contact()::VERSION,
				],
				[
					'label' => esc_html__( 'API: Is connected?', 'constant-contact-forms' ),
					'value' => ( constant_contact()->get_api()->is_connected() ) ? $yes : $no,
				],
				[
					'label' => esc_html__( 'API: Has access token?', 'constant-contact-forms' ),
					'value' => ( ! empty( constant_contact()->get_connect()->e_get( '_ctct_access_token' ) ) ) ? $yes : $no,
				],
				[
					'label' => esc_html__( 'API: Has refresh token?', 'constant-contact-forms' ),
					'value' => ( ! empty( constant_contact()->get_connect()->e_get( '_ctct_refresh_token' ) ) ) ? $yes : $no,
				],
				[
					'label' => esc_html__( 'API: Has expiration time?', 'constant-contact-forms' ),
					'value' => ( ! empty( constant_contact()->get_connect()->e_get( '_ctct_expires_in' ) ) ) ? $yes : $no,
				],
				[
					'label' => esc_html__( 'API: Token should expire on:', 'constant-contact-forms' ),
					'value' => $expires_on,
				],
				[
					'label' => esc_html__( 'CONSTANT_CONTACT_DEBUG_MAIL Enabled?', 'constant-contact-forms' ),
					'value' => ( defined( 'CONSTANT_CONTACT_DEBUG_MAIL' ) && CONSTANT_CONTACT_DEBUG_MAIL ) ? $yes : $no,
				],
				[
					'label' => esc_html__( 'Logs directory and file?', 'constant-contact-forms' ),
					'value' => $logs_writeable,
				],
				[
					'label' => esc_html__( 'reCAPTCHA Status', 'constant-contact-forms' ),
					'value' => $recaptcha_status,
				],
				[
					'label' => esc_html__( 'hCaptcha Status', 'constant-contact-forms' ),
					'value' => $hcaptcha_status,
				],
				[
					'label' => esc_html__( 'Turnstile Status', 'constant-contact-forms' ),
					'value' => $turnstile_status,
				],
				[
					'label' => esc_html__( 'Has missed submissions', 'constant-contact-forms' ),
					'value' => ( empty( $missed_submissions ) ) ? $no : sprintf( $yes_count, count( $missed_submissions ) )
				],
				[
					'label' => esc_html__( 'Constant Contact Status page', 'constant-contact-forms' ),
					'value' => 'https://status.constantcontact.com/'
				],
			],
		];

		return $debug_info;
	}
}

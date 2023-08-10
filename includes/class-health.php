<?php

class ConstantContact_Health {

	/**
	 * Parent plugin class.
	 *
	 * @since NEXT
	 * @var object
	 */
	protected object $plugin;

	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		add_filter( 'debug_information', [ $this, 'health_information' ], 1 );
	}

	public function health_information( $debug_info ) {

		$logs = constant_contact()->logging->get_log_locations();

		// Reused strings.
		$can_write    = esc_html__( 'Writable', 'constant-contact-forms' );
		$cannot_write = esc_html__( 'Not writable', 'constant-contact-forms' );
		$yes          = esc_html__( 'Yes', 'constant-contact-forms' );
		$no           = esc_html__( 'No', 'constant-contact-forms' );

		$logs_writeable = sprintf(
			'Folder: %s, File: %s',
			( is_writable( $logs['directory'] ) ) ? $can_write : $cannot_write,
			( is_writable( $logs['file'] ) ) ? $can_write : $cannot_write
		);
		$token_timestamp = get_option( 'ctct_access_token_timestamp', '' );
		$expires = constant_contact()->connect->e_get( '_ctct_expires_in' );
		$expires_on = esc_html__( 'Access token needs refreshed', 'constant-contact-forms' );
		if ( ! empty( $token_timestamp ) && ! empty( $expires ) ) {
			$expires_on_ts = $token_timestamp + $expires;
			$expires_on = date( 'Y-m-d, h:ia', $expires_on_ts );
		}

		$debug_info['constant-contact-forms'] = [
			'label' => esc_html__( 'Constant Contact Forms', 'constant-contact-forms' ),
			'description' => esc_html__( 'Debugging and troubleshooting information for support purposes', 'constant-contact-forms' ),
			'fields' => [
				[
					'label' => esc_html__( 'Plugin version', 'constant-contact-forms' ),
					'value' => constant_contact()::VERSION,
				],
				[
					'label' => esc_html__( 'API: Is connected?', 'constant-contact-forms' ),
					'value' => ( constant_contact()->api->is_connected() ) ? $yes : $no,
				],
				[
					'label' => esc_html__( 'API: Has access token?', 'constant-contact-forms' ),
					'value' => ( ! empty( constant_contact()->connect->e_get( '_ctct_access_token' ) ) ) ? $yes : $no,
				],
				[
					'label' => esc_html__( 'API: Has refresh token?', 'constant-contact-forms' ),
					'value' => ( ! empty( constant_contact()->connect->e_get( '_ctct_refresh_token' ) ) ) ? $yes : $no,
				],
				[
					'label' => esc_html__( 'API: Has expiration time?', 'constant-contact-forms' ),
					'value' => ( ! empty( constant_contact()->connect->e_get( '_ctct_expires_in' ) ) ) ? $yes : $no,
				],
				[
					'label' => esc_html__( 'API: Token should expire on:', 'constant-contact-forms' ),
					'value' => $expires_on,
				],
				[
					'label' => esc_html__( 'DISABLE_WP_CRON Enabled?', 'constant-contact-forms' ),
					'value' => ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) ? $yes : $no,
				],
				[
					'label' => esc_html__( 'ALTERNATE_WP_CRON Enabled?', 'constant-contact-forms' ),
					'value' => ( defined( 'ALTERNATE_WP_CRON' ) && ALTERNATE_WP_CRON ) ? $yes : $no,
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
					'label' => esc_html__( 'Opt in cron scheduled?', 'constant-contact-forms' ),
					'value' => ( wp_next_scheduled( 'ctct_schedule_form_opt_in' ) ) ? $yes : $no,
				],
				[
					'label' => esc_html__( 'Token refresh cron scheduled?', 'constant-contact-forms' ),
					'value' => ( wp_next_scheduled( 'refresh_token_job' ) ) ? $yes : $no,
				],
				[
					'label' => esc_html__( 'Cron check', 'constant-contact-forms' ),
					'value' => $this->cron_spawn(),
				]
			]
		];

		return $debug_info;
	}

	/**
	 * Commission a cron job to check on server status.
	 * @return string
	 * @since 1.0.0
	 */
	public function cron_spawn() {

		global $wp_version;

		if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) {
			/* Translators: Placeholder will be a timestamp for the current time. */
			return sprintf( esc_html__( 'The DISABLE_WP_CRON constant is set to true as of %1$s. WP-Cron is disabled and will not run.', 'constant-contact-forms' ), current_time( 'm/d/Y g:i:s a' ) );
		}

		if ( defined( 'ALTERNATE_WP_CRON' ) && ALTERNATE_WP_CRON ) {
			/* Translators: Placeholder will be a timestamp for the current time. */
			return sprintf( esc_html__( 'The ALTERNATE_WP_CRON constant is set to true as of %1$s. This plugin cannot determine the status of your WP-Cron system.', 'constant-contact-forms' ), current_time( 'm/d/Y g:i:s a' ) );
		}

		$sslverify     = version_compare( $wp_version, 4.0, '<' );
		$doing_wp_cron = sprintf( '%.22F', microtime( true ) );

		// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Filters defined in WP Core.
		/* This filter is documented in wp-includes/cron.php */
		$cron_request = apply_filters(
			'cron_request',
			[
				'url'  => site_url( 'wp-cron.php?doing_wp_cron=' . $doing_wp_cron ),
				'key'  => $doing_wp_cron,
				'args' => [
					'timeout'   => 3,
					'blocking'  => true,
					'sslverify' => apply_filters( 'https_local_ssl_verify', $sslverify ),
				],
			]
		);
		// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

		$cron_request['args']['blocking'] = true;

		$result        = wp_remote_post( $cron_request['url'], $cron_request['args'] );
		$response_code = wp_remote_retrieve_response_code( $result );

		if ( is_wp_error( $result ) ) {
			return $result->get_error_message();
		}

		if ( 300 <= $response_code ) {
			return sprintf(
			/* Translators: Placeholder will be an HTTP response code. */
				esc_html__( 'Unexpected HTTP response code: %1$s', 'constant-contact-forms' ),
				(int) $response_code
			);
		}

		return esc_html__( 'Cron spawn ok', 'constant-contact-forms' );
	}
}

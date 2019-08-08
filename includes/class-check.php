<?php
/**
 * Server status checks.
 *
 * @package ConstantContact
 * @subpackage Check
 * @author Constant Contact
 * @since 1.0.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Helper class to allow for checking and displaying server status.
 *
 * @since 1.0.0
 */
class ConstantContact_Check {

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
	 * @param object $plugin Parent plugin.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Lets you add 'ctct-debug-server-check' to the query
	 * args of a page to load a server requirements check.
	 *
	 * @since 1.0.0
	 */
	public function maybe_display_debug_info() {

		// phpcs:disable WordPress.Security.NonceVerification -- OK direct-accessing of $_GET.
		if ( isset( $_GET['ctct-debug-server-check'] ) && is_admin() && current_user_can( 'manage_options' ) ) {
			?>
				<div class="ctct-server-requirements">
					<h4><?php esc_attr_e( 'Server Check', 'constant-contact-forms' ); ?></h4>

					<?php $this->display_server_checks(); ?>

					<h4><?php esc_attr_e( 'Cron Check', 'constant-contact-forms' ); ?></h4>
					<p><?php echo esc_html( $this->cron_spawn() ); ?></p>
				</div>
			<?php
		}
		// phpcs:enable WordPress.Security.NonceVerification
	}

	/**
	 * Gets the list of functions / classes we need ot check on the server
	 * to be considered 'valid'.
	 *
	 * @since 1.0.0
	 *
	 * @return array Nested array of functions/classes needed.
	 */
	public function get_checks_to_make() {

		/**
		 * Filters the functions, classes, etc that we want to check on to be considered valid.
		 *
		 * @since 1.0.0
		 *
		 * @param array $value Array of things to check for.
		 */
		return apply_filters( 'constant_contact_server_checks', [
			'functions' => [
				'openssl_encrypt',
				'openssl_decrypt',
			],
			'classes'   => [
				'CMB2',
				'WDS_Shortcodes',
			],
		] );
	}


	/**
	 * Displays our server check.
	 *
	 * @since 1.0.0
	 */
	public function display_server_checks() {

		$checks = $this->get_checks_to_make();

		echo '<table class="ctct-server-check">';

		if (
			isset( $checks['functions'] ) &&
			is_array( $checks['functions'] ) &&
			1 <= count( $checks['functions'] )
		) {
			foreach ( $checks['functions'] as $function ) {
				echo '<tr><td>' . esc_attr( $function ) . '</td><td>' . esc_attr( $this->exists_text( $function, 'f' ) ) . '</td></tr>';
			}
		}

		if (
			isset( $checks['classes'] ) &&
			is_array( $checks['classes'] ) &&
			1 <= count( $checks['classes'] )
		) {

			foreach ( $checks['classes'] as $class ) {
				echo '<tr><td>' . esc_attr( $class ) . '</td><td>' . esc_attr( $this->exists_text( $class, 'c' ) ) . '</td></tr>';
			}
		}

		$crypto = $this->plugin->connect->check_crypto_class();
		echo '<tr><td>' . esc_attr__( 'Encrpytion Library: ', 'constant-contact-forms' ) . '</td><td>' . esc_attr( $this->exists_text( $crypto ) ) . '</td></tr>';

		echo '</table>';
	}

	/**
	 * Helper method to give us a display of something exists or not.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Function/class to check.
	 * @param string $type Function or class.
	 * @return string Emoji of checkmark.
	 */
	public function exists_text( $name, $type = '' ) {
		if ( 'f' === $type ) {
			$exists = function_exists( esc_attr( $name ) );
		} elseif ( 'c' === $type ) {
			$exists = class_exists( esc_attr( $name ) );
		} else {
			$exists = $name;
		}

		if ( $exists ) {
			return '✅';
		}

		return '🚫';
	}

	/**
	 * Commission a cron job to check on server status.
	 *
	 * @since 1.0.0
	 *
	 * @return string
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

		$cron_request = apply_filters( 'cron_request', [
			'url'  => site_url( 'wp-cron.php?doing_wp_cron=' . $doing_wp_cron ),
			'key'  => $doing_wp_cron,
			'args' => [
				'timeout'   => 3,
				'blocking'  => true,
				'sslverify' => apply_filters( 'https_local_ssl_verify', $sslverify ),
			],
		] );

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

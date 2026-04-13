<?php
/**
 * Constant Contact API Utility class.
 *
 * @package ConstantContact
 * @subpackage API Utility
 * @author Constant Contact
 * @since 2.18.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * API Utility class.
 *
 * @since 2.18.0
 */
class ConstantContact_API_Utility {

	/**
	 * Parent plugin class.
	 *
	 * @since 2.18.0
	 *
	 * @var object
	 */
	protected object $plugin;

	/**
	 * Constructor.
	 *
	 * @since 2.18.0
	 *
	 * @param object $plugin Parent plugin class.
	 */
	public function __construct( object $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Obfuscate the left side of email addresses at the `@`.
	 *
	 * @since 1.7.0
	 * @since 2.18.0 Moved to utility class.
	 *
	 * @param array $contact Contact data.
	 * @return array
	 */
	public function clear_email( array $contact ): array {
		$clean = [];
		foreach ( $contact as $contact_key => $contact_value ) {
			if ( is_array( $contact_value ) ) {
				$clean[ $contact_key ] = $this->clear_email( $contact_value );
			} elseif ( is_email( $contact_value ) ) {
				$email_parts           = explode( '@', $contact_value );
				$clean[ $contact_key ] = implode( '@', [ '***', $email_parts[1] ] );
			} else {
				$clean[ $contact_key ] = $contact_value;
			}
		}
		return $clean;
	}

	/**
	 * Obfuscate phone numbers.
	 *
	 * @since 1.13.0
	 * @since 2.18.0 Moved to utility class.
	 *
	 * @param array $contact Contact data.
	 * @return array
	 */
	public function clear_phone( array $contact ): array {
		$clean = $contact;
		foreach ( $contact as $contact_key => $contact_value ) {
			if ( is_array( $contact_value ) && ! empty( $contact_value['key'] ) && $contact_value['key'] === 'phone_number' ) {
				$clean[ $contact_key ]['val'] = '***-***-****';
			}
		}

		return $clean;
	}

	/**
	 * Remove hCaptcha data from logged data.
	 *
	 * @since 2.9.0
	 * @since 2.18.0 Moved to utility class.
	 *
	 * @param array $contact Contact data.
	 * @return array
	 */
	public function clear_hcaptcha( array $contact ): array {
		if ( array_key_exists( 'h-captcha-response', $contact ) ) {
			unset( $contact['h-captcha-response'] );
		}

		return $contact;
	}

	/**
	 * Pushes all error to api_error_message.
	 *
	 * @since 1.0.0
	 * @since 2.18.0 Moved to utility class.
	 *
	 * @throws Exception Throws Exception if encountered while attempting to log errors.
	 *
	 * @param array $errors Errors from API.
	 */
	public function log_errors( $errors ): void {
		if ( is_array( $errors ) ) {
			foreach ( $errors as $error ) {
				constant_contact_maybe_log_it(
					'API',
					$error
				);
			}
		}
	}

	/**
	 * Helper method to output a link for our connect modal.
	 *
	 * @since 1.0.0
	 * @since 2.18.0 Moved to utility class.
	 *
	 * @return string Signup URL.
	 */
	public function get_signup_link(): string {
		return 'https://www.constantcontact.com/signup';
	}


	/**
	 * Base64 encode URL.
	 *
	 * @since 2.0.0
	 * @since 2.18.0 Moved to utility class.
	 *
	 * @param string $data
	 *
	 * @return string
	 */
	public function base64url_encode( string $data ): string {
		return rtrim( strtr( base64_encode( $data ), '+/', '-_' ), '=' );
	}


	/**
	 * Obfuscate a value in our debug logs.
	 *
	 * Helps keep things private and not put into a potentially publicly accessed file.
	 *
	 * @since 2.1.0
	 * @since 2.18.0 Moved to utility class.
	 *
	 * @param string $data_item Item to obfuscate.
	 *
	 * @return string
	 */
	public function obfuscate_api_data_item( string $data_item ): string {
		$start = substr( $data_item, 0, 8 );
		return $start . '***';
	}
}

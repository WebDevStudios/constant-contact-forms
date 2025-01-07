<?php
/**
 * CaptchaService class.
 *
 * @package    ConstantContact
 * @subpackage Captcha
 * @author     Constant Contact
 * @since      NEXT
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

// phpcs:disable PEAR.NamingConventions.ValidClassName.Invalid -- OK classname.

/**
 * Class ConstantContact_CaptchaService.
 *
 * @since NEXT
 */
class ConstantContact_CaptchaService {

	/**
	 * Parent plugin class.
	 *
	 * @since NEXT
	 *
	 * @var object
	 */
	protected $plugin;

	/**
	 * The captcha service selected for use.
	 *
	 * @var string
	 * @since NEXT
	 */
	protected $captcha_service;


	/**
	 * The option key used to store the User-selected captcha service.
	 *
	 * @var string
	 * @since NEXT
	 */
	protected $captcha_service_option_key = '_ctct_captcha_service';

	/**
	 * Constructor.
	 *
	 * @since NEXT
	 *
	 * @param object $plugin Parent class object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		//$this->init();
		$this->maybe_initialize_default_captcha_service_option();
	}

	/**
	 * Get the selected captcha service.
	 *
	 * @since NEXT
	 * @return string The captcha service in use.
	 */
	public function get_captcha_service() {
		return $this->captcha_service;
	}

	/**
	 * Returns True if a captcha service is selected, False otherwise.
	 *
	 * @since NEXT
	 * @return bool True if a captcha service is selected, false otherwise.
	 */
	public function is_captcha_enabled() {

	}

	/**
	 * Get the selected captcha service.
	 *
	 * @since NEXT
	 * @return string Language for the hCaptcha object.
	 */
	private function maybe_initialize_default_captcha_service_option() {
		$settings_values = get_option( $this->plugin->settings->key );

		error_log( '$settings_values ' . var_export( $settings_values, true ) );

		// Bail if no options have been saved yet.
		if ( empty( $settings_values ) ) {
			return;
		}

		//$captcha_service_option_value = get_option( $this->captcha_service_option_key );
		$captcha_service_option_value = $settings_values[ $this->captcha_service_option_key ];

		error_log( '$this->captcha_service_option_key ' . var_export( $this->captcha_service_option_key, true ) );
		error_log( '$captcha_service_option_value ' . var_export( $captcha_service_option_value, true ) );

		// Bail if the captcha service option has already been set.
		if ( ! empty( $captcha_service_option_value ) ) {
			error_log( 'Captcha service already set!' );
			return;
		}

		$has_recaptcha_keys = ConstantContact_reCAPTCHA::has_recaptcha_keys();
		error_log( '$has_recaptcha_keys ' . var_export( $has_recaptcha_keys, true ) );

		// If the Google reCAPTCHA Site Key and Secret Key are set, set the Captcha Service to Google reCAPTCHA.
		if ( ! empty( $has_recaptcha_keys ) ) {
			$settings_values[ $this->captcha_service_option_key ] = 'recaptcha';
		} else {
			// Otherwise, set the Captcha Service option to None - Captcha Disabled, since no keys are present.
			$settings_values[ $this->captcha_service_option_key ] = 'disabled';
		}

		// Save the updated settings.
		//update_option( $this->plugin->settings->key, $settings_values );
	}
}

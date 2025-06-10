<?php
/**
 * CaptchaService class.
 *
 * @package    ConstantContact
 * @subpackage CaptchaService
 * @author     Constant Contact
 * @since      2.9.0
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

// phpcs:disable PEAR.NamingConventions.ValidClassName.Invalid -- OK classname.

/**
 * Class ConstantContact_CaptchaService.
 *
 * @since 2.9.0
 */
class ConstantContact_CaptchaService {

	/**
	 * The key that plugin options are stored under.
	 *
	 * @since 2.9.0
	 *
	 * @var string
	 */
	protected string $plugin_settings_key;

	/**
	 * The option key used to store the user-selected captcha service.
	 *
	 * @since 2.9.0
	 *
	 * @var string
	 */
	protected string $captcha_service_option_key = '_ctct_captcha_service';

	/**
	 * Constructor.
	 *
	 * @since 2.9.0
	 */
	public function __construct() {
		$this->plugin_settings_key = constant_contact()->settings->key;
		$this->maybe_initialize_captcha_service_option();
	}

	/**
	 * Get the selected captcha service.
	 *
	 * @since 2.9.0
	 *
	 * @return string The captcha service in use.
	 */
	public function get_selected_captcha_service() : string {
		$settings_values = get_option( $this->plugin_settings_key );

		return $settings_values[ $this->captcha_service_option_key ] ?? '';
	}

	/**
	 * Returns true if a captcha service is selected and keys are present, or false otherwise.
	 *
	 * @since 2.9.0
	 *
	 * @return bool True if a captcha service is selected and keys are present, or false otherwise.
	 */
	public function is_captcha_enabled() : bool {
		$captcha_service = $this->get_selected_captcha_service();

		// Bail early if the settings aren't available.
		if ( empty( $captcha_service ) ) {
			return false;
		}

		switch ( $captcha_service ) {
			case 'recaptcha' :
				return ConstantContact_reCAPTCHA::has_recaptcha_keys();

			case 'hcaptcha' :
				return ConstantContact_hCaptcha::has_hcaptcha_keys();

			default:
				return false;
		}
	}

	/**
	 * Set the Captcha service option based on previously existing
	 * setup from before 2.9.0 version.
	 *
	 * With version 2.9.0, we've introduced the ability to use hCaptcha or disable the captcha service globally
	 * in addition to maintining support for Google reCAPTCHA.
	 *
	 * @since 2.9.0
	 */
	private function maybe_initialize_captcha_service_option() {
		$plugin_settings = get_option( $this->plugin_settings_key );

		// Bail if no options have been saved yet. We'll let the user set the options manually since nothing needs to be migrated.
		if ( empty( $plugin_settings ) ) {
			return;
		}

		$captcha_service_option_value = $plugin_settings[ $this->captcha_service_option_key ] ?? null;

		// Bail if the captcha service option has already been set.
		if ( ! empty( $captcha_service_option_value ) ) {
			return;
		}

		$has_recaptcha_keys = ConstantContact_reCAPTCHA::has_recaptcha_keys();
		$has_hcaptcha_keys  = ConstantContact_hCaptcha::has_hcaptcha_keys();

		// If the Google reCAPTCHA Site Key and Secret Key are set, set the Captcha Service to Google reCAPTCHA.
		if ( ! empty( $has_recaptcha_keys ) ) {
			$plugin_settings[ $this->captcha_service_option_key ] = 'recaptcha';
		} elseif ( ! empty( $has_hcaptcha_keys ) ) {
			$plugin_settings[ $this->captcha_service_option_key ] = 'hcaptcha';
		} else {
			// Otherwise, set the Captcha Service option to 'None - Captcha Disabled', since no keys were present.
			$plugin_settings[ $this->captcha_service_option_key ] = 'disabled';
		}

		// Save the updated settings.
		update_option( $this->plugin_settings_key, $plugin_settings );
	}
}

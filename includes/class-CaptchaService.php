<?php
/**
 * CaptchaService class.
 *
 * @package    ConstantContact
 * @subpackage CaptchaService
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
	 * The key that plugin options are stored under.
	 *
	 * @since NEXT
	 *
	 * @var string
	 */
	protected $plugin_settings_key;

	/**
	 * The option key used to store the user-selected captcha service.
	 *
	 * @since NEXT
	 *
	 * @var string
	 */
	protected $captcha_service_option_key = '_ctct_captcha_service';

	/**
	 * Constructor.
	 *
	 * @since NEXT
	 */
	public function __construct() {
		$this->plugin_settings_key = constant_contact()->settings->key;
		$this->maybe_initialize_captcha_service_option();
	}

	/**
	 * Get the selected captcha service.
	 *
	 * @since NEXT
	 *
	 * @return string The captcha service in use.
	 */
	public function get_selected_captcha_service() {
		$settings_values = get_option( $this->plugin_settings_key );

		return $settings_values[ $this->captcha_service_option_key ] ?? '';
	}

	/**
	 * Returns true if a captcha service is selected and keys are present, or false otherwise.
	 *
	 * @since NEXT
	 *
	 * @return bool True if a captcha service is selected and keys are present, or false otherwise.
	 */
	public function is_captcha_enabled() {
		$captcha_service = $this->get_selected_captcha_service();

		// Bail early if the settings aren't available.
		if ( empty( $captcha_service ) ) {
			return false;
		}

		switch ( $captcha_service ) {
			case 'recaptcha' :
				return ConstantContact_reCAPTCHA::has_recaptcha_keys();
				break;

			case 'hcaptcha' :
				return ConstantContact_hCaptcha::has_hcaptcha_keys();
				break;

			default:
				return false;
		}
	}

	/**
	 * Set the Captcha service option based on previously existing
	 * set up from before NEXT version.
	 *
	 * @since NEXT
	 */
	private function maybe_initialize_captcha_service_option() {
		$plugin_settings = get_option( $this->plugin_settings_key );
		error_log( '$plugin_settings ' . var_export( $plugin_settings, true ) );

		// Bail if no options have been saved yet.
		if ( empty( $plugin_settings ) ) {
			return;
		}

		$captcha_service_option_value = $plugin_settings[ $this->captcha_service_option_key ] ?? null;
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
			$plugin_settings[ $this->captcha_service_option_key ] = 'recaptcha';
		} else {
			// Otherwise, set the Captcha Service option to None - Captcha Disabled, since no keys were present.
			$plugin_settings[ $this->captcha_service_option_key ] = 'disabled';
		}

		// Save the updated settings.
		update_option( $this->plugin_settings_key, $plugin_settings );
	}
}

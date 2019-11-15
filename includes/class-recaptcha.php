<?php
/**
 * Google reCAPTCHA Base.
 *
 * @package    ConstantContact
 * @subpackage reCAPTCHA
 * @author     Constant Contact
 * @since      1.7.0
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Class ConstantContact_reCAPTCHA.
 *
 * @since 1.7.0
 */
class ConstantContact_reCAPTCHA {

	/**
	 * Chosen reCAPTCHA version.
	 *
	 * @var string
	 * @since 1.7.0
	 */
	protected $version;

	/**
	 * Google reCAPTCHA site key.
	 *
	 * @var string
	 * @since 1.7.0
	 */
	protected $site_key;

	/**
	 * Google reCAPTCHA secret key.
	 *
	 * @var string
	 * @since 1.7.0
	 */
	protected $secret_key;

	/**
	 * Language code to use.
	 *
	 * @var string
	 * @since 1.7.0
	 */
	protected $lang_code;

	/**
	 * Google reCAPTCHA instance.
	 *
	 * @var \ReCaptcha\ReCaptcha
	 * @since 1.7.0
	 */
	public $recaptcha;

	/**
	 * Set our reCAPTCHA instance.
	 *
	 * @since 1.7.0
	 *
	 * @param \ReCaptcha\ReCaptcha|string $recaptcha Google reCAPTCHA instance.
	 */
	public function set_recaptcha_class( $recaptcha = '' ) {
		$this->recaptcha = $recaptcha;
	}

	/**
	 * Set our language code to use.
	 *
	 * @since 1.7.0
	 * @param string $lang_code Language code for the reCAPTCHA object.
	 */
	public function set_language( $lang_code ) {
		$this->lang_code = $lang_code;
	}

	/**
	 * Get our language code.
	 *
	 * @since 1.7.0
	 * @return string $lang_code Language code for the reCAPTCHA object.
	 */
	public function get_language() {
		return $this->lang_code;
	}

	/**
	 * Check if we have reCAPTCHA settings available to use with Google reCAPTCHA.
	 *
	 * @since 1.2.4
	 *
	 * @return bool
	 */
	public static function has_recaptcha_keys() {
		$site_key   = ctct_get_settings_option( '_ctct_recaptcha_site_key', '' );
		$secret_key = ctct_get_settings_option( '_ctct_recaptcha_secret_key', '' );

		return $site_key && $secret_key;
	}

	/**
	 * Return an array of our site key pair.
	 *
	 * @since 1.7.0
	 *
	 * @return array
	 */
	public function get_recaptcha_keys() {
		$keys               = [];
		$keys['site_key']   = ctct_get_settings_option( '_ctct_recaptcha_site_key', '' );
		$keys['secret_key'] = ctct_get_settings_option( '_ctct_recaptcha_secret_key', '' );

		return $keys;
	}

	/**
	 * Set our key properties.
	 *
	 * @since 1.7.0
	 */
	public function set_recaptcha_keys() {
		$keys = $this->get_recaptcha_keys();

		$this->site_key   = $keys['site_key'];
		$this->secret_key = $keys['secret_key'];
	}

	/**
	 * Return our chosen reCAPTCHA version setting.
	 *
	 * @since 1.7.0
	 *
	 * @return mixed
	 */
	public function get_recaptcha_version() {
		if ( ! isset( $this->version ) ) {
			$this->set_recaptcha_version();
		}

		return $this->version;
	}

	/**
	 * Set our chosen reCAPTCHA version setting.
	 *
	 * @since 1.7.0
	 */
	public function set_recaptcha_version() {
		$this->version = ctct_get_settings_option( '_ctct_recaptcha_version', '' );
	}
}

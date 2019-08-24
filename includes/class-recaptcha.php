<?php
/**
 * Google reCAPTCHA Base.
 *
 * @package    ConstantContact
 * @subpackage reCAPTCHA
 * @author     Constant Contact
 * @since      NEXT
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Class ConstantContact_reCAPTCHA.
 *
 * @since NEXT
 */
class ConstantContact_reCAPTCHA {

	/**
	 * Google reCAPTCHA site key.
	 *
	 * @var $site_key
	 * @since NEXT
	 */
	protected $site_key;

	/**
	 * Google reCAPTCHA secret key.
	 *
	 * @var $secret_key
	 * @since NEXT
	 */
	protected $secret_key;

	/**
	 * Language code to use.
	 *
	 * @var $lang_code
	 * @since NEXT
	 */
	protected $lang_code;

	/**
	 * Google reCAPTCHA instance.
	 *
	 * @var \ReCaptcha\ReCaptcha $recaptcha
	 * @since NEXT
	 */
	public $recaptcha;

	/**
	 * ConstantContact_reCAPTCHA constructor.
	 *
	 * @since NEXT
	 *
	 * @param \ReCaptcha\ReCaptcha $recaptcha Google reCAPTCHA instance.
	 */
	public function __construct( \ReCaptcha\ReCaptcha $recaptcha ) {
		$this->recaptcha = $recaptcha;
	}

	/**
	 * Set our language code to use.
	 *
	 * @since NEXT
	 * @param string $lang_code Language code for the reCAPTCHA object.
	 */
	public function set_language( $lang_code ) {
		$this->lang_code = $lang_code;
	}

	/**
	 * Get our language code.
	 *
	 * @since NEXT
	 * @return string $lang_code Language code for the reCAPTCHA object.
	 */
	public function get_language() {
		return $this->lang_code;
	}
}

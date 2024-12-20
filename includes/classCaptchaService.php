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
	 * The captcha service to use.
	 *
	 * @var string
	 * @since NEXT
	 */
	protected $captcha_service;

	/**
	 * Get the selected captcha service.
	 *
	 * @since NEXT
	 * @return string Language for the hCaptcha object.
	 */
	public function get_captcha_service() {
		return $this->captcha_service;
	}

	/**
	 * Set our language to use.
	 *
	 * @since NEXT
	 * @param string $language Language code for the hCaptcha object.
	 */
	public function set_language( $captcha_service ) {
		$this->captcha_service = $captcha_service;
	}
}

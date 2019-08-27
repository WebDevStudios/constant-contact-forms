<?php
/**
 * Google reCAPTCHA v2.
 *
 * AKA "I am human" checkbox.
 *
 * @package    ConstantContact
 * @subpackage reCAPTCHA
 * @author     Constant Contact
 * @since      NEXT
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Class ConstantContact_reCAPTCHA_v2
 *
 * @since NEXT
 */
class ConstantContact_reCAPTCHA_v3 extends ConstantContact_reCAPTCHA {
	/**
	 * ConstantContact_reCAPTCHA_v2 constructor.
	 *
	 * @since NEXT
	 *
	 * @param string               $site_key   API v2 site key.
	 * @param string               $secret_key API v2 secret key.
	 * @param \ReCaptcha\ReCaptcha $recaptcha  reCAPTCHA object.
	 */
	public function __construct( $site_key, $secret_key, $recaptcha ) {
		$this->site_key   = $site_key;
		$this->secret_key = $secret_key;

		parent::__construct( $recaptcha );
	}
}

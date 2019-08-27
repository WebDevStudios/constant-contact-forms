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
class ConstantContact_reCAPTCHA_v2 extends ConstantContact_reCAPTCHA {
	/**
	 * ConstantContact_reCAPTCHA_v2 constructor.
	 *
	 * @since NEXT
	 *
	 * @param string               $site_key   API v2 site key.
	 * @param string               $secret_key API v2 secret key.
	 * @param \ReCaptcha\ReCaptcha $recaptcha  reCAPTCHA object.
	 */
	public function __construct( $site_key, $secret_key, $recaptcha = '' ) {
		$this->site_key   = $site_key;
		$this->secret_key = $secret_key;

		if ( $recaptcha ) {
			parent::__construct( $recaptcha );
		}
	}

	/**
	 * Retrieve inline scripts for the reCAPTCHA form instance.
	 *
	 * @since NEXT
	 * @return string
	 */
	public function get_inline_script() {
		return '<script>function ctctEnableBtn(){ jQuery( "#ctct-submitted" ).attr( "disabled", false ); }function ctctDisableBtn(){ jQuery( "#ctct-submitted" ).attr( "disabled", "disabled" ); }</script>';
	}

	/**
	 * Retrieve the markup to house the Google reCAPTCHA checkbox.
	 *
	 * @since NEXT
	 * @return string
	 */
	public function get_inline_markup() {
		$tmpl = '<div class="g-recaptcha" data-sitekey="%s" data-callback="%s" data-expired-callback="%s"></div><script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=%s"></script>';

		return sprintf(
			$tmpl,
			$this->site_key,
			'ctctEnableBtn',
			'ctctDisableBtn',
			$this->lang_code
		);
	}
}

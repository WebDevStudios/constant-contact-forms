<?php
/**
 * Google reCAPTCHA v2.
 *
 * AKA "I am human" checkbox.
 *
 * @package    ConstantContact
 * @subpackage reCAPTCHA
 * @author     Constant Contact
 * @since      1.7.0
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Class ConstantContact_reCAPTCHA_v2
 *
 * @since 1.7.0
 */
class ConstantContact_reCAPTCHA_v2 extends ConstantContact_reCAPTCHA {
	/**
	 * Retrieve inline scripts for the reCAPTCHA form instance.
	 *
	 * @since 1.7.0
	 *
	 * @return string
	 */
	public function get_inline_script() {
		return '<script>function ctctEnableBtn(){ jQuery( "#ctct-submitted" ).attr( "disabled", false ); }function ctctDisableBtn(){ jQuery( "#ctct-submitted" ).attr( "disabled", "disabled" ); }</script>';
	}

	/**
	 * Retrieve the markup to house the Google reCAPTCHA checkbox.
	 *
	 * @since 1.7.0
	 *
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

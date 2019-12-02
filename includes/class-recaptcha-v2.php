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
	 * Size to use for the reCAPTCHA box.
	 *
	 * @var string
	 * @since 1.7.0
	 */
	public $recaptcha_size;

	/**
	 * Retrieve inline scripts for the reCAPTCHA form instance.
	 *
	 * @since 1.7.0
	 *
	 * @return string
	 */
	public function enqueue_scripts() {
		wp_add_inline_script( 'jquery', 'function ctctEnableBtn(){ jQuery( "#ctct-submitted" ).attr( "disabled", false ); }function ctctDisableBtn(){ jQuery( "#ctct-submitted" ).attr( "disabled", "disabled" ); }' );
	}

	/**
	 * Retrieve the markup to house the Google reCAPTCHA checkbox.
	 *
	 * @since 1.7.0
	 *
	 * @return string
	 */
	public function get_inline_markup() {
		return sprintf(
			'<div class="g-recaptcha" data-sitekey="%1$s" data-callback="ctctEnableBtn" data-expired-callback="ctctDisableBtn" data-size="%2$s"></div><script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=%3$s"></script>',
			$this->site_key,
			$this->recaptcha_size,
			$this->lang_code
		);
	}

	/**
	 * Set the reCAPTCHA size.
	 *
	 * @since 1.7.0
	 *
	 * @param string $size reCAPTCHA size to specify.
	 */
	public function set_size( $size ) {
		$this->recaptcha_size = $size;
	}
}

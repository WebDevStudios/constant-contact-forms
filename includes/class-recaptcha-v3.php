<?php
/**
 * Google reCAPTCHA v3.
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
 * Class ConstantContact_reCAPTCHA_v3
 *
 * @since 1.7.0
 */
class ConstantContact_reCAPTCHA_v3 extends ConstantContact_reCAPTCHA {

	/**
	 * Enqueue our needed scripts.
	 *
	 * @since 1.7.0
	 *
	 * @return null
	 */
	public function enqueue_scripts() {
		$this->set_recaptcha_keys();

		if ( ! ConstantContact_reCAPTCHA::has_recaptcha_keys() ) {
			return;
		}

		$debug  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true );
		$suffix = ( true === $debug ) ? '' : '.min';

		wp_enqueue_script(
			'recaptcha-lib',
			"//www.google.com/recaptcha/api.js?render={$this->site_key}",
			[],
			Constant_Contact::VERSION,
			true
		);

		wp_enqueue_script(
			'recaptcha-v3',
			constant_contact()->url() . "assets/js/ctct-plugin-recaptcha{$suffix}.js",
			[ 'jquery', 'recaptcha-lib' ],
			Constant_Contact::VERSION,
			true
		);
		wp_add_inline_script( 'recaptcha-v3', "recaptchav3 = {\"site_key\":\"{$this->site_key}\"}" );
	}
}

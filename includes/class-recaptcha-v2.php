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
		$debug  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true );
		$suffix = ( true === $debug ) ? '' : '.min';

		wp_enqueue_script(
			'recaptcha-v2',
			constant_contact()->url() . "assets/js/ctct-plugin-recaptcha-v2{$suffix}.js",
			[ 'jquery' ],
			Constant_Contact::VERSION,
			true
		);

		wp_enqueue_script(
			'recaptcha-lib-v2',
			add_query_arg( [
				'hl'     => $this->lang_code,
				'onload' => 'renderReCaptcha',
				'render' => 'explicit',
			], '//www.google.com/recaptcha/api.js' ),
			[ 'recaptcha-v2' ],
			Constant_Contact::VERSION,
			true
		);

		add_filter( 'script_loader_tag', [ $this, 'add_script_attributes' ], 10, 2 );
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
			'<div class="g-recaptcha" data-sitekey="%1$s" data-callback="ctctEnableBtn" data-expired-callback="ctctDisableBtn" data-size="%2$s"></div>',
			$this->site_key,
			$this->recaptcha_size
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

	public function add_script_attributes( $tag, $handle ) {
		if ( 'recaptcha-lib-v2' !== $handle ) {
			return $tag;
		}

		return str_replace( '<script', '<script async="async" defer', $tag );
	}
}

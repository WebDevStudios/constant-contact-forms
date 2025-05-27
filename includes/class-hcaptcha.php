<?php
/**
 * hCaptcha class.
 *
 * @package    ConstantContact
 * @subpackage hCaptcha
 * @author     Constant Contact
 * @since      2.9.0
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

// phpcs:disable PEAR.NamingConventions.ValidClassName.Invalid -- OK classname.

/**
 * Class ConstantContact_hCaptcha.
 *
 * @since 2.9.0
 */
class ConstantContact_hCaptcha {

	/**
	 * hCaptcha site key.
	 *
	 * @var string
	 * @since 2.9.0
	 */
	protected string $site_key;

	/**
	 * hCaptcha secret key.
	 *
	 * @var string
	 * @since 2.9.0
	 */
	protected string $secret_key;

	/**
	 * hCaptcha theme to use.
	 * Options are 'light', 'dark', and 'auto';
	 *
	 * @var string
	 * @since 2.9.0
	 */
	protected string $theme;

	/**
	 * Size to use for the hCaptcha box.
	 * Options are 'normal', 'compact', and 'invisible'.
	 *
	 * @var string
	 * @since 2.9.0
	 */
	public string $size;

	/**
	 * Language code to use.
	 * @See https://docs.hcaptcha.com/languages.
	 * Use 'auto' (default), or specify the language.
	 *
	 * @var string
	 * @since 2.9.0
	 */
	protected string $language;

	/**
	 * Mode to use.
	 * Use 'live' or 'test' mode. In 'test' mode, predefined keys are used.
	 *
	 * @var string
	 * @since 2.9.0
	 */
	protected string $mode;

	/**
	 * Return an array of our site key pair.
	 *
	 * @since 2.9.0
	 *
	 * @return array
	 */
	public function get_hcaptcha_keys() : array {
		$keys               = [];
		$keys['site_key']   = constant_contact_get_option( '_ctct_hcaptcha_site_key', '' );
		$keys['secret_key'] = constant_contact_get_option( '_ctct_hcaptcha_secret_key', '' );

		return $keys;
	}

	/**
	 * Set our key properties.
	 *
	 * @since 2.9.0
	 */
	public function set_hcaptcha_keys() {
		$keys = $this->get_hcaptcha_keys();

		$this->site_key   = $keys['site_key'];
		$this->secret_key = $keys['secret_key'];
	}

	/**
	 * Check if we have hCaptcha keys set.
	 *
	 * @since 2.9.0
	 *
	 * @return bool
	 */
	public static function has_hcaptcha_keys() : bool {
		$site_key   = constant_contact_get_option( '_ctct_hcaptcha_site_key', '' );
		$secret_key = constant_contact_get_option( '_ctct_hcaptcha_secret_key', '' );

		return $site_key && $secret_key;
	}

	/**
	 * Get the theme to use.
	 *
	 * @since 2.9.0
	 *
	 * @return string $theme Theme for the hCaptcha object.
	 */
	public function get_theme() : string {
		return $this->theme;
	}

	/**
	 * Set the hCaptcha theme.
	 *
	 * @since 2.9.0
	 *
	 * @param string $theme hCaptcha theme to use.
	 */
	public function set_theme( string $theme ) {
		$this->theme = $theme;
	}

	/**
	 * Get the size to use.
	 *
	 * @since 2.9.0
	 *
	 * @return string $size Size for the hCaptcha object.
	 */
	public function get_size() : string {
		return $this->size;
	}

	/**
	 * Set the hCaptcha size.
	 *
	 * @since 2.9.0
	 *
	 * @param string $size hCaptcha size to specify.
	 */
	public function set_size( string $size ) {
		$this->size = $size;
	}

	/**
	 * Get our language.
	 *
	 * @since 2.9.0
	 *
	 * @return string $language Language for the hCaptcha object.
	 */
	public function get_language() : string {
		return $this->language;
	}

	/**
	 * Set our language to use.
	 *
	 * @since 2.9.0
	 *
	 * @param string $language Language code for the hCaptcha object.
	 */
	public function set_language( string $language ) {
		$this->language = $language;
	}

	/**
	 * Get the mode to use.
	 *
	 * @since 2.9.0
	 *
	 * @return string $mode The mode for the hCaptcha object.
	 */
	public function get_mode() : string {
		return $this->mode;
	}

	/**
	 * Set the hCaptcha mode.
	 *
	 * @since 2.9.0
	 *
	 * @param string $mode hCaptcha mode to use.
	 */
	public function set_mode( string $mode ) {
		$this->mode = $mode;
	}

	/**
	 * Retrieve inline scripts for the hCaptcha form instance.
	 *
	 * @since 2.9.0
	 */
	public function enqueue_scripts() {
		$this->set_hcaptcha_keys();

		if ( ! self::has_hcaptcha_keys() ) {
			return;
		}

		$debug  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true );
		$suffix = ( true === $debug ) ? '' : '.min';

		wp_enqueue_script(
			'hcaptcha',
			constant_contact()->url() . "assets/js/ctct-plugin-hcaptcha$suffix.js",
			[],
			Constant_Contact::VERSION,
			true
		);

		wp_enqueue_script(
			'hcaptcha-api',
			add_query_arg(
				[
					'hl'     => $this->get_language(),
					'onload' => 'renderhCaptcha',
					'render' => 'explicit',
				],
				'//js.hcaptcha.com/1/api.js'
			),
			[ 'hcaptcha' ],
			Constant_Contact::VERSION,
			true
		);

		add_filter( 'script_loader_tag', [ $this, 'add_script_attributes' ], 10, 2 );
	}

	/**
	 * Retrieve the hCaptcha markup.
	 *
	 * @since 2.9.0
	 *
	 * @return string
	 */
	public function get_inline_markup() : string {
		return sprintf(
			'<div class="h-captcha"
				data-sitekey="%1$s"
				data-theme="%2$s"
				data-size="%3$s"
				data-mode="%4$s"
				data-callback="ctcthCaptchaEnableBtn"
				data-expired-callback="ctcthCaptchaDisableBtn"
			></div>',
			esc_attr( $this->site_key ),
			esc_attr( $this->get_theme() ),
			esc_attr( $this->get_size() ),
			esc_attr( $this->get_mode() )
		);
	}

	/**
	 * Add script attributes.
	 *
	 * @since 2.9.0
	 *
	 * @param  string $tag    Script tag.
	 * @param  string $handle Script handle.
	 * @return string         Script tag.
	 */
	public function add_script_attributes( string $tag, string $handle ) : string {
		if ( 'hcaptcha-api' !== $handle ) {
			return $tag;
		}

		return str_replace( '<script', '<script async="async" defer', $tag );
	}
}

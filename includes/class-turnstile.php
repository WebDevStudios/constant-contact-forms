<?php
/**
 * turnstile class.
 *
 * @package    ConstantContact
 * @subpackage turnstile
 * @author     Constant Contact
 * @since      2.15.1
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

// phpcs:disable PEAR.NamingConventions.ValidClassName.Invalid -- OK classname.

/**
 * Class ConstantContact_turnstile.
 *
 * @since 2.15.1
 */
class ConstantContact_turnstile {

	/**
	 * turnstile site key.
	 *
	 * @var string
	 * @since 2.15.1
	 */
	protected string $site_key;

	/**
	 * turnstile secret key.
	 *
	 * @var string
	 * @since 2.15.1
	 */
	protected string $secret_key;

	/**
	 * turnstile theme to use.
	 * Options are 'light', 'dark', and 'auto';
	 *
	 * @var string
	 * @since 2.15.1
	 */
	protected string $theme;

	/**
	 * Size to use for the turnstile box.
	 * Options are 'normal', 'compact', and 'flexible'.
	 *
	 * @var string
	 * @since 2.15.1
	 */
	public string $size;

	/**
	 * Language code to use.
	 * @See https://developers.cloudflare.com/turnstile/reference/supported-languages/.
	 * Use 'auto' (default), or specify the language.
	 *
	 * @var string
	 * @since 2.15.1
	 */
	protected string $language;

	/**
	 * Mode to use.
	 * Use 'live' or 'test' mode. In 'test' mode, predefined keys are used.
	 *
	 * @var string
	 * @since 2.15.1
	 */
	protected string $mode;

	/**
	 * Return an array of our site key pair.
	 *
	 * @since 2.15.1
	 *
	 * @return array
	 */
	public function get_turnstile_keys() : array {
		$keys               = [];
		$keys['site_key']   = constant_contact_get_option( '_ctct_turnstile_site_key', '' );
		$keys['secret_key'] = constant_contact_get_option( '_ctct_turnstile_secret_key', '' );

		return $keys;
	}

	/**
	 * Set our key properties.
	 *
	 * @since 2.15.1
	 */
	public function set_turnstile_keys() {
		$keys = $this->get_turnstile_keys();

		$this->site_key   = $keys['site_key'];
		$this->secret_key = $keys['secret_key'];
	}

	/**
	 * Check if we have turnstile keys set.
	 *
	 * @since 2.15.1
	 *
	 * @return bool
	 */
	public static function has_turnstile_keys() : bool {
		$site_key   = constant_contact_get_option( '_ctct_turnstile_site_key', '' );
		$secret_key = constant_contact_get_option( '_ctct_turnstile_secret_key', '' );

		return $site_key && $secret_key;
	}

	/**
	 * Get the theme to use.
	 *
	 * @since 2.15.1
	 *
	 * @return string $theme Theme for the turnstile object.
	 */
	public function get_theme() : string {
		return $this->theme;
	}

	/**
	 * Set the turnstile theme.
	 *
	 * @since 2.15.1
	 *
	 * @param string $theme turnstile theme to use.
	 */
	public function set_theme( string $theme ) {
		$this->theme = $theme;
	}

	/**
	 * Get the size to use.
	 *
	 * @since 2.15.1
	 *
	 * @return string $size Size for the turnstile object.
	 */
	public function get_size() : string {
		return $this->size;
	}

	/**
	 * Set the turnstile size.
	 *
	 * @since 2.15.1
	 *
	 * @param string $size turnstile size to specify.
	 */
	public function set_size( string $size ) {
		$this->size = $size;
	}

	/**
	 * Get our language.
	 *
	 * @since 2.15.1
	 *
	 * @return string $language Language for the turnstile object.
	 */
	public function get_language() : string {
		return $this->language;
	}

	/**
	 * Set our language to use.
	 *
	 * @since 2.15.1
	 *
	 * @param string $language Language code for the turnstile object.
	 */
	public function set_language( string $language ) {
		$this->language = $language;
	}

	/**
	 * Get the mode to use.
	 *
	 * @since 2.15.1
	 *
	 * @return string $mode The mode for the turnstile object.
	 */
	public function get_mode() : string {
		return $this->mode;
	}

	/**
	 * Set the turnstile mode.
	 *
	 * @since 2.15.1
	 *
	 * @param string $mode turnstile mode to use.
	 */
	public function set_mode( string $mode ) {
		$this->mode = $mode;
	}

	/**
	 * Retrieve inline scripts for the turnstile form instance.
	 *
	 * @since 2.15.1
	 */
	public function enqueue_scripts() {
		$this->set_turnstile_keys();

		if ( ! self::has_turnstile_keys() ) {
			return;
		}

		$debug  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true );
		$suffix = ( true === $debug ) ? '' : '.min';

		wp_enqueue_script(
			'turnstile',
			constant_contact()->url() . "assets/js/ctct-plugin-turnstile$suffix.js",
			[],
			Constant_Contact::VERSION,
			true
		);

		wp_enqueue_script(
			'turnstile-api',
			add_query_arg(
				[
					'render' => 'explicit',
				],
				'https://challenges.cloudflare.com/turnstile/v0/api.js'
			),
			[ 'turnstile' ],
			null,
			true
		);

		add_filter( 'script_loader_tag', [ $this, 'add_script_attributes' ], 10, 2 );
	}

	/**
	 * Retrieve the turnstile markup.
	 *
	 * @since 2.15.1
	 *
	 * @return string
	 */
	public function get_inline_markup() : string {
		return sprintf(
			'<div class="turnstile"
				data-sitekey="%1$s"
				data-theme="%2$s"
				data-size="%3$s"
				data-callback="ctctTurnstileEnableBtn"
				data-expired-callback="ctctTurnstileDisableBtn"
			></div>',
			esc_attr( $this->site_key ),
			esc_attr( $this->get_theme() ),
			esc_attr( $this->get_size() )
		);
	}

	/**
	 * Add script attributes.
	 *
	 * @since 2.15.1
	 *
	 * @param  string $tag    Script tag.
	 * @param  string $handle Script handle.
	 * @return string         Script tag.
	 */
	public function add_script_attributes( string $tag, string $handle ) : string {
		if ( 'turnstile-api' !== $handle ) {
			return $tag;
		}

		return str_replace( '<script', '<script defer', $tag );
	}
}

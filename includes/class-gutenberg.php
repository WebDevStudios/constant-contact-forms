<?php
/**
 * Gutenberg Support
 *
 * @package ConstantContact
 * @subpackage Gutenberg
 * @author Constant Contact
 * @since 1.5.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * This class get's everything up an running for Gutenberg support.
 *
 * @since 1.5.0
 */
class ConstantContact_Gutenberg {

	/**
	 * Parent plugin class.
	 *
	 * @since 1.5.0
	 * @var object
	 */
	protected object $plugin;

	/**
	 * Constructor.
	 *
	 * @since 1.5.0
	 *
	 * @param object $plugin Parent plugin.
	 */
	public function __construct( object $plugin ) {
		$this->plugin = $plugin;

		if ( $this->meets_requirements() ) {
			add_action( 'init', [ $this, 'register_blocks' ] );
		}
	}

	/**
	 * Check requirements.
	 *
	 * @author Eric Fuller
	 * @since  1.5.0
	 * @return bool
	 */
	private function meets_requirements() : bool {
		global $wp_version;

		return version_compare( $wp_version, '5.0.0' ) >= 0;
	}

	/**
	 * Register Gutenberg blocks.
	 *
	 * @author Eric Fuller
	 * @since 1.5.0
	 */
	public function register_blocks() {
		register_block_type(
			plugin_dir_path( dirname( __FILE__ ) ) . 'build',
			[ 'render_callback' => [ $this, 'display_single_contact_form' ] ]
		);
	}

	/**
	 * Display the single contact form block.
	 *
	 * @author Eric Fuller
	 * @since 1.5.0
	 *
	 * @param array $attributes The block attributes.
	 * @return string
	 */
	public function display_single_contact_form( array $attributes ) : string {
		if ( empty( $attributes['selectedForm'] ) ) {
			return '';
		}

		$display_title = true;
		if ( empty( $attributes['displayTitle'] ) || 'false' === $attributes['displayTitle'] ) {
			$display_title = false;
		}

		ob_start();
		echo constant_contact_get_form( absint( $attributes['selectedForm'] ), $display_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- XSS OK.
		return ob_get_clean();
	}
}

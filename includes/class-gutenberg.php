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
	protected $plugin;

	/**
	 * Constructor.
	 *
	 * @since 1.5.0
	 *
	 * @param object $plugin Parent plugin.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		if ( $this->meets_requirements() ) {
			$this->register_blocks();
		}
	}

	/**
	 * Check requirements.
	 *
	 * @author Eric Fuller
	 * @since  1.5.0
	 * @return bool
	 */
	private function meets_requirements() {
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
		register_block_type( 'constant-contact/single-contact-form', [
			'attributes'      => [
				'selectedForm' => [
					'type' => 'number',
				],
			],
			'render_callback' => [ $this, 'display_single_contact_form' ],
		] );
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
	public function display_single_contact_form( $attributes ) {
		if ( empty( $attributes['selectedForm'] ) ) {
			return '';
		}

		ob_start();
		echo constant_contact_get_form( absint( $attributes['selectedForm'] ) ); // WPCS: XSS OK.
		return ob_get_clean();
	}
}

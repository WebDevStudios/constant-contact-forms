<?php
/**
 * Gutenberg Support
 *
 * @package ConstantContact
 * @subpackage Gutenberg
 * @author Constant Contact
 * @since 1.5.0
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
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since 1.5.0
	 *
	 * @param object $plugin Parent plugin.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
		$this->register_blocks();
	}

	/**
	 * Register Gutenberg blocks.
	 *
	 * @author Eric Fuller
	 * @since 1.5.0
	 */
	public function register_blocks() {
		register_block_type( 'constant-contact/single-contact-form', array(
			'attributes'      => array(
				'selectedForm' => array(
					'type' => 'number',
				),
			),
			'render_callback' => array( $this, 'display_single_contact_form' ),
		));
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

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public function hooks() {
		// Hooks go here.
	}
}

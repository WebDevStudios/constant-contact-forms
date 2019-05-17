<?php
/**
 * Registers the [ctct] shortcode.
 *
 * @package ConstantContact
 * @subpackage Shortcode
 * @author Constant Contact
 * @since 1.0.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Registers the [ctct] shortcode.
 *
 * @since 1.0.0
 */
class ConstantContact_Shortcode {

	/**
	 * The Shortcode Tag.
	 *
	 * @since 1.6.0
	 * @var string
	 */
	public $tag = 'ctct';

	/**
	 * Constructor.
	 *
	 * @since 1.6.0
	 *
	 * @param Constant_Contact $plugin Parent plugin class.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Gets the shortcode atts supported by this shortcode. Filterable via shortcode_atts_{$shortcode} filter.
	 *
	 * @since 1.6.0
	 *
	 * @see shortcode_atts()
	 * @return array
	 */
	public function get_atts() {
		return [
			'form'       => '0',
			'show_title' => 'false',
		];
	}

	/**
	 * Registers the [ctct] shortcode.
	 *
	 * @since 1.6.0
	 */
	public function register_shortcode() {
		add_shortcode( $this->tag, [ $this->plugin->display_shortcode, 'render_shortcode' ] );
	}
}

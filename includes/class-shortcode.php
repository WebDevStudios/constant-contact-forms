<?php
/**
 * Shortcode button.
 *
 * @package ConstantContact
 * @subpackage Shortcode
 * @author Constant Contact
 * @since 1.0.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Extends WDS_Shortcodes to easily build up our shortcode.
 *
 * Sets up shortcode
 *
 * @since 1.0.0
 */
class ConstantContact_Shortcode {

	/**
	 * The Shortcode Tag.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $shortcode = 'ctct';

	/**
	 * Default attributes applied to the shortcode.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $atts_defaults = [];

	/**
	 * Shortcode Output.
	 *
	 * @since 1.0.0
	 * @return string shortcode html
	 */
	public function shortcode() {

		$custom_atts = [
			'form'       => '',
			'show_title' => true,
		];

		// Attributes.
		$atts = shortcode_atts( $custom_atts, $this->shortcode_object->atts );

		// Use our helper class to display the shortcode.
		return constant_contact()->display_shortcode->shortcode_wrapper( $atts );
	}

	/**
	 * Override for attribute getter.
	 *
	 * You can use this to override specific attribute acquisition
	 * ex. Getting attributes from options, post_meta, etc...
	 *
	 * @see WDS_Shortcode::att
	 *
	 * @since 1.0.0
	 * @param string      $att     Attribute to override.
	 * @param string|null $default Default value.
	 * @return string
	 */
	public function att( $att, $default = null ) {
		$current_value = parent::att( $att, $default );
		return $current_value;
	}
}

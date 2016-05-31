<?php
/**
 * ConstantContact_Shortcodes class
 *
 * @package ConstantContactShortcodes
 * @subpackage ConstantContact
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Class ConstantContact_Shortcodes
 */
class ConstantContact_Shortcodes {

	/**
	 * Holds an instance of the object.
	 *
	 * @since 1.0.0
	 * @var ConstantContact_Shortcodes
	 */
	private static $instance = null;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
	}

	/**
	 * Returns the running object
	 *
	 * @since 1.0.0
	 *
	 * @return ConstantContact_Shortcodes
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new ConstantContact_Shortcodes();
			self::$instance->hooks();
		}
		return self::$instance;
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'init' ) );
	}


	/**
	 * Hooked to WP init.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_shortcode( 'ctct', array( $this, 'ctct_shortcode' ) );
	}

	// Add Shortcode
	public function ctct_shortcode( $atts ) {

		// Attributes
		$atts = shortcode_atts(
			array(
				'form' => '',
			),
			$atts
		);

		$meta = get_post_meta( $atts['form'] );
		$form_data = $this->get_field_meta( $meta );

		ob_start();
		$shortcode = require( constant_contact()->dir() . 'templates/form.php' );
		$shortcode = ob_get_contents();
		ob_end_clean();
		echo $shortcode;
	}

	/**
	 * Proccess cmb2 options into form data array
	 *
	 * @param  array $form_meta post meta.
	 * @return array  form field data
	 */
	public function get_field_meta( $form_meta ) {

		if ( empty( $form_meta ) ) {
			return false;
		}

		$form_data = maybe_unserialize( $form_meta['fields_group'][0] );
		$fields = array();

		foreach ( $form_data as $key => $value ) {

			$fields[ $key ]['name'] = $form_data[ $key ]['_ctct_field_name'];

			if ( 'on' === $form_data[ $key ]['_ctct_required_field'] ) {
				$fields[ $key ]['required'] = $form_data[ $key ]['_ctct_required_field'];
			}
		}
		return $fields;
	}
}

/**
 * Helper function to get/return the ConstantContact_Shortcodes object.
 *
 * @since 1.0.0
 *
 * @return ConstantContact_Shortcodes object.
 */
function constantcontact_shortcodes() {
	return ConstantContact_Shortcodes::get_instance();
}

// Get it started.
constantcontact_shortcodes();

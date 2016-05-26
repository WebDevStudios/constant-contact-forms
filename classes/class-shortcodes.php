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
	 * @param  array $form_meta raw form data.
	 * @return array  form field data
	 */
	public function get_field_meta( $form_meta ) {

		foreach ( $form_meta as $meta => $value ) {
			if ( '_ctct_' !== substr( $meta, 0, 6 ) ) {
				unset( $form_meta[ $meta ] );
			}
		}
		unset( $form_meta['_ctct_description'] );

		// Move custom fields to end of array.
		$custom_v = $form_meta['_ctct_custom'];
		unset( $form_meta['_ctct_custom'] );
		$form_meta['_ctct_custom'] = $custom_v ;

		$fields = array();
		$values = array();

		$form = cmb2_get_metabox( 'fields_metabox' );
		$fields = $form->meta_box['fields'];

		foreach ( $form_meta as $field => $value ) {

			if ( '_ctct_custom' === $field ) {
				$custom = maybe_unserialize( $form_meta['_ctct_custom'][0] );
				foreach ( $custom as $field => $value ) {
					$values[ '_ctct_custom' . '_' . $field ]['name'] = $custom[$field];
				}
			} else {
				$values[ $field ]['name'] = $fields[ $field ]['name'];
			}
		}
		return $values;
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

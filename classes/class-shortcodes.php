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
	 * @var BuddyExtender_Admin
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
	 * @return BuddyExtender_Admin
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

		echo '<form id="myForm" action="#" method="post">';

		foreach ( $meta as $key => $value ) {

			if ( '_ctct_' === substr( $key, 0, 6 ) ) {
				$meta = $this->get_field_meta( $key );
				echo '<div><label>' . $meta . '</label>';
				echo '<input type="text" name="'. $key .'" value="" tabindex="1"></div>';
			}

		}

		echo '</form>';


	}

	public function get_field_meta( $field_id ) {

		$fields = array();

		$form = cmb2_get_metabox( 'fields_metabox' );

		//var_dump($form);

		foreach ( $form->meta_box['fields'] as $field => $value ) {

			//var_dump($field);

			if ( is_array( $value )  ) {
				foreach ( $value as $values ) {
					$fields[$field] = $form->meta_box['fields'][$field];
				}
			} else {
				$fields[$field] = $form->meta_box['fields'][$field];
			}
		}

		var_dump($fields);

		foreach ( $fields as $field => $value ) {



			if ( $field_id === $field  ) {
				return $value['name'];
			}
		}
		return false;
	}
}

/**
 * Helper function to get/return the BPExtender_Admin object.
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

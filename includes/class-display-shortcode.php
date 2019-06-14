<?php
/**
 * Handles the output of the shortcode.
 *
 * @package ConstantContact
 *
 * @subpackage DisplayShortcode
 * @author Constant Contact
 * @since 1.0.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Helper class that gets called to display our stuff on the front-end via a shortcode.
 *
 * @since 1.0.0
 */
class ConstantContact_Display_Shortcode {

	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	protected $plugin;

	/**
	 * Track form instances on page.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected static $form_instance = 0;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $plugin Parent plugin class.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_display_styles' ] );
	}

	/**
	 * Renders [ctct] shortcodes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function render_shortcode( $atts ) {

		$atts = shortcode_atts(
			$this->plugin->shortcode->get_atts(),
			$atts,
			$this->plugin->shortcode->tag
		);

		if ( ! isset( $atts['form'] ) ) {
			return '';
		}

		$show_title = ( isset( $atts['show_title'] ) && 'true' === $atts['show_title'] ) ? true : false;

		return $this->get_form( $atts['form'], $show_title );
	}

	/**
	 * Get a form from ID.
	 *
	 * @since 1.0.0
	 *
	 * @param int  $form_id Form ID.
	 * @param bool $show_title If true, show the form title.
	 * @return string
	 */
	public function get_form( $form_id, $show_title = false ) {

		$form_id = absint( $form_id );

		if ( ! $form_id ) {
			return '';
		}

		$meta = get_post_meta( $form_id );

		if ( ! $meta ) {
			return '';
		}

		$form_data = $this->get_field_meta( $meta, $form_id );
		$form      = sprintf(
			'<div data-form-id="%1$s" id="ctct-form-wrapper-%2$s" class="ctct-form-wrapper">%3$s</div>',
			esc_attr( $form_id ),
			esc_attr( self::$form_instance ),
			constant_contact()->display->form( $form_data, $form_id, $show_title )
		);

		++self::$form_instance;

		return $form;
	}

	/**
	 * Display a form to the screen.
	 *
	 * @since 1.0.0
	 *
	 * @param int  $form_id Form ID to display.
	 * @param bool $show_title If true, show the title.
	 */
	public function display_form( $form_id, $show_title = false ) {
		echo $this->get_form( absint( $form_id ), $show_title ); // WPCS: XSS Ok.
	}

	/**
	 * Proccess cmb2 options into form data array.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form_meta Post meta.
	 * @param int   $form_id   Form ID.
	 * @return string Form field data.
	 */
	public function get_field_meta( $form_meta, $form_id ) {

		if ( empty( $form_meta ) || ! is_array( $form_meta ) ) {
			return '';
		}

		if (
			isset( $form_meta['custom_fields_group'] ) &&
			$form_meta['custom_fields_group'] &&
			isset( $form_meta['custom_fields_group'][0] )
		) {
			return $this->get_field_values( $form_meta['custom_fields_group'][0], $form_meta, $form_id );
		}
		return '';
	}

	/**
	 * Get custom field values from post meta data from form CPT post.
	 *
	 * @since 1.0.0
	 *
	 * @param array $custom_fields Custom fields to parse through.
	 * @param array $full_data     Array of full data.
	 * @param int   $form_id       Form ID.
	 * @return array Form field markup.
	 */
	public function get_field_values( $custom_fields, $full_data, $form_id ) {

		$fields = $this->generate_field_values_for_fields( maybe_unserialize( $custom_fields ) );

		if ( $form_id ) {
			$fields['options']['form_id'] = $form_id;
		}

		$fields['options']['description'] = $this->get_nested_value_from_data( '_ctct_description', $full_data );

		$fields['options']['optin'] = $this->generate_optin_data( $full_data );

		return $fields;
	}

	/**
	 * Get all our data from our fields.
	 *
	 * @since 1.0.0
	 *
	 * @param array $custom_fields All custom fields data.
	 * @return array Fields array of converted data.
	 */
	public function generate_field_values_for_fields( $custom_fields ) {

		$fields = [];

		if ( ! is_array( $custom_fields ) ) {
			return $fields;
		}

		foreach ( $custom_fields as $key => $value ) {
			if ( ! isset( $custom_fields ) || ! isset( $custom_fields[ $key ] ) ) {
				continue;
			}

			$fields = $this->set_field( '_ctct_field_label', 'name', $key, $fields, $custom_fields );

			$fields = $this->set_field( '_ctct_map_select', 'map_to', $key, $fields, $custom_fields );
			$fields = $this->set_field( '_ctct_map_select', 'type', $key, $fields, $custom_fields );

			$fields = $this->set_field( '_ctct_field_desc', 'description', $key, $fields, $custom_fields );

			$fields['fields'][ $key ]['required'] = (
				isset( $custom_fields[ $key ]['_ctct_required_field'] ) &&
				'on' === $custom_fields[ $key ]['_ctct_required_field']
			);
		}

		return $fields;
	}

	/**
	 * Helper method to set our $fields array keys.
	 *
	 * @since 1.0.0
	 *
	 * @param string $from_key      Key to grab from $custom_fields.
	 * @param string $to_key        Key to use for return $fields.
	 * @param string $key           Field key.
	 * @param array  $fields        Current $fields array.
	 * @param array  $custom_fields All $custom_fields.
	 * @return array
	 */
	public function set_field( $from_key, $to_key, $key, $fields, $custom_fields ) {

		if (
			is_array( $custom_fields ) &&
			isset( $custom_fields[ $key ] ) &&
			$custom_fields[ $key ] &&
			isset( $custom_fields[ $key ][ $from_key ] ) &&
			$custom_fields[ $key ][ $from_key ]
		) {
			$fields['fields'][ $key ][ $to_key ] = $custom_fields[ $key ][ $from_key ];
		}

		return $fields;
	}

	/**
	 * Helper method to get our optin data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form_data Form data array.
	 * @return array Array of opt-in data.
	 */
	public function generate_optin_data( $form_data ) {
		return [
			'list'         => $this->get_nested_value_from_data( '_ctct_list', $form_data ),
			'show'         => $this->get_nested_value_from_data( '_ctct_opt_in', $form_data ),
			'instructions' => $this->get_nested_value_from_data( '_ctct_opt_in_instructions', $form_data ),
		];
	}

	/**
	 * Helper method to get opt in instructions or other text from form data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key       Field key.
	 * @param array  $form_data Form data.
	 * @return string Instructions.
	 */
	public function get_nested_value_from_data( $key, $form_data ) {
		if (
			isset( $form_data[ $key ] ) &&
			$form_data[ $key ] &&
			isset( $form_data[ $key ][0] ) &&
			$form_data[ $key ][0]
		) {
			return $form_data[ $key ][0];
		}

		return '';
	}

	/**
	 * Call the method to enqueue styles for display.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_display_styles() {
		constant_contact()->display->styles( true );
	}
}

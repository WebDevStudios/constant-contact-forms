<?php
/**
 * @package ConstantContact
 * @subpackage DisplayShortcode
 * @author Constant Contact
 * @since 1.0.0
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
	 * @var object
	 */
	protected $plugin = null;


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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_display_styles' ) );
	}

	/**
	 * Acts as a wrapper so our shortcode class doesnt have to do this.
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts Shortcode attributes.
	 * @return mixed
	 */
	public function shortcode_wrapper( $atts ) {

		// Bail if we don't have a form set.
		if ( ! isset( $atts['form'] ) ) {
			return '';
		}

		return $this->get_form( $atts['form'] );
	}

	/**
	 * Get a form from ID.
	 *
	 * @since 1.0.0
	 *
	 * @param int $form_id Form ID.
	 * @return string
	 */
	public function get_form( $form_id ) {

		// sanity check it
		$form_id = absint( $form_id );
		if ( ! $form_id ) {
			return '';
		}

		// Grab all post meta.
		$meta = get_post_meta( $form_id );

		// Bail if we didn't get meta.
		if ( ! $meta ) {
			return '';
		}

		// Pass our data into our field method.
		$form_data = $this->get_field_meta( $meta, $form_id );

		// Return our markup.
		$form = constant_contact()->display->form( $form_data, $form_id );

		return '<div id="ctct-form-' . $form_id . '" class="ctct-form-wrapper">' . $form . '</div><!-- .ctct-form-wrapper -->';

	}

	/**
	 * Display a form to the screen.
	 *
	 * @since 1.0.0
	 *
	 * @param int $form_id Form ID to display.
	 */
	public function display_form( $form_id ) {

		// @codingStandardsIgnoreStart
		echo $this->get_form( absint( $form_id ) );
		// @codingStandardsIgnoreEnd
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

		// Bail if we don't have form meta.
		if ( empty( $form_meta ) || ! is_array( $form_meta ) ) {
			return '';
		}

		// Data verificiation for our custom fields group.
		if (
			isset( $form_meta['custom_fields_group'] ) &&
			$form_meta['custom_fields_group'] &&
			isset( $form_meta['custom_fields_group'][0] )
		) {
			// If we passed all the checks, try to grab the data.
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
	 * @return string Form field markup.
	 */
	public function get_field_values( $custom_fields, $full_data, $form_id ) {

		// Get all our data from our fields, while we unserialize them
		$fields = $this->generate_field_values_for_fields( maybe_unserialize( $custom_fields ) );

		// Now that we've finished checking all of our form fields, we'll
		// want to set some general form information here.
		if ( $form_id ) {
			$fields['options']['form_id'] = $form_id;
		}

		// add in our form description, if we have one
		$fields['options']['description'] = $this->get_nested_value_from_data( '_ctct_description', $full_data );

		// Get our optin data
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

		// Set up our base fields value
		$fields = array();

		// Sanity check
		if ( ! is_array( $custom_fields ) ) {
			return $fields;
		}

		// Loop through each of our fields.
		foreach ( $custom_fields as $key => $value ) {

			// Make sure we have the parts of our array that we expect
			if ( ! isset( $custom_fields ) || ! isset( $custom_fields[ $key ] ) ) {
				continue;
			}

			// Set our field name, if we can.
			$fields = $this->set_field( '_ctct_field_label', 'name', $key, $fields, $custom_fields );

			// Set our field mapping, if we can.
			$fields = $this->set_field( '_ctct_map_select', 'map_to', $key, $fields, $custom_fields );
			$fields = $this->set_field( '_ctct_map_select', 'type', $key, $fields, $custom_fields );

			// Set our field description, if we can.
			$fields = $this->set_field( '_ctct_field_desc', 'description', $key, $fields, $custom_fields );

			// Set our field requirement, if we can. We do this by casting the results of our two checks to a boolean
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

		// Data sanity check / verification.
		if (
			is_array( $custom_fields ) &&
			isset( $custom_fields[ $key ] ) &&
			$custom_fields[ $key ] &&
			isset( $custom_fields[ $key ][ $from_key ] ) &&
			$custom_fields[ $key ][ $from_key ]
		) {
			// Set our data to the correct key
			$fields['fields'][ $key ][ $to_key ] = $custom_fields[ $key ][ $from_key ];
		}

		// Send it all back
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

		// Return our data for our optin
		return array(
			'list' => $this->get_nested_value_from_data( '_ctct_list', $form_data ),
			'show' => $this->get_nested_value_from_data( '_ctct_opt_in', $form_data ),
			'instructions' => $this->get_nested_value_from_data( '_ctct_opt_in_instructions', $form_data ),
		);
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

		// Get our instructions for our opt in.
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

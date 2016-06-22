<?php
/**
 * ConstantContact_Shortcode class
 *
 * @package ConstantContactShortcode
 * @author Pluginize
 * @since 1.0.0
 */

if ( class_exists( 'WDS_Shortcodes', false ) && ! class_exists( 'ConstantContact_Shortcode', false ) ) {

	/**
	 * ConstantContact_Shortcode
	 *
	 * Sets up shortcode
	 */
	class ConstantContact_Shortcode extends WDS_Shortcodes {

		/**
		 * The Shortcode Tag
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $shortcode = 'ctct';

		/**
		 * Default attributes applied tot he shortcode.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $atts_defaults = array();

		/**
		 * Shortcode Output
		 *
		 * @since 1.0.0
		 * @return string shortcode html
		 */
		public function shortcode() {

			// Attributes.
			$atts = shortcode_atts( array( 'form' => '' ), $this->shortcode_object->atts );

			// Bail if we don't have a form set
			if ( ! isset( $atts['form'] ) ) {
				return;
			}

			// Grab all post meta
			$meta = get_post_meta( $atts['form'] . 'a' );

			// Bail if we didn't get meta
			if ( ! $meta ) {
				return;
			}

			// Pass our data into our field method
			$form_data = $this->get_field_meta( $meta );

			// return our markup
			return constant_contact()->display->form( $form_data );

		}

		/**
		 * Proccess cmb2 options into form data array
		 *
		 * @since 1.0.0
		 * @param  array $form_meta post meta.
		 * @return array  form field data
		 */
		public function get_field_meta( $form_meta ) {

			// Bail if we don't have form meta
			if ( empty( $form_meta ) || ! is_array( $form_meta ) ) {
				return false;
			}

			// set our default values
			$custom_fields = array();

			// Data verificiation for our custom fields group
			if (
				isset( $form_meta['custom_fields_group'] ) &&
				$form_meta['custom_fields_group'] &&
				isset( $form_meta['custom_fields_group'][0] )
			) {
				// If we passed all the checks, try to grab the data
				$custom_fields = maybe_unserialize( $form_meta['custom_fields_group'][0]  );
			}

			// Loop through each of our fields
			foreach ( $custom_fields as $key => $value ) {

				$fields['fields'][ $key ]['name']   = $custom_fields[ $key ]['_ctct_field_name'];
				$fields['fields'][ $key ]['map_to'] = $custom_fields[ $key ]['_ctct_map_select'];

				if ( isset( $custom_fields[ $key ]['_ctct_required_field'] ) && 'on' === $custom_fields[ $key ]['_ctct_required_field'] ) {
					$fields['fields'][ $key ]['required'] = $custom_fields[ $key ]['_ctct_required_field'];
				}
			}

			if ( isset( $form_meta['_ctct_description'] ) ) {
				$fields['options']['description'] = $form_meta['_ctct_description'][0];
			}

			if ( isset( $form_meta['_ctct_list'] ) ) {
				$fields['options']['list'] = $form_meta['_ctct_list'][0];
			}

			if (
				isset( $form_meta['_ctct_opt_in'] ) &&
				$form_meta['_ctct_opt_in'] &&
				isset( $form_meta['_ctct_opt_in'][0] ) &&
				$form_meta['_ctct_opt_in'][0] &&
				'on' === $form_meta['_ctct_opt_in'][0]
			) {
				if (
					isset( $form_meta['_ctct_opt_in_instructions'] ) &&
					isset( $form_meta['_ctct_opt_in_instructions'][0] )
				) {
					$fields['options']['opt_in'] = $form_meta['_ctct_opt_in_instructions'][0];
				} else {
					$fields['options']['opt_in'] = '';
				}
			}

			return $fields;
		}

		/**
		 * Override for attribute getter
		 *
		 * You can use this to override specific attribute acquisition
		 * ex. Getting attributes from options, post_meta, etc...
		 *
		 * @see WDS_Shortcode::att
		 *
		 * @since 1.0.0
		 * @param string $att	 Attribute to override.
		 * @param string|null $default Default value.
		 * @return string
		 */
		public function att( $att, $default = null ) {
			$current_value = parent::att( $att, $default );
			return $current_value;
		}
	}

}

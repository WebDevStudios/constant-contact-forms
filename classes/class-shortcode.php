<?php
/**
 * ConstantContact_Shortcode class
 *
 * @package ConstantContactShortcode
 * @author Pluginize
 * @since 1.0.0
 */

if ( ! class_exists( 'ConstantContact_Shortcode', false ) ) {

    /**
     * ConstantContact_Shortcode
     *
     * Sets up shortcode
     */
	class ConstantContact_Shortcode extends WDS_Shortcodes {

		/**
		 * The Shortcode Tag
		 *
		 * @var string
		 */
		public $shortcode = 'ctct';

		/**
		 * Default attributes applied tot he shortcode.
		 *
		 * @var array
		 */
		public $atts_defaults = array();

		/**
		 * Shortcode Output
		 */
		public function shortcode() {

            $attributes = $this->shortcode_object->atts;

            // Attributes
    		$atts = shortcode_atts(
    			array(
    				'form' => '',
    			),
    			$attributes
    		);

    		$meta = get_post_meta( $atts['form'] );
    		$form_data = $this->get_field_meta( $meta );

    		ob_start();
    		$shortcode = require( constant_contact()->dir() . 'templates/form.php' );
    		$shortcode = ob_get_contents();
    		ob_end_clean();
    		return $shortcode;

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

    		$custom_fields = isset( $form_meta['custom_fields_group'] ) ?  maybe_unserialize( $form_meta['custom_fields_group'][0] ) : array();
    		$c_fields = array();

    		foreach ( $custom_fields as $key => $value ) {

    			$c_fields['fields'][ $key ]['name'] = $custom_fields[ $key ]['_ctct_field_name'];
                $c_fields['fields'][ $key ]['map_to'] = $custom_fields[ $key ]['_ctct_map_select'];

    			if ( isset( $custom_fields[ $key ]['_ctct_required_field'] ) && 'on' === $custom_fields[ $key ]['_ctct_required_field'] ) {
    				$c_fields['fields'][ $key ]['required'] = $custom_fields[ $key ]['_ctct_required_field'];
    			}
    		}

    		$fields = $c_fields;

    		if ( isset( $form_meta['_ctct_description'] ) ) {
    			$fields['options']['description'] = $form_meta['_ctct_description'][0];
    		}

    		if ( isset( $form_meta['_ctct_list'] ) ) {
    			$fields['options']['list'] = $form_meta['_ctct_list'][0];
    		}

    		if ( 'on' === $form_meta['_ctct_opt_in'][0] ) {
    			$fields['options']['opt_in'] = $form_meta['_ctct_opt_in_instructions'][0];
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
		 * @param string      $att     Attribute to override.
		 * @param string|null $default Default value.
		 * @return string
		 */
		public function att( $att, $default = null ) {
			$current_value = parent::att( $att, $default );
			return $current_value;
		}
	}

}

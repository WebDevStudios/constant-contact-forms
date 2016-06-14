<?php
/**
 * ConstantContact_Shortcode_Admin
 *
 * @package	ConstantContactShortcodeAdmin
 * @author	 Pluginize
 * @since 1.0.0
 */

if ( ! class_exists( 'ConstantContact_Shortcode_Admin', false ) ) {

	/**
	 * ConstantContact_Shortcode_Admin
	 *
	 * Sets up shortcode button
	 */
	class ConstantContact_Shortcode_Admin extends WDS_Shortcode_Admin {

		/**
		 * Hooks
		 *
		 * @return void
		 */
		public function hooks() {
			add_filter( $this->shortcode . '_shortcode_fields', array( $this, 'filter_shortcode_field' ), 10, 2 );
			parent::hooks();
		}

		/**
		 * Array of button data
		 *
		 * @return array
		 */
		public function js_button_data() {

			return array(
				'qt_button_text' => __( 'ctct', 'cptuiext' ),
				'button_tooltip' => __( 'ctct', 'cptuiext' ),
				'icon'		   => 'dashicons-format-aside',
				'include_close'  => false,
				'modalClass' 	 => 'ctct',
				'modalHeight' 	 => 'auto',
				'modalWidth'	 => 500,
			);

		}

		/**
		 * Adds fields to the button modal using CMB2
		 *
		 * @param array $fields CMB2 fields data.
		 * @param array $button_data Shotcode button data.
		 * @return array $fields
		 */
		public function fields( $fields, $button_data ) {

			$fields[] = array(
				'name'			 => __( 'Form Shortcode', 'cptuiext' ),
				'desc'			 => __( 'Choosing a form shortcode.', 'cptuiext' ),
				'id'			   => '_ctct_form',
				'type'			 => 'select',
				'show_option_none' => false,
				'options'		  => $this->get_forms(),
			);

			return $fields;
		}

		/**
		 * Filters the data sent to the editor.
		 *
		 * @since 1.0.0
		 * @param array $fields CMB2 fields data.
		 * @param array $shortcode_button Shortcode buttond data.
		 * @return array
		 */
		public function filter_shortcode_field( $fields, $shortcode_button ) {
			if ( ! $shortcode_button instanceof Shortcode_Button ) {
				return $fields;
			}

			$filtered_fields = array();
			$filtered_fields['form'] = $fields['_ctct_form'];

			return $filtered_fields;
		}

		/**
		 * Returns array of form ids
		 *
		 * @since 1.0.0
		 * @return array
		 */
		public function get_forms() {

			$args = array(
				'post_status' => 'publish',
				'post_type' => 'ctct_forms',
			);
			$the_query = new WP_Query( $args );

			$forms = array();

			foreach ( $the_query->posts as $key => $value ) {
				$forms[ $value->ID ] = $value->post_title;
			}
			return $forms;

		}
	}

}

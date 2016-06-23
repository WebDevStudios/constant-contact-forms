<?php
/**
 * ConstantContact_Shortcode_Admin
 *
 * @package	ConstantContactShortcodeAdmin
 * @author	 Pluginize
 * @since 1.0.0
 */

if ( class_exists( 'WDS_Shortcodes', false ) && ! class_exists( 'ConstantContact_Shortcode_Admin', false ) ) {

	/**
	 * ConstantContact_Shortcode_Admin
	 *
	 * Sets up shortcode button
	 */
	class ConstantContact_Shortcode_Admin extends WDS_Shortcode_Admin {

		/**
		 * Hooks
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function hooks() {
			add_filter( $this->shortcode . '_shortcode_fields', array( $this, 'filter_shortcode_field' ), 10, 2 );
			parent::hooks();

			add_action( 'save_post', array( $this, 'clear_saved_form_list_transient' ) );
		}

		/**
		 * Array of button data
		 *
		 * @since 1.0.0
		 * @return array
		 */
		public function js_button_data() {

			return apply_filters( 'constant_contact_shortcode_button_settings', array(
				'qt_button_text' => __( 'Add Constant Contact Form', 'constantcontact' ),
				'button_tooltip' => __( 'Add Constant Contact Form', 'constantcontact' ),
				'icon'           => 'dashicons-feedback',
				'include_close'  => false,
				'modalClass'     => 'ctct',
				'modalHeight'    => 'auto',
				'modalWidth'     => 500,
			) );
		}

		/**
		 * Adds fields to the button modal using CMB2
		 *
		 * @since 1.0.0
		 * @param array $fields CMB2 fields data.
		 * @param array $button_data Shotcode button data.
		 * @return array $fields
		 */
		public function fields( $fields, $button_data ) {

			$fields[] = array(
				'name'             => __( 'Form Shortcode', 'constantcontact' ),
				'desc'             => __( 'Choosing a form shortcode.', 'constantcontact' ),
				'id'               => '_ctct_form',
				'type'             => 'select',
				'show_option_none' => false,
				'options'          => $this->get_forms(),
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

			if ( isset( $fields['_ctct_form'] ) ) {
				$filtered_fields['form'] = $fields['_ctct_form'];
			}

			return $filtered_fields;
		}

		/**
		 * Returns array of form ids
		 *
		 * @since 1.0.0
		 * @return array
		 */
		public function get_forms() {

			// Grab our saved transient.
			$forms = get_transient( 'constant_contact_shortcode_form_list' );

			// Allow bypassing transient check.
			$bypass_forms = apply_filters( 'constant_contact_bypass_shotcode_forms', false );

			// If we dont have a transient or we bypass, go through the motions.
			if ( false === $forms || $bypass_forms ) {

				// Get all our forms that we have.
				$query = new WP_Query( array(
					'post_status'            => 'publish',
					'post_type'              => 'ctct_forms',
					'no_found_rows'          => true,
					'update_post_term_cache' => false,
				) );

				// Grab the posts.
				$q_forms = $query->get_posts();

				// If for some reason we got an error, just return a blank array.
				if ( is_wp_error( $q_forms ) && ! is_array( $q_forms ) ) {
					return array();
				}

				// Set up our default array.
				$forms = array();

				// Foreach form we have, lets build up our return array.
				foreach ( $q_forms as $form ) {

					// Make sure we have the data we want to use.
					if (
						isset( $form->ID ) &&
						$form->ID &&
						isset( $form->post_title ) &&
						$form->post_title
					) {
						// Clean that data before we use it.
						$forms[ absint( $form->ID ) ] = esc_attr( $form->post_title );
					}
				}

				// Save that.
				set_transient( 'constant_contact_shortcode_form_list', $forms, 1 * DAY_IN_SECONDS );
			}

			return $forms;
		}

		/**
		 * Delete transient of saved form
		 *
		 * @return void
		 */
		public function clear_saved_form_list_transient() {
			delete_transient( 'constant_contact_shortcode_form_list' );
		}
	}
}

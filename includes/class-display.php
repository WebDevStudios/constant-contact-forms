<?php

/**
 * ConstantContact_Display class
 *
 * @package ConstantContact_Display
 * @subpackage ConstantContact
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Class ConstantContact_Display
 */
class ConstantContact_Display {

	/**
	 * Parent plugin class
	 *
	 * @var   class
	 * @since 0.0.1
	 */
	protected $plugin = null;

	/**
	 * Constructor
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Scripts
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function scripts() {

		// Check if we are in debug mode. allow
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true ? true : false;

		// based on our debug mode, potentially add a min prefix
		$suffix = ( true === $debug ) ? '' : '.min';

		// Register our javascript file.
		wp_register_script(
			'ctct_frontend_forms',
			constant_contact()->url() . 'assets/js/ctct-plugin-frontend' . $suffix . '.js',
			array(),
			constant_contact()->version,
			true
		);
	}

	/**
	 * Main wrapper for getting our form display
	 *
	 * @since  1.0.0
	 * @return string Form markup
	 */
	public function form( $form_data, $form_id = '', $skip_styles = false ) {

		// Also enqueue our scripts
		$this->scripts();

		// Conditionally enqueue our styles
		if ( ! $skip_styles ) {

			wp_enqueue_style(
				'ctct_form_styles',
				constant_contact()->url() . 'assets/css/style.css',
				array(),
				constant_contact()->version
			);

			// Enqueued script.
			wp_enqueue_script( 'ctct_frontend_forms' );
		}

		// Start our return markup and some default variables
		$return           = '';
		$form_err_display = '';
		$error_message    = false;
		$status           = false;

		// Get a potential response from our processing wrapper
		// This returns an array that looks like this:
		// array(
		// 'status'  => $status,
		// 'message' => $message,
		// )
		// if the status is success, then we sent the form correctly
		// if the status is error, then we will re-show the form, but also
		// with our error messages.
		$response = constant_contact()->process_form->process_wrapper( $form_data, $form_id );

		// submitted values
		$old_values = isset( $response['values'] ) ? $response['values'] : '';
		$req_errors = isset( $response['errors'] ) ? $response['errors'] : '';

		// Check to see if we got a response, and if it has the fields we expect
		if ( $response && isset( $response['message'] ) && isset( $response['status'] ) ) {

			// If we were succesful, then display success message
			if ( 'success' == $response['status'] ) {

				// If we were successful, we'll return here so we don't display the entire form again
				return $this->message( 'success', $response['message'] );

			} else {

				// If we didn't get a success message, then we want to error.
				// We already checked for a messsage response, but we'll force the
				// status to error if we're not here
				$status = 'error';
				$error_message = trim( $response['message'] );
			}
		}

		// If we got an error for our status, and we have an error message, display it.
		if ( 'error' == $status || $error_message ) {

			if ( ! empty( $error_message ) ) {
				// We'll show this error right inside our form
				$form_err_display = $this->message( 'error', $error_message );
			}
		}

		// Force uniqueness of an id for the form
		// @todo figure out another way to do this maybe?
		$rf_id = 'ctct-form-' . mt_rand();

		// Build out our form
		$return .= '<form class="ctct-form" id=' . $rf_id . ' action="' . esc_url( $this->get_current_page() ) . '" method="post">';

		// If we have errors, display them
		$return .= $form_err_display;

		// Output our normal form fields
		$return .= $this->build_form_fields( $form_data, $old_values, $req_errors );

		// Add our hidden verification fields
		$return .= $this->add_verify_fields( $form_data );

		// Add our submit field
		$return .= $this->submit();

		// Nonce the field too
		$return .= wp_nonce_field( 'ctct_submit_form', 'ctct_form', true, false );

		// Close our form
		$return .= '</form>';

		$return .= '<script type="text/javascript">';
		$return .= 'var ajaxurl = "' . esc_url( admin_url( 'admin-ajax.php' ) ) . '";';
		$return .= '</script>';

		// Return it all
		return $return;
	}

	/**
	 * Get our current URL in a somewhat robust way
	 *
	 * @since  1.0.0
	 * @return string url of current page
	 */
	public function get_current_page() {
		global $wp;
		return trailingslashit( add_query_arg( '', '', home_url( $wp->request ) ) );
	}

	/**
	 * Adds hidden input fields to our form for form id and verify id
	 *
	 * @since  1.0.0
	 * @param  string $form_data html markup
	 */
	public function add_verify_fields( $form_data ) {

		// Sanity check
		if (
			isset( $form_data ) &&
			isset( $form_data['options'] ) &&
			isset( $form_data['options']['form_id'] )
		) {

			// sanitize our form id
			$form_id = absint( $form_data['options']['form_id'] );

			// sanity check on our form id
			if ( ! $form_id ) {
				return false;
			}

			// Add hidden field with our form id in it
			$return = $this->input( 'hidden', 'ctct-id', 'ctct-id', $form_id, '', '', true );

			// if we have saved a verify value, add that to our field as well. this is to double-check
			// that we have the correct form id for processing later
			$verify_key = get_post_meta( $form_id, '_ctct_verify_key', true );

			if ( $verify_key ) {
				$return .= $this->input( 'hidden', 'ctct-verify', 'ctct-verify', $verify_key, '', '', true );
			}

			return $return;
		}
	}

	/**
	 * Build form fields for shortcode
	 *
	 * @since 1.0.0
	 * @param  array $form_data formulated cmb2 data for form.
	 * @return void
	 */
	public function build_form_fields( $form_data, $old_values, $req_errors ) {

		// start our wrapper return var
		$return = '';

		// Check to see if we have a description for the form, and display it.
		if (
			isset( $form_data['options'] ) &&
			isset( $form_data['options']['description'] ) &&
			$form_data['options']['description'] &&
			isset( $form_data['options']['form_id'] )
		) {
			$form_id = absint( $form_data['options']['form_id'] );
			$return .= $this->description( $form_data['options']['description'], $form_id );
		}

		// Loop through each of our form fields and output it.
		foreach ( $form_data['fields'] as $key => $value ) {
			$return .= $this->field( $value, $old_values, $req_errors );
		}

		// Check to see if we have an opt-in for the form, and display it.
		if ( isset( $form_data['options'] ) ) {
			$return .= $this->opt_in( $form_data['options'] );
		}

		return $return;
	}

	/**
	 * Wrapper for single field display
	 *
	 * @since  1.0.0
	 * @param  array $field field data
	 * @return string        html markup
	 */
	public function field( $field, $old_values = array(), $req_errors = array() ) {

		// If we don't have a name or a mapping, it will be hard to do things.
		if ( ! isset( $field['name'] ) || ! isset( $field['map_to'] ) ) {
			return;
		}

		// Check all our data points.
		$name   = sanitize_text_field( $field['name'] );
		$map    = sanitize_text_field( $field['map_to'] );
		$desc   = sanitize_text_field( isset( $field['description'] ) ? $field['description'] : '' );
		$type   = sanitize_text_field( isset( $field['type'] ) ? $field['type'] : 'text_field' );
		$value  = sanitize_text_field( isset( $field['value'] ) ? $field['value'] : false );
		$req    = isset( $field['required'] ) ? $field['required'] : false;

		// We may have more than one of the same field in our array.
		// this makes sure we keep them unique when processing them.
		if ( 'submit' != $type ) {
			$map = $map . '___' . md5( serialize( $field ) );
		}

		// Default error status
		$field_error = false;

		// If we got any errors, then pass them through to the form field
		if ( ! empty( $req_errors ) ) {

			// Loop through each error
			foreach ( $req_errors as $error ) {

				// Make sure we have a field ID and an actual error
				if ( isset( $error['id'] ) && isset( $error['error'] ) ) {

					// If the error matches the field we're rendering
					if ( $map == $error['id'] ) {

						// Start our field error return
						$field_error = '<span class="ctct-field-error">';

						// Based on the error type, display an error.
						if ( 'invalid' == $error['error'] ) {
							 $field_error .= __( 'Error: Please correct your entry.', 'constantcontact' );
						} else {
							$field_error .= __( ' Error: Please fill out this field.', 'constantcontact' );
						}

						// Finish error return
						$field_error .= '</span>';
					}
				}
			}
		}

		// Potentially replace value with submitted value
		$value = $this->get_submitted_value( $value, $map, $field, $old_values );

		// Based on our type, output different things
		switch ( $type ) {
			case 'custom':
			case 'first_name':
			case 'last_name':
			case 'phone_number':
			case 'job_title':
			case 'company':
			case 'website':
			case 'text_field':
				return $this->input( 'text', $name, $map, $value, $desc, $req, false, $field_error );
				break;
			case 'email':
				return $this->input( 'email', $name, $map, $value, $desc, $req, false, $field_error );
				break;
			case 'hidden':
				return $this->input( 'hidden', $name, $map, $value, $desc, $req );
				break;
			case 'checkbox':
				return $this->checkbox( $name, $map, $value, $desc );
				break;
			case 'submit':
				return $this->input( 'submit', $name, $map, $value, $desc, $req, false, $field_error );
				break;
			case 'address':
				return $this->address( $name, $map, $value, $desc, $req, $field_error );
				break;
			case 'anniversery':
			case 'birthday':
				// need this to be month / day / year
				return $this->dates( 'text', $name, $map, $value, $desc, $req, false, $field_error );
				break;
			default:
				return $this->input( 'text', $name, $map, $value, $desc, $req, false, $field_error );
				break;
		}
	}

	/**
	 * Gets submitted values
	 *
	 * @since  1.0.0
	 * @param  array $field field data
	 * @return string        submitted value
	 */
	public function get_submitted_value( $value = '', $map = '', $field = array(), $submitted_vals = array() ) {

		// If we have a value already return it
		if ( $value ) {
			return $value;
		}

		// Sanity check
		if ( ! is_array( $submitted_vals ) ) {
			return '';
		}

		// Loop through each val and try to grab our submitted
		foreach ( $submitted_vals as $post ) {

			// Sanity check that
			if (
				isset( $post['key'] ) &&
				$post['key'] &&
				$post['key'] == $map &&
				isset( $_POST[ esc_attr( $map ) ] )
			) {
				// Clean and return
				return sanitize_text_field( $_POST[ esc_attr( $map ) ] );
			}
		}

		return '';
	}

	/**
	 * Helper method to display in-line for success/error messages
	 *
	 * @since  1.0.0
	 * @param  string $type    success / error / etc for class
	 * @param  string $message message to display to user
	 * @return string          html markup
	 */
	public function message( $type, $message ) {
		return '<p class="ctct-message ' . esc_attr( $type ) . '">' . esc_attr( $message ) . '</p>';
	}

	/**
	 * Helper method to display form description
	 *
	 * @since  1.0.0
	 * @param  string $description description to outpu
	 * @return echo              echos out form description markup
	 */
	public function description( $desc = '', $form_id = false ) {

		// Set default var
		$display = '';

		// if we have the permissions, also display an edit link
		if ( current_user_can( 'edit_posts' ) && $form_id ) {

			// get our edit link
			$edit_link = get_edit_post_link( absint( $form_id ) );

			// if we got a link, display it
			if ( $edit_link ) {
				$display .= '<a class="button ctct-button" href="' . esc_url( $edit_link ) . '">' . __( 'Edit Form', 'constantcontact' ) . '</a>';
			}
		}

		// Display our description
		return '<span class="ctct-form-description">' . wpautop( wp_kses_post( $desc ) ) . '</span>' . $display;

	}

	/**
	 * Helper method to display label for form field + field starting markup
	 *
	 * @since  1.0.0
	 * @param  string  $type  type of field
	 * @param  string  $name  name / id of field
	 * @param  string  $label label text for field
	 * @param  boolean $req   is this field required?
	 * @return string         HTML markup
	 */
	public function field_top( $type = '', $name = '', $f_id = '', $label = '', $req = false, $use_label = true ) {

		// Set blank defaults for required info
		$req_label = '';

		// If this is required, we output the HMTL5 required att
		if ( $req ) {
			$req_label = apply_filters( 'constant_contact_required_label', '<abbr title="required">*</abbr>' );
		}

		// Start building our return markup
		$markup = '<p class="ctct-form-field ctct-form-field-' . $type . '">';

		// alow skipping label, also don't show for submit buttons
		if ( $use_label && ( 'submit' != $type ) && ( 'hidden' != $type ) ) {

			// Our field label will be the form name + required asterisk + our label
			$markup .= $this->get_label( $f_id, $name . ' ' . $req_label . $label );
		}

		// return it
		return $markup;
	}

	/**
	 * Bottom of field markup
	 *
	 * @since  1.0.0
	 * @return string HTML markup
	 */
	public function field_bottom( $name = '', $field_label = '' ) {

		$markup = '';
		if ( ! empty( $name ) && ! empty( $field_label ) ) {
			$markup .= $this->get_label( $name, $field_label );
		}
		// Finish building our markup
		return $markup . '</p>';
	}

	/**
	 * Helper method to get form label
	 *
	 * @since  1.0.0
	 * @param  string $name name/id of form field
	 * @param  string $text text to display as label
	 * @return string       HTML markup
	 */
	public function get_label( $f_id, $field_label ) {
		return '<label for="' . $f_id . '">' . $field_label . '</label>';
	}

	/**
	 * Wrapper for 'input' form fields
	 *
	 * @since  1.0.0
	 * @param  string  $type   type of form field
	 * @param  string  $name   ID of form field
	 * @param  string  $value  pre-filled value
	 * @param  string  $label  label text for inpug
	 * @param  boolean $req    is this field required?
	 * @param  boolean $f_only should we only return the field itself, with no label?
	 * @return string          HTML markup for field
	 */
	public function input( $type = 'text', $name = '', $id = '', $value = '', $label = '', $req = false, $f_only = false, $field_error = false ) {

		// Sanitize our stuff / set values
		$name  = sanitize_text_field( $name );
		$f_id  = sanitize_title( $id );
		$type  = sanitize_text_field( $type );
		$value = sanitize_text_field( $value );
		$label = sanitize_text_field( $label );
		$req_text = $req ? 'required' : '';

		// Start our markup
		$markup = $this->field_top( $type, $name, $f_id, $label, $req );

		// Set our field as as seprate var, because we allow for only returning that
		$field = '<input ' . $req_text . ' type="' . $type . '" name="' . $f_id . '" id="' . $f_id . '" value="' . $value . '"/>';

		// If we have an error
		if ( $field_error ) {
			// Tack that sucker on to the end of our input
			$field = str_replace( '/>', 'class="ctct-invalid />', $field );
		}

		// Add our field to our markup
		$markup .= $field;

		// If we got an error, add it to the bottom label
		if ( $field_error ) {
			$markup .= $this->field_bottom( $id, $field_error );
		} else {
			$markup .= $this->field_bottom();
		}

		// If we passed in a flag for only the field, just return that
		if ( $f_only ) {
			return $field;
		}

		// Otherwise all the markup
		return $markup;
	}

	/**
	 * Checkbox field helper method
	 *
	 * @since  1.0.0
	 * @param  string $name  name/it of field
	 * @param  string $value value of field
	 * @param  string $label label / desc text
	 * @return string        html markup for checkbox
	 */
	public function checkbox( $name = '', $f_id = '', $value = '', $label = '' ) {

		// Clean our inputs
		$name  = sanitize_text_field( $name );
		$f_id  = sanitize_title( $f_id );
		$value = sanitize_text_field( $value );
		$label = esc_attr( $label );
		$type = 'checkbox';

		// Build up our markup
		$markup = $this->field_top( $type, $name, $f_id, $label, false, false );
		$markup .= '<input type="' . $type . '" name="' . $f_id . '" id="' . $f_id . '" value="' . $value . '" />';
		$markup .= $this->field_bottom( $name, ' ' . $label );

		// return it
		return $markup;
	}

	/**
	 * Helper method for submit button
	 *
	 * @since  1.0.0
	 * @return string html markup
	 */
	public function submit() {
		return $this->field( array(
			'type'   => 'submit',
			'name'   => 'ctct-submitted',
			'map_to' => 'ctct-submitted',
			'value'  => __( 'Send', 'constantcontact' ),
		) );
	}

	/**
	 * Build markup for opt_in form
	 *
	 * @since  1.0.0
	 * @param  array $form_data form data structure
	 * @return string            markup of optin form
	 */
	public function opt_in( $form_data ) {

		// Make sure we have our opt in set, as well as an associated list
		if ( isset( $form_data['opt_in'] ) && isset( $form_data['list'] ) ) {

			// build that checkbox
			return $this->checkbox(
				'ctct-opti-in',
				'ctct-opti-in',
				$form_data['list'],
				( isset( $form_data['opt_in_instructions'] ) ? $form_data['opt_in_instructions'] : '' )
			);
		}
	}

	/**
	 * Builds a fancy address field group
	 *
	 * @since  1.0.0
	 * @param  string $name  name of fields
	 * @param  string $f_id  form id name
	 * @param  array  $value values of each field
	 * @param  string $desc  label of field
	 * @return string        html markup of field
	 */
	public function address( $name = '', $f_id = '', $value = array(), $desc = '', $req = false, $field_error = '' ) {

		// Set up our text strings
		$street = __( 'Street Address', 'constantcontact' );
		$line_2 = __( 'Address Line 2', 'constantcontact' );
		$city   = __( 'City', 'constantcontact' );
		$state  = __( 'State', 'constantcontact' );
		$zip    = __( 'ZIP Code', 'constantcontact' );

		// @TODO these need to get set correctly
		// Set our values
		$v_street = isset( $value['street'] ) ? $value['street'] : '';
		$v_line_2 = isset( $value['line_2'] ) ? $value['line_2'] : '';
		$v_city   = isset( $value['city'] ) ? $value['city'] : '';
		$v_state  = isset( $value['state'] ) ? $value['state'] : '';
		$v_zip    = isset( $value['zip'] ) ? $value['zip'] : '';

		// Build our field
		$return  = '<p><fieldset>';
		$return .= ' <legend>' . esc_attr( $name ) . '</legend>';
		$return .= ' <div class="ctct-form-field ctct-field-full address-line-1">';
		$return .= '  <label for="street_' . esc_attr( $f_id ) . '">' . esc_attr( $street ) . '</label>';
		$return .= '  <input type="text" name="street_' . esc_attr( $f_id ) . '" id="street_' . esc_attr( $f_id ) . '" value="' . esc_attr( $v_street ) . '">';
		$return .= ' </div>';
		$return .= ' <div class="ctct-form-field ctct-field-full address-line-2" id="input_2_1_2_container">';
		$return .= '  <label for="line_2_' . esc_attr( $f_id ) . '">' . esc_attr( $line_2 ) . '</label>';
		$return .= '  <input type="text" name="line_2_' . esc_attr( $f_id ) . '" id="line_2_' . esc_attr( $f_id ) . '" value="' . esc_attr( $v_line_2 ) . '">';
		$return .= ' </div>';
		$return .= ' <div class="ctct-form-field ctct-field-third address-city" id="input_2_1_3_container">';
		$return .= '  <label for="city_' . esc_attr( $f_id ) . '">' . esc_attr( $city ) . '</label>';
		$return .= '  <input type="text" name="city_' . esc_attr( $f_id ) . '" id="city_' . esc_attr( $f_id ) . '" value="' . esc_attr( $v_city ) . '">';
		$return .= ' </div>';
		$return .= ' <div class="ctct-form-field ctct-field-third address-state" id="input_2_1_4_container">';
		$return .= '  <label for="state_' . esc_attr( $f_id ) . '">' . esc_attr( $state ) . '</label>';
		$return .= '  <input type="text" name="state_' . esc_attr( $f_id ) . '" id="state_' . esc_attr( $f_id ) . '" value="' . esc_attr( $v_state ) . '">';
		$return .= ' </div>';
		$return .= ' <div class="ctct-form-field ctct-field-third address-zip" id="input_2_1_5_container">';
		$return .= '  <label for="zip_' . esc_attr( $f_id ) . '">' . esc_attr( $zip ) . '</label>';
		$return .= '  <input type="text" name="zip_' . esc_attr( $f_id ) . '" id="zip_' . esc_attr( $f_id ) . '" value="' . esc_attr( $v_zip ) . '">';
		$return .= ' </div>';
		$return .= '</fieldset></p>';

		return $return;
	}

	public function dates( $name = '', $f_id = '', $value = array(), $desc = '', $req = false, $field_error = '' ) {

		// Set our field lables
		$month = __( 'Month', 'constantcontact' );
		$day   = __( 'Day', 'constantcontact' );
		$year  = __( 'Year', 'constantcontact' );

		// @TODO these need to get set correctly
		// Set our values
		$v_month = isset( $value['month'] ) ? $value['month'] : '';
		$v_day   = isset( $value['day'] ) ? $value['day'] : '';
		$v_year  = isset( $value['year'] ) ? $value['year'] : '';

		// Build our field
		$return  = '<p><fieldset>';
		$return .= ' <legend>' . esc_attr( $name ) . '</legend>';
		$return .= ' <div class="ctct-form-field ctct-field-third month">';
		$return .= $this->get_date_dropdown( $month, 'month', $v_month );
		$return .= ' </div>';
		$return .= ' <div class="ctct-form-field ctct-field-third day">';
		$return .= $this->get_date_dropdown( $day, 'day', $v_day );
		$return .= ' </div>';
		$return .= ' <div class="ctct-form-field ctct-field-third year">';
		$return .= $this->get_date_dropdown( $year, 'year', $v_year );
		$return .= ' </div>';

		$return .= '</fieldset></p>';

		return $return;
	}

	public function get_date_dropdown( $text = '', $type = '', $selected_value ) {
		// Start our return
		$return = '<select>';
		$return .= $this->get_date_options( $text, $this->get_date_values( $type ), $selected_value );
		$return .= '</select>';

		return $return;
	}

	public function get_date_options( $text, $values, $prev_selected_values ) {

		$return = '<option>' . $text . '</option>';

		foreach ( $values as $value ) {
			$return .= '<option>' . sanitize_text_field( $value ) . '</option>';
		}

		return $return;
	}

	public function get_date_values( $type ) {

		switch ( $type ) {
			case 'day':
				$return = apply_filters( 'constant_contact_dates_day', array(
					'Sunday'
				) );
				break;
			case 'month':
				$return = apply_filters( 'constant_contact_dates_month', array(
					'Sunday'
				) );
				break;
			case 'year':
				$return = apply_filters( 'constant_contact_dates_year', array(
					'Sunday'
				) );
				break;
		}

		return $return;
	}
}


<?php

/**
 * ConstantContact_Display class
 *
 * @package ConstantContactProcessForm
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
	 * Main wrapper for getting our form display
	 *
	 * @return string Form markup
	 */
	public function form( $form_data, $form_id = '', $skip_styles = false ) {

		// Conditionally enqueue our styles
		if ( ! $skip_styles ) {
			wp_enqueue_style(
				'ctct_form_styles',
				constant_contact()->url() . 'assets/css/style.css',
				array(),
				constant_contact()->version
			);
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
		// @TODO also do server-side verification and pass back field merrors
		$response = constant_contact()->process_form->process_wrapper( $form_data, $form_id );

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
				$error_message = $response['message'];
			}
		}

		// If we got an error for our status, and we have an error message, display it.
		if ( 'error' == $status || $error_message ) {

			// We'll show this error right inside our form
			$form_err_display = $this->message( 'error', $error_message );
		}

		// Force uniqueness of an id for the form
		// @todo figure out another way to do this maybe?
		$rf_id = 'ctct-form-' . mt_rand();

		// Build out our form
		$return .= '<form class="ctct-form" id=' . $rf_id . ' action="' . esc_url( $this->get_current_page() ) . '" method="post">';

		// If we have errors, display them
		$return .= $form_err_display;

		// Output our normal form fields
		$return .= $this->build_form_fields( $form_data );

		// Add our hidden verification fields
		$return .= $this->add_verify_fields( $form_data );

		// Add our submit field
		$return .= $this->submit();

		// Nonce the field too
		$return .= wp_nonce_field( 'ctct_submit_form', 'ctct_form', true, false );

		// Close our form
		$return .= '</form>';

		// Return it all
		return $return;
	}

	/**
	 * Get our current URL in a somewhat robust way
	 *
	 * @return string url of current page
	 */
	public function get_current_page() {
		global $wp;
		return trailingslashit( add_query_arg( '', '', home_url( $wp->request ) ) );
	}

	/**
	 * Adds hidden input fields to our form for form id and verify id
	 *
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
	public function build_form_fields( $form_data ) {

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
			$return .= $this->field( $value );
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
	 * @param  array $field field data
	 * @return string        html markup
	 */
	public function field( $field ) {

		// If we don't have a name or a mapping, it will be hard to do things.
		if ( ! isset( $field['name'] ) || ! isset( $field['map_to'] ) ) {
			return;
		}

		// Check all our data points.
		$name   = esc_attr( $field['name'] );
		$map    = esc_attr( $field['map_to'] );
		$desc   = esc_attr( isset( $field['description'] ) ? $field['description'] : '' );
		$type   = esc_attr( isset( $field['type'] ) ? $field['type'] : 'text_field' );
		$value  = esc_attr( isset( $field['value'] ) ? $field['value'] : false );
		$req    = isset( $field['required'] ) ? $field['required'] : false;

		// We may have more than one of the same field in our array.
		// this makes sure we keep them unique when processing them.
		if ( 'submit' != $type ) {
			$map = $map . '___' . md5( serialize( $field ) );
		}

		// Potentially replace value with submitted value
		$value = $this->get_submitted_value( $value, $map, $field );

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
				return $this->input( 'text', $name, $map, $value, $desc, $req );
				break;
			case 'email':
				return $this->input( 'email', $name, $map, $value, $desc, $req );
				break;
			case 'hidden':
				return $this->input( 'hidden', $name, $map, $value, $desc, $req );
				break;
			case 'checkbox':
				return $this->checkbox( $name, $map, $value, $desc );
				break;
			case 'submit':
				return $this->input( 'submit', $name, $map, $value, $desc );
				break;
			case 'address':
				return $this->address( $name, $map, $value, $desc );
				break;
			case 'anniversery':
			case 'birthday':
				// need this to be month / day / year
				return $this->input( 'text', $name, $map, $value, $desc );
				break;
			default:
				return $this->input( 'text', $name, $map, $value, $desc, $req );
				break;
		}
	}

	/**
	 * Gets submitted values
	 *
	 * @param  array $field field data
	 * @return string        submitted value
	 */
	public function get_submitted_value( $value = '', $map = '', $field = array() ) {

		// If we have a value already return it
		if ( $value ) {
			return $value;
		}
		// @TODO fix this
		return ( isset( $_POST[ 'ctct-' . $map ] ) ? esc_attr( $_POST[ 'ctct-' . $map ] ) : '' );
	}

	/**
	 * Helper method to display in-line for success/error messages
	 *
	 * @param  string $type    success / error / etc for class
	 * @param  string $message message to display to user
	 * @return string          html markup
	 */
	public function message( $type, $message ) {
		return '<p class="message ' . esc_attr( $type ) . '">' . esc_attr( $message ) . '</p>';
	}

	/**
	 * Helper method to display form description
	 *
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
				$display .= '<a href="' . esc_url( $edit_link ) . '">' . __( 'Edit Form', 'constantcontact' ) . '</a>';
			}
		}

		// Display our description
		return '<span class="constant-contact-form-description">' . wpautop( wp_kses_post( $desc ) ) . '</span>' . $display;

	}

	/**
	 * Helper method to display label for form field + field starting markup
	 *
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
			$req_label = apply_filters( 'constant_contact_required_label', '<abbr title="required">*</abbr>&nbsp;' );
		}

		// Start building our return markup
		$markup = '<p class="constant-contact-form-field constant-contact-form-field-' . $type . '">';

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
	 * @param  string  $type   type of form field
	 * @param  string  $name   ID of form field
	 * @param  string  $value  pre-filled value
	 * @param  string  $label  label text for inpug
	 * @param  boolean $req    is this field required?
	 * @param  boolean $f_only should we only return the field itself, with no label?
	 * @return string          HTML markup for field
	 */
	public function input( $type = 'text', $name = '', $id = '', $value = '', $label = '', $req = false, $f_only = false ) {

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
		$field = '<input ' . $req_text . ' type="' . $type . '" name="' . $f_id . '" id="' . $f_id . '" value="' . $value . '" />';

		// Add our field to our markup
		$markup .= $field;
		$markup .= $this->field_bottom();

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
	 * @param  string $name  name of fields
	 * @param  string $f_id  form id name
	 * @param  array  $value values of each field
	 * @param  string $desc  label of field
	 * @return string        html markup of field
	 */
	public function address( $name = '', $f_id = '', $value = array(), $desc = '' ) {

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
		$return .= ' <legend>' . esc_attr( $desc ) . '</legend>';
		$return .= ' <span class="ctct-address-field-full address-line-1">';
		$return .= '  <label for="street_' . esc_attr( $f_id ) . '">' . esc_attr( $street ) . '</label>';
		$return .= '  <input type="text" name="street_' . esc_attr( $f_id ) . '" id="street_' . esc_attr( $f_id ) . '" value="' . esc_attr( $v_street ) . '">';
		$return .= ' </span>';
		$return .= ' <span class="ctct-address-field-full address-line-2" id="input_2_1_2_container">';
		$return .= '  <label for="line_2_' . esc_attr( $f_id ) . '">' . esc_attr( $line_2 ) . '</label>';
		$return .= '  <input type="text" name="line_2_' . esc_attr( $f_id ) . '" id="line_2_' . esc_attr( $f_id ) . '" value="' . esc_attr( $v_line_2 ) . '">';
		$return .= ' </span>';
		$return .= ' <span class="ctct-address-field-left address-city" id="input_2_1_3_container">';
		$return .= '  <label for="city_' . esc_attr( $f_id ) . '">' . esc_attr( $city ) . '</label>';
		$return .= '  <input type="text" name="city_' . esc_attr( $f_id ) . '" id="city_' . esc_attr( $f_id ) . '" value="' . esc_attr( $v_city ) . '">';
		$return .= ' </span>';
		$return .= ' <span class="ctct-address-field-right address-state" id="input_2_1_4_container">';
		$return .= '  <label for="state_' . esc_attr( $f_id ) . '">' . esc_attr( $state ) . '</label>';
		$return .= '  <input type="text" name="state_' . esc_attr( $f_id ) . '" id="state_' . esc_attr( $f_id ) . '" value="' . esc_attr( $v_state ) . '">';
		$return .= ' </span>';
		$return .= ' <span class="ctct-address-field-left address-zip" id="input_2_1_5_container">';
		$return .= '  <label for="zip_' . esc_attr( $f_id ) . '">' . esc_attr( $zip ) . '</label>';
		$return .= '  <input type="text" name="zip_' . esc_attr( $f_id ) . '" id="zip_' . esc_attr( $f_id ) . '" value="' . esc_attr( $v_zip ) . '">';
		$return .= ' </span>';
		$return .= '</fieldset></p>';

		return $return;
	}
}


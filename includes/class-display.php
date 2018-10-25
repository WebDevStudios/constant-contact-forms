<?php
/**
 * Display.
 *
 * @package ConstantContact
 * @subpackage Display
 * @author Constant Contact
 * @since 1.0.0
 */

/**
 * Powers displaying our forms to the front end, generating field markup, and output.
 */
class ConstantContact_Display {

	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	protected $plugin = null;

	/**
	 * The global custom styles.
	 *
	 * @since 1.4.0
	 * @var array
	 */
	protected $global_form_styles = array();

	/**
	 * Styles set for a particular form.
	 *
	 * @since 1.4.0
	 * @var array
	 */
	protected $specific_form_styles = array();

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $plugin Parent plugin.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'styles' ) );
	}

	/**
	 * Scripts.
	 *
	 * @since 1.0.0
	 * @since 1.4.0 Deprecated parameter.
	 *
	 * @param bool $enqueue Set true to enqueue the scripts after registering.
	 */
	public function scripts( $enqueue = false ) {

		$debug  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true ? true : false;
		$suffix = ( true === $debug ) ? '' : '.min';

		wp_register_script(
			'ctct_frontend_forms',
			constant_contact()->url() . 'assets/js/ctct-plugin-frontend' . $suffix . '.js',
			array(),
			Constant_Contact::VERSION,
			true
		);

		wp_enqueue_script( 'ctct_frontend_forms' );
	}

	/**
	 * Enqueue styles.
	 *
	 * @since 1.0.0
	 * @since 1.4.0 Deprecated parameter.
	 *
	 * @param bool $enqueue Set true to enqueue the scripts after registering.
	 */
	public function styles( $enqueue = false ) {
		wp_enqueue_style( 'ctct_form_styles' );
	}

	/**
	 * Retrieve the styles set globally for forms.
	 *
	 * @since  1.4.0
	 * @author Scott Tirrell
	 */
	public function set_global_form_css() {

		$defaults = array(
			'global_form_classes'    => '',
			'global_label_placement' => '',
		);

		$global_form_css = array();

		$global_form_classes = ctct_get_settings_option( '_ctct_form_custom_classes' );
		if ( $global_form_classes ) {
			$global_form_css['global_form_classes'] = $global_form_classes;
		}

		$global_label_placement = ctct_get_settings_option( 'ctct_form_label_placement' );
		if ( $global_label_placement ) {
			$global_form_css['global_label_placement'] = $global_label_placement;
		}

		$this->global_form_styles = wp_parse_args( $global_form_css, $defaults );

	}

	/**
	 * Retrieve the styles set for a specific form.
	 *
	 * @param int $form_id The id of the form.
	 *
	 * @since  1.4.0
	 * @author Scott Tirrell
	 */
	public function set_specific_form_css( $form_id ) {
		$defaults = array(
			'form_background_color'               => '',
			'form_description_font_size'          => '',
			'form_description_color'              => '',
			'form_submit_button_font_size'        => '',
			'form_submit_button_text_color'       => '',
			'form_submit_button_background_color' => '',
			'form_padding_top'                    => '',
			'form_padding_right'                  => '',
			'form_padding_bottom'                 => '',
			'form_padding_left'                   => '',
			'input_custom_classes'                => '',
		);

		$specific_form_css = array();

		$ctct_form_background_color = get_post_meta( $form_id, '_ctct_form_background_color', true );
		if ( ! empty( $ctct_form_background_color ) ) {
			$specific_form_css['form_background_color'] = "background-color: {$ctct_form_background_color};";
		}

		$ctct_form_description_font_size = get_post_meta( $form_id, '_ctct_form_description_font_size', true );
		if ( ! empty( $ctct_form_description_font_size ) ) {
			$specific_form_css['form_description_font_size'] = "font-size: {$ctct_form_description_font_size};";
		}

		$ctct_form_description_color = get_post_meta( $form_id, '_ctct_form_description_color', true );
		if ( ! empty( $ctct_form_description_color ) ) {
			$specific_form_css['form_description_color'] = "color: {$ctct_form_description_color};";
		}

		$ctct_form_submit_button_font_size = get_post_meta( $form_id, '_ctct_form_submit_button_font_size', true );
		if ( ! empty( $ctct_form_submit_button_font_size ) ) {
			$specific_form_css['form_submit_button_font_size'] = "font-size: {$ctct_form_submit_button_font_size};";
		}

		$ctct_form_submit_button_text_color = get_post_meta( $form_id, '_ctct_form_submit_button_text_color', true );
		if ( ! empty( $ctct_form_submit_button_text_color ) ) {
			$specific_form_css['form_submit_button_text_color'] = "color: {$ctct_form_submit_button_text_color};";
		}

		$ctct_form_submit_button_background_color = get_post_meta( $form_id, '_ctct_form_submit_button_background_color', true );
		if ( ! empty( $ctct_form_submit_button_background_color ) ) {
			$specific_form_css['form_submit_button_background_color'] = "background-color: {$ctct_form_submit_button_background_color};";
		}

		$ctct_form_padding_top = get_post_meta( $form_id, '_ctct_form_padding_top', true );
		if ( ! empty( $ctct_form_padding_top ) ) {
			$specific_form_css['form_padding_top'] = "padding-top: {$ctct_form_padding_top}px;";
		}

		$ctct_form_padding_right = get_post_meta( $form_id, '_ctct_form_padding_right', true );
		if ( ! empty( $ctct_form_padding_right ) ) {
			$specific_form_css['form_padding_right'] = "padding-right: {$ctct_form_padding_right}px;";
		}

		$ctct_form_padding_bottom = get_post_meta( $form_id, '_ctct_form_padding_bottom', true );
		if ( ! empty( $ctct_form_padding_bottom ) ) {
			$specific_form_css['form_padding_bottom'] = "padding-bottom: {$ctct_form_padding_bottom}px;";
		}

		$ctct_form_padding_left = get_post_meta( $form_id, '_ctct_form_padding_left', true );
		if ( ! empty( $ctct_form_padding_left ) ) {
			$specific_form_css['form_padding_left'] = "padding-left: {$ctct_form_padding_left}px;";
		}

		$ctct_input_custom_classes = get_post_meta( $form_id, '_ctct_input_custom_classes', true );
		if ( ! empty( $ctct_input_custom_classes ) ) {
			$specific_form_css['input_custom_classes'] = esc_attr( $ctct_input_custom_classes );
		}

		$this->specific_form_styles = wp_parse_args( $specific_form_css, $defaults );
	}

	/**
	 * Main wrapper for getting our form display.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $form_data   Array of form data.
	 * @param string $form_id     Form ID.
	 * @param bool   $skip_styles Whether or not to skip style additions.
	 * @return string Form markup.
	 */
	public function form( $form_data, $form_id = '', $skip_styles = false ) {

		if ( 'publish' !== get_post_status( $form_id ) ) {
			return '';
		}

		$this->set_global_form_css();
		$this->set_specific_form_css( $form_id );
		$return           = '';
		$form_err_display = '';
		$error_message    = false;
		$status           = false;

		// Get a potential response from our processing wrapper
		// This returns an array that has 'status' and 'message keys'
		// if the status is success, then we sent the form correctly
		// if the status is error, then we will re-show the form, but also
		// with our error messages.
		$response = constant_contact()->process_form->process_wrapper( $form_data, $form_id );

		// Submitted values.
		$old_values = isset( $response['values'] ) ? $response['values'] : '';
		$req_errors = isset( $response['errors'] ) ? $response['errors'] : '';

		// Check to see if we got a response, and if it has the fields we expect.
		if ( $response && isset( $response['message'] ) && isset( $response['status'] ) ) {

			// If we were successful, then display success message.
			if ( 'success' === $response['status'] ) {

				// If we were successful, we'll return here so we don't display the entire form again.
				return $this->message( 'success', $response['message'] );

			} else {

				// If we didn't get a success message, then we want to error.
				// We already checked for a message response, but we'll force the
				// status to error if we're not here.
				$status        = 'error';
				$error_message = trim( $response['message'] );
			}
		}

		// If we got an error for our status, and we have an error message, display it.
		if ( 'error' === $status || $error_message ) {

			if ( ! empty( $error_message ) ) {
				// We'll show this error right inside our form.
				$form_err_display = $this->message( 'error', $error_message );
			}
		}

		// Force uniqueness of an id for the form.
		$rf_id = 'ctct-form-' . mt_rand();

		/**
		 * Filters the action value to use for the contact form.
		 *
		 * @since 1.1.1
		 *
		 * @param string $value   Value to put in the form action attribute. Default empty string.
		 * @param int    $form_id ID of the Constant Contact form being rendered.
		 */
		$form_action    = apply_filters( 'constant_contact_front_form_action', '', $form_id );
		$should_do_ajax = get_post_meta( $form_id, '_ctct_do_ajax', true );
		$do_ajax        = ( 'on' === $should_do_ajax ) ? $should_do_ajax : 'off';
		$form_classes   = 'ctct-form ctct-form-' . $form_id;
		$form_classes   .= ( $this->plugin->settings->has_recaptcha() ) ? ' has-recaptcha' : ' no-recaptcha';
		$form_classes  .= $this->build_custom_form_classes();

		$form_styles = '';
		if ( ! empty( $this->specific_form_styles['form_background_color'] ) ) {
			$form_styles = $this->specific_form_styles['form_background_color'];
		}

		foreach( ['bottom','left','right','top'] as $pos ) {
			$form_styles .= $this->specific_form_styles[ 'form_padding_' . $pos ];
		}

		// Add action before form for custom actions.
		ob_start();
		/**
		 * Fires before the start of the form tag.
		 *
		 * @since 1.4.0
		 *
		 * @param int $form_id Current form ID.
		 */
		do_action( 'ctct_before_form', $form_id );
		$return .= ob_get_contents();
		ob_end_clean();

		// Build out our form.
		$return .= '<form class="' . esc_attr( $form_classes ) . '" id="' . $rf_id . '" ';
		$return .= 'data-doajax="' . esc_attr( $do_ajax ) . '" ';
		$return .= 'style="' . esc_attr( $form_styles ) . '" ';
		$return .= 'action="' . esc_attr( $form_action ) . '" ';
		$return .= 'method="post">';

		// If we have errors, display them.
		$return .= $form_err_display;

		// Output our normal form fields.
		$return .= $this->build_form_fields( $form_data, $old_values, $req_errors );

		if ( $this->plugin->settings->has_recaptcha() ) {
			$return .= $this->build_recaptcha();
		}

		// Output a field that should not be populated, and will be visually hidden with inline CSS.
		$return .= $this->build_honeypot_field();

		// Add our hidden verification fields.
		$return .= $this->add_verify_fields( $form_data );

		$return .= $this->build_timestamp();

		// Add our submit field.
		$return .= $this->submit( $form_id );

		// Nonce the field too.
		$return .= wp_nonce_field( 'ctct_submit_form', 'ctct_form', true, false );

		// Add our disclose notice maybe.
		$return .= wp_kses_post( $this->maybe_add_disclose_note( $form_data ) );

		$return .= $this->must_opt_in( $form_data );

		$return .= '</form>';

		ob_start();
		/**
		 * Fires after the end of the form tag.
		 *
		 * @since 1.4.0
		 *
		 * @param int $form_id Current form ID.
		 */
		do_action( 'ctct_after_form', $form_id );
		$return .= ob_get_contents();
		ob_end_clean();

		$return .= '<script type="text/javascript">';
		$return .= 'var ajaxurl = "' . esc_url( admin_url( 'admin-ajax.php' ) ) . '";';
		$return .= '</script>';

		return $return;
	}

	/**
	 * Get our current URL in a somewhat robust way.
	 *
	 * @since 1.0.0
	 *
	 * @return string URL of current page.
	 */
	public function get_current_page() {

		// Grab our global wp objects.
		global $wp;

		// If we have a request, use that.
		$request = ( isset( $wp->request ) && $wp->request ) ? $wp->request : null;

		// If we still have a request, lets get our url magically.
		if ( $request ) {

			$curr_url = untrailingslashit( add_query_arg( '', '', home_url( $request ) ) );

			// If we're not using a custom permalink structure, theres a chance the above
			// will return the home_url. so we do another check to makesure we're going
			// to use the right thing. This check doesn't work on the homepage, but
			// that will just get caught with our fallback check correctly anyway.
			if ( ! is_home() && ( home_url() !== $curr_url ) ) {
				return $curr_url;
			}
		}

		// Otherwise, we'll default to just using add_query_arg, which may throw errors.
		return untrailingslashit( home_url( add_query_arg( array( '' => '' ) ) ) );
	}

	/**
	 * Adds hidden input fields to our form for form id and verify id.
	 *
	 * @since 1.0.0
	 *
	 * @param string $form_data html markup.
	 * @return mixed.
	 */
	public function add_verify_fields( $form_data ) {

		if (
			isset( $form_data ) &&
			isset( $form_data['options'] ) &&
			isset( $form_data['options']['form_id'] )
		) {

			$form_id = absint( $form_data['options']['form_id'] );

			if ( ! $form_id ) {
				return false;
			}

			// Add hidden field with our form id in it.
			$return = $this->input( 'hidden', 'ctct-id', 'ctct-id', $form_id, '', '', true );

			// If we have saved a verify value, add that to our field as well. this is to double-check
			// that we have the correct form id for processing later.
			$verify_key = get_post_meta( $form_id, '_ctct_verify_key', true );

			if ( $verify_key ) {
				$return .= $this->input( 'hidden', 'ctct-verify', 'ctct-verify', $verify_key, '', '', true );
			}

			return $return;
		}

		return false;
	}

	/**
	 * Build form fields for shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form_data  Formulated cmb2 data for form.
	 * @param array $old_values Original values.
	 * @param array $req_errors Errors.
	 * @return string
	 */
	public function build_form_fields( $form_data, $old_values, $req_errors ) {

		// Start our wrapper return var.
		$return  = '';
		$form_id = absint( $form_data['options']['form_id'] );

		// Check to see if we have a form ID for the form, and display our description.
		if ( isset( $form_data['options'] ) && isset( $form_data['options']['form_id'] ) ) {

			// Get our description.
			$desc = isset( $form_data['options']['description'] ) ? $form_data['options']['description'] : '';

			// Get our Description.
			$return .= $this->description( $desc, $form_id );

		}

		$label_placement = constant_contact_get_css_customization( $form_id, '_ctct_form_label_placement' );
		if ( empty( $label_placement ) ) {
			$label_placement = 'top';
		}

		if ( isset( $form_data['fields'] ) && is_array( $form_data['fields'] ) ) {
			// Loop through each of our form fields and output it.
			foreach ( $form_data['fields'] as $key => $value ) {
				$return .= $this->field( $value, $old_values, $req_errors, $form_id, $label_placement );
			}
		}

		// Check to see if we have an opt-in for the form, and display it.
		if ( isset( $form_data['options'] ) ) {
			$return .= $this->opt_in( $form_data['options'] );
		}

		return $return;
	}

	/**
	 * Display a honeypot spam field.
	 *
	 * @since 1.2.2
	 *
	 * @return string
	 */
	public function build_honeypot_field() {
		$return = '';

		$return .= sprintf(
			'<div id="ctct_usage"><label for="ctct_usage_field">%s</label><input type="text" value="" name="ctct_usage_field" id="ctct_usage_field" /></div>',
			esc_html__( 'Constant Contact Use.', 'constant-contact-forms' )
		);

		return $return;
	}

	public function build_recaptcha() {
		// If we've reached this point, we know we have our keys.
		$site_key = ctct_get_settings_option( '_ctct_recaptcha_site_key', '' );

		/**
		 * Filters the language code to be used with Google reCAPTCHA.
		 *
		 * See https://developers.google.com/recaptcha/docs/language for available values.
		 *
		 * @since 1.2.4
		 *
		 * @param string $value Language code to use. Default 'en'.
		 */
		$recaptcha_lang = apply_filters( 'constant_contact_recaptcha_lang', 'en' );

		$return = '';

		$return .= '<script>function ctctEnableBtn(){ jQuery( "#ctct-submitted" ).attr( "disabled", false ); }function ctctDisableBtn(){ jQuery( "#ctct-submitted" ).attr( "disabled", "disabled" ); }
</script>';
		$return .= sprintf(
			'<div class="g-recaptcha" data-sitekey="%s" data-callback="%s" data-expired-callback="%s"></div><script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=%s"></script>',
			esc_attr( $site_key ),
			'ctctEnableBtn',
			'ctctDisableBtn',
			esc_attr( $recaptcha_lang )
		);

		return $return;
	}

	public function build_timestamp() {
		return '<input type="hidden" name="ctct_time" value="' . current_time( 'timestamp' ) . '" />';
	}

	public function build_custom_form_classes() {
		$custom = '';
		if ( ! empty( $this->global_form_styles['global_form_classes'] ) ) {
			$custom .= ' ' . esc_attr( $this->global_form_styles['global_form_classes'] );
		}
		if ( ! empty( $this->specific_form_styles['input_custom_classes'] ) ) {
			$custom .= ' ' . esc_attr( $this->specific_form_styles['input_custom_classes'] );
		}

		return $custom;
	}

	/**
	 * Use a hidden field to denote needing to opt in.
	 *
	 * @since 1.3.6
	 *
	 * @param array $form_data
	 * @return string
	 */
	public function must_opt_in( array $form_data ) {
		if ( empty( $form_data['options']['optin']['show'] ) ) {
			return '';
		}

		return '<input type="hidden" name="ctct_must_opt_in" value="yes" />';
	}

	/**
	 * Wrapper for single field display.
	 *
	 * @since 1.0.0
	 * @since 1.4.0 Added label placement parameter.
	 *
	 * @param array  $field           Field data.
	 * @param array  $old_values      Original values.
	 * @param array  $req_errors      Errors.
	 * @param int    $form_id         Current form ID.
	 * @param string $label_placement Label placement location.
	 * @return string HTML markup
	 */
	public function field( $field, $old_values = array(), $req_errors = array(), $form_id = 0, $label_placement = 'top' ) {

		// If we don't have a name or a mapping, it will be hard to do things.
		if ( ! isset( $field['name'] ) || ! isset( $field['map_to'] ) ) {
			return '';
		}

		$field = wp_parse_args( $field, array(
			'name'             => '',
			'map_to'           => '',
			'type'             => '',
			'description'      => '',
			'field_custom_css' => array(),
			'required'         => false,
		) );

		// Check all our data points.
		$name  = sanitize_text_field( $field['name'] );
		$map   = sanitize_text_field( $field['map_to'] );
		$desc  = sanitize_text_field( isset( $field['description'] ) ? $field['description'] : '' );
		$type  = sanitize_text_field( isset( $field['type'] ) ? $field['type'] : 'text_field' );
		$value = sanitize_text_field( isset( $field['value'] ) ? $field['value'] : false );
		$req   = isset( $field['required'] ) ? $field['required'] : false;

		// We may have more than one of the same field in our array.
		// this makes sure we keep them unique when processing them.
		if ( 'submit' !== $type ) {
			$temp_field = $field;
			unset( $temp_field['field_custom_css'] );
			$map = $map . '___' . md5( serialize( $temp_field ) );
		}

		// Default error status.
		$field_error = false;

		// If we got any errors, then pass them through to the form field.
		if ( ! empty( $req_errors ) ) {

			// Loop through each error.
			foreach ( $req_errors as $error ) {

				// Make sure we have a field ID and an actual error.
				if ( isset( $error['id'] ) && isset( $error['error'] ) ) {

					// If the error matches the field we're rendering.
					if ( $map === $error['id'] ) {

						// Start our field error return.
						$field_error = '<span class="ctct-field-error">';

						// Based on the error type, display an error.
						if ( 'invalid' === $error['error'] ) {
							$field_error .= __( 'Error: Please correct your entry.', 'constant-contact-forms' );
						} else {
							$field_error .= __( ' Error: Please fill out this field.', 'constant-contact-forms' );
						}

						// Finish error return.
						$field_error .= '</span>';
					}
				}
			}
		}

		// Potentially replace value with submitted value.
		$value = $this->get_submitted_value( $value, $map, $field, $old_values );

		// Based on our type, output different things.
		switch ( $type ) {
			case 'custom':
			case 'first_name':
			case 'last_name':
			case 'phone_number':
			case 'job_title':
			case 'company':
			case 'website':
			case 'text_field':
				return $this->input( 'text', $name, $map, $value, $desc, $req, false, $field_error, $form_id, $label_placement );
				break;
			case 'custom_text_area':
				return $this->textarea( $name, $map, $value, $desc, $req, $field_error, 'maxlength="500"', $label_placement );
				break;
			case 'email':
				return $this->input( 'email', $name, $map, $value, $desc, $req, false, $field_error, $form_id, $label_placement );
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
				return $this->address( $name, $map, $value, $desc, $req, $field_error, $label_placement );
				break;
			case 'anniversery':
			case 'birthday':
				// Need this to be month / day / year.
				return $this->dates( $name, $map, $value, $desc, $req, $field_error );
				break;
			default:
				return $this->input( 'text', $name, $map, $value, $desc, $req, false, $field_error );
				break;
		}
	}

	/**
	 * Gets submitted values.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $value          Field value.
	 * @param string       $map            Map value.
	 * @param array        $field          Array of fields.
	 * @param array        $submitted_vals Array of submitted values.
	 * @return string Submitted value.
	 */
	public function get_submitted_value( $value = '', $map = '', $field = array(), $submitted_vals = array() ) {

		if ( $value ) {
			return $value;
		}

		if ( ! is_array( $submitted_vals ) ) {
			return '';
		}

		$return = array();

		foreach ( $submitted_vals as $post ) {

			if ( isset( $post['key'] ) && $post['key'] ) {

				// If we have an address, its a special case.
				if ( 'address' === $field['name'] ) {

					// If any of our keys contain our address breaker, then add
					// it to the array.
					if ( strpos( $post['key'], '_address___' ) !== false ) {

						// Try to grab the street_address (etc) part of our key.
						$addr_key = explode( '___', $post['key'] );

						// If we got something, add it to our return array.
						if ( isset( $addr_key[0] ) && $addr_key[0] ) {

							$post_key = '';

							// Validate our data we're about to use
							//
							// We also flag PHPCS to ignore this line, as we get
							// a nonce verification error, but we process the nonce
							// quite a bit earlier than this
							//
							// @codingStandardsIgnoreLine
							if ( isset( $_POST[ esc_attr( $post['key'] ) ] ) ) { // Input var okay.

								// We also flag PHPCS to ignore this line, as we get
								// a nonce verification error, but we process the nonce
								// quite a bit earlier than this
								//
								// @codingStandardsIgnoreLine
								$post_key = sanitize_text_field( wp_unslash( $_POST[ esc_attr( $post['key'] ) ] ) ); // Input var okay.
							}

							// Set our return data.
							$return[ esc_attr( $addr_key[0] ) ] = $post_key;
						}
					}
				} elseif ( $post['key'] === $map && isset( $_POST[ esc_attr( $map ) ] ) ) {
					// Otherwise make sure we have a value.
					//
					// We also flag PHPCS to ignore this line, as we get
					// a nonce verification error, but we process the nonce
					// quite a bit earlier than this.

					// Clean and return.
					//
					// We also flag PHPCS to ignore this line, as we get
					// a nonce verification error, but we process the nonce
					// quite a bit earlier than this
					return sanitize_text_field( wp_unslash( $_POST[ esc_attr( $map ) ] ) );
				} // End if().
			} // End if().
		} // End foreach().

		return $return;
	}

	/**
	 * Helper method to display in-line for success/error messages.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type    Success/error/etc for class.
	 * @param string $message Message to display to user.
	 * @return string HTML markup.
	 */
	public function message( $type, $message ) {
		return '<p class="ctct-message ' . esc_attr( $type ) . '">' . esc_attr( $message ) . '</p>';
	}

	/**
	 * Get an inline style tag to use for the form's description.
	 *
	 * @author Scott Tirrell
	 * @since  1.4.0
	 *
	 * @return string The inline style tag for the form's description.
	 */
	public function get_description_inline_styles() {
		$inline_style = '';
		$styles       = array();

		// Set any custom CSS for the form description.
		$specific_form_styles = $this->specific_form_styles;

		if ( ! empty( $specific_form_styles['form_description_font_size'] ) ) {
			$styles[] = $specific_form_styles['form_description_font_size'];
		}

		if ( ! empty( $specific_form_styles['form_description_color'] ) ) {
			$styles[] = $specific_form_styles['form_description_color'];
		}

		if ( ! empty( $styles ) ) {
			$inline_style = 'style="' . esc_attr( implode( ' ', $styles ) ) . '"';
		}

		return $inline_style;
	}

	/**
	 * Helper method to display form description.
	 *
	 * @since 1.0.0
	 *
	 * @param string      $desc    Description to output.
	 * @param int|boolean $form_id Form ID.
	 * @return string Form description markup.
	 */
	public function description( $desc = '', $form_id = false ) {

		$display      = '';
		$inline_style = $this->get_description_inline_styles();

		// If we have the permissions, also display an edit link.
		if ( current_user_can( 'edit_posts' ) && $form_id ) {

			// Get our edit link.
			$edit_link = get_edit_post_link( absint( $form_id ) );

			// If we got a link, display it.
			if ( $edit_link ) {
				$display .= '<a class="button ctct-button" href="' . esc_url( $edit_link ) . '">' . __( 'Edit Form', 'constant-contact-forms' ) . '</a>';
			}
		}

		return '<span class="ctct-form-description" ' . $inline_style . '>' . wpautop( wp_kses_post( $desc ) ) . '</span>' . $display;
	}

	/**
	 * Helper method to display label for form field + field starting markup.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $type           Type of field.
	 * @param string  $name           Name / id of field.
	 * @param string  $f_id           Field ID.
	 * @param string  $label          Label text for field.
	 * @param boolean $req            If this field required.
	 * @param boolean $use_label      Whether or not to use label.
	 * @return string HTML markup.
	 */
	public function field_top( $type = '', $name = '', $f_id = '', $label = '', $req = false, $use_label = true ) {

		$classes = array(
			'ctct-form-field',
			'ctct-form-field-' . $type,
		);
		if ( $req ) {
			$classes[] = 'ctct-form-field-required';
		}

		// Start building our return markup.
		$markup = '<p class="' . implode( ' ', $classes ) . '">';

		// If we're not on submit or hidden, but still doing label on bottom,
		// then output a container div.
		if ( ! $use_label ) {
			$markup .= '<span class="ctct-input-container">';
		}

		return $markup;
	}

	/**
	 * Bottom of field markup.
	 *
	 * @since 1.0.0
	 * @since 1.3.5 Added $use_label
	 *
	 * @param string $name        Field name.
	 * @param string $field_label Field label.
	 * @param bool   $use_label   Whether or not to include label markup.
	 * @return string HTML markup
	 */
	public function field_bottom( $name = '', $field_label = '', $use_label = true ) {

		$markup = '';
		if ( ! empty( $name ) && ! empty( $field_label ) ) {
			$markup .= $this->get_label( $name, $field_label );
		}

		if ( ! $use_label ) {
			$markup .= '</span>';
		}

		// Finish building our markup.
		return $markup . '</p>';
	}

	/**
	 * Get inline styles for the form's submit button.
	 *
	 * @since 1.4.0
	 * @author Scott Tirrell
	 *
	 * @return string
	 */
	public function get_submit_inline_styles() {
		$inline_style = '';
		$styles       = array();

		// Set any custom CSS for the form submit button.
		$specific_form_styles = $this->specific_form_styles;

		if ( ! empty( $specific_form_styles['form_submit_button_font_size'] ) ) {
			$styles[] = $specific_form_styles['form_submit_button_font_size'];
		}

		if ( ! empty( $specific_form_styles['form_submit_button_text_color'] ) ) {
			$styles[] = $specific_form_styles['form_submit_button_text_color'];
		}

		if ( ! empty( $specific_form_styles['form_submit_button_background_color'] ) ) {
			$styles[] = $specific_form_styles['form_submit_button_background_color'];
		}

		if ( ! empty( $styles ) ) {
			$inline_style = 'style="' . esc_attr( implode( ' ', $styles ) ) . '"';
		}

		return $inline_style;
	}

	/**
	 * Helper method to get form label.
	 *
	 * @since 1.0.0
	 *
	 * @param string $f_id        Name/id of form field.
	 * @param string $field_label Text to display as label.
	 * @return string HTML markup
	 */
	public function get_label( $f_id, $field_label ) {
		return '<label for="' . $f_id . '">' . $field_label . '</label>';
	}

	/**
	 * Wrapper for 'input' form fields.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $type                 Type of form field.
	 * @param string  $name                 ID of form field.
	 * @param string  $id                   ID attribute value.
	 * @param string  $value                pre-filled value.
	 * @param string  $label                label text for input.
	 * @param boolean $req                  If field required.
	 * @param boolean $f_only               If we only return the field itself, with no label.
	 * @param boolean $field_error          Field error.
	 * @param int     $form_id              Current form ID.
	 * @param string  $label_placement      Where to place the label.
	 * @return string HTML markup for field.
	 */
	public function input( $type = 'text', $name = '', $id = '', $value = '', $label = '', $req = false, $f_only = false, $field_error = false, $form_id = 0, $label_placement = '' ) {

		// Sanitize our stuff / set values.
		$name                  = sanitize_text_field( $name );
		$f_id                  = sanitize_title( $id );
		$input_inline_styles   = '';
		$label_placement_class = 'ctct-label-' . $label_placement;
		$specific_form_styles  = $this->specific_form_styles;

		// Use different styles for submit button.
		if ( 'submit' === $type ) {
			$input_inline_styles = $this->get_submit_inline_styles();
		}

		$type     = sanitize_text_field( $type );
		$value    = sanitize_text_field( $value );
		$label    = sanitize_text_field( $label );
		$req_text = $req ? 'required' : '';

		// Start our markup.
		$markup = $this->field_top( $type, $name, $f_id, $label, $req );

		// Set blank defaults for required info.
		$req_label = '';
		// If this is required, we output the HMTL5 required att.
		if ( $req ) {

			/**
			 * Filters the markup used for the required indicator.
			 *
			 * @since 1.0.0
			 *
			 * @param string $value An `<abbr>` tag with an asterisk indicating required status.
			 */
			$req_label = apply_filters( 'constant_contact_required_label', '<abbr title="required">*</abbr>' );
		}
		if ( ( 'top' === $label_placement || 'left' === $label_placement || 'hidden' === $label_placement ) && ( 'submit' !== $type ) && ( 'hidden' !== $type ) ) {
			$markup .= '<span class="' . $label_placement_class . '">';
			$markup .= $this->get_label( $f_id, $name . ' ' . $req_label );
			$markup .= '</span>';
		}

		// Provide some CSS class(es).
		$classes   = array( 'ctct-' . esc_attr( $type ) );
		$classes[] = $label_placement_class;
		if ( ! empty( $specific_form_styles['input_custom_classes'] ) ) {
			$custom_input_classes = explode( ' ', $specific_form_styles['input_custom_classes'] );
			$classes              = array_merge( $classes, $custom_input_classes );
		}

		/**
		 * Filter to add classes for the rendering input.
		 *
		 * @since  1.2.0
		 * @param  array  $classes Array of classes to apply to the field.
		 * @param  string $type    The field type being rendered.
		 * @return array
		 */
		$classes = apply_filters( 'constant_contact_input_classes', $classes, $type );

		/**
		 * Filters whether or not to remove characters from potential maxlength attribute value.
		 *
		 * @since 1.3.0
		 *
		 * @param bool $value Whether or not to truncate. Default false.
		 */
		$truncate_max_length = apply_filters( 'constant_contact_include_custom_field_label', false, $form_id );
		$max_length          = '';
		if ( false !== strpos( $id, 'custom___' ) ) {
			$max_length = ( $truncate_max_length ) ? $this->get_max_length_attr( $name ) : $this->get_max_length_attr();
		}

		// If we have an error.
		if ( $field_error ) {
			// Tack an error class on to the end of our classes.
			$classes[] = 'ctct-invalid';
		}

		$class_attr = '';
		// Append classes to our field.
		if ( count( $classes ) ) {
			$class_attr = 'class="' . implode( ' ', $classes ) . '"';
		}

		// Finish the markup for our field itself.
		$field = '<input %s type="%s" name="%s" id="%s" %s value="%s" %s placeholder="%s" %s />';
		$markup .= sprintf(
			$field,
			$req_text,
			$type,
			$f_id,
			$f_id,
			$input_inline_styles,
			$value,
			$max_length,
			$label,
			$class_attr
		);
		// Reassign because if we want "field only", like for hidden inputs, we need to
		// still pass a value that went through sprintf().
		$field = $markup;

		if ( ( 'bottom' === $label_placement || 'right' === $label_placement ) && ( 'submit' !== $type ) && ( 'hidden' !== $type ) ) {
			$markup .= '<span class="' . $label_placement_class . '">';
			$markup .= $this->get_label( $f_id, $name . ' ' . $req_label );
			$markup .= '</span>';
		}

		// If we got an error, add it to the bottom label.
		if ( $field_error ) {
			$markup .= $this->field_bottom( $id, $field_error );
		} else {
			$markup .= $this->field_bottom();
		}

		// If we passed in a flag for only the field, just return that.
		if ( $f_only ) {
			return $field;
		}

		return $markup;
	}

	/**
	 * Checkbox field helper method.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name  Name/it of field.
	 * @param string $f_id  Field ID.
	 * @param string $value Value of field.
	 * @param string $label Label / desc text.
	 * @return string HTML markup for checkbox.
	 */
	public function checkbox( $name = '', $f_id = '', $value = '', $label = '' ) {

		$name  = sanitize_text_field( $name );
		$f_id  = sanitize_title( $f_id );
		$value = sanitize_text_field( $value );
		$label = esc_attr( $label );
		$type = 'checkbox';

		// Provide some CSS class(es).
		$classes = array( 'ctct-' . esc_attr( $type ) );

		/**
		 * Filter to add classes for the rendering input.
		 *
		 * @since 1.2.0
		 * @todo  Can we abstract this to use $this->input?
		 *
		 * @param array  $classes Array of classes to apply to the field.
		 * @param string $type    The field type being rendered.
		 * @return array
		 */
		$classes = apply_filters( 'constant_contact_input_classes', $classes, $type );

		$markup  = $this->field_top( $type, $name, $f_id, $label, false, false );
		$markup .= '<input type="' . $type . '" name="' . $f_id . '" id="' . $f_id . '" value="' . $value . '" class="' . implode( ' ', $classes ) . '" />';
		$markup .= $this->field_bottom( $name, ' ' . $label );

		return $markup;
	}

	/**
	 * Helper method for submit button.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Added form ID parameter.
	 *
	 * @param int $form_id Rendered form ID.
	 * @return string HTML markup.
	 */
	public function submit( $form_id = 0 ) {
		$button_text = get_post_meta( $form_id, '_ctct_button_text', true );
		$button_text =
		( ! empty( $button_text ) ) ?
			$button_text :
			/**
			 * Filters the text that appears on the submit button.
			 *
			 * @since 1.1.0
			 *
			 * @param string $value Submit button text.
			 */
			apply_filters( 'constant_contact_submit_text', __( 'Send', 'constant-contact-forms' )
		);

		return $this->field( array(
			'type'   => 'submit',
			'name'   => 'ctct-submitted',
			'map_to' => 'ctct-submitted',
			'value'  => $button_text,
		) );
	}

	/**
	 * Build markup for opt_in form.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form_data Form data structure.
	 * @return string Markup of optin form.
	 */
	public function opt_in( $form_data ) {

		// Make sure we have our optin data.
		if ( ! isset( $form_data['optin'] ) ) {
			return '';
		}

		// Set up our defaults.
		$optin = wp_parse_args( $form_data['optin'], array(
			'list'         => false,
			'show'         => false,
			'instructions' => '',
		) );

		// Make sure we have our opt in set, as well as an associated list.
		if ( isset( $optin['list'] ) && $optin['list'] ) {
			return $this->optin_display( $optin );
		}

		return '';
	}

	/**
	 * Internal method to display checkbox.
	 *
	 * @since 1.0.0
	 *
	 * @param array $optin Optin data.
	 * @return string HTML markup.
	 */
	public function optin_display( $optin ) {

		$label   = sanitize_text_field( isset( $optin['instructions'] ) ? $optin['instructions'] : '' );
		$value   = sanitize_text_field( isset( $optin['list'] ) ? $optin['list'] : '' );

		$show = false;
		if ( isset( $optin['show'] ) && 'on' === $optin['show'] ) {
			$show = true;
		}

		$markup = '';

		// If we set to hide the field, then hide it inline.
		if ( ! $show ) {
			$markup = '<div class="ctct-optin-hide" style="display:none;">';
		}

		// Grab our markup.
		$markup .= $this->get_optin_markup( $label, $value, $show );

		// If we set to hide, close our open div.
		if ( ! $show ) {
			$markup .= '</div><!--.ctct-optin-hide -->';
		}

		return $markup;
	}

	/**
	 * Helper method to get optin markup.
	 *
	 * @since 1.0.0
	 *
	 * @param string $label Label for field.
	 * @param string $value Value of opt in field.
	 * @param string $show  Whether or not we are showing the field.
	 * @return string HTML markup
	 */
	public function get_optin_markup( $label, $value, $show ) {

		// If we aren't showing the field, then we default our checkbox to checked.
		$checked = $show ? '' : 'checked';

		$markup  = $this->field_top( 'checkbox', 'ctct-opt-in', 'ctct-opt-in', $label, false, false );
		$markup .= '<input type="checkbox" ' . $checked . ' name="ctct-opt-in" id="ctct-opt-in" class="ctct-checkbox ctct-opt-in" value="' . $value . '" />';
		$markup .= $this->field_bottom( 'ctct-opt-in', ' ' . wp_kses_post( $label ), false );

		return $markup;
	}

	/**
	 * Builds a fancy address field group.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $name            Name of fields.
	 * @param string  $f_id            Form ID name.
	 * @param array   $value           Values of each field.
	 * @param string  $desc            Label of field.
	 * @param boolean $req             Whether or not required.
	 * @param string  $field_error     Field error value.
	 * @param string  $label_placement Where to put the label.
	 * @return string field HTML markup.
	 */
	public function address( $name = '', $f_id = '', $value = array(), $desc = '', $req = false, $field_error = '', $label_placement = 'top' ) {

		// Set up our text strings.
		$street = __( 'Street Address', 'constant-contact-forms' );
		$line_2 = __( 'Address Line 2', 'constant-contact-forms' );
		$city   = __( 'City', 'constant-contact-forms' );
		$state  = __( 'State', 'constant-contact-forms' );
		$zip    = __( 'ZIP Code', 'constant-contact-forms' );

		// Set our values.
		$v_street = isset( $value['street_address'] ) ? $value['street_address'] : '';
		$v_line_2 = isset( $value['line_2_address'] ) ? $value['line_2_address'] : '';
		$v_city   = isset( $value['city_address'] ) ? $value['city_address'] : '';
		$v_state  = isset( $value['state_address'] ) ? $value['state_address'] : '';
		$v_zip    = isset( $value['zip_address'] ) ? $value['zip'] : '';

		/**
		 * Filters the markup used for the required indicator.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value An `<abbr>` tag with an asterisk indicating required status.
		 */
		$req_label             = $req ? ' ' . apply_filters( 'constant_contact_required_label', '<abbr title="required">*</abbr>' ) : '';
		$req_class             = $req ? ' ctct-form-field-required ' : '';
		$req                   = $req ? ' required ' : '';
		$label_placement_class = 'ctct-label-' . $label_placement;

		$label_street1 = sprintf(
			'<span class="%s"><label for="street_%s">%s</label></span>',
			esc_attr( $label_placement_class ),
			esc_attr( $f_id ),
			esc_attr( $street ) . $req_label
		);
		$input_street1 = sprintf(
			'<input %stype="text" class="ctct-text ctct-address-street %s" name="street_%s" id="street_%s" value="%s">',
			$req,
			esc_attr( $label_placement_class ),
			esc_attr( $f_id ),
			esc_attr( $f_id ),
			esc_attr( $v_street )
		);

		$input_street1_whole = '';
		if ( 'top' === $label_placement || 'left' === $label_placement || 'hidden' === $label_placement ) {
			$input_street1_whole = $label_street1 . $input_street1;
		}
		if ( 'bottom' === $label_placement || 'right' === $label_placement ) {
			$input_street1_whole = $input_street1 . $label_street1;
		}

		$label_street2 = sprintf(
			'<span class="%s"><label for="line_2_%s">%s</label></span>',
			$label_placement_class,
			esc_attr( $f_id ),
			esc_attr( $line_2 )
		);
		// Address Line 2 is not required, note the missing $req inclusion.
		$input_street2 = sprintf(
			'<input type="text" class="ctct-text ctct-address-line-2 %s" name="line_2_%s" id="line_2_%s" value="%s">',
			esc_attr( $label_placement_class ),
			esc_attr( $f_id ),
			esc_attr( $f_id ),
			esc_attr( $v_line_2 )
		);

		$input_street2_whole = '';
		if ( 'top' === $label_placement || 'left' === $label_placement || 'hidden' === $label_placement ) {
			$input_street2_whole = $label_street2 . $input_street2;
		}
		if ( 'bottom' === $label_placement || 'right' === $label_placement ) {
			$input_street2_whole = $input_street2 . $label_street2;
		}

		$label_city = sprintf(
			'<span class="%s"><label for="city_%s">%s</label></span>',
			$label_placement_class,
			esc_attr( $f_id ),
			esc_attr( $city ) . $req_label
		);
		$input_city = sprintf(
			'<input %stype="text" class="ctct-text ctct-address-city %s" name="city_%s" id="city_%s" value="%s">',
			$req,
			esc_attr( $label_placement_class ),
			esc_attr( $f_id ),
			esc_attr( $f_id ),
			esc_attr( $v_city )
		);

		$input_city_whole = '';
		if ( 'top' === $label_placement || 'left' === $label_placement || 'hidden' === $label_placement ) {
			$input_city_whole = $label_city . $input_city;
		}
		if ( 'bottom' === $label_placement || 'right' === $label_placement ) {
			$input_city_whole = $input_city . $label_city;
		}

		$label_state = sprintf(
			'<span class="%s"><label for="state_%s">%s</label></span>',
			$label_placement_class,
			esc_attr( $f_id ),
			esc_attr( $state ) . $req_label
		);
		$input_state = sprintf(
			'<input %stype="text" class="ctct-text ctct-address-state %s" name="state_%s" id="state_%s" value="%s">',
			$req,
			esc_attr( $label_placement_class ),
			esc_attr( $f_id ),
			esc_attr( $f_id ),
			esc_attr( $v_state )
		);

		$input_state_whole = '';
		if ( 'top' === $label_placement || 'left' === $label_placement || 'hidden' === $label_placement ) {
			$input_state_whole = $label_state . $input_state;
		}
		if ( 'bottom' === $label_placement || 'right' === $label_placement ) {
			$input_state_whole = $input_state . $label_state;
		}

		$label_zip = sprintf(
			'<span class="%s"><label for="zip_%s">%s</label></span>',
			$label_placement_class,
			esc_attr( $f_id ),
			esc_attr( $zip ) . $req_label
		);
		$input_zip = sprintf(
			'<input %stype="text" class="ctct-text ctct-address-zip %s" name="zip_%s" id="zip_%s" value="%s">',
			$req,
			esc_attr( $label_placement_class ),
			esc_attr( $f_id ),
			esc_attr( $f_id ),
			esc_attr( $v_zip )
		);

		$input_zip_whole = '';
		if ( 'top' === $label_placement || 'left' === $label_placement || 'hidden' === $label_placement ) {
			$input_zip_whole = $label_zip . $input_zip;
		}
		if ( 'bottom' === $label_placement || 'right' === $label_placement ) {
			$input_zip_whole = $input_zip . $label_zip;
		}

		// Build our field.
		$return  = '<fieldset class="ctct-address"><legend>%s</legend>';
		$return .= '<div class="ctct-form-field ctct-field-full address-line-1%s">%s</div>';
		$return .= '<div class="ctct-form-field ctct-field-full address-line-2%s" id="input_2_1_2_container">%s</div>';
		$return .= '<div class="ctct-form-field ctct-field-third address-city%s" id="input_2_1_3_container">%s</div>';
		$return .= '<div class="ctct-form-field ctct-field-third address-state%s" id="input_2_1_4_container">%s</div>';
		$return .= '<div class="ctct-form-field ctct-field-third address-zip%s" id="input_2_1_5_container">%s</div>';
		$return .= '</fieldset>';

		return sprintf(
			$return,
			esc_html( $name ),
			$req_class,
			$input_street1_whole,
			$req_class,
			$input_street2_whole,
			$req_class,
			$input_city_whole,
			$req_class,
			$input_state_whole,
			$req_class,
			$input_zip_whole
		);
	}

	/**
	 * Gets and return a 3-part date selector.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $name        Name of field.
	 * @param string  $f_id        Field ID.
	 * @param array   $value       Values to pre-fill.
	 * @param string  $desc        Description of fields.
	 * @param boolean $req         If is required.
	 * @param string  $field_error Field error text.
	 * @return string Fields HTML markup.
	 */
	public function dates( $name = '', $f_id = '', $value = array(), $desc = '', $req = false, $field_error = '' ) {

		// Set our field lables.
		$month = __( 'Month', 'constant-contact-forms' );
		$day   = __( 'Day', 'constant-contact-forms' );
		$year  = __( 'Year', 'constant-contact-forms' );

		// @TODO these need to get set correctly.
		// Set our values.
		$v_month = isset( $value['month'] ) ? $value['month'] : '';
		$v_day   = isset( $value['day'] ) ? $value['day'] : '';
		$v_year  = isset( $value['year'] ) ? $value['year'] : '';

		$req_class = $req ? ' ctct-form-field-required ' : '';

		// Build our field.
		$return  = '<p class="ctct-date"><fieldset>';
		$return .= ' <legend>' . esc_attr( $name ) . '</legend>';
		$return .= ' <div class="ctct-form-field ctct-field-inline month' . $req_class . '">';
		$return .= $this->get_date_dropdown( $month, $f_id, 'month', $v_month, $req );
		$return .= ' </div>';
		$return .= ' <div class="ctct-form-field ctct-field-inline day' . $req_class . '">';
		$return .= $this->get_date_dropdown( $day, $f_id, 'day', $v_day, $req );
		$return .= ' </div>';
		$return .= ' <div class="ctct-form-field ctct-field-inline year' . $req_class . '">';
		$return .= $this->get_date_dropdown( $year, $f_id, 'year', $v_year, $req );
		$return .= ' </div>';

		$return .= '</fieldset></p>';

		return $return;
	}

	/**
	 * Gets actual dropdowns for date selector.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $text           Text for default option.
	 * @param string  $f_id           Field ID.
	 * @param string  $type           Type of dropdown (day, month, year).
	 * @param string  $selected_value Previous value.
	 * @param boolean $req            If is require.
	 * @return string field markup.
	 */
	public function get_date_dropdown( $text = '', $f_id = '', $type = '', $selected_value = '', $req = false ) {

		// Account for our weird IDs.
		$f_id = str_replace( 'birthday', 'birthday_' . $type, $f_id );
		$f_id = str_replace( 'anniversary', 'anniversary_' . $type, $f_id );

		$return = '<select name="' . esc_attr( $f_id ) . '" class="ctct-date-select ctct-date-select-' . esc_attr( $type ) . '">';

		if ( $req ) {
			$return = str_replace( '">', '" required>', $return );
		}

		// Grab all of our options based on the field type.
		$return .= $this->get_date_options( $text, $this->get_date_values( $type ), $selected_value );

		$return .= '</select>';

		return $return;
	}

	/**
	 * Gets option markup for a date selector.
	 *
	 * @since 1.0.0
	 *
	 * @param string $text                 Default first option.
	 * @param array  $values               Values to use.
	 * @param array  $prev_selected_values Previous selected values.
	 * @return string HTML markup.
	 */
	public function get_date_options( $text = '', $values = array(), $prev_selected_values = array() ) {

		$return = '<option value="">' . sanitize_text_field( $text ) . '</option>';

		if ( ! is_array( $values ) ) {
			return $return;
		}

		foreach ( $values as $key => $value ) {

			$key = sanitize_text_field( isset( $key ) ? $key : '' );

			$value = sanitize_text_field( isset( $value ) ? $value : '' );

			$return .= '<option value="' . $key . '">' . $value . '</option>';
		}

		return $return;
	}

	/**
	 * Gets array of data for a date dropdown type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Day, month, or year.
	 * @return array Array of data.
	 */
	public function get_date_values( $type ) {
		$return = array();

		// Based on $type, we'll send back an array of either days, months, or years.
		switch ( $type ) {
			case 'day':

				/**
				 * Filters the array of numbers used to indicate day of the month in numerals.
				 *
				 * @since 1.0.0
				 *
				 * @param array $value Array of numbers ranging from 1 to 31.
				 */
				$return = apply_filters( 'constant_contact_dates_day', $this->get_days() );
				break;
			case 'month':

				/**
				 * Filters the array of months used for dropdown.
				 *
				 * @since 1.0.0
				 *
				 * @param array $value Array of months from calendar.
				 */
				$return = apply_filters( 'constant_contact_dates_month', array(
					'january'   => __( 'January', 'constant-contact-forms' ),
					'february'  => __( 'February', 'constant-contact-forms' ),
					'march'     => __( 'March', 'constant-contact-forms' ),
					'april'     => __( 'April', 'constant-contact-forms' ),
					'may'       => __( 'May', 'constant-contact-forms' ),
					'june'      => __( 'June', 'constant-contact-forms' ),
					'july '     => __( 'July ', 'constant-contact-forms' ),
					'august'    => __( 'August', 'constant-contact-forms' ),
					'september' => __( 'September', 'constant-contact-forms' ),
					'october'   => __( 'October', 'constant-contact-forms' ),
					'november'  => __( 'November', 'constant-contact-forms' ),
					'december'  => __( 'December', 'constant-contact-forms' ),
				) );
				break;
			case 'year':

				/**
				 * Filters the array of years, starting from 1910 to present.
				 *
				 * @since 1.0.0
				 *
				 * @param array $value Array of years.
				 */
				$return = apply_filters( 'constant_contact_dates_year', $this->get_years() );
				break;
		} // End switch().

		return $return;
	}

	/**
	 * Helper method to get all years.
	 *
	 * @since 1.0.0
	 *
	 * @return array Years from 1910-current year.
	 */
	public function get_years() {
		$years = array();

		// Get all of our years.
		$year_range = range( 1910,  date( 'Y' ) );

		$year_range = array_reverse( $year_range );

		// Loop through each of the years we have.
		foreach ( $year_range as $year ) {
			$years[ $year ] = $year;
		}

		return $years;
	}

	/**
	 * Gets array of 1-31.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of days.
	 */
	public function get_days() {
		$days = array();

		// Get all of our day.
		$day_range = range( 1, 31 );

		// Loop through each of the days we have.
		foreach ( $day_range as $day ) {
			$days[ $day ] = $day;
		}

		return $days;
	}

	/**
	 * Displays text area field.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $name            Name of field.
	 * @param string  $map             ID of field.
	 * @param string  $value           Previous value of field.
	 * @param string  $desc            Description/label of field.
	 * @param boolean $req             If is required.
	 * @param string  $field_error     Error from field.
	 * @param string  $extra_attrs     Extra attributes to append.
	 * @param string  $label_placement Where to place the label.
	 * @return string HTML markup.
	 */
	public function textarea( $name = '', $map = '', $value = '', $desc = '', $req = false, $field_error = '', $extra_attrs = '', $label_placement = 'top' ) {

		$classes          = array( 'ctct-form-field' );
		$textarea_classes = array( 'ctct-textarea' );

		// Set our required text.
		$req_text = $req ? 'required' : '';
		if ( $req ) {
			$classes[] = 'ctct-form-field-required';
		}
		$label_placement_class = 'ctct-label-' . $label_placement;
		$textarea_classes[]    = $label_placement_class;

		// If required, get our label.
		$req_label = '';
		if ( $req ) {

			/**
			 * Filters the markup used for the required indicator.
			 *
			 * @since 1.0.0
			 *
			 * @param string $value An `<abbr>` tag with an asterisk indicating required status.
			 */
			$req_label = apply_filters( 'constant_contact_required_label', '<abbr title="required">*</abbr>' );
		}

		$return   = '<p class="' . implode( ' ', $classes ) . '">';
		$label    = '<span class="' . $label_placement_class . '"><label for="' . esc_attr( $map ) . '">' . esc_attr( $name ) . ' ' . $req_label . '</label></span>';
		$textarea = '<textarea class="' . esc_attr( implode( ' ', $textarea_classes ) ) . '" ' . $req_text . ' name="' . esc_attr( $map ) . '" placeholder="' . esc_attr( $desc ) . '" ' . $extra_attrs . '>' . esc_html( $value ) . '</textarea>';

		if ( 'top' === $label_placement || 'left' === $label_placement || 'hidden' === $label_placement ) {
			$return .= $label . $textarea;
		}
		if ( 'right' === $label_placement || 'bottom' === $label_placement ) {
			$return .= $textarea . $label;
		}
		if ( $field_error ) {
			$return .= '<span class="ctct-field-error"><label for="' . esc_attr( $map ) . '">' . esc_attr( __( 'Error: Please correct your entry.', 'constant-contact-forms' ) ) . '</label></span>';
		}

		return $return . '</p>';
	}

	/**
	 * Maybe display the disclosure notice.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form_data Form data.
	 * @return string HTML markup
	 */
	public function maybe_add_disclose_note( $form_data ) {

		$opts = isset( $form_data['options'] ) ? $form_data['options'] : false;

		if ( ! $opts ) {
			return '';
		}

		$optin = isset( $opts['optin'] ) ? $opts['optin'] : false;

		if ( ! $optin ) {
			return '';
		}

		$list = isset( $optin['list'] ) ? $optin['list'] : false;

		if ( ! $list ) {
			return '';
		}

		return $this->get_disclose_text();
	}

	/**
	 * Get our disclose markup.
	 *
	 * @since 1.0.0
	 *
	 * @return string HTML markup.
	 */
	public function get_disclose_text() {

		/**
		 * Filters the content used to display the disclose text.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value HTML and disclose text.
		 */
		return apply_filters( 'constant_contact_disclose', '<div class="ctct-disclosure"><hr><sub>' . $this->get_inner_disclose_text() . '</sub></div>' );
	}

	/**
	 * Get our disclose text.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_inner_disclose_text() {
		// translators: placeholder will hold company info for site owner.
		return sprintf(
			__(
				'By submitting this form, you are consenting to receive marketing emails from: %s. You can revoke your consent to receive emails at any time by using the SafeUnsubscribe&reg; link, found at the bottom of every email. %s', 'constant-contact-forms'
			),
			$this->plugin->api->get_disclosure_info(),
			sprintf(
				'<a href="%s" target="_blank">%s</a>',
				esc_url( 'https://www.constantcontact.com/legal/service-provider' ),
				esc_html__( 'Emails are serviced by Constant Contact', 'constant-contact-forms' )
			)
		);
	}

	public function get_max_length_attr( $optional_label = '' ) {
		$length       = 48; // Two less than 50char custom field limit for ": "
		$label_length = 0;
		if ( ! empty( $optional_label ) ) {
			$label_length = mb_strlen( $optional_label );
		}
		if ( absint( $label_length ) > 0 ) {
			$length = $length - $label_length;
		}
		return 'maxlength="' . $length . '"';
	}


}


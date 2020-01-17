<?php
/**
 * Display.
 *
 * @package ConstantContact
 * @subpackage Display
 * @author Constant Contact
 * @since 1.0.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Powers displaying our forms to the front end, generating field markup, and output.
 *
 * @since 1.0.0
 */
class ConstantContact_Display {

	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	protected $plugin;

	/**
	 * The global custom styles.
	 *
	 * @since 1.4.0
	 * @var array
	 */
	protected $global_form_styles = [];

	/**
	 * Styles set for a particular form.
	 *
	 * @since 1.4.0
	 * @var array
	 */
	protected $specific_form_styles = [];

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $plugin Parent plugin.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'styles' ] );
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
		$debug  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true );
		$suffix = ( true === $debug ) ? '' : '.min';

		wp_register_script(
			'ctct_frontend_forms',
			constant_contact()->url() . 'assets/js/ctct-plugin-frontend' . $suffix . '.js',
			[ 'jquery' ],
			Constant_Contact::VERSION,
			true
		);

		$recaptcha_base       = new ConstantContact_reCAPTCHA();
		$version              = $recaptcha_base->get_recaptcha_version();
		$version              = $version ?: 'v2';
		$recaptcha_class_name = "ConstantContact_reCAPTCHA_{$version}";

		$recaptcha = new $recaptcha_class_name();
		$recaptcha->enqueue_scripts();

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
	 */
	public function set_global_form_css() {
		$defaults = [
			'global_form_classes'    => '',
			'global_label_placement' => '',
		];

		$global_form_css = [];

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
	 */
	public function set_specific_form_css( $form_id ) {
		$defaults = [
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
		];

		$specific_form_css = [];

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
	 * Set inline title styles.
	 *
	 * @since 1.5.0
	 *
	 * @return string $title_styles The title styles.
	 */
	private function set_title_styles() {
		$title_styles = '';

		if ( ! empty( $this->specific_form_styles['form_description_color'] ) ) {
			$title_styles .= ' style="' . esc_attr( $this->specific_form_styles['form_description_color'] ) . '"';
		}

		return $title_styles;
	}

	/**
	 * Generate the form title.
	 *
	 * @since 1.5.0
	 *
	 * @param bool $show_title If true, create title markup.
	 * @param int  $form_id The form id.
	 * @return string The form title.
	 */
	private function set_form_title( $show_title, $form_id ) {
		if ( ! $show_title ) {
			return '';
		}

		$title_styles = $this->set_title_styles();

		return '<h3' . $title_styles . '>' . esc_html( get_the_title( $form_id ) ) . '</h3>';
	}

	/**
	 * Main wrapper for getting our form display.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $form_data   Array of form data.
	 * @param string $form_id     Form ID.
	 * @param bool   $show_title  Show title if true.
	 * @return string Form markup.
	 */
	public function form( $form_data, $form_id = '', $show_title = false ) {
		if ( 'publish' !== get_post_status( $form_id ) ) {
			return '';
		}

		$this->set_global_form_css();
		$this->set_specific_form_css( $form_id );

		$return           = '';
		$form_err_display = '';
		$error_message    = false;
		$status           = false;
		$form_title       = $this->set_form_title( $show_title, $form_id );

		// Get a potential response from our processing wrapper
		// This returns an array that has 'status' and 'message keys'
		// if the status is success, then we sent the form correctly
		// if the status is error, then we will re-show the form, but also
		// with our error messages.
		$response = constant_contact()->process_form->process_wrapper( $form_data, $form_id );

		$old_values = isset( $response['values'] ) ? $response['values'] : '';
		$req_errors = isset( $response['errors'] ) ? $response['errors'] : '';

		if ( $response && isset( $response['message'] ) && isset( $response['status'] ) ) {

			if ( 'success' === $response['status'] ) {
				return $this->message( 'success', $response['message'] );
			} else {

				// If we didn't get a success message, then we want to error.
				// We already checked for a message response, but we'll force the
				// status to error if we're not here.
				$status        = 'error';
				$error_message = trim( $response['message'] );
			}
		}

		if ( 'error' === $status || $error_message ) {
			if ( ! empty( $error_message ) ) {
				$form_err_display = $this->message( 'error', $error_message );
			}
		}

		$rf_id   = 'ctct-form-' . wp_rand();
		$return .= $form_title;

		/**
		 * Filters the action value to use for the contact form.
		 *
		 * @since 1.1.1
		 *
		 * @param string $value   Value to put in the form action attribute. Default empty string.
		 * @param int    $form_id ID of the Constant Contact form being rendered.
		 */
		$form_action              = apply_filters( 'constant_contact_front_form_action', '', $form_id );
		$should_do_ajax           = get_post_meta( $form_id, '_ctct_do_ajax', true );
		$do_ajax                  = ( 'on' === $should_do_ajax ) ? $should_do_ajax : 'off';
		$should_disable_recaptcha = get_post_meta( $form_id, '_ctct_disable_recaptcha', true );
		$disable_recaptcha        = 'on' === $should_disable_recaptcha;
		$form_classes             = 'ctct-form ctct-form-' . $form_id;
		$form_classes            .= ConstantContact_reCAPTCHA::has_recaptcha_keys() ? ' has-recaptcha' : ' no-recaptcha';
		$form_classes            .= $this->build_custom_form_classes();

		$form_styles = '';
		if ( ! empty( $this->specific_form_styles['form_background_color'] ) ) {
			$form_styles = $this->specific_form_styles['form_background_color'];
		}

		foreach ( [ 'bottom', 'left', 'right', 'top' ] as $pos ) {
			$form_styles .= $this->specific_form_styles[ 'form_padding_' . $pos ];
		}

		ob_start();
		/**
		 * Fires before the start of the form tag.
		 *
		 * @since 1.4.0
		 *
		 * @param int $form_id Current form ID.
		 */
		do_action( 'ctct_before_form', $form_id );
		$return .= ob_get_clean();

		$return .= '<form class="' . esc_attr( $form_classes ) . '" id="' . $rf_id . '" ';
		$return .= 'data-doajax="' . esc_attr( $do_ajax ) . '" ';
		$return .= 'style="' . esc_attr( $form_styles ) . '" ';
		$return .= 'action="' . esc_attr( $form_action ) . '" ';
		$return .= 'method="post">';

		$return .= $form_err_display;

		$return .= $this->build_form_fields( $form_data, $old_values, $req_errors );

		if ( ! $disable_recaptcha && ConstantContact_reCAPTCHA::has_recaptcha_keys() ) {
			$recaptcha_version = ctct_get_settings_option( '_ctct_recaptcha_version', '' );
			if ( 'v2' === $recaptcha_version ) {
				$return .= $this->build_recaptcha( $form_id );
			}
		}

		$return .= $this->build_honeypot_field();

		$return .= $this->add_verify_fields( $form_data );

		$return .= $this->build_timestamp();

		$return .= $this->submit( $form_id );

		$return .= wp_nonce_field( 'ctct_submit_form', 'ctct_form', true, false );

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
		$return .= ob_get_clean();

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
		global $wp;

		$request = ( isset( $wp->request ) && $wp->request ) ? $wp->request : null;

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

		return untrailingslashit( home_url( add_query_arg( [ '' => '' ] ) ) );
	}

	/**
	 * Adds hidden input fields to our form for form id and verify id.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form_data Form data for the current form.
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
		$return  = '';
		$form_id = absint( $form_data['options']['form_id'] );

		if ( isset( $form_data['options'] ) && isset( $form_data['options']['form_id'] ) ) {
			$desc = isset( $form_data['options']['description'] ) ? $form_data['options']['description'] : '';

			$return .= $this->description( $desc, $form_id );

		}

		$label_placement = constant_contact_get_css_customization( $form_id, '_ctct_form_label_placement' );
		if ( empty( $label_placement ) ) {
			$label_placement = 'top';
		}

		if ( isset( $form_data['fields'] ) && is_array( $form_data['fields'] ) ) {
			foreach ( $form_data['fields'] as $key => $value ) {
				$return .= $this->field( $value, $old_values, $req_errors, $form_id, $label_placement );
			}
		}

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
		return sprintf(
			'<div id="ctct_usage"><label for="ctct_usage_field">%s</label><input type="text" value="" name="ctct_usage_field" id="ctct_usage_field" /></div>',
			esc_html__( 'Constant Contact Use.', 'constant-contact-forms' )
		);
	}

	/**
	 * Display a Google reCAPTCHA field.
	 *
	 * This method is dedicated for the version 2 "I am human" style.
	 *
	 * @since 1.2.4
	 *
	 * @param int $form_id ID of form being rendered.
	 * @return string
	 */
	public function build_recaptcha( $form_id ) {
		$recaptcha = new ConstantContact_reCAPTCHA_v2();

		$recaptcha->set_recaptcha_keys();

		$recaptcha->set_size(
			/**
			 * Filters the reCAPTCHA size to render.
			 *
			 * @since 1.7.0
			 *
			 * @param string $value Size to render. Options: `normal`, `compact`. Default `normal`.
			 */
			apply_filters( 'constant_contact_recaptcha_size', 'normal', $form_id )
		);

		/**
		 * Filters the language code to be used with Google reCAPTCHA.
		 *
		 * See https://developers.google.com/recaptcha/docs/language for available values.
		 *
		 * @since 1.2.4
		 * @since 1.7.0 Added form ID for conditional amending.
		 *
		 * @param string $value   Language code to use. Default 'en'.
		 * @param int    $form_id ID of the form being rendered.
		 */
		$recaptcha->set_language( apply_filters( 'constant_contact_recaptcha_lang', 'en', $form_id ) );

		// phpcs:disable WordPress.WP.EnqueuedResources -- Okay use of inline script.
		$return = $recaptcha->get_inline_markup();
		// phpcs:enable WordPress.WP.EnqueuedResources

		return $return;
	}

	/**
	 * Render a hidden input field storing the current time.
	 *
	 * @since 1.2.4
	 *
	 * @return string
	 */
	public function build_timestamp() {
		return '<input type="hidden" name="ctct_time" value="' . current_time( 'timestamp' ) . '" />';
	}

	/**
	 * Add custom CSS classes to the form.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
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
	 * @param array $form_data Options for the form.
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
	public function field( $field, $old_values = [], $req_errors = [], $form_id = 0, $label_placement = 'top' ) {
		if ( ! isset( $field['name'] ) || ! isset( $field['map_to'] ) ) {
			return '';
		}

		$field = wp_parse_args( $field, [
			'name'             => '',
			'map_to'           => '',
			'type'             => '',
			'description'      => '',
			'field_custom_css' => [],
			'required'         => false,
		] );

		$name  = sanitize_text_field( $field['name'] );
		$map   = sanitize_text_field( $field['map_to'] );
		$desc  = sanitize_text_field( isset( $field['description'] ) ? $field['description'] : '' );
		$type  = sanitize_text_field( isset( $field['type'] ) ? $field['type'] : 'text_field' );
		$value = sanitize_text_field( isset( $field['value'] ) ? $field['value'] : false );
		$req   = isset( $field['required'] ) ? $field['required'] : false;

		// phpcs:disable WordPress.PHP.DiscouragedPHPFunctions -- Okay use of serialize() here.
		if ( 'submit' !== $type ) {
			$temp_field = $field;
			unset( $temp_field['field_custom_css'] );
			$map = $map . '___' . md5( serialize( $temp_field ) );
		}
		// phpcs:enable WordPress.PHP.DiscouragedPHPFunctions

		$field_error = false;

		if ( ! empty( $req_errors ) ) {

			foreach ( $req_errors as $error ) {

				if ( isset( $error['id'] ) && isset( $error['error'] ) ) {

					if ( $map === $error['id'] ) {

						$field_error = '<span class="ctct-field-error">';

						if ( 'invalid' === $error['error'] ) {
							$field_error .= esc_html__( 'Error: Please correct your entry.', 'constant-contact-forms' );
						} else {
							$field_error .= esc_html__( ' Error: Please fill out this field.', 'constant-contact-forms' );
						}

						$field_error .= '</span>';
					}
				}
			}
		}

		$value = $this->get_submitted_value( $value, $map, $field, $old_values );

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
			case 'custom_text_area':
				return $this->textarea( $name, $map, $value, $desc, $req, $field_error, 'maxlength="500"', $label_placement );
			case 'email':
				return $this->input( 'email', $name, $map, $value, $desc, $req, false, $field_error, $form_id, $label_placement );
			case 'hidden':
				return $this->input( 'hidden', $name, $map, $value, $desc, $req );
			case 'checkbox':
				return $this->checkbox( $name, $map, $value, $desc );
			case 'submit':
				return $this->input( 'submit', $name, $map, $value, $desc, $req, false, $field_error );
			case 'address':
				return $this->address( $name, $map, $value, $desc, $req, $field_error, $label_placement );
			case 'anniversery':
			case 'birthday':
				// Need this to be month / day / year.
				return $this->dates( $name, $map, $value, $desc, $req, $field_error );
			default:
				return $this->input( 'text', $name, $map, $value, $desc, $req, false, $field_error );
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
	 * @return mixed Submitted value.
	 */
	public function get_submitted_value( $value = '', $map = '', $field = [], $submitted_vals = [] ) {
		if ( $value ) {
			return $value;
		}

		if ( ! is_array( $submitted_vals ) ) {
			return '';
		}

		$return = [];

		foreach ( $submitted_vals as $post ) {

			if ( isset( $post['key'] ) && $post['key'] ) {

				if ( 'address' === $field['name'] ) {

					if ( strpos( $post['key'], '_address___' ) !== false ) {

						$addr_key = explode( '___', $post['key'] );

						if ( isset( $addr_key[0] ) && $addr_key[0] ) {

							$post_key = '';

							// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification -- Okay accessing of $_POST value.
							if ( isset( $_POST[ esc_attr( $post['key'] ) ] ) ) {
								$post_key = sanitize_text_field( wp_unslash( $_POST[ esc_attr( $post['key'] ) ] ) );
							}
							// phpcs:enable WordPress.Security.NonceVerification.NoNonceVerification

							$return[ esc_attr( $addr_key[0] ) ] = $post_key;
						}
					}
				// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification -- Okay accessing of $_POST value.
				} elseif ( $post['key'] === $map && isset( $_POST[ esc_attr( $map ) ] ) ) {
					return sanitize_text_field( wp_unslash( $_POST[ esc_attr( $map ) ] ) );
				}
				// phpcs:enable WordPress.Security.NonceVerification.NoNonceVerification
			}
		}

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
		$role = ( 'error' === $type ) ? ' role="alert"' : '';

		return sprintf(
			'<p class="ctct-message %s"%s>%s</p>',
			esc_attr( $type ),
			$role,
			esc_html( $message )
		);
	}

	/**
	 * Get an inline style tag to use for the form's description.
	 *
	 * @since 1.4.0
	 *
	 * @return string The inline style tag for the form's description.
	 */
	public function get_description_inline_styles() {
		$inline_style = '';
		$styles       = [];

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

		if ( $form_id && current_user_can( 'edit_posts' ) ) {

			$edit_link = get_edit_post_link( absint( $form_id ) );

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

		$classes = [
			'ctct-form-field',
			'ctct-form-field-' . $type,
		];
		if ( $req ) {
			$classes[] = 'ctct-form-field-required';
		}

		$markup = '<p class="' . implode( ' ', $classes ) . '">';

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

		return $markup . '</p>';
	}

	/**
	 * Get inline styles for the form's submit button.
	 *
	 * @since 1.4.0
	 *
	 * @return string
	 */
	public function get_submit_inline_styles() {
		$inline_style = '';
		$styles       = [];

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
		$name                  = sanitize_text_field( $name );
		$f_id                  = sanitize_title( $id );
		$input_inline_styles   = '';
		$label_placement_class = 'ctct-label-' . $label_placement;
		$specific_form_styles  = $this->specific_form_styles;
		$inline_font_styles    = $this->get_inline_font_color();

		if ( 'submit' === $type ) {
			$input_inline_styles = $this->get_submit_inline_styles();
		}

		$type     = sanitize_text_field( $type );
		$value    = sanitize_text_field( $value );
		$label    = sanitize_text_field( $label );
		$req_text = $req ? 'required' : '';

		$markup = $this->field_top( $type, $name, $f_id, $label, $req );

		$req_label = '';

		if ( $req ) {
			$req_label = $this->display_required_indicator();
		}
		if ( ( 'top' === $label_placement || 'left' === $label_placement || 'hidden' === $label_placement ) && ( 'submit' !== $type ) && ( 'hidden' !== $type ) ) {
			if ( $inline_font_styles ) {
				$markup .= '<span class="' . $label_placement_class . '"  style="' . $inline_font_styles . '">';
			} else {
				$markup .= '<span class="' . $label_placement_class . '">';
			}
			$markup .= $this->get_label( $f_id, $name . ' ' . $req_label );
			$markup .= '</span>';
		}

		$classes   = [ 'ctct-' . esc_attr( $type ) ];
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
		 * @param  int    $form_id Form ID.
		 * @param  int    $f_id    Field ID.
		 * @return array
		 */
		$classes = apply_filters( 'constant_contact_input_classes', $classes, $type, $form_id, $f_id );

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
			$max_length = $truncate_max_length ? $this->get_max_length_attr( $name ) : $this->get_max_length_attr();
		}

		if ( $field_error ) {
			$classes[] = 'ctct-invalid';
		}

		$class_attr = '';

		if ( count( $classes ) ) {
			$class_attr = 'class="' . implode( ' ', $classes ) . '"';
		}

		$field   = '<input %s type="%s" name="%s" id="%s" %s value="%s" %s placeholder="%s" %s />';
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

		// Reassign because if we want "field only", like for hidden inputs, we need to still pass a value that went through sprintf().
		$field = $markup;

		if ( ( 'bottom' === $label_placement || 'right' === $label_placement ) && ( 'submit' !== $type ) && ( 'hidden' !== $type ) ) {
			$markup .= '<span class="' . $label_placement_class . '">';
			$markup .= $this->get_label( $f_id, $name . ' ' . $req_label );
			$markup .= '</span>';
		}

		if ( $field_error ) {
			$markup .= $this->field_bottom( $id, $field_error );
		} else {
			$markup .= $this->field_bottom();
		}

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
		$type  = 'checkbox';

		$classes = [ 'ctct-' . esc_attr( $type ) ];

		/**
		 * Filter to add classes for the rendering input.
		 *
		 * @since  1.2.0
		 * @param  array  $classes Array of classes to apply to the field.
		 * @param  string $type    The field type being rendered.
		 * @return array
		 */
		$classes = apply_filters( 'constant_contact_input_classes', $classes, $type ); // @todo if/when we start using the checkbox field type, pass in a $form_id and $f_id value.

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
		! empty( $button_text ) ?
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

		return $this->field( [
			'type'   => 'submit',
			'name'   => 'ctct-submitted',
			'map_to' => 'ctct-submitted',
			'value'  => $button_text,
		] );
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

		if ( ! isset( $form_data['optin'] ) ) {
			return '';
		}

		$optin = wp_parse_args( $form_data['optin'], [
			'list'         => false,
			'show'         => false,
			'instructions' => '',
		] );

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

		$label = sanitize_text_field( isset( $optin['instructions'] ) ? $optin['instructions'] : '' );
		$value = sanitize_text_field( isset( $optin['list'] ) ? $optin['list'] : '' );

		$show = false;
		if ( isset( $optin['show'] ) && 'on' === $optin['show'] ) {
			$show = true;
		}

		$markup = '';

		if ( ! $show ) {
			$markup = '<div class="ctct-optin-hide" style="display:none;">';
		}

		$markup .= $this->get_optin_markup( $label, $value, $show );

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
	public function address( $name = '', $f_id = '', $value = [], $desc = '', $req = false, $field_error = '', $label_placement = 'top' ) {
		$street = esc_html__( 'Street Address', 'constant-contact-forms' );
		$line_2 = esc_html__( 'Address Line 2', 'constant-contact-forms' );
		$city   = esc_html__( 'City', 'constant-contact-forms' );
		$state  = esc_html__( 'State', 'constant-contact-forms' );
		$zip    = esc_html__( 'ZIP Code', 'constant-contact-forms' );

		$v_street = isset( $value['street_address'] ) ? $value['street_address'] : '';
		$v_line_2 = isset( $value['line_2_address'] ) ? $value['line_2_address'] : '';
		$v_city   = isset( $value['city_address'] ) ? $value['city_address'] : '';
		$v_state  = isset( $value['state_address'] ) ? $value['state_address'] : '';
		$v_zip    = isset( $value['zip_address'] ) ? $value['zip'] : '';

		$req_label             = $req ? ' ' . $this->display_required_indicator() : '';
		$req_class             = $req ? ' ctct-form-field-required ' : '';
		$req                   = $req ? ' required ' : '';
		$label_placement_class = 'ctct-label-' . $label_placement;
		$inline_font_styles    = $this->get_inline_font_color();

		$label_street1 = sprintf(
			'<span class="%1$s"><label for="street_%2$s" style="%3$s">%4$s</label></span>',
			esc_attr( $label_placement_class ),
			esc_attr( $f_id ),
			esc_attr( $inline_font_styles ),
			esc_attr( $street ) . $req_label
		);
		$input_street1 = sprintf(
			'<input %1$stype="text" class="ctct-text ctct-address-street %2$s" name="street_%3$s" id="street_%4$s" value="%5$s">',
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
			'<span class="%1$s"><label for="line_2_%2$s" style="%3$s">%4$s</label></span>',
			$label_placement_class,
			esc_attr( $f_id ),
			esc_attr( $inline_font_styles ),
			esc_attr( $line_2 )
		);

		$input_street2 = sprintf(
			'<input type="text" class="ctct-text ctct-address-line-2 %1$s" name="line_2_%2$s" id="line_2_%3$s" value="%4$s">',
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
			'<span class="%1$s"><label for="city_%2$s" style="%3$s">%4$s</label></span>',
			$label_placement_class,
			esc_attr( $f_id ),
			esc_attr( $inline_font_styles ),
			esc_attr( $city ) . $req_label
		);

		$input_city = sprintf(
			'<input %1$stype="text" class="ctct-text ctct-address-city %2$s" name="city_%3$s" id="city_%4$s" value="%5$s">',
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
			'<span class="%1$s"><label for="state_%2$s" style="%3$s">%4$s</label></span>',
			$label_placement_class,
			esc_attr( $f_id ),
			esc_attr( $inline_font_styles ),
			esc_attr( $state ) . $req_label
		);

		$input_state = sprintf(
			'<input %1$stype="text" class="ctct-text ctct-address-state %2$s" name="state_%3$s" id="state_%4$s" value="%5$s">',
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
			'<span class="%1$s"><label for="zip_%2$s" style="%3$s">%4$s</label></span>',
			$label_placement_class,
			esc_attr( $f_id ),
			esc_attr( $inline_font_styles ),
			esc_attr( $zip ) . $req_label
		);

		$input_zip = sprintf(
			'<input %1$stype="text" class="ctct-text ctct-address-zip %2$s" name="zip_%3$s" id="zip_%4$s" value="%5$s">',
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

		$return  = '<fieldset class="ctct-address"><legend style="%s">%s</legend>';
		$return .= '<div class="ctct-form-field ctct-field-full address-line-1%s">%s</div>';
		$return .= '<div class="ctct-form-field ctct-field-full address-line-2%s" id="input_2_1_2_container">%s</div>';
		$return .= '<div class="ctct-form-field ctct-field-third address-city%s" id="input_2_1_3_container">%s</div>';
		$return .= '<div class="ctct-form-field ctct-field-third address-state%s" id="input_2_1_4_container">%s</div>';
		$return .= '<div class="ctct-form-field ctct-field-third address-zip%s" id="input_2_1_5_container">%s</div>';
		$return .= '</fieldset>';

		return sprintf(
			$return,
			esc_attr( $inline_font_styles ),
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
	public function dates( $name = '', $f_id = '', $value = [], $desc = '', $req = false, $field_error = '' ) {
		$month = esc_html__( 'Month', 'constant-contact-forms' );
		$day   = esc_html__( 'Day', 'constant-contact-forms' );
		$year  = esc_html__( 'Year', 'constant-contact-forms' );

		$v_month = isset( $value['month'] ) ? $value['month'] : '';
		$v_day   = isset( $value['day'] ) ? $value['day'] : '';
		$v_year  = isset( $value['year'] ) ? $value['year'] : '';

		$req_class = $req ? ' ctct-form-field-required ' : '';

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
		$f_id = str_replace( 'birthday', 'birthday_' . $type, $f_id );
		$f_id = str_replace( 'anniversary', 'anniversary_' . $type, $f_id );

		$return = '<select name="' . esc_attr( $f_id ) . '" class="ctct-date-select ctct-date-select-' . esc_attr( $type ) . '">';

		if ( $req ) {
			$return = str_replace( '">', '" required>', $return );
		}

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
	public function get_date_options( $text = '', $values = [], $prev_selected_values = [] ) {
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
		$return = [];

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
				$return = apply_filters( 'constant_contact_dates_month', [
					'january'   => esc_html__( 'January', 'constant-contact-forms' ),
					'february'  => esc_html__( 'February', 'constant-contact-forms' ),
					'march'     => esc_html__( 'March', 'constant-contact-forms' ),
					'april'     => esc_html__( 'April', 'constant-contact-forms' ),
					'may'       => esc_html__( 'May', 'constant-contact-forms' ),
					'june'      => esc_html__( 'June', 'constant-contact-forms' ),
					'july '     => esc_html__( 'July ', 'constant-contact-forms' ),
					'august'    => esc_html__( 'August', 'constant-contact-forms' ),
					'september' => esc_html__( 'September', 'constant-contact-forms' ),
					'october'   => esc_html__( 'October', 'constant-contact-forms' ),
					'november'  => esc_html__( 'November', 'constant-contact-forms' ),
					'december'  => esc_html__( 'December', 'constant-contact-forms' ),
				] );
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
		}

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
		$years      = [];
		$year_range = range( 1910, date( 'Y' ) );
		$year_range = array_reverse( $year_range );

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
		$days      = [];
		$day_range = range( 1, 31 );

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

		$classes          = [ 'ctct-form-field' ];
		$textarea_classes = [ 'ctct-textarea' ];

		$req_text = $req ? 'required' : '';

		if ( $req ) {
			$classes[] = 'ctct-form-field-required';
		}

		$label_placement_class = 'ctct-label-' . $label_placement;
		$textarea_classes[]    = $label_placement_class;

		$req_label = '';

		if ( $req ) {
			$req_label = $this->display_required_indicator();
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
		return apply_filters(
			'constant_contact_disclose',
			sprintf(
				'<div class="ctct-disclosure" style="%s"><hr><sub>%s</sub></div>',
				esc_attr( $this->get_inline_font_color() ),
				$this->get_inner_disclose_text() ) );
	}

	/**
	 * Get our disclose text.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_inner_disclose_text() {
		return sprintf(
			// Translators: placeholder will hold company info for site owner.
			__(
				'By submitting this form, you are consenting to receive marketing emails from: %1$s. You can revoke your consent to receive emails at any time by using the SafeUnsubscribe&reg; link, found at the bottom of every email. %2$s', 'constant-contact-forms'
			),
			$this->plugin->api->get_disclosure_info(),
			sprintf(
				'<a href="%s" target="_blank">%s</a>',
				esc_url( 'https://www.constantcontact.com/legal/service-provider' ),
				esc_html__( 'Emails are serviced by Constant Contact', 'constant-contact-forms' )
			)
		);
	}

	/**
	 * Get markup for the "maxlength" attribute to add to some text inputs.
	 *
	 * @since 1.0.0
	 *
	 * @param string $optional_label Optional label.
	 * @return string
	 */
	public function get_max_length_attr( $optional_label = '' ) {
		$length       = 48; // Two less than 50char custom field limit for ": ".
		$label_length = 0;

		if ( ! empty( $optional_label ) ) {
			$label_length = mb_strlen( $optional_label );
		}

		if ( absint( $label_length ) > 0 ) {
			$length = $length - $label_length;
		}

		return 'maxlength="' . $length . '"';
	}

	/**
	 * Get the inline font color.
	 *
	 * @since 1.4.3
	 *
	 * @return string
	 */
	private function get_inline_font_color() {
		$inline_font_styles = '';
		if ( ! empty( $this->specific_form_styles['form_description_color'] ) ) {
			$inline_font_styles = $this->specific_form_styles['form_description_color'];
		}

		return $inline_font_styles;
	}

	/**
	 * Display the markup for the required indicator.
	 *
	 * @since 1.0.0
	 *
	 * @return string The required indicator markup.
	 */
	public function display_required_indicator() {
		/**
		 * Filters the markup used for the required indicator.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value An `<abbr>` tag with an asterisk indicating required status.
		 */
		return apply_filters( 'constant_contact_required_label', '<abbr title="required">*</abbr>' );
	}
}

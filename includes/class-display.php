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
	protected object $plugin;

	/**
	 * The global custom styles.
	 *
	 * @since 1.4.0
	 * @var array
	 */
	protected array $global_form_styles = [];

	/**
	 * Styles set for a particular form.
	 *
	 * @since 1.4.0
	 * @var array
	 */
	protected array $specific_form_styles = [];

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $plugin Parent plugin.
	 */
	public function __construct( object $plugin ) {
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
	public function scripts( bool $enqueue = false ) {
		$debug  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true );
		$suffix = ( true === $debug ) ? '' : '.min';

		wp_register_script(
			'ctct_frontend_forms',
			constant_contact()->url() . 'assets/js/ctct-plugin-frontend' . $suffix . '.js',
			[],
			Constant_Contact::VERSION,
			true
		);

		$captcha_service = new ConstantContact_CaptchaService();
		if ( $captcha_service->is_captcha_enabled() ) {
			if  ( 'recaptcha' === $captcha_service->get_selected_captcha_service() ) {
				$recaptcha_base       = new ConstantContact_reCAPTCHA();
				$version              = $recaptcha_base->get_recaptcha_version();
				$version              = $version ?: 'v2';
				$recaptcha_class_name = "ConstantContact_reCAPTCHA_$version";

				$recaptcha = new $recaptcha_class_name();

				/**
				 * Filters the language code to be used with Google reCAPTCHA.
				 * See https://developers.google.com/recaptcha/docs/language for available values.
				 *
				 * @since 1.2.4
				 * @since 1.7.0  Added form ID for conditional amending.
				 * @since 2.10.0 Removed form ID due to changing where we invoke and use language code.
				 *
				 * @param string $value Language code to use. Default 'en'.
				 */
				$recaptcha->set_language( apply_filters( 'constant_contact_recaptcha_lang', 'en' ) );
				$recaptcha->enqueue_scripts();
			} elseif ( 'hcaptcha' === $captcha_service->get_selected_captcha_service() ) {
				$hcaptcha = new ConstantContact_hCaptcha();
				$hcaptcha->enqueue_scripts();
			}
		}

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
	public function styles( bool $enqueue = false ) {
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

		$global_form_classes = constant_contact_get_option( '_ctct_form_custom_classes' );
		if ( $global_form_classes ) {
			$global_form_css['global_form_classes'] = $global_form_classes;
		}

		$global_label_placement = constant_contact_get_option( 'ctct_form_label_placement' );
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
	public function set_specific_form_css( int $form_id ) {
		$defaults = [
			'form_background_color'               => '',
			'form_description_font_size'          => '',
			'form_max_width'                      => '',
			'form_description_color'              => '',
			'form_submit_button_font_size'        => '',
			'form_submit_button_text_color'       => '',
			'form_submit_button_background_color' => '',
			'form_title_heading_level'            => 'h3',
			'form_padding_top'                    => '',
			'form_padding_right'                  => '',
			'form_padding_bottom'                 => '',
			'form_padding_left'                   => '',
			'input_custom_classes'                => '',
		];

		$specific_form_css = [];

		$ctct_form_background_color = get_post_meta( $form_id, '_ctct_form_background_color', true );
		if ( ! empty( $ctct_form_background_color ) ) {
			$specific_form_css['form_background_color'] = "background-color: $ctct_form_background_color;";
		}

		$ctct_form_title_font_color = get_post_meta( $form_id, '_ctct_form_title_font_color', true );
		if ( ! empty( $ctct_form_title_font_color ) ) {
			$specific_form_css['form_title_font_color'] = "color: $ctct_form_title_font_color;";
		}

		$ctct_form_max_width = get_post_meta( $form_id, '_ctct_form_max_width', true );
		if ( ! empty( $ctct_form_max_width ) ) {
			$specific_form_css['max_width'] = "max-width: $ctct_form_max_width%;";
		}

		$ctct_form_description_font_size = get_post_meta( $form_id, '_ctct_form_description_font_size', true );
		if ( ! empty( $ctct_form_description_font_size ) ) {
			$specific_form_css['form_description_font_size'] = "font-size: $ctct_form_description_font_size;";
		}

		$ctct_form_description_color = get_post_meta( $form_id, '_ctct_form_description_color', true );
		if ( ! empty( $ctct_form_description_color ) ) {
			$specific_form_css['form_description_color'] = "color: $ctct_form_description_color;";
		}

		$ctct_form_submit_button_font_size = get_post_meta( $form_id, '_ctct_form_submit_button_font_size', true );
		if ( ! empty( $ctct_form_submit_button_font_size ) ) {
			$specific_form_css['form_submit_button_font_size'] = "font-size: $ctct_form_submit_button_font_size;";
		}

		$ctct_form_submit_button_text_color = get_post_meta( $form_id, '_ctct_form_submit_button_text_color', true );
		if ( ! empty( $ctct_form_submit_button_text_color ) ) {
			$specific_form_css['form_submit_button_text_color'] = "color: $ctct_form_submit_button_text_color;";
		}

		$ctct_form_submit_button_background_color = get_post_meta( $form_id, '_ctct_form_submit_button_background_color', true );
		if ( ! empty( $ctct_form_submit_button_background_color ) ) {
			$specific_form_css['form_submit_button_background_color'] = "background-color: $ctct_form_submit_button_background_color;";
		}

		$ctct_form_title_heading_level = get_post_meta( $form_id, '_ctct_form_title_heading_level', true );
		if ( ! empty( $ctct_form_title_heading_level ) ) {
			$specific_form_css['form_title_heading_level'] = $ctct_form_title_heading_level;
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
	private function set_title_styles() : string {
		$title_styles = '';

		if ( ! empty( $this->specific_form_styles['form_title_font_color'] ) ) {
			$title_styles .= ' style="' . esc_attr( $this->specific_form_styles['form_title_font_color'] ) . '"';
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
	private function set_form_title( bool $show_title, int $form_id ) : string {
		if ( ! $show_title ) {
			return '';
		}

		$heading_level = $this->specific_form_styles['form_title_heading_level'];
		$title_styles = $this->set_title_styles();

		return sprintf(
			'<%1$s%2$s>%3$s</%4$s>',
			$heading_level,
			$title_styles,
			esc_html( get_the_title( $form_id ) ),
			$heading_level
		);
	}

	/**
	 * Main wrapper for getting our form display.
	 *
	 * @since  1.0.0
	 * @since  1.8.3 Added $instance param to help properly track multiple instances of the same form.
	 *
	 * @throws Exception
	 *
	 * @param  array  $form_data  Array of form data.
	 * @param  string $form_id    Form ID.
	 * @param  bool   $show_title Show title if true.
	 * @param  int    $instance   Current form instance.
	 * @return string Form markup.
	 */
	public function form( array $form_data, string $form_id = '', bool $show_title = false, int $instance = 0 ) : string {
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

		$captcha_service          = new ConstantContact_CaptchaService();
		$selected_captcha_service = $captcha_service->get_selected_captcha_service();

		// Get a potential response from our processing wrapper
		// This returns an array that has 'status' and 'message keys'
		// if the status is success, then we sent the form correctly
		// if the status is error, then we will re-show the form, but also
		// with our error messages.
		$response = constant_contact()->get_process_form()->process_wrapper( $form_data, $form_id, $instance );

		$old_values = $response['values'] ?? [];
		$req_errors = $response['errors'] ?? [];

		if ( $response && isset( $response['message'] ) && isset( $response['status'] ) ) {

			if ( 'success' === $response['status'] ) {
				return $this->message( 'success', $response['message'], 'status' );
			} else {

				// If we didn't get a success message, then we want to error.
				// We already checked for a message response, but we'll force the
				// status to error if we're not here.
				$status        = 'error';
				$error_message = trim( $response['message'] );
			}
		}

		if ( 'error' === $status ) {
			if ( ! empty( $error_message ) ) {
				$form_err_display = $this->message( 'error', $error_message, 'alert' );
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
		$inline_form              = get_post_meta( $form_id, '_ctct_inline_display', true );
		$should_do_ajax           = get_post_meta( $form_id, '_ctct_do_ajax', true );
		$do_ajax                  = ( 'on' === $should_do_ajax ) ? $should_do_ajax : 'off';
		$should_disable_captcha   = get_post_meta( $form_id, '_ctct_disable_recaptcha', true ); // Note: Despite option name, this applies to whatever the enabled captcha service is.
		$disable_captcha          = 'on' === $should_disable_captcha;

		$form_classes             = [ 'ctct-form ctct-form-' . $form_id, 'comment-form' ];

		// TODO?: Rename this to has-captcha/no-captcha?
		$form_classes[] = $captcha_service->is_captcha_enabled() && ! $disable_captcha ? ' has-recaptcha' : ' no-recaptcha';
		$form_classes[] = 'on' === $inline_form ? 'ctct-inline' : 'ctct-default';
		$form_classes = array_merge( $form_classes, $this->build_custom_form_classes() );

		$form_styles = '';
		if ( ! empty( $this->specific_form_styles['form_background_color'] ) ) {
			$form_styles = $this->specific_form_styles['form_background_color'];
		}

		if ( ! empty( $this->specific_form_styles['max_width'] ) ) {
			$form_styles .= $this->specific_form_styles['max_width'];
		}

		foreach ( [ 'bottom', 'left', 'right', 'top' ] as $pos ) {
			$form_styles .= $this->specific_form_styles[ 'form_padding_' . $pos ];
		}

		ob_start();

		/**
		 * Fires before the start of the form tag.
		 *
		 * @deprecated 1.9.0 Deprecated in favor of properly-prefixed hookname.
		 *
		 * @since 1.4.0
		 *
		 * @param int $form_id Current form ID.
		 */
		do_action_deprecated( 'ctct_before_form', [ $form_id ], '1.9.0', 'constant_contact_before_form' );

		/**
		 * Fires before the opening form tag.
		 *
		 * @author Rebekah Van Epps <rebekah.vanepp@webdevstudios.com>
		 * @since  1.9.0
		 *
		 * @param int $form_id Current form ID.
		 */
		do_action( 'constant_contact_before_form', $form_id );

		$return .= ob_get_clean();

		if (
			! empty( $form_data['options']['description'] ) &&
			(
				empty( $form_data['options']['description_visibility'] ) ||
				'on' !== $form_data['options']['description_visibility']
			)
		) {
			$return .= $this->description( $form_data['options']['description'], $form_id );
		}

		$return .= '<form class="' . esc_attr( implode( ' ', $form_classes ) ) . '" id="' . $rf_id . '" ';
		$return .= 'data-doajax="' . esc_attr( $do_ajax ) . '" ';
		$return .= 'style="' . esc_attr( $form_styles ) . '" ';
		$return .= 'action="' . esc_attr( $form_action ) . '" ';
		$return .= 'method="post">';

		$return .= $form_err_display;

		$return .= $this->build_form_fields( $form_data, $old_values, $req_errors, $instance );

		if ( $captcha_service->is_captcha_enabled() && ! $disable_captcha ) {
			if ( 'recaptcha' === $selected_captcha_service ) {
				$recaptcha_version = constant_contact_get_option( '_ctct_recaptcha_version', '' );
				if ( 'v2' === $recaptcha_version ) {
					$return .= $this->build_recaptcha( $form_id );
				}
			} elseif ( 'hcaptcha' === $selected_captcha_service ) {
				$return .= $this->build_hcaptcha( $form_id );
			}
		}

		$return .= $this->submit( $form_id );

		$return .= $this->build_honeypot_field();

		$return .= $this->add_verify_fields( $form_data );

		$return .= $this->create_instance_field( $instance );

		$return .= $this->build_timestamp();

		$return .= $this->must_opt_in( $form_data );

		$return .= '</form>';

		$return .= wp_kses_post( $this->maybe_add_disclose_note( $form_data ) );

		ob_start();

		/**
		 * Fires after the end of the form tag.
		 *
		 * @deprecated 1.9.0 Deprecated in favor of properly-prefixed hookname.
		 *
		 * @since 1.4.0
		 *
		 * @param int $form_id Current form ID.
		 */
		do_action_deprecated( 'ctct_after_form', [ $form_id ], '1.9.0', 'constant_contact_after_form' );

		/**
		 * Fires after the closing form tag.
		 *
		 * @author Rebekah Van Epps <rebekah.vanepp@webdevstudios.com>
		 * @since  1.9.0
		 *
		 * @param int $form_id Current form ID.
		 */
		do_action( 'constant_contact_after_form', $form_id );

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
	public function get_current_page() : string {
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
	 * @return string|bool.
	 */
	public function add_verify_fields( array $form_data ) {
		if (
			isset( $form_data['options']['form_id'] )
		) {

			$form_id = absint( $form_data['options']['form_id'] );

			if ( ! $form_id ) {
				return false;
			}

			$return = $this->input_hidden( 'ctct-id', $form_id );

			// If we have saved a verify value, add that to our field as well. this is to double-check
			// that we have the correct form id for processing later.
			$verify_key = get_post_meta( $form_id, '_ctct_verify_key', true );

			if ( $verify_key ) {
				$return .= $this->input_hidden( 'ctct-verify', $verify_key );
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
	 * @param  array $form_data  Formulated cmb2 data for form.
	 * @param  array $old_values Original values.
	 * @param  array $req_errors Errors.
	 * @param  int   $instance   Current form instance.
	 * @return string
	 */
	public function build_form_fields( array $form_data, array $old_values, array $req_errors, int $instance ) : string {
		$return  = '';
		$form_id = absint( $form_data['options']['form_id'] );

		$label_placement = constant_contact_get_css_customization( $form_id, '_ctct_form_label_placement' );
		if ( empty( $label_placement ) ) {
			$label_placement = 'top';
		}

		if ( isset( $form_data['fields'] ) && is_array( $form_data['fields'] ) ) {
			foreach ( $form_data['fields'] as $key => $value ) {
				$return .= $this->field( $value, $old_values, $req_errors, $form_id, $label_placement, $instance );
			}
		}

		if ( isset( $form_data['options']['optin']['list'] ) ) {
			$lists = maybe_unserialize( $form_data['options']['optin']['list'] );

			$return .= $this->field(
				[
					'name'     => __( 'Select list(s) to subscribe to', 'constant-contact-forms' ),
					'map_to'   => 'lists',
					'type'     => 'checkbox',
					'required' => true,
					'value'    => $lists,
				],
				$old_values,
				$req_errors,
				$form_id,
				$label_placement,
				$instance
			);
			$return .= $this->opt_in( $form_data['options'], $instance );
		}

		return $return;
	}

	/**
	 * Display a honeypot spam field.
	 *
	 * @since 1.2.2
	 * @since 1.13.0 Moved visually hidden .ctct_usage styles inline to prevent honeypot field from
	 *               being displayed when the "Disable Constant Contact CSS" option is checked.
	 *
	 * @return string
	 */
	public function build_honeypot_field() : string {
		return sprintf(
			'<div ' .
				'class="ctct_usage" ' .
				'style="border: 0 none; clip: rect( 0, 0, 0, 0 ); height: 1px; margin: -1px; overflow: hidden; padding: 0; position: absolute; width: 1px;"' .
			'><label for="ctct_usage_field">%s</label><input type="text" value="" id="ctct_usage_field" name="ctct_usage_field" class="ctct_usage_field" tabindex="-1" /></div>',
			esc_html__( 'Constant Contact Use. Please leave this field blank.', 'constant-contact-forms' )
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
	public function build_recaptcha( int $form_id ) : string {
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

		return $recaptcha->get_inline_markup();
	}

	/**
	 * Display an hCaptcha field.
	 *
	 * @since 2.9.0
	 *
	 * @param int $form_id ID of form being rendered.
	 * @return string
	 */
	public function build_hcaptcha( int $form_id ) : string {
		$hcaptcha = new ConstantContact_hCaptcha();

		$hcaptcha->set_hcaptcha_keys();

		$hcaptcha->set_theme(
			/**
			 * Filters the theme to be used with hCaptcha.
			 *
			 * Options are 'light' and 'dark';
			 *
			 * @since 2.9.0
			 *
			 * @param string $value   Theme to use. Default 'light'.
			 * @param int    $form_id ID of the form being rendered.
			 */
			apply_filters( 'constant_contact_hcaptcha_theme', 'light', $form_id )
		);

		$hcaptcha->set_size(
			/**
			 * Filters the hCaptcha size to render.
			 *
			 * @since 2.9.0
			 *
			 * @param string $value Size to render. Options are 'normal', 'compact', and 'invisible'.
			 */
			apply_filters( 'constant_contact_hcaptcha_size', 'normal', $form_id )
		);

		$hcaptcha->set_language(
			/**
			 * Filters the language code to be used with hCaptcha.
			 *
			 * See https://docs.hcaptcha.com/languages for available values.
			 *
			 * @since 2.9.0
			 *
			 * @param string $value   Language code to use. Default '' for automatic detection.
			 * @param int    $form_id ID of the form being rendered.
			 */
			apply_filters( 'constant_contact_hcaptcha_lang', '', $form_id )
		);

		$hcaptcha->set_mode(
			/**
			 * Set the hCaptcha Mode to use.
			 *
			 * @since 2.9.0
			 *
			 * @param string $value   Use 'live' (default) or 'test' mode. In 'test' mode, predefined keys are used.
			 * @param int    $form_id ID of the form being rendered.
			 */
			apply_filters( 'constant_contact_hcaptcha_mode', 'live', $form_id )
		);

		return $hcaptcha->get_inline_markup();
	}

	/**
	 * Render a hidden input field storing the current time.
	 *
	 * @since 1.2.4
	 *
	 * @return string
	 */
	public function build_timestamp() : string {
		return '<input type="hidden" name="ctct_time" value="' . current_time( 'timestamp' ) . '" />'; // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
	}

	/**
	 * Add custom CSS classes to the form.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function build_custom_form_classes() : array {
		$custom   = [];
		$global   = [];
		$per_form = [];
		if ( ! empty( $this->global_form_styles['global_form_classes'] ) ) {
			$global = explode( ' ', $this->global_form_styles['global_form_classes'] );
			if ( ! empty( $global ) ) {
				$custom = array_merge( $custom, $global );
			}
		}

		if ( ! empty( $this->specific_form_styles['input_custom_classes'] ) ) {
			$per_form = explode( ' ', $this->specific_form_styles['input_custom_classes'] );
			if ( ! empty( $per_form ) ) {
				$custom = array_merge( $custom, $per_form );
			}
		}

		return array_unique( array_filter( array_merge( $custom, $global, $per_form ) ) );
	}

	/**
	 * Use a hidden field to denote needing to opt in.
	 *
	 * @since 1.3.6
	 *
	 * @param array $form_data Options for the form.
	 * @return string
	 */
	public function must_opt_in( array $form_data ) : string {
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
	 * @param  array  $field           Field data.
	 * @param  array  $old_values      Original values.
	 * @param  array  $req_errors      Errors.
	 * @param  int    $form_id         Current form ID.
	 * @param  string $label_placement Label placement location.
	 * @param  int    $instance        Current form instance.
	 * @return string                  HTML markup
	 */
	public function field( array $field, array $old_values = [], array $req_errors = [], int $form_id = 0, string $label_placement = 'top', int $instance = 0 ) : string {
		if ( ! isset( $field['name'] ) || ! isset( $field['map_to'] ) ) {
			return '';
		}

		$field = wp_parse_args(
			$field,
			[
				'name'             => '',
				'map_to'           => '',
				'type'             => '',
				'description'      => '',
				'field_custom_css' => [],
				'required'         => false,
			]
		);

		$name  = sanitize_text_field( $field['name'] );
		$map   = sanitize_text_field( $field['map_to'] );
		$desc  = sanitize_text_field( $field['description'] ?? '' );
		$type  = sanitize_text_field( $field['type'] ?? 'text_field' );
		$value = $field['value'] ?? false;
		$value = is_array( $value ) ? array_map( 'sanitize_text_field', $value ) : sanitize_text_field( $value );
		$req   = $field['required'] ?? false;

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
			case 'phone_number':
				return $this->input( 'tel', $name, $map, $value, $desc, $req, false, $field_error, $form_id, $label_placement, $instance );
			case 'website':
				return $this->input( 'url', $name, $map, $value, $desc, $req, false, $field_error, $form_id, $label_placement, $instance );
			case 'custom':
			case 'first_name':
			case 'last_name':
			case 'job_title':
			case 'company':
			case 'text_field':
				return $this->input( 'text', $name, $map, $value, $desc, $req, false, $field_error, $form_id, $label_placement, $instance );
			case 'custom_text_area':
				return $this->textarea( $name, $map, $value, $desc, $req, $field_error, 'maxlength="2000"', $label_placement, $instance );
			case 'email':
				return $this->input( 'email', $name, $map, $value, $desc, $req, false, $field_error, $form_id, $label_placement, $instance );
			case 'hidden':
				return $this->input_hidden( $name, $value );
			case 'checkbox':
				return $this->checkbox( $name, $map, $value, $desc, $req, $field_error, $form_id, $label_placement, $instance );
			case 'submit':
				return $this->input( 'submit', $name, $map, $value, $desc, $req, false, $field_error, $form_id, $label_placement, $instance );
			case 'address':
				$value = ! empty( $value ) ? $value : [];
				return $this->address( $name, $map, $value, $desc, $req, $field_error, $form_id, $label_placement, $instance );
			case 'anniversery':
				return $this->dates( $name, $map, $value, $desc, $req, $field_error, $instance );
			case 'birthday':
				return $this->birthday( $name, $map, $value, $desc, $req, false, $field_error, $form_id, $label_placement, $instance );
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
	 * @return array|string Submitted value.
	 */
	public function get_submitted_value( $value = '', string $map = '', array $field = [], array $submitted_vals = [] ) {
		if ( $value ) {
			return $value;
		}

		if ( ! is_array( $submitted_vals ) || empty( $submitted_vals ) ) {
			return '';
		}

		$return = [];

		foreach ( $submitted_vals as $post ) {

			if ( isset( $post['key'] ) && $post['key'] ) {
				$post_map = filter_input( INPUT_POST, esc_attr( $map ), FILTER_SANITIZE_SPECIAL_CHARS );

				if ( 'address' === $field['name'] ) {

					if ( strpos( $post['key'], '_address___' ) !== false ) {

						$addr_key = explode( '___', $post['key'] );

						if ( isset( $addr_key[0] ) && $addr_key[0] ) {
							$post_key = filter_input( INPUT_POST, esc_attr( $post['key'] ), FILTER_SANITIZE_SPECIAL_CHARS );
							$post_key = empty( $post_key ) ? '' : sanitize_text_field( wp_unslash( $post_key ) );

							$return[ esc_attr( $addr_key[0] ) ] = $post_key;
						}
					}
				} elseif ( $post['key'] === $map && ! empty( $post_map ) ) {
					return sanitize_text_field( wp_unslash( $post_map ) );
				}
			}
		}

		return $return;
	}

	/**
	 * Helper method to display in-line for success/error messages.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $type    Success/error/etc for class.
	 * @param  string $message Message to display to user.
	 * @param  string $role    Message role.
	 * @return string          HTML markup.
	 */
	public function message( string $type, string $message, string $role = 'log' ) : string {
		return sprintf(
			'<p class="ctct-message %s ctct-%s" role="%s">%s</p>',
			esc_attr( $type ),
			esc_attr( $type ),
			esc_attr( $role ),
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
	public function get_description_inline_styles() : string {
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
	public function description( string $desc = '', $form_id = false ) : string {

		$display      = '';
		$inline_style = $this->get_description_inline_styles();

		return '<span class="ctct-form-description" ' . $inline_style . '>' . wpautop( wp_kses_post( $desc ) ) . '</span>' . $display;
	}

	/**
	 * Helper method to display label for form field + field starting markup.
	 *
	 * @since  1.0.0
	 * @since  1.9.0 Added $tag
	 *
	 * @param  string  $type           Type of field.
	 * @param  string  $name           Name / id of field.
	 * @param  string  $f_id           Field ID.
	 * @param  string  $label          Label text for field.
	 * @param  boolean $req            If this field required.
	 * @param  boolean $use_label      Whether or not to use label.
	 * @param  string  $tag            HTML tag for field.
	 * @return string HTML markup.
	 */
	public function field_top( string $type = '', string $name = '', string $f_id = '', string $label = '', bool $req = false, bool $use_label = true, string $tag = 'p' ) : string {

		$classes = [
			'ctct-form-field',
			'ctct-form-field-' . $type,
		];
		if ( $req ) {
			$classes[] = 'ctct-form-field-required';
		}

		$markup = sprintf(
			'<%1$s class="%2$s">',
			$tag,
			esc_attr( implode( ' ', $classes ) )
		);

		if ( ! $use_label ) {
			$markup .= '<span class="ctct-input-container">';
		}

		return $markup;
	}

	/**
	 * Bottom of field markup.
	 *
	 * @since  1.0.0
	 * @since  1.3.5 Added $use_label
	 * @since  1.9.0 Added $tag
	 *
	 * @param  string $name        Field name.
	 * @param  string $field_label Field label.
	 * @param  bool   $use_label   Whether or not to include label markup.
	 * @param  string $tag         HTML tag for field.
	 * @return string HTML markup
	 */
	public function field_bottom( string $name = '', string $field_label = '', bool $use_label = true, string $tag = 'p' ) : string {

		$markup = '';
		if ( ! empty( $name ) && ! empty( $field_label ) ) {
			$markup .= $this->get_label( $name, $field_label );
		}

		if ( ! $use_label ) {
			$markup .= '</span>';
		}

		return $markup . "</$tag>"; // opening markup for `$tag` variable set in `field_top()`
	}

	/**
	 * Get inline styles for the form's submit button.
	 *
	 * @since 1.4.0
	 *
	 * @return string
	 */
	public function get_submit_inline_styles() : string {
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
	public function get_label( string $f_id, string $field_label ) : string {
		return '<label for="' . $f_id . '">' . $field_label . '</label>';
	}

	/**
	 * Wrapper for 'input' form fields.
	 *
	 * @since 1.0.0
	 *
	 * @param  string  $type            Type of form field.
	 * @param  string  $name            ID of form field.
	 * @param  string  $id              ID attribute value.
	 * @param  string  $value           pre-filled value.
	 * @param  string  $label           label text for input.
	 * @param  boolean $req             If field required.
	 * @param  boolean $f_only          If we only return the field itself, with no label.
	 * @param  boolean $field_error     Field error.
	 * @param  int     $form_id         Current form ID.
	 * @param  string  $label_placement Where to place the label.
	 * @param  int     $instance        Current form instance.
	 * @return string                   HTML markup for field.
	 */
	public function input( string $type = 'text', string $name = '', string $id = '', string $value = '', string $label = '', bool $req = false, bool $f_only = false, bool $field_error = false, int $form_id = 0, string $label_placement = '', int $instance = 0, bool $show_label = true, string $date_part = '' ) : string {
		$id_salt               = wp_rand();
		$name                  = sanitize_text_field( $name );
		$field_key             = sanitize_title( $id );
		$field_id              = "{$field_key}_{$instance}_$id_salt";
		$input_inline_styles   = '';
		$tel_regex_pattern     = '';
		$label_placement_class = 'ctct-label-' . $label_placement;
		$specific_form_styles  = $this->specific_form_styles;
		$inline_font_styles    = $this->get_inline_font_color();

		if ( 'submit' === $type ) {
			$input_inline_styles = $this->get_submit_inline_styles();
		}

		if ( 'tel' === $type ) {
			$tel_regex_pattern = '^[0-9\-\+\.()]*';
		}

		$type     = sanitize_text_field( $type );
		$value    = sanitize_text_field( $value );
		$label    = esc_html( sanitize_text_field( $label ) );
		$req_text = $req ? 'required aria-required="true" ' : 'aria-required="false" ';

		$markup = $this->field_top( $type, $name, $field_key, $label, $req );

		$req_label = '';

		if ( $req ) {
			$req_label = $this->display_required_indicator();
		}
		if ( $show_label && ( 'top' === $label_placement || 'left' === $label_placement || 'hidden' === $label_placement ) && ( 'submit' !== $type ) ) {
			if ( $inline_font_styles ) {
				$markup .= '<span class="' . $label_placement_class . '"  style="' . $inline_font_styles . '">';
			} else {
				$markup .= '<span class="' . $label_placement_class . '">';
			}

			$markup .= $this->get_label( $field_id, $name . ' ' . $req_label );
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
		 * @param  array  $classes   Array of classes to apply to the field.
		 * @param  string $type      The field type being rendered.
		 * @param  int    $form_id   Form ID.
		 * @param  int    $field_key Field ID.
		 * @return array
		 */
		$classes = apply_filters( 'constant_contact_input_classes', $classes, $type, $form_id, $field_key );

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
		} elseif ( false !== strpos( $id, 'first_name___' ) || false !== strpos( $id, 'last_name___' ) ) {
			$max_length = 'maxlength="255"';
		}

		if ( $field_error ) {
			$classes[] = 'ctct-invalid';
		}

		$classes[]  = $field_key;
		$class_attr = '';

		if ( count( $classes ) ) {
			$class_attr = esc_attr( implode( ' ', $classes ) );
		}

		$tel_pattern_title = apply_filters( 'constant_contact_tel_pattern_title', esc_html__( 'numbers, dashes, pluses, periods, and parentheses', 'constant-contact-forms' ) );

		// Button field type do not need a placeholder.
		$placeholder = '';

		if ( 'submit' !== $type ) {
			$placeholder = "placeholder=\"$label\"";
		}

		$minmax = '';
		if ( 'year' === $date_part ) {
			$minmax = 'maxlength="4" min="1900" max="2035"';
		}
		if ( 'month' === $date_part ) {
			$minmax = 'maxlength="2" min="1" max="12"';
		}

		if ( 'day' === $date_part ) {
			$minmax = 'maxlength="2" min="1" max="31"';
		}

		/* 1: Required text, 2: Field type, 3: Field name, 4: Inline styles, 5: Field value, 6: Max length, 7: Placeholder, 8: Field class(es), 9: Field ID., 10: Tel Regex Pattern. */
		$field   = '<input %1$s type="%2$s" id="%3$s" name="%4$s" %5$s value="%6$s" class="%7$s" %8$s %9$s %10$s %11$s />';
		$markup .= sprintf(
			$field,
			$req_text, // %1$s starts here.
			$type,
			$field_id,
			$field_key,
			$input_inline_styles,
			$value,
			$class_attr,
			$max_length,
			$placeholder,
			$tel_regex_pattern ? "pattern=\"$tel_regex_pattern\" title=\"$tel_pattern_title\"" : '',
			$minmax
		);

		// Reassign because if we want "field only", like for hidden inputs, we need to still pass a value that went through sprintf().
		$field = $markup;

		if ( $show_label && ( 'bottom' === $label_placement || 'right' === $label_placement ) && ( 'submit' !== $type ) ) {
			$markup .= '<span class="' . $label_placement_class . '">';
			$markup .= $this->get_label( $field_id, $name . ' ' . $req_label );
			$markup .= '</span>';
		}

		if ( $field_error ) {
			$markup .= $this->field_bottom( $field_id, $field_error );
		} else {
			$markup .= $this->field_bottom();
		}

		if ( $f_only ) {
			return $field;
		}

		return $markup;
	}

	/**
	 * Display hidden input field.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.9.0
	 *
	 * @param  string $name  Field name.
	 * @param  string $value Field value.
	 * @return string        HTML markup for field.
	 */
	public function input_hidden( string $name = '', string $value = '' ) : string {
		return sprintf(
			'<input type="hidden" name="%1$s" value="%2$s" />',
			sanitize_text_field( $name ),
			sanitize_text_field( $value )
		);
	}

	/**
	 * Checkbox field helper method.
	 *
	 * @since 1.0.0
	 * @since 1.9.0 Updated params to mirror text inputs and updated output to work with multicheck.
	 *
	 * @param  string       $name            ID of form field.
	 * @param  string       $id              ID attribute value.
	 * @param  string|array $value           Value of field.
	 * @param  string       $label           label text for input.
	 * @param  boolean      $req             If field required.
	 * @param  boolean      $field_error     Field error.
	 * @param  int          $form_id         Current form ID.
	 * @param  string       $label_placement Where to place the label.
	 * @param  int          $instance        Current form instance.
	 * @return string                        HTML markup for checkbox.
	 */
	public function checkbox( string $name = '', string $id = '', $value = [], string $label = '', bool $req = false, bool $field_error = false, int $form_id = 0, string $label_placement = '', int $instance = 0 ) : string {
		$name                  = sanitize_text_field( $name );
		$field_key             = sanitize_title( $id );
		$field_id              = "{$field_key}_$instance";
		$label_placement_class = 'ctct-label-top';
		$value                 = is_array( $value ) ? array_map( 'sanitize_text_field', $value ) : sanitize_text_field( $value ); // Somehow could get string for `$value` with this, so we do some juggling here.
		$value                 = is_array( $value ) ? $value : [ $value ];
		$label                 = esc_attr( $label );
		$type                  = 'checkbox';
		$classes               = [
			'ctct-' . esc_attr( $type ),
			'ctct-label-right',
		];

		/**
		 * Filter to add classes for the rendering input.
		 *
		 * @since  1.2.0
		 * @param  array  $classes   Array of classes to apply to the field.
		 * @param  string $type      The field type being rendered.
		 * @param  int    $form_id   Form ID.
		 * @param  int    $field_key Field ID.
		 * @return array
		 */
		$classes = apply_filters( 'constant_contact_input_classes', $classes, $type, $form_id, $field_key );

		$markup     = $this->field_top( $type, $name, $field_key, $label, $req, true, 'div' );
		$class_attr = 'class="' . implode( ' ', $classes ) . '"';

		$markup .= '<fieldset>';
		$markup .= '<legend class="' . $label_placement_class . '">';
		$markup .= $name;
		$markup .= '</legend>';

		$key_pieces = explode( '___', $field_key );
		$total      = count( $value );
		$count      = 0;

		for ( $i = 0; $i < $total; $i++ ) {
			$input_label = $value[ $i ];

			// Retrieve list names for label.
			if ( 'lists' === $key_pieces[0] ) {
				$list = get_posts(
					[
						'numberposts' => 1,
						'post_type'   => 'ctct_lists',
						'meta_key'    => '_ctct_list_id',
						'meta_value'  => $input_label,
					]
				);

				// Skip list IDs that don't have corresponding post.
				if ( empty( $list ) ) {
					continue;
				}

				$input_label = reset( $list )->post_title;
			}

			$markup .= sprintf(
				'<input type="%1$s" name="%2$s[]" id="%3$s" value="%4$s" %5$s %6$s />',
				$type,
				$field_key,
				"{$field_id}_$i",
				$value[ $i ],
				$class_attr,
				0 === $count ? 'checked' : ''
			);
			$markup .= '<span class="ctct-label-right">';
			$markup .= $this->get_label( "{$field_id}_$i", $input_label );
			$markup .= '</span>';

			if ( $i < ( count( $value ) - 1 ) ) {
				$markup .= '<br />';
			}

			$count++;
		}

		$markup .= '</fieldset>';

		if ( $field_error ) {
			$markup .= $this->field_bottom( $field_id, $field_error, true, 'div' );
		} else {
			$markup .= $this->field_bottom( '', '', true, 'div' );
		}

		// If only one list displayed, hide input.
		if ( 'lists' === $key_pieces[0] && $count <= 1 ) {
			$markup = "<div class='ctct-list-selector' style='display:none;'>$markup</div>";
		}

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
	public function submit( int $form_id = 0 ) : string {
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
			apply_filters(
				'constant_contact_submit_text',
				__( 'Send', 'constant-contact-forms' )
			);

		return $this->field(
			[
				'type'   => 'submit',
				'name'   => 'ctct-submitted',
				'map_to' => 'ctct-submitted',
				'value'  => esc_html( $button_text ),
			]
		);
	}

	/**
	 * Build markup for opt_in form.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $form_data Form data structure.
	 * @param  int   $instance  Current form instance.
	 * @return string           Markup of optin form.
	 */
	public function opt_in( array $form_data, int $instance = 0 ) : string {

		if ( ! isset( $form_data['optin'] ) ) {
			return '';
		}

		$optin = wp_parse_args(
			$form_data['optin'],
			[
				'list'         => false,
				'show'         => false,
				'instructions' => '',
			]
		);

		if ( isset( $optin['list'] ) && $optin['list'] ) {
			return $this->optin_display( $optin, $instance );
		}

		return '';
	}

	/**
	 * Internal method to display checkbox.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $optin    Optin data.
	 * @param  int   $instance Current form instance.
	 * @return string          HTML markup.
	 */
	private function optin_display( array $optin, int $instance = 0 ) : string {

		$label = sanitize_text_field( $optin['instructions'] ?? '' );

		$show = false;
		if ( isset( $optin['show'] ) && 'on' === $optin['show'] ) {
			$show = true;
		}

		$markup = '';

		if ( ! $show ) {
			$markup = '<div class="ctct-optin-hide" style="display:none;">';
		}

		$markup .= $this->get_optin_markup( $label, true, $show, $instance );

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
	 * @param  string $label    Label for field.
	 * @param  string $value    Value of opt in field.
	 * @param  string $show     Whether or not we are showing the field.
	 * @param  int    $instance Current form instance.
	 * @return string           HTML markup
	 */
	public function get_optin_markup( string $label, string $value, string $show, int $instance = 0 ) : string {
		$checked   = $show ? '' : 'checked';
		$field_key = 'ctct-opt-in';
		$field_id  = "{$field_key}_$instance";

		$markup  = $this->field_top( 'checkbox', $field_key, $field_key, $label, false, false );
		$markup .= '<input type="checkbox" ' . $checked . ' name="' . esc_attr( $field_key ) . '" class="ctct-checkbox ' . esc_attr( $field_key ) . '" value="' . esc_attr( $value ) . '" id="' . esc_attr( $field_id ) . '" />';
		$markup .= $this->field_bottom( $field_id, ' ' . wp_kses_post( $label ), false );

		return $markup;
	}

	/**
	 * Builds a fancy address field group.
	 *
	 * @since 1.0.0
	 *
	 * @param  string  $name            Name of fields.
	 * @param  string  $field_key       Form ID name.
	 * @param  array   $value           Values of each field.
	 * @param  string  $desc            Label of field.
	 * @param  boolean $req             Whether or not required.
	 * @param  string  $field_error     Field error value.
	 * @param  string  $label_placement Where to put the label.
	 * @param  int     $instance        Current form instance.
	 * @return string                   HTML markup.
	 */
	public function address( string $name = '', string $field_key = '', array $value = [], string $desc = '', bool $req = false, string $field_error = '', int $form_id = 0, string $label_placement = 'top', int $instance = 0 ) : string {
		$field_id = "{$field_key}_$instance";
		$street   = esc_html__( 'Street Address', 'constant-contact-forms' );
		$line_2   = esc_html__( 'Address Line 2', 'constant-contact-forms' );
		$city     = esc_html__( 'City', 'constant-contact-forms' );
		$state    = apply_filters( 'constant_contact_address_state', esc_html__( 'State', 'constant-contact-forms' ) );
		$zip      = apply_filters( 'constant_contact_address_zip_code', esc_html__( 'ZIP Code', 'constant-contact-forms' ) );

		$req_label             = $req ? ' ' . $this->display_required_indicator() : '';
		$req_class             = $req ? ' ctct-form-field-required ' : '';
		$req                   = $req ? ' required aria-required="true" ' : 'aria-required="false" ';
		$label_placement_class = 'ctct-label-' . $label_placement;
		$inline_font_styles    = $this->get_inline_font_color();

		if ( ! empty( $form_id ) ) {
			$included_address_fields = get_post_meta( $form_id, '_ctct_address_fields_include', true );
			$required_address_fields = get_post_meta( $form_id, '_ctct_address_fields_require', true );
		}

		if ( ! empty( $included_address_fields ) ) {
			$fields = [];
			foreach ( $included_address_fields as $field ) {
				// These can be left alone for each iteration. No need to make field-specific variables.
				$is_required_bool = ( ! empty( $required_address_fields ) && in_array( $field, $required_address_fields, true ) );
				$is_required      = ( ! empty( $required_address_fields ) && in_array( $field, $required_address_fields, true ) ) ? 'required aria-required="true" ' : 'aria-required="false" ';

				// Reassigning in this context
				$req_class   = $is_required_bool ? 'ctct-form-field-required' : '';
				$field_label = '';
				switch ( $field ) {
					case 'country':
						$field_label          = esc_html__( 'Country', 'constant-contact-forms' );
						$input_numbered_class = 'input_2_1_2_container';
						break;
					case 'street':
						$field_label          = esc_html__( 'Street', 'constant-contact-forms' );
						$input_numbered_class = 'input_2_1_2_container';
						break;
					case 'city':
						$field_label          = esc_html__( 'City', 'constant-contact-forms' );
						$input_numbered_class = 'input_2_1_3_container';
						break;
					case 'state':
						$field_label          = esc_html__( 'State/Province', 'constant-contact-forms' );
						$input_numbered_class = 'input_2_1_4_container';
						break;
					case 'zip':
						$field_label          = esc_html__( 'Postal Code', 'constant-contact-forms' );
						$input_numbered_class = 'input_2_1_5_container';
						break;
					default:
						$input_numbered_class = '';
						break;
				}
				if ( 'country' !== $field ) {
					$field_value          = $value[ $field ] ?? '';
					$label_placement_tmpl = '<span class="%1$s"><label for="%2$s_%3$s" style="%4$s">%5$s %6$s</label></span><input %7$s type="text" class="ctct-text ctct-address-%2$s %1$s %2$s_%8$s" name="%2$s_%8$s" value="%9$s" id="%2$s_%3$s">';

					if ( in_array( $label_placement_class, [ 'ctct-label-bottom', 'ctct-label-right' ], true ) ) {
						$label_placement_tmpl = '<input %7$s type="text" class="ctct-text ctct-address-%2$s %1$s %2$s_%8$s" name="%2$s_%8$s" value="%9$s" id="%2$s_%3$s"><span class="%1$s"><label for="%2$s_%3$s" style="%4$s">%5$s %6$s</label></span>';
					}
					$fields[ $field ] = sprintf(
						'<div class="ctct-form-field ctct-field-full address-%1$s %2$s %3$s">%4$s</div>',
						$field,
						esc_attr( $req_class ),
						$input_numbered_class,
						sprintf(
							$label_placement_tmpl,
							esc_attr( $label_placement_class ), // 1
							$field, // 2
							esc_attr( $field_id ), // 3
							esc_attr( $inline_font_styles ), // 4
							$field_label, // 5
							( $is_required_bool ) ? $this->display_required_indicator() : '', // 6
							$is_required, // 7
							esc_attr( $field_key ), // 8
							esc_attr( $field_value ) // 9
						)
					);
				} else {
					$countries      = constant_contact_countries_array();
					$select_options = [
						'<option value="">' . esc_html__( 'Please choose an option', 'constant-contact-forms' ) . '</option>',
					];
					$field_value    = $value[ $field ] ?? '';
					foreach ( $countries as $country ) {
						$select_options[] = sprintf(
							'<option value="%1$s" %2$s>%3$s</option>',
							esc_attr( $country ),
							selected( $field_value, esc_attr( $country ) ),
							esc_html( $country )
						);
					}

					$label_placement_tmpl = '<span class="%1$s"><label for="%2$s_%3$s" style="%4$s">%5$s %6$s</label></span><select %7$s class="ctct-dropdown ctct-address-%2$s %1$s %2$s_%8$s" name="%2$s_%8$s" id="%2$s_%3$s">%9$s</select>';

					if ( in_array( $label_placement_class, [ 'ctct-label-bottom', 'ctct-label-right' ], true ) ) {
						$label_placement_tmpl = '<select %7$s class="ctct-dropdown ctct-address-%2$s %1$s %2$s_%8$s" name="%2$s_%8$s" id="%2$s_%3$s">%9$s</select><span class="%1$s"><label for="%2$s_%3$s" style="%4$s">%5$s %6$s</label></span>';
					}

					$fields[ $field ] = sprintf(
						'<div class="ctct-form-field ctct-field-full address-%1$s %2$s %3$s">%4$s</div>',
						$field,
						esc_attr( $req_class ),
						$input_numbered_class,
						sprintf(
							$label_placement_tmpl,
							esc_attr( $label_placement_class ), // 1
							$field, // 2
							esc_attr( $field_id ), // 3
							esc_attr( $inline_font_styles ), // 4
							$field_label, // 5
							( $is_required_bool ) ? $this->display_required_indicator() : '', // 6
							$is_required, // 7
							esc_attr( $field_key ), // 8
							implode( $select_options ) // 9
						)
					);
				}
			}

			$return = '<fieldset class="ctct-address"><legend style="%1$s">%2$s</legend>%3$s</fieldset>';
			return sprintf(
				$return,
				esc_attr( $inline_font_styles ),
				esc_html( $name ),
				implode( '', $fields )
			);
		} else {
			// LEGACY-ISH VERSION

			$v_street = $value['street_address'] ?? '';
			$v_line_2 = $value['line_2_address'] ?? '';
			$v_city   = $value['city_address'] ?? '';
			$v_state  = $value['state_address'] ?? '';
			$v_zip    = isset( $value['zip_address'] ) ? $value['zip'] : '';

			$label_street1 = sprintf(
				'<span class="%1$s"><label for="street_%2$s" style="%3$s">%4$s</label></span>',
				esc_attr( $label_placement_class ),
				esc_attr( $field_id ),
				esc_attr( $inline_font_styles ),
				esc_attr( $street ) . $req_label
			);
			$input_street1 = sprintf(
				'<input %1$stype="text" class="ctct-text ctct-address-street %2$s street_%3$s" name="street_%4$s" value="%5$s" id="street_%6$s">',
				$req,
				esc_attr( $label_placement_class ),
				esc_attr( $field_key ),
				esc_attr( $field_key ),
				esc_attr( $v_street ),
				esc_attr( $field_id )
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
				esc_attr( $field_id ),
				esc_attr( $inline_font_styles ),
				esc_attr( $line_2 )
			);

			$input_street2 = sprintf(
				'<input type="text" class="ctct-text ctct-address-line-2 %1$s line_2_%2$s" name="line_2_%3$s" value="%4$s" id="line_2_%5$s">',
				esc_attr( $label_placement_class ),
				esc_attr( $field_key ),
				esc_attr( $field_key ),
				esc_attr( $v_line_2 ),
				esc_attr( $field_id )
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
				esc_attr( $field_id ),
				esc_attr( $inline_font_styles ),
				esc_attr( $city ) . $req_label
			);

			$input_city = sprintf(
				'<input %1$stype="text" class="ctct-text ctct-address-city %2$s city_%3$s" name="city_%4$s" value="%5$s" id="city_%6$s">',
				$req,
				esc_attr( $label_placement_class ),
				esc_attr( $field_key ),
				esc_attr( $field_key ),
				esc_attr( $v_city ),
				esc_attr( $field_id )
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
				esc_attr( $field_id ),
				esc_attr( $inline_font_styles ),
				esc_attr( $state ) . $req_label
			);

			$input_state = sprintf(
				'<input %1$stype="text" class="ctct-text ctct-address-state %2$s state_%3$s" name="state_%4$s" value="%5$s" id="state_%6$s">',
				$req,
				esc_attr( $label_placement_class ),
				esc_attr( $field_key ),
				esc_attr( $field_key ),
				esc_attr( $v_state ),
				esc_attr( $field_id )
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
				esc_attr( $field_id ),
				esc_attr( $inline_font_styles ),
				esc_attr( $zip ) . $req_label
			);

			$input_zip = sprintf(
				'<input %1$stype="text" class="ctct-text ctct-address-zip %2$s zip_%3$s" name="zip_%4$s" value="%5$s" id="zip_%6$s">',
				$req,
				esc_attr( $label_placement_class ),
				esc_attr( $field_key ),
				esc_attr( $field_key ),
				esc_attr( $v_zip ),
				esc_attr( $field_id )
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
			$return .= '<div class="ctct-form-field ctct-field-full address-line-2%s input_2_1_2_container">%s</div>';
			$return .= '<div class="ctct-form-field ctct-field-third address-city%s input_2_1_3_container">%s</div>';
			$return .= '<div class="ctct-form-field ctct-field-third address-state%s input_2_1_4_container">%s</div>';
			$return .= '<div class="ctct-form-field ctct-field-third address-zip%s input_2_1_5_container">%s</div>';
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
	}

	public function birthday( $name = '', $map = '', $value = '', $desc = '', $req = false, $f_only = false, $field_error = false, $form_id = 0, $label_placement = '', $instance = 0 ) {
		$return = '';
		/*$map = str_replace( 'birthday', '', $map );
		$return = sprintf(
			'<label for="%1$s">%2$s</label>',
			esc_attr( $name . '_month_' . $instance ),
			esc_html__( 'Birthday', 'constant-contact-forms' )
		);

		$return .= $this->get_label()

		$return .= sprintf(
			'<input id="%1$s" name="%2$s" type="number" min="1" max="12" placeholder="%3$s"/>',
			esc_attr( $name . '_month_' . $instance ),
			esc_attr( 'birthday_month' . $map ),
			esc_attr__('MM', 'constant-contact-forms' )
		);

		$return .= '/';

		$return .= sprintf(
			'<input id="%1$s" name="%2$s" type="number" min="1" max="31" placeholder="%3$s"/>',
			esc_attr( $name . '_day_' . $instance ),
			esc_attr( 'birthday_day' . $map ),
			esc_attr__( 'DD', 'constant-contact-forms' )
		);*/

		// input( string $type = 'text', string $name = '', string $id = '', string $value = '', string $label = '', bool $req = false, bool $f_only = false, bool $field_error = false, int $form_id = 0, string $label_placement = '', int $instance = 0 )
		$separator = $this->get_form_date_separator();
		$return .= '<div class="ctct-form-fields ctct-birthday-fields ctct-birthday-label-' . $label_placement . '">';
		$return .= $this->input( 'number', $name, $map, $value, 'MM', $req, false, $field_error, $form_id, $label_placement, $instance, true, 'month' );
		$return .= '<span class="ctct-birthday-field-separator"> ' . esc_html( $separator ) . ' </span>';
		$return .= $this->input( 'number', $name, $map, $value, 'DD', $req, false, $field_error, $form_id, $label_placement, $instance, false, 'day' );
		$return .= '</div>';

		return $return;
	}
	/**
	 * Gets and return a 3-part date selector.
	 *
	 * @since 1.0.0
	 *
	 * @param  string  $name        Name of field.
	 * @param  string  $f_id        Field ID.
	 * @param  array   $value       Values to pre-fill.
	 * @param  string  $desc        Description of fields.
	 * @param  boolean $req         If is required.
	 * @param  string  $field_error Field error text.
	 * @param  int     $instance    Current form instance.
	 * @return string               Fields HTML markup.
	 */
	public function dates( string $name = '', string $f_id = '', $value = [], string $desc = '', bool $req = false, string $field_error = '', int $instance = 0 ) : string {
		$month = esc_html__( 'Month', 'constant-contact-forms' );
		$day   = esc_html__( 'Day', 'constant-contact-forms' );
		$year  = esc_html__( 'Year', 'constant-contact-forms' );

		$v_month = $value['month'] ?? '';
		$v_day   = $value['day'] ?? '';
		$v_year  = $value['year'] ?? '';

		$req_class = $req ? ' ctct-form-field-required ' : '';

		$return  = '<p class="ctct-date"><fieldset>';
		$return .= ' <legend>' . esc_attr( $name ) . '</legend>';
		$return .= ' <div class="ctct-form-field ctct-field-inline month' . $req_class . '">';
		//$return .= $this->get_date_dropdown( $month, $f_id, 'month', $v_month, $req, $instance );
		$return .= ' </div>';
		$return .= ' <div class="ctct-form-field ctct-field-inline day' . $req_class . '">';
		//$return .= $this->get_date_dropdown( $day, $f_id, 'day', $v_day, $req, $instance );
		$return .= ' </div>';
		$return .= ' <div class="ctct-form-field ctct-field-inline year' . $req_class . '">';
		//$return .= $this->get_date_dropdown( $year, $f_id, 'year', $v_year, $req, $instance );
		$return .= ' </div>';

		$return .= '</fieldset></p>';

		return $return;
	}

	/**
	 * Displays text area field.
	 *
	 * @since 1.0.0
	 *
	 * @param  string  $name            Name of field.
	 * @param  string  $map             ID of field.
	 * @param  string  $value           Previous value of field.
	 * @param  string  $desc            Description/label of field.
	 * @param  boolean $req             If is required.
	 * @param  string  $field_error     Error from field.
	 * @param  string  $extra_attrs     Extra attributes to append.
	 * @param  string  $label_placement Where to place the label.
	 * @param  int     $instance        Current form instance.
	 * @return string                   HTML markup.
	 */
	public function textarea( string $name = '', string $map = '', string $value = '', string $desc = '', bool $req = false, string $field_error = '', string $extra_attrs = '', string $label_placement = 'top', int $instance = 0 ) : string {

		$classes          = [ 'ctct-form-field', 'comment-form-comment' ];
		$textarea_classes = [ 'ctct-textarea' ];
		$field_id         = "{$map}_$instance";
		$req_text         = $req ? 'required aria-required="true" ' : 'aria-required="false" ';

		if ( $req ) {
			$classes[] = 'ctct-form-field-required';
		}

		$label_placement_class = 'ctct-label-' . $label_placement;
		$textarea_classes[]    = $label_placement_class;
		$inline_font_styles    = $this->get_inline_font_color();

		$req_label = '';

		if ( $req ) {
			$req_label = $this->display_required_indicator();
		}

		$return   = '<p class="' . implode( ' ', $classes ) . '">';
		$label    = '<span class="' . $label_placement_class . '" style="' . $inline_font_styles . '"><label for="' . esc_attr( $field_id ) . '">' . esc_attr( $name ) . ' ' . $req_label . '</label></span>';
		$textarea = '<textarea class="' . esc_attr( implode( ' ', $textarea_classes ) ) . '" ' . $req_text . ' name="' . esc_attr( $map ) . '" id="' . esc_attr( $field_id ) . '" placeholder="' . esc_attr( $desc ) . '" ' . $extra_attrs . '>' . esc_html( $value ) . '</textarea>';

		$instructions_span = '<span class="ctct-textarea-warning-label" style="' . $inline_font_styles . '">' . esc_html__( 'Limit 2000 Characters', 'constant-contact-forms' ) . '</span>';

		if ( 'top' === $label_placement || 'left' === $label_placement || 'hidden' === $label_placement ) {
			$return .= $label . $textarea;
		}

		if ( 'right' === $label_placement || 'bottom' === $label_placement ) {
			$return .= $textarea . $label;
		}

		$return .= $instructions_span;

		if ( $field_error ) {
			$return .= '<span class="ctct-field-error"><label for="' . esc_attr( $field_id ) . '">' . esc_attr( __( 'Error: Please correct your entry.', 'constant-contact-forms' ) ) . '</label></span>';
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
	public function maybe_add_disclose_note( array $form_data ) : string {

		$opts = $form_data['options'] ?? false;

		if ( ! $opts ) {
			return '';
		}

		$optin = $opts['optin'] ?? false;

		if ( ! $optin ) {
			return '';
		}

		if ( ! constant_contact()->get_api()->is_connected() ) {
			return '';
		}

		$list = $optin['list'] ?? false;

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
	public function get_disclose_text() : string {

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
				'<div class="ctct-disclosure" style="%s"><hr><small>%s</small></div>',
				esc_attr( $this->get_inline_font_color() ),
				$this->get_inner_disclose_text()
			)
		);
	}

	/**
	 * Get our disclose text.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_inner_disclose_text() : string {

		$alternative_legal_text = constant_contact_get_option( '_ctct_alternative_legal_text' );

		if ( empty( $alternative_legal_text ) ) {
			return sprintf(
				// Translators: placeholder will hold company info for site owner.
				__(
					'By submitting this form, you are consenting to receive marketing emails from: %1$s. You can revoke your consent to receive emails at any time by using the SafeUnsubscribe&reg; link, found at the bottom of every email. %2$s',
					'constant-contact-forms'
				),
				$this->plugin->get_api()->get_disclosure_info(),
				sprintf(
					'<a href="%1$s" target="_blank" rel="noopener noreferrer" aria-label="%2$s">%3$s</a>',
					esc_url( 'https://www.constantcontact.com/legal/about-constant-contact' ),
					esc_attr__( 'About Constant Contact, opens a new window', 'constant-contact-forms' ),
					esc_html__( 'Emails are serviced by Constant Contact', 'constant-contact-forms' )
				)
			);
		}

		return $alternative_legal_text;
	}

	/**
	 * Get markup for the "maxlength" attribute to add to some text inputs.
	 *
	 * @since 1.0.0
	 *
	 * @param string $optional_label Optional label.
	 * @return string
	 */
	public function get_max_length_attr( string $optional_label = '' ) : string {
		$length       = 253; // Two less than 255char custom field limit for ": ".
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
	 * Return the character to use to seprate out date fields visually.
	 *
	 * Used between year, month, date fields display, as needed.
	 *
	 * @since NEXT
	 * @return string
	 */
	public function get_form_date_separator() : string {
		// https://en.wikipedia.org/wiki/List_of_date_formats_by_country
		/**
		 * Filters the character to use to separate out the date fields visually.
		 *
		 * @since NEXT
		 *
		 * @param  string $value Character to use for visual separator.
		 * @return string
		 */
		return (string) apply_filters( 'constant_contact_form_date_separator', '/' );
	}

	/**
	 * Get the inline font color.
	 *
	 * @since 1.4.3
	 *
	 * @return string
	 */
	private function get_inline_font_color() : string {
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
	public function display_required_indicator() : string {

		$title_attr = esc_attr__( 'This is a required field', 'constant-contact-forms' );
		/**
		 * Filters the markup used for the required indicator.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value A `<span>` tag with an asterisk indicating required status.
		 */
		return apply_filters( 'constant_contact_required_label', '<span class="ctct-required-indicatior" title="' . esc_attr( $title_attr ) .'">*</span>' );
	}

	/**
	 * Add hidden input field to verify current instance of form.
	 *
	 * @since 1.8.3
	 *
	 * @param  int $instance Current instance of form.
	 * @return string HTML markup for instance field.
	 */
	protected function create_instance_field( int $instance ) : string {
		return $this->input_hidden( 'ctct-instance', absint( $instance ) );
	}
}

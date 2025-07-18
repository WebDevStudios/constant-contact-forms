<?php
/**
 * Builder fields.
 *
 * @package ConstantContact
 * @subpackage BuilderFields
 * @author Constant Contact
 * @since 1.0.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Helper class for dealing with our form builder field functionality.
 *
 * @since 1.0.0
 */
class ConstantContact_Builder_Fields {

	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	protected object $plugin;

	/**
	 * Prefix for our meta fields/boxes.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public string $prefix = '_ctct_';

	/**
	 * Default option and placeholder values for the fields.
	 *
	 * @since 1.6.0
	 *
	 * @var array
	 */
	protected array $defaults = [];

	/**
	 * The default option and placeholder values for the fields after being run through filters.
	 *
	 * @since 1.6.0
	 *
	 * @var array
	 */
	protected array $filtered = [];

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $plugin Parent class object.
	 */
	public function __construct( object $plugin ) {
		$this->plugin = $plugin;
		$this->init();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'init', [ $this, 'hooks' ] );
		add_action( 'init', [ $this, 'init_field_defaults' ] );
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		global $pagenow;

		if ( ! $pagenow ) {
			return;
		}

		/**
		 * Filters the pages to add our form builder content to.
		 *
		 * @since 1.0.0
		 *
		 * @param array $value Array of WP admin pages to load builder on.
		 */
		$form_builder_pages = apply_filters(
			'constant_contact_form_builder_pages',
			[ 'post-new.php', 'post.php' ]
		);

		if ( ! in_array( $pagenow, $form_builder_pages, true ) ) {
			return;
		}

		add_action( 'cmb2_admin_init', [ $this, 'description_metabox' ] );
		add_action( 'cmb2_admin_init', [ $this, 'constant_contact_list_metabox' ] );
		add_action( 'cmb2_admin_init', [ $this, 'opt_ins_metabox' ] );
		add_action( 'cmb2_admin_init', [ $this, 'generated_shortcode' ] );
		add_action( 'cmb2_admin_init', [ $this, 'email_settings' ] );
		add_action( 'cmb2_admin_init', [ $this, 'custom_form_css_metabox' ] );
		add_action( 'cmb2_admin_init', [ $this, 'custom_input_css_metabox' ] );
		add_action( 'cmb2_admin_init', [ $this, 'fields_metabox' ] );
		add_action( 'cmb2_admin_init', [ $this, 'address_settings' ] );
		add_action( 'cmb2_admin_init', [ $this, 'add_css_reset_metabox' ] );
		add_filter( 'cmb2_override__ctct_generated_shortcode_meta_save', '__return_empty_string' );
		add_action( 'cmb2_render_reset_css_button', [ $this, 'render_reset_css_button' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'add_placeholders_to_js' ] );
	}
	/**
	 * Init default placeholder text and field types for fields.
	 *
	 * @since 1.6.0
	 */
	public function init_field_defaults() {

		$this->defaults['fields'] = [
			'email'            => [
				'option'      => esc_html__( 'Email (required)', 'constant-contact-forms' ),
				'placeholder' => esc_html__( 'c.contact@example.com', 'constant-contact-forms' ),
			],
			'first_name'       => [
				'option'      => esc_html__( 'First Name', 'constant-contact-forms' ),
				'placeholder' => esc_html__( 'John', 'constant-contact-forms' ),
			],
			'last_name'        => [
				'option'      => esc_html__( 'Last Name', 'constant-contact-forms' ),
				'placeholder' => esc_html__( 'Smith', 'constant-contact-forms' ),
			],
			'phone_number'     => [
				'option'      => esc_html__( 'Phone Number', 'constant-contact-forms' ),
				'placeholder' => esc_html__( '(555) 272-3342', 'constant-contact-forms' ),
			],
			'address'          => [
				'option'      => esc_html__( 'Address', 'constant-contact-forms' ),
				'placeholder' => esc_html__( '4115 S. Main Rd.', 'constant-contact-forms' ),
			],
			'job_title'        => [
				'option'      => esc_html__( 'Job Title', 'constant-contact-forms' ),
				'placeholder' => esc_html__( 'Project Manager', 'constant-contact-forms' ),
			],
			'company'          => [
				'option'      => esc_html__( 'Company', 'constant-contact-forms' ),
				'placeholder' => esc_html__( 'Acme Manufacturing', 'constant-contact-forms' ),
			],
			'website'          => [
				'option'      => esc_html__( 'Website', 'constant-contact-forms' ),
				'placeholder' => esc_html__( 'https://www.example.com', 'constant-contact-forms' ),
			],
			'custom'           => [
				'option'      => esc_html__( 'Custom Text Field', 'constant-contact-forms' ),
				'placeholder' => esc_html__( 'A custom text field', 'constant-contact-forms' ),
			],
			'custom_text_area' => [
				'option'      => esc_html__( 'Custom Text Area', 'constant-contact-forms' ),
				'placeholder' => esc_html__( 'A large custom text field', 'constant-contact-forms' ),
			],
		];

		/**
		 * Allows filtering the Constant Contact field types to display as an option.
		 *
		 * @since 1.0.0
		 *
		 * @param array $value Array of field types.
		 */
		$this->filtered['options'] = apply_filters( 'constant_contact_field_types', wp_list_pluck( $this->defaults['fields'], 'option' ) );

		/**
		 * Allows filtering of all field placeholders.
		 *
		 * @since 1.2.0
		 *
		 * @param array $default_fields The field placeholders to use for field description.
		 */
		$this->filtered['placeholders'] = apply_filters( 'constant_contact_field_placeholders', wp_list_pluck( $this->defaults['fields'], 'placeholder' ) );

		/**
		 * Allows filtering the default placeholder text to use for fields without a placeholder.
		 *
		 * @since 1.2.0
		 *
		 * @param string $default_placeholder The placeholder text.
		 */
		$this->filtered['placeholders']['default'] = apply_filters( 'constant_contact_default_placeholder', esc_html__( 'A brief description of this field (optional)', 'constant-contact-forms' ) );
	}

	/**
	 * Make placeholder text available to the ctct_form JavaScript.
	 *
	 * @since 1.6.0
	 */
	public function add_placeholders_to_js() {
		wp_add_inline_script( 'ctct_form', 'const ctct_admin_placeholders = ' . json_encode( $this->filtered['placeholders'] ), 'before' );
	}

	/**
	 * Adds CTCT lists to the metabox.
	 *
	 * @since 1.0.0
	 */
	public function constant_contact_list_metabox() {

		$list_metabox = new_cmb2_box(
			[
				'id'           => 'ctct_0_list_metabox',
				'title'        => esc_html__( 'Constant Contact List', 'constant-contact-forms' ),
				'object_types' => [ 'ctct_forms' ],
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true,
			]
		);

		if ( constant_contact()->get_api()->is_connected() ) {

			$lists = $this->get_local_lists();

			if ( empty( $lists ) ) {
				$list_metabox->add_field(
					[
						'name' => esc_html__( 'No Lists Found', 'constant-contact-forms' ),
						'desc' => sprintf(
							'<a href="%s">%s</a>',
							esc_url( admin_url( 'edit.php?post_type=ctct_lists' ) ),
							esc_html__( 'Create a List', 'constant-contact-forms' )
						),
						'type' => 'title',
						'id'   => $this->prefix . 'tip',
					]
				);
			} else {
				$instructions[] = esc_html__( 'Click the plus character to add list. Click the minus character to remove list.', 'constant-contact-forms' );
				$instructions[] = esc_html__( 'Click and drag added lists in "Associated Lists" to reorder. First one listed will be the default.', 'constant-contact-forms' );
				$list_metabox->add_field(
					[
						'name'         => esc_html__( 'Associated lists ', 'constant-contact-forms' ),
						'before'       => esc_html__( 'Allow subscribers to select from chosen lists. ( Add at least one ).', 'constant-contact-forms' ),
						'before_field' => '<p>' . implode( '</p><p>', $instructions ) . '</p>',
						'id'           => $this->prefix . 'list',
						'type'         => 'custom_attached_posts',
						'options'      => [
							'filter_boxes'  => true,
							'query_args'    => [
								'posts_per_page' => -1,
								'post_type'      => 'ctct_lists',
							],
							'hide_selected' => true,
						],
					]
				);
			}
		} else {
			$list_metabox->add_field(
				[
					'name' => esc_html__( 'No connected account', 'constant-contact-forms' ),
					'desc' => esc_html__( 'Please connect to an intended Constant Contact account to start adding items to some lists', 'constant-contact-forms' ),
					'type' => 'title',
					'id'   => $this->prefix . 'no_connection',
				]
			);
		}
	}

	/**
	 * Form description CMB2 metabox.
	 *
	 * @since 1.0.0
	 */
	public function description_metabox() {

		$description_metabox = new_cmb2_box(
			[
				'id'           => 'ctct_0_description_metabox',
				'title'        => esc_html__( 'Form Description', 'constant-contact-forms' ),
				'object_types' => [ 'ctct_forms' ],
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true,
			]
		);

		$description_metabox->add_field(
			[
				'description' => esc_html__( 'This message will display above the form fields, so use it as an opportunity to pitch your email list. Tell visitors why they should subscribe to your emails, focusing on benefits like insider tips, discounts, subscriber coupons, and more.', 'constant-contact-forms' ),
				'id'          => $this->prefix . 'description',
				'type'        => 'wysiwyg',
				'options'     => [
					'media_buttons' => false,
					'textarea_rows' => '5',
					'teeny'         => false,
				],
			]
		);

		$description_metabox->add_field(
			[
				'name'        => esc_html__( 'Description visibility', 'constant-contact-forms' ),
				'id'          => $this->prefix . 'description_visibility',
				'type'        => 'checkbox',
				'description' => esc_html__( 'Hide description on the frontend.', 'constant-contact-forms' ),
			]
		);
	}

	/**
	 * Form options CMB2 metabox.
	 *
	 * @since 1.0.0
	 */
	public function opt_ins_metabox() {

		$options_metabox = new_cmb2_box(
			[
				'id'           => 'ctct_1_optin_metabox',
				'title'        => esc_html__( 'Form Options', 'constant-contact-forms' ),
				'object_types' => [ 'ctct_forms' ],
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true,
			]
		);

		$options_metabox->add_field(
			[
				'name'    => esc_html__( 'Button text', 'constant-contact-forms' ),
				'id'      => $this->prefix . 'button_text',
				'type'    => 'text',
				'default' => esc_attr__( 'Sign up', 'constant-contact-forms' ),
			]
		);

		$options_metabox->add_field(
			[
				'name'    => esc_html__( 'Success message', 'constant-contact-forms' ),
				'id'      => $this->prefix . 'form_submission_success',
				'type'    => 'text',
				'default' => esc_attr__( 'Your information has been submitted', 'constant-contact-forms' ),
			]
		);

		$options_metabox->add_field(
			[
				'name'  => esc_html__( 'Submission behavior', 'constant-contact-forms' ),
				'type'  => 'title',
				'id'    => 'submission_behavior_title',
				'after' => '<hr/>',
			]
		);

		$options_metabox->add_field(
			[
				'name'            => esc_html__( 'Redirect URL', 'constant-contact-forms' ),
				'id'              => $this->prefix . 'redirect_uri',
				'type'            => 'text',
				'description'     => sprintf(
					'%1$s</br><strong>%2$s</strong><br/>%3$s',
					esc_html__( 'Leave blank to keep users on the current page.', 'constant-contact-forms' ),
					esc_html__( 'NOTE: This URL must be within the current site and may not be a direct link to a media file (e.g., a PDF document). Providing a Redirect URL that is outside the current site or is a media file will cause issues with Constant Constact functionality, including contacts not being added to lists successfully.', 'constant-contact-forms' ),
					esc_html__( 'It is recommended to leave this field blank or provide a URL to a page that contains any external or media links within the page content.', 'constant-contact-forms' )
				),
				'sanitization_cb' => 'constant_contact_clean_url',
			]
		);

		$options_metabox->add_field(
			[
				'name'        => esc_html__( 'No page refresh', 'constant-contact-forms' ),
				'id'          => $this->prefix . 'do_ajax',
				'type'        => 'checkbox',
				'description' => esc_html__( 'Enable form submission without a page refresh. This option overrides the Redirect URL choice above.', 'constant-contact-forms' ),
			]
		);

		$captcha_service = new ConstantContact_CaptchaService();
		if ( $captcha_service->is_captcha_enabled() ) {
			$options_metabox->add_field(
				[
					'name'        => esc_html__( 'Disable captcha for this form?', 'constant-contact-forms' ),
					'id'          => $this->prefix . 'disable_recaptcha', // Note: This applies to the currently enabled captcha service, not just reCAPTCHA. Originally, only reCAPTCHA was available.
					'type'        => 'checkbox',
					'description' => esc_html__( 'Checking will disable the currently enabled captcha output for this form. Only valid if using Google reCAPTCHA version 2 or hCaptcha.', 'constant-contact-forms' ),
				]
			);
		}

		$options_metabox->add_field(
			[
				'name'  => esc_html__( 'Spam notice', 'constant-contact-forms' ),
				'type'  => 'title',
				'id'    => 'spam_notice_title',
				'after' => '<hr/>',
			]
		);

		$options_metabox->add_field(
			[
				'name'        => esc_html__( 'Spam Error Message', 'constant-contact-forms' ),
				'id'          => $this->prefix . 'spam_error',
				'type'        => 'text',
				'description' => esc_html__( 'Set the spam error message displayed for this form.', 'constant-contact-forms' ),
			]
		);

		if ( constant_contact()->get_api()->is_connected() ) {
			$this->show_optin_connected_fields( $options_metabox );
		}
	}

	/**
	 * Metabox for user to set custom CSS for a form.
	 *
	 * @since 1.4.0
	 */
	public function custom_form_css_metabox() {
		$custom_css_metabox = new_cmb2_box(
			[
				'id'           => 'ctct_1_custom_form_css_metabox',
				'title'        => esc_html__( 'Form Design', 'constant-contact-forms' ),
				'object_types' => [ 'ctct_forms' ],
				'context'      => 'side',
				'priority'     => 'low',
			]
		);

		$custom_css_metabox->add_field(
			[
				'name'        => esc_html__( 'Background Color', 'constant-contact-forms' ),
				'id'          => $this->prefix . 'form_background_color',
				'type'        => 'colorpicker',
				'description' => esc_html__(
					'Applies to the whole form.',
					'constant-contact-forms'
				),
			]
		);

		$custom_css_metabox->add_field(
			[
				'name' => esc_html__( 'Form Fonts', 'constant-contact-forms' ),
				'type' => 'title',
				'id'   => 'form-description-title',
			]
		);

		$custom_css_metabox->add_field(
			[
				'name'             => esc_html__( 'Font Size', 'constant-contact-forms' ),
				'id'               => $this->prefix . 'form_description_font_size',
				'type'             => 'select',
				'show_option_none' => 'Default',
				'options_cb'       => 'constant_contact_get_font_dropdown_sizes',
				'description'      => esc_html__(
					'Only applies to the form description.',
					'constant-contact-forms'
				),
			]
		);

		$custom_css_metabox->add_field(
			[
				'name'        => esc_html__( 'Font Color', 'constant-contact-forms' ),
				'id'          => $this->prefix . 'form_description_color',
				'type'        => 'colorpicker',
				'description' => esc_html__(
					'Applies to the form description, input labels, and disclosure text.',
					'constant-contact-forms'
				),
			]
		);

		$custom_css_metabox->add_field(
			[
				'name' => esc_html__( 'Form Submit Button', 'constant-contact-forms' ),
				'type' => 'title',
				'id'   => 'form-submit-button-title',
			]
		);

		$custom_css_metabox->add_field(
			[
				'name'             => esc_html__( 'Font Size', 'constant-contact-forms' ),
				'id'               => $this->prefix . 'form_submit_button_font_size',
				'type'             => 'select',
				'show_option_none' => 'Default',
				'options_cb'       => 'constant_contact_get_font_dropdown_sizes',
			]
		);

		$custom_css_metabox->add_field(
			[
				'name'        => esc_html__( 'Font Color', 'constant-contact-forms' ),
				'id'          => $this->prefix . 'form_submit_button_text_color',
				'type'        => 'colorpicker',
				'description' => esc_html__(
					'Choose a color for the submit button text.',
					'constant-contact-forms'
				),
			]
		);

		$custom_css_metabox->add_field(
			[
				'name'        => esc_html__( 'Background Color', 'constant-contact-forms' ),
				'id'          => $this->prefix . 'form_submit_button_background_color',
				'type'        => 'colorpicker',
				'description' => esc_html__(
					'Choose a color for the submit button background.',
					'constant-contact-forms'
				),
			]
		);
	}

	/**
	 * Metabox for user to set custom CSS for a form.
	 *
	 * @since 1.4.0
	 */
	public function custom_input_css_metabox() {
		$custom_css_metabox = new_cmb2_box(
			[
				'id'           => 'ctct_1_custom_input_css_metabox',
				'title'        => esc_html__( 'Input Design', 'constant-contact-forms' ),
				'object_types' => [ 'ctct_forms' ],
				'context'      => 'side',
				'priority'     => 'low',
			]
		);

		$custom_css_metabox->add_field(
			[
				'name'        => esc_html__( 'Form Padding', 'constant-contact-forms' ),
				'type'        => 'title',
				'id'          => 'form-padding-title',
				'description' => esc_html__(
					'Enter padding values in number of pixels. Padding will be applied to four sides of the form.',
					'constant-contact-forms'
				),
			]
		);

		$custom_css_metabox->add_field(
			[
				'name'       => esc_html__( 'Top', 'constant-contact-forms' ),
				'id'         => $this->prefix . 'form_padding_top',
				'type'       => 'text_small',
				'show_names' => true,
				'attributes' => [
					'type' => 'number',
				],
			]
		);

		$custom_css_metabox->add_field(
			[
				'name'       => esc_html__( 'Right', 'constant-contact-forms' ),
				'id'         => $this->prefix . 'form_padding_right',
				'type'       => 'text_small',
				'show_names' => true,
				'attributes' => [
					'type' => 'number',
				],
			]
		);

		$custom_css_metabox->add_field(
			[
				'name'       => esc_html__( 'Bottom', 'constant-contact-forms' ),
				'id'         => $this->prefix . 'form_padding_bottom',
				'type'       => 'text_small',
				'show_names' => true,
				'attributes' => [
					'type' => 'number',
				],
			]
		);

		$custom_css_metabox->add_field(
			[
				'name'       => esc_html__( 'Left', 'constant-contact-forms' ),
				'id'         => $this->prefix . 'form_padding_left',
				'type'       => 'text_small',
				'show_names' => true,
				'attributes' => [
					'type' => 'number',
				],
			]
		);

		$custom_css_metabox->add_field(
			[
				'name'        => esc_html__( 'Custom Classes', 'constant-contact-forms' ),
				'id'          => $this->prefix . 'input_custom_classes',
				'type'        => 'text',
				'description' => esc_html__(
					'Set custom CSS class(es) for inputs. Separate multiple classes with spaces.',
					'constant-contact-forms'
				),
			]
		);

		$custom_css_metabox->add_field(
			[
				'name'             => esc_html__( 'Label Placement', 'constant-contact-forms' ),
				'id'               => $this->prefix . 'form_label_placement',
				'type'             => 'select',
				'show_option_none' => esc_html__( 'Global', 'constant-contact-forms' ),
				'options'          => [
					'top'    => esc_html__( 'Top', 'constant-contact-forms' ),
					'left'   => esc_html__( 'Left', 'constant-contact-forms' ),
					'bottom' => esc_html__( 'Bottom', 'constant-contact-forms' ),
					'right'  => esc_html__( 'Right', 'constant-contact-forms' ),
					'hidden' => esc_html__( 'Hidden', 'constant-contact-forms' ),
				],
				'description'      => esc_html__(
					'Set the position for labels for inputs.',
					'constant-contact-forms'
				),
			]
		);
	}

	/**
	 * Helper method to show our connected optin fields.
	 *
	 * @since 1.0.0
	 *
	 * @param object $options_metabox CMB2 options metabox object.
	 */
	public function show_optin_connected_fields( object $options_metabox ) {

		$overall_description = sprintf(
			'<hr/><p>%s %s</p>',
			esc_html__(
				'Enabling this option will require users to check a box to be added to your list.',
				'constant-contact-forms'
			),
			sprintf(
				'<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
				'https://knowledgebase.constantcontact.com/articles/KnowledgeBase/18260-WordPress-Constant-Contact-Forms-Options',
				esc_html__( 'Learn more', 'constant-contact-forms' )
			)
		);

		$options_metabox->add_field(
			[
				'name'  => esc_html__( 'Email opt-in', 'constant-contact-forms' ),
				'type'  => 'title',
				'id'    => 'email-optin-title',
				'after' => $overall_description,
			]
		);

		$this->show_enable_show_checkbox_field( $options_metabox );
		$this->show_affirmation_field( $options_metabox );
	}

	/**
	 * Helper method to show our show/hide checkbox field.
	 *
	 * @since 1.0.0
	 *
	 * @param object $options_metabox CMB2 options metabox object.
	 */
	public function show_enable_show_checkbox_field( object $options_metabox ) {

		$description  = esc_html__( 'Add a checkbox so subscribers can opt-in to your email list.', 'constant-contact-forms' );
		$description .= '<br>';
		$description .= esc_html__( '(For use with Contact Us form)', 'constant-contact-forms' );

		$options_metabox->add_field(
			[
				'name'        => esc_html__( 'Opt-in checkbox', 'constant-contact-forms' ),
				'id'          => $this->prefix . 'opt_in',
				'description' => $description,
				'type'        => 'checkbox',
			]
		);
	}

	/**
	 * Helper method to show our affirmation textarea field.
	 *
	 * @since 1.0.0
	 *
	 * @param object $options_metabox CMB2 options metabox object.
	 */
	public function show_affirmation_field( object $options_metabox ) {

		$business_name = get_bloginfo( 'name' );
		$business_name = ( $business_name ) ?: esc_html__( 'Your Business Name', 'constant-contact-forms' );

		$options_metabox->add_field(
			[
				'name'    => esc_html__( 'Opt-in Affirmation', 'constant-contact-forms' ),
				'id'      => $this->prefix . 'opt_in_instructions',
				'type'    => 'textarea_small',
				// translators: placeholder has a business name from Constant Contact.
				'default' => sprintf( esc_html__( 'Example: Yes, I would like to receive emails from %s. (You can unsubscribe anytime)', 'constant-contact-forms' ), $business_name ),
			]
		);
	}

	/**
	 * Fields builder CMB2 metabox.
	 *
	 * @since 1.0.0
	 */
	public function fields_metabox() {

		$fields_metabox = new_cmb2_box(
			[
				'id'           => 'ctct_2_fields_metabox',
				'title'        => esc_html__( 'Form Fields', 'constant-contact-forms' ),
				'object_types' => [ 'ctct_forms' ],
				'context'      => 'normal',
				'priority'     => 'low',
				'show_names'   => true,
			]
		);

		$fields_metabox->add_field(
			[
				'name'        => esc_html__( 'Add Fields', 'constant-contact-forms' ),
				/**
				 * No birthdays or anniversarys in CC API V2, keeping this for later.
				 * "You can also collect birthday and anniversary dates to use with Constant Contact autoresponders! "
				 *
				 * @since 1.0.2
				 */
				'description' => esc_html__( 'Create a field for each piece of information you want to collect. Good basics include email address, first name, and last name.', 'constant-contact-forms' ),
				'id'          => $this->prefix . 'title',
				'type'        => 'title',
			]
		);

		$custom_group = $fields_metabox->add_field(
			[
				'id'          => 'custom_fields_group',
				'type'        => 'group',
				'repeatable'  => true,
				'options'     => [
					'group_title'   => esc_html__( 'Field {#}', 'constant-contact-forms' ),
					'add_button'    => esc_html__( 'Add Another Field', 'constant-contact-forms' ),
					'remove_button' => esc_html__( 'Remove Field', 'constant-contact-forms' ),
					'sortable'      => true,
				],
				'after_group' => [ $this, 'unique_label_messaging' ],
			]
		);

		$fields_metabox->add_group_field(
			$custom_group,
			[
				'name'             => esc_html__( 'Select a Field', 'constant-contact-forms' ),
				'id'               => $this->prefix . 'map_select',
				'type'             => 'select',
				'show_option_none' => false,
				'default'          => 'email',
				'row_classes'      => 'map',
				'options'          => $this->filtered['options'],
			]
		);

		$fields_metabox->add_group_field(
			$custom_group,
			[
				'name'    => esc_html__( 'Field Label', 'constant-contact-forms' ),
				'id'      => $this->prefix . 'field_label',
				'type'    => 'text',
				'default' => '',
				'after'   => '<span class="ctct-warning"><span class="dashicons dashicons-warning"></span>' . esc_html__( 'Field label is not unique', 'constant-contact-forms' ) . '</span>',
			]
		);

		$fields_metabox->add_group_field(
			$custom_group,
			[
				'name'       => esc_html__( 'Field Description', 'constant-contact-forms' ),
				'id'         => $this->prefix . 'field_desc',
				'type'       => 'text',
				'attributes' => [
					'placeholder' => esc_html__( 'Ex: Enter email address', 'constant-contact-forms' ),
				],
			]
		);

		$fields_metabox->add_group_field(
			$custom_group,
			[
				'name'        => esc_html__( 'Required', 'constant-contact-forms' ),
				'id'          => $this->prefix . 'required_field',
				'type'        => 'checkbox',
				'row_classes' => 'required',
			]
		);

	}

	/**
	 * Add messaging about custom fields needing unique labels.
	 *
	 * @since 2.0.0
	 *
	 * @param array      $field_args
	 * @param CMB2_Field $field
	 */
	public function unique_label_messaging( array $field_args, CMB2_Field $field ) {
		printf(
			'<p>%s</p>',
			esc_html__( '"Custom Text Field" labels are used for custom fields and need to be unique.', 'constant-contact-forms' )
		);
		printf(
			'<p>%s</p>',
			sprintf(
				// translators: Placeholders are for html link markup.
				esc_html__( 'Custom fields created within Constant Contact with field type "%1$sdate field%2$s" are not supported.', 'constant-contact-forms' ),
				'<a target="_blank" rel="noopener" href="' . esc_url( 'https://knowledgebase.constantcontact.com/email-digital-marketing/articles/KnowledgeBase/33120-Create-and-Manage-Custom-Contact-Fields?lang=en_US' ) . '">',
				'</a>'
			)
		);
	}

	/**
	 * Show a metabox rendering our shortcode.
	 *
	 * @since 1.1.0
	 */
	public function generated_shortcode() {
		$generated = new_cmb2_box(
			[
				'id'           => 'ctct_2_generated_metabox',
				'title'        => esc_html__( 'Embed Shortcode', 'constant-contact-forms' ),
				'object_types' => [ 'ctct_forms' ],
				'context'      => 'side',
				'priority'     => 'low',
				'show_names'   => true,
			]
		);

		$generated->add_field(
			[
				'name'       => esc_html__( 'Shortcode to use', 'constant-contact-forms' ),
				'id'         => $this->prefix . 'generated_shortcode',
				'type'       => 'text_medium',
				'desc'       => sprintf(
					/* Translators: Placeholders here represent `<em>` and `<strong>` HTML tags. */
					esc_html__( '%1$sCopy and paste wherever shortcodes are supported.%2$s', 'constant-contact-forms' ),
					'<small><em>',
					'</em></small>',
				),
				'default'    => ( $generated->object_id > 0 ) ? '[ctct form="' . $generated->object_id . '" show_title="false"]' : '',
				'attributes' => [
					'readonly' => 'readonly',
				],
			]
		);
	}

	/**
	 * Add a metabox for customizing destination email for a given form.
	 *
	 * @since 1.4.0
	 */
	public function email_settings() {

		$email_settings = new_cmb2_box(
			[
				'id'           => 'email_settings',
				'title'        => esc_html__( 'Email settings', 'constant-contact-forms' ),
				'object_types' => [ 'ctct_forms' ],
				'context'      => 'side',
				'priority'     => 'low',
			]
		);

		$email_settings->add_field(
			[
				'name' => esc_html__( 'Email recipients', 'constant-contact-forms' ),
				'desc' => esc_html__( 'Addresses that should receive notifications for the form. Separate multiple emails by a comma, leave blank to default to admin email.', 'constant-contact-forms' ),
				'id'   => $this->prefix . 'email_settings',
				'type' => 'text_medium',
			]
		);

		$email_settings->add_field(
			[
				'name'       => esc_html__( 'Disable form\'s email notifications', 'constant-contact-forms' ),
				'desc'       => esc_html__( 'Check this option to disable emails for this form.', 'constant-contact-forms' ),
				'id'         => $this->prefix . 'disable_emails_for_form',
				'type'       => 'checkbox',
				'show_on_cb' => 'constant_contact_should_hide_disable_admin_email'
			]
		);
	}

	/**
	 * Add a metabox for address settings.
	 *
	 * @since 2.3.0
	 */
	public function address_settings() {

		$address_settings = new_cmb2_box(
			[
				'id'           => 'address_settings',
				'title'        => esc_html__( 'Address Fields settings', 'constant-contact-forms' ),
				'object_types' => [ 'ctct_forms' ],
				'context'      => 'side',
				'priority'     => 'low',
				'show_on_cb'   => [ $this, 'show_address_metabox' ],
			]
		);

		$address_settings->add_field(
			[
				'before'            => '<p>' . esc_html__( 'Select which fields to include and which to require:', 'constant-contact-forms' ) . '</p>',
				'name'              => esc_html__( 'Include:', 'constant-contact-forms' ),
				'id'                => $this->prefix . 'address_fields_include',
				'type'              => 'multicheck',
				'select_all_button' => false,
				'options_cb'        => [ $this, 'get_individual_address_fields' ],
			]
		);

		$address_settings->add_field(
			[
				'name'              => esc_html__( 'Require:', 'constant-contact-forms' ),
				'id'                => $this->prefix . 'address_fields_require',
				'type'              => 'multicheck',
				'select_all_button' => false,
				'options_cb'        => [ $this, 'get_individual_address_fields' ],
			]
		);
	}

	/**
	 * Callback to determine if we should show the address metabox.
	 *
	 * @since 2.3.0
	 *
	 * @param CMB2 $cmb
	 * @return bool
	 */
	public function show_address_metabox( CMB2 $cmb ) : bool {
		$data = get_post_meta( $cmb->object_id(), 'custom_fields_group', true );
		if ( empty( $data ) ) {
			return false;
		}

		$fields = wp_list_pluck( $data, '_ctct_map_select' );

		return in_array( 'address', $fields, true );
	}

	/**
	 * Return an array of individual address fields.
	 *
	 * @since 2.3.0
	 *
	 * @return array
	 */
	public function get_individual_address_fields() : array {
		return [
			'country' => esc_html__( 'Country', 'constant-contact-forms' ),
			'street'  => esc_html__( 'Street', 'constant-contact-forms' ),
			'city'    => esc_html__( 'City', 'constant-contact-forms' ),
			'state'   => esc_html__( 'State/Province', 'constant-contact-forms' ),
			'zip'     => esc_html__( 'Postal Code', 'constant-contact-forms' ),
		];
	}

	/**
	 * Render the metabox for resetting style fields.
	 *
	 * @since 1.5.0
	 */
	public function add_css_reset_metabox() {

		$reset_css_metabox = new_cmb2_box(
			[
				'id'           => 'ctct_3_reset_css_metabox',
				'title'        => esc_html__( 'Reset Styles', 'constant-contact-forms' ),
				'object_types' => [ 'ctct_forms' ],
				'context'      => 'side',
				'priority'     => 'low',
			]
		);

		$reset_css_metabox->add_field(
			[
				'id'          => $this->prefix . 'reset_styles',
				'type'        => 'reset_css_button',
				'title'       => esc_html__( 'Reset', 'constant-contact-forms' ),
				'description' => esc_html__(
					'Reset the styles for this Form.',
					'constant-contact-forms'
				),
			]
		);
	}

	/**
	 * Render the Reset Style button.
	 *
	 * @since 1.5.0
	 *
	 * @param object $field The CMB2 field object.
	 */
	public function render_reset_css_button( object $field ) {
		?>
			<button type="button" id="ctct-reset-css" class="button">
				<?php esc_html_e( 'Reset', 'constant-contact-forms' ); ?>
			</button>

			<p>
				<em><?php echo esc_html( $field->args['description'] ); ?></em>
			</p>
		<?php
	}

	/**
	 * Returns available lists that are available locally.
	 *
	 * @author Scott Anderson <scott.anderson@webdevstudios.com>
	 * @since  1.12.0
	 *
	 * @return array
	 */
	private function get_local_lists() : array {

		$args = [
			'post_type'              => 'ctct_lists',
			'posts_per_page'         => 1000, // phpcs:ignore WordPress.WP.PostsPerPage
			'orderby'                => 'title',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
		];

		$lists = get_posts( $args );

		$formatted_lists = [];

		foreach ( $lists as $list ) {
			$form_id                     = get_post_meta( $list->ID, '_ctct_list_id', true );
			$formatted_lists[ $form_id ] = $list->post_title;
		}

		return $formatted_lists;
	}
}

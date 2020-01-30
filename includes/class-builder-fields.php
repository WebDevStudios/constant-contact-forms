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
	protected $plugin;

	/**
	 * Prefix for our meta fields/boxes.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $prefix = '_ctct_';

	/**
	 * Default option and placeholder values for the fields.
	 *
	 * @since 1.6.0
	 *
	 * @var array
	 */
	protected $defaults = [];

	/**
	 * The default option and placeholder values for the fields after being run through filters.
	 *
	 * @since 1.6.0
	 *
	 * @var array
	 */
	protected $filtered = [];

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $plugin Parent class object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->init();
		$this->init_field_defaults();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'init', [ $this, 'hooks' ] );
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		global $pagenow;

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

		if ( $pagenow && in_array( $pagenow, $form_builder_pages, true ) ) {
			add_action( 'cmb2_admin_init', [ $this, 'description_metabox' ] );
			add_action( 'cmb2_admin_init', [ $this, 'constant_contact_list_metabox' ] );
			add_action( 'cmb2_admin_init', [ $this, 'opt_ins_metabox' ] );
			add_action( 'cmb2_admin_init', [ $this, 'generated_shortcode' ] );
			add_action( 'cmb2_admin_init', [ $this, 'email_settings' ] );
			add_action( 'cmb2_admin_init', [ $this, 'custom_form_css_metabox' ] );
			add_action( 'cmb2_admin_init', [ $this, 'custom_input_css_metabox' ] );
			add_action( 'cmb2_admin_init', [ $this, 'fields_metabox' ] );
			add_action( 'cmb2_admin_init', [ $this, 'add_css_reset_metabox' ] );
			add_filter( 'cmb2_override__ctct_generated_shortcode_meta_save', '__return_empty_string' );
			add_action( 'cmb2_render_reset_css_button', [ $this, 'render_reset_css_button' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'add_placeholders_to_js' ] );
		}

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
				'placeholder' => esc_html__( 'http://www.example.com', 'constant-contact-form' ),
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
		wp_localize_script( 'ctct_form', 'ctct_admin_placeholders', $this->filtered['placeholders'] );
	}

	/**
	 * Adds CTCT lists to the metabox.
	 *
	 * @since 1.0.0
	 */
	public function constant_contact_list_metabox() {

		if ( constant_contact()->api->is_connected() ) {
			$list_metabox = new_cmb2_box( [
				'id'           => 'ctct_0_list_metabox',
				'title'        => esc_html__( 'Constant Contact List', 'constant-contact-forms' ),
				'object_types' => [ 'ctct_forms' ],
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true,
			] );

			$lists = $this->plugin->builder->get_lists();

			if ( $lists ) {
				$list_metabox->add_field( [
					'name'             => esc_html__( 'Add subscribers to', 'constant-contact-forms' ),
					'id'               => $this->prefix . 'list',
					'type'             => 'select',
					'show_option_none' => esc_html__( 'No List Selected', 'constant-contact-forms' ),
					'default'          => 'none',
					'options'          => $lists,
				] );
			}
		}
	}

	/**
	 * Form description CMB2 metabox.
	 *
	 * @since 1.0.0
	 */
	public function description_metabox() {

		$description_metabox = new_cmb2_box( [
			'id'           => 'ctct_0_description_metabox',
			'title'        => esc_html__( 'Form Description', 'constant-contact-forms' ),
			'object_types' => [ 'ctct_forms' ],
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
		] );

		$description_metabox->add_field( [
			'description' => esc_html__( 'This message will display above the form fields, so use it as an opportunity to pitch your email list. Tell visitors why they should subscribe to your emails, focusing on benefits like insider tips, discounts, subscriber coupons, and more.', 'constant-contact-forms' ),
			'id'          => $this->prefix . 'description',
			'type'        => 'wysiwyg',
			'options'     => [
				'media_buttons' => false,
				'textarea_rows' => '5',
				'teeny'         => false,
			],
		] );
	}

	/**
	 * Form options CMB2 metabox.
	 *
	 * @since 1.0.0
	 */
	public function opt_ins_metabox() {

		$options_metabox = new_cmb2_box( [
			'id'           => 'ctct_1_optin_metabox',
			'title'        => esc_html__( 'Form Options', 'constant-contact-forms' ),
			'object_types' => [ 'ctct_forms' ],
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
		] );

		$options_metabox->add_field( [
			'name'    => esc_html__( 'Button text', 'constant-contact-forms' ),
			'id'      => $this->prefix . 'button_text',
			'type'    => 'text',
			'default' => esc_attr__( 'Sign up', 'constant-contact-forms' ),
		] );

		$options_metabox->add_field( [
			'name'    => esc_html__( 'Success message', 'constant-contact-forms' ),
			'id'      => $this->prefix . 'form_submission_success',
			'type'    => 'text',
			'default' => esc_attr__( 'Your information has been submitted', 'constant-contact-forms' ),
		] );

		$options_metabox->add_field( [
			'name'  => esc_html__( 'Submission behavior', 'constant-contact-forms' ),
			'type'  => 'title',
			'id'    => 'submission_behavior_title',
			'after' => '<hr/>',
		] );

		$options_metabox->add_field( [
			'name'            => esc_html__( 'Redirect URL', 'constant-contact-forms' ),
			'id'              => $this->prefix . 'redirect_uri',
			'type'            => 'text',
			'description'     => sprintf(
				/* Translators: 1: basic field info, 2: warning about invalid values, 3: recommended field value */
				'%1$s</br><strong>%2$s</strong><br/>%3$s',
				esc_html__( 'Leave blank to keep users on the current page.', 'constant-contact-forms' ),
				esc_html__( 'NOTE: This URL must be within the current site and may not be a direct link to a media file (e.g., a PDF document). Providing a Redirect URL that is outside the current site or is a media file will cause issues with Constant Constact functionality, including contacts not being added to lists successfully.', 'constant-contact-forms' ),
				esc_html__( 'It is recommended to leave this field blank or provide a URL to a page that contains any external or media links within the page content.', 'constant-contact-forms' )
			),
			'sanitization_cb' => 'constant_contact_clean_url',
		] );

		$options_metabox->add_field( [
			'name'        => esc_html__( 'No page refresh', 'constant-contact-forms' ),
			'id'          => $this->prefix . 'do_ajax',
			'type'        => 'checkbox',
			'description' => esc_html__( 'Enable form submission without a page refresh. This option overrides the Redirect URL choice above.', 'constant-contact-forms' ),
		] );

		if ( ConstantContact_reCAPTCHA::has_recaptcha_keys() ) {
			$options_metabox->add_field( [
				'name'        => esc_html__( 'Disable Google reCAPTCHA for this form?', 'constant-contact-forms' ),
				'id'          => $this->prefix . 'disable_recaptcha',
				'type'        => 'checkbox',
				'description' => esc_html__( "Checking will disable Google's reCAPTCHA output for this form. Only valid if using Google reCAPTCHA version 2", 'constant-contact-forms' ),
			] );
		}

		$options_metabox->add_field( [
			'name'  => esc_html__( 'Spam notice', 'constant-contact-forms' ),
			'type'  => 'title',
			'id'    => 'spam_notice_title',
			'after' => '<hr/>',
		] );

		$options_metabox->add_field( [
			'name'        => esc_html__( 'Spam Error Message', 'constant-contact-forms' ),
			'id'          => $this->prefix . 'spam_error',
			'type'        => 'text',
			'description' => esc_html__( 'Set the spam error message displayed for this form.', 'constant-contact-forms' ),
		] );

		if ( constant_contact()->api->is_connected() ) {
			$this->show_optin_connected_fields( $options_metabox );
		}
	}

	/**
	 * Metabox for user to set custom CSS for a form.
	 *
	 * @since 1.4.0
	 */
	public function custom_form_css_metabox() {
		$custom_css_metabox = new_cmb2_box( [
			'id'           => 'ctct_1_custom_form_css_metabox',
			'title'        => esc_html__( 'Form Design', 'constant-contact-forms' ),
			'object_types' => [ 'ctct_forms' ],
			'context'      => 'side',
			'priority'     => 'low',
		] );

		$custom_css_metabox->add_field( [
			'name'        => esc_html__( 'Background Color', 'constant-contact-forms' ),
			'id'          => $this->prefix . 'form_background_color',
			'type'        => 'colorpicker',
			'description' => esc_html__(
				'Applies to the whole form.',
				'constant-contact-forms'
			),
		] );

		$custom_css_metabox->add_field( [
			'name' => esc_html__( 'Form Fonts', 'constant-contact-forms' ),
			'type' => 'title',
			'id'   => 'form-description-title',
		] );

		$custom_css_metabox->add_field( [
			'name'             => esc_html__( 'Font Size', 'constant-contact-forms' ),
			'id'               => $this->prefix . 'form_description_font_size',
			'type'             => 'select',
			'show_option_none' => 'Default',
			'options_cb'       => 'constant_contact_get_font_dropdown_sizes',
			'description'      => esc_html__(
				'Only applies to the form description.',
				'constant-contact-forms'
			),
		] );

		$custom_css_metabox->add_field( [
			'name'        => esc_html__( 'Font Color', 'constant-contact-forms' ),
			'id'          => $this->prefix . 'form_description_color',
			'type'        => 'colorpicker',
			'description' => esc_html__(
				'Applies to the form description, input labels, and disclosure text.',
				'constant-contact-forms'
			),
		] );

		$custom_css_metabox->add_field( [
			'name' => esc_html__( 'Form Submit Button', 'constant-contact-forms' ),
			'type' => 'title',
			'id'   => 'form-submit-button-title',
		] );

		$custom_css_metabox->add_field( [
			'name'             => esc_html__( 'Font Size', 'constant-contact-forms' ),
			'id'               => $this->prefix . 'form_submit_button_font_size',
			'type'             => 'select',
			'show_option_none' => 'Default',
			'options_cb'       => 'constant_contact_get_font_dropdown_sizes',
		] );

		$custom_css_metabox->add_field( [
			'name'        => esc_html__( 'Font Color', 'constant-contact-forms' ),
			'id'          => $this->prefix . 'form_submit_button_text_color',
			'type'        => 'colorpicker',
			'description' => esc_html__(
				'Choose a color for the submit button text.',
				'constant-contact-forms'
			),
		] );

		$custom_css_metabox->add_field( [
			'name'        => esc_html__( 'Background Color', 'constant-contact-forms' ),
			'id'          => $this->prefix . 'form_submit_button_background_color',
			'type'        => 'colorpicker',
			'description' => esc_html__(
				'Choose a color for the submit button background.',
				'constant-contact-forms'
			),
		] );
	}

	/**
	 * Metabox for user to set custom CSS for a form.
	 *
	 * @since 1.4.0
	 */
	public function custom_input_css_metabox() {
		$custom_css_metabox = new_cmb2_box( [
			'id'           => 'ctct_1_custom_input_css_metabox',
			'title'        => esc_html__( 'Input Design', 'constant-contact-forms' ),
			'object_types' => [ 'ctct_forms' ],
			'context'      => 'side',
			'priority'     => 'low',
		] );

		$custom_css_metabox->add_field( [
			'name'        => esc_html__( 'Form Padding', 'constant-contact-forms' ),
			'type'        => 'title',
			'id'          => 'form-padding-title',
			'description' => esc_html__(
				'Enter padding values in number of pixels. Padding will be applied to four sides of the form.',
				'constant-contact-form' ),
		] );

		$custom_css_metabox->add_field( [
			'name'       => esc_html__( 'Top', 'constant-contact-forms' ),
			'id'         => $this->prefix . 'form_padding_top',
			'type'       => 'text_small',
			'show_names' => true,
			'attributes' => [
				'type' => 'number',
			],
		] );

		$custom_css_metabox->add_field( [
			'name'       => esc_html__( 'Right', 'constant-contact-forms' ),
			'id'         => $this->prefix . 'form_padding_right',
			'type'       => 'text_small',
			'show_names' => true,
			'attributes' => [
				'type' => 'number',
			],
		] );

		$custom_css_metabox->add_field( [
			'name'       => esc_html__( 'Bottom', 'constant-contact-forms' ),
			'id'         => $this->prefix . 'form_padding_bottom',
			'type'       => 'text_small',
			'show_names' => true,
			'attributes' => [
				'type' => 'number',
			],
		] );

		$custom_css_metabox->add_field( [
			'name'       => esc_html__( 'Left', 'constant-contact-forms' ),
			'id'         => $this->prefix . 'form_padding_left',
			'type'       => 'text_small',
			'show_names' => true,
			'attributes' => [
				'type' => 'number',
			],
		] );

		$custom_css_metabox->add_field( [
			'name'        => esc_html__( 'Custom Classes', 'constant-contact-forms' ),
			'id'          => $this->prefix . 'input_custom_classes',
			'type'        => 'text',
			'description' => esc_html__(
				'Set custom CSS class(es) for inputs. Separate multiple classes with spaces.',
				'constant-contact-forms'
			),
		] );

		$custom_css_metabox->add_field( [
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
		] );
	}

	/**
	 * Helper method to show our connected optin fields.
	 *
	 * @since 1.0.0
	 *
	 * @param object $options_metabox CMB2 options metabox object.
	 */
	public function show_optin_connected_fields( $options_metabox ) {

		$overall_description = sprintf(
			'<hr/><p>%s %s</p>',
			esc_html__(
				'Enabling this option will require users to check a box to be added to your list.',
				'constant-contact-forms'
			),
			sprintf(
				'<a href="%s" target="_blank">%s</a>',
				'https://knowledgebase.constantcontact.com/articles/KnowledgeBase/18260-WordPress-Constant-Contact-Forms-Options',
				esc_html__( 'Learn more', 'constant-contact-forms' )
			)
		);

		$options_metabox->add_field( [
			'name'  => esc_html__( 'Email opt-in', 'constant-contact-forms' ),
			'type'  => 'title',
			'id'    => 'email-optin-title',
			'after' => $overall_description,
		] );

		$this->show_enable_show_checkbox_field( $options_metabox );
		$this->show_affirmation_field( $options_metabox );
	}

	/**
	 * Helper method to show our non connected optin fields.
	 *
	 * @since 1.0.0
	 *
	 * @param object $options_metabox CMB2 options metabox object.
	 */
	public function show_optin_not_connected_fields( $options_metabox ) {

		$options_metabox->add_field( [
			'name'        => esc_html__( 'Enable email subscriber opt-in', 'constant-contact-forms' ),
			'id'          => $this->prefix . 'opt_in_not_connected',
			'description' => esc_html__( 'Adds an opt-in to the bottom of your form.', 'constant-contact-forms' ),
			'type'        => 'checkbox',
			'attributes'  => [
				'disabled' => 'disabled',
			],
		] );

		$this->show_affirmation_field( $options_metabox );
	}

	/**
	 * Helper method to show our show/hide checkbox field.
	 *
	 * @since 1.0.0
	 *
	 * @param object $options_metabox CMB2 options metabox object.
	 */
	public function show_enable_show_checkbox_field( $options_metabox ) {

		$description  = esc_html__( 'Add a checkbox so subscribers can opt-in to your email list.', 'constant-contact-forms' );
		$description .= '<br>';
		$description .= esc_html__( '(For use with Contact Us form)', 'constant-contact-forms' );

		$options_metabox->add_field( [
			'name'        => esc_html__( 'Opt-in checkbox', 'constant-contact-forms' ),
			'id'          => $this->prefix . 'opt_in',
			'description' => $description,
			'type'        => 'checkbox',
		] );
	}

	/**
	 * Helper method to show our affirmation textarea field.
	 *
	 * @since 1.0.0
	 *
	 * @param object $options_metabox CMB2 options metabox object.
	 */
	public function show_affirmation_field( $options_metabox ) {

		$business_name = get_bloginfo( 'name' );
		$business_name ? ( $business_name ) : __( 'Your Business Name', 'constant-contact-forms' );

		$options_metabox->add_field( [
			'name'    => esc_html__( 'Opt-in Affirmation', 'constant-contact-forms' ),
			'id'      => $this->prefix . 'opt_in_instructions',
			'type'    => 'textarea_small',
			// translators: placeholder has a business name from Constant Contact.
			'default' => sprintf( __( 'Example: Yes, I would like to receive emails from %s. (You can unsubscribe anytime)', 'constant-contact-forms' ), $business_name ),
		] );
	}

	/**
	 * Fields builder CMB2 metabox.
	 *
	 * @since 1.0.0
	 */
	public function fields_metabox() {

		$fields_metabox = new_cmb2_box( [
			'id'           => 'ctct_2_fields_metabox',
			'title'        => esc_html__( 'Form Fields', 'constant-contact-forms' ),
			'object_types' => [ 'ctct_forms' ],
			'context'      => 'normal',
			'priority'     => 'low',
			'show_names'   => true,
		] );

		$fields_metabox->add_field( [
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
		] );

		$custom_group = $fields_metabox->add_field( [
			'id'         => 'custom_fields_group',
			'type'       => 'group',
			'repeatable' => true,
			'options'    => [
				'group_title'   => esc_html__( 'Field {#}', 'constant-contact-forms' ),
				'add_button'    => esc_html__( 'Add Another Field', 'constant-contact-forms' ),
				'remove_button' => esc_html__( 'Remove Field', 'constant-contact-forms' ),
				'sortable'      => true,
			],
		] );

		$fields_metabox->add_group_field( $custom_group, [
			'name'             => esc_html__( 'Select a Field', 'constant-contact-forms' ),
			'id'               => $this->prefix . 'map_select',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => 'email',
			'row_classes'      => 'map',
			'options'          => $this->filtered['options'],
		] );

		$fields_metabox->add_group_field( $custom_group, [
			'name'    => esc_html__( 'Field Label', 'constant-contact-forms' ),
			'id'      => $this->prefix . 'field_label',
			'type'    => 'text',
			'default' => '',
		] );

		$fields_metabox->add_group_field( $custom_group, [
			'name'       => esc_html__( 'Field Description', 'constant-contact-forms' ),
			'id'         => $this->prefix . 'field_desc',
			'type'       => 'text',
			'attributes' => [
				'placeholder' => esc_html__( 'Ex: Enter email address', 'constant-contact-forms' ),
			],
		] );

		$fields_metabox->add_group_field( $custom_group, [
			'name'        => esc_html__( 'Required', 'constant-contact-forms' ),
			'id'          => $this->prefix . 'required_field',
			'type'        => 'checkbox',
			'row_classes' => 'required',
		] );

	}

	/**
	 * Show a metabox rendering our shortcode.
	 *
	 * @since 1.1.0
	 */
	public function generated_shortcode() {
		$generated = new_cmb2_box( [
			'id'           => 'ctct_2_generated_metabox',
			'title'        => esc_html__( 'Shortcode', 'constant-contact-forms' ),
			'object_types' => [ 'ctct_forms' ],
			'context'      => 'side',
			'priority'     => 'low',
			'show_names'   => true,
		] );

		$generated->add_field( [
			'name'       => esc_html__( 'Shortcode to use', 'constant-contact-forms' ),
			'id'         => $this->prefix . 'generated_shortcode',
			'type'       => 'text_medium',
			'desc'       => sprintf(
				/* Translators: Placeholders here represent `<em>` and `<strong>` HTML tags. */
				esc_html__( 'Shortcode to embed — %1$s%2$sYou can copy and paste this in a post to display your form.%3$s%4$s', 'constant-contact-forms' ),
				'<small>',
				'<em>',
				'</em>',
				'</small>'
			),
			'default'    => ( $generated->object_id > 0 ) ? '[ctct form="' . $generated->object_id . '" show_title="false"]' : '',
			'attributes' => [
				'readonly' => 'readonly',
			],
		] );
	}

	/**
	 * Add a metabox for customizing destination email for a given form.
	 *
	 * @since 1.4.0
	 */
	public function email_settings() {

		$email_settings = new_cmb2_box( [
			'id'           => 'email_settings',
			'title'        => esc_html__( 'Email settings', 'constant-contact-forms' ),
			'object_types' => [ 'ctct_forms' ],
			'context'      => 'side',
			'priority'     => 'low',
		] );

		$email_settings->add_field( [
			'name' => esc_html__( 'Email destination', 'constant-contact-forms' ),
			'desc' => esc_html__( 'Who should receive email notifications for this form. Separate multiple emails by a comma. Leave blank to default to admin email.', 'constant-contact-forms' ),
			'id'   => $this->prefix . 'email_settings',
			'type' => 'text_medium',
		] );

		$email_settings->add_field( [
			'name' => esc_html__( 'Disable email notifications for this form?', 'constant-contact-forms' ),
			'desc' => esc_html__( 'Check this option to disable emails for this Constant Contact Forms form.', 'constant-contact-forms' ),
			'id'   => $this->prefix . 'disable_emails_for_form',
			'type' => 'checkbox',
		] );
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
	 * @param object $field The CMB2 field object.
	 */
	public function render_reset_css_button( $field ) {
		?>
			<button type="button" id="ctct-reset-css" class="button">
				<?php esc_html_e( 'Reset', 'constant-contact-forms' ); ?>
			</button>

			<p>
				<em><?php echo esc_html( $field->args['description'] ); ?></em>
			</p>
		<?php
	}
}

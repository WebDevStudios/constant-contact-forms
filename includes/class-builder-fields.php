<?php
/**
 * Builder fields.
 *
 * @package ConstantContact
 * @subpackage BuilderFields
 * @author Constant Contact
 * @since 1.0.0
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
	 * @var object
	 */
	protected $plugin = null;

	/**
	 * Prefix for our meta fields/boxes.
	 *
	 * @var string
	 */
	public $prefix = '_ctct_';

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
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'init', array( $this, 'hooks' ) );
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
			array( 'post-new.php', 'post.php' )
		);

		// Only load the cmb2 fields on our specified pages.
		if ( $pagenow && in_array( $pagenow, $form_builder_pages, true ) ) {

			add_action( 'cmb2_admin_init', array( $this, 'description_metabox' ) );
			add_action( 'cmb2_admin_init', array( $this, 'opt_ins_metabox' ) );
			add_action( 'cmb2_admin_init', array( $this, 'fields_metabox' ) );
			add_action( 'cmb2_admin_init', array( $this, 'generated_shortcode' ) );
			add_filter( 'cmb2_override__ctct_generated_shortcode_meta_save', '__return_empty_string' );
		}

	}

	/**
	 * Form description CMB2 metabox.
	 *
	 * @since 1.0.0
	 */
	public function description_metabox() {

		/**
		 * Initiate the $description_metabox.
		 */
		$description_metabox = new_cmb2_box( array(
			'id'           => 'ctct_0_description_metabox',
			'title'        => __( 'Form Description', 'constant-contact-forms' ),
			'object_types' => array( 'ctct_forms' ),
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
		) );

		$description_metabox->add_field( array(
			'description' => __( 'This message will display above the form fields, so use it as an opportunity to pitch your email list. Tell visitors why they should subscribe to your emails, focusing on benefits like insider tips, discounts, subscriber coupons, and more.', 'constant-contact-forms' ),
			'id'   => $this->prefix . 'description',
			'type' => 'wysiwyg',
			'options' => array(
				'media_buttons' => false,
				'textarea_rows' => '5',
				'teeny'         => false,
			),
		) );
	}

	/**
	 * Form options CMB2 metabox.
	 *
	 * @since 1.0.0
	 */
	public function opt_ins_metabox() {

		$options_metabox = new_cmb2_box( array(
			'id'           => 'ctct_1_optin_metabox',
			'title'        => __( 'Form Options', 'constant-contact-forms' ),
			'object_types' => array( 'ctct_forms' ),
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
		) );

		$options_metabox->add_field( array(
			'name' => __( 'Button text', 'constant-contact-forms' ),
			'id'   => $this->prefix . 'button_text',
			'type' => 'text_medium',
		) );

		$options_metabox->add_field( array(
			'name'        => __( 'Submit via AJAX', 'constant-contact-forms' ),
			'id'          => $this->prefix . 'do_ajax',
			'type'        => 'checkbox',
			'description' => __( 'Enables form submissions without triggering a page refresh. Overrides and prevents "Redirect to" value usage.', 'constant-contact-forms' ),
		) );

		$options_metabox->add_field( array(
			'name' => __( 'Custom form submission success text', 'constant-contact-forms' ),
			'id'   => $this->prefix . 'form_submission_success',
			'type' => 'text_medium',
		) );

		$options_metabox->add_field( array(
			'name' => __( 'Redirect to', 'constant-contact-forms' ),
			'id'   => $this->prefix . 'redirect_uri',
			'type' => 'text_medium',
		) );

		if ( constant_contact()->api->is_connected() ) {
			$this->show_optin_connected_fields( $options_metabox );
		}/**
		  * Same as the block above.
		else {
			$this->show_optin_not_connected_fields( $options_metabox );
		}*/
	}

	/**
	 * Helper method to show our connected optin fields.
	 *
	 * @since 1.0.0
	 *
	 * @param object $options_metabox CMB2 options metabox object.
	 */
	public function show_optin_connected_fields( $options_metabox ) {

		// Get our lists.
		$lists = $this->plugin->builder->get_lists();

		// Add field if conncted to API.
		if ( $lists ) {

			// Allow choosing a list to add to.
			$options_metabox->add_field( array(
				'name'             => __( 'Add subscribers to', 'constant-contact-forms' ),
				'id'               => $this->prefix . 'list',
				'type'             => 'select',
				'show_option_none' => __( 'No List Selected', 'constant-contact-forms' ),
				'default'          => 'none',
				'options'          => $lists,
			) );
		}

		// Show our show/hide checkbox field.
		$this->show_enable_show_checkbox_field( $options_metabox );

		// Show our affirmation textbox field.
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

		$options_metabox->add_field( array(
			'name'        => __( 'Enable email subscriber opt-in', 'constant-contact-forms' ),
			'id'          => $this->prefix . 'opt_in_not_connected',
			'description' => __( 'Adds an opt-in to the bottom of your form.', 'constant-contact-forms' ),
			'type'        => 'checkbox',
			'attributes'  => array(
				'disabled' => 'disabled',
			),
		) );

		// Show our affirmation textbox field.
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

		$description = __( 'Show opt-in checkbox to allow visitors to opt-in to your email list.', 'constant-contact-forms' );
		$description .= '<br>';
		$description .= __( '(usually used with a Contact Us type form)', 'constant-contact-forms' );

		$options_metabox->add_field( array(
			'name'        => __( 'Show Opt-in checkbox', 'constant-contact-forms' ),
			'id'          => $this->prefix . 'opt_in',
			'description' => $description,
			'type'        => 'checkbox',
			'before_row'  => '<div><strong>' . esc_html__( 'Important: opt-in settings required for sending user submissions to ConstantContact.com', 'constant-contact-forms' ) . '</strong></div>',
		) );
	}

	/**
	 * Helper method to show our affirmation textarea field.
	 *
	 * @since 1.0.0
	 *
	 * @param object $options_metabox CMB2 options metabox object.
	 */
	public function show_affirmation_field( $options_metabox ) {

		// Get our site name, and if we don't have it, then use a placeholder.
		$business_name = get_bloginfo( 'name' );
		$business_name ? ( $business_name ) : __( 'Your Business Name', 'constant-contact-forms' );

		$options_metabox->add_field( array(
			'name'        => __( 'Opt-in Affirmation', 'constant-contact-forms' ),
			'id'          => $this->prefix . 'opt_in_instructions',
			'type'        => 'textarea_small',
			// translators: placeholder has a business name from Constant Contact.
			'default'     => sprintf( __( 'Example: Yes, I would like to receive emails from %s. (You can unsubscribe anytime)', 'constant-contact-forms' ), $business_name ),
		) );
	}

	/**
	 * Fields builder CMB2 metabox.
	 *
	 * @since 1.0.0
	 */
	public function fields_metabox() {

		/**
		 * Initiate the $fields_metabox.
		 */
		$fields_metabox = new_cmb2_box( array(
			'id'           => 'ctct_2_fields_metabox',
			'title'        => __( 'Form Fields', 'constant-contact-forms' ),
			'object_types' => array( 'ctct_forms' ),
			'context'      => 'normal',
			'priority'     => 'low',
			'show_names'   => true,
		) );

		// Custom CMB2 fields.
		$fields_metabox->add_field( array(
			'name'        => __( 'Add Fields', 'constant-contact-forms' ),
			/**
			 * No birthdays or anniversarys in CC API V2, keeping this for later.
			 * "You can also collect birthday and anniversary dates to use with Constant Contact autoresponders! "
			 * @since 1.0.2
			 */
			'description' => __( 'Create a field for each piece of information you want to collect. Good basics include email address, first name, and last name.', 'constant-contact-forms' ),
			'id'          => $this->prefix . 'title',
			'type'        => 'title',
		) );

		// Form builder repeater.
		$custom_group = $fields_metabox->add_field( array(
			'id'         => 'custom_fields_group',
			'type'       => 'group',
			'repeatable' => true,
			'options'    => array(
				'group_title'   => __( 'Field {#}', 'constant-contact-forms' ),
				'add_button'    => __( 'Add Another Field', 'constant-contact-forms' ),
				'remove_button' => __( 'Remove Field', 'constant-contact-forms' ),
				'sortable'      => true,
			),
		) );

		/**
		 * The default placeholder text to use for fields without a placeholder.
		 *
		 * @since 1.2.0
		 *
		 * @param string $default_placeholder The placeholder text.
		 */
		$default_placeholder = apply_filters( 'constant_contact_default_placeholder', __( 'A brief description of this field (optional)', 'constant-contact-forms' ) );

		// Define field configuration for options and placeholders.
		$default_fields = array(
			'email' => array(
				'option'      => __( 'Email (required)', 'constant-contact-forms' ),
				'placeholder' => __( 'c.contact@example.com', 'constant-contact-forms' ),
			),
			'first_name' => array(
				'option'      => __( 'First Name', 'constant-contact-forms' ),
				'placeholder' => __( 'John', 'constant-contact-forms' ),
			),
			'last_name' => array(
				'option'      => __( 'Last Name', 'constant-contact-forms' ),
				'placeholder' => __( 'Smith', 'constant-contact-forms' ),
			),
			'phone_number' => array(
				'option'      => __( 'Phone Number', 'constant-contact-forms' ),
				'placeholder' => __( '(555) 272-3342', 'constant-contact-forms' ),
			),
			'address' => array(
				'option'      => __( 'Address', 'constant-contact-forms' ),
				'placeholder' => __( '4115 S. Main Rd.', 'constant-contact-forms' ),
			),
			'job_title' => array(
				'option'      => __( 'Job Title', 'constant-contact-forms' ),
				'placeholder' => __( 'Project Manager', 'constant-contact-forms' ),
			),
			'company' => array(
				'option'      => __( 'Company', 'constant-contact-forms' ),
				'placeholder' => __( 'Acme Manufacturing', 'constant-contact-forms' ),
			),
			'website' => array(
				'option'      => __( 'Website', 'constant-contact-forms' ),
				'placeholder' => __( 'http://www.example.com', 'constant-contact-form' ),
			),
			/**
			 * V2 of the CC API doesn't support these fields. Hopefully this will get sorted out.
			 * 'birthday' => array(
			 *     'option' => __( 'Birthday', 'constant-contact-forms' ),
			 *     'placeholder' => 'M/D/Y',
			 * ),
			 * 'anniversary'      => array(
			 *     'option' => __( 'Anniversary', 'constant-contact-forms' ),
			 *     'placeholder' => 'M/D/Y',
			 *     ),
			 * @since 1.0.2
			 */
			'custom' => array(
				'option'      => __( 'Custom Text Field', 'constant-contact-forms' ),
				'placeholder' => __( 'A custom text field', 'constant-contact-forms' ),
			),
			'custom_text_area' => array(
				'option'      => __( 'Custom Text Area', 'constant-contact-forms' ),
				'placeholder' => __( 'A large custom text field', 'constant-contact-forms' ),
			),
		);

		/**
		 * Filters the Constant Contact field types to display as an option.
		 *
		 * @since 1.0.0
		 *
		 * @param array $value Array of field types.
		 */
		$filtered_options = apply_filters( 'constant_contact_field_types', wp_list_pluck( $default_fields, 'option' ) );

		/**
		 * Filter the field placeholders.
		 *
		 * @since 1.2.0
		 *
		 * @param array $default_fields The field placeholders to use for field description.
		 */
		$filtered_placeholders            = apply_filters(
			'constant_contact_field_placeholders',
			wp_list_pluck( $default_fields, 'placeholder' )
		);
		$filtered_placeholders['default'] = $default_placeholder;

		// Go ahead and enqueue with our placeholder text.
		$this->plugin->admin->scripts( array(
			'ctct_form',
			'ctct_admin_placeholders',
			$filtered_placeholders,
		) );

		// Choose which field.
		$fields_metabox->add_group_field( $custom_group, array(
			'name'             => __( 'Select a Field', 'constant-contact-forms' ),
			'id'               => $this->prefix . 'map_select',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => 'email',
			'row_classes'      => 'map',
			'options'          => $filtered_options,
		) );

		// Add a field label.
		$fields_metabox->add_group_field( $custom_group, array(
			'name'    => __( 'Field Label', 'constant-contact-forms' ),
			'id'      => $this->prefix . 'field_label',
			'type'    => 'text',
			'default' => __( 'Email', 'constant-contact-forms' ),
		) );

		// Add our field description.
		$fields_metabox->add_group_field( $custom_group, array(
			'name'       => __( 'Field Description', 'constant-contact-forms' ),
			'id'         => $this->prefix . 'field_desc',
			'type'       => 'text',
			'attributes' => array(
				'placeholder' => __( 'Ex: Enter email address', 'constant-contact-forms' ),
			),
		) );

		// Allow toggling of required fields.
		$fields_metabox->add_group_field( $custom_group, array(
			'name'        => __( 'Required', 'constant-contact-forms' ),
			'id'          => $this->prefix . 'required_field',
			'type'        => 'checkbox',
			'row_classes' => 'required',
		) );

	}

	/**
	 * Show a metabox rendering our shortcode.
	 *
	 * @since 1.1.0
	 */
	public function generated_shortcode() {
		$generated = new_cmb2_box( array(
			'id'           => 'ctct_2_generated_metabox',
			'title'        => __( 'Shortcode', 'constant-contact-forms' ),
			'object_types' => array( 'ctct_forms' ),
			'context'      => 'side',
			'priority'     => 'low',
			'show_names'   => false,
		) );

		$generated->add_field( array(
			'name'       => __( 'Shortcode to use', 'constant-contact-forms' ),
			'id'         => $this->prefix . 'generated_shortcode',
			'type'       => 'text_medium',
			'desc'       => __( 'Shortcode to embed - <em><small>You can copy and paste this in a post to display your form.</small></em>', 'constant-contact-forms' ),
			'default'    => ( $generated->object_id > 0 ) ? '[ctct form="' . $generated->object_id . '"]' : '',
			'attributes' => array(
				'readonly' => 'readonly',
			),
		) );
	}
}

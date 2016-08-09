<?php
/**
 * ConstantContact_Builder_Fields form Builder Settings
 *
 * @package ConstantContact_Builder_Fields
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * ConstantContact_Builder_Fields
 */
class ConstantContact_Builder_Fields {

	/**
	 * Parent plugin class
	 *
	 * @var   class
	 * @since 0.0.1
	 */
	protected $plugin = null;

	/**
	 * Prefix for our meta fields / boxes
	 * @var  string
	 */
	public $prefix = '_ctct_';

	/**
	 * Constructor
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'init', array( $this, 'hooks' ) );
	}

	/**
	 * Initiate our hooks
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		global $pagenow;

		// Allow filtering the pages to load form build actions
		$form_builder_pages = apply_filters(
			'constant_contact_form_builder_pages',
			array( 'post-new.php', 'post.php' )
		);

		// Only load the cmb2 fields on our specified pages
		if ( in_array( $pagenow, $form_builder_pages, true ) ) {

			add_action( 'cmb2_admin_init', array( $this, 'description_metabox' ) );
			add_action( 'cmb2_admin_init', array( $this, 'opt_ins_metabox' ) );
			add_action( 'cmb2_admin_init', array( $this, 'fields_metabox' ) );
		}

	}

	/**
	 * Form description CMB2 metabox
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function description_metabox() {

		/**
		 * Initiate the $description_metabox
		 */
		$description_metabox = new_cmb2_box( array(
			'id'			=> 'ctct_0_description_metabox',
			'title'		 	=> __( 'Form Description', 'constantcontact' ),
			'object_types'  => array( 'ctct_forms' ),
			'context'	   	=> 'normal',
			'priority'	  	=> 'high',
			'show_names'	=> true,
		) );

		$description_metabox->add_field( array(
			'description' => __( 'This message will display above the form fields, so use it as an opportunity to pitch your email list. Tell visitors why they should subscribe to your emails, focusing on benefits like insider tips, discounts, subscriber coupons, and more.', 'constantcontact' ),
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
	 * Form options CMB2 metabox
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function opt_ins_metabox() {

		// Initiate the $options_metabox, as this is used either way
		$options_metabox = new_cmb2_box( array(
			'id'			=> 'ctct_1_optin_metabox',
			'title'		 	=> __( 'Connect your form to a Constant Contact Email List', 'constantcontact' ),
			'object_types'  => array( 'ctct_forms' ),
			'context'	   	=> 'normal',
			'priority'	  	=> 'high',
			'show_names'	=> true,
		) );

		// Depending on if we're connected or not, show different opt-in fields
		if ( constant_contact()->api->is_connected() ) {
			$this->show_optin_connected_fields( $options_metabox );
		} else {
			$this->show_optin_not_connected_fields( $options_metabox );
		}
	}

	/**
	 * Helper method to show our connected optin fields
	 *
	 * @since   1.0.0
	 * @param   object  $options_metabox  CMB2 options metabox object
	 * @return  void
	 */
	public function show_optin_connected_fields( $options_metabox ) {

		// Add field if conncted to API.
		if ( $lists = $this->plugin->builder->get_lists() ) {

			// Allow choosing a list to add to
			$options_metabox->add_field( array(
				'name'             => __( 'Add subscribers to', 'constantcontact' ),
				'id'               => $this->prefix . 'list',
				'type'             => 'select',
				'show_option_none' =>  __( 'No List Selected', 'constantcontact' ),
				'default'          => 'none',
				'options'          => $lists,
			) );
		}

		// Show our show/hide checkbox field
		$this->show_enable_show_checkbox_field( $options_metabox );

		// Show our affirmation textbox field
		$this->show_affirmation_field( $options_metabox );
	}

	/**
	 * Helper method to show our non connected optin fields
	 *
	 * @since   1.0.0
	 * @param   object  $options_metabox  CMB2 options metabox object
	 * @return  void
	 */
	public function show_optin_not_connected_fields( $options_metabox ) {

		$options_metabox->add_field( array(
			'name'        => __( 'Enable email subscriber opt-in', 'constantcontact' ),
			'id'          => $this->prefix . 'opt_in_not_connected',
			'description' => __( 'Email opt-in is added to the bottom of your form.', 'constantcontact' ),
			'type'        => 'checkbox',
		) );

		// Show our affirmation textbox field
		$this->show_affirmation_field( $options_metabox );
	}

	/**
	 * Helper method to show our show/hide checkbox field
	 *
	 * @since   1.0.0
	 * @param   object  $options_metabox  CMB2 options metabox object
	 * @return  void
	 */
	public function show_enable_show_checkbox_field( $options_metabox ) {
		$options_metabox->add_field( array(
			'name'        => __( 'Show Opt-in checkbox', 'constantcontact' ),
			'id'          => $this->prefix . 'opt_in',
			'description' => __( 'Show opt-in checkbox to allow visitors to opt-in to your email list. (usually used with a Contact Us type form)', 'constantcontact' ),
			'type'        => 'checkbox',
		) );
	}

	/**
	 * Helper method to show our affirmation textarea field
	 *
	 * @since   1.0.0
	 * @param   object  $options_metabox  CMB2 options metabox object
	 * @return  void
	 */
	public function show_affirmation_field( $options_metabox ) {

		// Get our site name, and if we don't have it, then use a placeholder
		$business_name = get_bloginfo( 'name' );
		$business_name ? $business_name : __( 'Your Business Name', 'constantcontact' );

		$options_metabox->add_field( array(
			'name'        => __( 'Opt-in Affirmation', 'constantcontact' ),
			'id'          => $this->prefix . 'opt_in_instructions',
			'type'        => 'textarea_small',
			'default'     => sprintf( __( 'Example: Yes, I would like to receive emails from %s. (You can unsubscribe  anytime)', 'constantcontact' ), $business_name ),
		) );
	}

	/**
	 * Fields builder CMB2 metabox
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function fields_metabox() {

		/**
		 * Initiate the $fields_metabox
		 */
		$fields_metabox = new_cmb2_box( array(
			'id'			=> 'ctct_2_fields_metabox',
			'title'		 	=> __( 'Form Fields', 'constantcontact' ),
			'object_types'  => array( 'ctct_forms' ),
			'context'	   	=> 'normal',
			'priority'	  	=> 'low',
			'show_names'	=> true,
		) );

		// Custom CMB2 fields.
		$fields_metabox->add_field( array(
			'name'        => __( 'Add Fields', 'constantcontact' ),
			'description' => __( 'Create a field for each piece of information you want to collect. Good basics include email address, first name, and last name. You can also collect birthday and anniversary dates to use with Constant Contact autoresponders! ', 'constantcontact' ),
			'id'          => $this->prefix . 'title',
			'type'        => 'title',
		) );

		// Form builder repeater
		$custom_group = $fields_metabox->add_field( array(
			'id'         => 'custom_fields_group',
			'type'       => 'group',
			'repeatable' => true,
			'options'    => array(
				'group_title'   => __( 'Field {#}', 'constantcontact' ),
				'add_button'    => __( 'Add Another Field', 'constantcontact' ),
				'remove_button' => __( 'Remove Field', 'constantcontact' ),
				'sortable'      => true,
			),
		) );

		// Add a field label
		$fields_metabox->add_group_field( $custom_group, array(
			'name'    => __( 'Field Label', 'constantcontact' ),
			'id'      => $this->prefix . 'field_label',
			'type'    => 'text',
			'default' => __( 'Email', 'constantcontact' ),
		) );

		// Add our field description
		$fields_metabox->add_group_field( $custom_group, array(
			'name'    => __( 'Field Description', 'constantcontact' ),
			'id'      => $this->prefix . 'field_desc',
			'type'    => 'text',
			'default' => '',
		) );

		$default_fields = apply_filters( 'constant_contact_field_types', array(
			'email'            => __( 'Email (required)', 'constantcontact' ),
			'first_name'       => __( 'First Name', 'constantcontact' ),
			'last_name'        => __( 'Last Name', 'constantcontact' ),
			'phone_number'     => __( 'Phone Number', 'constantcontact' ),
			'address'          => __( 'Address', 'constantcontact' ),
			'job_title'        => __( 'Job Title', 'constantcontact' ),
			'company'          => __( 'Company', 'constantcontact' ),
			'website'          => __( 'Website', 'constantcontact' ),
			'birthday'         => __( 'Birthday', 'constantcontact' ),
			'anniversary'      => __( 'Anniversary', 'constantcontact' ),
			'custom'           => __( 'Custom Text Field', 'constantcontact' ),
			'custom_text_area' => __( 'Custom Text Area', 'constantcontact' ),
		) );

		// Choose which field
		$fields_metabox->add_group_field( $custom_group, array(
			'name'             => __( 'Select a Field', 'constantcontact' ),
			'id'               => $this->prefix . 'map_select',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => 'email',
			'row_classes'      => 'map',
			'options'          => $default_fields,
		) );

		// Allow toggling of required fields
		$fields_metabox->add_group_field( $custom_group, array(
			'name'        => __( 'Required', 'constantcontact' ),
			'id'          => $this->prefix . 'required_field',
			'type'        => 'checkbox',
			'row_classes' => 'required',
		) );

	}
}

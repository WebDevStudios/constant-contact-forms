<?php
/**
 * ConstantContact_Builder form Builder Settings
 *
 * @package ConstantContactBuilder
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * ConstantContact_Builder
 */
class ConstantContact_Builder {

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
	 * @since  0.0.1
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
			add_action( 'cmb2_admin_init', array( $this, 'fields_metabox' ) );
			add_action( 'cmb2_admin_init', array( $this, 'options_metabox' ) );
			add_action( 'cmb2_after_post_form_ctct_description_metabox', array( $this, 'add_form_css' ) );

			add_action( 'cmb2_save_field', array( $this, 'override_save' ), 10, 4 );
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );
		}

	}

	/**
	 * Form description CMB2 metabox
	 *
	 * @return void
	 */
	public function description_metabox() {

		$prefix = '_ctct_';

		/**
		 * Initiate the $description_metabox
		 */
		$description_metabox = new_cmb2_box( array(
			'id'			=> 'ctct_description_metabox',
			'title'		 	=> __( 'Form Description', 'constantcontact' ),
			'object_types'  => array( 'ctct_forms' ),
			'context'	   	=> 'normal',
			'priority'	  	=> 'high',
			'show_names'	=> true,
		) );

		$description_metabox->add_field( array(
			'description' => __( 'Add a description about this form. This will be shown above the form fields on the site.', 'constantcontact' ),
			'id'   => $prefix . 'description',
			'type' => 'textarea_small',
		) );
	}


	/**
	 * Fields builder CMB2 metabox
	 *
	 * @return void
	 */
	public function fields_metabox() {

		$prefix = '_ctct_';

		/**
		 * Initiate the $fields_metabox
		 */
		$fields_metabox = new_cmb2_box( array(
			'id'			=> 'ctct_fields_metabox',
			'title'		 	=> __( 'Form Fields', 'constantcontact' ),
			'object_types'  => array( 'ctct_forms' ),
			'context'	   	=> 'normal',
			'priority'	  	=> 'high',
			'show_names'	=> true,
		) );

		// Custom CMB2 fields.
		$fields_metabox->add_field( array(
			'name'        => __( 'Add Fields', 'constantcontact' ),
			'description' => __( 'Fields are sortable and can be mapped to Constant Contact default fields.', 'constantcontact' ),
			'id'          => $prefix . 'title',
			'type'        => 'title',
		) );

		// Default fields.
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

		$fields_metabox->add_group_field( $custom_group, array(
			'name'    => __( 'Field Name', 'constantcontact' ),
			'id'      => $prefix . 'field_name',
			'type'    => 'text',
			'default' => 'Email',
		) );

		$default_fields = apply_filters( 'constant_contact_field_types', array(
			'custom'       => __( 'Custom Text Field', 'constantcontact' ),
			'email'        => __( 'Email', 'constantcontact' ),
			'first_name'   => __( 'First Name', 'constantcontact' ),
			'last_name'    => __( 'Last Name', 'constantcontact' ),
			'phone_number' => __( 'Phone Number', 'constantcontact' ),
			'address'      => __( 'Address', 'constantcontact' ),
			'job_title'    => __( 'Job Title', 'constantcontact' ),
			'company'      => __( 'Company', 'constantcontact' ),
			'website'      => __( 'Website', 'constantcontact' ),
			'birthday'     => __( 'Birthday', 'constantcontact' ),
			'anniversary'  => __( 'Anniversary', 'constantcontact' ),
		) );

		$fields_metabox->add_group_field( $custom_group, array(
			'name'             => __( 'Map to field', 'constantcontact' ),
			'id'               => $prefix . 'map_select',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => 'email',
			'row_classes'      => 'map',
			'options'          => $default_fields,
		) );

		$fields_metabox->add_group_field( $custom_group, array(
			'name'        => __( 'Required', 'constantcontact' ),
			'id'          => $prefix . 'required_field',
			'type'        => 'checkbox',
			'row_classes' => 'required',
			'default'     => 'on',
		) );

	}

	/**
	 * Form options CMB2 metabox
	 *
	 * @return void
	 */
	public function options_metabox() {

		$prefix = '_ctct_';

		/**
		 * Initiate the $options_metabox
		 */
		$options_metabox = new_cmb2_box( array(
			'id'			=> 'ctct_options_metabox',
			'title'		 	=> __( 'Form Options', 'constantcontact' ),
			'object_types'  => array( 'ctct_forms' ),
			'context'	   	=> 'normal',
			'priority'	  	=> 'high',
			'show_names'	=> true,
		) );

		$options_metabox->add_field( array(
			'description' => __( 'Choose form options.', 'constantcontact' ),
			'id'          => $prefix . 'title',
			'type'        => 'title',
		) );

		// Add field if conncted to API.
		if ( $lists = $this->get_lists() ) {

			$options_metabox->add_field( array(
				'name'             => __( 'List', 'constantcontact' ),
				'id'               => $prefix . 'list',
				'description'      => __( 'Choose a list.', 'constantcontact' ),
				'type'             => 'select',
				'show_option_none' => true,
				'default'          => 'none',
				'options'          => $lists,
			) );

		}

		$options_metabox->add_field( array(
			'name'        => __( 'New List', 'constantcontact' ),
			'id'          => $prefix . 'new_list',
			'description' => __( 'Enter title of new list.', 'constantcontact' ),
			'type'        => 'text',
		) );

		$options_metabox->add_field( array(
			'name'        => __( 'Opt In', 'constantcontact' ),
			'id'          => $prefix . 'opt_in',
			'description' => __( 'Add Opt In checkbox to form.', 'constantcontact' ),
			'type'        => 'checkbox',
		) );

		$options_metabox->add_field( array(
			'name'        => __( 'Opt In Instructions', 'constantcontact' ),
			'id'          => $prefix . 'opt_in_instructions',
			'description' => __( 'Add Opt In instructions.', 'constantcontact' ),
			'type'        => 'textarea_small',
		) );

	}

	/**
	 * Get lists for dropdown option
	 *
	 * @author Brad Parbs
	 * @return array array of lists
	 */
	public function get_lists() {

		// Grab our lists
		$lists = constantcontact_lists()->get_lists();

		if ( $lists && is_array( $lists ) ) {

			// Always want the 'new' element to be in the list
			$get_lists['new'] = __( 'New', 'constantcontact' );

			// Loop though our lists
			foreach ( $lists as $list => $value ) {

				// Make sure we have something to use as a key and a value,
				// and that we don't overwrite our 'new' value we set before
				if ( ! empty( $list ) && ! empty( $value ) && 'new' != $list ) {
					$get_lists[ $list ] = $value;
				}
			}

			// Return those lists
			return $get_lists;
		}

		return array();

	}

	/**
	 * Custom CMB2 meta box css
	 */
	public function add_form_css() {

		// Let's style this thing
		wp_enqueue_style(
			'constant-contact-form-builder',
			constant_contact()->url() . 'assets/css/form-builder.css',
			array(),
			constant_contact()->version
		);
	}

	/**
	 * Hook into CMB2 save meta to check if email field has been added
	 *
	 * @param  string $field_id CMB2 Field id.
	 * @param  [type] $updated  [description]
	 * @param  [type] $action   [description]
	 * @param  object $cmbobj   CMB2 field object
	 * @return void
	 */
	public function override_save( $field_id, $updated, $action, $cmbobj ) {

		// Hey $post nice to see you
		global $post;

		// Do all our existence checks
		if (
			isset( $post->ID ) &&
			$post->ID &&
			isset( $post->type ) &&
			$post->type &&
			'ctct_forms' == $post->type &&
			$cmbobj &&
			isset( $cmbobj->data_to_save ) &&
			isset( $cmbobj->data_to_save['custom_fields_group'] ) &&
			is_array( $cmbobj->data_to_save['custom_fields_group'] )
		) {

			// We want to set our meta to false, as we'll want to loop through
			// and see if we should set it to true, but we want it to be false most
			// of the time
			update_post_meta( $post->ID, '_ctct_has_email_field', 'false' );

			// Loop through all of our custom fields group fields
			foreach ( $cmbobj->data_to_save['custom_fields_group'] as $data ) {

				// If we have a an email field set in our map select:
				if ( isset( $data['_ctct_map_select'] ) && 'email' === $data['_ctct_map_select'] ) {

					// update our post meta to mark that we have email
					update_post_meta( $post->ID, '_ctct_has_email_field', 'true' );

					// bail out, more than one email fields are fine, but we know we have at least one
					break;
				}
			}
		}
	}

	/**
	 * Set admin notice if no email field
	 *
	 * @return void
	 */
	public function admin_notice() {
	    global $post;

	    if ( $post && isset( $post->ID ) && isset( $post->post_type ) ) {

	    	// Make sure we're on our post type as well
	    	if ( 'ctct_forms' != $post->post_type ) {
	    		return;
	    	}

	    	// Check to see if we have an email set on our field
	    	$has_email = get_post_meta( $post->ID, '_ctct_has_email_field', true );

	    	if ( ! $has_email || 'false' == $has_email ) {
				$class = 'notice notice-error';
				$message = __( 'You have not added an email field to your form. Forms will not send unless a field is mapped to email.', 'constantcontact' );
				printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
	    	}
	    }
	}
}

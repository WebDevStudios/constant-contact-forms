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
	 * Holds an instance of the project
	 *
	 * @ConstantContact_Builder
	 **/
	private static $instance = null;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
	}

	/**
	 * Get the running object
	 *
	 * @return ConstantContact_Builder
	 **/
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->hooks();
		}
		return self::$instance;
	}

	/**
	 * Initiate our hooks
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		//add_filter( 'cmb2_meta_boxes', array( $this, 'form_metaboxes' ) );
		add_action( 'cmb2_admin_init', array( $this, 'form_metabox' ) );
		add_action( 'cmb2_after_post_form__ctct_fields_metabox', array( $this, 'add_custom_css_for_metabox' ), 10, 2 );

	}

	/**
	 * [form_metaboxes description]
	 *
	 * @param  array $meta_boxes cmb2 metabox appended data.
	 * @return array  cmb2 metabox appended data
	 */
	public function form_metabox() {

		$prefix = '_ctct_';

		/**
		 * Initiate the $description_metabox
		 */
		$description_metabox = new_cmb2_box( array(
			'id'			=> 'description_metabox',
			'title'		 	=> __( 'Description', constant_contact()->text_domain ),
			'object_types'  => array( 'ctct_forms' ),
			'context'	   	=> 'normal',
			'priority'	  	=> 'high',
			'show_names'	=> false,
		) );

		$description_metabox->add_field( array(
			'name' => __( 'description_metabox', constant_contact()->text_domain ),
			'id'   => $prefix . 'description',
			'type' => 'textarea_small',
		) );

		/**
		 * Initiate the $fields_metabox
		 */
		$fields_metabox = new_cmb2_box( array(
			'id'			=> $prefix . 'fields_metabox',
			'title'		 	=> __( 'Form Fields', constant_contact()->text_domain ),
			'object_types'  => array( 'ctct_forms' ),
			'context'	   	=> 'normal',
			'priority'	  	=> 'high',
			'show_names'	=> true,
		) );

		// Default fields.
		$default_group_field_id = $fields_metabox->add_field( array(
		    'id'          => 'default_group',
		    'type'        => 'group',
		    'repeatable'  => false,
			'options'     => array(
				'group_title'   => __( 'Default Fields', constant_contact()->text_domain ),
			),
		) );

		$fields_metabox->add_group_field( $default_group_field_id, array(
			'description' => __( 'Check each field you wish to display in the form.', constant_contact()->text_domain ),
			'id'   => $prefix . 'title',
			'type' => 'title',
		) );

		$fields_metabox->add_group_field( $default_group_field_id, array(
			'name' => __( 'First Name', constant_contact()->text_domain ),
			'id'   => $prefix . 'first_name',
			'type' => 'checkbox',
			'after_field' => array( $this, 'cmb_after_row_cb' ),
		) );

		$fields_metabox->add_group_field( $default_group_field_id, array(
			'name' => __( 'Last Name', constant_contact()->text_domain ),
			'id'   => $prefix . 'last_name',
			'type' => 'checkbox',
		) );

		$fields_metabox->add_group_field( $default_group_field_id, array(
			'name' => __( 'Phone Number', constant_contact()->text_domain ),
			'id'   => $prefix . 'phone_number',
			'type' => 'checkbox',
		) );

		$fields_metabox->add_group_field( $default_group_field_id, array(
			'name' => __( 'Address', constant_contact()->text_domain ),
			'id'   => $prefix . 'address',
			'type' => 'checkbox',
		) );

		$fields_metabox->add_group_field( $default_group_field_id, array(
			'name' => __( 'Job Title', constant_contact()->text_domain ),
			'id'   => $prefix . 'job_title',
			'type' => 'checkbox',
		) );

		$fields_metabox->add_group_field( $default_group_field_id, array(
			'name' => __( 'Company', constant_contact()->text_domain ),
			'id'   => $prefix . 'company',
			'type' => 'checkbox',
		) );

		$fields_metabox->add_group_field( $default_group_field_id, array(
			'name' => __( 'Website', constant_contact()->text_domain ),
			'id'   => $prefix . 'website',
			'type' => 'checkbox',
		) );

		$fields_metabox->add_group_field( $default_group_field_id, array(
			'name' => __( 'Birthday', constant_contact()->text_domain ),
			'id'   => $prefix . 'birthday',
			'type' => 'checkbox',
		) );

		$fields_metabox->add_group_field( $default_group_field_id, array(
			'name' => __( 'Anniversary', constant_contact()->text_domain ),
			'id'   => $prefix . 'anniversary',
			'type' => 'checkbox',
		) );


		// Custom fields
		$custom_group_field_id = $fields_metabox->add_field( array(
		    'id'          => $prefix . 'custom_group',
		    'type'        => 'group',
		    'repeatable'  => false,
			'options'     => array(
			'group_title'   => __( 'Custom Fields', constant_contact()->text_domain ),
			),
		) );

		$fields_metabox->add_group_field( $custom_group_field_id, array(
			'description' => __( 'Add custom fields to the form.', constant_contact()->text_domain ),
			'id'   => $prefix . 'title',
			'type' => 'title',
		) );


		$fields_metabox->add_group_field( $custom_group_field_id, array(
			'name' => __( 'Field Name', constant_contact()->text_domain ),
			'id'   => $prefix . 'custom',
			'type' => 'text',
			'repeatable' => true,
			'options' => array(
				'add_row_text' => 'Add Field',
			),
		) );


		// Form options
		$options_group_field_id = $fields_metabox->add_field( array(
		    'id'          => $prefix . 'form_options',
		    'type'        => 'group',
		    'repeatable'  => false,
			'options'     => array(
			'group_title'   => __( 'Form Options', constant_contact()->text_domain ),
			),
		) );

		$fields_metabox->add_group_field( $options_group_field_id, array(
			'description' => __( 'Choose form options.', constant_contact()->text_domain ),
			'id'   => $prefix . 'title',
			'type' => 'title',
		) );

		$fields_metabox->add_group_field( $options_group_field_id, array(
			'name' => __( 'List', constant_contact()->text_domain ),
			'id'   => $prefix . 'list',
			'description' => __( 'Choose a list.', constant_contact()->text_domain ),
			'type' => 'select',
			'show_option_none' => true,
			'default' => 'none',
			'options' => array(
				'new' => 'New List'
			),
		) );

		$fields_metabox->add_group_field( $options_group_field_id, array(
			'name' => __( 'New List', constant_contact()->text_domain ),
			'id'   => $prefix . 'new_list',
			'description' => __( 'Enter title of new list.', constant_contact()->text_domain ),
			'type' => 'text',
		) );

		$fields_metabox->add_group_field( $options_group_field_id, array(
			'name' => __( 'Opt In', constant_contact()->text_domain ),
			'id'   => $prefix . 'opt_in',
			'description' => __( 'Add Opt In checkbox to form.', constant_contact()->text_domain ),
			'type' => 'checkbox',
		) );

	}

	/**
	 * [form_metaboxes description]
	 *
	 * @param  array $meta_boxes cmb2 metabox appended data.
	 * @return array  cmb2 metabox appended data
	 */
	public function form_metaboxes( array $meta_boxes ) {

		$prefix = '_ctct_';

		$meta_boxes['form_description_metabox'] = array(
			'id'			=> 'description_metabox',
			'title'		 	=> __( 'Description', constant_contact()->text_domain ),
			'object_types'  => array( 'ctct_forms' ),
			'context'	   	=> 'normal',
			'priority'	  	=> 'high',
			'show_names'	=> false,
			'fields'		=> array(
				array(
					'name' => __( 'description_metabox', constant_contact()->text_domain ),
					'id'   => $prefix . 'description',
					'type' => 'textarea_small',
				),
			),
		);

		$meta_boxes['form_fields_metabox'] = array(
			'id'			=> 'fields_metabox',
			'title'		 	=> __( 'Form Fields', constant_contact()->text_domain ),
			'object_types'  => array( 'ctct_forms' ),
			'context'	   	=> 'normal',
			'priority'	  	=> 'high',
			'show_names'	=> true,
			'fields'		=> array(
				array(
					'name' => __( 'Default Fields', constant_contact()->text_domain ),
					'description' => __( 'Check each field you wish to display in the form.', constant_contact()->text_domain ),
					'id'   => $prefix . 'title',
					'type' => 'title',
				),
				array(
					'name' => __( 'First Name', constant_contact()->text_domain ),
					'id'   => $prefix . 'first_name',
					'type' => 'checkbox',
				),
				array(
					'name' => __( 'Last Name', constant_contact()->text_domain ),
					'id'   => $prefix . 'last_name',
					'type' => 'checkbox',
				),
				array(
					'name' => __( 'Phone Number', constant_contact()->text_domain ),
					'id'   => $prefix . 'phone_number',
					'type' => 'checkbox',
				),
				array(
					'name' => __( 'Address', constant_contact()->text_domain ),
					'id'   => $prefix . 'address',
					'type' => 'checkbox',
				),
				array(
					'name' => __( 'Job Title', constant_contact()->text_domain ),
					'id'   => $prefix . 'job_title',
					'type' => 'checkbox',
				),
				array(
					'name' => __( 'Company', constant_contact()->text_domain ),
					'id'   => $prefix . 'company',
					'type' => 'checkbox',
				),
				array(
					'name' => __( 'Website', constant_contact()->text_domain ),
					'id'   => $prefix . 'website',
					'type' => 'checkbox',
				),
				array(
					'name' => __( 'Birthday', constant_contact()->text_domain ),
					'id'   => $prefix . 'birthday',
					'type' => 'checkbox',
				),
				array(
					'name' => __( 'Anniversary', constant_contact()->text_domain ),
					'id'   => $prefix . 'anniversary',
					'type' => 'checkbox',
				),

				array(
					'name' => __( 'Custom Fields', constant_contact()->text_domain ),
					'id'   => $prefix . 'custom_title',
					'description' => __( 'Add custom fields to the form.', constant_contact()->text_domain ),
					'type' => 'title',
				),
				array(
					'name' => __( 'Field Name', constant_contact()->text_domain ),
					'id'   => $prefix . 'custom',
					'type' => 'text',
					'repeatable' => true,
					'options' => array(
						'add_row_text' => 'Add Field',
					),
				),
				array(
					'name' => __( 'List', constant_contact()->text_domain ),
					'id'   => $prefix . 'list',
					'description' => __( 'Choose a list.', constant_contact()->text_domain ),
					'type' => 'select',
					'show_option_none' => true,
					'default' => 'none',
					'options' => array(
						'new' => 'New List'
					),
				),
				array(
					'name' => __( 'New List', constant_contact()->text_domain ),
					'id'   => $prefix . 'new_list',
					'description' => __( 'Enter title of new list.', constant_contact()->text_domain ),
					'type' => 'text',
				),
				array(
					'name' => __( 'Opt In', constant_contact()->text_domain ),
					'id'   => $prefix . 'opt_in',
					'description' => __( 'Add Opt In checkbox to form.', constant_contact()->text_domain ),
					'type' => 'checkbox',
				),
			),
		);

		$meta_boxes = apply_filters( 'ctct_form_metaboxes', $meta_boxes );

		return $meta_boxes;
	}

	/**
	 * Custom meta box css
	 *
	 * @param integer $post_id current post id.
	 * @param object  $cmb cmb2 metabox object.
	 */
	public function add_custom_css_for_metabox( $post_id, $cmb ) {
		?>
		<style type="text/css" media="screen">
			/*.postbox-container .cmb2-wrap > .cmb-field-list > .cmb-row {
				padding: 0;
			}*/
			#default_group_repeat .cmb-row:not(:last-of-type) {
				border-bottom: none;
			}
			#default_group_repeat .cmb-row {
				padding-top: 0;
				padding-bottom: 0;
			}
			.postbox-container .cmb-th, .cmb-repeat-group-wrap .cmb-th {
				width: 22%;
			}
			.postbox-container .cmb-th + .cmb-td, .cmb-repeat-group-wrap .cmb-th + .cmb-td {
				width: 75%;
			}

			div.cmb-row.cmb-type-select.cmb2-id--ctct-form-options-0--ctct-list.cmb-repeat-group-field {
				border-bottom: none;
				padding-bottom: 0;
			}

		</style>
		<?php
	}

	/**
	 * Add required check
	 * @param  object $field_args Current field args
	 * @param  object $field      Current field object
	 */
	public function cmb_after_row_cb( $field_args, $field ) {
		//var_dump( $field->args['_id'] );
		echo '     required <input type="checkbox" class="cmb2-option cmb2-list" name="default_group[0][_ctct_required]['. $field->args['_id'] .']" id="default_group_0_' . $field->args['_id'] . '" value="on">';
	}


}

/**
 * Helper function to get/return the ConstantContact_Builder object
 *
 * @since  1.0.0
 * @return ConstantContact_Builder object
 */
function ctct_builder_admin() {
	return ConstantContact_Builder::get_instance();
}

// Get it started.
ctct_builder_admin();

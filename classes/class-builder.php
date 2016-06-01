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

		add_action( 'cmb2_admin_init', array( $this, 'description_metabox' ) );
		add_action( 'cmb2_admin_init', array( $this, 'fields_metabox' ) );
		add_action( 'cmb2_admin_init', array( $this, 'options_metabox' ) );
		add_action( 'cmb2_after_post_form_ctct_description_metabox', array( $this, 'add_custom_css_for_metabox' ), 10, 2 );

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
			'title'		 	=> __( 'Form Description', constant_contact()->text_domain ),
			'object_types'  => array( 'ctct_forms' ),
			'context'	   	=> 'normal',
			'priority'	  	=> 'high',
			'show_names'	=> true,
		) );

		$description_metabox->add_field( array(
			'description' => __( 'Add a description about this form.', constant_contact()->text_domain ),
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
			'title'		 	=> __( 'Form Fields', constant_contact()->text_domain ),
			'object_types'  => array( 'ctct_forms' ),
			'context'	   	=> 'normal',
			'priority'	  	=> 'high',
			'show_names'	=> true,
		) );

		$fields_metabox->add_field( array(
			'description' => __( 'Add fields to this form. Fields are sortable. A required email field will be added to each form.', constant_contact()->text_domain ),
			'id'   => $prefix . 'title',
			'type' => 'title',
		) );

		// Default fields.
		$default_group_field_id = $fields_metabox->add_field( array(
			'id'		  => 'fields_group',
			'type'		=> 'group',
			'repeatable'  => true,
			'options'	 => array(
				'group_title'   => __( 'Field {#}', constant_contact()->text_domain ),
				'add_button'	=> __( 'Add Another Field', constant_contact()->text_domain ),
				'remove_button' => __( 'Remove Field', constant_contact()->text_domain ),
				'sortable'	  => true,
			),
		) );

		$fields_metabox->add_group_field( $default_group_field_id, array(
			'name' => __( 'Field Name', constant_contact()->text_domain ),
			'id'   => $prefix . 'field_name',
			'type' => 'text',
		) );

		$fields_metabox->add_group_field( $default_group_field_id, array(
			'name' => __( 'Required', constant_contact()->text_domain ),
			'id'   => $prefix . 'required_field',
			'type' => 'checkbox',
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
			'title'		 	=> __( 'Form Options', constant_contact()->text_domain ),
			'object_types'  => array( 'ctct_forms' ),
			'context'	   	=> 'normal',
			'priority'	  	=> 'high',
			'show_names'	=> true,
		) );

		$options_metabox->add_field( array(
			'description' => __( 'Choose form options.', constant_contact()->text_domain ),
			'id'   => $prefix . 'title',
			'type' => 'title',
		) );

		$options_metabox->add_field( array(
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

		$options_metabox->add_field( array(
			'name' => __( 'New List', constant_contact()->text_domain ),
			'id'   => $prefix . 'new_list',
			'description' => __( 'Enter title of new list.', constant_contact()->text_domain ),
			'type' => 'text',
		) );

		$options_metabox->add_field( array(
			'name' => __( 'Opt In', constant_contact()->text_domain ),
			'id'   => $prefix . 'opt_in',
			'description' => __( 'Add Opt In checkbox to form.', constant_contact()->text_domain ),
			'type' => 'checkbox',
		) );

		$options_metabox->add_field( array(
			'name' => __( 'Opt In Instructions', constant_contact()->text_domain ),
			'id'   => $prefix . 'opt_in_instructions',
			'description' => __( 'Add Opt In instructions.', constant_contact()->text_domain ),
			'type' => 'textarea_small',
		) );

	}


	/**
	 * Custom CMB2 meta box css
	 *
	 * @param integer $post_id current post id.
	 * @param object  $cmb cmb2 metabox object.
	 */
	public function add_custom_css_for_metabox( $post_id, $cmb ) {
		?>
		<style type="text/css" media="screen">

			#ctct_fields_metabox .cmb-row {
				border-bottom: none;
				padding-bottom: 0.5em;
			}

			#ctct_options_metabox .cmb-row {
				border-bottom: none;
			}
			#ctct_options_metabox .cmb2-id--ctct-opt-in,
			#ctct_options_metabox .cmb2-id--ctct-list,
			#ctct_fields_metabox .cmb-remove-field-row {
				border-top: 1px solid #e9e9e9;
			}
			#ctct_options_metabox .cmb2-id--ctct-list {
				padding-bottom: 0.5em;
			}
			#ctct_options_metabox .cmb2-id--ctct-new-list {
				padding: 0 0;
			}
		</style>
		<?php
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

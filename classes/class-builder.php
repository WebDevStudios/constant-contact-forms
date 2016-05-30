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
		add_action( 'cmb2_after_post_form__ctct_fields_metabox', array( $this, 'add_custom_css_for_metabox' ), 10, 2 );

	}

	/**
	 * [form_metaboxes description]
	 *
	 * @param  array $meta_boxes cmb2 metabox appended data.
	 * @return array  cmb2 metabox appended data
	 */
	public function description_metabox() {

		$prefix = '_ctct_';

		/**
		 * Initiate the $fields_metabox
		 */
		$description_metabox = new_cmb2_box( array(
			'id'			=> $prefix . 'description_metabox',
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
	 * [form_metaboxes description]
	 *
	 * @param  array $meta_boxes cmb2 metabox appended data.
	 * @return array  cmb2 metabox appended data
	 */
	public function fields_metabox() {

		$prefix = '_ctct_';

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

		$fields_metabox->add_field( array(
			'description' => __( 'Add fields to this form. Fields are sortable. A required email field will be added to each form.', constant_contact()->text_domain ),
			'id'   => $prefix . 'title',
			'type' => 'title',
		) );

		// Default fields.
		$default_group_field_id = $fields_metabox->add_field( array(
			'id'		  => 'default_group',
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
	 * [form_metaboxes description]
	 *
	 * @param  array $meta_boxes cmb2 metabox appended data.
	 * @return array  cmb2 metabox appended data
	 */
	public function options_metabox() {

		$prefix = '_ctct_';

		/**
		 * Initiate the $fields_metabox
		 */
		$options_metabox = new_cmb2_box( array(
			'id'			=> $prefix . 'options_metabox',
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
				padding-bottom: 1px;
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
	 * @param  object $field	  Current field object
	 */
	public function required_field( $field_args, $field ) {
		//var_dump( $field->args['_id'] );
		echo '	 required <input type="checkbox" class="cmb2-option cmb2-list" name="default_group[0][_ctct_required]['. $field->args['_id'] .']" id="default_group_0_' . $field->args['_id'] . '" value="on">';
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

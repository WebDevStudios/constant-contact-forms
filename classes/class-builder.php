<?php
/**
 * CTCT form Builder Settings
 *
 * @version 1.0.0
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
		if( is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->hooks();
		}
		return self::$instance;
	}

	/**
	 * Initiate our hooks
	 * @since 1.0.0
	 */
	public function hooks() {
		add_filter( 'cmb2_meta_boxes', array( $this, 'form_metaboxes' ) );
		add_action( 'cmb2_after_post_form_fields_metabox', array( $this, 'add_custom_css_for_metabox' ), 10, 2 );

	}


	public function form_metaboxes( array $meta_boxes ) {

		// Start with an underscore to hide fields from custom fields list
		$prefix = '_ctct_';

		$meta_boxes['form_description_metabox'] = array(
			'id'			=> 'description_metabox',
			'title'		 => __( 'Description', constant_contact()->text_domain ),
			'object_types'  => array( 'ctct_forms', ), // Post type
			'context'	   => 'normal',
			'priority'	  => 'high',
			'show_names'	=> false, // Show field names on the left
			'fields'		=> array(
				array(
					'name' => __( 'description_metabox', constant_contact()->text_domain ),
					'id'   => $prefix . 'description',
					'type' => 'textarea_small',
				)
			)
		);

		$meta_boxes['form_fields_metabox'] = array(
			'id'			=> 'fields_metabox',
			'title'		 => __( 'Form Fields', constant_contact()->text_domain ),
			'object_types'  => array( 'ctct_forms', ),
			'context'	   => 'normal',
			'priority'	  => 'high',
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
			)
		);
		return $meta_boxes;
	}


	public function add_custom_css_for_metabox( $post_id, $cmb ) {
		?>
		<style type="text/css" media="screen">
			.postbox-container .cmb2-wrap > .cmb-field-list > .cmb-row {
				padding: 0;
			}
			.postbox-container .cmb-row:not(:last-of-type) {
				border-bottom: none;
			}
			.cmb2-id--ctct-title > .cmb-td,
			.cmb2-id--ctct-custom-title > .cmb-td  {
				padding: 1.8em 0;
			}
			.cmb2-id--ctct-custom-title > .cmb-td {
				border-top: 1px solid #e9e9e9;
			}
			.postbox-container .cmb-repeat-row .cmb-td {
				padding-bottom: 0.3em;
			}
			.postbox-container .cmb2-metabox > .cmb-row.table-layout .cmb-repeat-table .cmb-tbody .cmb-row:not(:first-of-type) .cmb-td {
				padding-top: 0.3em;
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

// Get it started
ctct_builder_admin();

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
	 * Holds an instance of the builder
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
			self::$instance->init();
		}
		return self::$instance;
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

		if ( in_array( $pagenow, array( 'post-new.php', 'post.php' ), true ) ) {

			add_action( 'cmb2_admin_init', array( $this, 'description_metabox' ) );
			add_action( 'cmb2_admin_init', array( $this, 'fields_metabox' ) );
			add_action( 'cmb2_admin_init', array( $this, 'options_metabox' ) );
			add_action( 'cmb2_after_post_form_ctct_description_metabox', array( $this, 'add_custom_css_for_metabox' ), 10, 2 );

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

		// Custom CMB2 fields.
		$fields_metabox->add_field( array(
			'name' => __( 'Add Fields', constant_contact()->text_domain ),
			'description' => __( 'Fields are sortable and can be mapped to Constant Contact default fields.', constant_contact()->text_domain ),
			'id'   => $prefix . 'title',
			'type' => 'title',
		) );

		// Default fields.
		$custom_group_field_id = $fields_metabox->add_field( array(
			'id'		  => 'custom_fields_group',
			'type'		=> 'group',
			'repeatable'  => true,
			'options'	 => array(
				'group_title'   => __( 'Field {#}', constant_contact()->text_domain ),
				'add_button'	=> __( 'Add Another Field', constant_contact()->text_domain ),
				'remove_button' => __( 'Remove Field', constant_contact()->text_domain ),
				'sortable'	  => true,
			),
		) );

		$fields_metabox->add_group_field( $custom_group_field_id, array(
			'name' => __( 'Field Name', constant_contact()->text_domain ),
			'id'   => $prefix . 'field_name',
			'type' => 'text',
			'default' => 'Email',
		) );

		$default_fields = array(
			'custom' => __( 'Custom', constant_contact()->text_domain ),
			'email' => __( 'Email', constant_contact()->text_domain ),
			'first_name' => __( 'First Name', constant_contact()->text_domain ),
			'last_name' => __( 'Last Name', constant_contact()->text_domain ),
			'phone_number' => __( 'Phone Number', constant_contact()->text_domain ),
			'address' => __( 'Address', constant_contact()->text_domain ),
			'job_title' => __( 'Job Title', constant_contact()->text_domain ),
			'company' => __( 'Company', constant_contact()->text_domain ),
			'website' => __( 'Website', constant_contact()->text_domain ),
			'birthday' => __( 'Birthday', constant_contact()->text_domain ),
			'anniversary' => __( 'Anniversary', constant_contact()->text_domain ),
		);

		$fields_metabox->add_group_field( $custom_group_field_id, array(
			'name' => __( 'Map to field', constant_contact()->text_domain ),
			'id'   => $prefix . 'map_select',
			'type' => 'select',
			'show_option_none' => false,
			'default' => 'email',
			'options' => $default_fields,
		) );

		$fields_metabox->add_group_field( $custom_group_field_id, array(
			'name' => __( 'Required', constant_contact()->text_domain ),
			'id'   => $prefix . 'required_field',
			'type' => 'checkbox',
			'row_classes' => 'required',
			'default' => 'on',
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

		// Add field if conncted to API.
		if ( $lists = $this->get_lists() ) {

			$options_metabox->add_field( array(
				'name' => __( 'List', constant_contact()->text_domain ),
				'id'   => $prefix . 'list',
				'description' => __( 'Choose a list.', constant_contact()->text_domain ),
				'type' => 'select',
				'show_option_none' => true,
				'default' => 'none',
				'options' => $lists,
			) );

		}

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

	public function get_lists() {

			$get_lists = array();

			if ( $lists = constantcontact_lists()->get_lists() ) {

				$get_lists['new'] = 'New';

				foreach ( $lists as $list => $value ) {
					$get_lists[ $list ] = $value;
				}

				return $get_lists;

			}

			return false;

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

			#custom_fields_group_repeat .cmb-field-list .cmb-row:not(:last-of-type) {
				border-bottom: none;
				padding-bottom: 0.1em;
			}
			#custom_fields_group_repeat .required {
				padding-bottom: 0.1em;
				padding-top: 0.1em;
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
			#default_fields_group_repeat .cmb-field-list > .cmb-row {
				padding-top: 0.5em;
			}
			#default_fields_group_repeat .cmb-field-list > .cmb-row:not(:last-of-type)  {
				padding-bottom: 0.1em;
				border-bottom: 1px solid #e9e9e9 !important;
			}
			.cmb-repeat-group-wrap .cmb-repeatable-grouping {
				margin: 0 0 1.5em 0;
			}
			.cmb-repeat-group-wrap .cmb-repeatable-grouping .cmb-row {
				margin: 0 0 0 0.3em;
			}
			.postbox-container .cmb-remove-field-row {
				padding-top: 0.8em;
				padding-bottom: 0.8em;
			}
			.cmb-repeat-group-wrap {
				padding: 0 !important;
			}
			.cmb-repeat-group-wrap .cmb-repeat-group-field {
				padding-top: 0.2em;
			}
			button.cmb-add-group-row {
				color: white !important;
				background: #008ec2 !important;
				border-color: #006799 !important;
			}
			a.move-up::after {
			  content: "move up";
			}
			a.move-down::after {
			  content: "move down";
			}
			.cmb2-metabox button.dashicons-before.dashicons-no-alt.cmb-remove-group-row {
				top: .3em;
			}
			button.cmb-remove-group-row {
				background: #ffdfa3 !important;
			}
		</style>
		<?php
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
		global $post;

		if ( isset( $cmbobj->data_to_save['custom_fields_group'] ) ) {
			foreach ( $cmbobj->data_to_save['custom_fields_group'] as $key => $value ) {
				if ( 'email' === $value['_ctct_map_select'] ) {
					update_post_meta( $post->ID, '_ctct_has_email_field', 'true' );
				} else {
					update_post_meta( $post->ID, '_ctct_has_email_field', 'false' );
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

	    if ( isset( $post ) && 'false' === get_post_meta( $post->ID, '_ctct_has_email_field', true ) ) {
			$class = 'notice notice-error';
			$message = __( 'Oop, looks like you havent added an email field to your form. Forms will not send unless a field is mapped to email.', constant_contact()->text_domain );
			printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
	    }
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

<?php
/**
 * ConstantContact_Forms_CPT Class File.
 *
 * @package ConstantContactFormsCPT
 * @subpackage ConstantContact
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Main initiation class.
 *
 * @internal
 *
 * @since 1.0.0
 */
class ConstantContact_CPTS {

	/**
	 * Parent plugin class.
	 *
	 * @var object
	 * @since 1.0.0
	 */
	protected $plugin = null;

	/**
	 * Holds an instance of the object.
	 *
	 * @var object ConstantContact_CPTS
	 * @since 1.0.0
	 */
	private static $instance = null;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $plugin this class.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'constant_contact_forms_post_type' ) );
		add_action( 'init', array( $this, 'constant_contact_lists_post_type' ) );

		add_action( 'admin_menu', array( $this, 'menu_items' ) );

		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
	}

	/**
	 * Register Custom Post Type.
	 *
	 * @since 1.0.0
	 */
	public function constant_contact_forms_post_type() {

		$labels = array(
			'name'				  => _x( 'Forms', 'Post Type General Name', constant_contact()->text_domain ),
			'singular_name'		 => _x( 'Form', 'Post Type Singular Name', constant_contact()->text_domain ),
			'menu_name'			 => __( 'Contact Form', constant_contact()->text_domain ),
			'name_admin_bar'		=> __( 'Contact Form', constant_contact()->text_domain ),
			'archives'			  => __( 'Form Archives', constant_contact()->text_domain ),
			'parent_item_colon'	 => __( 'Parent Form:', constant_contact()->text_domain ),
			'all_items'			 => __( 'All Forms', constant_contact()->text_domain ),
			'add_new_item'		  => __( 'Add New Form', constant_contact()->text_domain ),
			'add_new'			   => __( 'Add New Form', constant_contact()->text_domain ),
			'new_item'			  => __( 'New Form', constant_contact()->text_domain ),
			'edit_item'			 => __( 'Edit Form', constant_contact()->text_domain ),
			'update_item'		   => __( 'Update Form', constant_contact()->text_domain ),
			'view_item'			 => __( 'View Form', constant_contact()->text_domain ),
			'search_items'		  => __( 'Search Form', constant_contact()->text_domain ),
			'not_found'			 => __( 'Not found', constant_contact()->text_domain ),
			'not_found_in_trash'	=> __( 'Not found in Trash', constant_contact()->text_domain ),
			'featured_image'		=> __( 'Featured Image', constant_contact()->text_domain ),
			'set_featured_image'	=> __( 'Set featured image', constant_contact()->text_domain ),
			'remove_featured_image' => __( 'Remove featured image', constant_contact()->text_domain ),
			'use_featured_image'	=> __( 'Use as featured image', constant_contact()->text_domain ),
			'insert_into_item'	  => __( 'Insert into Form', constant_contact()->text_domain ),
			'uploaded_to_this_item' => __( 'Uploaded to this Form', constant_contact()->text_domain ),
			'items_list'			=> __( 'Forms list', constant_contact()->text_domain ),
			'items_list_navigation' => __( 'Forms list navigation', constant_contact()->text_domain ),
			'filter_items_list'	 => __( 'Filter forms list', constant_contact()->text_domain ),
		);
		$args = array(
			'label'			   => __( 'Constant Contact', constant_contact()->text_domain ),
			'description'		 => __( 'Constant Contact forms.', constant_contact()->text_domain ),
			'labels'			  => $labels,
			'supports'			=> array( 'title' ),
			'taxonomies'		  => array(),
			'hierarchical'		=> false,
			'public'			  => false,
			'show_ui'			 => true,
			'show_in_menu'		=> true,
			'menu_position'	   => 20,
			'menu_icon'		   => 'dashicons-megaphone',
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'		  => true,
			'has_archive'		 => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => false,
			'capability_type'	 => 'page',
		);
		register_post_type( 'ctct_forms', $args );

	}

	/**
	 * Register Custom Post Type.
	 *
	 * @since 1.0.0
	 */
	public function constant_contact_lists_post_type() {

		$labels = array(
			'name'				  => _x( 'Lists', 'Post Type General Name', constant_contact()->text_domain ),
			'singular_name'		 => _x( 'List', 'Post Type Singular Name', constant_contact()->text_domain ),
			'menu_name'			 => __( 'Lists', constant_contact()->text_domain ),
			'name_admin_bar'		=> __( 'Lists', constant_contact()->text_domain ),
			'archives'			  => __( 'List Archives', constant_contact()->text_domain ),
			'parent_item_colon'	 => __( 'Parent List:', constant_contact()->text_domain ),
			'all_items'			 => __( 'All Lists', constant_contact()->text_domain ),
			'add_new_item'		  => __( 'Add New List', constant_contact()->text_domain ),
			'add_new'			   => __( 'Add New List', constant_contact()->text_domain ),
			'new_item'			  => __( 'New List', constant_contact()->text_domain ),
			'edit_item'			 => __( 'Edit List', constant_contact()->text_domain ),
			'update_item'		   => __( 'Update List', constant_contact()->text_domain ),
			'view_item'			 => __( 'View List', constant_contact()->text_domain ),
			'search_items'		  => __( 'Search List', constant_contact()->text_domain ),
			'not_found'			 => __( 'Not found', constant_contact()->text_domain ),
			'not_found_in_trash'	=> __( 'Not found in Trash', constant_contact()->text_domain ),
			'featured_image'		=> __( 'Featured Image', constant_contact()->text_domain ),
			'set_featured_image'	=> __( 'Set featured image', constant_contact()->text_domain ),
			'remove_featured_image' => __( 'Remove featured image', constant_contact()->text_domain ),
			'use_featured_image'	=> __( 'Use as featured image', constant_contact()->text_domain ),
			'insert_into_item'	  => __( 'Insert into List', constant_contact()->text_domain ),
			'uploaded_to_this_item' => __( 'Uploaded to this List', constant_contact()->text_domain ),
			'items_list'			=> __( 'Lists list', constant_contact()->text_domain ),
			'items_list_navigation' => __( 'Lists list navigation', constant_contact()->text_domain ),
			'filter_items_list'	 => __( 'Filter lists list', constant_contact()->text_domain ),
		);
		$args = array(
			'label'			   => __( 'Constant Contact', constant_contact()->text_domain ),
			'description'		 => __( 'Constant Contact lists.', constant_contact()->text_domain ),
			'labels'			  => $labels,
			'supports'			=> array( 'title' ),
			'taxonomies'		  => array(),
			'hierarchical'		=> false,
			'public'			  => false,
			'show_ui'			 => true,
			'show_in_menu'		=> true,
			'menu_position'	   => 20,
			'menu_icon'		   => 'dashicons-megaphone',
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'		  => true,
			'has_archive'		 => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'	 => 'page',
		);
		register_post_type( 'ctct_lists', $args );
	}

	/**
	 * Custom post udate messages to match CPT naming
	 *
	 * @param  array $messages default update messages.
	 * @return array appended update messages with custom post types
	 */
	public function post_updated_messages( $messages ) {

		global $post;

		$messages['ctct_lists'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => __( 'List updated.' ),
			2 => __( 'Custom field updated.' ),
			3 => __( 'Custom field deleted.' ),
			4 => __( 'List updated.' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'List restored to revision from %s' ), wp_post_revision_title( ( int ) $_GET['revision'], false ) ) : false,
			6 => __( 'List published.' ),
			7 => __( 'List saved.' ),
			8 => __( 'List submitted.' ),
			9 => __( 'List scheduled for: <strong>%1$s</strong>.' ), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ),
			10 => __( 'List draft updated.' ),
		);

		$messages['ctct_forms'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => __( 'Form updated.' ),
			2 => __( 'Custom field updated.' ),
			3 => __( 'Custom field deleted.' ),
			4 => __( 'Form updated.' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Form restored to revision from %s' ), wp_post_revision_title( ( int ) $_GET['revision'], false ) ) : false,
			6 => __( 'Form published.' ),
			7 => __( 'Form saved.' ),
			8 => __( 'Form submitted.' ),
			9 => __( 'Form scheduled for: <strong>%1$s</strong>.' ), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ),
			10 => __( 'Form draft updated.' ),
		);

		return $messages;
	}


	/**
	 * Customize admin menu items
	 *
	 * @return void
	 */
	public function menu_items() {

		// Remove top level CPT menu.
		remove_menu_page( 'edit.php?post_type=ctct_lists' );

		if ( constantcontact_api()->get_account_info() ) {
			// Add List submenu item for CPT.
			add_submenu_page(
				'edit.php?post_type=ctct_forms',
				__( 'Lists', constant_contact()->text_domain ),
				__( 'Lists', constant_contact()->text_domain ),
				'manage_options',
				'edit.php?post_type=ctct_lists',
				''
			);
		}
	}
}

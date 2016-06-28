<?php
/**
 * ConstantContact_Forms_CPT Class File.
 *
 * @package ConstantContact_CPTS
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
		add_action( 'init', array( $this, 'forms_post_type' ) );
		add_action( 'init', array( $this, 'lists_post_type' ) );

		add_action( 'admin_menu', array( $this, 'menu_items' ) );

		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
	}

	/**
	 * Register Custom Post Type.
	 *
	 * @since 1.0.0
	 */
	public function forms_post_type() {

		$labels = array(
			'name'                  => _x( 'Forms', 'Post Type General Name', 'constantcontact' ),
			'singular_name'         => _x( 'Form', 'Post Type Singular Name', 'constantcontact' ),
			'menu_name'             => __( 'Contact Form', 'constantcontact' ),
			'name_admin_bar'        => __( 'Contact Form', 'constantcontact' ),
			'archives'              => __( 'Form Archives', 'constantcontact' ),
			'parent_item_colon'     => __( 'Parent Form:', 'constantcontact' ),
			'all_items'             => __( 'All Forms', 'constantcontact' ),
			'add_new_item'          => __( 'Add New Form', 'constantcontact' ),
			'add_new'               => __( 'Add New Form', 'constantcontact' ),
			'new_item'              => __( 'New Form', 'constantcontact' ),
			'edit_item'             => __( 'Edit Form', 'constantcontact' ),
			'update_item'           => __( 'Update Form', 'constantcontact' ),
			'view_item'             => __( 'View Form', 'constantcontact' ),
			'search_items'          => __( 'Search Form', 'constantcontact' ),
			'not_found'             => __( 'Not found', 'constantcontact' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'constantcontact' ),
			'featured_image'        => __( 'Featured Image', 'constantcontact' ),
			'set_featured_image'    => __( 'Set featured image', 'constantcontact' ),
			'remove_featured_image' => __( 'Remove featured image', 'constantcontact' ),
			'use_featured_image'    => __( 'Use as featured image', 'constantcontact' ),
			'insert_into_item'      => __( 'Insert into Form', 'constantcontact' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Form', 'constantcontact' ),
			'items_list'            => __( 'Forms list', 'constantcontact' ),
			'items_list_navigation' => __( 'Forms list navigation', 'constantcontact' ),
			'filter_items_list'     => __( 'Filter forms list', 'constantcontact' ),
		);
		$args = array(
			'label'               => __( 'Constant Contact', 'constantcontact' ),
			'description'         => __( 'Constant Contact forms.', 'constantcontact' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'taxonomies'          => array(),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 20,
			'menu_icon'           => 'dashicons-megaphone',
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => false,
			'capability_type'     => 'page',
		);
		register_post_type( 'ctct_forms', $args );

	}

	/**
	 * Register Custom Post Type.
	 *
	 * @since 1.0.0
	 */
	public function lists_post_type() {

		$labels = array(
			'name'                  => _x( 'Lists', 'Post Type General Name', 'constantcontact' ),
			'singular_name'         => _x( 'List', 'Post Type Singular Name', 'constantcontact' ),
			'menu_name'             => __( 'Lists', 'constantcontact' ),
			'name_admin_bar'        => __( 'Lists', 'constantcontact' ),
			'archives'              => __( 'List Archives', 'constantcontact' ),
			'parent_item_colon'	    => __( 'Parent List:', 'constantcontact' ),
			'all_items'             => __( 'All Lists', 'constantcontact' ),
			'add_new_item'          => __( 'Add New List', 'constantcontact' ),
			'add_new'               => __( 'Add New List', 'constantcontact' ),
			'new_item'              => __( 'New List', 'constantcontact' ),
			'edit_item'             => __( 'Edit List', 'constantcontact' ),
			'update_item'           => __( 'Update List', 'constantcontact' ),
			'view_item'             => __( 'View List', 'constantcontact' ),
			'search_items'          => __( 'Search List', 'constantcontact' ),
			'not_found'             => __( 'Not found', 'constantcontact' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'constantcontact' ),
			'featured_image'        => __( 'Featured Image', 'constantcontact' ),
			'set_featured_image'    => __( 'Set featured image', 'constantcontact' ),
			'remove_featured_image' => __( 'Remove featured image', 'constantcontact' ),
			'use_featured_image'    => __( 'Use as featured image', 'constantcontact' ),
			'insert_into_item'	     => __( 'Insert into List', 'constantcontact' ),
			'uploaded_to_this_item' => __( 'Uploaded to this List', 'constantcontact' ),
			'items_list'            => __( 'Lists list', 'constantcontact' ),
			'items_list_navigation' => __( 'Lists list navigation', 'constantcontact' ),
			'filter_items_list'	    => __( 'Filter lists list', 'constantcontact' ),
		);
		$args = array(
			'label'               => __( 'Constant Contact', 'constantcontact' ),
			'description'         => __( 'Constant Contact lists.', 'constantcontact' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'taxonomies'          => array(),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 20,
			'menu_icon'           => 'dashicons-megaphone',
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'page',
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
			1 => __( 'List updated.', 'constantcontact' ),
			2 => __( 'Custom field updated.', 'constantcontact' ),
			3 => __( 'Custom field deleted.', 'constantcontact' ),
			4 => __( 'List updated.', 'constantcontact' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'List restored to revision from %s', 'constantcontact' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => __( 'List published.', 'constantcontact' ),
			7 => __( 'List saved.', 'constantcontact' ),
			8 => __( 'List submitted.', 'constantcontact' ),
			9 => __( 'List scheduled for: <strong>%1$s</strong>.', 'constantcontact' ), date_i18n( 'M j, Y @ G:i', strtotime( $post->post_date ) ),
			10 => __( 'List draft updated.', 'constantcontact' ),
		);

		$messages['ctct_forms'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => __( 'Form updated.', 'constantcontact' ),
			2 => __( 'Custom field updated.', 'constantcontact' ),
			3 => __( 'Custom field deleted.', 'constantcontact' ),
			4 => __( 'Form updated.', 'constantcontact' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Form restored to revision from %s', 'constantcontact' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => __( 'Form published.', 'constantcontact' ),
			7 => __( 'Form saved.', 'constantcontact' ),
			8 => __( 'Form submitted.', 'constantcontact' ),
			9 => __( 'Form scheduled for: <strong>%1$s</strong>.', 'constantcontact' ), date_i18n( 'M j, Y @ G:i', strtotime( $post->post_date ) ),
			10 => __( 'Form draft updated.', 'constantcontact' ),
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

		// Make sure we're connected before adding our lists;
		if ( constantcontact_api()->get_account_info() ) {
			// Add List submenu item for CPT.
			add_submenu_page(
				'edit.php?post_type=ctct_forms',
				__( 'Lists', 'constantcontact' ),
				__( 'Lists', 'constantcontact' ),
				'manage_options',
				'edit.php?post_type=ctct_lists',
				''
			);
		}
	}
}

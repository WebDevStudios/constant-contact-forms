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
class ConstantContact_Forms_CPT {

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
	 * @var object buddypages_Pages_CPT
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
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'init', array( $this, 'constant_contact_forms_post_type' ) );
	}


	/**
	 * Set it off!
	 *
	 * @since 1.0.0
	 */
	public function init() {
	}


	/**
	 * Register Custom Post Type.
	 *
	 * @since 1.0.0
	 */
	public function constant_contact_forms_post_type() {

		$labels = array(
			'name'                  => _x( 'Constant Contact', 'Post Type General Name', 'buddypages' ),
			'singular_name'         => _x( 'Constant Contact', 'Post Type Singular Name', 'buddypages' ),
			'menu_name'             => __( 'Constant Contact', 'buddypages' ),
			'name_admin_bar'        => __( 'Constant Contact', 'buddypages' ),
			'archives'              => __( 'Item Archives', 'buddypages' ),
			'parent_item_colon'     => __( 'Parent Item:', 'buddypages' ),
			'all_items'             => __( 'All Items', 'buddypages' ),
			'add_new_item'          => __( 'Add New Item', 'buddypages' ),
			'add_new'               => __( 'Add New', 'buddypages' ),
			'new_item'              => __( 'New Item', 'buddypages' ),
			'edit_item'             => __( 'Edit Item', 'buddypages' ),
			'update_item'           => __( 'Update Item', 'buddypages' ),
			'view_item'             => __( 'View Item', 'buddypages' ),
			'search_items'          => __( 'Search Item', 'buddypages' ),
			'not_found'             => __( 'Not found', 'buddypages' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'buddypages' ),
			'featured_image'        => __( 'Featured Image', 'buddypages' ),
			'set_featured_image'    => __( 'Set featured image', 'buddypages' ),
			'remove_featured_image' => __( 'Remove featured image', 'buddypages' ),
			'use_featured_image'    => __( 'Use as featured image', 'buddypages' ),
			'insert_into_item'      => __( 'Insert into item', 'buddypages' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'buddypages' ),
			'items_list'            => __( 'Items list', 'buddypages' ),
			'items_list_navigation' => __( 'Items list navigation', 'buddypages' ),
			'filter_items_list'     => __( 'Filter items list', 'buddypages' ),
		);
		$args = array(
			'label'               => __( 'Constant Contact', 'buddypages' ),
			'description'         => __( 'BuddyPress user pages for profiles and groups', 'buddypages' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'author' ),
			'taxonomies'          => array(),
			'hierarchical'        => true,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 20,
			'menu_icon'           => 'dashicons-megaphone',
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
		);
		register_post_type( 'ctct_forms', $args );

	}
}

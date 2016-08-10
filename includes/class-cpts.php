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

		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_filter( 'enter_title_here', array( $this, 'change_default_title' ) );
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
			'menu_icon'           => constant_contact()->url . 'assets/images/ctct-icon.png',
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
			'all_items'             => __( 'Lists', 'constantcontact' ),
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
			'show_in_menu'        => 'edit.php?post_type=ctct_forms',
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

		// Only register if we're connected
		if ( constantcontact_api()->is_connected() ) {
			register_post_type( 'ctct_lists', $args );
		}
	}

	/**
	 * Custom post udate messages to match CPT naming
	 *
	 * @since  1.0.0
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
	 * Customize the "Enter your title" placeholder text for Title field
	 *
	 * @since  1.0.0
	 * @param  string $title desired placeholder text.
	 * @return string $title output string
	 */
	public function change_default_title( $title ) {
	    global $post;

	    // Sanity check
	    if ( ! isset( $post ) ) {
	    	return $title;
	    }

	    // Check for post type
	    if ( ! isset( $post->post_type ) ) {
	    	return $title;
	    }

	    // If we're on our forms post type
	    if ( 'ctct_forms' === $post->post_type ) {
	    	$title = sprintf(
	    		'%s <span class="ctct-admin-title-details">%s</span>',
	    		__( 'Enter a form name', 'constantcontact' ),
	    		__( '(Examples: Join Our Email List, Subscribe to Emails)', 'constantcontact' )
			);

	    }

	    return $title;
	}

	/**
	 * Returns array of form ids
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_forms( $expanded_data = false, $bust_cache = false ) {

		// Grab our saved transient.
		$forms = get_transient( 'constant_contact_shortcode_form_list' );

		// Allow bypassing transient check.
		$bypass_forms = apply_filters( 'constant_contact_bypass_shotcode_forms', false );

		// If we dont have a transient or we bypass, go through the motions.
		if ( false === $forms || $bypass_forms || $bust_cache ) {

			// Get all our forms that we have.
			$query = new WP_Query( array(
				'post_status'            => 'publish',
				'post_type'              => 'ctct_forms',
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
			) );

			// Grab the posts.
			$q_forms = $query->get_posts();

			// If for some reason we got an error, just return a blank array.
			if ( is_wp_error( $q_forms ) && ! is_array( $q_forms ) ) {
				return array();
			}

			// If we're not using this for the shortcode in the admin, just return
			// the IDs of our forms
			if ( ! $expanded_data ) {
				return $q_forms;
			}

			// Set up our default array.
			$forms = array();

			// Foreach form we have, lets build up our return array.
			foreach ( $q_forms as $form ) {

				// Make sure we have the data we want to use.
				if (
					isset( $form->ID ) &&
					$form->ID &&
					isset( $form->post_title ) &&
					isset( $form->post_modified )
				) {

					// Get our title
					$title = ( $form->post_title ) ? $form->post_title : __( 'No title', 'constantcontact' );

					// Get the last modified time in human text
					$last_modified = human_time_diff( strtotime( $form->post_modified ), time() );

					// Build up our title for the shortcode form admin
					$title = sprintf(
						esc_html__( '%s (last modified %s ago)', 'constantcontact' ),
						$title,
						$last_modified
					);

					// Clean that data before we use it.
					$forms[ absint( $form->ID ) ] = $title;
				}
			}

			// Save that.
			set_transient( 'constant_contact_shortcode_form_list', $forms, 1 * HOUR_IN_SECONDS );
		}

		return $forms;
	}
}

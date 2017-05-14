<?php
/**
 * Custom Post Types.
 *
 * @package ConstantContact
 * @subpackage CPTS
 * @author Constant Contact
 * @since 1.0.0
 */

/**
 * Powers our custom post types.
 *
 * @internal
 *
 * @since 1.0.0
 */
class ConstantContact_CPTS {

	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @param object $plugin Parent class.
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
			'name'                  => _x( 'Forms', 'Post Type General Name', 'constant-contact-forms' ),
			'singular_name'         => _x( 'Form', 'Post Type Singular Name', 'constant-contact-forms' ),
			'menu_name'             => __( 'Contact Form', 'constant-contact-forms' ),
			'name_admin_bar'        => __( 'Contact Form', 'constant-contact-forms' ),
			'archives'              => __( 'Form Archives', 'constant-contact-forms' ),
			'parent_item_colon'     => __( 'Parent Form:', 'constant-contact-forms' ),
			'all_items'             => __( 'All Forms', 'constant-contact-forms' ),
			'add_new_item'          => __( 'Add New Form', 'constant-contact-forms' ),
			'add_new'               => __( 'Add New Form', 'constant-contact-forms' ),
			'new_item'              => __( 'New Form', 'constant-contact-forms' ),
			'edit_item'             => __( 'Edit Form', 'constant-contact-forms' ),
			'update_item'           => __( 'Update Form', 'constant-contact-forms' ),
			'view_item'             => __( 'View Form', 'constant-contact-forms' ),
			'search_items'          => __( 'Search Form', 'constant-contact-forms' ),
			'not_found'             => __( 'No forms found', 'constant-contact-forms' ),
			'not_found_in_trash'    => __( 'No forms found in Trash', 'constant-contact-forms' ),
			'featured_image'        => __( 'Featured Image', 'constant-contact-forms' ),
			'set_featured_image'    => __( 'Set featured image', 'constant-contact-forms' ),
			'remove_featured_image' => __( 'Remove featured image', 'constant-contact-forms' ),
			'use_featured_image'    => __( 'Use as featured image', 'constant-contact-forms' ),
			'insert_into_item'      => __( 'Insert into Form', 'constant-contact-forms' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Form', 'constant-contact-forms' ),
			'items_list'            => __( 'Forms list', 'constant-contact-forms' ),
			'items_list_navigation' => __( 'Forms list navigation', 'constant-contact-forms' ),
			'filter_items_list'     => __( 'Filter forms list', 'constant-contact-forms' ),
		);
		$args = array(
			'label'               => __( 'Constant Contact', 'constant-contact-forms' ),
			'description'         => __( 'Constant Contact forms.', 'constant-contact-forms' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'taxonomies'          => array(),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 20,
			'menu_icon'           => constant_contact()->url . 'assets/images/ctct-icon.png',
			'show_in_admin_bar'   => true,
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
			'name'                  => _x( 'Lists', 'Post Type General Name', 'constant-contact-forms' ),
			'singular_name'         => _x( 'List', 'Post Type Singular Name', 'constant-contact-forms' ),
			'menu_name'             => __( 'Lists', 'constant-contact-forms' ),
			'name_admin_bar'        => __( 'Lists', 'constant-contact-forms' ),
			'archives'              => __( 'List Archives', 'constant-contact-forms' ),
			'parent_item_colon'	    => __( 'Parent List:', 'constant-contact-forms' ),
			'all_items'             => __( 'Lists', 'constant-contact-forms' ),
			'add_new_item'          => __( 'Add New List', 'constant-contact-forms' ),
			'add_new'               => __( 'Add New List', 'constant-contact-forms' ),
			'new_item'              => __( 'New List', 'constant-contact-forms' ),
			'edit_item'             => __( 'Edit List', 'constant-contact-forms' ),
			'update_item'           => __( 'Update List', 'constant-contact-forms' ),
			'view_item'             => __( 'View List', 'constant-contact-forms' ),
			'search_items'          => __( 'Search List', 'constant-contact-forms' ),
			'not_found'             => __( 'Not found', 'constant-contact-forms' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'constant-contact-forms' ),
			'featured_image'        => __( 'Featured Image', 'constant-contact-forms' ),
			'set_featured_image'    => __( 'Set featured image', 'constant-contact-forms' ),
			'remove_featured_image' => __( 'Remove featured image', 'constant-contact-forms' ),
			'use_featured_image'    => __( 'Use as featured image', 'constant-contact-forms' ),
			'insert_into_item'	     => __( 'Insert into List', 'constant-contact-forms' ),
			'uploaded_to_this_item' => __( 'Uploaded to this List', 'constant-contact-forms' ),
			'items_list'            => __( 'Lists list', 'constant-contact-forms' ),
			'items_list_navigation' => __( 'Lists list navigation', 'constant-contact-forms' ),
			'filter_items_list'	    => __( 'Filter lists list', 'constant-contact-forms' ),
		);
		$args = array(
			'label'               => __( 'Constant Contact', 'constant-contact-forms' ),
			'description'         => __( 'Constant Contact lists.', 'constant-contact-forms' ),
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

		// Only register if we're connected.
		if ( constantcontact_api()->is_connected() ) {
			register_post_type( 'ctct_lists', $args );
		}
	}

	/**
	 * Custom post udate messages to match CPT naming.
	 *
	 * @since 1.0.0
	 *
	 * @param array $messages Default update messages.
	 * @return array appended update messages with custom post types.
	 */
	public function post_updated_messages( $messages ) {

		global $post;

		$messages['ctct_lists'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => __( 'List updated.', 'constant-contact-forms' ),
			2 => __( 'Custom field updated.', 'constant-contact-forms' ),
			3 => __( 'Custom field deleted.', 'constant-contact-forms' ),
			4 => __( 'List updated.', 'constant-contact-forms' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'List restored to revision from %s', 'constant-contact-forms' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false, // Input var okay.
			6 => __( 'List published.', 'constant-contact-forms' ),
			7 => __( 'List saved.', 'constant-contact-forms' ),
			8 => __( 'List submitted.', 'constant-contact-forms' ),
			9 => __( 'List scheduled for: <strong>%1$s</strong>.', 'constant-contact-forms' ), date_i18n( 'M j, Y @ G:i', strtotime( $post->post_date ) ),
			10 => __( 'List draft updated.', 'constant-contact-forms' ),
		);

		$messages['ctct_forms'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => __( 'Form updated.', 'constant-contact-forms' ),
			2 => __( 'Custom field updated.', 'constant-contact-forms' ),
			3 => __( 'Custom field deleted.', 'constant-contact-forms' ),
			4 => __( 'Form updated.', 'constant-contact-forms' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Form restored to revision from %s', 'constant-contact-forms' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,  // Input var okay.
			6 => sprintf( __( "Success! Here's the shortcode: %s. Just paste it into a post or page editor to publish", 'constant-contact-forms' ), '<strong>' . constant_contact_display_shortcode( $post->ID ) . '</strong>' ),
			7 => __( 'Form saved.', 'constant-contact-forms' ),
			8 => __( 'Form submitted.', 'constant-contact-forms' ),
			9 => __( 'Form scheduled for: <strong>%1$s</strong>.', 'constant-contact-forms' ), date_i18n( 'M j, Y @ G:i', strtotime( $post->post_date ) ),
			10 => __( 'Form draft updated.', 'constant-contact-forms' ),
		);

		return $messages;
	}

	/**
	 * Customize the "Enter your title" placeholder text for Title field.
	 *
	 * @since 1.0.0
	 *
	 * @param string $title Desired placeholder text.
	 * @return string $title output string
	 */
	public function change_default_title( $title ) {
	    global $post;

	    if ( ! isset( $post ) ) {
	    	return $title;
	    }

	    if ( ! isset( $post->post_type ) ) {
	    	return $title;
	    }

	    // If we're on our forms post type.
	    if ( 'ctct_forms' === $post->post_type ) {
	    	$title = sprintf(
	    		'%s <span class="ctct-admin-title-details">%s</span>',
				__( 'Enter a form name', 'constant-contact-forms' ),
				__( '(Examples: Join Our Email List, Contact Us)', 'constant-contact-forms' )
			);

	    }

	    return $title;
	}

	/**
	 * Returns array of form ids.
	 * Can return more information with `true` passed to the first parameter.
	 * Caches results, pass `true` to the second parameter to bust the cache.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $expanded_data Set `true` to process the retrieved posts.
	 * @param bool $bust_cache    Set `true` to bust the cached forms.
	 * @return array
	 */
	public function get_forms( $expanded_data = false, $bust_cache = false ) {

		$forms = get_transient( 'constant_contact_shortcode_form_list' );

		/**
		 * Filters whether or not to bypass transient checks.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $value Whether or not to bypass.
		 */
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

			$q_forms = $query->get_posts();

			// If for some reason we got an error, just return a blank array.
			if ( is_wp_error( $q_forms ) && ! is_array( $q_forms ) ) {
				return array();
			}

			// If we're not using this for the shortcode in the admin, just return
			// the IDs of our forms.
			if ( ! $expanded_data ) {
				return $q_forms;
			}

			$forms = array();

			foreach ( $q_forms as $form ) {

				// Make sure we have the data we want to use.
				if (
					isset( $form->ID ) &&
					$form->ID &&
					isset( $form->post_title ) &&
					isset( $form->post_modified )
				) {

					// Get our title.
					$title = ( $form->post_title ) ? $form->post_title : __( 'No title', 'constant-contact-forms' );

					// Get the last modified time in human text.
					$last_modified = human_time_diff( strtotime( $form->post_modified ), time() );

					// Build up our title for the shortcode form admin.
					$title = sprintf(
						// translators: Placeholders will be form title and then last modified date.
						esc_html__( '%1$s (last modified %2$s ago)', 'constant-contact-forms' ),
						$title,
						$last_modified
					);

					// Clean that data before we use it.
					$forms[ absint( $form->ID ) ] = $title;
				}
			}

			set_transient( 'constant_contact_shortcode_form_list', $forms, 1 * HOUR_IN_SECONDS );
		} // End if().

		return $forms;
	}
}

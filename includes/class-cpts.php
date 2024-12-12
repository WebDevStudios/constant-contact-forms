<?php
/**
 * Custom Post Types.
 *
 * @package ConstantContact
 * @subpackage CPTS
 * @author Constant Contact
 * @since 1.0.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
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
	protected $plugin;

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
		add_action( 'init', [ $this, 'forms_post_type' ] );
		add_action( 'init', [ $this, 'lists_post_type' ] );

		add_filter( 'post_updated_messages', [ $this, 'post_updated_messages' ] );
		add_filter( 'enter_title_here', [ $this, 'change_default_title' ] );

		add_filter( 'post_row_actions', [ $this, 'duplicate_form_link' ], 10, 2 );
		add_action( 'admin_menu', [ $this, 'maybe_duplicate_form' ] );
		add_action( 'admin_notices', [ $this, 'admin_notices' ] );
	}

	/**
	 * Register Custom Post Type.
	 *
	 * @since 1.0.0
	 */
	public function forms_post_type() {

		$labels = [
			'name'                  => _x( 'Forms', 'Post Type General Name', 'constant-contact-forms' ),
			'singular_name'         => _x( 'Form', 'Post Type Singular Name', 'constant-contact-forms' ),
			'menu_name'             => __( 'Contact Form', 'constant-contact-forms' ),
			'name_admin_bar'        => __( 'Contact Form', 'constant-contact-forms' ),
			'archives'              => __( 'Form Archives', 'constant-contact-forms' ),
			'parent_item_colon'     => __( 'Parent Form:', 'constant-contact-forms' ),
			'all_items'             => __( 'Forms', 'constant-contact-forms' ),
			'add_new_item'          => __( 'Add New Form', 'constant-contact-forms' ),
			'add_new'               => __( 'Add Form', 'constant-contact-forms' ),
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
		];
		$args   = [
			'label'               => __( 'Constant Contact', 'constant-contact-forms' ),
			'description'         => __( 'Constant Contact forms.', 'constant-contact-forms' ),
			'labels'              => $labels,
			'supports'            => [ 'title' ],
			'taxonomies'          => [],
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
			'show_in_rest'        => true,
		];
		register_post_type( 'ctct_forms', $args );

	}

	/**
	 * Register Custom Post Type.
	 *
	 * @since 1.0.0
	 */
	public function lists_post_type() {

		$labels = [
			'name'                  => _x( 'Lists', 'Post Type General Name', 'constant-contact-forms' ),
			'singular_name'         => _x( 'List', 'Post Type Singular Name', 'constant-contact-forms' ),
			'menu_name'             => __( 'Lists', 'constant-contact-forms' ),
			'name_admin_bar'        => __( 'Lists', 'constant-contact-forms' ),
			'archives'              => __( 'List Archives', 'constant-contact-forms' ),
			'parent_item_colon'     => __( 'Parent List:', 'constant-contact-forms' ),
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
			'insert_into_item'      => __( 'Insert into List', 'constant-contact-forms' ),
			'uploaded_to_this_item' => __( 'Uploaded to this List', 'constant-contact-forms' ),
			'items_list'            => __( 'Lists list', 'constant-contact-forms' ),
			'items_list_navigation' => __( 'Lists list navigation', 'constant-contact-forms' ),
			'filter_items_list'     => __( 'Filter lists list', 'constant-contact-forms' ),
		];
		$args   = [
			'label'               => __( 'Constant Contact', 'constant-contact-forms' ),
			'description'         => __( 'Constant Contact lists.', 'constant-contact-forms' ),
			'labels'              => $labels,
			'supports'            => [ 'title' ],
			'taxonomies'          => [],
			'hierarchical'        => false,
			'public'              => true,
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
		];

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

		$revision = filter_input( INPUT_GET, 'revision', FILTER_SANITIZE_NUMBER_INT );

		$messages['ctct_lists'] = [
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'List updated.', 'constant-contact-forms' ),
			2  => __( 'Custom field updated.', 'constant-contact-forms' ),
			3  => __( 'Custom field deleted.', 'constant-contact-forms' ),
			4  => __( 'List updated.', 'constant-contact-forms' ),
			5  => ! empty( $revision ) ?
				/* translators: formatted revision timestamp. */
				sprintf( __( 'List restored to revision from %s', 'constant-contact-forms' ), wp_post_revision_title( $revision, false ) ) :
				false,
			6  => __( 'List published.', 'constant-contact-forms' ),
			7  => __( 'List saved.', 'constant-contact-forms' ),
			8  => __( 'List submitted.', 'constant-contact-forms' ),
			/* translators: formatted post date timestamp. */
			9  => __( 'List scheduled for: <strong>%1$s</strong>.', 'constant-contact-forms' ),
			date_i18n( 'M j, Y @ G:i', strtotime( $post->post_date ) ),
			10 => __( 'List draft updated.', 'constant-contact-forms' ),
		];

		$messages['ctct_forms'] = [
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Form updated.', 'constant-contact-forms' ),
			2  => __( 'Custom field updated.', 'constant-contact-forms' ),
			3  => __( 'Custom field deleted.', 'constant-contact-forms' ),
			4  => __( 'Form updated.', 'constant-contact-forms' ),
			5  => ! empty( $revision ) ?
				/* translators: formatted revision timestamp. */
				sprintf( __( 'Form restored to revision from %s', 'constant-contact-forms' ), wp_post_revision_title( $revision, false ) ) :
				false,
			6  => sprintf(
					/* translators: form shortcode. */
				__( "Success! Here's the shortcode: %s. Just paste it into a post or page editor to publish", 'constant-contact-forms' ),
				'<strong>' . constant_contact_display_shortcode( $post->ID ) . '</strong>'
			),
			7  => __( 'Form saved.', 'constant-contact-forms' ),
			8  => __( 'Form submitted.', 'constant-contact-forms' ),
			/* translators: formatted post date timestamp. */
			9  => __( 'Form scheduled for: <strong>%1$s</strong>.', 'constant-contact-forms' ),
			date_i18n( 'M j, Y @ G:i', strtotime( $post->post_date ) ),
			10 => __( 'Form draft updated.', 'constant-contact-forms' ),
		];

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

		$forms = get_transient( ConstantContact_Shortcode::FORMS_LIST_TRANSIENT );

		/**
		 * Filters whether or not to bypass transient checks.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $value Whether or not to bypass.
		 */
		$bypass_forms = apply_filters( 'constant_contact_bypass_shotcode_forms', false );

		if ( false === $forms || $bypass_forms || $bust_cache ) {

			$query = new WP_Query(
				[
					'post_status'            => 'publish',
					'post_type'              => 'ctct_forms',
					'no_found_rows'          => true,
					'update_post_term_cache' => false,
				]
			);

			$q_forms = $query->get_posts();

			if ( is_wp_error( $q_forms ) && ! is_array( $q_forms ) ) {
				return [];
			}

			if ( ! $expanded_data ) {
				return $q_forms;
			}

			$forms = [];

			foreach ( $q_forms as $form ) {

				if (
					isset( $form->ID ) &&
					$form->ID &&
					isset( $form->post_title ) &&
					isset( $form->post_modified )
				) {
					$title         = $form->post_title ?: __( 'No title', 'constant-contact-forms' );
					$last_modified = human_time_diff( strtotime( $form->post_modified ), current_time( 'timestamp' ) ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested

					$title = sprintf(
						// translators: Placeholders will be form title and then last modified date.
						esc_html__( '%1$s (last modified %2$s ago)', 'constant-contact-forms' ),
						$title,
						$last_modified
					);

					$forms[ absint( $form->ID ) ] = $title;
				}
			}

			set_transient( ConstantContact_Shortcode::FORMS_LIST_TRANSIENT, $forms, 1 * HOUR_IN_SECONDS );
		}

		return $forms;
	}

	/**
	 * Add a "Duplicate form" action to forms in our `ctct_forms` list table.
	 *
	 * @since 2.8.0
	 *
	 * @param array   $actions Current array of actions for a post in the list table,
	 * @param WP_Post $post    Post object for the current post being listed.
	 *
	 * @return array
	 */
	public function duplicate_form_link( $actions, $post ) {
		if ( 'ctct_forms' !== $post->post_type ) {
			return $actions;
		}

		if ( current_user_can( 'edit_posts' ) ) {
			$duplicate_url_args = [
				'action'  => 'duplicate_ctct_form',
				'post_id' => absint( $post->ID ),
			];
			$duplicate_url = add_query_arg(
				$duplicate_url_args, admin_url( 'edit.php?post_type=ctct_forms' )
			);

			$actions['ctct-forms-duplicate'] = sprintf(
				'<a href="%1$s">%2$s</a>',
				esc_url( wp_nonce_url( $duplicate_url, 'ctct_duplicate_form', 'ctct_duplicate_form' ) ),
				esc_html__( 'Duplicate form', 'constant-contact-forms' )
			);
		}

		return $actions;
	}

	/**
	 * Maybe prrocess a clicked "Duplicate form" link.
	 *
	 * @since 2.8.0
	 */
	public function maybe_duplicate_form() {
		if ( empty( $_GET ) ) {
			return;
		}

		if (
			isset( $_GET['ctct_duplicate_form'] ) &&
			check_admin_referer( 'ctct_duplicate_form', 'ctct_duplicate_form' )
		) {
			if ( ! isset( $_GET['post_id'] ) ) {
				wp_die( esc_html__( 'No form to duplicate has been supplied.', 'constant-contact-forms' ) );
			}

			$returned_id = $this->duplicate_form( absint( $_GET['post_id'] ) );

			$success = 'false';
			if ( $returned_id ) {
				$success = 'true';
			}

			wp_safe_redirect(
				add_query_arg(
					[
						'ctct_duplicate_form_success' => $success
					],
					admin_url( 'edit.php?post_type=ctct_forms' )
				)
			);
			exit();
		}
	}

	/**
	 * Perform a duplication of a clicked form.
	 *
	 * @since 2.8.0
	 *
	 * @param int $post_id Form ID to duplicate.
	 * @return false|int|WP_Error
	 */
	protected function duplicate_form( int $post_id ) {
		$to_copy_post = get_post( $post_id );
		$curr_user    = wp_get_current_user();
		$to_be_author = $curr_user->ID;

		if ( ! empty( $to_copy_post ) ) {
			$form_args = [
				'comment_status' => $to_copy_post->comment_status,
				'ping_status'    => $to_copy_post->ping_status,
				'post_author'    => $to_be_author,
				'post_content'   => $to_copy_post->post_content,
				'post_excerpt'   => $to_copy_post->post_excerpt,
				'post_name'      => $to_copy_post->post_name,
				'post_status'    => 'publish',
				'post_title'     => $to_copy_post->post_title,
				'post_type'      => 'ctct_forms',
			];

			$copied_form_post_id = wp_insert_post( $form_args );

			$meta_keys   = get_post_meta( $to_copy_post->ID );
			$copied_meta = [];
			foreach ( $meta_keys as $meta_key => $meta_key_value ) {
				// WP has a polyfill for this PHP8 function
				if ( str_starts_with( $meta_key, '_ctct_' ) ) {
					$copied_meta[ $meta_key ] = maybe_unserialize( $meta_key_value[0] );
				}
			}
			$copied_meta['custom_fields_group'] = maybe_unserialize( $meta_keys['custom_fields_group'][0] );

			foreach ( $copied_meta as $meta_key => $meta_value ) {
				update_post_meta( $copied_form_post_id, $meta_key, $meta_value );
			}

			return $copied_form_post_id;
		}

		return false;
	}

	/**
	 * Add an admin notice with success or failure messaging for form duplication attempts.
	 *
	 * @since 2.8.0
	 */
	public function admin_notices() {
		if ( empty( $_GET ) ) {
			return;
		}

		if ( empty( $_GET['ctct_duplicate_form_success'] ) ) {
			return;
		}

		$message = ( 'true' === sanitize_text_field( $_GET['ctct_duplicate_form_success'] ) ) ?
			esc_html__( 'Constant Contact Forms form duplication succeeded.', 'constant-contact-forms' ) :
			esc_html__( 'Constant Contact Forms form duplication failed.', 'constant-contact-forms' );
		$type    = ( 'true' === sanitize_text_field( $_GET['ctct_duplicate_form_success'] ) ) ? 'success' : 'error';
		wp_admin_notice(
			$message,
			array(
				'id'          => 'ctct_form_duplication_notice',
				'type'        => $type,
				'dismissible' => true,
			)
		);
	}
}

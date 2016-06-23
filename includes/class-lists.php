<?php
/**
 * ConstantContact_Lists class
 *
 * @package ConstantContactLists
 * @subpackage ConstantContact
 * @author Pluginize
 * @since 1.0.0
 */

use Ctct\ConstantContact;
use Ctct\Exceptions\CtctException;

/**
 * Class ConstantContact_Lists
 */
class ConstantContact_Lists {

	/**
	 * Parent plugin class
	 *
	 * @var   class
	 * @since 0.0.1
	 */
	protected $plugin = null;

	/**
	 * Constructor
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Returns the running object
	 *
	 * @since 1.0.0
	 * @return ConstantContact_Lists
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new ConstantContact_Lists();
			self::$instance->hooks();
		}
		return self::$instance;
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'cmb2_init', array( $this, 'sync_lists' ) );
		add_action( 'cmb2_admin_init', array( $this, 'add_lists_metabox' ) );

		add_action( 'save_post_ctct_lists', array( $this, 'save_list' ) );
		add_action( 'save_post_ctct_lists', array( $this, 'update_list' ) );
		add_action( 'wp_trash_post', array( $this, 'delete_list' ) );

	}

	/**
	 * CMB2 metabox for list data
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_lists_metabox() {

		$cmb = new_cmb2_box( array(
			'id' => 'ctct_list_metabox',
			'title' => __( 'List Meta', 'constantcontact' ),
			'object_types'  => array( 'ctct_lists' ),
			'context'	   => 'normal',
			'priority' => 'high',
			'show_names'	=> true,
		) );

		$post_meta = get_post_meta( $cmb->object_id(), '_ctct_list_id', true );

		if ( $post_meta ) {
			$cmb->add_field( array(
				'name' 	=> __( 'ID', 'constantcontact' ),
				'desc' 	=> esc_attr( $post_meta ),
				'id'   	=> '_ctct_list_meta',
				'type'	=> 'title',
			) );
		}
	}

	/**
	 * Syncs list cpt with lists on CTCT
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function sync_lists() {

		// Because we want the lists to stay in sync, but we don't want to do too many
		// requests, we save an option and check against it to make sure we don't sync more than
		// every minute or so.
		// Grab our last synced option. We're setting the default to be 24 hours ago,
		// so that if this is not set, we'll always go through with our process.
		// This is also filterable, if you want to force never syncing or always syncing
		// To force always syncing, filter the 'constant_contact_lists_last_synced' option to be a
		// timestamp 24 hours old. To force never syncing, filter it to be a time in the future
		//
		// Currently, the rate limit for this is a refresh every 2 minutes. This can be filtered to be
		// less or more time.
		$last_synced = get_option( 'constant_contact_lists_last_synced', time() - DAY_IN_SECONDS );
		$sync_rate_limit_time = apply_filters( 'constant_contact_list_sync_rate_limit', 2 * MINUTE_IN_SECONDS );

		// If our last synced time plus our rate limit is less than or equal to right now,
		// then we don't want to refresh. If we refreshed less than 2 minutes ago, we do not want to
		// redo it.
		if ( ( $last_synced + $sync_rate_limit_time ) >= time() ) {
			return;
		}

		// Make sure we're on the correct page
		global $pagenow;
		if ( ! in_array( $pagenow, array( 'edit.php' ), true ) ) {
			return;
		}

		// If we can't edit and delete posts, get out of here
		if ( ! current_user_can( 'edit_posts' ) || ! current_user_can( 'delete_posts' )  ) {
			return;
		}

		// If we don't have an api token, leave
		if ( ! constantcontact_api()->get_api_token() ) {
			return;
		}

		// Make sure we're on the edit page
		if ( ! isset( $_GET['post_type'] ) ) {
			return;
		}

		// Make sure we're on our cpt page
		if ( 'ctct_lists' != esc_attr( $_GET['post_type'] ) ) {
			return;
		}


		// Grab all our lists that we have
		$query = new WP_Query( apply_filters( 'constant_contact_lists_query_for_sync', array(
			'post_type'	             => 'ctct_lists',
			'posts_per_page'         => 50,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
		) ) );

		// Grab our posts
		$lists_to_remove = $query->get_posts();

		// Make sure we didn't get an error and that it is an array
		if ( is_wp_error( $lists_to_remove ) || ! is_array( $lists_to_remove ) ) {
			return;
		}

		// Make our lists to delete array
		$lists_to_delete = array();

		// Loop through each of our lists
		foreach ( $lists_to_remove as $post ) {

			// make sure we have a post ied
			if ( isset( $post->ID ) ) {

				// grab our list id
				$list_id = get_post_meta( $post->ID, '_ctct_list_id', true );

				if ( ! $list_id ) {
					// If we didn't get a list id, we'll want to generate a random string
					// so that we'll still delete it, rather than automatically giving it a
					// numerical value in our array
					$list_id = 'delete_' . wp_generate_password( 20, false );
				}

				// set the key of our array to the list id, value to our post ID
				$lists_to_delete[ esc_attr( $list_id ) ] = absint( $post->ID );
			}
		}

		// Grab the lists we'll want to update/insert
		$lists_to_insert = constantcontact_api()->get_lists();

		// verify our data before continuing
		if ( $lists_to_insert && is_array( $lists_to_insert ) ) {

			// Loop through our lists to insert
			foreach ( $lists_to_insert as $list ) {

				// If we dont' have an idea, bail out of this one
				if ( ! isset( $list->id ) ) {
					continue;
				}

				// sanitize our data
				$list_id = esc_attr( $list->id );

				// Build up our insertion args
				$new_post = apply_filters( 'constant_contact_list_insert_args', array(
					  'post_title'	=> isset( $list->name ) ? esc_attr( $list->name ) : '',
					  'post_status' => 'publish',
					  'post_type'   => 'ctct_lists',
				) );

				// By default, we'll attempt to update post meta for everything
				$update_meta = true;

				// if our list that we want to insert is in our delete array,
				// just update it, instead of deleting and re-adding
				if ( isset( $lists_to_delete[ $list_id ] ) ) {

					// tack on the id to our new post array, so that we
					// will force an update, rather that re-inserting
					$new_post['ID'] = $lists_to_delete[ $list_id ];

					// Remove it from our list of lists to delete
					unset( $lists_to_delete[ $list_id ] );

					// If we already have it, no need to update our meta,
					// might as well save a query
					$update_meta = false;
				}

				// Insert or update our list
				$post = wp_insert_post( $new_post );

				// If we added / inserted it correctly, and we need to update our meta
				if ( ! is_wp_error( $post ) && $post && $update_meta ) {

					// Update with our list id
					update_post_meta( $post, '_ctct_list_id', $list_id );
				}
			}
		}

		// Loop through each of the lists we didn't touch with the update/insert
		// and double check them, then delete
		foreach ( $lists_to_delete as $post_id ) {

			// force our post ID to be an int
			$post_id = absint( $post_id );

			// If we have an ID and the post type is a list
			if ( $post_id && ( 'ctct_lists' == get_post_type( $post_id ) ) ) {

				// remove that post
				wp_delete_post( $post_id, true );
			}
		}

		// Update our last synced option to prevent doing this too often
		update_option( 'constant_contact_lists_last_synced', time() );

		/**
		 * Hook when a ctct list is updated.
		 *
		 * @since 1.0.0
		 * @param array $list ctct returned list data
		 */
		do_action( 'ctct_sync_lists', $lists_to_insert );
	}

	/**
	 * Saves list cpt and sends add list request to CTCT
	 *
	 * @since 1.0.0
	 * @param  integer $post_id current post id.
	 * @return void
	 */
	public function save_list( $post_id ) {
		global $pagenow;

		if ( 'post-new.php' === $pagenow ) {

			$ctct_list = get_post( $post_id );

			if ( isset( $ctct_list ) && $ctct_list->post_modified_gmt === $ctct_list->post_date_gmt ) {

				$list = constantcontact_api()->add_list(
					array(
						'id' => $ctct_list->ID,
						'name' => $ctct_list->post_title,
					)
				);

				add_post_meta( $post_id, '_ctct_list_id', $list->id );

				/**
				 * Hook when a ctct list is saved.
				 *
				 * @since 1.0.0
				 * @param integer $post_id cpt post id
				 * @param integer $list_id ctct list id
				 * @param array $list ctct returned list data
				 */
				do_action( 'ctct_update_list', $post_id, $list_id, $list );

			}
		}
	}

	/**
	 * Update list data cpt and send update request to CTCT
	 *
	 * @since 1.0.0
	 * @param  integer $post_id current post id.
	 * @return void
	 */
	public function update_list( $post_id ) {

		global $pagenow;

		if ( 'post.php' === $pagenow ) {

			$ctct_list = get_post( $post_id );
			$list_id = get_post_meta( $ctct_list->ID, '_ctct_list_id', true );

			if ( $list_id ) {

				$list = constantcontact_api()->update_list(
					array(
						'id' => $list_id,
						'name' => $ctct_list->post_title,
					)
				);

				/**
				 * Hook when a ctct list is updated.
				 *
				 * @since 1.0.0
				 * @param integer $post_id cpt post id
				 * @param integer $list_id ctct list id
				 * @param array $list ctct returned list data
				 */
				do_action( 'ctct_update_list', $post_id, $list_id, $list );

			}
		}
	}

	/**
	 * Delete list from CTCT and database.
	 *
	 * @param  integer $post_id list id.
	 * @return boolean
	 */
	public function delete_list( $post_id ) {

		$list_id = get_post_meta( $post_id, '_ctct_list_id', true );

		if ( $list_id ) {

			$list = constantcontact_api()->delete_list(
				array(
					'id' => $list_id,
				)
			);

			wp_delete_post( $post_id, true );

			/**
			 * Hook when a ctct list is deleted.
			 *
			 * @since 1.0.0
			 * @param integer $post_id
			 * @param integer $list_id
			 */
			do_action( 'ctct_delete_list', $post_id, $list_id );

		}
		return false;
	}

	/**
	 * Returns array of the list data from CTCT
	 *
	 * @since 1.0.0
	 * @return array contact list data from CTCT
	 */
	public function get_lists() {

		$get_lists = array();

		// grab our lists
		$lists = constantcontact_api()->get_lists();

		// make sure we have an array to loop through
		if ( $lists && is_array( $lists ) ) {

			foreach ( $lists as $list ) {

				// make sure we have the right data before setting it to our array
				if ( isset( $list->id ) && isset( $list->name ) ) {
					$get_lists[ esc_attr( $list->id ) ] = esc_attr( $list->name );
				}
			}
		}

		return $get_lists;
	}
}

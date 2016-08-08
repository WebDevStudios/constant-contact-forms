<?php
/**
 * ConstantContact_Lists class
 *
 * @package ConstantContact_Lists
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
	 * @since  1.0.0
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

		// Hook in our CMB2 fields / functionality
		add_action( 'cmb2_admin_init', array( $this, 'sync_lists' ) );
		add_action( 'cmb2_admin_init', array( $this, 'add_lists_metabox' ) );

		// On save, process a list
		add_action( 'save_post_ctct_lists', array( $this, 'save_or_update_list' ), 10, 1 );
		add_action( 'transition_post_status', array( $this, 'post_status_transition' ), 11, 3 );

		// Show duplicate notices for lists
		add_action( 'admin_notices', array( $this, 'show_duplicate_list_message' ) );

		// On deletion, verify the list is handled correctly
		add_action( 'wp_trash_post', array( $this, 'delete_list' ) );

		// Add some CMB2 goodness
		add_action( 'cmb2_after_post_form_ctct_list_metabox', array( $this, 'add_form_css' ) );
		add_action( 'cmb2_render_constant_contact_list_information', array( $this, 'list_info_metabox' ), 10, 5 );

		// Add a force sync button to the admin
		add_filter( 'views_edit-ctct_lists', array( $this, 'add_force_sync_button' ) );
		add_action( 'admin_init', array( $this, 'check_for_list_sync_request' ) );

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
			'title' => __( 'List Information', 'constantcontact' ),
			'object_types'  => array( 'ctct_lists' ),
			'context'	   => 'normal',
			'priority' => 'high',
			'show_names'	=> true,
		) );

		$cmb->add_field( array(
			'name' 	=> '',
			'desc' 	=> '',
			'id'   	=> '_ctct_list_meta',
			'type'	=> 'constant_contact_list_information',
		) );
	}

	/**
	 * Display our list information metabox
	 */
	public function list_info_metabox( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {

		// Sanity check
		if ( ! $object_id ) {
			echo $this->get_list_info_no_data();
			return;
		}

		$list_id = get_post_meta( absint( $object_id ), '_ctct_list_id', true );

		if ( ! $list_id ) {
			echo $this->get_list_info_no_data();
			return;
		}

		// Get our list
		$list_info = constant_contact()->api->get_list( esc_attr( $list_id ) );

		// Make sure we have an actual list
		if ( ! isset( $list_info->id ) ) {
			echo $this->get_list_info_no_data();
			return;
		}

		// Cast our list to an array, so we can easily display it
		$list_info = (array) $list_info;

		echo '<ul>';

		// Unset non-cust facing details
		unset( $list_info['id'] );
		unset( $list_info['status'] );

		// Convert our time/dates to a better format
		if ( isset( $list_info['created_date'] ) && $list_info['created_date'] ) {
			$list_info['created_date'] = '&nbsp;&nbsp;' . date( 'l, F jS, Y g:i A', strtotime( $list_info['created_date'] ) );
		}

		// Convert our time/dates to a better format
		if ( isset( $list_info['modified_date'] ) && $list_info['modified_date'] ) {
			$list_info['modified_date'] = date( 'l, F jS, Y g:i A', strtotime( $list_info['modified_date'] ) );
		}

		// Loop through each property of the list object
		foreach ( $list_info as $key => $value ) {

			// Clean up our property name
			$key = sanitize_text_field( $key );
			$key = str_replace( '_', ' ', $key );
			$key = ucwords( $key );

			echo '<li>' . $key . ': ' . sanitize_text_field( $value ) . '</li>';
		}

		echo '</ul>';
	}

	/**
	 * Gets our no list info data
	 *
	 * @return string
	 */
	public function get_list_info_no_data() {
		return '<em>' . __( 'List information will populate upon saving.', 'constantcontact' ) . '</em>';
	}

	/**
	 * Style our form stuff
	 *
	 * @since  1.0.0
	 */
	public function add_form_css() {
		wp_enqueue_style( 'constant-contact-forms' );
	}

	/**
	 * Syncs list cpt with lists on CTCT
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function sync_lists( $force = false ) {

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
		$sync_rate_limit_time = apply_filters( 'constant_contact_list_sync_rate_limit', 15 * MINUTE_IN_SECONDS );

		// If our last synced time plus our rate limit is less than or equal to right now,
		// then we don't want to refresh. If we refreshed less than 15 minutes ago, we do not want to
		// redo it. Also allow forcing a bypass of this check
		if ( ( ! $force ) && ( $last_synced + $sync_rate_limit_time ) >= time() ) {
			return;
		}

		// Make sure we're on the correct page
		global $pagenow;
		if ( ! in_array( $pagenow, array( 'edit.php' ), true ) ) {
			return;
		}

		// If we can't edit and delete posts, get out of here
		if ( ! current_user_can( 'edit_posts' ) || ! current_user_can( 'delete_posts' ) ) {
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
		if ( 'ctct_lists' !== esc_attr( $_GET['post_type'] ) ) {
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
		$lists_to_insert = constantcontact_api()->get_lists( true );

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

			// If we have an ID and the post type is a list, and is published
			if (
				$post_id &&
				( 'ctct_lists' === get_post_type( $post_id ) ) &&
				( 'publish' == get_post_status( $post_id ) )
			) {

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
	 * Wrapper function to handle saving and updating our lists
	 *
	 * @since  1.0.0
	 * @param  int $post_id wp post id
	 * @return bool          whether or not it worked
	 */
	public function save_or_update_list( $post_id ) {

		// Make sure we're on the new post page
		global $pagenow;

		if ( ! in_array( $pagenow, array( 'post.php', 'post-new.php' ), true ) ) {
			return false;
		}
		// If we can't edit posts, get out of here
		if ( ! current_user_can( 'edit_posts' ) ) {
			return false;
		}

		// Grab the post we're using
		$ctct_list = get_post( $post_id );

		// If we didn't get data, bail
		if ( ! $ctct_list ) {
			return false;
		}

		// If we don't have a post status, bail
		if ( ! isset( $ctct_list->post_status ) ) {
			return false;
		}

		// If we're an autodraft, bail out
		if ( 'auto-draft' === $ctct_list->post_status ) {
			return false;
		}

		// if we didn't get an ID, return
		if ( ! isset( $ctct_list->ID ) ) {
			return;
		}

		// Verify we don't mark things as duplicate if they aren't
		delete_post_meta( $ctct_list->ID, 'ctct_duplicate_list' );

		// When we're adding a list, make sure we don't have one of the same name
		if ( $this->check_if_list_exists_by_title( $ctct_list->post_title ) ) {

			// If it does exist, flag it in our post meta
			add_post_meta( $ctct_list->ID, 'ctct_duplicate_list', true );

			if ( 'draft' != $ctct_list->post_status ) {
				$return = wp_update_post( array(
					'ID'          => absint( $ctct_list->ID ),
					'post_status' => 'draft',
				) );
			}
		} else {
			// Try to grab our list id from our post meta
			$list_id = get_post_meta( $ctct_list->ID, '_ctct_list_id', true );

			// If we got a list id, let's update that list, other wise add it
			if ( ! empty( $list_id ) ) {
				$return = $this->_update_list( $ctct_list, $list_id );
			} else {
				$return = $this->_add_list( $ctct_list );
			}
		}

		// Force re-syncing our lists right after deletion
		$this->sync_lists( true );

		// Set our last synced time to now, so we don't re-add our new/removed list right away
		update_option( 'constant_contact_lists_last_synced', time() );

		return $return;

	}

	/**
	 * Saves list cpt and sends add list request to CTCT
	 *
	 * @since 1.0.0
	 * @param  object $ctct_list WP Post object
	 * @return void
	 */
	public function _add_list( $ctct_list ) {

		// Make sure we have all the data we want to use
		if (
			isset( $ctct_list ) &&
			isset( $ctct_list->ID ) &&
			isset( $ctct_list->post_title )
		) {

			// Make sure we get a unique list name for our list
			$name = $this->set_unique_list_name( $ctct_list->ID, $ctct_list->post_title );

			// Push our list into the API. For the list ID, we append a string of random numbers
			// to make sure its unique.
			$list = constantcontact_api()->add_list(
				array(
					'id' => absint( $ctct_list->ID ) . wp_rand( 0, 1000 ),
					'name' => esc_attr( $name ),
				)
			);

			// If we got a list ID back, make sure we add that to post meta
			if ( isset( $list->id ) && $list->id ) {
				add_post_meta( $ctct_list->ID, '_ctct_list_id', esc_attr( $list->id ) );
			}

			/**
			 * Hook when a ctct list is saved.
			 *
			 * @since 1.0.0
			 * @param integer $post_id cpt post id
			 * @param integer $list_id ctct list id
			 * @param array $list ctct returned list data
			 */
			do_action( 'ctct_update_list', $ctct_list->ID, $list->id, $list );

			// check to make sure our api request was good
			if ( is_object( $list ) && isset( $list->id ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Set a unique list name for a post based on id / title
	 *
	 * @since   1.0.0
	 * @param   int  $id     post ID
	 * @param   string  $title  post title
	 */
	public function set_unique_list_name( $id, $title = '' ) {

		// Keep track of our original title, so we can compare later
		$original_title = $title;

		// Grab all our lists
		$lists = $this->get_lists( true );

		// Start by assuming our list title will exist, so we can kick off the loop
		$list_title_exists = true;

		// Set our unique increment to start with
		$increment = 0;

		// Keep checking + modifying title until we find a list title that is unique
		while ( $list_title_exists ) {

			// CC doesn't allow duplicate list titles, so we want to rename one of them
			$list_title_exists = $this->check_if_list_exists_by_title( $title, $lists );

			// If we did get a unique name, then break out
			if ( ! $list_title_exists ) {
				break;
			}

			// If our string contains what we tacked on previously,
			// just remove it
			if ( strpos( $title, ' (' . $increment . ')' ) !== false ) {
			    $title = str_replace( ' (' . $increment . ')', '', $title );
			}

			// Increase our increment count, and add it into the title
			$increment = $increment + 1;
			$title = $title . ' (' . $increment . ')';
		}

		// If we did modify our list title, update the WP side of things
		if ( $title != $original_title ) {

			// Update our list post type to make sure we have
			// the new title, so that it matches
			wp_update_post( array(
				'ID'         => absint( $id ),
				'post_title' => $title,
			) );
		}

		// Send back our title
		return $title;

	}

	/**
	 * CC doesn't allow duplicate lists by title, so we want to fix a 2nd list
	 * that gets attempted to created
	 *
	 * @since   1.0.0
	 * @param   string  $title  title of list
	 * @param   array   $lists lists to search in
	 * @return  bool          if exists
	 */
	public function check_if_list_exists_by_title( $title, $lists = array() ) {

		// If we didn't get passed in a list array, then grab them all
		if ( empty( $lists ) ) {
			$lists = $this->get_lists();
		}

		// Loop through each of our lists
		foreach ( $lists as $list ) {

			// If we come across one that matches, then return true,
			// as a list with that title exists
			if ( $title == $list ) {
				return true;
			}
		}

		// If we made it through, then return false
		return false;
	}

	/**
	 * Hooked into transition_post_status, we want to verify our deletion of a
	 * list when we remove it, as well as re-adding any restored lists.
	 *
	 * @since   1.0.0
	 * @param   string  $new_status  transitioned to status
	 * @param   string  $old_status  transitioned from status
	 * @param   objewct  $post        post object
	 * @return  void
	 */
	public function post_status_transition( $new_status, $old_status, $post ) {

		// Make sure we have a post passed in
		if ( ! $post ) {
			return;
		}

		// If we don't have a post type, bail.
		if ( ! isset( $post->post_type ) ) {
			return;
		}

		// If we don't have an ID, bail
		if ( ! $post->ID ) {
			return;
		}

		// If we're not on the list post type
		if ( 'ctct_lists' != $post->post_type ) {
			return;
		}

		// Only fire if we got a change in status
		if ( $new_status == $old_status ) {
			return;
		}

		// If we're moving something out of the trash, re-run our add list functionality.
		if ( 'trash' == $old_status ) {
			return $this->_add_list( $post );
		}

	}

	/**
	 * Update list data cpt and send update request to CTCT
	 *
	 * @since 1.0.0
	 * @param  integer $post_id current post id.
	 * @return void
	 */
	public function _update_list( $ctct_list, $list_id ) {

		// Make sure we have all the data we want to use
		if (
			isset( $ctct_list ) &&
			isset( $ctct_list->ID ) &&
			isset( $ctct_list->post_title ) &&
			! empty( $list_id )
		) {
			// Update the list via the API
			$list = constantcontact_api()->update_list(
				array(
					'id' => esc_attr( $list_id ),
					'name' => esc_attr( $ctct_list->post_title ),
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
			do_action( 'ctct_update_list', $ctct_list->ID, $list_id, $list );

			// check to make sure our api request was good
			if ( is_object( $list ) && isset( $list->id ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Delete list from CTCT and database.
	 *
	 * @since  1.0.0
	 * @param  integer $post_id list id.
	 * @return boolean
	 */
	public function delete_list( $post_id ) {

		// type cast our post id
		$post_id = absint( $post_id );

		// bail out if we can't get it
		if ( ! $post_id ) {
			return false;
		}

		// try to grab our list id
		$list_id = get_post_meta( $post_id, '_ctct_list_id', true );

		// Make sure we got a list ID to delete with
		if ( ! $list_id ) {
			return false;
		}

		// delete via the api
		$list = constantcontact_api()->delete_list(
			array(
				'id' => esc_attr( $list_id ),
			)
		);

		/**
		 * Hook when a ctct list is deleted.
		 *
		 * @since 1.0.0
		 * @param integer $post_id
		 * @param integer $list_id
		 */
		do_action( 'ctct_delete_list', $post_id, $list_id );

		return $list;
	}

	/**
	 * Returns array of the list data from CTCT
	 *
	 * @since 1.0.0
	 * @return array contact list data from CTCT
	 */
	public function get_lists( $skip_cache = false ) {

		$get_lists = array();

		// Grab our lists, potentiall forcing a bypass of the cache
		$lists = constantcontact_api()->get_lists( $skip_cache );

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

	/**
	 * Hooked into admin_notices, show our duplicate list message if we have one
	 *
	 */
	public function show_duplicate_list_message() {

		// Make sure we're on the correct page
		global $pagenow, $post;
		if ( ! in_array( $pagenow, array( 'post.php' ), true ) ) {
			return;
		}

		// Make sure we have all the data we need
		if (
			isset( $post->ID ) &&
			$post->ID &&
			isset( $post->post_type ) &&
			$post->post_type &&
			'ctct_lists' == $post->post_type &&
			get_post_meta( $post->ID, 'ctct_duplicate_list', true )
		) {

			// Add our output ( I know, gross inline CSS )
			?>
			<div class="notice notice-error">
				<p><?php esc_attr_e( 'You already have a list with that name.', 'constantcontact' ); ?></p>
			</div>
			<style>
			#title {
				background: url( "<?php echo $this->plugin->url; ?> 'assets/images/error.svg" ) no-repeat;
				background-color: fade-out( #FF4136, 0.98);
				background-position: 8px 50%;
				background-size: 24px;
				border-color: #FF4136;
				padding-left: 40px;
			}
			</style>
			<?php
		}
	}

	/**
	 * Adds a 'Sync Lists with Constant Contact' button to the lists CPT page
	 *
	 * @since   1.0.0
	 * @param   array  $views  current views
	 */
	public function add_force_sync_button( $views ) {

		// Build up our nonced url
		$link = wp_nonce_url( add_query_arg(), 'ctct_reysncing', 'ctct_resyncing' );

		// Add a view to our list
		$views['sync'] = '<strong><a href="' . $link . '">' . __( 'Sync Lists with Constant Contact', 'constantcontact' ) . '</a></strong>';

		// Send em back
		return $views;
	}

	/**
	 * Watch for our request to re-sync lists, and do it
	 *
	 * @since   1.0.0
	 */
	public function check_for_list_sync_request() {

		// Only run if we have our request, and we are capable of it
		if (
			isset( $_GET['ctct_resyncing'] ) &&
			$_GET['ctct_resyncing'] &&
			is_admin() &&
			current_user_can( 'manage_options' )
		) {
			// Force our last updated time to be in the past, so we trigger the auto-refresh
			update_option( 'constant_contact_lists_last_synced', time() - HOUR_IN_SECONDS );
		}
	}
}

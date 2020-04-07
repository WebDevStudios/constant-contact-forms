<?php
/**
 * Lists.
 *
 * @package ConstantContact
 * @subpackage Lists
 * @author Constant Contact
 * @since 1.0.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

use Ctct\ConstantContact;
use Ctct\Exceptions\CtctException;

/**
 * Powers Lists functionality, creation, deletion, syncing, and more.
 *
 * @since 1.0.0
 */
class ConstantContact_Lists {

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
	 *
	 * @param object $plugin Plugin base class.
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

		add_action( 'cmb2_admin_init', [ $this, 'sync_lists' ] );
		add_action( 'cmb2_admin_init', [ $this, 'add_lists_metabox' ] );

		add_action( 'save_post_ctct_lists', [ $this, 'save_or_update_list' ], 10, 1 );
		add_action( 'transition_post_status', [ $this, 'post_status_transition' ], 11, 3 );

		add_action( 'wp_trash_post', [ $this, 'delete_list' ] );

		add_action( 'cmb2_after_post_form_ctct_list_metabox', [ $this, 'add_form_css' ] );
		add_action( 'cmb2_render_constant_contact_list_information', [ $this, 'list_info_metabox' ], 10, 5 );

		add_filter( 'views_edit-ctct_lists', [ $this, 'add_force_sync_button' ] );
		add_action( 'admin_init', [ $this, 'check_for_list_sync_request' ] );

		add_filter( 'post_row_actions', [ $this, 'remove_quick_edit_from_lists' ] );

		add_action( 'admin_init', [ $this, 'maybe_display_duplicate_list_error' ] );
	}

	/**
	 * CMB2 metabox for list data.
	 *
	 * @since 1.0.0
	 */
	public function add_lists_metabox() {

		$cmb = new_cmb2_box( [
			'id'           => 'ctct_list_metabox',
			'title'        => __( 'List Information', 'constant-contact-forms' ),
			'object_types' => [ 'ctct_lists' ],
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
		] );

		$cmb->add_field( [
			'name' => '',
			'desc' => '',
			'id'   => '_ctct_list_meta',
			'type' => 'constant_contact_list_information',
		] );
	}

	/**
	 * Display our list information metabox.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed  $field             Something.
	 * @param string $escaped_value     Something.
	 * @param int    $object_id         Current object ID.
	 * @param string $object_type       Current object type.
	 * @param string $field_type_object Field type object.
	 * @return void
	 */
	public function list_info_metabox( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {

		if ( ! $object_id ) {
			echo wp_kses_post( $this->get_list_info_no_data() );
			return;
		}

		$list_id = get_post_meta( absint( $object_id ), '_ctct_list_id', true );

		if ( ! $list_id ) {
			echo wp_kses_post( $this->get_list_info_no_data() );
			return;
		}

		$list_info = constant_contact()->api->get_list( esc_attr( $list_id ) );

		if ( ! isset( $list_info->id ) ) {
			echo wp_kses_post( $this->get_list_info_no_data() );
			return;
		}

		$list_info = (array) $list_info;

		echo '<ul>';

		unset( $list_info['id'], $list_info['status'] );

		if ( isset( $list_info['created_date'] ) && $list_info['created_date'] ) {
			$list_info['created_date'] = date( 'l, F jS, Y g:i A', strtotime( $list_info['created_date'] ) );
		}

		if ( isset( $list_info['modified_date'] ) && $list_info['modified_date'] ) {
			$list_info['modified_date'] = date( 'l, F jS, Y g:i A', strtotime( $list_info['modified_date'] ) );
		}

		foreach ( $list_info as $key => $value ) {
			$key = sanitize_text_field( $key );
			$key = str_replace( '_', ' ', $key );
			$key = ucwords( $key );

			echo wp_kses_post( '<li>' . $key . ': ' . sanitize_text_field( $value ) . '</li>' );
		}

		echo '</ul>';
	}

	/**
	 * Gets our no list info data.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_list_info_no_data() {
		return '<em>' . __( 'List information will populate upon saving.', 'constant-contact-forms' ) . '</em>';
	}

	/**
	 * Style our form stuff.
	 *
	 * @since 1.0.0
	 */
	public function add_form_css() {
		wp_enqueue_style( 'constant-contact-forms-admin' );
	}

	/**
	 * Syncs list cpt with lists on CTCT.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $force Whether or not to force syncing.
	 * @return void
	 */
	public function sync_lists( $force = false ) {
		global $pagenow;
		if ( ! $pagenow || ( ! in_array( $pagenow, [ 'edit.php' ], true ) ) ) {
			return;
		}

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
		$last_synced = get_option( 'constant_contact_lists_last_synced', current_time( 'timestamp' ) - DAY_IN_SECONDS );

		/**
		 * Filters the rate limit to use for syncing lists.
		 *
		 * @since 1.0.0
		 *
		 * @param int $value Amount of time to wait between syncs. Default 15 minutes.
		 */
		$sync_rate_limit_time = apply_filters( 'constant_contact_list_sync_rate_limit', 15 * MINUTE_IN_SECONDS );

		// If our last synced time plus our rate limit is less than or equal to right now,
		// then we don't want to refresh. If we refreshed less than 15 minutes ago, we do not want to
		// redo it. Also allow forcing a bypass of this check.
		if ( ( ! $force ) && ( $last_synced + $sync_rate_limit_time ) >= current_time( 'timestamp' ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_posts' ) || ! current_user_can( 'delete_posts' ) ) {
			return;
		}

		if ( ! constantcontact_api()->get_api_token() ) {
			return;
		}

		$post_type = filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_STRING );

		if ( ! isset( $post_type ) ) {
			return;
		}

		if ( 'ctct_lists' !== esc_attr( sanitize_text_field( wp_unslash( $post_type ) ) ) ) {
			return;
		}

		/**
		 * Filters the arguments used to grab the lists to sync.
		 *
		 * @since 1.0.0
		 *
		 * @param array $value Array of WP_Query arguments.
		 */
		$query = new WP_Query( apply_filters( 'constant_contact_lists_query_for_sync', [
			'post_type'              => 'ctct_lists',
			'posts_per_page'         => 100,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'fields'                 => 'ids',
		] ) );

		$potentially_remove_list = $query->get_posts();

		if ( is_wp_error( $potentially_remove_list ) || ! is_array( $potentially_remove_list ) ) {
			return;
		}

		$lists_to_delete = [];

		foreach ( $potentially_remove_list as $post_id ) {
			if ( isset( $post_id ) ) {
				$list_id = get_post_meta( $post_id, '_ctct_list_id', true );

				if ( ! $list_id ) {

					// If we didn't get a list id, we'll want to generate a random string
					// so that we'll still delete it, rather than automatically giving it a
					// numerical value in our array.
					$list_id = 'delete_' . wp_generate_password( 20, false );
				}

				$lists_to_delete[ esc_attr( $list_id ) ] = absint( $post_id );
			}
		}

		$lists_to_insert = constantcontact_api()->get_lists( true );

		if ( $lists_to_insert && is_array( $lists_to_insert ) ) {

			if ( count( $lists_to_insert ) >= 150 ) {
				$this->plugin->updates->add_notification( 'too_many_lists' );

				$lists_to_insert = array_chunk( $lists_to_insert, 100 );
				if ( isset( $lists_to_insert[0] ) ) {
					$lists_to_insert = $lists_to_insert[0];
				}
			}

			foreach ( $lists_to_insert as $list ) {

				if ( ! isset( $list->id ) ) {
					continue;
				}

				$list_id = esc_attr( $list->id );

				/**
				 * Filters the arguments used for inserting new lists from a fresh sync.
				 *
				 * @since 1.0.0
				 *
				 * @param array $value Array of arguments for a new list to be inserted into database.
				 */
				$new_post = apply_filters( 'constant_contact_list_insert_args', [
					'post_title'  => isset( $list->name ) ? esc_attr( $list->name ) : '',
					'post_status' => 'publish',
					'post_type'   => 'ctct_lists',
				] );

				// By default, we'll attempt to update post meta for everything.
				$update_meta = true;

				// If our list that we want to insert is in our delete array,
				// just update it, instead of deleting and re-adding.
				if ( isset( $lists_to_delete[ $list_id ] ) ) {

					// Tack on the id to our new post array, so that we
					// will force an update, rather that re-inserting.
					$new_post['ID'] = $lists_to_delete[ $list_id ];

					unset( $lists_to_delete[ $list_id ] );

					// If we already have it, no need to update our meta,
					// might as well save a query.
					$update_meta = false;
				}

				$post = wp_insert_post( $new_post );

				if ( ! is_wp_error( $post ) && $post && $update_meta ) {
					update_post_meta( $post, '_ctct_list_id', $list_id );
				}
			} // End foreach.
		} // End if.

		// Loop through each of the lists we didn't touch with the update/insert
		// and double check them, then delete.
		foreach ( $lists_to_delete as $post_id ) {

			$post_id = absint( $post_id );

			if (
				$post_id &&
				( 'ctct_lists' === get_post_type( $post_id ) ) &&
				( 'publish' === get_post_status( $post_id ) )
			) {
				wp_delete_post( $post_id, true );
			}
		}

		update_option( 'constant_contact_lists_last_synced', current_time( 'timestamp' ) );

		/**
		 * Hook when a ctct list is updated.
		 *
		 * @since 1.0.0
		 *
		 * @param array $lists_to_insert CTCT returned list data.
		 */
		do_action( 'ctct_sync_lists', $lists_to_insert );
	}

	/**
	 * Wrapper function to handle saving and updating our lists.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id wp post id.
	 * @return bool Whether or not it worked.
	 */
	public function save_or_update_list( $post_id ) {

		global $pagenow;

		if ( ! $pagenow || ( ! in_array( $pagenow, [ 'post.php', 'post-new.php' ], true ) ) ) {
			return false;
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			return false;
		}

		$ctct_list = get_post( $post_id );

		if ( ! $ctct_list ) {
			return false;
		}

		if ( ! isset( $ctct_list->post_status ) ) {
			return false;
		}

		if ( 'auto-draft' === $ctct_list->post_status ) {
			return false;
		}

		if ( ! isset( $ctct_list->ID ) ) {
			return false;
		}

		delete_post_meta( $ctct_list->ID, 'ctct_duplicate_list' );

		$return = false;

		if ( $this->check_if_list_exists_by_title( $ctct_list->post_title ) ) {

			add_post_meta( $ctct_list->ID, 'ctct_duplicate_list', true );

			if ( 'draft' !== $ctct_list->post_status ) {
				$return = wp_update_post( [
					'ID'          => absint( $ctct_list->ID ),
					'post_status' => 'draft',
				] );
			}
		} else {
			$list_id = get_post_meta( $ctct_list->ID, '_ctct_list_id', true );

			if ( ! empty( $list_id ) ) {
				$return = $this->update_list( $ctct_list, $list_id );
			} else {
				$return = $this->add_list( $ctct_list );
			}
		}

		delete_transient( 'ctct_lists' );

		$this->sync_lists( true );

		update_option( 'constant_contact_lists_last_synced', current_time( 'timestamp' ) );

		return $return;

	}

	/**
	 * Saves list cpt and sends add list request to CTCT.
	 *
	 * @since 1.0.0
	 *
	 * @param object $ctct_list WP Post object.
	 * @return bool
	 */
	public function add_list( $ctct_list ) {

		if ( ! isset( $ctct_list ) || empty( $ctct_list ) ) {
			return false;
		}

		if ( ! isset( $ctct_list ) || empty( $ctct_list ) ) {
			return false;
		}

		if ( ! isset( $ctct_list->ID ) || 0 >= $ctct_list->ID ) {
			return false;
		}

		if ( ! isset( $ctct_list->post_title ) || empty( $ctct_list->post_title ) ) {
			return false;
		}

		$name = $this->set_unique_list_name( $ctct_list->ID, $ctct_list->post_title );

		// Push our list into the API. For the list ID, we append a string of random numbers
		// to make sure its unique.
		$list = constantcontact_api()->add_list(
			[
				'id'   => absint( $ctct_list->ID ) . wp_rand( 0, 1000 ),
				'name' => esc_attr( $name ),
			]
		);

		$list_id = false;

		if ( isset( $list->id ) && $list->id ) {
			add_post_meta( $ctct_list->ID, '_ctct_list_id', esc_attr( $list->id ) );
			$list_id = $list->id;
		}

		/**
		 * Hook when a ctct list is saved.
		 *
		 * @since 1.0.0
		 *
		 * @param integer $post_id CPT post id.
		 * @param integer $list_id Ctct list id.
		 * @param array   $list    Ctct returned list data.
		 */
		do_action( 'ctct_update_list', $ctct_list->ID, $list_id, $list );

		return is_object( $list ) && isset( $list->id );
	}

	/**
	 * Set a unique list name for a post based on id / title.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $id    Post ID.
	 * @param string $title Post title.
	 * @return string
	 */
	public function set_unique_list_name( $id, $title = '' ) {

		$original_title    = $title;
		$lists             = $this->get_lists( true );
		$list_title_exists = true;
		$increment         = 0;

		// Keep checking + modifying title until we find a list title that is unique.
		while ( $list_title_exists ) {

			// CC doesn't allow duplicate list titles, so we want to rename one of them.
			$list_title_exists = $this->check_if_list_exists_by_title( $title, $lists );

			if ( ! $list_title_exists ) {
				break;
			}

			// If our string contains what we tacked on previously,
			// just remove it.
			if ( strpos( $title, ' (' . $increment . ')' ) !== false ) {
				$title = str_replace( ' (' . $increment . ')', '', $title );
			}

			$increment = $increment++;
			$title     = $title . ' (' . $increment . ')';
		}

		if ( $title !== $original_title ) {
			wp_update_post( [
				'ID'         => absint( $id ),
				'post_title' => $title,
			] );
		}

		return $title;

	}

	/**
	 * CC doesn't allow duplicate lists by title, so we want to fix a 2nd list
	 * that gets attempted to created.
	 *
	 * @since 1.0.0
	 *
	 * @param string $title Title of list.
	 * @param array  $lists Lists to search in.
	 * @return bool If exists.
	 */
	public function check_if_list_exists_by_title( $title, $lists = [] ) {

		if ( empty( $lists ) ) {
			$lists = $this->get_lists();
		}

		foreach ( $lists as $list ) {
			if ( $title === $list ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Hooked into transition_post_status, we want to verify our deletion of a
	 * list when we remove it, as well as re-adding any restored lists.
	 *
	 * @since 1.0.0
	 *
	 * @param string $new_status Transitioned to status.
	 * @param string $old_status Transitioned from status.
	 * @param object $post       Post object.
	 * @return bool
	 */
	public function post_status_transition( $new_status, $old_status, $post ) {

		if ( ! $post ) {
			return false;
		}

		if ( ! isset( $post->post_type ) ) {
			return false;
		}

		if ( ! $post->ID ) {
			return false;
		}

		if ( 'ctct_lists' !== $post->post_type ) {
			return false;
		}

		if ( $new_status === $old_status ) {
			return false;
		}

		if ( 'trash' === $old_status ) {
			return $this->add_list( $post );
		}

		return true;
	}

	/**
	 * Update list data cpt and send update request to CTCT.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $ctct_list List post object.
	 * @param integer $list_id Current list id.
	 * @return bool
	 */
	public function update_list( $ctct_list, $list_id ) {

		if ( ! isset( $ctct_list ) || 0 >= $ctct_list->ID || empty( $list_id ) ) {
			return false;
		}

		if ( ! isset( $ctct_list->post_title ) ) {
			return false;
		}

		$list = constantcontact_api()->update_list(
			[
				'id'   => esc_attr( $list_id ),
				'name' => esc_attr( $ctct_list->post_title ),
			]
		);

		/**
		 * Hook when a ctct list is updated.
		 *
		 * @since 1.0.0
		 * @param integer $post_id CPT post id.
		 * @param integer $list_id Ctct list id.
		 * @param array   $list    Ctct returned list data.
		 */
		do_action( 'ctct_update_list', $ctct_list->ID, $list_id, $list );

		return is_object( $list ) && isset( $list->id );
	}

	/**
	 * Delete list from CTCT and database.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $post_id List id.
	 * @return boolean
	 */
	public function delete_list( $post_id ) {

		$post_id = absint( $post_id );

		if ( ! $post_id ) {
			return false;
		}

		$list_id = get_post_meta( $post_id, '_ctct_list_id', true );

		if ( ! $list_id ) {
			return false;
		}

		$list = constantcontact_api()->delete_list(
			[
				'id' => esc_attr( $list_id ),
			]
		);

		/**
		 * Hook when a ctct list is deleted.
		 *
		 * @since 1.0.0
		 *
		 * @param integer $post_id Form list ID that was deleted.
		 * @param integer $list_id Constant Contact list ID.
		 */
		do_action( 'ctct_delete_list', $post_id, $list_id );

		return $list;
	}

	/**
	 * Returns array of the list data from CTCT
	 *
	 * @since 1.0.0
	 *
	 * @param bool $skip_cache Whether or not to skip cache.
	 * @return array Contact list data from CTCT.
	 */
	public function get_lists( $skip_cache = false ) {

		$get_lists = [];

		$lists = constantcontact_api()->get_lists( $skip_cache );

		if ( $lists && is_array( $lists ) ) {

			foreach ( $lists as $list ) {
				if ( isset( $list->id ) && isset( $list->name ) ) {
					$get_lists[ esc_attr( $list->id ) ] = esc_attr( $list->name );
				}
			}
		}

		return $get_lists;
	}

	/**
	 * Maybe show some information about duplicate list errors.
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	public function maybe_display_duplicate_list_error() {
		global $pagenow, $post;
		if ( $pagenow || ( ! in_array( $pagenow, [ 'post.php' ], true ) ) ) {
			return;
		}

		if (
			isset( $post->ID ) &&
			$post->ID &&
			isset( $post->post_type ) &&
			$post->post_type &&
			'ctct_lists' === $post->post_type &&
			get_post_meta( $post->ID, 'ctct_duplicate_list', true )
		) {
			add_action( 'admin_head', [ $this, 'list_css' ] );
			add_action( 'admin_notices', [ $this, 'show_duplicate_list_message' ] );
		}
	}

	/**
	 * Output some embedded CSS for our error.
	 *
	 * @since 1.4.0
	 */
	public function list_css() {
		?>
		<style>
			.post-type-ctct_lists #titlediv #title {
				background: url( "<?php echo esc_url_raw( $this->plugin->url . 'assets/images/error.svg' ); ?>" ) no-repeat;
				background-color: fade-out( #FF4136, 0.98);
				background-position: 8px 50%;
				background-size: 24px;
				border-color: #FF4136;
				padding-left: 40px;
			}
		</style>
		<?php
	}

	/**
	 * Hooked into admin_notices, show our duplicate list message if we have one.
	 *
	 * @since 1.0.0
	 */
	public function show_duplicate_list_message() {
		?>
		<div class="notice notice-error">
				<p><?php esc_attr_e( 'You already have a list with that name.', 'constant-contact-forms' ); ?></p>
		</div>
	<?php
	}

	/**
	 * Adds a 'Sync Lists with Constant Contact' button to the lists CPT page.
	 *
	 * @since 1.0.0
	 *
	 * @param array $views Current views.
	 * @return array
	 */
	public function add_force_sync_button( $views ) {

		$link = wp_nonce_url( add_query_arg( [ 'ctct_list_sync' => 'true' ] ), 'ctct_reysncing', 'ctct_resyncing' );

		$views['sync'] = '<strong><a href="' . $link . '">' . __( 'Sync Lists with Constant Contact', 'constant-contact-forms' ) . '</a></strong>';

		return $views;
	}

	/**
	 * Watch for our request to re-sync lists, and do it.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function check_for_list_sync_request() {

		$ctct_resyncing = filter_input( INPUT_GET, 'ctct_resyncing', FILTER_SANITIZE_STRING );

		if ( ! isset( $ctct_resyncing ) || ! is_admin() ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( sanitize_text_field( wp_unslash( $ctct_resyncing ) ) ) {
			update_option( 'constant_contact_lists_last_synced', current_time( 'timestamp' ) - HOUR_IN_SECONDS );

			$url = remove_query_arg( [ 'ctct_resyncing', 'ctct_list_sync' ] );

			wp_safe_redirect( $url );
			die;
		}
	}

	/**
	 * Remove quick edit from our lists post type.
	 *
	 * @since 1.0.0
	 *
	 * @param array $actions Current actions.
	 * @return array Modified actions.
	 */
	public function remove_quick_edit_from_lists( $actions ) {
		global $post;

		if ( $post && isset( $post->post_type ) && $post->post_type && 'ctct_lists' === $post->post_type ) {
			unset( $actions['inline hide-if-no-js'] );
		}

		return $actions;
	}
}

<?php

/**
 * Class WDS_CMB2_Attached_Posts_Field
 */
class ConstantContact_Attached_Lists_Field {

	/**
	 * Current version number
	 */
	const VERSION = '1.0.0';

	/**
	 * CMB2_Field object
	 * @var CMB2_Field
	 */
	protected $field;

	/**
	 * Whether to output the type label.
	 * Determined when multiple post types exist in the query_args field arg.
	 * @var bool
	 */
	protected $do_type_label = false;

	/**
	 * Initialize the plugin by hooking into CMB2
	 */
	public function __construct() {
		// Required to create custom field types. In this case: "custom_attached_post"
		add_action( 'cmb2_render_custom_attached_posts', [ $this, 'render' ], 10, 5 );
		add_action( 'cmb2_sanitize_custom_attached_posts', [ $this, 'sanitize' ], 10, 2 );

		add_action( 'cmb2_attached_posts_field_add_find_posts_div', [ $this, 'add_find_posts_div' ] );
		add_action( 'cmb2_after_init', [ $this, 'ajax_find_posts' ] );
	}

	/**
	 * Add a CMB custom field to allow for the selection of multiple posts
	 * attached to a single page
	 */
	public function render( $field, $escaped_value, $object_id, $object_type, $field_type ) {
		self::setup_scripts();
		$this->field         = $field;
		$this->do_type_label = false;

		add_action( 'admin_footer', 'find_posts_div' );

		$query_args  = (array) $this->field->options( 'query_args' );

		// Setup our args
		$args = wp_parse_args( $query_args, [
			'post_type'      => 'post',
			'posts_per_page' => 100,
		] );

		// Most likely prevents listing a self post in the list.
		if ( isset( $_POST['post'] ) ) {
			$args['post__not_in'] = [ absint( $_POST['post'] ) ];
		}

		// loop through post types to get labels for all.
		$post_type_labels = [];
		foreach ( (array) $args['post_type'] as $post_type ) {
			// Get post type object for attached post type
			$post_type_obj = get_post_type_object( $post_type );

			// continue if we don't have a label for the post type
			if ( ! $post_type_obj || ! isset( $post_type_obj->labels->name ) ) {
				continue;
			}

			if ( $post_type_obj->hierarchical ) {
				$args['orderby'] = $args['orderby'] ?? 'name';
				$args['order']   = $args['order'] ?? 'ASC';
			}

			$post_type_labels[] = $post_type_obj->labels->name;
		}

		$this->do_type_label = count( $post_type_labels ) > 1;

		$post_type_labels = implode( '/', $post_type_labels );

		$filter_boxes = '';
		// Check 'filter' setting
		if ( $this->field->options( 'filter_boxes' ) ) {
			$filter_boxes = '<div class="search-wrap"><input type="text" placeholder="' . sprintf( __( 'Filter %s', 'cmb' ), $post_type_labels ) . '" class="regular-text search" name="%s" /></div>';
		}

		// Check to see if we have any meta values saved yet
		$attached = (array) $escaped_value;

		$objects = $this->get_all_objects( $args, $attached );

		// If there are no posts found, just stop
		if ( empty( $objects ) ) {
			return;
		}

		// Wrap our lists
		?>
		<div class="attached-posts-wrap widefat" data-fieldname="<?php echo esc_attr( $field_type->_name() ); ?>">
			<div class="retrieved-wrap column-wrap">
				<p class="attached-posts-section">
					<strong><?php printf( esc_html__( 'Available %s', 'cmb' ), $post_type_labels ); ?></strong>
				</p>

				<?php
				$hide_selected = $this->field->options( 'hide_selected' ) ? ' hide-selected' : '';

				if ( $filter_boxes ) {
					printf( $filter_boxes, 'available-search' );
				}
				?>
				<ul class="retrieved connected <?php echo esc_attr( $hide_selected ); ?>">
					<?php

					// Loop through our posts as list items
					$this->display_retrieved( $objects, $attached );
					?>
				</ul><!-- .retrieved -->
				<?php

				$findtxt = $field_type->_text( 'find_text', __( 'Search lists' ) );

				$js_data = json_encode( [
					'types'      => $args['post_type'],
					'cmbId'      => $this->field->cmb_id,
					'errortxt'   => esc_attr( $field_type->_text( 'error_text', esc_html__( 'An error has occurred. Please reload the page and try again.', 'constant-contact-forms' ) ) ),
					'findtxt'    => esc_attr( $field_type->_text( 'find_text', esc_html__( 'Find lists', 'constant-contact-forms' ) ) ),
					'groupId'    => $this->field->group ? $this->field->group->id() : false,
					'fieldId'    => $this->field->_id(),
					'exclude'    => $args['post__not_in'] ?? [],
				] );
				?>

				<p>
					<button type="button" class="button cmb2-attached-posts-search-button" data-search=" echo esc_attr( $js_data ); ?>">
						<?php echo esc_html( $findtxt ); ?>
						<span title="<?php echo esc_attr( $findtxt ); ?>" class="dashicons dashicons-search"></span>
					</button>
				</p>
			</div><!-- .retrieved-wrap -->
			<div class="attached-wrap column-wrap">
				<p class="attached-posts-section">
					<strong><?php printf( esc_html__( 'Associated %s', 'constant-contact-forms' ), $post_type_labels ); ?></strong>
				</p>
			<?php
			if ( $filter_boxes ) {
				printf( $filter_boxes, 'attached-search' );
			}
			?>
				<ul class="attached connected">
					<?php
					// If we have any ids saved already, display them
					$ids = $this->display_attached( $attached );
					?>
				</ul><!-- #attached -->
			</div><!-- .attached-wrap -->

			<?php

			echo $field_type->input( [
				'type'  => 'hidden',
				'class' => 'attached-posts-ids',
				'value' => ! empty( $ids ) ? implode( ',', $ids ) : '',
				'desc'  => '',
			] );
			?>
		</div><!-- .attached-posts-wrap -->
		<?php
	}

	/**
	 * Outputs the <li>s in the retrieved (left) column.
	 *
	 * @param mixed $objects  Posts or users.
	 * @param array $attached Array of attached posts/users.
	 *
	 * @return void
	 * @since  1.2.5
	 */
	protected function display_retrieved( $objects, $attached ) {
		$count = 0;

		// Loop through our posts as list items
		foreach ( $objects as $object ) {

			// Set our zebra stripes
			$class = ++ $count % 2 == 0 ? 'even' : 'odd';

			// Set a class if our post is in our attached meta
			$class .= ! empty ( $attached ) && in_array( $this->get_id( $object ), $attached ) ? ' added' : '';

			$this->list_item( $object, $class );
		}
	}

	/**
	 * Outputs the <li>s in the attached (right) column.
	 *
	 * @param array $attached Array of attached posts/users.
	 *
	 * @return array
	 * @since  1.2.5
	 */
	protected function display_attached( $attached ) {
		$ids = [];

		// Remove any empty values
		$attached = array_filter( $attached );

		if ( empty( $attached ) ) {
			return $ids;
		}

		$count = 0;

		// Loop through and build our existing display items
		foreach ( $attached as $id ) {
			$object = $this->get_object( $id );
			$id     = $this->get_id( $object );

			if ( empty( $object ) ) {
				continue;
			}

			// Set our zebra stripes
			$class = ++ $count % 2 == 0 ? 'even' : 'odd';

			$this->list_item( $object, $class, 'dashicons-minus', 'right_list' );
			$ids[ $id ] = $id;
		}

		return $ids;
	}

	/**
	 * Outputs a column list item.
	 *
	 * @param mixed  $object     Post or User.
	 * @param string $li_class   The list item (zebra) class.
	 * @param string $icon_class The icon class. Either 'dashicons-plus' or 'dashicons-minus'.
	 *
	 * @return void
	 * @since  1.2.5
	 */
	public function list_item( $object, $li_class, $icon_class = 'dashicons-plus', $list_side = 'left_list' ) {
		// Determines if we should show the sorting icons. Only want on right list.
		$sort_icon_left = 'right_list' === $list_side ? '<span class="dashicons dashicons-sort sort"></span>' : '';
		// Build our list item
		printf(
			'<li data-id="%1$s" class="%2$s" target="_blank">%3$s<a title="' . __( 'Edit' ) . '" href="%4$s">%5$s</a>%6$s<span class="dashicons %7$s add-remove"></span></li>',
			$this->get_list_id_by_object( $object ),
			$li_class,
			$sort_icon_left,
			get_edit_post_link( $object ),
			get_the_title( $object ),
			$this->get_object_label( $object ),
			$icon_class
		);
	}

	/**
	 * Get ID for the object.
	 *
	 * @param mixed $object Post or User
	 *
	 * @return int            The object ID.
	 * @since  1.2.4
	 */
	public function get_id( $object ) {
		return $object->ID;
	}

	public function get_list_id_by_object( $object ) {
		return get_post_meta( $object->ID, '_ctct_list_id', true );
	}

	/**
	 * Get object by id.
	 *
	 * @param int $id Post or User ID.
	 *
	 * @return mixed     Post or User if found.
	 * @since  1.2.4
	 */
	public function get_object( $id ) {
		return get_post( absint( $id ) );
	}

	/**
	 * Fetches the default query for items, and combines with any objects attached.
	 *
	 * @param array $args     Array of query args.
	 * @param array $attached Array of attached object ids.
	 *
	 * @return array            Array of attached object ids.
	 * @since  1.2.4
	 */
	public function get_all_objects( $args, $attached = [] ) {
		$objects = $this->get_objects( $args );

		$attached_objects = [];
		foreach ( $objects as $object ) {
			$attached_objects[ $this->get_id( $object ) ] = $object;
		}

		if ( ! empty( $attached ) ) {
			$is_users                                        = $this->field->options( 'query_users' );
			$args[ $is_users ? 'include' : 'post__in' ]      = $attached;
			$args[ $is_users ? 'number' : 'posts_per_page' ] = count( $attached );

			$new = $this->get_objects( $args );

			foreach ( $new as $object ) {
				if ( ! isset( $attached_objects[ $this->get_id( $object ) ] ) ) {
					$attached_objects[ $this->get_id( $object ) ] = $object;
				}
			}
		}

		return $attached_objects;
	}

	/**
	 * Peforms a get_posts or get_users query.
	 *
	 * @param array $args Array of query args.
	 *
	 * @return array        Array of results.
	 * @since  1.2.4
	 */
	public function get_objects( $args ) {
		return call_user_func( 'get_posts', $args );
	}

	/**
	 * Enqueue admin scripts for our attached posts field
	 */
	protected static function setup_scripts() {
		static $once = false;

		$url = constant_contact::url() . 'assets/attached-lists/';

		$requirements = [
			'jquery-ui-core',
			'jquery-ui-widget',
			'jquery-ui-mouse',
			'jquery-ui-draggable',
			'jquery-ui-droppable',
			'jquery-ui-sortable',
			'wp-backbone',
		];

		wp_enqueue_script( 'cmb2-attached-posts-field', $url . 'js/attached-posts.js', $requirements, self::VERSION, true );
		wp_enqueue_style( 'cmb2-attached-posts-field', $url . 'css/attached-posts-admin.css', [], self::VERSION );

		if ( ! $once ) {
			wp_localize_script( 'cmb2-attached-posts-field', 'CMBAP', [
				'edit_link_template' => str_replace( get_the_ID(), 'REPLACEME', get_edit_post_link( get_the_ID() ) ),
				'ajaxurl'            => admin_url( 'admin-ajax.php', 'relative' ),
			] );

			$once = true;
		}
	}

	/**
	 * Add the find posts div via a hook so we can relocate it manually
	 */
	public function add_find_posts_div() {
		// `find_posts_div` -> Outputs the modal window used for attaching media to posts or pages in the media-listing screen.
		add_action( 'wp_footer', 'find_posts_div' );
	}

	/**
	 * Sanitizes/formats the attached-posts field value.
	 *
	 * @param string $sanitized_val The sanitized value to be saved.
	 * @param string $val           The unsanitized value.
	 *
	 * @return string                 The (maybe-modified) sanitized value to be saved.
	 * @since  1.2.4
	 */
	public function sanitize( $sanitized_val, $val ) {
		if ( ! empty( $val ) ) {
			$sanitized_val = explode( ',', $val );
		}

		return $sanitized_val;
	}

	/**
	 * Check to see if we have a post type set and, if so, add the
	 * pre_get_posts action to set the queried post type
	 * @return void
	 * @since  1.2.4
	 */
	public function ajax_find_posts() {
		if (
			defined( 'DOING_AJAX' )
			&& DOING_AJAX
			&& isset( $_POST['cmb2_attached_search'], $_POST['retrieved'], $_POST['action'], $_POST['search_types'] )
			&& 'find_posts' == $_POST['action']
			&& ! empty( $_POST['search_types'] )
		) {
			add_action( 'pre_get_posts', [ $this, 'modify_query' ] );
		}
	}

	/**
	 * Modify the search query.
	 *
	 * @param WP_Query $query WP_Query instance during the pre_get_posts hook.
	 *
	 * @return void
	 * @since  1.2.4
	 */
	public function modify_query( $query ) {
		$types = $_POST['search_types'];
		$types = is_array( $types ) ? array_map( 'esc_attr', $types ) : esc_attr( $types );
		$query->set( 'post_type', $types );

		if ( ! empty( $_POST['retrieved'] ) && is_array( $_POST['retrieved'] ) ) {
			// Exclude posts/users already existing.
			$ids = array_map( 'absint', $_POST['retrieved'] );

			if ( ! empty( $_POST['exclude'] ) && is_array( $_POST['exclude'] ) ) {
				// Exclude the post that we're looking at.
				$exclude = array_map( 'absint', $_POST['exclude'] );
				$ids     = array_merge( $ids, $exclude );
			}

			$query->set( 'post__not_in', $ids );
		}

		$this->maybe_callback( $query, $_POST );
	}

	/**
	 * If field has a 'attached_posts_search_query_cb', run the callback.
	 *
	 * @param WP_Query $query     WP_Query instance during the pre_get_posts hook.
	 * @param array    $post_args The $_POST array.
	 *
	 * @return void
	 * @since  1.2.4
	 */
	public function maybe_callback( $query, $post_args ) {
		$cmb   = $post_args['cmb_id'] ?? '';
		$group = $post_args['group_id'] ?? '';
		$field = $post_args['field_id'] ?? '';

		$cmb = cmb2_get_metabox( $cmb );
		if ( $cmb && $group ) {
			$group = $cmb->get_field( $group );
		}

		if ( $cmb && $field ) {
			$group = $group ? $group : null;
			$field = $cmb->get_field( $field, $group );
		}

		if ( $field && ( $cb = $field->maybe_callback( 'attached_posts_search_query_cb' ) ) ) {
			call_user_func( $cb, $query, $field, $this );
		}
	}
}

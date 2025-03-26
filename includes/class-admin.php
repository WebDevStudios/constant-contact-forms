<?php
/**
 * Admin
 *
 * @package    ConstantContact
 * @subpackage Admin
 * @author     Constant Contact
 * @since      1.0.1
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Powers admin options pages, customized display for plugin listing, and admin scripts.
 *
 * @since 1.0.1
 */
class ConstantContact_Admin {

	/**
	 * Option key, and option page slug.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private string $key = 'ctct_options';

	/**
	 * Options page metabox id.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private string $metabox_id = 'ctct_option_metabox';

	/**
	 * Options Page title.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected string $title = '';

	/**
	 * Options Page hook.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected string $options_page = '';

	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	protected $plugin;

	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	protected $basename;

	/**
	 * The parent menu page slug.
	 *
	 * @since 1.0.1
	 * @var string
	 */
	protected string $parent_menu_slug = 'edit.php?post_type=ctct_forms';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Constant_Contact $plugin Primary class file.
	 * @param string           $basename Primary class basename.
	 */
	public function __construct( $plugin, $basename ) {
		$this->plugin   = $plugin;
		$this->basename = $basename;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		add_action( 'current_screen', [ $this, 'current_screen' ] );
		add_action( 'admin_init', [ $this, 'init' ] );
		add_action( 'admin_menu', [ $this, 'add_options_page' ], 999 );

		add_filter( 'manage_ctct_forms_posts_columns', [ $this, 'set_custom_columns' ] );
		add_action( 'manage_ctct_forms_posts_custom_column', [ $this, 'custom_columns' ], 10, 2 );

		add_filter( 'manage_ctct_lists_posts_columns', [ $this, 'set_custom_lists_columns' ] );
		add_action( 'manage_ctct_lists_posts_custom_column', [ $this, 'custom_lists_columns' ], 10, 2 );

		add_filter( 'plugin_action_links_' . $this->basename, [ $this, 'add_social_links' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'scripts' ] );
	}

	/**
	 * Adds functionality to Constant Contact admin screen.
	 *
	 * @param array $screen Details on the current admin screen.
	 * @return void
	 * @since 1.11.0
	 * @author Darren Cooney <darren.cooney@webdevstudios.com>
	 */
	public function current_screen( $screen ) {
		if ( constant_contact()->is_constant_contact() ) {
			add_action( 'in_admin_header', [ $this, 'admin_page_toolbar' ] );
		}
	}


	/**
	 * Build the Constant Contact header toolbar.
	 *
	 * @return void
	 * @since 1.11.0
	 * @author Darren Cooney <darren.cooney@webdevstudios.com>
	 */
	public function admin_page_toolbar() {

		global $submenu, $submenu_file, $plugin_page, $pagenow;

		$cpt_slug    = 'ctct_forms';
		$parent_slug = "edit.php?post_type=$cpt_slug";

		$tabs = [];

		if ( isset( $submenu[ $parent_slug ] ) ) {
			foreach ( $submenu[ $parent_slug ] as $i => $sub_item ) {

				// Check user can access page.
				if ( ! current_user_can( $sub_item[1] ) ) {
					continue;
				}

				// Ignore "Add New".
				if ( 10 === $i ) {
					continue;
				}

				// Define tab.
				$tab = [
					'text' => $sub_item[0],
					'url'  => $sub_item[2],
				];

				// Convert submenu slug to "$parent_slug&page=test".
				if ( ! strpos( $sub_item[2], '.php' ) ) {
					$tab['url'] = add_query_arg( [ 'page' => $sub_item[2] ], $parent_slug );
				}

				// Detect active state.
				if ( $submenu_file === $sub_item[2] || $plugin_page === $sub_item[2] ) {
					$tab['is_active'] = true;
				}

				// Special case for "Add New" page.
				if ( 5 === $i && "post-new.php?post_type=$cpt_slug" === $submenu_file ) {
					$tab['is_active'] = true;
				}

				$tabs[] = $tab;
			}
		}

		$tabs[] = [
			'url'  => 'https://wordpress.org/support/plugin/constant-contact-forms/reviews/#new-post',
			'text' => esc_html__( 'Review Constant Contact Forms', 'constant-contact-forms' ),
		];

		$connect_title = esc_html__( 'Connected', 'constant-contact-forms' );
		$connect_alt   = esc_html__( 'Your Constant Contact account is connected!', 'constant-contact-forms' );
		$api_status    = esc_html( 'connected' );
		if ( ! constant_contact()->api->is_connected() || constant_contact_get_needs_manual_reconnect() ) {
			$connect_title = esc_html__( 'Disconnected', 'constant-contact-forms' );
			$connect_alt   = esc_html__( 'Your Constant Contact account is not connected.', 'constant-contact-forms' );
			$api_status    = esc_attr( 'disconnected' );
		}
		?>
			<div class="ctct-header">
				<h2><?php esc_html_e( 'Constant Contact', 'constant-contact-forms' ); ?></h2>
				<?php
				if ( $tabs ) {
					echo wp_kses( '<ul>', [ 'ul' => [] ] );
					foreach ( $tabs as $tab ) {
						echo wp_kses( '<li>', [ 'li' => [] ] );
						printf(
							'<a class="ctct-item%s" href="%s">%s</a>',
							! empty( $tab['is_active'] ) ? ' is-active' : '',
							esc_url( $tab['url'] ),
							esc_html( $tab['text'] )
						);
						echo wp_kses( '</li>', [ 'li' => [] ] );
					}
					echo wp_kses( '</ul>', [ 'ul' => [] ] );
				}
				?>
				<a href="edit.php?post_type=ctct_forms&page=ctct_options_connect" class="ctct-status ctct-<?php echo esc_attr( $api_status ); ?>" title="<?php echo esc_attr( $connect_alt ); ?>">
					<?php echo esc_html( $connect_title ); ?>
				</a>
			</div>
		<?php
	}

	/**
	 * Register our setting to WordPress.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Add menu options page.
	 *
	 * @since 1.0.0
	 */
	public function add_options_page() {

		add_submenu_page(
			$this->parent_menu_slug,
			esc_html__( 'About', 'constant-contact-forms' ),
			esc_html__( 'About', 'constant-contact-forms' ),
			'manage_options',
			$this->key . '_about',
			[ $this, 'admin_page_display' ]
		);

		// This is for the GPLv3 license, not a premium product license.
		add_submenu_page(
			$this->parent_menu_slug,
			esc_html__( 'License', 'constant-contact-forms' ),
			esc_html__( 'License', 'constant-contact-forms' ),
			'manage_options',
			$this->key . '_license',
			[ $this, 'admin_page_display' ]
		);

		// This page is ultimately linked to from the About page content.
		remove_submenu_page( $this->parent_menu_slug, $this->key . '_license' );

		// Include CMB CSS in the head to avoid FOUC.
		add_action( "admin_print_styles-{$this->options_page}", [ 'CMB2_hookup', 'enqueue_cmb_css' ] );

	}

	/**
	 * Admin page markup. Mostly handled by CMB2.
	 *
	 * @since 1.0.0
	 */
	public function admin_page_display() {

		/**
		 * Fires before the Constant Contact admin page display.
		 *
		 * @since 1.0.0
		 */
		do_action( 'constant_contact_admin_before' );

		?>
		<div class="wrap cmb2-options-page <?php echo esc_attr( $this->key ); ?> ctct-page-wrap">

			<?php

			$page = [];
			// phpcs:disable WordPress.Security.NonceVerification -- OK accessing of $_GET values.
			if ( isset( $_GET['page'] ) ) {
				$page_key = sanitize_text_field( wp_unslash( $_GET['page'] ) );
				$page     = explode( $this->key . '_', $page_key );
			}
			// phpcs:enable WordPress.Security.NonceVerification

			if ( isset( $page[1] ) && $page[1] ) {

				switch ( esc_attr( $page[1] ) ) {
					case 'about':
						constant_contact()->admin_pages->about_page();
						break;
					case 'help':
						constant_contact()->admin_pages->help_page();
						break;
					case 'license':
						constant_contact()->admin_pages->license_page();
						break;
				}
			} else {
				cmb2_metabox_form( $this->metabox_id, $this->key );
			}
			?>
		</div>
		<?php

		/**
		 * Fires after the Constant Contact admin page display.
		 *
		 * @since 1.0.0
		 */
		do_action( 'constant_contact_admin_after' );
	}

	/**
	 * Register settings notices for display.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $object_id Option key.
	 * @param array $updated   Array of updated fields.
	 *
	 * @return void
	 */
	public function settings_notices( $object_id, $updated ) {

		if ( $object_id !== $this->key || empty( $updated ) ) {
			return;
		}

		add_settings_error( $this->key . '-notices', '', esc_html__( 'Settings updated.', 'constant-contact-forms' ), 'updated' );
		settings_errors( $this->key . '-notices' );
	}

	/**
	 * Public getter method for retrieving protected/private variables.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception Throws an exception if the field is invalid.
	 *
	 * @param string $field Field to retrieve.
	 * @return mixed Field value or exception is thrown.
	 */
	public function __get( $field ) {

		$field = esc_attr( $field );

		if ( in_array( $field, [ 'key', 'metabox_id', 'title', 'options_page' ], true ) ) {
			return $this->{$field};
		}

		return constant_contact()->{$field};
	}

	/**
	 * Add columns to Forms post type.
	 *
	 * @internal
	 *
	 * @param array $columns post list columns.
	 *
	 * @return array $columns Array of columns to add.
	 *
	 * @since 1.0.0
	 */
	public function set_custom_columns( array $columns ) {

		$columns['description'] = esc_html__( 'Description', 'constant-contact-forms' );
		$columns['shortcodes']  = esc_html__( 'Shortcode', 'constant-contact-forms' );
		$columns['ctct_list']   = esc_html__( 'Associated List', 'constant-contact-forms' );

		return $columns;
	}

	/**
	 * Content of custom post columns.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param string  $column  Column title.
	 * @param integer $post_id Post id of post item.
	 *
	 * @return void
	 */
	public function custom_columns( string $column, int $post_id ) {
		$post_id = absint( $post_id );

		if ( ! $post_id ) {
			return;
		}

		$table_list_ids = get_post_meta( $post_id, '_ctct_list', true );
		$table_list_ids = is_array( $table_list_ids ) ? $table_list_ids : [ $table_list_ids ];

		switch ( $column ) {
			case 'shortcodes':
				echo '<div class="ctct-shortcode-wrap"><input class="ctct-shortcode" type="text" value="';
				echo esc_html( '[ctct form="' . $post_id . '" show_title="false"]' );
				echo '" readonly="readonly">';
				echo '<button type="button" class="button" data-copied="' . esc_html__( 'Copied!', 'constant-contact-forms' ) . '">';
				echo esc_html__( 'Copy', 'constant-contact-forms' );
				echo '</button>';
				echo '</div>';
				break;
			case 'description':
				echo wp_kses_post( wpautop( get_post_meta( $post_id, '_ctct_description', true ) ) );
				break;
			case 'ctct_list':
				$list_html = [];

				foreach ( $table_list_ids as $list_id ) {
					$list = $this->get_associated_list_by_id( $list_id );
					if ( ! empty( $list ) ) {
						$edit_url    = ( null !== get_edit_post_link( $list->ID ) ) ?
							get_edit_post_link( $list->ID ) :
							'';
						$title       = get_the_title( $list->ID );
						$list_html[] = sprintf(
							'<a href="%s">%s</a>',
							esc_url( $edit_url ),
							esc_html( $title )
						);
					}
				}

				if ( empty( $list_html ) ) {
					esc_html_e( 'No associated list', 'constant-contact-forms' );
					break;
				}

				echo wp_kses_post( implode( ', ', $list_html ) );
				break;
		}
	}

	/**
	 * Add our contact count column for ctct_lists.
	 *
	 * @internal
	 * @since 1.3.0
	 *
	 * @param array $columns WP_List_Table columns.
	 * @return mixed
	 */
	public function set_custom_lists_columns( $columns ) {
		$columns['ctct_total'] = esc_html__( 'Contact Count', 'constant-contact-forms' );

		unset( $columns['date'] );

		return $columns;
	}

	/**
	 * Content of custom post columns.
	 *
	 * @internal
	 * @since 1.3.0
	 *
	 * @param string  $column  Column title.
	 * @param integer $post_id Post id of post item.
	 *
	 * @return void
	 */
	public function custom_lists_columns( $column, $post_id ) {

		$post_id = absint( $post_id );

		if ( ! $post_id ) {
			return;
		}

		$table_list_id = get_post_meta( $post_id, '_ctct_list_id', true );

		switch ( $column ) {
			case 'ctct_total':
				$list_info = constant_contact()->api->get_list( esc_attr( $table_list_id ) );
				$list_info = (object) $list_info;
				if ( isset( $list_info->membership_count ) ) {
					echo esc_html( $list_info->membership_count );
				} else {
					esc_html_e( 'None available', 'constant-contact-forms' );
				}

				break;
		}
	}

	/**
	 * Add social media links to plugin screen.
	 *
	 * @since 1.0.0
	 *
	 * @param array $links plugin action links.
	 * @return array
	 */
	public function add_social_links( $links ) {

		if ( ! is_array( $links ) ) {
			return $links;
		}

		$x_cta = esc_html__( 'Check out the official WordPress plugin from @constantcontact:', 'constant-contact-forms' );

		$add_links[] = $this->get_admin_link( esc_html__( 'About', 'constant-contact-forms' ), 'about' );
		$add_links[] = $this->get_admin_link( esc_html__( 'License', 'constant-contact-forms' ), 'license' );

		/**
		 * Filters the Constant Contact base url used for social links.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value Social URL base.
		 */
		$site_link = esc_url( apply_filters( 'constant_contact_social_base_url', 'https://constantcontact.com/' ) );

		$social_share = esc_html__( 'Spread the word!', 'constant-contact-forms' );
		$add_links[]  = '<a title="' . $social_share . '" href="https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $site_link ) . '" target="_blank" rel="noopener noreferrer" class="dashicons-before dashicons-facebook"></a>';
		$add_links[]  = '<a title="' . $social_share . '" href="https://x.com/intent/post?text=' . $x_cta . ' ' . $site_link . '" target="_blank" rel="noopener noreferrer"><img src="' . esc_url( constant_contact()->url ) . '/assets/images/logo-black.png" alt="' . esc_attr__( 'X logo', 'constant-contact-forms' ) . '" style="height:16px;width:16px;float:none;"></a>';

		/**
		 * Filters the final custom social links.
		 *
		 * @since 1.0.0
		 *
		 * @param array $add_links Array of social links with HTML markup.
		 */
		$add_links = apply_filters( 'constant_contact_social_links', $add_links );

		return array_merge( $links, $add_links );
	}

	/**
	 * Get a link to an admin page.
	 *
	 * @since 1.0.1
	 *
	 * @param string $text      The link text to use.
	 * @param string $link_slug The slug of the admin page.
	 * @return string
	 */
	public function get_admin_link( $text, $link_slug ) {

		static $link_template = '<a title="%1$s" href="%2$s" target="_blank" rel="noopener noreferrer">%1$s</a>';
		static $link_args     = [
			'post_type' => 'ctct_forms',
			'page'      => '',
		];

		$link_args['page'] = 'ctct_options_' . $link_slug;
		$link              = add_query_arg( $link_args, admin_url( 'edit.php' ) );

		return sprintf( $link_template, $text, $link );
	}

	/**
	 * Scripts.
	 *
	 * @since 1.0.0
	 *
	 * @param array $extra_localizations Optional. An array of arrays of `[ $handle, $name, $data ]` passed to wp_localize_script.
	 * @return void
	 */
	public function scripts( $extra_localizations = [] ) {

		global $pagenow;

		$debug  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true );
		$suffix = ( true === $debug ) ? '' : '.min';

		wp_register_script(
			'ctct_form',
			constant_contact()->url() . 'assets/js/ctct-plugin-admin' . $suffix . '.js',
			[],
			Constant_Contact::VERSION,
			true
		);

		wp_localize_script(
			'ctct_form',
			'ctctTexts',
			/**
			 * Filters the text used as part of the ctct_form javascript object.
			 *
			 * @since 1.0.0
			 *
			 * @param array $value Array of strings to be used with javascript calls.
			 */
			apply_filters(
				'constant_contact_localized_js_texts',
				[
					'leavewarning' => esc_html__( 'You have unsaved changes.', 'constant-contact-forms' ),
					'move_up'      => esc_html__( 'move up', 'constant-contact-forms' ),
					'move_down'    => esc_html__( 'move down', 'constant-contact-forms' ),
				]
			)
		);

		if (
			'ctct_options_settings' === filter_input( INPUT_GET, 'page', FILTER_SANITIZE_SPECIAL_CHARS )
		) {
			wp_enqueue_script( 'ctct_form' );
		}

		$current_screen = get_current_screen();
		$is_gutenberg   = is_object( $current_screen ) ? $current_screen->is_block_editor : true;

		/**
		 * Filters the allowed pages to enqueue the ctct_form script on.
		 *
		 * @since 1.0.0
		 *
		 * @param array $value Array of WP Admin base files to conditionally load on.
		 */
		$allowed_pages = apply_filters( 'constant_contact_script_load_pages', [ 'post.php', 'post-new.php' ] );
		if ( $pagenow && in_array( $pagenow, $allowed_pages, true ) && ! constant_contact()->is_constant_contact() ) {
			wp_enqueue_script( 'ctct_form' );
		}

		if ( ! is_array( $extra_localizations ) ) {
			return;
		}

		if ( ! empty( $extra_localizations ) ) {
			if ( ! is_array( $extra_localizations[0] ) ) {
				$extra_localizations = [ $extra_localizations ];
			}

			foreach ( $extra_localizations as $localization_set ) {
				call_user_func_array( 'wp_localize_script', $localization_set );
			}
		}
	}

	/**
	 * Fetch Constant Contact List post type ID by Constant Contact List ID.
	 *
	 * @since 1.3.0
	 *
	 * @param string $list_id Constant Contact list ID value.
	 * @return mixed
	 */
	public function get_associated_list_by_id( $list_id ) {
		global $wpdb;

		$rs = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT p.ID
				FROM $wpdb->posts as p
				INNER JOIN $wpdb->postmeta as pm on p.ID = pm.post_id
				WHERE pm.meta_key = '_ctct_list_id'
				AND pm.meta_value = %s",
				$list_id
			)
		);

		if ( ! empty( $rs ) ) {
			return $rs[0];
		}

		return [];
	}
}

/**
 * Wrapper function around cmb2_get_option.
 *
 * @since 1.0.0
 *
 * @param string $key Options array key.
 * @return mixed Option value.
 */
function constantcontact_get_option( $key = '' ) {
	return cmb2_get_option( constant_contact()->admin->key, $key );
}

<?php
/**
 * Admin
 *
 * @package ConstantContact
 * @subpackage Admin
 * @author Constant Contact
 * @since 1.0.1
 */

/**
 * Powers admin options pages, customized display for plugin listing, and admin scripts.
 */
class ConstantContact_Admin {

	/**
	 * Option key, and option page slug.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $key = 'ctct_options';

	/**
	 * Options page metabox id.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $metabox_id = 'ctct_option_metabox';

	/**
	 * Options Page title.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $title = '';

	/**
	 * Options Page hook.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $options_page = '';

	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	protected $plugin = null;

	/**
	 * The parent menu page slug.
	 *
	 * @since 1.0.01
	 * @var string
	 */
	protected $parent_menu_slug = 'edit.php?post_type=ctct_forms';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Constant_Contact $plugin Primary class file.
	 * @param string $basename Primary class basename.
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
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ), 999 );

		add_filter( 'manage_ctct_forms_posts_columns', array( $this, 'set_custom_columns' ) );
		add_action( 'manage_ctct_forms_posts_custom_column' , array( $this, 'custom_columns' ), 10, 2 );

		add_filter( 'manage_ctct_lists_posts_columns', array( $this, 'set_custom_lists_columns' ) );
		add_action( 'manage_ctct_lists_posts_custom_column', array( $this, 'custom_lists_columns' ), 10, 2 );

		add_filter( 'plugin_action_links_' . $this->basename, array( $this, 'add_social_links' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
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
			__( 'About', 'constant-contact-forms' ),
			__( 'About', 'constant-contact-forms' ),
			'manage_options',
			$this->key . '_about',
			array( $this, 'admin_page_display' )
		);

		add_submenu_page(
			$this->parent_menu_slug,
			__( 'License', 'constant-contact-forms' ),
			__( 'License', 'constant-contact-forms' ),
			'manage_options',
			$this->key . '_license',
			array( $this, 'admin_page_display' )
		);

		remove_submenu_page( $this->parent_menu_slug, $this->key . '_license' );
		// Include CMB CSS in the head to avoid FOUC.
		add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
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
		<div class="wrap cmb2-options-page <?php echo esc_attr( $this->key ); ?>">

			<div id="options-wrap">
				<?php

				// If we have a page get var set, let's try to pull out the page we're looking for.
				if ( isset( $_GET['page'] ) ) {

					$page_key = sanitize_text_field( wp_unslash( $_GET['page'] ) );

					$page = explode( $this->key . '_', $page_key );
				} else {
					$page = array();
				}

				// If we have a second element set, let's check out what it should be.
				if ( isset( $page[1] ) && $page[1] ) {

					// Switch through our whitelisted pages that we shoud include.
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
	 */
	public function settings_notices( $object_id, $updated ) {
		if ( $object_id !== $this->key || empty( $updated ) ) {
			return;
		}
		add_settings_error( $this->key . '-notices', '', __( 'Settings updated.', 'constant-contact-forms' ), 'updated' );
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

		// Allowed fields to retrieve.
		if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		} else {
			return constant_contact()->{$field};
		}
	}

	/**
	 * Add columns to Forms post type.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns post list columns.
	 * @return array $columns Array of columns to add.
	 */
	public function set_custom_columns( $columns ) {

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
	 */
	public function custom_columns( $column, $post_id ) {

		// Force our $post_id to an int.
		$post_id = absint( $post_id );

		// If its a 0 bail out.
		if ( ! $post_id ) {
			return;
		}

		$table_list_id = get_post_meta( $post_id, '_ctct_list', true );

		switch ( $column ) {
			case 'shortcodes':
				echo esc_attr( '[ctct form="' . $post_id . '"]' );
			break;
			case 'description':
				echo wp_kses_post( wpautop( get_post_meta( $post_id, '_ctct_description', true ) ) );
			break;
			case 'ctct_list':
				$list = $this->get_associated_list_by_id( $table_list_id );
				if ( ! empty( $list ) ) {
					printf(
						'<a href="%s">%s</a>',
						get_edit_post_link( $list->ID ),
						get_the_title( $list->ID )
					);
				} else {
					esc_html_e( 'No associated form', 'constant-contact-forms' );
				}
			break;
		}
	}

	/**
	 * Add our contact count column for ctct_lists.
	 *
	 * @internal
	 * @since 1.3.0
	 *
	 * @param $columns
	 * @return mixed
	 */
	public function set_custom_lists_columns( $columns ) {
		$columns['ctct_total'] = esc_html__( 'Contact Count', 'constant-contact-forms' );

		// No need to display the date of a sync'd list post.
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
	 */
	public function custom_lists_columns( $column, $post_id ) {

		// Force our $post_id to an int.
		$post_id = absint( $post_id );

		// If its a 0 bail out.
		if ( ! $post_id ) {
			return;
		}

		$table_list_id = get_post_meta( $post_id, '_ctct_list_id', true );

		switch ( $column ) {
			case 'ctct_total':
				$list_info = constant_contact()->api->get_list( esc_attr( $table_list_id ) );
				echo ( isset( $list_info->contact_count ) ) ? $list_info->contact_count : esc_html__( 'None available', 'constant-contact-forms' );
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
		// Get Twitter share link.
		$twitter_cta = __( 'Check out the official WordPress plugin from @constantcontact :', 'constant-contact-forms' );

		// Add about page.
		$add_links[] = $this->get_admin_link( __( 'About', 'constant-contact-forms' ), 'about' );
		$add_links[] = $this->get_admin_link( __( 'License', 'constant-contact-forms' ), 'license' );

		/**
		 * Filters the Constant Contact base url used for social links.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value Social URL base.
		 */
		$site_link = apply_filters( 'constant_contact_social_base_url' , 'https://constantcontact.com/' );

		// Start our social share links.
		$social_share = __( 'Spread the word!', 'constant-contact-forms' );
		$add_links[] = '<a title="' . $social_share . '" href="https://www.facebook.com/sharer/sharer.php?u=' . urlencode( $site_link ) . '" target="_blank" class="dashicons-before dashicons-facebook"></a>';
		$add_links[] = '<a title="' . $social_share . '" href="https://twitter.com/home?status=' . $twitter_cta . ' ' . $site_link . '" target="_blank" class="dashicons-before dashicons-twitter"></a>';
		$add_links[] = '<a title="' . $social_share . '" href="https://plus.google.com/share?url=' . urlencode( $site_link ) . '" target="_blank" class="dashicons-before dashicons-googleplus"></a>';

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

		// Resuse these.
		static $link_template = '<a title="%1$s" href="%2$s" target="_blank">%1$s</a>';
		static $link_args = array(
			'post_type' => 'ctct_forms',
			'page'      => '',
		);

		$link_args['page'] = 'ctct_options_' . $link_slug;
		$link = add_query_arg( $link_args, admin_url( 'edit.php' ) );
		return sprintf( $link_template, $text, $link );
	}

	/**
	 * Scripts.
	 *
	 * @since 1.0.0
	 *
	 * @param array $extra_localizations An array of arrays of `array( $handle, $name, $data )` passed to wp_localize_script.
	 */
	public function scripts( $extra_localizations = array() ) {

		global $pagenow;

		// Check if we are in debug mode. allow.
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true ? true : false;

		// Based on our debug mode, potentially add a min prefix.
		$suffix = ( true === $debug ) ? '' : '.min';

		wp_register_script(
			'ctct_form',
			constant_contact()->url() . 'assets/js/ctct-plugin-admin' . $suffix . '.js',
			array(),
			Constant_Contact::VERSION,
			true
		);

		wp_localize_script(
			'ctct_form',
			'ctct_texts',
			/**
			 * Filters the text used as part of the ctct_form javascript object.
			 *
			 * @since 1.0.0
			 *
			 * @param array $value Array of strings to be used with javascript calls.
			 */
			apply_filters( 'constant_contact_localized_js_texts', array(
				'leavewarning' => __( 'You have unsaved changes.', 'constant-contact-forms' ),
				'move_up'      => __( 'move up', 'constant-contact-forms' ),
				'move_down'    => __( 'move down', 'constant-contact-forms' ),
			) )
		);
		$privacy_settings = get_option( 'ctct_privacy_policy_status', '' );

		wp_localize_script(
			'ctct_form',
			'ctct_settings',
			array(
				'privacy_set' => ( empty( $privacy_settings ) ) ? 'no' : 'yes',
			)
		);

		if ( constant_contact_maybe_display_optin_notification() || ( isset( $_GET['page'] ) && 'ctct_options_settings' === $_GET['page'] ) ) {
			wp_enqueue_script( 'ctct_form' );
		}

		// Some admin_enqueue_scripts action calls pass the pagenow string value and not an array.
		if ( ! is_array( $extra_localizations ) ) {
			return;
		}

		// If we have extra localizations, iterate and call with `wp_localize_script`.
		if ( ! empty( $extra_localizations ) ) {
			// We only have a single array, put it in another array.
			if ( ! is_array( $extra_localizations[0] ) ) {
				$extra_localizations = array( $extra_localizations );
			}

			foreach ( $extra_localizations as $localization_set ) {
				call_user_func_array( 'wp_localize_script', $localization_set );
			}
		}

		/**
		 * Filters the allowed pages to enqueue the ctct_form script on.
		 *
		 * @since 1.0.0
		 *
		 * @param array $value Array of WP Admin base files to conditionally load on.
		 */
		$allowed_pages = apply_filters( 'constant_contact_script_load_pages', array( 'post.php', 'post-new.php' ) );

		if ( $pagenow && in_array( $pagenow, $allowed_pages, true ) ) {
			// Enqueued script with localized data.
			wp_enqueue_script( 'ctct_form' );
		}
	}

	/**
	 * Fetch Constant Contact List post type ID by Constant Contact List ID.
	 *
	 * @since 1.3.0
	 *
	 * @param string $list_id Constant Contact list ID value
	 * @return mixed
	 */
	public function get_associated_list_by_id( $list_id ) {
		global $wpdb;
		$sql = "SELECT p.ID FROM $wpdb->posts as p INNER JOIN $wpdb->postmeta as pm on p.ID = pm.post_id WHERE pm.meta_key = '_ctct_list_id' AND pm.meta_value = %s";
		$rs = $wpdb->get_results(
			$wpdb->prepare( $sql, $list_id )
		);
		if ( ! empty( $rs ) ) {
			return $rs[0];
		}
		return array();
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

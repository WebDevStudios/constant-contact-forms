<?php
/**
 * ConstantContact_Admin class
 *
 * @package ConstantContactAdmin
 * @subpackage ConstantContact
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Class ConstantContact_Admin
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
	 * Holds an instance of the object.
	 *
	 * @since 1.0.0
	 * @var BuddyExtender_Admin
	 */
	private static $instance = null;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		// Set our title.
		$this->title = __( constant_contact()->plugin_name, 'constantcontact' );
	}

	/**
	 * Returns the running object
	 *
	 * @since 1.0.0
	 *
	 * @return BuddyExtender_Admin
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new ConstantContact_Admin();
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
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ), 999 );

		add_filter( 'manage_ctct_forms_posts_columns', array( $this, 'set_custom_columns' ) );
		add_action( 'manage_ctct_forms_posts_custom_column' , array( $this, 'custom_columns' ), 10, 2 );

		add_filter( 'plugin_action_links_' . $this->basename, array( $this, 'add_social_links' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
	}


	/**
	 * Register our setting to WP.
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
			'edit.php?post_type=ctct_forms',
			__( 'Help', 'constanctcontact' ),
			__( 'Help', 'constanctcontact' ),
			'manage_options',
			$this->key . '_help',
			array( $this, 'admin_page_display' )
		);

		add_submenu_page(
			'edit.php?post_type=ctct_forms',
			__( 'About', 'constanctcontact' ),
			__( 'About', 'constanctcontact' ),
			'manage_options',
			$this->key . '_about',
			array( $this, 'admin_page_display' )
		);

		// Include CMB CSS in the head to avoid FOUC.
		add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
	}

	/**
	 * Admin page markup. Mostly handled by CMB2.
	 *
	 * @since 1.0.0
	 */
	public function admin_page_display() {
		do_action( 'constant_contact_admin_before' );
		?>
		<div class="wrap cmb2-options-page <?php echo esc_attr( $this->key ); ?>">

			<div id="options-wrap">
				<?php

				// If we have a $_GET['page'], let's try to pull out the page we're looking for
				if ( isset( $_GET['page'] ) && $_GET['page'] ) {
					$page = explode( $this->key . '_', esc_attr( $_GET['page'] ) );
				} else {
					$page = array();
				}

				// If we have a second element set, let's check out what it should be
				if ( isset( $page[1] ) && $page[1] ) {

					// switch through our whitelisted pages that we shoud include
					switch ( $page[1] ) {
						case 'about':
							include_once( constant_contact()->path . 'inc/admin/about.php' );
							break;
						case 'help':
							include_once( constant_contact()->path . 'inc/admin/help.php' );
							break;
					}
				} else {
					cmb2_metabox_form( $this->metabox_id, $this->key );
				}
				?>
			</div>
		</div>
		<?php
		do_action( 'constant_contact_admin_after' );
	}

	/**
	 * Register settings notices for display.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $object_id Option key.
	 * @param array $updated Array of updated fields.
	 */
	public function settings_notices( $object_id, $updated ) {
		if ( $object_id !== $this->key || empty( $updated ) ) {
			return;
		}
		add_settings_error( $this->key . '-notices', '', __( 'Settings updated.', 'constantcontact' ), 'updated' );
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
		// Allowed fields to retrieve.
		if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}
		throw new Exception( 'Invalid property: ' . $field );
	}

	/**
	 * Admin Page Tabs
	 *
	 * @return void
	 */
	public function page_tabs() {

		$tabs = array(
			array(
				'title' => __( 'Tab 1', 'constantcontact' ),
				'url' => '',
				'key' => 'builder',
				'callback' => 'admin_page_display',
			),
			array(
				'title' => __( 'Tab 2', 'constantcontact' ),
				'url' => '',
				'key' => 'help',
				'callback' => 'admin_page_display',
			),
			array(
				'title' => __( 'Tab 3', 'constantcontact' ),
				'url' => '',
				'key' => 'about',
				'callback' => 'admin_page_display',
			),
		);

		echo '<h2 class="nav-tab-wrapper">';

		foreach ( $tabs as $tab => $value ) {
			$active = 0 === $tab ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . esc_attr( $active ) . '">' . esc_attr( $value['title'] ) . '</a>';
		}

		echo '</h2>';
	}

	/**
	 * Add shortcode columns to each cpt.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 * @param array $columns post list columns.
	 * @return array $columns Array of columns to add.
	 */
	public function set_custom_columns( $columns ) {

		$columns['description'] = __( 'Description', 'constantcontact' );
		$columns['shortcodes']  = __( 'Shortcode', 'constantcontact' );

		return $columns;
	}

	/**
	 * Content of custom  post columns.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 * @param string  $column  Column title.
	 * @param integer $post_id Post id of post item.
	 */
	public function custom_columns( $column, $post_id ) {

		// Force our $post_id to an int
		$post_id = absint( $post_id );

		// if its a 0 bail out
		if ( ! $post_id ) {
			return;
		}

		switch ( $column ) {
			case 'shortcodes':
				echo esc_attr( '[ctct form="' . $post_id . '"]' );
			break;
			case 'description':
				echo esc_attr( get_post_meta( $post_id, '_ctct_description', true ) );
			break;
		}
	}

	/**
	 * Add social media links to plugin screen
	 *
	 * @param array $links plugin action links.
	 */
	public function add_social_links( $links ) {

		// Generate our site link
		$site_link = apply_filters( 'constant_contact_social_base_url' , 'https://constantcontact.com/' );

		// Build up all our social links
		$add_links = apply_filters( 'constant_contacnt_social_links', array(
			'<a title="' . __( 'Be a better marketer. All it takes is Constant Contact email marketing.', 'constantcontact' ) . '" href="' . $site_link . '" target="_blank">constantcontact.com</a>',
			'<a title="' . __( 'Spread the word!', 'constantcontact' ) . '" href="https://www.facebook.com/sharer/sharer.php?u=' . urlencode( $site_link ) . '" target="_blank" class="dashicons-before dashicons-facebook"></a>',
			'<a title="' . __( 'Spread the word!', 'constantcontact' ) . '" href="https://twitter.com/home?status=' . __( 'Check out the official WordPress plugin from @constantcontact : ' . $site_link, 'constantcontact' ) . '" target="_blank" class="dashicons-before dashicons-twitter"></a>',
			'<a title="' . __( 'Spread the word!', 'constantcontact' ) . '" href="https://plus.google.com/share?url=' . urlencode( $site_link ) . '" target="_blank" class="dashicons-before dashicons-googleplus"></a>',
		) );

		return array_merge( $links, $add_links );
	}

	/**
	 * Scripts
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function scripts() {
		global $pagenow;

		// Check if we are in debug mode. allow
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG == true ? true : false;

		// based on our debug mode, potentially add a min prefix
		$suffix = ( true === $debug ) ? '' : '.min';

		// Register out javascript file.
		wp_register_script(
			'ctct_form',
			constant_contact()->url() . 'assets/js/plugin' . $suffix . '.js',
			array(),
			constant_contact()->version,
			true
		);

		// Allow filtering of allowed pages that we load scripts on
		$allowed_pages = apply_filters( 'constant_contact_script_load_pages', array( 'post.php', 'post-new.php' ) );

		if ( isset( $pagenow ) && is_array( $pagenow ) && in_array( $pagenow, $allowed_pages, true ) ) {
			// Enqueued script with localized data.
			wp_enqueue_script( 'ctct_form' );
		}
	}
}

/**
 * Helper function to get/return the BPExtender_Admin object.
 *
 * @since 1.0.0
 *
 * @return ConstantContact_Admin object.
 */
function constantcontact_admin() {
	return ConstantContact_Admin::get_instance();
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
	return cmb2_get_option( constantcontact_admin()->key, $key );
}

// Get it started.
constantcontact_admin();

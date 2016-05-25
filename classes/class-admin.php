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
		$this->title = __( constant_contact()->plugin_name, constant_contact()->text_domain );
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
		add_action( 'cmb2_admin_init', array( $this, 'add_options_page_metabox' ) );
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

		$icon = constant_contact()->menu_icon;

		add_submenu_page(
			'edit.php?post_type=ctct_forms',
			'Help',
			'Help',
			'manage_options',
			$this->key . '_help',
			array( $this, 'admin_page_display' )
		);

		add_submenu_page(
			'edit.php?post_type=ctct_forms',
			'About',
			'About',
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
		wp_enqueue_style( 'ad-sidebar' );
		?>
		<div class="wrap cmb2-options-page <?php echo esc_attr( $this->key ); ?>">

			<div id="options-wrap">
				<?php
				$page = isset( $_GET['page'] ) ? explode(  $this->key . '_', $_GET['page'] ) : '';

				//bpextender_products_sidebar();

				if ( in_array( $page[1], array( 'about', 'help' ) ) ) {
					if ( file_exists( constant_contact()->path . 'inc/admin/'. $page[1] .'.php' )  ) {
						include_once( constant_contact()->path . 'inc/admin/'. $page[1] .'.php' );
					}
				} else {
					cmb2_metabox_form( $this->metabox_id, $this->key );
				}

				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Add the options metabox to the array of metaboxes.
	 *
	 * @since 1.0.0
	 */
	function add_options_page_metabox() {

		$prefix = '_ctct_';

		// Hook in our save notices.
		add_action( "cmb2_save_options-page_fields_{$this->metabox_id}", array( $this, 'settings_notices' ), 10, 2 );

		$cmb = new_cmb2_box( array(
			'id'		 => $this->metabox_id,
			'hookup'	 => false,
			'cmb_styles' => false,
			'show_on'	=> array(
			// These are important don't remove.
			'key'   => 'options-page',
			'value' => array( $this->key ),
			),
		) );

		$cmb->add_field( array(
			'name' => 'Test Title',
			'desc' => 'This is a title description',
			'type' => 'title',
			'id'   => $prefix . 'test_title'
		) );

		$cmb->add_field( array(
			'name' => 'Test Text',
			'desc' => '',
			'type' => 'text',
			'id'   => $prefix . 'test_text'
		) );
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
		add_settings_error( $this->key . '-notices', '', __( 'Settings updated.', constant_contact()->text_domain ), 'updated' );
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
				'title' => __( 'Tab 1', constant_contact()->text_domain ),
				'url' => '',
				'key' => 'builder',
				'callback' => 'admin_page_display',
			),
			array(
				'title' => __( 'Tab 2', constant_contact()->text_domain ),
				'url' => '',
				'key' => 'help',
				'callback' => 'admin_page_display',
			),
			array(
				'title' => __( 'Tab 3', constant_contact()->text_domain ),
				'url' => '',
				'key' => 'about',
				'callback' => 'admin_page_display',
			),
		);

		echo '<h2 class="nav-tab-wrapper">';
			foreach ( $tabs as $tab => $value ) {

				$active = 0 === $tab ? 'nav-tab-active' : '';
				echo '<a class="nav-tab '. esc_attr( $active ) .'">'. esc_attr( $value['title'] ) .'</a>';
			}
		echo '</h2>';
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

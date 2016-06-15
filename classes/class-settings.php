<?php
/**
 * CMB2 Network Settings
 * @version 1.0.0
 */
class ConstantContact_Settings {

	/**
 	 * Option key, and option page slug
 	 * @var string
 	 */
	private $key = 'ctct_options_settings';

	/**
 	 * Settings page metabox id
 	 * @var string
 	 */
	private $metabox_id = 'ctct_option_metabox_settings';

	/**
	 * Settings Page title
	 * @var string
	 */
	protected $title = '';

	/**
	 * Settings Page hook
	 * @var string
	 */
	protected $options_page = '';

	/**
	 * Holds an instance of the project
	 *
	 * @Myprefix_Network_Admin
	 **/
	private static $instance = null;

	/**
	 * Constructor
	 * @since 1.0.0
	 */
	private function __construct() {
	}

	/**
	 * Get the running object
	 *
	 * @return Myprefix_Network_Admin
	 **/
	public static function get_instance() {
		if( is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->hooks();
		}
		return self::$instance;
	}

	/**
	 * Initiate our hooks
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'cmb2_admin_init', array( $this, 'add_options_page_metabox' ) );

		// Override CMB's getter
		add_filter( 'cmb2_override_option_get_'. $this->key, array( $this, 'get_override' ), 10, 2 );
		// Override CMB's setter
		add_filter( 'cmb2_override_option_save_'. $this->key, array( $this, 'update_override' ), 10, 2 );

		add_action( 'cmb2_init', array( $this, 'add_optin_to_forms' ) );
		add_filter( 'preprocess_comment', array( $this, 'process_optin_comment_form' ) );
		add_filter( 'authenticate', array( $this, 'process_optin_login_form' ), 10, 3 );

	}

	/**
	 * Register our setting to WP
	 * @since  1.0.0
	 */
	public function init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Add menu options page
	 * @since 1.0.0
	 */
	public function add_options_page() {

		$this->options_page = add_submenu_page(
			'edit.php?post_type=ctct_forms',
			__( 'Settings', constant_contact()->text_domain ),
			__( 'Settings', constant_contact()->text_domain ),
			'manage_options',
			$this->key,
			array( $this, 'admin_page_display' )
		);

		// add_action( "admin_head-{$this->options_page}", array( $this, 'enqueue_js' ) );
		// Include CMB CSS in the head to avoid FOUC
		add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
	}

	/**
	 * Admin page markup. Mostly handled by CMB2
	 * @since  1.0.0
	 */
	public function admin_page_display() {
		?>
		<div class="wrap cmb2-options-page <?php echo $this->key; ?>">
			<h2><?php esc_attr_e( ' Settings', constant_contact()->text_domain ); ?></h2>
			<?php cmb2_metabox_form( $this->metabox_id, $this->key ); ?>
		</div>
		<?php
	}

	/**
	 * Add the options metabox to the array of metaboxes
	 * @since  1.0.0
	 */
	function add_options_page_metabox() {

		// hook in our save notices
		add_action( "cmb2_save_options-page_fields_{$this->metabox_id}", array( $this, 'settings_notices' ), 10, 2 );

		$cmb = new_cmb2_box( array(
			'id'		 => $this->metabox_id,
			'hookup'	 => false,
			'cmb_styles' => false,
			'show_on'	=> array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key, )
			),
		) );

		$option_options = array(
			'comment_form' => __( 'Comment Form', constant_contact()->text_domain ),
			'login_form' => __( 'Login Form', constant_contact()->text_domain ),
		);

		if ( get_option( 'users_can_register' ) ) {
			$option_options['reg_form'] = __( 'Registration Form', constant_contact()->text_domain );
		}


		if ( $lists = ctct_builder_admin()->get_lists() ) {

			unset( $lists['new'] );

			// Set our CMB2 fields
			$cmb->add_field( array(
				'name' 	=> __( 'Opt In', constant_contact()->text_domain ),
				'desc' 	=> __( 'Add opt in checkbox to selected forms.', constant_contact()->text_domain ),
				'id'   	=> '_ctct_optin_forms',
				'type'	=> 'multicheck',
				'options' => $option_options,
			) );

			$cmb->add_field( array(
				'name' 	=> __( 'Opt In Label', constant_contact()->text_domain ),
				'desc' 	=> __( 'Opt in checkbox form label.', constant_contact()->text_domain ),
				'id'   	=> '_ctct_optin_label',
				'type'	=> 'text',
			) );

			$cmb->add_field( array(
				'name' 	=> __( 'Opt In List', constant_contact()->text_domain ),
				'desc' 	=> __( 'Choose list to add opt in subsciptions.', constant_contact()->text_domain ),
				'id'   	=> '_ctct_optin_list',
				'type'	=> 'select',
				'show_option_none' => true,
				'default'		  => 'none',
				'options'		  => $lists,
			) );
		}

		$cmb->add_field( array(
			'name' 	=> __( 'API key', constant_contact()->text_domain ),
			'id'   	=> '_ctct_api_key',
			'type'	=> 'text',
		) );
		$cmb->add_field( array(
			'name' 	=> __( 'API Secret', constant_contact()->text_domain ),
			'id'   	=> '_ctct_api_secret',
			'type'	=> 'text',
		) );

	}

	/**
	 * Add selected optin to forms.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_optin_to_forms() {

		if ( ! ctct_builder_admin()->get_lists() ) { return; }

		$optin_selected = ctct_get_settings_option( '_ctct_optin_forms' );

		foreach ( $optin_selected as $key => $value ) {
			switch ( $value ) {
				case 'login_form':
					add_action( 'login_form', array( $this, 'optin_form_field' ) );
				break;
				case 'comment_form':
					add_action( 'comment_form_after_fields', array( $this, 'optin_form_field' ) );
				break;
				case 'reg_form':
					add_action( 'register_form', array( $this, 'optin_form_field' ) );
					add_action( 'signup_extra_fields', array( $this, 'optin_form_field' ) );
				break;
			}
		}
	}

	/**
	 * Opt in field checkbox
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function optin_form_field() {
		$label = ctct_get_settings_option( '_ctct_optin_label' ) ? ctct_get_settings_option( '_ctct_optin_label' ) : __( 'Sign up to our newsletter.', constant_contact()->text_domain );
	?>
	    <p style="padding: 0 0 1em 0;">
	        <label for="ctct_optin">
	        <input type="checkbox" value="<?php echo esc_attr( ctct_get_settings_option( '_ctct_optin_list' ) ); ?>" class="checkbox" id="ctct_optin" name="ctct_optin_list">
			<?php echo esc_attr( $label ); ?>
			</label>
	    </p>
	<?php

	}

	/**
	 * Sends contact to CTCT if optin checked
	 *
	 * @param  array $comment_data comment form data.
	 * @return array comment form data
	 */
	public function process_optin_comment_form( $comment_data ) {

		if ( isset( $_POST['ctct_optin_list'] ) ) {

			$args = array(
				'email' => sanitize_email( $comment_data['comment_author_email'] ),
				'list' => $_POST['ctct_optin_list'],
				'first_name' => sanitize_text_field( $comment_data['comment_author'] ),
				'last_name' => '',
			);

			constantcontact_api()->add_contact( $args );
		}

		return $comment_data;
	}

	/**
	 * Sends contact to CTCT if optin checked
	 *
	 * @param  [type] $user     [description]
	 * @param  [type] $username [description]
	 * @param  [type] $password [description]
	 * @return [type]           [description]
	 */
	public function process_optin_login_form( $user, $username, $password ) {

		if ( isset( $_POST['ctct_optin_list'] ) ) {

			$user_data = get_user_by('login', $username );

			//error_log( print_r( $user_data, true ) );

			$args = array(
				'email' => sanitize_email( $user_data->data->user_email ),
				'list' => $_POST['ctct_optin_list'],
				'first_name' => sanitize_text_field( $user_data->data->display_name ),
				'last_name' => '',
			);

			$addct = constantcontact_api()->add_contact( $args );

			//error_log( print_r( $addct, true ) );
		}

		return $user;
	}

	/**
	 * Register settings notices for display
	 *
	 * @since  1.0.0
	 * @param  int   $object_id Option key
	 * @param  array $updated   Array of updated fields
	 * @return void
	 */
	public function settings_notices( $object_id, $updated ) {
		if ( $object_id !== $this->key || empty( $updated ) ) {
			return;
		}

		add_settings_error( $this->key . '-notices', '', __( 'Settings updated.', constant_contact()->text_domain ), 'updated' );
		settings_errors( $this->key . '-notices' );
	}

	/**
	 * Replaces get_option with get_site_option
	 *
	 * @since  1.0.0
	 * @return mixed site option
	 */
	public function get_override( $test, $default = false ) {
		return get_site_option( $this->key, $default );
	}

	/**
	 * Replaces update_option with update_site_option
	 *
	 * @since  1.0.0
	 * @return mixed site option
	 */
	public function update_override( $test, $option_value ) {
		return update_site_option( $this->key, $option_value );
	}

	/**
	 * Public getter method for retrieving protected/private variables
	 *
	 * @since  1.0.0
	 * @param  string $field Field to retrieve.
	 * @return mixed Field value or exception is thrown
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve
		if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}

		throw new Exception( 'Invalid property: ' . $field );
	}

}

/**
 * Helper function to get/return the ConstantContact_Settings object
 *
 * @since  1.0.0
 * @return object ConstantContact_Settings
 */
function ctct_settings_admin() {
	return ConstantContact_Settings::get_instance();
}

/**
 * Wrapper function around cmb2_get_option
 *
 * @since  1.0.0
 * @param  string $key Options array key.
 * @return mixed Option value
 */
function ctct_get_settings_option( $key = '' ) {
	return cmb2_get_option( ctct_settings_admin()->key, $key );
}

// Get it started.
ctct_settings_admin();

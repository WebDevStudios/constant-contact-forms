<?php
/**
 * Powers our settings and options page, as well as injecting our optins to the front-end.
 *
 * @package ConstantContact
 * @subpackage ConstantContact_Settings
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * ConstantContact_Settings class
 */
class ConstantContact_Settings {

	/**
	 * Option key, and option page slug
	 *
	 * @var string
	 * @since  1.0.0
	 */
	private $key = 'ctct_options_settings';

	/**
	 * Settings page metabox id
	 *
	 * @var string
	 * @since  1.0.0
	 */
	private $metabox_id = 'ctct_option_metabox_settings';

	/**
	 * Settings Page title
	 *
	 * @var string
	 * @since  1.0.0
	 */
	protected $title = '';

	/**
	 * Settings Page hook
	 *
	 * @var string
	 * @since  1.0.0
	 */
	protected $options_page = '';

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
	 * @param object $plugin parent plugin instance.
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		// Kick it off / register our settings
		add_action( 'admin_init', array( $this, 'init' ) );

		// Add our options menu + options page
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'cmb2_admin_init', array( $this, 'add_options_page_metabox' ) );

		// Override CMB's getter.
		add_filter( 'cmb2_override_option_get_' . $this->key, array( $this, 'get_override' ), 10, 2 );

		// Override CMB's setter.
		add_filter( 'cmb2_override_option_save_' . $this->key, array( $this, 'update_override' ), 10, 2 );

		// Hook in all our form opt-in injects, decide to show or not when we are at the display point
		$this->inject_optin_form_hooks();

		// Process our opt-ins
		add_filter( 'preprocess_comment', array( $this, 'process_optin_comment_form' ) );
		add_filter( 'authenticate', array( $this, 'process_optin_login_form' ), 10, 3 );
	}

	/**
	 * Hook in all our form opt-in injects, decide to show or not when we are at the display point
	 *
	 * @since   1.0.0
	 */
	public function inject_optin_form_hooks() {

		// Login form
		add_action( 'login_form', array( $this, 'optin_form_field_login' ) );

		// Comment Form
		add_action( 'comment_form_after_fields', array( $this, 'optin_form_field_comment' ) );

		// Registration form
		add_action( 'register_form', array( $this, 'optin_form_field_registration' ) );
		add_action( 'signup_extra_fields', array( $this, 'optin_form_field_registration' ) );
	}

	/**
	 * Register our setting to WP
	 *
	 * @since 1.0.0
	 */
	public function init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Add menu options page
	 *
	 * @since 1.0.0
	 */
	public function add_options_page() {

		// Only show our settings page if we're connected to CC
		if ( constant_contact()->api->is_connected() ) {

			$this->options_page = add_submenu_page(
				'edit.php?post_type=ctct_forms',
				__( 'Advanced Opt-in', 'constantcontact' ),
				__( 'Advanced Opt-in', 'constantcontact' ),
				'manage_options',
				$this->key,
				array( $this, 'admin_page_display' )
			);

			// Include CMB CSS in the head to avoid FOUC.
			add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
		}
	}

	/**
	 * Admin page markup. Mostly handled by CMB2
	 *
	 * @since 1.0.0
	 */
	public function admin_page_display() {
		?>
		<div class="wrap cmb2-options-page <?php echo esc_attr( $this->key ); ?>">
			<h2><?php esc_attr_e( 'Advanced Opt-in', 'constantcontact' ); ?></h2>
			<?php
			if ( function_exists( 'cmb2_metabox_form' ) ) {
				cmb2_metabox_form( $this->metabox_id, $this->key );
			}

			// add 'ctct-debug-server-check' to query args to display server debug
			$this->plugin->check->maybe_display_debug_info();
			?>
		</div>
		<?php
	}

	/**
	 * Are we on the settings page?
	 *
	 * @since   1.0.0
	 * @return  boolean  if we are on the settings page or not
	 */
	public function on_settings_page() {

		// Get our current page
		global $pagenow;

		// Check if we're on edit.php, and if we're on our options page, cast to bool and return
		return ( 'edit.php' == $pagenow && isset( $_GET['page'] ) && 'ctct_options_settings' == $_GET['page'] );
	}

	/**
	 * Add the options metabox to the array of metaboxes
	 *
	 * @since  1.0.0
	 */
	function add_options_page_metabox() {

		// Hook in our save notices.
		add_action( "cmb2_save_options-page_fields_{$this->metabox_id}", array( $this, 'settings_notices' ), 10, 2 );

		// Only do the settings fields if we're on the options settings page of edit.php
		if ( $this->on_settings_page() ) {

			// Start our new field
			$cmb = new_cmb2_box( array(
				'id'		 => $this->metabox_id,
				'hookup'	 => false,
				'cmb_styles' => false,
				'show_on'	=> array(
					'key'   => 'options-page',
					'value' => array( $this->key ),
				),
			) );

			// Get our lists fields
			$this->do_lists_field( $cmb );
		}
	}

	/**
	 * Helper to show our lists field for settings
	 *
	 * @since   1.0.0
	 * @param object $cmb CMB fields object
	 */
	public function do_lists_field( $cmb ) {

		// Get our lists
		$lists = constant_contact()->builder->get_lists();

		if ( $lists && is_array( $lists ) ) {

			// Set our CMB2 fields.
			$cmb->add_field( array(
				'name' 	=> __( 'Opt In Location', 'constantcontact' ),
				'id'   	=> '_ctct_optin_forms',
				'type'	=> 'multicheck',
				'options' => $this->get_optin_show_options(),
			) );

			// Tack on 'select a list' to our lists array
			$lists[0] = __( 'Select a list', 'constantcontact' );

			$cmb->add_field( array(
				'name' 	=> __( 'Add subscribers to', 'constantcontact' ),
				'id'   	=> '_ctct_optin_list',
				'type'	=> 'select',
				'show_option_none' => false,
				'default'          => __( 'Select a list', 'constantcontact' ),
				'options'		   => $lists,
			) );

			// Get our site name, and if we don't have it, then use a placeholder
			$business_name = get_bloginfo( 'name' );
			$business_name ? $business_name : __( 'Business Name', 'constantcontact' );

			$cmb->add_field( array(
				'name' 	=> __( 'Opt-in Affirmation', 'constantcontact' ),
				'id'   	=> '_ctct_optin_label',
				'type'	=> 'text',
				'default'		  => sprintf( __( 'Yes, I would like to receive emails from %s. Sign me up!', 'constantcontact' ), $business_name ),
			) );
		}
	}

	/**
	 * Get array of options for our 'optin show' settings
	 *
	 * @since   1.0.0
	 * @return  array  array of options
	 */
	public function get_optin_show_options() {

		// Set up our default options
		$optin_options = array(
			'comment_form' => __( 'Add a checkbox to the comment field in your posts', 'constantcontact' ),
			'login_form'   => __( 'Add a checkbox to the main WordPress login page', 'constantcontact' ),
		);

		// If users can register, then allow this option as well
		if ( get_option( 'users_can_register' ) ) {
			$optin_options['reg_form'] = __( 'Add a checkbox to the WordPress user registration page', 'constantcontact' );
		}

		// Send em back
		return $optin_options;
	}

	/**
	 * Based on a type of form we pass in, check if the saved option
	 * for that form is checked or not in the admin
	 *
	 * @since   1.0.0
	 * @param   string  $type  allowed values: 'login_form', 'comment_form', 'reg_form'
	 * @return  boolean        if should show or not
	 */
	public function check_if_optin_should_show( $type ) {

		// Get all our settings
		$available_areas = ctct_get_settings_option( '_ctct_optin_forms' );

		// If our settings aren't an array, bail out
		if ( ! is_array( $available_areas ) ) {
			return false;
		}

		// Otherwise, check to see if our check is in the array
		return in_array( $type, $available_areas );
	}

	/**
	 * Potentially add our opt-in form to the login form. We have three almost
	 * identical functions here. This allows us to hook them all in by themselves
	 * and determine whether or not they should have been hooked in when we get
	 * to displaying them, rather than on potentially pages we dont care about.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function optin_form_field_login() {

		// If we should show it this field, then show it
		if ( $this->check_if_optin_should_show( 'login_form' ) ) {
			$this->optin_form_field();
		}
	}

	/**
	 * Potentially add our opt-in form to comment forms
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function optin_form_field_comment() {

		// If we should show it this field, then show it
		if ( $this->check_if_optin_should_show( 'comment_form' ) ) {
			$this->optin_form_field();
		}
	}

	/**
	 * Potentially add our opt-in form to the registration form
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function optin_form_field_registration() {

		// If we should show it this field, then show it
		if ( $this->check_if_optin_should_show( 'reg_form' ) ) {
			$this->optin_form_field();
		}
	}

	/**
	 * Opt in field checkbox
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function optin_form_field() {

		// Only show this if we're connected
		if ( ! constant_contact()->api->is_connected() ) {
			return;
		}

		// Get our label, based on our settings if they're available
		$saved_label = ctct_get_settings_option( '_ctct_optin_label' );
		$list = ctct_get_settings_option( '_ctct_optin_list' );

		// Otherwise, use our default
		$label = $saved_label ? $saved_label : __( 'Sign up to our newsletter.', 'constantcontact' );

		?><p class="ctct-optin-wrapper" style="padding: 0 0 1em 0;">
	        <label for="ctct_optin">
	        	<input type="checkbox" value="<?php echo esc_attr( $list ); ?>" class="checkbox" id="ctct_optin" name="ctct_optin_list" />
				<?php echo sanitize_text_field( $label ); ?>
			</label>
			<?php echo wp_nonce_field( 'ct_ct_add_to_optin', 'ct_ct_optin' ); ?>
	    </p><?php

	}

	/**
	 * Sends contact to CTCT if optin checked
	 *
	 * @since  1.0.0
	 * @param  array $comment_data comment form data.
	 * @return array comment form data
	 */
	public function process_optin_comment_form( $comment_data ) {

		// Sanity check
		if ( ! isset( $_POST['ctct_optin_list'] ) ) {
			return $comment_data;
		}

		// nonce sanity check
		if ( ! isset( $_POST['ct_ct_optin'] ) ) {
			return $comment_data;
		}

		// Check our nonce
		if ( ! wp_verify_nonce( $_POST['ct_ct_optin'], 'ct_ct_add_to_optin' ) ) {
			return $comment_data;
		}

		// Send our data to be processed, send back original comment data
		return $this->_process_comment_data_for_optin( $comment_data );
	}

	/**
	 * Process our comment data and send to CC
	 *
	 * @since   1.0.0
	 * @param   array  $comment_data  array of comment data
	 * @return  array                 passed in comment data
	 */
	public function _process_comment_data_for_optin( $comment_data ) {

		// finally, if we have at least an email, then add it to the api
		if ( isset( $comment_data['comment_author_email'] ) && $comment_data['comment_author_email'] ) {

			// If we can grab a name, try to use it
			$name = isset( $comment_data['comment_author'] ) ? $comment_data['comment_author'] : '';

			// If we can get a website, use it
			$website = isset( $comment_data['comment_author_url'] ) ? $comment_data['comment_author_url'] : '';

			// Build up our data array
			$args = array(
				'list'       => sanitize_text_field( wp_unslash( $_POST['ctct_optin_list'] ) ),
				'email'      => sanitize_email( $comment_data['comment_author_email'] ),
				'first_name' => sanitize_text_field( $name ),
				'last_name'  => '',
				'website'    => sanitize_text_field( $website ),
			);

			// Add the contact, based on our whitelist of information we have from above
			constantcontact_api()->add_contact( $args );
		}

		// Send back original comment data
		return $comment_data;
	}

	/**
	 * Sends contact to CTCT if optin checked
	 *
	 * @since  1.0.0
	 * @param  array  $user
	 * @param  string $username login name.
	 * @param  string $password user password.
	 * @return object  CTCT return API for contact
	 */
	public function process_optin_login_form( $user, $username, $password ) {

		// Sanity check
		if ( ! isset( $_POST['ctct_optin_list'] ) ) {
			return $user;
		}

		// nonce sanity check
		if ( ! isset( $_POST['ct_ct_optin'] ) ) {
			return $user;
		}

		// Check our nonce
		if ( ! wp_verify_nonce( $_POST['ct_ct_optin'], 'ct_ct_add_to_optin' ) ) {
			return $user;
		}

		// Check username
		if ( empty( $username ) ) {
			return $user;
		}

		// Send data to CC and send back our passed in user object
		return $this->_process_user_data_for_optin( $user, $username );
	}

	/**
	 * Sends user data to CC
	 *
	 * @since   1.0.0
	 * @param   object  $user      WP user object
	 * @param   string  $username  username
	 * @return  object             passed in $user object
	 */
	public function _process_user_data_for_optin( $user, $username ) {

		// Get user
		$user_data = get_user_by( 'login', $username );

		// Get email
		if ( $user_data && isset( $user_data->data ) && isset( $user_data->data->user_email ) ) {
			$email = sanitize_email( $user_data->data->user_email );
		} else {
			$email = '';
		}

		// Get name
		if ( $user_data && isset( $user_data->data ) && isset( $user_data->data->display_name ) ) {
			$name = sanitize_text_field( $user_data->data->display_name );
		} else {
			$name = '';
		}

		// If we have one or the other, try it
		if ( $email ) {
			$args = array(
				'email'      => $email,
				'list'       => sanitize_text_field( wp_unslash( $_POST['ctct_optin_list'] ) ),
				'first_name' => $name,
				'last_name'  => '',
			);

			// Add the contact!
			constantcontact_api()->add_contact( $args );
		}

		// Send back our passed in user object
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

		// Sanity checking
		if ( $object_id !== $this->key || empty( $updated ) ) {
			return;
		}

		// Output any errors / notices we need
		add_settings_error( $this->key . '-notices', '', __( 'Settings updated.', 'constantcontact' ), 'updated' );
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
			if ( isset( $this->{$field} ) ) {
				return $this->{$field};
			} else {
				return null;
			}
		}

		throw new Exception( 'Invalid property: ' . $field );
	}
}

/**
 * Wrapper function around cmb2_get_option
 *
 * @since  1.0.0
 * @param  string $key Options array key.
 * @return mixed Option value
 */
function ctct_get_settings_option( $key = '' ) {
	return cmb2_get_option( constant_contact()->settings->key, $key );
}

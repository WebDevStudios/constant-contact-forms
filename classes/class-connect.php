
<?php

use Ctct\Auth\CtctOAuth2;
use Ctct\Exceptions\OAuth2Exception;

/**
 * CMB2 Network Settings
 * @version 0.1.0
 */
class ConstantContact_Connect {

	/**
 	 * Option key, and option page slug
 	 * @var string
 	 */
	private $key = 'ctct_options_connect';

	/**
 	 * Settings page metabox id
 	 * @var string
 	 */
	private $metabox_id = 'ctct_option_metabox_connect';

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

	private $oauth = '';

	/**
	 * Holds an instance of the project
	 *
	 * @Myprefix_Network_Admin
	 **/
	private static $instance = null;

	/**
	 * Constructor
	 * @since 0.1.0
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
	 * @since 0.1.0
	 */
	public function hooks() {

		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'cmb2_admin_init', array( $this, 'add_options_page_metabox' ) );

		// Override CMB's getter
		add_filter( 'cmb2_override_option_get_'. $this->key, array( $this, 'get_override' ), 10, 2 );
		// Override CMB's setter
		add_filter( 'cmb2_override_option_save_'. $this->key, array( $this, 'update_override' ), 10, 2 );
	}

	/**
	 * Register our setting to WP
	 * @since  0.1.0
	 */
	public function init() {
		register_setting( $this->key, $this->key );

		// instantiate the CtctOAuth2 class
		$this->oauth = new CtctOAuth2( '595r3d4q432c3mdv2jtd3nj9', 'XJ9H8n5m8fqt2WBpSk6E6dJm', 'http://wdsplugins.dev/constantcontact');

		//error_log( print_r($this->oauth, true) );
	}

	/**
	 * Add menu options page
	 * @since 0.1.0
	 */
	public function add_options_page() {

		$this->options_page = add_submenu_page(
			'edit.php?post_type=ctct_forms',
			__( 'Connect', constant_contact()->text_domain ),
			__( 'Connect', constant_contact()->text_domain ),
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
	 * @since  0.1.0
	 */
	public function admin_page_display() {
		?>
		<style>
		.wp-core-ui .button-primary {
			display: block;
			margin: 20px 0;
		}

		</style>
		<script>
			jQuery.noConflict();
			(function($) {
				$(document).ready(function() {
					$( '#ctct_option_metabox_connect' ).submit( function(e) {
						e.preventDefault();
						window.location.href = '<?php echo $this->oauth->getAuthorizationUrl(); ?>';
					});
				});
			})(jQuery);
		</script>
		<div class="wrap cmb2-options-page <?php echo esc_attr( $this->key ); ?>">
			<h2><?php esc_attr_e( constant_contact()->plugin_name . ' Connect', constant_contact()->text_domain ); ?></h2>

            <?php if ( constantcontact_get_api_token() ) : ?>
                <div class="message notice">
        			<p><?php esc_attr_e( 'Account connected to Constant Contact', constant_contact()->text_domain ); ?></p>
                </div>
                <input type="button" class="button-primary" value="Disconnect">

            <?php else : ?>
                <?php cmb2_metabox_form( $this->metabox_id, $this->key, array(
					'save_button' => __( 'Get Access Token', constant_contact()->text_domain ),
				) ); ?>
				<p>
					* Getting an access token requires a Constant Contact account. When you click Get Access Token, you are taken to a Constant Contact account sign up page. Create a new account, or if you have an existing Constant Contact account (NOT your Mashery developer account), sign in.
					Click Grant Access to generate an access token. Copy it and keep it handy.
				</p>
            <?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Add the options metabox to the array of metaboxes
	 * @since  0.1.0
	 */
	function add_options_page_metabox() {

        $prefix = 'ctct_connect';

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

		// API fields.
		$cmb->add_field( array(
			'name' => __( '1. Sign Up to Constant Contact', constant_contact()->text_domain ),
			'desc' => __( 'Create an account at <a target="_blank" href="https://constantcontact.com/"> Constant Contact.</a>', constant_contact()->text_domain ),
			'id'   => $prefix . 'ctct_signup',
			'type' => 'title',
		) );

		$cmb->add_field( array(
			'name' => __( '2. Sign Up to Mashery', constant_contact()->text_domain ),
			'desc' => __( 'Create an account or login at <a target="_blank" href="https://constantcontact.mashery.com/member/register/"> Mashery.</a> Check your inbox after registering to confirm.', constant_contact()->text_domain ),
			'id'   => $prefix . 'mashery_signup',
			'type' => 'title',
		) );

		$cmb->add_field( array(
			'name' => __( '3. Register Application', constant_contact()->text_domain ),
			'desc' => __( 'An API Key will be assigned to your application. <a target="_blank" href="https://constantcontact.mashery.com/apps/register/"> Register Application.</a> Get redirect url below. This must match exactly.', constant_contact()->text_domain ),
			'id'   => $prefix . 'app_register',
			'type' => 'title',
		) );

		$cmb->add_field( array(
			'name' => __( '4. Get API Keys', constant_contact()->text_domain ),
			'desc' => __( 'Get your API Keys from <a target="_blank" href="https://constantcontact.mashery.com/apps/mykeys"> Get API Keys.</a>', constant_contact()->text_domain ),
			'id'   => $prefix . 'app_token',
			'type' => 'title',
		) );

		$cmb->add_field( array(
			'name' => __( '5. Connect to Constant Contact', constant_contact()->text_domain ),
			'desc' => __( 'Fill your API Key and Secret Key and click Get Access Token button. You will be sent to Constant Contact to login and authorize this plugin to access your account.', constant_contact()->text_domain ),
			'id'   => $prefix . 'connect',
			'type' => 'title',
		) );

		$cmb->add_field( array(
			'name' => __( 'API KEY', constant_contact()->text_domain ),
			'desc' => __( 'Enter Constant Contact API Key', constant_contact()->text_domain ),
			'id'   => $prefix . 'api_key',
			'type' => 'text',
		) );

		$cmb->add_field( array(
			'name' => __( 'SECRET KEY', constant_contact()->text_domain ),
			'desc' => __( 'Enter Constant Contact Secret Key', constant_contact()->text_domain ),
			'id'   => $prefix . 'secret_key',
			'type' => 'text',
		) );

		$cmb->add_field( array(
			'name' => __( 'Redirect URL', constant_contact()->text_domain ),
			'desc' => __( get_site_url() . '/constantcontact/', constant_contact()->text_domain ),
			'id'   => $prefix . 'redirect_url',
			'type' => 'title',
		) );

	}

	/**
	 * Register settings notices for display
	 *
	 * @since  0.1.0
	 * @param  int   $object_id Option key.
	 * @param  array $updated   Array of updated fields.
	 * @return void
	 */
	public function settings_notices( $object_id, $updated ) {
		if ( $object_id !== $this->key || empty( $updated ) ) {
			return;
		}

		add_settings_error( $this->key . '-notices', '', __( 'Settings updated.', 'myprefix' ), 'updated' );
		settings_errors( $this->key . '-notices' );
	}

	/**
	 * Replaces get_option with get_site_option
	 *
	 * @since  0.1.0
	 */
	public function get_override( $test, $default = false ) {
		return get_site_option( $this->key, $default );
	}

	/**
	 * Replaces update_option with update_site_option
	 *
	 * @since  0.1.0
	 */
	public function update_override( $test, $option_value ) {
		return update_site_option( $this->key, $option_value );
	}

	/**
	 * Public getter method for retrieving protected/private variables
	 *
	 * @since  0.1.0
	 * @param  string $field Field to retrieve.
	 * @return mixed Field value or exception is thrown
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve.
		if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}

		throw new Exception( 'Invalid property: ' . $field );
	}

}

/**
 * Helper function to get/return the Myprefix_Network_Admin object
 *
 * @since  0.1.0
 * @return ConstantContact_Connect object
 */
function ctct_connect_admin() {
	return ConstantContact_Connect::get_instance();
}

/**
 * Wrapper function around cmb2_get_option
 *
 * @since  0.1.0
 * @param  string $key Options array key.
 * @return mixed Option value
 */
function ctct_get_connect_option( $key = '' ) {
	return cmb2_get_option( myprefix_admin()->key, $key );
}

// Get it started.
ctct_connect_admin();

/**
 * Returns api token string to access api
 *
 * @return string api token
 */
function constantcontact_get_api_token() {
    return false;
}

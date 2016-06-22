<?php
/**
 * ConstantContact_Connect
 *
 * @package ConstantContactConnect
 * @author Pluginize
 * @since 1.0.0
 */

use Ctct\ConstantContact;
use Ctct\Auth\CtctOAuth2;
use Ctct\Exceptions\OAuth2Exception;
use Defuse\Crypto\Key;

/**
 * Class ConstantContact_Connect
 */
class ConstantContact_Connect {

	/**
	 * Option key, and option page slug
	 *
	 * @var string
	 */
	private $key = 'ctct_options_connect';

	/**
	 * CtctOAuth2 object
	 *
	 * @var object
	 */
	private $oauth = '';

	/**
	 * Api Error message
	 *
	 * @var string
	 */
	public $error_message = '';

	/**
	 * Current page redirect Url
	 *
	 * @var string
	 */
	private $redirect_url = '';

	/**
	 * Holds an instance of the project
	 *
	 * @var object
	 */
	private static $instance = null;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
	}

	/**
	 * Get the running object
	 *
	 * @return ConstantContact_Connect
	 **/
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->hooks();
		}
		return self::$instance;
	}

	/**
	 * Initiate our hooks
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
	}

	/**
	 * Register our setting to WP
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function init() {

		$path = add_query_arg( array(
			'post_type' => 'ctct_forms',
			'page' => 'ctct_options_connect',
		), admin_url( 'edit.php' ) );
		$this->redirect_url = $path;

		// Instantiate the CtctOAuth2 class.
		$this->oauth = new CtctOAuth2( constantcontact_api()->get_api_token( 'CTCT_APIKEY' ), constantcontact_api()->get_api_token( 'CTCT_SECRETKEY' ), get_site_url() . '/?auth=ctct' );
		$this->disconnect();

	}

	/**
	 * Add menu options page
	 *
	 * @since 1.0.0
	 */
	public function add_options_page() {

		$this->options_page = add_submenu_page(
			'edit.php?post_type=ctct_forms',
			__( 'Connect', 'constantcontact' ),
			__( 'Connect', 'constantcontact' ),
			'manage_options',
			$this->key,
			array( $this, 'admin_page_display' )
		);

	}

	/**
	 * Admin page markup. Mostly handled by CMB2
	 *
	 * @since  1.0.0
	 * @return mixed page markup or false if not admin.
	 */
	public function admin_page_display() {

		// Only run if logged in user can manage site options.
		if ( ! current_user_can( 'manage_options' ) ) { return false; }

		$access_token = false;

		// If the 'code' query parameter is present in the uri, the code can exchanged for an access token.
		if ( isset( $_GET['code'] ) && is_admin() ) {
			try {
				$response = $this->oauth->getAccessToken( sanitize_text_field( wp_unslash( $_GET['code'] ) ) );
				$access_token = $response['access_token'];
			} catch ( OAuth2Exception $ex ) {
				foreach ( $ex->getErrors() as $error ) {
					$this->error_message = $this->api_error_message( $error );
				}
				if ( ! isset( $access_token ) ) {
					$access_token = null;
				}
			}
		}

		// Save auth token to options.
		if ( $access_token ) {
			$this->secure_token( sanitize_text_field( $access_token ) );
		}

		?>
		<style>
		.wp-core-ui .button-primary {
			margin: 20px 0;
		}
		.ctct-logo {
			max-width: 400px;
		}
		.ctct-description {
			max-width: 800px;
		}
		</style>

		<script>
			jQuery.noConflict();
			(function($) {
				$(document).ready(function() {
					$( '.ctct-disconnect' ).on( 'click', function(e) {
						var disconnect = confirm('<? esc_html_e( 'Are you sure you want to disconnect?', 'constantcontact' ); ?>');
						if (disconnect) {
							window.location.href = '<?php echo esc_url( $this->redirect_url . '&ctct-disconnect=true' ); ?>';
						}
					});
				});
			})(jQuery);
		</script>

		<div class="wrap cmb2-options-page <?php echo esc_attr( $this->key ); ?>">

			<img class="ctct-logo" src="<?php echo esc_url( constant_contact()->url . 'assets/images/constant-contact-logo.png' ); ?>">

			<?php echo esc_html( constantcontact_connect_error_message() ); ?>

			<?php if ( $token = constantcontact_api()->get_api_token() ) : ?>

				<div class="message notice">
					<p>
						<?php esc_html_e( 'Account connected to Constant Contact.', 'constantcontact' ); ?>
					</p>
				</div>
				<input type="button" class="button-primary ctct-disconnect" value="Disconnect">

			<?php else : ?>
				<p class="ctct-description">
					<?php esc_html_e( 'Click the connect button and login or sign up to Constant Contact. By connecting, you authorize this plugin to access your account on Constant Contact.', 'constantcontact' ); ?>
				</p>
				<a href="<?php echo esc_url_raw( $this->oauth->getAuthorizationUrl() . '&oauthSignup=true' ); ?>" class="button-primary ctct-connect"><?php esc_html_e( 'Connect to Constant Contact', 'constantcontact' ); ?></a>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Disconnect from api
	 *
	 * @since  1.0.0
	 * @return boolean
	 */
	private function disconnect() {

		// Only run if logged in user can manage site options.
		if ( ! current_user_can( 'manage_options' ) ) { return false; }

		if ( isset( $_GET['ctct-disconnect'] ) && is_admin() ) {

			// Delete access token.
			delete_option( '_ctct_token' );
			delete_option( '_ctct_access_salt' );

			// Create a redirect back to connect page.
			wp_safe_redirect( remove_query_arg( array( 'ctct-disconnect', 'code', 'auth', 'username' ), $this->redirect_url ) );
			exit;
		}

	}

	/**
	 * Secure API access token
	 *
	 * @since  1.0.0
	 * @param string $access_token api access token.
	 * @return void
	 */
	private function secure_token( $access_token ) {
		update_option( '_ctct_token', $access_token );

	}


	/**
	 * Process api error response
	 *
	 * @since  1.0.0
	 * @param  array $error api error repsonse.
	 * @return mixed Error message or false if no error.
	 */
	private function api_error_message( $error ) {

		switch ( $error->error_key ) {
			case 'http.status.authentication.invalid_token':
				return __( 'Your API access token is invalid. Reconnect to Constant Contact to receive a new token.', 'constantcontact' );
			break;
			default:
			 return false;
			break;

		}

	}
}

/**
 * Helper function to get/return the ConstantContact_Connect object
 *
 * @since  1.0.0
 * @return ConstantContact_Connect object
 */
function ctct_connect_admin() {
	return ConstantContact_Connect::get_instance();
}

// Get it started.
ctct_connect_admin();

/**
 * If error then displays a notice
 *
 * @return string
 */
function constantcontact_connect_error_message() {
	if ( $message = ctct_connect_admin()->error_message ) {
		$notice = '<div class="message error notice"><p>';
		$notice .= ctct_connect_admin()->error_message;
		$notice .= '</p></div>';

		return $notice;
	}
}

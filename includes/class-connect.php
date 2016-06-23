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

		$this->redirect_url = add_query_arg(
			array(
				'post_type' => 'ctct_forms',
				'page'      => 'ctct_options_connect',
			),
			admin_url( 'edit.php' )
		);

		// Instantiate the CtctOAuth2 class.
		$oath_connect = new CtctOAuth2(
			constant_contact()->api->get_api_token( 'CTCT_APIKEY' ),
			constant_contact()->api->get_api_token( 'CTCT_SECRETKEY' ),
			add_query_arg( array( 'auth' => 'ctct' ), get_site_url() )
		);

		// Make sure that the connect worked before setting as class prop
		if ( $oath_connect ) {
			$this->oauth = $oath_connect;
			$this->disconnect();
		}
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
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$response = false;

		// If the 'code' query parameter is present in the uri, the code can exchanged for an access token.
		if ( isset( $_GET['code'] ) && is_admin() ) {
			try {

				$response = $this->oauth->getAccessToken( sanitize_text_field( wp_unslash( $_GET['code'] ) ) );
			} catch ( OAuth2Exception $ex ) {
				constant_contact()->api->log_errors( $ex->getErrors() );
			}
		}

		// Save auth token to options.
		if ( $response && isset( $response['access_token'] ) && $response['access_token'] ) {
			$this->update_token( sanitize_text_field( $response['access_token'] ) );
		}

		wp_enqueue_style( 'constant-contact-oath', constant_contact()->url() . 'assets/css/oath.css' );
		?>

		<script>
			jQuery.noConflict();
			(function($) {
				$(document).ready(function() {
					$( '.ctct-disconnect' ).on( 'click', function(e) {
						var disconnect = confirm('<?php esc_html_e( 'Are you sure you want to disconnect?', 'constantcontact' ); ?>');
						if (disconnect) {
							<?php // @TODO replace this so there is also a nonce ?>
							window.location.href = '<?php echo esc_url( add_query_arg( array( 'ctct-disconnect' => 'true' ), $this->redirect_url ) ); ?>';
						}
					});
				});
			})(jQuery);
		</script>

		<div class="wrap cmb2-options-page <?php echo esc_attr( $this->key ); ?>">
			<img class="ctct-logo" src="<?php echo esc_url( constant_contact()->url . 'assets/images/constant-contact-logo.png' ); ?>">
			<?php if ( constantcontact_api()->get_api_token() ) : ?>
				<div class="message notice">
					<p>
						<?php esc_html_e( 'Account connected to Constant Contact.', 'constantcontact' ); ?>
					</p>
				</div>
				<input type="button" class="button-primary ctct-disconnect" value="<?php esc_html_e( 'Disconnect', 'constantcontact' ); ?>">
			<?php else : ?>
				<p class="ctct-description">
					<?php esc_html_e( 'Click the connect button and login or sign up to Constant Contact. By connecting, you authorize this plugin to access your account on Constant Contact.', 'constantcontact' ); ?>
				</p>
				<a href="<?php echo esc_url( add_query_arg( array( 'oauthSignup' => 'true' ), $this->oauth->getAuthorizationUrl() ) ); ?>" class="button-primary ctct-connect">
					<?php esc_html_e( 'Connect to Constant Contact', 'constantcontact' ); ?>
				</a>
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
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

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
	private function update_token( $access_token ) {
		update_option( '_ctct_token', $access_token );

	}
}

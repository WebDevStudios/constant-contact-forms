<?php
/**
 * Connect
 *
 * @package ConstantContact
 * @subpackage Connect
 * @author Constant Contact
 * @since 1.0.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

use Ctct\ConstantContact;
use Ctct\Auth\CtctOAuth2;
use Ctct\Exceptions\OAuth2Exception;
use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;

/**
 * Powers our admin connect page, as well as misc functionality around connecting to Constant Contact.
 *
 * @since 1.0.0
 */
class ConstantContact_Connect {

	/**
	 * Option key, and option page slug.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $key = 'ctct_options_connect';

	/**
	 * Api Error message.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $error_message = '';

	/**
	 * Current page redirect Url.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $redirect_url = '';

	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	protected $plugin;

	/**
	 * Whether or not to encrypt.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	public $should_encrypt = false;

	/**
	 * Options page.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $options_page = '';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $plugin Parent class.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'init', [ $this, 'maybe_connect' ] );
		add_action( 'plugins_loaded', [ $this, 'maybe_disconnect' ] );
		add_action( 'admin_menu', [ $this, 'add_options_page' ] );
	}

	/**
	 * Watches for our specific $_GET paramaters and if we get a connect request,
	 * pass it to our auth server class to process.
	 *
	 * @since 1.0.0
	 */
	public function maybe_connect() {

		// phpcs:disable WordPress.Security.NonceVerification -- OK direct-accessing of $_GET.
		if ( isset( $_GET['cc_connect_attempt'] ) && is_user_logged_in() ) {

			$verified = constant_contact()->authserver->verify_and_save_access_token_return();

			$redirect_args = [
				'post_type' => 'ctct_forms',
				'page'      => 'ctct_options_connect',
			];

			if ( ! $verified ) {
				$redirect_args['ctct_connect_error'] = 'true';
			}

			wp_safe_redirect( add_query_arg( $redirect_args, admin_url( 'edit.php' ) ) );
			die;
		}
		// phpcs:enable WordPress.Security.NonceVerification
	}

	/**
	 * Add menu options page.
	 *
	 * @since 1.0.0
	 */
	public function add_options_page() {

		$connect_title = esc_html__( 'Disconnect', 'constant-contact-forms' );
		$connect_link  = 'edit.php?post_type=ctct_forms';

		if ( ! constant_contact()->api->is_connected() ) {
			$connect_title = esc_html__( 'Connect Now', 'constant-contact-forms' );
		}

		$this->options_page = add_submenu_page(
			$connect_link,
			$connect_title,
			$connect_title,
			'manage_options',
			$this->key,
			[ $this, 'admin_page_display' ]
		);
	}

	/**
	 * Admin page markup. Mostly handled by CMB2.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed page markup or false if not admin.
	 */
	public function admin_page_display() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		wp_enqueue_style( 'constant-contact-forms-admin' );

		wp_localize_script( 'ctct_form', 'ctctTexts', [ 'disconnectconfirm' => __( 'Are you sure you want to disconnect?', 'constant-contact-forms' ) ] );

		wp_enqueue_script( 'ctct_form' );
		?>
		<div class="wrap <?php echo esc_attr( $this->key ); ?>">

			<?php if ( constantcontact_api()->get_api_token() ) : ?>

			<div class="ctct-connected">
				<div class="ctct-connected-wrap">
					<h2><?php esc_html_e( 'Account Connected!', 'constant-contact-forms' ); ?></h2>
					<p class="ctct-description">
						<?php esc_html_e( 'You are connected to your Constant Contact account.', 'constant-contact-forms' ); ?>
					</p>
					<div class="ctct-connection-details">
						<p class="ctct-label">
							<strong><?php esc_html_e( 'Account Name:', 'constant-contact-forms' ); ?></strong>
						</p>
						<p>
							<?php
							$token = constant_contact()->api->get_api_token();
							try {
								$account = constant_contact()->api->cc()->accountService->getAccountInfo( $token );
								if ( $account ) {
									echo esc_html( $account->first_name . ' ' . $account->last_name );
								}
							} catch ( CtctException $ex ) {
								esc_html_e( 'There was an issue with retrieving connected account information. Please try again.', 'constant-contact-forms' );
							}
							?>
						</p>
					</div>
					<div class="ctct-connection-details">
						<p class="ctct-label">
							<strong><?php esc_html_e( 'Email Address:', 'constant-contact-forms' ); ?></strong>
						</p>
						<p>
							<?php
							if ( $account ) {
								echo '<a href="mailto:' . esc_html( $account->email ) . '">' . esc_html( $account->email ) . '</a>';
							}
							?>
						</p>
					</div>
					<div class="ctct-connection-details">
						<p class="ctct-label">
							<strong><?php esc_html_e( 'Status:', 'constant-contact-forms' ); ?></strong>
						</p>
						<form method="post" action="<?php echo esc_url( $this->redirect_url ); ?>">
							<?php wp_nonce_field( 'ctct-admin-disconnect', 'ctct-admin-disconnect' ); ?>
							<input type="hidden" id="ctct-disconnect" name="ctct-disconnect" value="true">
							<input type="submit" class="button button-primary ctct-disconnect" value="<?php esc_html_e( 'Disconnect', 'constant-contact-forms' ); ?>">
						</form>
					</div>
				</div>

				<?php  //if ( ! constant_contact_has_forms() ) : ?>
					
				<hr />
				
				<?php // phpcs:disable WordPress.WP.EnqueuedResources -- Ok use of inline scripts. ?>
				
				<div class="ctct-connected-next-step">
					<div class="ctct-video">
						<script src="https://fast.wistia.com/embed/medias/xix7jf8p55.jsonp" async></script>
						<script src="https://fast.wistia.com/assets/external/E-v1.js" async></script>
						<div class="wistia_embed wistia_async_xix7jf8p55 seo=false" style="height:225px;width:400px;margin:0 auto;">&nbsp;</div>
					</div>

					<div>
						<h3><?php esc_html_e( 'Getting Started', 'constant-contact-forms' ); ?></h3>
						<p><?php esc_html_e( 'Your account is connected to Constant Contact and you\'re ready to start collecting visitor information.', 'constant-contact-forms' ); ?></p>
						<a href="wp-admin/post-new.php?post_type=ctct_forms" class="button"><?php esc_html_e( 'Add Contact Form', 'constant-contact-forms' ); ?></a>
					</div>
				</div>
				<?php // phpcs:enable WordPress.WP.EnqueuedResources ?>

				<div class="ctct-connected-opt-in">
					<div>
						<h3><?php esc_html_e( 'Please Help to Improve this Plugin', 'constant-contact-forms' ); ?></h3>
						<p>
							<?php
								printf(
									/* Translators: Placeholder will hold link to Constant Contact privacy statement. */
									esc_html__( 'Allow Constant Contact to use Google Analytics&trade; to track your usage across the Constant Contact Forms plugin. You can opt-out within the Settings page. See our %1$s.', 'constant-contact-forms' ),
									sprintf(
										'<a href="https://www.constantcontact.com/legal/privacy-statement">%1$s</a>',
										esc_html__( 'Privacy Statement', 'constant-contact-forms' )
									)
								);
							?>
						</p>
					</div>
					<div id="ctct-connect-ga-optin" class="ctct-connect-ga-optin">
						<a class="button button-primary ctct-connect" data-allow="on"><?php esc_html_e( 'Allow', 'constant-contact-forms' ); ?></a>
						<a class="button" data-allow="off"><?php esc_html_e( 'Dismiss', 'constant-contact-forms' ); ?></a>
					</div>
				</div>

				<?php // endif; ?>
			</div>

		<?php else : ?>

		<?php
			// phpcs:disable WordPress.Security.NonceVerification -- OK direct-accessing of $_GET.
			if ( isset( $_GET['ctct_connect_error'] ) ) :
		?>
			<div id="message" class="ctct-error"><p>
			<?php esc_html_e( 'There was an error connecting your account. Please try again.', 'constant-contact-forms' ); ?>
			</p></div>
		<?php
			endif;
			// phpcs:enable WordPress.Security.NonceVerification
		?>
		
			<h2><?php esc_html_e( 'Connect Now', 'constant-contact-forms' ); ?></h2>
			<p class="ctct-description">
				<?php esc_html_e( "Get the most out of this plugin &mdash; use it with an active Constant Contact account. By connecting to an account, you'll be able to engage visitors through email marketing and turn more of them into customers.", 'constant-contact-forms' ); ?>
			</p>

			<div class="ctct-call-to-actions">
				<div class="ctct-call-to-actions--item connect-account">
					<div>
						<h3><?php esc_html_e( 'Connect to Constant Contact', 'constant-contact-forms' ); ?></h3>
						<p><?php esc_html_e( 'By connecting to Constant Contact, you authorize this plugin to access your account.', 'constant-contact-forms' ); ?></p>
					</div>
					<?php

					$proof     = constant_contact()->authserver->set_verification_option();
					$auth_link = constant_contact()->authserver->do_connect_url( $proof );
					$auth_link = add_query_arg( [ 'rmc' => 'wp_connect_connect' ], $auth_link );

					if ( $auth_link ) :
					?>
						<a href="<?php echo esc_url_raw( $auth_link ); ?>" class="button ctct-button button-blue ctct-connect">
							<?php esc_html_e( 'Connect Plugin', 'constant-contact-forms' ); ?>
						</a>
					<?php endif; ?>
				</div>

				<div class="ctct-call-to-actions--item no-account">
					<div>
						<h3><?php esc_html_e( 'No Constant Contact Account?', 'constant-contact-forms' ); ?></h3>
						<p><?php esc_html_e( 'Create professional emails to nurture relationships with contacts even after they leave your website. Sign up for a free 60-day trial.', 'constant-contact-forms' ); ?></p>
					</div>
					<a class="button ctct-button button-orange" href="<?php echo esc_url_raw( add_query_arg( [ 'rmc' => 'wp_connect_try' ], constant_contact()->api->get_signup_link() ) ); ?>"><?php esc_attr_e( 'Try Us Free', 'constant-contact-forms' ); ?></a>
				</div>
			</div>

			<p class="small">
				<strong><?php esc_html_e( 'NOTE: ', 'constant-contact-forms' ); ?></strong><?php esc_html_e( "It's possible to use the plugin without connecting to a Constant Contact account. In this case, all information collected by the forms will be individually emailed to the Site Admin.", 'constant-contact-forms' ); ?>
			</p>
		<?php endif; ?>
		<?php
		return true;
	}

	/**
	 * Disconnect from API.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception Throw Exception if encountered during disconnection.
	 *
	 * @return boolean
	 */
	public function maybe_disconnect() {

		if ( ! isset( $_POST['ctct-admin-disconnect'] ) ) {
			return false;
		}

		if ( ! isset( $_POST['ctct-disconnect'] ) ) {
			return false;
		}

		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ctct-admin-disconnect'] ) ), 'ctct-admin-disconnect' ) ) {

			delete_option( 'ctct_token' );
			delete_option( '_ctct_token' );

			$saved_options = get_option( 'ctct_options_settings' );
			if ( isset( $saved_options['_ctct_disable_email_notifications'] ) ) {
				unset( $saved_options['_ctct_disable_email_notifications'] );
				update_option( 'ctct_options_settings', $saved_options );
			}
		} else {
			constant_contact_maybe_log_it( 'Nonces', 'Account disconnection nonce failed to verify.' );
		}
		return true;
	}

	/**
	 * Get an encrypted value.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception Throws Exception if encountered while attempting to retrieve encrypted value.
	 *
	 * @param string  $check_key key to save to.
	 * @param boolean $fallback_to_ctct_opt Fall back maybe.
	 * @return boolean|string
	 */
	public function e_get( $check_key, $fallback_to_ctct_opt = false ) {

		if ( ! $this->is_encryption_ready() ) {
			return get_option( $check_key, '' );
		}

		$key = $this->get_encrpyt_key();

		if ( $fallback_to_ctct_opt ) {
			$options = get_option( 'ctct_options_settings', false );
			if ( $options && isset( $options[ $check_key ] ) ) {
				$encrypted_token = $options[ $check_key ];
			} else {
				return false;
			}
		} else {
			$encrypted_token = get_option( $check_key );
			if ( ! $encrypted_token ) {
				return false;
			}
		}

		try {
			$return = Crypto::decrypt( $encrypted_token, $key );
		} catch ( Exception $e ) {
			$return = '';
		}

		return $return;

	}

	/**
	 * Set an encrypted value.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception Exception.
	 *
	 * @param string  $check_key Key to save to.
	 * @param string  $data      Data to save.
	 * @param boolean $autoload  Autoload it.
	 * @return string
	 */
	public function e_set( $check_key, $data, $autoload = false ) {

		if ( ! $this->is_encryption_ready() ) {
			update_option( $check_key, $data );
			return $data;
		}

		$key = $this->get_encrpyt_key();

		$saved = Crypto::encrypt( $data, $key );

		update_option( $check_key, $saved, $autoload );

		return $saved;
	}

	/**
	 * Secure API access token.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception Exception.
	 *
	 * @param string $access_token API access token.
	 * @return string
	 */
	public function update_token( $access_token ) {
		return $this->e_set( 'ctct_token', $access_token, true );
	}

	/**
	 * Get saved API token.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception Throws Exception if encountered while attempting to save API token.
	 *
	 * @return string Token.
	 */
	public function get_api_token() {
		$this->check_deleted_legacy_token();

		return $this->e_get( 'ctct_token' );
	}

	/**
	 * If we have a legacy token, let's re-save it.
	 *
	 * @since 1.0.0
	 */
	public function check_deleted_legacy_token() {
		$legacy = get_option( '_ctct_token' );

		if ( $legacy ) {
			$this->update_token( $legacy );
			delete_option( '_ctct_token' );
		}
	}

	/**
	 * Get our encrypt key.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception Exception.
	 *
	 * @return string Key to use for encrypt.
	 */
	public function get_encrpyt_key() {

		if ( ! $this->is_encryption_ready() ) {
			return 'ctct_key';
		}

		$key = get_option( 'ctct_key', false );

		if ( ! $key ) {
			$key = $this->generate_and_save_key();
		}

		return Key::loadFromAsciiSafeString( $key );
	}

	/**
	 * Generates and saves a new key.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception Exception.
	 *
	 * @param boolean $first_try If first try or not.
	 * @return string|object Key.
	 */
	public function generate_and_save_key( $first_try = true ) {

		if ( ! $this->is_encryption_ready() ) {
			return 'ctct_key';
		}

		$key     = Key::createNewRandomKey();
		$key     = $key->saveToAsciiSafeString();
		$updated = update_option( 'ctct_key', $key );

		if ( ! $updated || $first_try ) {
			$key = $this->generate_and_save_key( false );
		}

		return $key;
	}

	/**
	 * Checks to see if the server will support encryption functionality.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean If we should load/use the encryption libraries.
	 */
	public function is_encryption_ready() {

		if ( ! function_exists( 'openssl_encrypt' ) || ! function_exists( 'openssl_decrypt' ) ) {
			return false;
		}

		if ( ! $this->check_crypto_class() ) {
			return false;
		}
		return true;
	}

	/**
	 * Helper method to check our crypto clases.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean If we can encrpyt or not.
	 */
	public function check_crypto_class() {

		try {
			$return = false;
			Constant_Contact::get_instance()->load_libs();

			if ( class_exists( 'Defuse\Crypto\RuntimeTests' ) ) {

				// If we have our Crpyto class, we'll run the included
				// runtime tests and see if we get the correct response.
				$tests  = new Defuse\Crypto\RuntimeTests();
				$tests  = $tests->runtimeTest();
				$return = true;
			}
		} catch ( Exception $exception ) {
			if ( $exception ) {
				$return = false;
			}
		}

		return $return;
	}
}

<?php
/**
 * Connect
 *
 * @package ConstantContact
 * @subpackage Connect
 * @author Constant Contact
 * @since 1.0.0
 */

use Ctct\ConstantContact;
use Ctct\Auth\CtctOAuth2;
use Ctct\Exceptions\OAuth2Exception;
use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;

/**
 * Powers our admin connect page, as well as misc functionality around connecting to Constant Contact.
 */
class ConstantContact_Connect {

	/**
	 * Option key, and option page slug.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private $key = 'ctct_options_connect';

	/**
	 * CtctOAuth2 object.
	 *
	 * @var object
	 * @since 1.0.0
	 */
	private $oauth = '';

	/**
	 * Api Error message.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $error_message = '';

	/**
	 * Current page redirect Url.
	 *
	 * @var string
	 * @since  1.0.0
	 */
	private $redirect_url = '';

	/**
	 * Parent plugin class.
	 *
	 * @var object
	 * @since 0.0.1
	 */
	protected $plugin = null;

	/**
	 * Whether or not to encrypt.
	 *
	 * @var bool
	 */
	public $should_encrypt = false;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
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

		add_action( 'init', array( $this, 'maybe_connect' ) );

		add_action( 'plugins_loaded', array( $this, 'maybe_disconnect' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
	}

	/**
	 * Watches for our specific $_GET paramaters and if we get a connect request,
	 * pass it to our auth server class to process.
	 *
	 * @since 0.0.1
	 */
	public function maybe_connect() {

		// If we have this get, we may be getting an connect attempt, so lets
		// verify it and potentially process it
		if ( isset( $_GET['cc_connect_attempt'] ) && is_user_logged_in() ) { // Input var okay.

			// Call our access token processing.
			$verified = constant_contact()->authserver->verify_and_save_access_token_return();

			$redirect_args = array(
				'post_type' => 'ctct_forms',
				'page'      => 'ctct_options_connect',
			);

			if ( ! $verified ) {
				$redirect_args['ctct_connect_error'] = 'true';
			}

			wp_redirect( add_query_arg( $redirect_args, admin_url( 'edit.php' ) ) );
			die;
		}
	}

	/**
	 * Add menu options page.
	 *
	 * @since 1.0.0
	 */
	public function add_options_page() {

		if ( ! constant_contact()->api->is_connected() ) {
			// Set our default title of the connect link.
			$connect_title = __( 'Connect Now', 'constant-contact-forms' );
			$connect_link = 'edit.php?post_type=ctct_forms';

		} else {

			// If we've already been connected, then we can set it to be a disconnect button.
			$connect_title = __( 'Disconnect', 'constant-contact-forms' );
			$connect_link = 'edit.php?post_type=ctct_forms';
		}

		// Set up our page.
		$this->options_page = add_submenu_page(
			$connect_link,
			$connect_title,
			$connect_title,
			'manage_options',
			$this->key,
			array( $this, 'admin_page_display' )
		);
	}

	/**
	 * Admin page markup. Mostly handled by CMB2.
	 *
	 * @since 1.0.0
	 * @return mixed page markup or false if not admin.
	 */
	public function admin_page_display() {

		wp_register_style(
			'constant_contact_admin_global_no_connection',
			constant_contact()->url() . 'assets/css/admin-global-no-connection.css',
			array(),
			constant_contact()->version
		);

		wp_enqueue_style( 'constant_contact_admin_global_no_connection' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		wp_enqueue_style( 'constant-contact-oath', constant_contact()->url() . 'assets/css/oath.css' );

		wp_localize_script( 'ctct_form', 'ctct_texts', array( 'disconnectconfirm' => __( 'Are you sure you want to disconnect?', 'constant-contact-forms' ) ) );

		wp_enqueue_script( 'ctct_form' );
		?>
		<div class="wrap cmb2-options-page <?php echo esc_attr( $this->key ); ?>">
			<img class="ctct-logo" src="<?php echo esc_url( constant_contact()->url . 'assets/images/constant-contact-logo.png' ); ?>">
			<div class="ctct-body">
			<?php if ( constantcontact_api()->get_api_token() ) : ?>
				<div class="message notice">
					<p>
						<?php esc_html_e( 'Plugin connected to Constant Contact.', 'constant-contact-forms' ); ?>
					</p>
				</div>

				<form method="post" action="<?php echo esc_url( $this->redirect_url ); ?>">
					<?php wp_nonce_field( 'ctct-admin-disconnect', 'ctct-admin-disconnect' ); ?>
					<input type="hidden" id="ctct-disconnect" name="ctct-disconnect" value="true">
					<input type="submit" class="button button-orange ctct-disconnect" value="<?php esc_html_e( 'Disconnect', 'constant-contact-forms' ); ?>">
				</form>

			<?php else : ?>

			<?php
			if ( isset( $_GET['ctct_connect_error'] ) ) { // Input var okay.
			?>
				<div id="message" class="error"><p>
				<?php esc_html_e( 'There was an error connecting your account. Please try again.', 'constant-contact-forms' ); ?>
				</p></div>
			<?php
			}
			?>
				<p class="ctct-description">
					<?php esc_html_e( 'To take full advantage of this plugin, we recommend having an active Constant Contact account.', 'constant-contact-forms' ); ?>
				</p>

				<!-- Start Columns Here -->
					<div class="left">
						<img
							class="flare"
							src="<?php echo esc_url_raw( $this->plugin->url . 'assets/images/question-mail-connect.png' ); ?>"
							alt="<?php echo esc_attr_x( '? mail', 'email marketing alt text', 'constant-contact-forms' ); ?>"
						/>
						<h3><?php esc_attr_e( 'Need an account?', 'constant-contact-forms' ); ?></h3>
						<p>
							<?php esc_attr_e( 'Sign up for a free 60-day trial to connect with visitors beyond your website.', 'constant-contact-forms' ); ?>
						</p>
						<a class="button button-orange" href="<?php echo esc_url_raw( constant_contact()->api->get_signup_link() ); ?>"><?php esc_attr_e( 'Try us Free', 'constant-contact-forms' ); ?></a>
					</div>
					<div class="right">
						<img
							class="flare"
							src="<?php echo esc_url_raw( $this->plugin->url . 'assets/images/cc-login-connect.png' ); ?>"
							alt="<?php echo esc_attr_x( 'hand holding phone', 'connect alt text', 'constant-contact-forms' ); ?>"
						/>
						<h3><?php esc_attr_e( 'Have an account?', 'constant-contact-forms' ); ?></h3>
						<p>
							<?php esc_attr_e( 'Connect the plugin to log in to your Constant Contact account', 'constant-contact-forms' ); ?>
							<em><?php esc_attr_e( '(by connecting, you authorize this plugin to access your account).', 'constant-contact-forms' ); ?></em>
						</p>
						<?php

						// Get our middleware link.
						$proof = constant_contact()->authserver->set_verification_option();
						$auth_link = constant_contact()->authserver->do_connect_url( $proof );

						// If we have a link, then display the connect button.
						if ( $auth_link ) { ?>
							<a href="<?php echo esc_url_raw( $auth_link ); ?>" class="button button-blue ctct-connect">
								<?php esc_html_e( 'Connect Plugin', 'constant-contact-forms' ); ?>
							</a>
						<?php } ?>
					</div>
				</div>

				<p class="ctct-description small">
					<strong><?php esc_html_e( 'NOTE: ', 'constant-contact-forms' ); ?></strong><?php esc_html_e( 'It’s possible to use the plugin without connecting to a Constant Contact account. In this case, all information collected by the forms will be individually emailed to the Site Admin.', 'constant-contact-forms' ); ?>
				</p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Disconnect from API.
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	public function maybe_disconnect() {

		// Make sure we ahve our nonce key.
		if ( ! isset( $_POST['ctct-admin-disconnect'] ) ) {
			return;
		}

		// Make sure we want to disconnect.
		if ( ! isset( $_POST['ctct-disconnect'] ) ) {
			return;
		}

		// Only run if logged in user can manage site options.
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ctct-admin-disconnect'] ) ), 'ctct-admin-disconnect' ) ) {

			// Delete access token and delete our legacy token as well.
			delete_option( 'ctct_token' );
			delete_option( '_ctct_token' );
		}
	}

	/**
	 * Get an encrypted value.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $check_key key to save to.
	 * @param boolean $fallback_to_ctct_opt Fall back maybe.
	 * @return boolean|string
	 */
	public function e_get( $check_key, $fallback_to_ctct_opt = false ) {

		if ( ! $this->is_encryption_ready() ) {
			return get_option( $check_key, '' );
		}

		// Get our key.
		$key = $this->get_encrpyt_key();

		// Get our saved token.
		if ( $fallback_to_ctct_opt ) {

			// If we want to fallback, we'll get the nested option.
			$options = get_option( 'ctct_options_settings', false );
			if ( $options && isset( $options[ $check_key ] ) ) {
				$encrypted_token = $options[ $check_key ];
			} else {
				return false;
			}
		} else {

			// Otherwise get normal option.
			$encrypted_token = get_option( $check_key );

			// Make sure we have something.
			if ( ! $encrypted_token ) {
				return false;
			}
		}

		try {
			// Try to decrypt it.
			$return = Crypto::decrypt( $encrypted_token, $key );
		} catch ( Exception $e ) {
			// Otherwise just return the raw val.
			$return = '';
		}

		return $return;

	}

	/**
	 * Set an encrypted value.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $check_key  Key to save to.
	 * @param string  $data       Data to save.
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
	 * @return string token
	 */
	public function get_api_token() {

		// Clean up our old tokens.
		$this->check_deleted_legacy_token();

		return $this->e_get( 'ctct_token' );
	}

	/**
	 * If we have a legacy token, let's re-save it.
	 *
	 * @since 1.0.0
	 */
	public function check_deleted_legacy_token() {

		// Get our old token.
		$legacy = get_option( '_ctct_token' );

		// If we got a legacy value, reencrypt and delete it.
		if ( $legacy ) {
			// Update our token with our legacy data.
			$this->update_token( $legacy );
			delete_option( '_ctct_token' );
		}
	}

	/**
	 * Get our encrypt key.
	 *
	 * @since 1.0.0
	 * @return string Key to use for encrypt.
	 */
	public function get_encrpyt_key() {

		if ( ! $this->is_encryption_ready() ) {
			return 'ctct_key';
		}

		$key = get_option( 'ctct_key', false );

		// If we don't have one, make one.
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
	 * @param boolean $first_try If first try or not.
	 * @return object Key
	 */
	public function generate_and_save_key( $first_try = true ) {

		// If we can't run encryption stuff, then don't.
		if ( ! $this->is_encryption_ready() ) {
			return 'ctct_key';
		}

		// Generate a random key from our Encryption library.
		$key = Key::createNewRandomKey();

		// Save our key as a safe string, so we can add it to the DB.
		$key = $key->saveToAsciiSafeString();

		// Save it as our ctct_key, so that we can use it later.
		$updated = update_option( 'ctct_key', $key );

		// If we weren't able to update it, try again, but only do it once.
		if ( ! $updated || $first_try ) {

			// Try generating and saving again, but only one more time.
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

		// Make sure we have our openssl libraries.
		if ( ! function_exists( 'openssl_encrypt' ) || ! function_exists( 'openssl_decrypt' ) ) {
			return false;
		}

		// Check to make sure we dont' get any exceptions when loading the class.
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
			Constant_Contact::get_instance()->load_libs( true );

			// If we have the Runtime test class.
			if ( class_exists( 'Defuse\Crypto\RuntimeTests' ) ) {

				// If we have our Crpyto class, we'll run the included
				// runtime tests and see if we get the correct response.
				$tests  = new Defuse\Crypto\RuntimeTests;
				$tests  = $tests->runtimeTest();
				$return = true;
			}
		} catch ( Exception $exception ) {

			// If we caught an exception of some kind, then we're not able
			// to use this library.
			if ( $exception ) {
				$return = false;
			}
		}

		// Send back if we can or can't use the library.
		return $return;
	}
}

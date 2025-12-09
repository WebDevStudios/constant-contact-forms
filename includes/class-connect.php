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
	private string $key = 'ctct_options_connect';

	/**
	 * Api Error message.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public string $error_message = '';

	/**
	 * Current page redirect Url.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private string $redirect_url = '';

	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	protected object $plugin;

	/**
	 * Options page.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public string $options_page = '';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $plugin Parent class.
	 */
	public function __construct( object $plugin ) {
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
		if ( isset( $_GET['code'] ) && isset( $_GET['state'] ) && is_user_logged_in() ) {

			$verified = constant_contact()->get_api()->acquire_access_token();
			update_option( 'ctct_access_token_timestamp', time() );

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
		if ( ! constant_contact()->get_api()->is_connected() ) {
			$connect_title = esc_html__( 'Connect Now', 'constant-contact-forms' );
		}

		if ( constant_contact_get_needs_manual_reconnect() ) {
			$connect_title .= '<span class="dashicons dashicons-warning ctct-menu-icon"></span>';
		}

		$this->options_page = add_submenu_page(
			'edit.php?post_type=ctct_forms',
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
	 */
	public function admin_page_display() {

		wp_enqueue_style( 'constant-contact-forms-admin' );

		wp_localize_script( 'ctct_form', 'ctctTexts', [ 'disconnectconfirm' => esc_html__( 'Are you sure you want to disconnect?', 'constant-contact-forms' ) ] );

		wp_enqueue_script( 'ctct_form' );
			if ( constant_contact()->get_api()->get_api_token() ) :

			$heading     = esc_html__( 'Account connected!', 'constant-contact-forms' );
			$description = esc_html__( 'You are connected to the Constant Contact account shown below.', 'constant-contact-forms' );
			if ( constant_contact_get_needs_manual_reconnect() ) {
				$heading     = esc_html__( 'Manual reconnection required', 'constant-contact-forms' );
				$description = esc_html__( 'Issues with reauthentication for tokens occurred and a manual disconnect and reconnect is needed.', 'constant-contact-forms' );
			}
			?>
			<div class="wrap connected <?php echo esc_attr( $this->key ); ?>">
				<div class="ctct-connected">
					<div class="ctct-connected-wrap">
						<h3><?php echo esc_html( $heading ); ?></h3>
						<p class="ctct-description">
							<?php echo esc_html( $description ); ?>
						</p>
						<div class="ctct-connection-details">
							<p class="ctct-label">
								<strong><?php esc_html_e( 'Account Name:', 'constant-contact-forms' ); ?></strong>
							</p>
							<p>
								<?php
								$account = false;

								try {
									$account = (object) constant_contact()->get_api()->get_account_info();
									if ( $account ) {
										echo esc_html( $account->first_name . ' ' . $account->last_name );
									}
								} catch ( Exception $ex ) {
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
									echo '<a href="mailto:' . esc_html( $account->contact_email ) . '">' . esc_html( $account->contact_email ) . '</a>';
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

					<hr />

					<?php // phpcs:disable WordPress.WP.EnqueuedResources -- Ok use of inline scripts. ?>

					<div class="ctct-connected-next-step">
						<div>
							<h3><?php esc_html_e( 'Turn contacts into customers!', 'constant-contact-forms' ); ?></h3>
							<p><?php esc_html_e( 'Your site is connected to Constant Contact and ready to start collecting visitor information.', 'constant-contact-forms' ); ?></p>
							<p><?php esc_html_e( 'Create a form that meets your visitors\' needs here:', 'constant-contact-forms' ); ?></p>
							<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=ctct_forms' ) ); ?>" class="button"><?php esc_html_e( 'Add Form', 'constant-contact-forms' ); ?></a>
						</div>
						<div>
							<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) : ?>
								<h3><?php esc_html_e( 'You have a WooCommerce store.', 'constant-contact-forms' ); ?></h3>
								<p><?php esc_html_e( 'We have a plugin for that, too! With Constant Contact + WooCommerce, you can import order, product and customer information into Constant Contact for use in your campaigns.', 'constant-contact-forms' ); ?></p>
								<a href="<?php echo esc_url( network_admin_url( 'plugin-install.php?s=constant+contact+woocommerce&tab=search&type=term' ) ); ?>" class="button"><?php esc_html_e( 'Install the WooCommerce plugin', 'constant-contact-forms' ); ?></a>
							<?php endif; ?>
						</div>
					</div>
				<?php // phpcs:enable WordPress.WP.EnqueuedResources ?>
			</div>

		<?php else :

			$auth_link = constant_contact()->get_api()->get_authorization_url();
			$auth_link = add_query_arg(
				[
					'rmc' => 'wp_connect_connect'
				],
				$auth_link
			);

			$code_link = add_query_arg(
				[
					'post_type' => 'ctct_forms',
					'page'      => 'ctct_options_settings_auth',
				],
				admin_url( 'edit.php' )
			);
			?>
			<div class="ctct-wrap not-connected <?php echo esc_attr( $this->key ); ?>">
				<div class="ctct-cta-left">
					<?php
					// phpcs:disable WordPress.Security.NonceVerification -- OK direct-accessing of $_GET.
					if ( isset( $_GET['ctct_connect_error'] ) ) :
						?>
						<div id="message" class="ctct-error">
							<p>
								<?php esc_html_e( 'There was an error connecting your account. Please try again.', 'constant-contact-forms' ); ?>
							</p>
						</div>
					<?php
					endif;
					// phpcs:enable WordPress.Security.NonceVerification
					?>
					<div class="ctct-logo"></div>
					<h2><?php esc_html_e( 'Connect Constant Contact to your website', 'constant-contact-forms' ); ?></h2>
					<p><?php esc_html_e( 'Log in to your Constant Contact account, or create a new one with a 30-day free trial to get started.', 'constant-contact-forms' ); ?></p>

					<div class="cta-buttons">
						<?php
						if ( $auth_link ) :
							?>
							<a href="<?php echo esc_url_raw( $auth_link ); ?>" target="_blank" class="button ctct-button button-blue ctct-connect">
								<?php esc_html_e( 'Connect', 'constant-contact-forms' ); ?>
							</a>
						<?php endif; ?>
						<a href="https://www.constantcontact.xom/signup" target="_blank" class="button ctct-button ctct-signup">
							<?php esc_html_e( 'Start a free trial', 'constant-contact-forms' ); ?>
						</a>
					</div>

					<p>
					<?php
						printf(
							// translators: Placeholders are for html link markup.
							esc_html__( 'If you already have your code, paste it into your %1$sAccount settings%2$s', 'constant-contact-forms' ),
							sprintf(
								'<a href="%1$s">',
								esc_url( $code_link ),
							),
							'</a>'
						)
					?>
					</p>
				</div>
				<div class="ctct-cta-right">
					<img src="<?php echo esc_url( constant_contact()->url ); ?>/assets/images/form-example-connect.png" alt="<?php esc_attr_e( 'Picture of a a signup form builder from Constant Contact', 'constant-contact-forms' ); ?>') ?>">
					<p>
					<?php
						printf(
							// translators: Placeholders are for html link markup.
							esc_html__( 'To learn more about this pluging read our %1$shelp article%2$s', 'constant-contact-forms' ),
							sprintf(
								'<a href="%1$s">',
								esc_url( 'https://knowledgebase.constantcontact.com/email-digital-marketing/articles/KnowledgeBase/10054-Install-the-Constant-Contact-Forms-plugin-for-WordPress-to-gather-sign-ups-and-feedback?lang=en_US' ),
							),
							'</a>'
						)
					?>
					</p>
				</div>
			</div>
			<?php
		endif;
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
	public function maybe_disconnect() : bool {

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

			delete_option( 'ctct_access_token' );
			delete_option( '_ctct_access_token' );
			delete_option( 'ctct_refresh_token' );
			delete_option( '_ctct_refresh_token' );
			delete_option( '_ctct_expires_in' );
			delete_option( 'ctct_maybe_needs_reconnected' );

			delete_option( 'CtctConstantContactcode_verifier' );
			delete_option( 'CtctConstantContactState' );
			delete_option( 'ctct_auth_url' );
			delete_option( 'ctct_key' );

			constant_contact_delete_option( '_ctct_form_state_authcode' );

			wp_clear_scheduled_hook( 'refresh_token_job' );
			wp_unschedule_hook( 'refresh_token_job' );

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
	public function e_get( string $check_key, bool $fallback_to_ctct_opt = false ) {

		if ( ! $this->is_encryption_ready() ) {
			return get_option( $check_key, '' );
		}

		$key = $this->get_encrpyt_key();
		if ( ! $key ) {
			return false;
		}
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
	public function e_set( string $check_key, string $data, bool $autoload = false ) : string {

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
	 */
	public function update_token( string $access_token, string $refresh_token ) {
		$this->e_set( 'ctct_access_token', $access_token, true );
		$this->e_set( 'ctct_refresh_token', $refresh_token, true );
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

		return $this->e_get( 'ctct_access_token' );
	}

	/**
	 * If we have a legacy token, let's re-save it.
	 *
	 * @since 1.0.0
	 */
	public function check_deleted_legacy_token() {
		$legacy = get_option( '_ctct_access_token' );

		if ( $legacy ) {
			$this->update_token( $legacy, null );
			delete_option( '_ctct_access_token' );
		}
	}

	/**
	 * Get our encrypt key.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception Exception.
	 *
	 * @return false|Defuse\Crypto\Key Key to use for encrypt.
	 */
	public function get_encrpyt_key() {

		if ( ! $this->is_encryption_ready() ) {
			return false;
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
	public function check_crypto_class() : bool {

		try {
			$return = false;
			Constant_Contact::get_instance()->load_libs();

			if ( class_exists( 'Defuse\Crypto\RuntimeTests' ) ) {

				// If we have our Crpyto class, we'll run the included
				// runtime tests and see if we get the correct response.
				$tests  = new Defuse\Crypto\RuntimeTests();
				$tests::runtimeTest();
				$return = true;
			}
		} catch ( Exception $exception ) {}

		return $return;
	}
}

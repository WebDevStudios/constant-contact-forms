<?php
/**
 * @package ConstantContact
 * @subpackage Notifications
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Powers admin pages and activation message.
 */
class ConstantContact_Notifications {

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
	 * Initiate our hooks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function hooks() {

		// Add activation message
		add_action( 'admin_notices', array( $this, 'maybe_show_activation_message' ) );

	}

	/**
	 * Depending on if we should or shouldn't show our activation message, queue it up
	 *
	 * @since   1.0.0
	 */
	public function maybe_show_activation_message() {

		// If we have our query args where we're attempting to dismiss the notice
		if ( $this->should_message_be_dismissed_and_saved() ) {
			// Then save that we dismissed it
			$this->save_dismissed_activation_message();
		}

		// Only show if not connected & it wasn't dismissed
		if ( ! $this->was_activation_message_dismissed() && ! constant_contact()->api->is_connected() ) {
			$this->activation_message();
		}
	}

	/**
	 * Checks our query args and nonce to make sure we should save the dismissal of the notice
	 *
	 * @since   1.0.0
	 * @return  boolean  should we dismiss and save?
	 */
	public function should_message_be_dismissed_and_saved() {

		// Check to make sure we got a dismissal ID
		if ( ! $this->get_notification_dismissal_id() ) {
			return false;
		}

		// If our nonce fails, then bail
		if ( ! ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['ctct-dismiss'] ) ), 'ctct-user-is-dismissing' ) ) ) { // Input var okay.
			return false;
		}

		return true;
	}

	public function get_notification_dismissal_id() {

		// If we don't have our nonce action, bail
		if ( ! isset( $_GET['ctct-dismiss'] ) ) { // Input var okay.
			return false;
		}

		// if we don't have our dismiss query arg, bail
		if ( ! isset( $_GET['ctct-dismiss-action'] ) ) { // Input var okay.
			return false;
		}

		// Get what notification we're attempting to dismiss
		$dismissing_notif = sanitize_text_field( wp_unslash( $_GET['ctct-dismiss-action'] ) );  // Input var okay.

		// If we don't have an action set for our dismiss action, bail
		if ( ! $dismissing_notif ) {
			return false;
		}

		// Send back our notification type
		return $dismissing_notif;
	}

	/**
	 * Was our activation message dismissed
	 *
	 * @since   1.0.0
	 * @return  boolean
	 */
	public function was_activation_message_dismissed() {
		return get_option( 'ctct_notices_dismissed' );
	}

	/**
	 * Save the fact that the user dismissed our message, and don't show again
	 *
	 * @since   1.0.0
	 * @return  boolean  if we updated correctly
	 */
	public function save_dismissed_activation_message() {
		return update_option( 'ctct_notices_dismissed', true, true );
	}

	/**
	 * Displays a fancy activation message to the user
	 *
	 * @since   1.0.0
	 */
	public function activation_message() {

		// Style it up
		wp_enqueue_style(
			'constant-contact-admin-notices',
			constant_contact()->url() . 'assets/css/admin-notices.css',
			array(),
			constant_contact()->version
		);

		?>
		<div id="ctct-admin-notice" class="ctct-admin-notice updated notice">
				<p class="ctct-notice-intro">
				<?php
					printf(
						esc_attr__( 'To take full advatage of the %s plugin, we recommend having an active Constant Contact account or an active free trial with Constant Contact.', 'constantcontact' ),
						'<strong>' . esc_attr__( 'Constant Contact Forms' ) . '</strong>'
					);
				?>
				</p>
				<p>
					<a href="<?php echo esc_url_raw( constant_contact()->api->get_connect_link() ); ?>" target="_blank" class="ctct-notice-button button-primary">
						<?php esc_attr_e( 'Connect your account', 'constantcontact' ); ?>
					</a>
					<a href="https://www.constantcontact.com/" target="_blank" class="ctct-notice-button button-secondary">
						<?php esc_attr_e( 'Try Us Free', 'constantcontact' ); ?>
					</a>
					<a class='ctct-notice-dismiss' href="<?php echo esc_url_raw( $this->get_activation_dismiss_url( 'activation' ) ); ?>">
						<em><?php esc_attr_e( 'Dismiss this notice.', 'constantcontact' ); ?></em>
					</a>
				</p>
			</p>
		</div>

		<?php
	}

	/**
	 * Helper method to get our dimiss activation message url
	 *
	 * @since   1.0.0
	 * @return  string  url to dismiss prompt
	 */
	public function get_activation_dismiss_url( $type ) {

		// Set a link with our current url and desired action
		$link = add_query_arg( array( 'ctct-dismiss-action' => esc_attr( $type ) ) );

		// Also nonce it and return it
		return wp_nonce_url( $link, 'ctct-user-is-dismissing', 'ctct-dismiss' );
	}
}


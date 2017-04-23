<?php
/**
 * Notifications.
 *
 * @package ConstantContact
 * @subpackage Notifications
 * @author Constant Contact
 * @since 1.0.0
 */

/**
 * Powers admin pages and activation message.
 */
class ConstantContact_Notifications {

	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $plugin Parent plugin class.
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
		// Add activation message.
		add_action( 'admin_notices', array( $this, 'main' ) );
	}

	/**
	 * Get all our notifications that should fire.
	 *
	 * @since 1.0.0
	 */
	public function get_notifications() {

		/**
		 * Filters our notifications.
		 *
		 * @since 1.0.0
		 *
		 * @param array $value Array of notification details.
		 */
		return apply_filters( 'constant_contact_notifications', array(
			array(
				'ID'         => 'activation',
				'callback'   => array( 'ConstantContact_Notification_Content', 'activation' ),
				'require_cb' => 'constant_contact_is_not_connected',
			),
		) );
	}

	/**
	 * Get our update notifications from our update class.
	 *
	 * @since 1.0.0
	 *
	 * @return array Update notifications we should surface.
	 */
	public function get_update_notifications() {
		return get_option( 'ctct_update_notifications', array() );
	}

	/**
	 * Depending on if we should or shouldn't show our activation message, queue it up.
	 *
	 * @since 1.0.0
	 */
	public function main() {

		// If we have our query args where we're attempting to dismiss the notice
		// Get our potentically dismissed notif ID.
		$notif_id = $this->get_dismissal_id();

		// First, we want to check if we should save any dismissals.
		if ( $this->check_dismissal_nonce() && $notif_id ) {

			// Then save that we dismissed it.
			$this->save_dismissed_notification( $notif_id );
		}

		// Get all our notifications.
		$notifications = $this->get_notifications();
		$update_notifications = $this->get_update_notifications();

		if ( is_array( $notifications ) && is_array( $update_notifications ) ) {
			$notifications = array_merge( $notifications, $update_notifications );
		}

		foreach ( $notifications as $notif ) {
			$this->maybe_show_notification( $notif );
		}
	}

	/**
	 * Determines whether or not a specific notification should be show, and
	 * shows it it it should be shown.
	 *
	 * @since 1.0.0
	 *
	 * @param array $notif Array of notification data.
	 * @return mixed False if not shown, nothing if shown.
	 */
	public function maybe_show_notification( $notif ) {

		// If our notification isn't an array, skip it.
		if ( ! is_array( $notif ) ) {
			return false;
		}

		// Save our notification data to a helper var.
		$notif_id     = isset( $notif['ID'] ) ? esc_attr( $notif['ID'] ) : false;
		$callback     = isset( $notif['callback'] ) ? $notif['callback'] : false;
		$require_cb   = isset( $notif['require_cb'] ) ? $notif['require_cb'] : false;

		// If we don't have an ID or callback set, bail.
		if ( ! $notif_id || ! $callback ) {
			return false;
		}

		// Don't show if it was dismissed.
		if ( $this->was_notification_dismissed( $notif_id ) ) {
			return false;
		}

		// If we don have requirements set up for a notif, then check them.
		if ( $require_cb ) {

			// Check to make sure the requirements defined for a notification evalulate to true.
			$requirements_passed = ( $this->check_requirements_callback_for_notif( $require_cb ) );

			// If we didnt' pass, then return.
			if ( ! $requirements_passed ) {
				return false;
			}
		}

		// If we cant call our notification callback, bail.
		if ( ! is_callable( $callback ) ) {
			return false;
		}

		// Get our notifictaion content from our callback.
		$notif_content = call_user_func( $callback );

		$this->show_notice( $notif_id, $notif_content );

		return true;

	}

	/**
	 * Call and return results of executing a callback for a notificaion.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $require_cb Valid callback.
	 * @return bool Results of callback.
	 */
	public function check_requirements_callback_for_notif( $require_cb ) {

		if ( is_callable( $require_cb ) ) {
			return ( call_user_func( $require_cb ) );
		}

		return false;
	}

	/**
	 * Checks to see if we have a dismissal nonce, and if it is valid.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether or not nonce is verified.
	 */
	public function check_dismissal_nonce() {

		// Double check that we have our nonce that we'll use
		if ( ! isset( $_GET['ctct-dismiss'] ) ) { // Input var okay.
			return false;
		}

		// Save our nonce
		$nonce = sanitize_text_field( wp_unslash( $_GET['ctct-dismiss'] ) );  // Input var okay.

		// If our nonce fails, then we don't want to dismiss it.
		return ( wp_verify_nonce( $nonce, 'ctct-user-is-dismissing' ) );
	}

	/**
	 * Get the notice the user is attempting to dismiss.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed False on failure, string of ID on success.
	 */
	public function get_dismissal_id() {

		// If we don't have our nonce action, bail
		if ( ! isset( $_GET['ctct-dismiss'] ) ) { // Input var okay.
			return false;
		}

		// If we don't have our dismiss query arg, bail.
		if ( ! isset( $_GET['ctct-dismiss-action'] ) ) { // Input var okay.
			return false;
		}

		// Get what notification we're attempting to dismiss.
		$dismissing_notif = sanitize_text_field( wp_unslash( $_GET['ctct-dismiss-action'] ) );  // Input var okay.

		// If we don't have an action set for our dismiss action, bail.
		if ( ! $dismissing_notif ) {
			return false;
		}

		// Send back our notification type.
		return $dismissing_notif;
	}

	/**
	 * Save the fact that the user dismissed our message, and don't show again.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key ID of notification.
	 * @return bool If we updated correctly.
	 */
	public function save_dismissed_notification( $key ) {

		// Call our save option helper.
		return $this->save_dismissed_option( $key, true );
	}

	/**
	 * Set a specific notificaion saved state to false.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key ID of notificaion.
	 * @return bool Update succeeded?
	 */
	public function delete_dismissed_notification( $key ) {

		// Call our save option helper.
		return $this->save_dismissed_option( $key, false );
	}

	/**
	 * Get all saved dismissial states.
	 *
	 * @since 1.0.0
	 *
	 * @return array States of dismissial notices.
	 */
	public function get_dismissed_options() {
		return get_option( 'ctct_notices_dismissed' );
	}

	/**
	 * Save all dismissed notices.
	 *
	 * @since 1.0.0
	 *
	 * @param array $options Array of dismissial states.
	 * @return bool If updated.
	 */
	public function save_dismissed_options( $options ) {
		return update_option( 'ctct_notices_dismissed', $options, true );
	}

	/**
	 * Save a single dismissal notice state.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key   ID of notice.
	 * @param string $value Value to be saved to DB.
	 * @return bool If saved or not.
	 */
	public function save_dismissed_option( $key, $value ) {

		// Get all of our options we have saved.
		$options = $this->get_dismissed_options();

		// If for some reason, we didn't get an array, then clear it out.
		if ( ! is_array( $options ) ) {
			$options = array();
		}

		// Save our keyed notification to be saved.
		$options[ esc_attr( $key ) ] = esc_attr( $value );

		// Save all the options.
		return $this->save_dismissed_options( $options );
	}

	/**
	 * Check to see if we've already dismissed a specific notificaion.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Notification ID.
	 * @return bool If dismissed.
	 */
	public function was_notification_dismissed( $key = '' ) {

		// Get all our options.
		$option = $this->get_dismissed_option( $key );

		// If we have 'true' or '1' saved, then its true.
		$is_true = ( ( 'true' === $option ) || ( '1' === $option ) );

		// Cast to boolean and send it back.
		return ( $is_true ? true : false );
	}

	/**
	 * Helper to get single option from our array of notif states.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key ID of notification state to get.
	 * @return string Value in DB.
	 */
	public function get_dismissed_option( $key = '' ) {

		// Get all our dismissed notifications.
		$options = $this->get_dismissed_options();

		// If we have the notification saved in our options array.
		if ( isset( $options[ esc_attr( $key ) ] ) ) {

			// Return the option of whatever it is.
			return $options[ esc_attr( $key ) ];
		}

		// Otherwise, we'll just return false and bail.
		return false;
	}

	/**
	 * Displays a fancy activation message to the user.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key     Notification key.
	 * @param string $content Admin notice content.
	 */
	public function show_notice( $key, $content = '' ) {

		// If we don't have any content, bail.
		if ( ! $content ) {
			return;
		}

		// Show our styles.
		$this->do_styles();

		// Wrap our content in our markup.
		?>
		<div id="ctct-admin-notice-<?php echo esc_attr( $key ); ?>" class="ctct-admin-notice updated notice">
			<?php echo wp_kses_post( $content ); ?>
			<?php constant_contact()->notifications->do_dismiss_link( esc_attr( $key ) ); ?>
		</div>
		<?php
	}

	/**
	 * Enqueue our admin notification styles.
	 *
	 * @since 1.0.0
	 */
	public function do_styles() {

		// Set our flag about notice being shown, so we don't re-enqueue styles.
		static $have_styles = false;

		// If we haven't shown styles yet, enqueue em.
		if ( ! $have_styles ) {

			wp_enqueue_style(
				'constant-contact-admin-notices',
				constant_contact()->url() . 'assets/css/admin-notices.css',
				array(),
				Constant_Contact::VERSION
			);

			$have_styles = true;
		}
	}

	/**
	 * Display our dismiss link for a notfication.
	 *
	 * @since 1.0.0
	 *
	 * @param string $notif_id ID of notification.
	 */
	public function do_dismiss_link( $notif_id ) {

		?>
		<a class="ctct-notice-dismiss notice-dismiss" href="<?php echo esc_url_raw( $this->get_activation_dismiss_url( esc_attr( $notif_id ) ) ); ?>">
			<span class="screen-reader-text"><?php esc_attr_e( 'Dismiss this notice.', 'constant-contact-forms' ); ?></span>
		</a>
		<?php
	}

	/**
	 * Helper method to get our dimiss activation message url.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Dismiss action type.
	 * @return string URL to dismiss prompt.
	 */
	public function get_activation_dismiss_url( $type ) {

		// Set a link with our current url and desired action.
		$link = add_query_arg( array( 'ctct-dismiss-action' => esc_attr( $type ) ) );

		return wp_nonce_url( $link, 'ctct-user-is-dismissing', 'ctct-dismiss' );
	}
}


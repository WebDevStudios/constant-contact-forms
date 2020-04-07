<?php
/**
 * Notifications.
 *
 * @package ConstantContact
 * @subpackage Notifications
 * @author Constant Contact
 * @since 1.0.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tags in docblocks.
 */

/**
 * Powers admin pages and activation message.
 *
 * @since 1.0.0
 */
class ConstantContact_Notifications {

	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	protected $plugin;

	/**
	 * Option name where dismissed notices are logged.
	 *
	 * @since 1.6.0
	 *
	 * @var string
	 */
	public static $dismissed_notices_option = 'ctct_notices_dismissed';

	/**
	 * Option name where the "Leave a Review" notice is dismissed.
	 *
	 * @since 1.6.0
	 *
	 * @var string
	 */
	public static $review_dismissed_option = 'ctct-review-dismissed';

	/**
	 * Option name for when the "Leave a Review" button has been clicked (we assume the user has left a review).
	 *
	 * @since 1.6.0
	 *
	 * @var string
	 */
	public static $reviewed_option = 'ctct-reviewed';

	/**
	 * Option name for deleted forms, containing IDs for post and widget instances of forms.
	 *
	 * @since NEXT
	 *
	 * @var string
	 */
	public static $deleted_forms = 'ctct_deleted_forms';

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
		add_action( 'admin_notices', [ $this, 'main' ] );
	}

	/**
	 * Get all our notifications that should fire.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_notifications() {

		/**
		 * Filters our notifications.
		 *
		 * @since 1.0.0
		 *
		 * @param array $value Array of notification details.
		 */
		return apply_filters( 'constant_contact_notifications', [
			[
				'ID'         => 'activation',
				'callback'   => [ 'ConstantContact_Notification_Content', 'activation' ],
				'require_cb' => 'constant_contact_is_not_connected',
			],
		] );
	}

	/**
	 * Get our update notifications from our update class.
	 *
	 * @since 1.0.0
	 *
	 * @return array Update notifications we should surface.
	 */
	public function get_update_notifications() {
		return get_option( 'ctct_update_notifications', [] );
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

		// Get current action to ensure dismissing on correct load.
		$action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING );

		if ( $this->check_dismissal_nonce() && $notif_id ) {
			$this->save_dismissed_notification( $notif_id );
		}

		$notifications        = $this->get_notifications();
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

		if ( ! is_array( $notif ) ) {
			return false;
		}

		$notif_id   = isset( $notif['ID'] ) ? esc_attr( $notif['ID'] ) : false;
		$callback   = isset( $notif['callback'] ) ? $notif['callback'] : false;
		$require_cb = isset( $notif['require_cb'] ) ? $notif['require_cb'] : false;

		if ( ! $notif_id || ! $callback ) {
			return false;
		}

		if ( $this->was_notification_dismissed( $notif_id ) ) {
			return false;
		}

		if ( $require_cb ) {

			$requirements_passed = $this->check_requirements_callback_for_notif( $require_cb );

			if ( ! $requirements_passed ) {
				return false;
			}
		}

		if ( ! is_callable( $callback ) ) {
			return false;
		}

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
			return call_user_func( $require_cb );
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

		// phpcs:disable WordPress.Security.NonceVerification -- OK direct-accessing of $_GET.
		if ( ! isset( $_GET['ctct-dismiss'] ) ) {
			return false;
		}

		$nonce = sanitize_text_field( wp_unslash( $_GET['ctct-dismiss'] ) );
		// phpcs:enable WordPress.Security.NonceVerification

		return wp_verify_nonce( $nonce, 'ctct-user-is-dismissing' );
	}

	/**
	 * Get the notice the user is attempting to dismiss.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed False on failure, string of ID on success.
	 */
	public function get_dismissal_id() {

		// phpcs:disable WordPress.Security.NonceVerification -- OK direct-accessing of $_GET.
		if ( ! isset( $_GET['ctct-dismiss'] ) || ! isset( $_GET['ctct-dismiss-action'] ) ) {
			return false;
		}

		$dismissing_notif = sanitize_text_field( wp_unslash( $_GET['ctct-dismiss-action'] ) );
		// phpcs:enable WordPress.Security.NonceVerification

		if ( ! $dismissing_notif ) {
			return false;
		}

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
		if ( 'deleted_forms' === $key ) {
			$this->delete_dismissed_option( $key );
		}
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
		return get_option( self::$dismissed_notices_option );
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
		return update_option( self::$dismissed_notices_option, $options, true );
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
		$options = $this->get_dismissed_options();

		if ( ! is_array( $options ) ) {
			$options = [];
		}

		$options[ esc_attr( $key ) ] = esc_attr( $value );

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

		$option = $this->get_dismissed_option( $key );

		$is_true = ( ( 'true' === $option ) || ( '1' === $option ) );

		return $is_true ?: false ;
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

		$options = $this->get_dismissed_options();

		if ( isset( $options[ esc_attr( $key ) ] ) ) {
			return $options[ esc_attr( $key ) ];
		}

		return false;
	}

	/**
	 * Displays a fancy activation message to the user.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key     Notification key.
	 * @param string $content Admin notice content.
	 * @return void
	 */
	public function show_notice( $key, $content = '' ) {

		if ( ! $content ) {
			return;
		}

		$this->do_styles();

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

		static $have_styles = false;

		if ( ! $have_styles ) {
			wp_enqueue_style( 'constant-contact-forms-admin' );
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
		$link = add_query_arg( [ 'ctct-dismiss-action' => esc_attr( $type ) ] );
		return wp_nonce_url( $link, 'ctct-user-is-dismissing', 'ctct-dismiss' );
	}

	/**
	 * Fully remove a saved notification option from the database.
	 *
	 * Redirect to current page with dismissal query args removal to avoid potentially re-dismissing notices unintentionally.
	 *
	 * @since  NEXT
	 *
	 * @param  string $key Notice option key.
	 */
	protected function delete_dismissed_option( $key ) {
		delete_option( "ctct_{$key}" );
		wp_safe_redirect( remove_query_arg( [ 'ctct-dismiss-action', 'ctct-dismiss' ] ) );
		exit;
	}
}


<?php
/**
 * Notification content.
 *
 * @package ConstantContact
 * @subpackage Notifications_Content
 * @author Constant Contact
 * @since 1.0.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tags in docblocks.
 */

/**
 * Holds notification content for easy manipulation
 *
 * @since 1.0.0
 */
class ConstantContact_Notification_Content {

	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	protected $plugin;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $plugin Plugin primary object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Display our notification content for our activation message.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function activation() {
		$auth_url = add_query_arg( [ 'rmc' => 'wp_admin_connect' ], constant_contact()->api->get_connect_link() );
		$try_url  = add_query_arg( [ 'rmc' => 'wp_admin_try' ], constant_contact()->api->get_signup_link() );

		ob_start();
	?>
			<p class="ctct-notice-intro">
				<?php
					printf(

						// translators: Placeholder will hold "Constan Contact Forms" with <strong> tags.
						esc_attr__( 'Get the most out of the %s plugin &mdash; use it with an active Constant Contact account.', 'constant-contact-forms' ),
						'<strong>' . esc_attr__( 'Constant Contact Forms' ) . '</strong>'
					);
				?>
			</p>

			<p>
				<a href="<?php echo esc_url_raw( $auth_url ); ?>" class="ctct-notice-button button-primary">
					<?php esc_attr_e( 'Connect your account', 'constant-contact-forms' ); ?>
				</a>
				<a href="<?php echo esc_url_raw( $try_url ); ?>" class="ctct-notice-button button-secondary">
					<?php esc_attr_e( 'Try Us Free', 'constant-contact-forms' ); ?>
				</a>

				<?php
					$link_start = sprintf( '<a href="%1$s">', admin_url( 'edit.php?post_type=ctct_forms&page=ctct_options_about' ) );

					printf(
						/* Translators: Placeholders around "Learn More" hold html `<a>` tag. */
						esc_html__( '%1$sLearn More%2$s about the power of email marketing.', 'constant-contact-forms' ),
						sprintf( '<a href="%1$s">', esc_url( admin_url( 'edit.php?post_type=ctct_forms&page=ctct_options_about' ) ) ),
						'</a>'
					)
				?>
			</p>
	<?php
		return ob_get_clean();
	}

	/**
	 * Notification content for our 'too many lists' error.
	 *
	 * @since 1.0.0
	 *
	 * @return string Notification text.
	 */
	public static function too_many_lists() {
		return esc_html__( 'You currently have a large number of lists in your Constant Contact account. You may experience some issues with syncing them.', 'constant-contact-forms' );
	}

	/**
	 * Notification content for opt-in notice.
	 *
	 * @since 1.2.0
	 *
	 * @return string
	 */
	public static function optin_admin_notice() {
		add_filter( 'wp_kses_allowed_html', 'constant_contact_filter_html_tags_for_optin' );

		ob_start();
	?>

		<div class="admin-notice-logo">
			<img src="<?php echo esc_url( constant_contact()->url ); ?>/assets/images/ctct-admin-notice-logo.png" alt="<?php echo esc_attr_x( 'Constant Contact logo', 'img alt text', 'constant-contact-forms' ); ?>" />
		</div>

		<div class="admin-notice-message">
			<h4 id="ctct-admin-notice-tracking-optin-header"><?php esc_html_e( 'Constant Contact Forms for WordPress data tracking opt-in', 'constant-contact-forms' ); ?></h4>
			<div>
				<input type="checkbox" id="ctct_admin_notice_tracking_optin" name="ctct_admin_notice_tracking_optin" value="yes" aria-labelledby="ctct-admin-notice-tracking-optin-header" />
			</div>
			<div>
				<?php
					printf(
						/* Translators: Placeholder here is a `<br />` HTML tag for formatting. */
						esc_html__( 'Allow Constant Contact to use Google Analytics&trade; to track your usage across the Constant Contact Forms plugin. %1$s You can change this opt-in within the plugin\'s settings page at any time.', 'constant-contact-forms' ),
						'<br />'
					);
				?>
			</div>
		</div>

	<?php
		return ob_get_clean();
	}

	/**
	 * Admin notice regarding review requests.
	 *
	 * @since 1.2.2
	 *
	 * @return string
	 */
	public static function review_request() {
		add_filter( 'wp_kses_allowed_html', 'constant_contact_filter_html_tags_for_optin' );

		ob_start();
	?>

		<div class="admin-notice-logo">
			<img src="<?php echo esc_url( constant_contact()->url ); ?>/assets/images/ctct-admin-notice-logo.png" alt="<?php echo esc_attr_x( 'Constant Contact logo', 'img alt text', 'constant-contact-forms' ); ?>" />
		</div>

		<div class="admin-notice-message">
			<div>
				<?php
					printf(
						/* Translators: Placeholders here are for `<strong>` HTML tags. */
						esc_html__( 'You have been successfully using %1$sConstant Contact Forms%2$s to capture valuable site visitor information! Please consider leaving us a nice review. Reviews help fellow WordPress admins find our plugin and lets you provide us useful feedback.', 'constant-contact-forms' ),
						'<strong>',
						'</strong>'
					);
				?>
			</div>
			<p>
				<a class="button button-secondary ctct-review" target="_blank" href="https://wordpress.org/support/plugin/constant-contact-forms/reviews/"><?php esc_html_e( 'Leave a review', 'constant-contact-forms' ); ?></a>
				<a class="button button-secondary ctct-review-dismiss" href="#"><?php esc_html_e( 'Dismiss', 'constant-contact-forms' ); ?></a>
			</p>
		</div>

	<?php
		return ob_get_clean();
	}

	/**
	 * Admin notice regarding thrown exceptions.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	public static function exceptions() {
		return sprintf(
			/* Translators: placeholders will be html `<a>` links. */
			esc_html__( 'Constant Contact Forms has experienced issues that may need addressed and functionality may be missing. Please enable the "Support" checkbox in the %1$sConstant Contact settings%2$s and start a %3$sforum support thread%4$s. Our support team will aid with further steps.', 'constant-contact-forms' ),
			sprintf( '<a href="%s">', esc_url( admin_url( 'edit.php?post_type=ctct_forms&page=ctct_options_settings' ) ) ),
			'</a>',
			sprintf( '<a href="%s" target="_blank">', esc_url( 'https://wordpress.org/support/topic/constant-contact-forms-has-experienced-issues-that-need-addressed-admin-notice/' ) ),
			'</a>'
		);
	}

	/**
	 * Admin notice regarding deleted forms.
	 *
	 * @since  NEXT
	 *
	 * @return string Deleted forms notice HTML.
	 */
	public static function deleted_forms() {
		$option = get_option( ConstantContact_Notifications::$deleted_forms, [] );
		return '';
	}

}

/**
 * Filters in the input to our allowed tags for our admin notice.
 *
 * @since 1.2.0
 *
 * @param array $allowedtags Allowed HTML.
 * @return array
 */
function constant_contact_filter_html_tags_for_optin( $allowedtags = [] ) {

	$allowedtags['input'] = [
		'type'  => true,
		'id'    => true,
		'name'  => true,
		'value' => true,
	];

	return $allowedtags;
}

/**
 * Adds our opt-in notification to the notification system.
 *
 * @since 1.2.0
 *
 * @param array $notifications Array of notifications pending to show.
 * @return array Array of notifications to show.
 */
function constant_contact_add_optin_notification( $notifications = [] ) {

	$notifications[] = [
		'ID'         => 'optin_admin_notice',
		'callback'   => [ 'ConstantContact_Notification_Content', 'optin_admin_notice' ],
		'require_cb' => 'constant_contact_maybe_display_optin_notification',
	];

	return $notifications;
}
add_filter( 'constant_contact_notifications', 'constant_contact_add_optin_notification' );

/**
 * Adds our opt-in notification to the notification system.
 *
 * @since 1.2.0
 *
 * @param array $notifications Array of notifications pending to show.
 * @return array Array of notifications to show.
 */
function constant_contact_add_review_notification( $notifications = [] ) {

	$notifications[] = [
		'ID'         => 'review_request',
		'callback'   => [ 'ConstantContact_Notification_Content', 'review_request' ],
		'require_cb' => 'constant_contact_maybe_display_review_notification',
	];

	return $notifications;
}
add_filter( 'constant_contact_notifications', 'constant_contact_add_review_notification' );

/**
 * Adds a notification that errors have occurred that need looked into.
 *
 * @since 1.6.0
 *
 * @param array $notifications Array of notifications pending to show.
 * @return array Array of notifications to show.
 */
function constant_contact_exceptions_thrown( $notifications = [] ) {

	$notifications[] = [
		'ID'         => 'exceptions',
		'callback'   => [ 'ConstantContact_Notification_Content', 'exceptions' ],
		'require_cb' => 'constant_contact_maybe_display_exceptions_notice',
	];

	return $notifications;
}

add_filter( 'constant_contact_notifications', 'constant_contact_exceptions_thrown' );

/**
 * Add notification on form deletion if instances of that form appear as shortcodes or widgets.
 *
 * @since  NEXT
 *
 * @param  array $notifications Array of notifications to be shown.
 * @return array                Array of notifications to be shown.
 */
function constant_contact_form_deleted( array $notifications = [] ) {
	$notifications[] = [
		'ID'         => 'deleted_forms',
		'callback'   => [ 'ConstantContact_Notification_Content', 'deleted_forms' ],
		'require_cb' => 'constant_contact_maybe_display_deleted_forms_notice',
	];

	return $notifications;
}
add_filter( 'constant_contact_notifications', 'constant_contact_form_deleted' );

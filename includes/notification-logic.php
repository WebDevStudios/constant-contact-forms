<?php
/**
 * Notifications logic.
 *
 * @package    ConstantContact
 * @subpackage NotificationsLogic
 * @author     Constant Contact
 * @since      2.10.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */


/**
 * Maybe display the review request notification in the Constant Contact areas.
 * @return bool
 * @since 1.2.2
 */
function constant_contact_maybe_display_review_notification() {

	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}

	if ( ! constant_contact()->is_constant_contact() ) {
		return false;
	}

	if ( 'true' === get_option( ConstantContact_Notifications::$reviewed_option, 'false' ) ) {
		return false;
	}

	$activated_time = get_option( Constant_Contact::$activated_date_option );

	if ( ! $activated_time || time() < strtotime( '+14 days', $activated_time ) ) {
		return false;
	}

	$dismissed = get_option( ConstantContact_Notifications::$review_dismissed_option, [] );

	if ( isset( $dismissed['count'] ) && '1' === $dismissed['count'] ) {
		$fourteen_days = strtotime( '-14 days' );

		if ( isset( $dismissed['time'] ) && $dismissed['time'] < $fourteen_days ) {
			return true;
		}

		return false;
	}

	if ( isset( $dismissed['count'] ) && '2' === $dismissed['count'] ) {
		$thirty_days = strtotime( '-30 days' );
		if ( isset( $dismissed['time'] ) && $dismissed['time'] < $thirty_days
		) {
			return true;
		}

		return false;
	}

	if ( isset( $dismissed['count'] ) && '3' === $dismissed['count'] ) {
		$thirty_days = strtotime( '-14 days' );
		if ( isset( $dismissed['time'] ) && $dismissed['time'] < $thirty_days
		) {
			return true;
		}

		return false;
	}

	if ( isset( $dismissed['count'] ) && '4' === $dismissed['count'] ) {
		$thirty_days = strtotime( '-30 days' );
		if ( isset( $dismissed['time'] ) && $dismissed['time'] < $thirty_days
		) {
			return true;
		}

		return false;
	}

	if ( isset( $dismissed['count'] ) && '5' === $dismissed['count'] ) {
		return false;
	}

	if ( absint( get_option( 'ctct-processed-forms' ) ) >= 10 ) {
		return true;
	}

	return true;
}

/**
 * Handles the notice of if we have exceptions existing.
 * @return bool
 * @since 1.6.0
 */
function constant_contact_maybe_display_exceptions_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}

	$maybe_has_error = get_option( 'ctct_exceptions_exist' );

	return ( 'true' === $maybe_has_error );
}

/**
 * Determine whether to display the deleted forms notice in admin.
 * @return bool Whether to display the deleted forms notice.
 * @since  1.8.0
 */
function constant_contact_maybe_display_deleted_forms_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}

	return ! empty( get_option( ConstantContact_Notifications::$deleted_forms, [] ) );
}

/**
 * Maybe set exception notice for admin notification.
 *
 * @param Exception $e
 *
 * @since 1.13.0
 */
function constant_contact_forms_maybe_set_exception_notice( $e = '' ) {

	if ( ! empty( $e ) ) {
		// Do not notify if the exception code is 400 or the message contains "Bad Request".
		if (
			( 400 === $e->getCode() ) ||
			( false !== stripos( $e->getMessage(), 'Bad Request' ) )
		) {
			return;
		}

		// Do not notify if the exception code is 503 or the message contains "Service Unavailable".
		if (
			( 503 === $e->getCode() ) ||
			( false !== stripos( $e->getMessage(), 'Service Unavailable' ) )
		) {
			return;
		}
	}

	constant_contact_set_has_exceptions();
}

/**
 * Maybe show notification about API v3 changes.
 * @return bool|int
 * @since 1.14.0
 */
function constant_contact_maybe_display_api3_upgrade_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}

	$current_version = get_option( 'ctct_plugin_version' );

	return version_compare( $current_version, '2.0.0', '<' );
}

/**
 * Maybe show notification about newly implemented API v3 changes.
 * @return bool|int
 * @since 2.0.0
 */
function constant_contact_maybe_display_api3_upgraded_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}

	$current_version = get_option( 'ctct_plugin_version' );

	return (
		version_compare( $current_version, '2.0.0', '=' ) ||
		'' === get_option( 'CtctConstantContactState', '' )
	);
}

/**
 * Maybe show notification for need to manually disconnect/reconnect account.
 * @return bool
 * @since 2.2.0
 */

function constant_contact_maybe_display_disconnect_reconnect_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}

	$maybe_display = get_transient( 'ctct_maybe_needs_reconnected' );

	return true === $maybe_display;
}

/**
 * Maybe show notification regarding `DISABLE_WP_CRON`.
 * @return bool
 * @since 2.2.0
 */
function constant_contact_maybe_show_cron_notification() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}

	if ( ! constant_contact()->is_constant_contact() ) {
		return false;
	}

	if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) {
		return true;
	}

	return false;
}

function constant_contact_maybe_show_update_available_notification() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}

	if ( ! constant_contact()->is_constant_contact() ) {
		return false;
	}

	$version = '';
	$resp    = wp_remote_get( 'https://api.wordpress.org/plugins/info/1.2/?action=plugin_information&slug=constant-contact-forms' );
	if ( ! is_wp_error( $resp ) ) {
		$data    = json_decode( wp_remote_retrieve_body( $resp ) );
		$version = $data->version;
	}
	$current_version = get_option( 'ctct_plugin_version' );

	if ( $version && version_compare( $current_version, $version, '<' ) ) {
		return true;
	}

	// If we got this far, we just failed to get the current available version.
	return false;
}

/**
 * Maybe display our list notes notification.
 * @return bool
 * @since 2.10.0
 */
function constant_contact_maybe_show_list_notes_notification(): bool {
	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}

	if ( ! constant_contact()->is_constant_contact() ) {
		return false;
	}

	// Technically already checked for in is_constant_contact() but re-checking for just
	// this screen should also limit it to JUST our lists list.
	$screen = get_current_screen();
	if ( is_null( $screen ) || 'edit-ctct_lists' !== $screen->id ) {
		return false;
	}

	return true;
}

<?php
/**
 * Helper Functions for end-users to leverage when building themes or plugins.
 *
 * @package ConstantContact
 * @author Constant Contact
 * @since 1.0.0
 */

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Checks to see if a user is connected to Constant Contact or not.
 *
 * @since 1.0.0
 *
 * @return boolean Whether or not they are connected.
 */
function constant_contact_is_connected() {
	return ( constant_contact()->api->is_connected() );
}

/**
 * Checks to see if a user is not connected to Constant Contact or not.
 *
 * @since 1.0.0
 *
 * @return boolean Whether or not they are NOT connected.
 */
function constant_contact_is_not_connected() {
	return ! ( constant_contact()->api->is_connected() );
}

/**
 * Get a form's markup without using a shortcode.
 *
 * @since 1.0.0
 *
 * @param int $form_id Form post ID to grab.
 * @return string HTML markup
 */
function constant_contact_get_form( $form_id ) {
	return constant_contact()->display_shortcode->get_form( $form_id );
}

/**
 * Get a form and display it without using a shortcode.
 *
 * @since 1.0.0
 *
 * @param int $form_id Form post ID to grab.
 */
function constant_contact_display_form( $form_id ) {
	constant_contact()->display_shortcode->display_form( $form_id );
}

/**
 * Get an array of forms.
 *
 * @since 1.0.0
 *
 * @return array WP_Query results of forms.
 */
function constant_contact_get_forms() {
	return constant_contact()->cpts->get_forms( false, true );
}

/**
 * Render a shortcode for display, not for parsing.
 *
 * @since 1.2.0
 *
 * @param string $form_id Form ID to provide in the output.
 * @return string Non-parsed shortcode.
 */
function constant_contact_display_shortcode( $form_id ) {
	return sprintf( '[ctct form="%s"]', $form_id );
}

/**
 * Maybe display the opt-in notification on the dashboard.
 *
 * @since 1.2.0
 *
 * @return bool
 */
function constant_contact_maybe_display_optin_notification() {

	if ( ! function_exists( 'get_current_screen' ) ) {
		return false;
	}

	$current_screen = get_current_screen();
	if ( ! is_object( $current_screen ) || 'dashboard' !== $current_screen->base ) {
		return false;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}

	$privacy = get_option( 'ctct_privacy_policy_status', '' );

	if ( '' !== $privacy ) {
		return false;
	}

	return true;
}

/**
 * Maybe display the review request notification in the Constant Contact areas.
 *
 * @since 1.2.2
 *
 * @return bool
 */
function constant_contact_maybe_display_review_notification() {

	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}

	if ( ! constant_contact()->is_constant_contact() ) {
		return false;
	}

	if ( 'true' === get_option( 'ctct-reviewed', 'false' ) ) {
		return false;
	}

	// @todo date_diff() comparisons.
	//
	$dismissed = get_option( 'ctct-review-dismissed', array() );
	if ( isset( $dismissed['count'] ) && '1' === $dismissed['count'] ) {
		$fourteen_days = strtotime( '-14 days' );
		if ( isset( $dismissed['time'] ) &&
		     $dismissed['time'] < $fourteen_days
		) {
			return true;
		} else {
			return false;
		}
	}

	if ( isset( $dismissed['count'] ) && '2' === $dismissed['count'] ) {
		$thirty_days = strtotime( '-30 days' );
		if ( isset( $dismissed['time'] ) &&
		     $dismissed['time'] < $thirty_days
		) {
			return true;
		} else {
			return false;
		}
	}

	if ( isset( $dismissed['count'] ) && '3' === $dismissed['count'] ) {
		return false;
	}

	if ( absint( get_option( 'ctct-processed-forms' ) ) >= 10 ) {
		return true;
	}

	return true;
}

/**
 * Whether or not to show our reCAPTCHA info notice. Should only show
 *
 * @since 1.2.4
 *
 * @return bool
 */
function constant_contact_maybe_display_reCAPTCHA_notification() {
	return true;
}

/**
 * Handle the optin checkbox for the admin notice.
 *
 * @since 1.2.0
 */
function constant_contact_optin_ajax_handler() {

	$response = $_REQUEST;

	if ( ! isset( $response['optin'] ) || 'on' !== $response['optin'] ) {
		wp_send_json_success( array( 'opted-in' => 'off' ) );
	}

	$options = get_option( constant_contact()->settings->key );
	$options['_ctct_data_tracking'] = $response['optin'];
	update_option( constant_contact()->settings->key, $options );

	wp_send_json_success( array( 'opted-in' => 'on' ) );
	exit();
}
add_action( 'wp_ajax_constant_contact_optin_ajax_handler', 'constant_contact_optin_ajax_handler' );

/**
 * Handle the privacy policy agreement or disagreement selection.
 *
 * @since 1.2.0
 */
function constant_contact_privacy_ajax_handler() {

	$response = $_REQUEST;
	$agreed = sanitize_text_field( $response['privacy_agree'] );
	update_option( 'ctct_privacy_policy_status', $agreed );

	wp_send_json_success( array( 'updated' => 'true' ) );
	exit();
}
add_action( 'wp_ajax_constant_contact_privacy_ajax_handler', 'constant_contact_privacy_ajax_handler' );

/**
 * Handle the ajax for the review admin notice.
 *
 * @since 1.2.2
 */
function constant_contact_review_ajax_handler() {

	if ( isset( $_REQUEST['ctct-review-action'] ) ) {
		$action = strtolower( sanitize_text_field( $_REQUEST['ctct-review-action'] ) );

		switch ( $action ) {
			case 'dismissed':
				$dismissed          = get_option( 'ctct-review-dismissed', array() );
				$dismissed['time']  = time();
				if ( empty( $dismissed['count'] ) ) {
					$dismissed['count'] = '1';
				} elseif ( isset( $dismissed['count'] ) && '2' === $dismissed['count'] ) {
					$dismissed['count'] = '3';
				} else {
					$dismissed['count'] = '2';
				}
				update_option( 'ctct-review-dismissed', $dismissed );

				break;

			case 'reviewed':
				update_option( 'ctct-reviewed', 'true' );
				break;

			default:
				break;
		}
	}

	wp_send_json_success( array( 'review-action' => 'processed' ) );
	exit();
}
add_action( 'wp_ajax_constant_contact_review_ajax_handler', 'constant_contact_review_ajax_handler' );

/**
 * Process potential custom Constant Contact Forms action urls.
 *
 * @since 1.2.3
 */
function ctct_custom_form_action_processing() {
	if ( empty( $_POST ) || ! isset( $_POST['ctct-id'] ) ) {
		return false;
	}
	// Only run this if we have a custom action being filtered in.
	if ( ! constant_contact_has_redirect_uri( absint( $_POST['ctct-id'] ) ) ) {
		return false;
	}

	return constant_contact()->process_form->process_form();
}
add_action( 'wp_head', 'ctct_custom_form_action_processing' );

/**
 * Determine if we have any Constant Contact Forms published.
 *
 * @since 1.2.5
 *
 * @return bool
 */
function ctct_has_forms() {
	$args = array(
		'post_type'      => 'ctct_forms',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
	);
	$forms = new WP_Query( $args );
	return ( $forms->have_posts() );
}

/**
 * Whether or not there is a redirect URI meta value set for a form.
 *
 * @since 1.3.0
 *
 * @param int $form_id Form ID to check.
 * @return bool
 */
function constant_contact_has_redirect_uri( $form_id = 0 ) {
	$maybe_redirect_uri = get_post_meta( $form_id, '_ctct_redirect_uri', true );

	return empty( $maybe_redirect_uri ) ? false : true;
}

/**
 * Compare timestamps for rendered time vs current time.
 *
 * @since 1.3.2
 *
 * @param bool  $maybe_spam Whether or not an entry has been determined as spam.
 * @param array $data       Submitted form data.
 * @return bool
 */
function constant_contact_check_timestamps( $maybe_spam, $data ) {
	$current = time();
	$difference = $current - $data['ctct_time'];
	if ( $difference <= 5 ) {
		return true;
	}
	return $maybe_spam;
}
add_filter( 'constant_contact_maybe_spam', 'constant_contact_check_timestamps', 10, 2 );

/**
 * Clean and correctly protocol an given URL.
 *
 * @since 1.3.6
 *
 * @param string $url
 * @return string
 */
function constant_contact_clean_url( $url = '' ) {
	// Reject and return untouched if not provided a string.
	if ( ! is_string( $url ) ) {
		return $url;
	}

	$clean_url = esc_url( $url );
	if ( is_ssl() && 'http' === parse_url( $clean_url, PHP_URL_SCHEME ) ) {
		$clean_url = str_replace( 'http', 'https', $clean_url );
	}
	return $clean_url;
}

/**
 * Checks if we have our new debugging option enabled.
 *
 * @since 1.3.7
 *
 * @return bool
 */
function constant_contact_debugging_enabled() {
	$debugging_enabled = ctct_get_settings_option( '_ctct_logging' );

	return (
		( defined( 'CONSTANT_CONTACT_DEBUG_MAIL' ) && CONSTANT_CONTACT_DEBUG_MAIL ) ||
		'on' === $debugging_enabled
	);
}

/**
 * Potentially add an item to our custom error log.
 *
 * @since 1.3.7
 *
 * @param strint       $log_name   Component that the log item is for.
 * @param string       $error      The error to log.
 * @param mixed|string $extra_data Any extra data to add to the log.
 */
function constant_contact_maybe_log_it( $log_name, $error, $extra_data = '' ) {
	if ( ! constant_contact_debugging_enabled() ) {
		return;
	}

	$logger = new Logger( $log_name );
	$logger->pushHandler( new StreamHandler( constant_contact()->logger_location ) );
	$extra = [];

	if ( $extra_data ) {
		$extra = [ 'Extra information', [ $extra_data ] ];
	}
	// Log status of error.
	$logger->addInfo( $error, $extra );
}

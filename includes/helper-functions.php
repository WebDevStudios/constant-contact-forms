<?php
/**
 * Helper Functions for end-users to leverage when building themes or plugins.
 *
 * @package ConstantContact
 * @author Constant Contact
 * @since 1.0.0
 */

/**
 * Checks to see if a user is connected to Constant Contact or not.
 *
 * @since 1.0.0
 *
 * @return boolean Whether or not they are connected
 */
function constant_contact_is_connected() {
	return ( constant_contact()->api->is_connected() );
}

/**
 * Checks to see if a user is not connected to Constant Contact or not.
 *
 * @since 1.0.0
 *
 * @return boolean Whether or not they are NOT connected
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
 * @param $form_id Form ID to provide in the output.
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

	/*
	 * If clicked on review button already. Return false
	 *
	 * Create new UI-hidden review option in the options table.
	 *
	 * Update form submission count via process_form. Make sure
	 * it's as close to actual success as possible. Prevent false
	 * positives as much as possible.
	 *
	 * Update, via ajax, if the review button has been clicked.
	 * Update, via ajax, if the dismiss button has been clicked.
	 *
	 * If dismissed, save an array storing a count, and the current
	 * UTC time. If dismissed again(at 14 day mark), update time to
	 * current UTC value, increment count by 1.
	 */

	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}

	if ( ! constant_contact()->is_constant_contact() ) {
		return false;
	}

	// Fetch our oldest form available, that's published.
	$first_form = get_posts( array(
		'posts_per_page' => 1,
		'orderby'        => 'date',
		'order'          => 'ASC',
		'post_type'      => 'ctct_forms',
		'post_status'    => 'publish',
	) );

	// No published forms.
	if ( empty( $first_form ) ) {
		return false;
	}

	// Get our UTC timestamps for comparison.
	$post_date     = strtotime( $first_form[0]->post_date );
	$seven_days    = strtotime( '-7 days' );
	$fourteen_days = strtotime( '-14 days' );
	$thirty_days   = strtotime( '-30 days' );

	// Need to get dismissed count. If count = 1
	$first_dismissed_time = '';
	// Check if our first dismissal is older than 14 days.
	if ( $first_dismissed_time < $fourteen_days && true ) {
		return true;
	}

	// Need to get dismissed count. If count = 2
	$second_dismissed_time = '';
	// Check if our second dismissal is older than 30 days.
	if ( $second_dismissed_time < $thirty_days && true ) {
		return true;
	}

	// Needs to run last because at some point, this will always return.
	// Check if our post date for this form is older than 7 days.
	// Fetch option storing submission count.
	if ( $post_date < $seven_days && true ) {
		return true;
	}

	return false;
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

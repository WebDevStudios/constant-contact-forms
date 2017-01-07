<?php
/**
 * Helper Functions for end-users to leverage when building themes or plugins.
 *
 * @package ConstantContact
 * @author Constant Contact
 * @since 1.0.0
 */

/**
 * Checks to see if a user is connected to Constant Contact or not
 *
 * @since   1.0.0
 * @return  boolean  whether or not they are connected
 */
function constant_contact_is_connected() {
	return ( constant_contact()->api->is_connected() );
}

/**
 * Checks to see if a user is not connected to Constant Contact or not
 *
 * @since   1.0.0
 * @return  boolean  whether or not they are NOT connected
 */
function constant_contact_is_not_connected() {
	return ! ( constant_contact()->api->is_connected() );
}

/**
 * Get a form's markup without using a shortcode
 *
 * @since 1.0.0
 * @param int $form_id Form post ID to grab.
 * @return  string            HTML markup
 */
function constant_contact_get_form( $form_id ) {
	return constant_contact()->display_shortcode->get_form( $form_id );
}

/**
 * Get a form and display it without using a shortcode
 *
 * @since 1.0.0
 * @param int $form_id Form post ID to grab.
 */
function constant_contact_display_form( $form_id ) {
	constant_contact()->display_shortcode->display_form( $form_id );
}

/**
 * Get an array of forms
 *
 * @since   1.0.0
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

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

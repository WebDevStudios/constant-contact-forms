<?php
/**
 * Helper Functions for end-users to leverage when building themes or plugins.
 *
 * @package ConstantContact
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Get a form's markup without using a shortcode
 *
 * @since   1.0.0
 * @param   int  $form_id  form post ID to grab
 * @return  string            HTML markup
 */
function constant_contact_get_form( $form_id ) {
	return constant_contact()->display_shortcode->get_form( $form_id );
}

/**
 * Get a form and display it without using a shortcode
 *
 * @since   1.0.0
 * @param   int  $form_id  form post ID to grab
 */
function constant_contact_display_form( $form_id ) {
	constant_contact()->display_shortcode->display_form( $form_id );
}

/**
 * Get an array of forms
 *
 * @since   1.0.0
 * @return  array  WP Query results of forms
 */
function constant_contact_get_forms() {
	return constant_contact()->cpts->get_forms( false, true );
}

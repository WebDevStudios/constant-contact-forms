<?php
/**
 * Fixes for compatibility issues with other plug-ins.
 *
 * @package ConstantContact
 * @author  Constant Contact
 * @since   NEXT
 */

/**
 * Add the Constant Contact post type to an array of excluded post types.
 *
 * @since NEXT
 *
 * @param array $excluded The post types to exclude.
 * @return array
 */
function constant_contact_exclude_ctct_forms( $excluded ) {
	$excluded[] = 'ctct_forms';
	return $excluded;
}

// If the WordPress Calls to Action plug-in is installed, exclude our post type to conflict.
add_filter( 'cta_excluded_post_types', 'constant_contact_exclude_ctct_forms' );

/**
 * Ignore the field added by Popup Maker from being processed by Constant Contact.
 *
 * @since 1.4.0
 *
 * @param array $ignored The array of fields that Constant Contact should ignore.
 * @return array
 */
function constant_contact_exclude_pum( $ignored ) {
	$ignored[] = 'pum_form_popup_id';

	return $ignored;
}
add_filter( 'constant_contact_ignored_post_form_values', 'constant_contact_exclude_pum' );

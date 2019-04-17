<?php
/**
 * Fixes for compatibility issues with other plug-ins.
 *
 * @package ConstantContact
 * @author  Constant Contact
 * @since   1.4.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Add the Constant Contact post type to an array of excluded post types.
 *
 * @since 1.4.0
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

/**
 * Filter out fields we do not want in our form.
 *
 * Function originally coded specifically to handle WP-SpamShield $_POST values.
 *
 * @since 1.5.0
 *
 * @param array $ignored_keys Keys to ignore for API requests.
 * @param int   $form_id      Current form ID being processed.
 *
 * @return array
 */
function constant_contact_wpspamshield_compatibility( $ignored_keys = [], $form_id = 0 ) {
	/*
	 * Standard form name insertion into array will not work with WP-SpamShield because
	 * those values change periodically, from my experiences and support time. This solution
	 * is a little bit of a hammer, but it appears to be working.
	 *
	 * May also prove to be a way forward to only ever having to deal with our intended values
	 * instead of having to manually ignore. Something that should have been done from the start.
	 */

	if ( ! defined( 'WPSS_VERSION' ) ) {
		return $ignored_keys;
	}

	// Need to assign a value so that the strings are associative keys.
	$misc_keys = [
		'ctct-opt-in' => [],
		'ctct-id'     => [],
	];

	// Grab all the original fields from our form.
	$original_fields = constant_contact()->process_form->get_original_fields( $form_id );

	// This will merge our two misc keys above with our original fields, and
	// then return just their keys.
	$good_keys = array_keys( array_merge( $misc_keys, $original_fields ) );

	// This will grab all of the keys from the global $_POST, and then assign
	// the difference between that and our intended keys.
	$bad_keys = array_diff( array_keys( $_POST ), $good_keys ); // WPCS: CSRF ok.

	// This will merge the passed ignored keys with our newly found bad keys,
	// and then return all the unique values for our return value.
	$new_ignore_keys = array_unique( array_merge( $ignored_keys, $bad_keys ) );

	return $new_ignore_keys;
}
add_filter( 'constant_contact_ignored_post_form_values', 'constant_contact_wpspamshield_compatibility', 10, 2 );

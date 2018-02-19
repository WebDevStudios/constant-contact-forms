<?php
/**
 * Fixes for compatibility issues with other plug-ins.
 *
 * @package ConstantContact
 * @author  Constant Contact
 * @since   1.3.6
 */

/**
 * Add the Constant Contact post type to an array of excluded post types.
 *
 * @since NEXT
 *
 * @param array $excluded The post types to exclude.
 * @return array
 */
function exclude_ctct_forms( $excluded ) {
	$excluded[] = 'ctct_forms';
	return $excluded;
}

// If the WordPress Calls to Action plug-in is installed, exclude our post type to conflict.
add_filter( 'cta_excluded_post_types', 'exclude_ctct_forms' );

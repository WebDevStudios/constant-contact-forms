<?php
/**
 * Deprecated functions.
 *
 * @package Constantcontact
 * @author  Constant Contact
 * @since   NEXT
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 * phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- Incorrectly prefixed functions have been deprecated.
 */

/**
 * Wrapper function around cmb2_get_option.
 *
 * @deprecated NEXT Deprecated in favor of properly-prefixed function name.
 *
 * @since  1.0.0
 *
 * @param  string $key     Options array key.
 * @param  string $default Default value if no option exists.
 * @return mixed           Option value.
 */
function ctct_get_settings_option( $key = '', $default = null ) {
	_deprecated_function( __FUNCTION__, 'NEXT', 'constant_contact_get_option' );

	return constant_contact_get_option( $key, $default );
}

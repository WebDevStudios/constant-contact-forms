<?php
/**
 * Deprecated functions.
 *
 * @package Constantcontact
 * @author  Constant Contact
 * @since   NEXT
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
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
function ctct_get_settings_option( $key = '', $default = null ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- Function is prefixed.
	_deprecated_function( __FUNCTION__, 'NEXT', 'constant_contact_get_option' );

	return constant_contact_get_option( $key, $default );
}

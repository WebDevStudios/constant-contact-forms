<?php
/**
 * Auth Redirect
 *
 * @package ConstantContactConnect
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Add auth params to query_vars
 *
 * @param array $vars url params.
 */
function constantcontact_rewrite_add_var( $vars ) {
	$vars[] = 'auth';
	$vars[] = 'code';
	$vars[] = 'username';
	return $vars;
}
add_filter( 'query_vars', 'constantcontact_rewrite_add_var' );

/**
 * Check for query params and redirect
 *
 * @return boolean
 */
function constantcontact_rewrite_catch() {
	global $wp_query;

	// Only run if logged in user can manage site options.
	if ( ! current_user_can( 'manage_options' ) ) { return false; }

	if ( isset( $wp_query->query_vars['code'] ) && 'ctct' === $wp_query->query_vars['auth'] && ! is_admin() ) {

		// Create a redirect back to connect page.
		$path = add_query_arg( array(
			'post_type' => 'ctct_forms',
			'page' => 'ctct_options_connect',
			'code' => sanitize_text_field( $wp_query->query_vars['code'] ),
			'user' => sanitize_text_field( $wp_query->query_vars['username'] ),
		) );
		wp_safe_redirect( admin_url( 'edit.php' . $path ) );
		exit;
	}
}
add_action( 'template_redirect', 'constantcontact_rewrite_catch' );

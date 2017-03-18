<?php
/**
 * Authentication Redirection.
 *
 * @package ConstantContact
 * @subpackage AuthRedirect
 * @author Constant Contact
 * @since 1.0.0
 */

/**
 * Hooks in to allow for our query args for connecting to Constant Contact, and helps with authentication to it.
 *
 * @since 1.0.0
 */
class ConstantContact_Auth_Redirect {

	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $plugin Parent plugin.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function hooks() {
		add_filter( 'query_vars', array( $this, 'rewrite_add_var' ) );
		add_action( 'template_redirect', array( $this, 'rewrite_catch' ) );
	}

	/**
	 * Add auth params to query_vars.
	 *
	 * @since 1.0.0
	 *
	 * @param array $vars url params.
	 * @return array
	 */
	function rewrite_add_var( $vars ) {
		$vars[] = 'auth';
		$vars[] = 'code';
		$vars[] = 'username';
		return $vars;
	}

	/**
	 * Check for query params and redirect.
	 *
	 * @since 1.0.0
	 */
	function rewrite_catch() {
		global $wp_query;

		if (
			current_user_can( 'manage_options' ) &&
			! is_admin() &&
			isset( $wp_query ) &&
			$wp_query &&
			isset( $wp_query->query_vars ) &&
			is_array( $wp_query->query_vars ) &&
			isset( $wp_query->query_vars['code'] ) &&
			isset( $wp_query->query_vars['auth'] ) &&
			isset( $wp_query->query_vars['username'] ) &&
			'ctct' === $wp_query->query_vars['auth']
		) {

			// Create a redirect back to connect page.
			wp_safe_redirect( add_query_arg( array(
				'post_type' => 'ctct_forms',
				'page'      => 'ctct_options_connect',
				'code'      => sanitize_text_field( $wp_query->query_vars['code'] ),
				'user'      => sanitize_text_field( $wp_query->query_vars['username'] ),
			), admin_url( 'edit.php' ) ) );
			exit;
		}
	}
}

<?php
/**
 * ConstantContact_Auth_Redirect class
 *
 * @package ConstantContactProcessForm
 * @subpackage ConstantContact
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Class ConstantContact_Auth_Redirect
 */
class ConstantContact_Auth_Redirect {

	/**
	 * Parent plugin class
	 *
	 * @var   class
	 * @since 0.0.1
	 */
	protected $plugin = null;

	/**
	 * Constructor
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function hooks() {
		add_filter( 'query_vars', array( $this, 'rewrite_add_var' ) );
		add_action( 'template_redirect', array( $this, 'rewrite_catch' ) );
	}

	/**
	 * Add auth params to query_vars
	 *
	 * @param array $vars url params.
	 */
	function rewrite_add_var( $vars ) {
		$vars[] = 'auth';
		$vars[] = 'code';
		$vars[] = 'username';
		return $vars;
	}

	/**
	 * Check for query params and redirect
	 *
	 * @return boolean
	 */
	function rewrite_catch() {
		global $wp_query;

		// So many data checks!
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

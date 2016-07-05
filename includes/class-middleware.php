<?php
/**
 * Constant Contact Middleware
 *
 * @since 1.0.1
 * @package Constant Contact
 */

/**
 * Constant Contact Middleware.
 *
 * @since 1.0.1
 */
class ConstantContact_Middleware {
	/**
	 * Parent plugin class
	 *
	 * @var   class
	 * @since 1.0.1
	 */
	protected $plugin = null;

	/**
	 * Constructor
	 *
	 * @since  1.0.1
	 * @param  object $plugin Main plugin object.
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks
	 *
	 * @since  1.0.1
	 * @return void
	 */
	public function hooks() {
	}

	public function main() {

		// oh ugly I know
		echo '<br><br><br><br><br>';

		$middleware = $this->get_middleware_link();

		if ( ! $middleware ) {
			return;
		}

		$middleware = $this->add_query_args_to_link( $middleware );

		echo $this->link_it( $middleware );
	}

	public function add_query_args_to_link( $link ) {
		return add_query_arg( array(
			'ctct-auth'  => 'auth',
			'ctct-proof' => $this->get_proof(),
			'ctct-site'  => get_site_url(),
			),
		$link );
	}

	public function get_proof() {
		return 'this-is-my-proof-key';
	}

	public function get_middleware_link() {
		$options = get_option( 'ctct_options_settings' );
		return isset( $options['_ctct_middleware'] ) ? $options['_ctct_middleware'] : false;
	}

	public function link_it( $link ) {
		return '<a href="' . $link . '" target="_blank">' . $link . '</a>';
	}
}

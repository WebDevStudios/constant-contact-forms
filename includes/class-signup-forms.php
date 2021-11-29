<?php
/**
 * Sign-up Forms
 *
 * @package ConstantContact
 * @subpackage Beaver Builder
 * @author Constant Contact
 * @since NEXT
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * This class get's everything up an running for Sign-up Forms.
 *
 * @since NEXT
 */
class ConstantContact_Signup_Forms {

	/**
	 * Parent plugin class.
	 *
	 * @since NEXT
	 * @var object
	 */
	protected $plugin;

	/**
	 * Constructor.
	 *
	 * @since NEXT
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
	 */
	public function hooks() {
		add_action( 'wp_head', [ $this, 'inject_universal_code' ] );
	}

    /**
	 * Attempt to inject Universal Code of Sign-up Form.
	 *
	 * @author Scott Anderson <scott.anderson@webdevstudios.com>
	 * @since  NEXT
	 *
	 * @return void
	 */
    public function inject_universal_code() : void {
        $universal_code         = constant_contact_get_option( '_ctct_signup_universal_code', '' );
		$disable_universal_code = constant_contact_get_option( '_ctct_signup_uc_disable', 'off' );

        if ( '' === $universal_code || 'on' === $disable_universal_code ) {
            return;
        }
        echo $universal_code;
    }

}

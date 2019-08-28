<?php
/**
 * Updates
 *
 * @package ConstantContact
 * @subpackage Updates
 * @author Constant Contact
 * @since 1.0.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Powers any update version-to-version functionality we need.
 *
 * @since 1.0.0
 */
class ConstantContact_Updates {

	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	protected $plugin;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $plugin Plugin to store.
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
		if ( is_admin() ) {
			add_action( 'plugins_loaded', [ $this, 'check_for_update_needed' ] );
		}
	}

	/**
	 * Checks our current version of the plugin and what our last installed
	 * version was. If necessary, will fire update functions that need to fire.
	 *
	 * @since 1.0.0
	 */
	public function check_for_update_needed() {

		$installed = get_option( 'ctct_plugin_version', '0.0.0' );
		$current   = esc_attr( $this->plugin->version );

		if ( ! version_compare( $current, $installed, '<' ) ) {

			update_option( 'ctct_plugin_version', $current, true );

			// Convert our installed / current version to something we can use
			// in a function name.
			$installed = sanitize_title( str_replace( '.', '_', $installed ) );
			$current   = sanitize_title( str_replace( '.', '_', $current ) );

			// Build up an update method function to call if we need it
			// this will create something like: run_update_v0_0_0_to_v1_0_1
			// which will then get run if it needs to.
			$method_to_call = [ $this, esc_attr( 'run_update_v' . $installed . '_to_v' . $current ) ];

			// If we can call our update function, then call it, passing in 'v1_0_0' as argument.
			if ( is_callable( $method_to_call ) ) {
				call_user_func_array( $method_to_call, [ 'v' . $current ] );
			}
		}
	}

	/**
	 * If we have an update that requires surfacing a notification to the user,
	 * let queue it up for display later at some point.
	 *
	 * @since 1.0.0
	 *
	 * @param string $update_id Update key to use for version.
	 */
	public function add_notification( $update_id ) {

		$current_notifs = get_option( 'ctct_update_notifications', [] );
		$compare_notifs = $current_notifs;

		if ( ! is_array( $current_notifs ) ) {
			$current_notifs = [];
		}

		// Set up our update notif ID to use.
		$notif_id = 'update-' . str_replace( '_', '-', esc_attr( $update_id ) );

		// Tack on our new update notifications.
		$current_notifs[ $notif_id ] = [
			'ID'       => $notif_id,
			'callback' => [ 'ConstantContact_Notification_Content', esc_attr( $update_id ) ],
		];

		if ( $compare_notifs !== $current_notifs ) {
			update_option( 'ctct_update_notifications', $current_notifs );
		}
	}

}

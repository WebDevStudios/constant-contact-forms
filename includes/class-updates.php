<?php
/**
 * Updates
 *
 * @package ConstantContact
 * @subpackage Updates
 * @author Constant Contact
 * @since 1.0.0
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
	protected $plugin = null;

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
	 *
	 * @return void
	 */
	public function hooks() {

		// Check to make sure we don't need to run any update functionality, but only in the admin.
		if ( is_admin() ) {
			add_action( 'plugins_loaded', array( $this, 'check_for_update_needed' ) );
		}
	}

	/**
	 * Checks our current version of the plugin and what our last installed
	 * version was. If necessary, will fire update functions that need to fire.
	 *
	 * @since 1.0.0
	 */
	public function check_for_update_needed() {

		// Grab what our current version in the DB is saved as.
		$installed = get_option( 'ctct_plugin_version', '0.0.0' );
		$current = esc_attr( $this->plugin->version );

		if ( ! version_compare( $current, $installed, '<' ) ) {

			// Update our DB option to the current plugin version.
			update_option( 'ctct_plugin_version', $current, true );

			// Convert our installed / current version to something we can use
			// in a function name.
			$installed = sanitize_title( str_replace( '.', '_', $installed ) );
			$current = sanitize_title( str_replace( '.', '_', $current ) );

			// Build up an update method function to call if we need it
			// this will create something like: run_update_v0_0_0_to_v1_0_1
			// which will then get run if it needs to.
			$method_to_call = array( $this, esc_attr( 'run_update_v' . $installed . '_to_v' . $current ) );

			// If we can call our update function, then call it, passing in 'v1_0_0' as argument.
			if ( is_callable( $method_to_call ) ) {
				call_user_func_array( $method_to_call, array( 'v' . $current ) );
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

		// Get our current saved update notifications.
		$current_notifs = get_option( 'ctct_update_notifications' );
		$compare_notifs = $current_notifs;

		// If its not an array, cast it as one.
		if ( ! is_array( $current_notifs ) ) {
			$current_notifs = array();
		}

		// Set up our update notif ID to use.
		$notif_id = 'update-' . str_replace( '_', '-', esc_attr( $update_id ) );

		// Tack on our new update notifications.
		$current_notifs[ $notif_id ] = array(
			'ID'       => $notif_id,
			'callback' => array( 'ConstantContact_Notification_Content', esc_attr( $update_id ) ),
		);

		// Re-save it if we actually did add one.
		if ( $compare_notifs !== $current_notifs ) {
			update_option( 'ctct_update_notifications', $current_notifs );
		}
	}


	/**
	 * Sample update scaffolding for 1.0.0 -> 1.0.1 update.
	 *
	 * @since 1.0.0
	 *
	 * @param string $version Version updating to.
	 */
	public function run_update_v1_0_0_to_v1_0_1( $version ) {

		// If we need a notification, then we add it in this way.
		// Example of adding a notification to an update. The @codingStand.. line
		// can be removed as this is to suppress commented code warnings in PHPCS
		// @codingStandardsIgnoreLine
		// $this->add_notification( $version );
		// Here we would run any other necessary update code.
	}

	/**
	 * For 0.0.0 to 1.0.1, we just run our 1.0.0 to 1.0.1.
	 * We currently need this in place for the above method, as a user
	 * doesn't always update 0.0.0->1.0.0->1.0.1, might be 0.0.0->1.0.1
	 *
	 * @since 1.0.0
	 *
	 * @param string $version Version updating to.
	 */
	public function run_update_v0_0_0_to_v1_0_1( $version ) {
		$this->run_update_v1_0_0_to_v1_0_1( $version );
	}
}

<?php
/**
 * ConstantContact_Lists class
 *
 * @package ConstantContactLists
 * @subpackage ConstantContact
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Class ConstantContact_Lists
 */
class ConstantContact_Lists {

	/**
	 * Holds an instance of the object.
	 *
	 * @since 1.0.0
	 * @var ConstantContact_Lists
	 */
	private static $instance = null;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
	}

	/**
	 * Returns the running object
	 *
	 * @since 1.0.0
	 *
	 * @return ConstantContact_Lists
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new ConstantContact_Lists();
			self::$instance->hooks();
		}
		return self::$instance;
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'init' ) );
	}


	/**
	 * Hooked to WP init.
	 *
	 * @since 1.0.0
	 */
	public function init() {
	}

}

/**
 * Helper function to get/return the ConstantContact_Lists object.
 *
 * @since 1.0.0
 *
 * @return ConstantContact_Lists object.
 */
function constantcontact_lists() {
	return ConstantContact_Lists::get_instance();
}

// Get it started.
constantcontact_lists();

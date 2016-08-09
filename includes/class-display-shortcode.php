<?php
/**
 * ConstantContact_Display_Shortcode class
 *
 * @package ConstantContact_Display_Shortcode
 * @subpackage ConstantContact
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Class ConstantContact_Display_Shortcode
 */
class ConstantContact_Display_Shortcode {

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
	}
}

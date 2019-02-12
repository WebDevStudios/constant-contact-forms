<?php
/**
 * Gutenberg Support
 *
 * @package ConstantContact
 * @subpackage Gutenberg
 * @author Constant Contact
 * @since 1.5.0
 */

/**
 * This class get's everything up an running for Gutenberg support.
 *
 * @since 1.5.0
 */
class ConstantContact_Gutenberg {

	/**
	 * Parent plugin class.
	 *
	 * @since 1.5.0
	 * @var object
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since 1.5.0
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
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public function hooks() {
		// Hooks go here.
	}
}

<?php
/**
 * ConstantContact_Display class
 *
 * @package ConstantContactProcessForm
 * @subpackage ConstantContact
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Class ConstantContact_Display
 */
class ConstantContact_Display {

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
	 * @since  0.0.1
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Helper method to display form description
	 *
	 * @author Brad Parbs
	 * @param  string $description description to outpu
	 * @return echo              echos out form description markup
	 */
	public function description( $description ) {
		echo '<p class="constant-contact constant-contact-form-description">' . esc_attr( $description ) . '</p>';
	}
}


<?php
/**
 * Elementor Support
 *
 * @package ConstantContact
 * @subpackage Elementor
 * @author Constant Contact
 * @since 1.11.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * This class get's everything up an running for Elementor support.
 *
 * @since 1.11.0
 */
class ConstantContact_Elementor {

	/**
	 * Parent plugin class.
	 *
	 * @since 1.11.0
	 * @var object
	 */
	protected $plugin;

	/**
	 * Constructor.
	 *
	 * @since 1.11.0
	 *
	 * @param object $plugin Parent plugin.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Register Hooks
	 *
	 * @since 1.10.0
	 */
	private function hooks() {
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widget' ] );
	}

	/**
	 * Registers all Elementor Widgets
	 *
	 * @since 1.10.0
	 */
	public function register_widget() {
		require_once __DIR__ . '/widgets/elementor-widget.php';
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new ConstantContact_Elementor_Widget() );
	}

}

<?php
/**
 * Beaver Builder Support
 *
 * @package ConstantContact
 * @subpackage Beaver Builder
 * @author Constant Contact
 * @since 1.11.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * This class get's everything up an running for Beaver Builder support.
 *
 * @since 1.11.0
 */
class ConstantContact_Beaver_Builder {

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
		if ( class_exists( 'FLBuilder' ) ) {
			add_action( 'init', [ $this, 'register_bb_modules' ] );
		}
	}

	/**
	 * Registers Beaver Builder Modules
	 *
	 * @since 1.10.0
	 */
	public function register_bb_modules() {
		FLBuilder::register_module(
			'CCForm',
			[
				'ccm-tab-1' => [
					'title'    => esc_html__( 'Settings', 'constant-contact-forms' ),
					'sections' => [
						'ccm-section-1' => [
							'title'  => esc_html__( 'Settings', 'constant-contact-forms' ),
							'fields' => [
								'cc_display_title' => [
									'type'    => 'select',
									'label'   => esc_html__( 'Title', 'constant-contact-forms' ),
									'default' => 'true',
									'options' => [
										'true'  => esc_html__( 'Display', 'constant-contact-forms' ),
										'false' => esc_html__( 'Hide', 'constant-contact-forms' ),
									],
								],
								'cc_form'          => [
									'type'    => 'select',
									'label'   => __( 'Form', 'constant-contact-forms' ),
									'options' => $this->get_form_options(),
								],
							],
						],
					],
				],
			]
		);
	}

	/**
	 * Provides all Constant Contact Forms current Published.
	 *
	 * @since 1.10.0
	 */
	private function get_form_options() {

		$options = [];

		$forms = get_posts(
			[
				'post_type'   => 'ctct_forms',
				'post_status' => 'publish',
				'numberposts' => -1,
			]
		);

		foreach ( $forms as $form ) {
			$options[ $form->ID ] = $form->post_title;
		}

		if ( empty( $options ) ) {
			$options[''] = esc_html__( 'No forms currently published.', 'constant-contact-forms' );
		}

		return $options;
	}

}

class CCForm extends FLBuilderModule {
	public function __construct() {
		parent::__construct(
			[
				'name'        => esc_html__( 'Constant Contact Form', 'constant-contact-forms' ),
				'description' => esc_html__( 'Display a Constant Contact Form', 'constant-contact-forms' ),
				'category'    => esc_html__( 'Constant Contact', 'constant-contact-forms' ),
				'dir'         => __DIR__ . '/cc-modules/form/',
				'url'         => __DIR__ . '/cc-modules/form/',
			]
		);
	}
}

<?php
/**
 * Beaver Builder Support
 *
 * @package ConstantContact
 * @subpackage Beaver Builder
 * @author Constant Contact
 * @since NEXT
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * This class get's everything up an running for Beaver Builder support.
 *
 * @since NEXT
 */
class ConstantContact_Beaver_Builder {

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
		if ( class_exists( 'FLBuilder' ) ) {
			add_action( 'init', [ $this, 'register_bb_modules'] );
		}
	}

	/**
	 * Registers Beaver Builder Modules
	 *
	 * @since  NEXT
	 */
	public function register_bb_modules() {
		FLBuilder::register_module( 'CCForm', array(
			'ccm-tab-1'      => array(
				'title'         => __( 'Settings', 'constant-contact-forms' ),
				'sections'      => array(
					'ccm-section-1'  => array(
						'title'         => __( 'Settings', 'constant-contact-forms' ),
						'fields'        => array(
							'cc_display_title'     => array(
								'type'          => 'select',
								'label'         => __('Title', 'constant-contact-forms'),
								'default'       => 'true',
								'options'       => array(
									'true'      => __('Display', 'constant-contact-forms'),
									'false'     => __('Hide', 'constant-contact-forms')
								),
							),
							'cc_form'     => array(
								'type'          => 'select',
								'label'         => __('Form', 'constant-contact-forms'),
								'options'       => $this->get_form_options(),
							),
						)
					)
				)
			)
		) );
	}

	/**
	 * Provides all Constant Contact Forms current Published.
	 *
	 * @since  NEXT
	 */
	private function get_form_options () {

		$options = [

		];

		$forms = get_posts( [
			'post_type' => 'ctct_forms',
			'post_status' => 'publish',
			'numberposts' => -1
		] );

		foreach ( $forms as $form ) {
			$options[ $form->ID ] = $form->post_title;
		}

		if ( empty( $options ) ) {
			$options[ '' ] = __( 'No forms currently published.', 'constant-contact-forms' );
		}

		return $options;
	}

}

class CCForm extends FLBuilderModule {
	public function __construct()
	{
		parent::__construct( array(
			'name'            => __( 'Constant Contact Form', 'constant-contact-forms' ),
			'description'     => __( 'Display a Constant Contact Form', 'constant-contact-forms' ),
			'category'        => __( 'Constant Contact', 'constant-contact-forms' ),
			'dir'             => __DIR__ . '/cc-modules/form/',
			'url'             => __DIR__ . '/cc-modules/form/',
		) );
	}
}

<?php
/**
 * Elementor Widget
 *
 * @package ConstantContact
 * @subpackage Elementor
 * @author Constant Contact
 * @since NEXT
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * This class get's everything up an running for Elementor Widget.
 *
 * @since NEXT
 */
class ConstantContact_Elementor_Widget extends \Elementor\Widget_Base {

	/**
	 * Widgets Name
	 *
	 * @since  NEXT
	 */
	public function get_name() {
		return 'constant-contact';
	}
	
	/**
	 * Widgets Title
	 *
	 * @since  NEXT
	 */
	public function get_title() {
		return 'Constant Contact Form';
	}

	/**
	 * Widgets Icon
	 *
	 * @since  NEXT
	 */
	public function get_icon() {
		return 'eicon-form-horizontal';
	}
	
	/**
	 * Widgets Category
	 *
	 * @since  NEXT
	 */
	public function get_categories() {
		return [ 'basic' ];
	}
	
	/**
	 * Displays Widget Controls.
	 *
	 * @since  NEXT
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'section_title',
			[
				'label' => __( 'Constant Contact Form Settings', 'constant-contact-forms' ),
			]
		);
		
		$this->add_control(
			'show_title',
			[
				'label'        => __( 'Show Title', 'constant-contact-forms' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'constant-contact-forms' ),
				'label_off'    => __( 'Hide', 'constant-contact-forms' ),
				'return_value' => true,
				'default'      => true,
			]
		);

		$this->add_control(
			'form_id',
			[
				'label' => __( 'Form', 'constant-contact-forms' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $this->get_form_options(),
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Provides all Constant Contact Forms current Published.
	 *
	 * @since  NEXT
	 */
	private function get_form_options () {

		$options = [

		];

		$forms = get_posts([
			'post_type' => 'ctct_forms',
			'post_status' => 'publish',
			'numberposts' => -1
		]);

		foreach ( $forms as $form ) {
			$options[ $form->ID ] = $form->post_title;
		}

		if ( empty( $options ) ) {
			$options[ '' ] = __( 'No forms currently published.', 'constant-contact-forms' );
		}

		return $options;
	}

	/**
	 * Displays Widget
	 *
	 * @since  NEXT
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['form_id'] ) ) {
			echo __( 'Please select a form.', 'constant-contact-forms' );
			return;
		}

		$show_title = $settings['show_title'] ? 'true' : 'false';
		echo do_shortcode( "[ctct form='{$settings['form_id']}' show_title='{$show_title}']" );
	}
	
}

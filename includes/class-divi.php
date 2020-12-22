<?php
/**
 * Divi Support
 *
 * @package ConstantContact
 * @subpackage Divi
 * @author Constant Contact
 * @since NEXT
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

use ET_Builder_Module;

/**
 * This class get's everything up an running for Divi support.
 *
 * @since NEXT
 */
class ConstantContact_Divi extends ET_Builder_Module {

	/**
	 * The module slug.
	 *
	 * @var string $slug
	 */
	public $slug = 'cc_contact_form';

	/**
	 * Visual Builder support
	 *
	 * @var string $vb_support
	 */
	public $vb_support = 'on';

	/**
	 * Whitelisted fields.
	 *
	 * @var array $whitelisted_fields
	 */
	public $whitelisted_fields = [];

	/**
	 * Field defaults.
	 *
	 * @var    array $fields_defaults
	 */
	public $fields_defaults = [];

	/**
	 * Advanced options.
	 *
	 * @var array $advanced_options
	 */
	public $advanced_options = [];

	/**
	 * Option toggles.
	 *
	 * @var array $options_toggles
	 */
	public $options_toggles = [];

	/**
	 * Function init()
	 */
	function init() {
		$this->name = esc_html__( 'Constant Contact Forms', 'constant-contact-forms' );

		$this->whitelisted_fields = [
			'show_title',
			'cc_form'
		];

		$this->main_css_element = '%%order_class%% .cc_forms';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Content', 'constant-contact-forms' ),
					'elements'     => esc_html__( 'Elements', 'constant-contact-forms' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout' => esc_html__( 'Layout', 'constant-contact-forms' ),
					'text'   => array(
						'title'    => esc_html__( 'Text', 'constant-contact-forms' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->fields_defaults = [
			'show_title' => [ 'on' ]
		];
	}

	/**
	 * Returns the module fields.
	 *
	 * @return array The fields.
	 */
	function get_fields() {
		return array(
			'show_title'               => array(
				'label'            => esc_html__( 'Show Title', 'constant-contact-forms' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'true'  => esc_html__( 'Yes', 'constant-contact-forms' ),
					'false' => esc_html__( 'No', 'constant-contact-forms' ),
				),
				'description'      => esc_html__( 'This will turn title on and off.', 'constant-contact-forms' ),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'elements',
			),
			'cc_form'            => array(
				'label'           => esc_html__( 'Constant Contact Form', 'constant-contact-forms' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'Select a form.', 'constant-contact-forms' ),
				'options'         => $this->get_form_options(),
				'toggle_slug'     => 'elements',
			),
		);
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
			$options[ '' ] = esc_html__( 'No forms currently published.', 'constant-contact-forms' );
		}

		return $options;
	}

	/**
	 * Render Form.
	 *
	 * @param array  $unprocessed_props The unprocessed props.
	 * @param null   $content           The content.
	 * @param string $render_slug       The render slug.
	 *
	 * @return string The content.
	 */
	public function render( $unprocessed_props, $content = null, $render_slug = '' ) {

		$settings = $this->get_settings_for_display();

		if ( empty( $this->props['cc_form'] ) ) {
			echo esc_html__( 'Please select a form.', 'constant-contact-forms' );
			return;
		}

		echo do_shortcode( "[ctct form='{$this->props['cc_form']}' show_title='{$this->props['show_title']}']" );
	}


}

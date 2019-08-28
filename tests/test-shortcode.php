<?php
/**
 * Tests the ConstantContact_Shortcode class.
 *
 * @package ConstantContact
 * @subpackage Display
 * @since 1.6.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Tests the ConstantContact_Shortcode class.
 *
 * @since 1.6.0
 */
class ConstantContact_Shortcode_Test extends WP_UnitTestCase {

	/**
	 * Set up.
	 *
	 * @since 1.6.0
	 */
	public function setUp() {
		parent::setUp();
		$this->plugin = constant_contact();
	}

	/**
	 * Should get default shortcode atts values.
	 *
	 * @since 1.6.0
	 *
	 * @test
	 */
	public function should_get_default_shortcode_atts_values() {

		$expected = [
			'form'       => '0',
			'show_title' => 'false',
		];

		$actual = $this->plugin->shortcode->get_atts();

		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Should get filtered shortcode atts values.
	 *
	 * @since 1.6.0
	 *
	 * @test
	 */
	public function should_get_filtered_shortcode_atts_values() {

		// Ensure WP Core's 'shortcode_atts_{tag}' filter works to set att values and even add new atts.
		add_filter( 'shortcode_atts_ctct', function( $atts ) {
			$atts['form']       = '12';
			$atts['show_title'] = 'true';
			$atts['foo']        = 'bar';

			return $atts;
		} );

		$expected = [
			'form'       => '12',
			'show_title' => 'true',
			'foo'        => 'bar',
		];

		// Using an empty array here because we just want to know the filter-provided values are working.
		$actual = shortcode_atts( $this->plugin->shortcode->get_atts(), [], 'ctct' );

		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Should clear forms list transient on save post.
	 *
	 * @since 1.6.0
	 *
	 * @test
	 */
	public function should_clear_forms_list_transient_on_save_post() {
		$test_post_id = $this->factory->post->create( [ 'post_content' => 'Hi here is the form: [ctct form="12" show_title="true"]' ] );

		set_transient( ConstantContact_Shortcode::FORMS_LIST_TRANSIENT, [ 'form1', 'form2' ], 1 * WEEK_IN_SECONDS );

		$forms_list = get_transient( ConstantContact_Shortcode::FORMS_LIST_TRANSIENT );

		// Multiple assertions in a method is bad, but we want to confirm the transient was set so that the next assertion is meaningful.
		$this->assertIsArray( $forms_list );

		// Trigger save_post.
		wp_update_post( [
			'ID'         => $test_post_id,
			'post_title' => 'We are now updating the post to trigger save_post action.',
		] );

		$should_be_false = get_transient( ConstantContact_Shortcode::FORMS_LIST_TRANSIENT );

		$this->assertFalse( $should_be_false );
	}
}

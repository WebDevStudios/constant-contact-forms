<?php
/**
 * @package ConstantContact_Tests
 * @subpackage DisplayShortcode
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Display_Shortcode_Test extends WP_UnitTestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Display_Shortcode' ) );
	}

	function test_class_access() {
		$this->assertTrue( constant_contact()->display_shortcode instanceof ConstantContact_Display_Shortcode );
	}

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}
}

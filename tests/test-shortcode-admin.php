<?php
/**
 * @package ConstantContact_Tests
 * @subpackage ShortcodeAdmin
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Shortcode_Admin_Test extends WP_UnitTestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Shortcode_Admin' ) );
	}

	function test_class_access() {
		$this->assertTrue( constant_contact()->shortcode_admin instanceof ConstantContact_Shortcode_Admin );
	}

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}
}

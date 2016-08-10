<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Settings
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Settings_Test extends WP_UnitTestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Settings' ) );
	}

	function test_class_access() {
		$this->assertTrue( constant_contact()->settings instanceof ConstantContact_Settings );
	}

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}
}

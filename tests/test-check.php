<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Check
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Check_Test extends WP_UnitTestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Check' ) );
	}

	function test_class_access() {
		$this->assertTrue( constant_contact()->check instanceof ConstantContact_Check );
	}

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}
}

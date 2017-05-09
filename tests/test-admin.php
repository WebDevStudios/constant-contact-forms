<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Admin
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Admin_Test extends WP_UnitTestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Admin' ) );
	}

	function test_class_access() {

		$this->assertTrue( constant_contact()->admin instanceof ConstantContact_Admin );
	}

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}
}

<?php
/**
 * @package ConstantContact_Tests
 * @subpackage API
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_API_Test extends WP_UnitTestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_API' ) );
	}

	function test_class_access() {
		$this->assertTrue( constant_contact()->api instanceof ConstantContact_API );
	}

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}
}

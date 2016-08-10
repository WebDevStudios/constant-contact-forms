<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Connect
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Connect_Test extends WP_UnitTestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Connect' ) );
	}

	function test_class_access() {
		$this->assertTrue( constant_contact()->connect instanceof ConstantContact_Connect );
	}

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}
}

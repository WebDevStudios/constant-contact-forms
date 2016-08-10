<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Loader
 * @author Pluginize
 * @since 1.0.0
 */

class BaseTest extends WP_UnitTestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'Constant_Contact' ) );
	}

	function test_get_instance() {
		$this->assertTrue( Constant_Contact() instanceof Constant_Contact );
	}

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}
}

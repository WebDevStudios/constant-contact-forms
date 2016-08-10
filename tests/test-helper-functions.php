<?php
/**
 * @package ConstantContact_Tests
 * @subpackage HelperFunctions
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Helper_Functions_Test extends WP_UnitTestCase {

	function test_functions_exist() {
		$this->assertTrue( function_exists( 'constant_contact_get_form' ) );
		$this->assertTrue( function_exists( 'constant_contact_display_form' ) );
		$this->assertTrue( function_exists( 'constant_contact_get_forms' ) );
	}

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}
}

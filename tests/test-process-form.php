<?php
/**
 * @package ConstantContact_Tests
 * @subpackage ProcessForm
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Process_Form_Test extends WP_UnitTestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Process_Form' ) );
	}

	function test_class_access() {
		$this->assertTrue( constant_contact()->process_form instanceof ConstantContact_Process_Form );
	}

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}
}

<?php
/**
 * @package ConstantContact_Tests
 * @subpackage BuilderFields
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Builder_Fields_Test extends WP_UnitTestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Builder_Fields' ) );
	}

	function test_class_access() {
		$this->assertTrue( constant_contact()->builder_fields instanceof ConstantContact_Builder_Fields );
	}

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}
}

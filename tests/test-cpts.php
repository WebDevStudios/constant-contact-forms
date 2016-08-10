<?php

class ConstantContact_CPTS_Test extends WP_UnitTestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_CPTS' ) );
	}

	function test_class_access() {
		$this->assertTrue( constant_contact()->cpts instanceof ConstantContact_CPTS );
	}

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}
}

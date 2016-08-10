<?php

class ConstantContact_Middleware_Test extends WP_UnitTestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Middleware' ) );
	}

	function test_class_access() {
		$this->assertTrue( constant_contact()->authserver instanceof ConstantContact_Middleware );
	}

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}
}

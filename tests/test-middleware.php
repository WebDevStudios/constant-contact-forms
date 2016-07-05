<?php

class CC_Middleware_Test extends WP_UnitTestCase {

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'CC_Middleware') );
	}

	function test_class_access() {
		$this->assertTrue( ()->middleware instanceof CC_Middleware );
	}
}

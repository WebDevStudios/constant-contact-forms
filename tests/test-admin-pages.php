<?php

class ConstantContact_Admin_Pages_Test extends WP_UnitTestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Admin_Pages' ) );
	}

	function test_class_access() {

		if ( is_admin() ) {
			$this->assertTrue( constant_contact()->admin_pages instanceof ConstantContact_Admin_Pages );
		}
	}

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}
}

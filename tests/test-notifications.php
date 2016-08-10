<?php
/**
 * @package ConstantContact_Notifications
 * @subpackage DisplayShortcode
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Notifications_Test extends WP_UnitTestCase {

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ConstantContact_Notifications' ) );
	}

	function test_class_access() {
		$this->assertTrue( constant_contact()->notifications instanceof ConstantContact_Notifications );
	}

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}
}

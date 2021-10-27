<?php
/**
 * @package ConstantContact_Tests
 * @subpackage HelperFunctions
 * @author Pluginize
 * @since 1.0.0
 */

class ConstantContact_Helper_Functions_Test extends WP_UnitTestCase {

	/**
	 * The option name used by constant_contact_set_has_exceptions().
	 */
	const EXCEPT_OPTION_NAME = 'ctct_exceptions_exist';

	/**
	 * Runs the routine before each test is executed.
	 *
	 * @since NEXT
	 */
	public function setUp() {
		// This code will run before each test.
		parent::setUp();
	}

	/**
	 * After a test method runs, resets any state in WordPress the test method might have changed.
	 *
	 * @since NEXT
	 */
	public function tearDown() {
		// This code will run after each test.
		delete_option( self::EXCEPT_OPTION_NAME );
		parent::tearDown();
	}

	/**
	 * Test that constant_contact_set_has_exceptions( 'false' )
	 * sets the ctct_exceptions_exist option to 'false'.
	 *
	 * @since NEXT
	 *
	 * @test
	 */
	public function test_constant_contact_set_has_exceptions_false() {

		constant_contact_set_has_exceptions( 'false' );

		$this->assertEquals(
			get_option( self::EXCEPT_OPTION_NAME ),
			'false'
		);
	}

	/**
	 * Test that constant_contact_set_has_exceptions( 'true' )
	 * sets the ctct_exceptions_exist option to 'true'.
	 *
	 * @since NEXT
	 *
	 * @test
	 */
	public function test_constant_contact_set_has_exceptions_true() {

		constant_contact_set_has_exceptions( 'true' );

		$this->assertEquals(
			get_option( self::EXCEPT_OPTION_NAME ),
			'true'
		);
	}

	/**
	 * Test that an Exception with a 418 code
	 * sets ctct_exceptions_exist option to 'true'.
	 *
	 * @since NEXT
	 *
	 * @test
	 */
	public function test_exception_with_418_error_sets_ctct_exceptions_exist_to_true() {

		/**
		 * Note that 418 was chosen simply because
		 * it was not implemented in guzzlehttp/guzzle:^5.1.0.
		 */
		constant_contact_forms_maybe_set_exception_notice(
			new Exception(
				'I\'m a teapot',
				418
			)
		);

		$this->assertEquals(
			get_option( self::EXCEPT_OPTION_NAME ),
			'true'
		);
	}

	/**
	 * Test that an Exception with a 400 code
	 * does not set ctct_exceptions_exist option to 'true'.
	 *
	 * @since NEXT
	 *
	 * @test
	 */
	public function test_exception_with_400_error_does_not_set_ctct_exceptions_exist_to_true() {

		constant_contact_forms_maybe_set_exception_notice(
			new Exception(
				'Bad Request',
				400
			)
		);

		$this->assertNotEquals(
			get_option( self::EXCEPT_OPTION_NAME ),
			'true'
		);
	}

	/**
	 * Test that an Exception with a 503 code
	 * does not set ctct_exceptions_exist option to 'true'.
	 *
	 * @since NEXT
	 *
	 * @test
	 */
	public function test_exception_with_503_error_does_not_set_ctct_exceptions_exist_to_true() {

		constant_contact_forms_maybe_set_exception_notice(
			new Exception(
				'Service Unavailable',
				503
			)
		);

		$this->assertNotEquals(
			get_option( self::EXCEPT_OPTION_NAME ),
			'true'
		);
	}
}

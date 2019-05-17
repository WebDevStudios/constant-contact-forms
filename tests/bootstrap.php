<?php
/**
 * @package ConstantContact_Tests
 * @subpackage Boostrap
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Bootstrap the plugin unit testing environment.
 *
 * Edit 'active_plugins' setting below to point to your main plugin file.
 *
 * @package wordpress-plugin-tests
 */

// Support for:
// 1. `WP_DEVELOP_DIR` environment variable
// 2. Plugin installed inside of WordPress.org developer checkout
// 3. Tests checked out to /tmp

if ( false !== getenv( 'WP_DEVELOP_DIR' ) ) {
	$test_root = getenv( 'WP_DEVELOP_DIR' );
} elseif ( false !== getenv( 'WP_TESTS_DIR' ) ) {
	$test_root = getenv( 'WP_TESTS_DIR' );
} elseif ( file_exists( '../../../../tests/phpunit/includes/bootstrap.php' ) ) {
	$test_root = '../../../../tests/phpunit';
} elseif ( file_exists( '/tmp/wordpress-tests-lib/includes/bootstrap.php' ) ) {
	$test_root = '/tmp/wordpress-tests-lib';
}
require_once $test_root . '/includes/functions.php';

function _test_constant_contact_manually_load_plugin() {
	require dirname( dirname( __FILE__ ) ) . '/constant-contact-forms.php';
}

tests_add_filter( 'muplugins_loaded', '_test_constant_contact_manually_load_plugin' );

require $test_root . '/includes/bootstrap.php';

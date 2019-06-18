<?php
/**
 * Tests plugin's Settings and related functionality. Note: No transients testing here.
 *
 * @package ConstantContact
 * @subpackage Tests
 * @since 1.6.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Tests plugin's Settings and related functionality.
 *
 * @since 1.6.0
 *
 * @todo 1. Delete existing ctct_options_settings option.
 * @todo 2. Update all options to a specific values.
 * @todo 3. Use the ctct get option template tag to get those values.
 * @todo 4. Confirm the results match.
 *
 * Option keys:
 * — [ ] ctct-review-dismissed
 * — [ ] ctct_token
 * — [ ] ctct_plugin_version
 * — [ ] ctct_options_settings
 * — [ ] ctct_notices_dismissed
 * — [ ] ctct_key
 * — [ ] _ctct_api_key
 */
class ConstantContact_Settings_Test extends WP_UnitTestCase {

	/**
	 * Default ctct_options_settings values.
	 *
	 * @since 1.6.0
	 */
	public $default_values = [
		'_ctct_optin_list'                  => '1441050418',
		'_ctct_optin_label'                 => 'Yes, I would like to receive emails from WebDevStudios. Sign me up!',
		'_ctct_form_label_placement'        => 'top',
		'_ctct_spam_error'                  => 'Looks like you are a bot. Scram!',
		'_ctct_recaptcha_site_key'          => 'A7Euw2zioWWYqqbK7CXrbMdiWDNTjg',
		'_ctct_recaptcha_secret_key'        => 'UiF7hwksHkXmbPPFJ9ujDFD4XTsLTP',
		'_ctct_logging'                     => 'on',
		'_ctct_bypass_cron'                 => 'on',
		'_ctct_data_tracking'               => 'on',
		'_ctct_disable_email_notifications' => 'on',
	];

	/**
	 * Set up.
	 *
	 * @since 1.6.0
	 */
	public function setUp() {
		parent::setUp();

		$this->plugin = constant_contact();

		$this->delete_existing_options();
		$this->setup_new_options();
	}

	/**
	 * Delete existing options.
	 *
	 * @since 1.6.0
	 */
	public function delete_existing_options() {
		delete_option( 'ctct_options_settings' );
	}

	/**
	 * Set up plugin options with values.
	 *
	 * @since 1.6.0
	 */
	public function setup_new_options() {
		update_option( 'ctct_options_settings', $this->default_values );
	}

	/**
	 * Should get correct values from the plugin's helper function for getting values.
	 *
	 * @since 1.6.0
	 *
	 * @dataProvider option_values_provider
	 * @test
	 */
	public function should_get_correct_values_from_helper_function( $option_key, $default_option_value ) {
		$expected = $default_option_value;
		$actual   = ctct_get_settings_option( $option_key );

		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Data provider looping through all "ctct_settings_options" saying we expect the default values we set up for each option.
	 *
	 * @since 1.6.0
	 *
	 * @return array
	 */
	public function option_values_provider() {
		$data = [];

		foreach ( $this->default_values as $option_key => $default_option_value ) {
			$data[ $option_key ] = [ $option_key, $default_option_value ];
		}

		return $data;
	}


}

<?php
/**
 * Constant Contact Uninstaller class.
 *
 * @package ConstantContact
 * @subpackage Settings
 * @author Constant Contact
 * @since 1.6.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Uninstalls database options, transients, and post meta for Constant Contact forms.
 *
 * @since 1.6.0
 */
class ConstantContact_Uninstall {

	/**
	 * Names of options to delete.
	 *
	 * @since 1.6.0
	 *
	 * @var array
	 */
	private $options = [];

	/**
	 * Names of transients to delete.
	 *
	 * @since 1.6.0
	 *
	 * @var array
	 */
	private $transients = [];

	/**
	 * A public function for running the uninstallation processes.
	 *
	 * @since 1.6.0
	 */
	public function run() {
		$this->delete_options();
		$this->delete_transients();
	}

	/**
	 * Get filterable list of option names to delete.
	 *
	 * @since 1.6.0
	 *
	 * @return array
	 */
	private function get_option_names() {
		$this->options = [
			'ctct_first_form_modal_dismissed',
			'ctct_options_settings',
			'ctct_key',
			'constant_contact_lists_last_synced',
			'ctct_connect_verification',
			'ctct-processed-forms',
			'ctct_plugin_version',
			'ctct_privacy_policy_status',
			'widget_ctct_form',
			'_ctct_api_key',
			'ctct_token',
			Constant_Contact::$activated_date_option,
			ConstantContact_Notifications::$dismissed_notices_option,
			ConstantContact_Notifications::$review_dismissed_option,
			ConstantContact_Notifications::$reviewed_option,
		];

		/**
		 * Allows filtering which options are deleted upon plugin deactivation.
		 *
		 * @since 1.6.0
		 *
		 * @param array $options One-dimensional array of option names to delete.
		 */
		return apply_filters( 'ctct_option_names_to_uninstall', $this->options );
	}

	/**
	 * Get filterable list of transient names to delete.
	 *
	 * @since 1.6.0
	 *
	 * @return array
	 */
	private function get_transient_names() {
		$this->transients = [
			'constant_contact_acct_info',
			'ctct_contact',
			'ctct_lists',
			'constant_contact_shortcode_form_list',
		];

		/**
		 * Allows filtering which transients are deleted upon plugin deactivation.
		 *
		 * @since 1.6.0
		 *
		 * @param array $options One-dimensional array of transient names to delete.
		 */
		return apply_filters( 'ctct_transient_names_to_uninstall', $this->transients );
	}

	/**
	 * Delete database options.
	 *
	 * @since 1.6.0
	 */
	private function delete_options() {
		foreach ( $this->get_option_names() as $option_name ) {
			delete_option( $option_name );
		}
	}

	/**
	 * Delete transients.
	 *
	 * @since 1.6.0
	 */
	private function delete_transients() {
		foreach ( $this->get_transient_names() as $transient_name ) {
			delete_transient( $transient_name );
		}
	}
}

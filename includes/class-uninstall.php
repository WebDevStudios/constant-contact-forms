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
	 * A public function for running the uninstallation processes.
	 *
	 * @since 1.6.0
	 */
	public function run() {
		$this->delete_options();
		$this->delete_transients();
		$this->delete_cron_hooks();
		$this->delete_log_dir();
	}

	/**
	 * Get filterable list of option names to delete.
	 *
	 * @since 1.6.0
	 *
	 * @return array
	 */
	private function get_option_names() {
		$default_options = [
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
			'ctct_access_token',
			'_ctct_access_token',
			'ctct_refresh_token',
			'_ctct_refresh_token',
			'_ctct_expires_in',
			'ctct_access_token_timestamp',
			'CtctConstantContactcode_verifier',
			'CtctConstantContactState',
			'ctct_auth_url',
			'ctct_key',
			'ctct_exceptions_exist',
			'ctct_api_v2_v3_migrated',
			'ctct_missed_api_requests',
			'ctct_log_suffix',
			Constant_Contact::$activated_date_option,
			ConstantContact_Notifications::$dismissed_notices_option,
			ConstantContact_Notifications::$review_dismissed_option,
			ConstantContact_Notifications::$reviewed_option,
			ConstantContact_Notifications::$deleted_forms,
		];

		/**
		 * Allows filtering which options are deleted upon plugin deactivation.
		 *
		 * @deprecated 1.9.0 Deprecated in favor of properly-prefixed hookname.
		 *
		 * @since 1.6.0
		 *
		 * @param array $options One-dimensional array of option names to delete.
		 */
		$options = apply_filters_deprecated( 'ctct_option_names_to_uninstall', [ $default_options ], '1.9.0', 'constant_contact_option_names_to_uninstall' );

		/**
		 * Filters which options are deleted when plugin is uninstalled.
		 *
		 * @author Rebekah Van Epps <rebekah.vanepp@webdevstudios.com>
		 * @since  1.9.0
		 *
		 * @param  array $options Options to be deleted.
		 */
		return apply_filters( 'constant_contact_option_names_to_uninstall', $options );
	}

	/**
	 * Get filterable list of transient names to delete.
	 *
	 * @since 1.6.0
	 *
	 * @return array
	 */
	private function get_transient_names() {
		$default_transients = [
			'constant_contact_acct_info',
			'ctct_contact',
			'ctct_lists',
			'constant_contact_shortcode_form_list',
		];

		/**
		 * Allows filtering which transients are deleted upon plugin deactivation.
		 *
		 * @deprecated 1.9.0 Deprecated in favor of properly-prefixed hookname.
		 *
		 * @since 1.6.0
		 *
		 * @param array $transients One-dimensional array of transient names to delete.
		 */
		$transients = apply_filters_deprecated( 'ctct_transient_names_to_uninstall', [ $default_transients ], '1.9.0', 'constant_contact_transient_names_to_uninstall' );

		/**
		 * Filters which transients are deleted when plugin is uninstalled.
		 *
		 * @author Rebekah Van Epps <rebekah.vanepp@webdevstudios.com>
		 * @since  1.9.0
		 *
		 * @param  array $transients Transients to be deleted.
		 */
		return apply_filters( 'constant_contact_transient_names_to_uninstall', $transients );
	}

	/**
	 * Get filterable list of cron hooks to delete.
	 *
	 * @since 1.6.0
	 *
	 * @return array
	 */
	private function get_cron_hook_names() {
		$default_cron_hooks = [
			'ctct_schedule_form_opt_in',
		];

		/**
		 * Allows filtering which cron hooks are deleted upon plugin deactivation.
		 *
		 * @deprecated 1.9.0 Deprecated in favor of properly-prefixed hookname.
		 *
		 * @since 1.6.0
		 *
		 * @param array $cron_hooks One-dimensional array of cron hook names to delete.
		 */
		$cron_hooks = apply_filters_deprecated( 'ctct_cron_hook_names_to_uninstall', [ $default_cron_hooks ], '1.9.0', 'constant_contact_cron_hook_names_to_uninstall' );

		/**
		 * Filters which cron hooks are deleted when plugin is uninstalled.
		 *
		 * @author Rebekah Van Epps <rebekah.vanepp@webdevstudios.com>
		 * @since  1.9.0
		 *
		 * @param  array $cron_hooks Cron hooks to be deleted.
		 */
		return apply_filters( 'constant_contact_cron_hook_names_to_uninstall', $cron_hooks );
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

	/**
	 * Delete cron hooks.
	 *
	 * @since 1.6.0
	 */
	private function delete_cron_hooks() {
		foreach ( $this->get_cron_hook_names() as $cron_hook_name ) {
			wp_clear_scheduled_hook( $cron_hook_name );
		}
	}

	/**
	 * Delete logging directory.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.8.2
	 */
	private function delete_log_dir() {
		constant_contact()->get_logging()->delete_current_log_dir();
	}
}

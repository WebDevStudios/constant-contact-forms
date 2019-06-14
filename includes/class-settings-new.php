<?php
/**
 * Constant Contact Settings class.
 *
 * @package ConstantContact
 * @subpackage Settings
 * @author Constant Contact
 * @since 1.6.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Powers our settings and options page, as well as injecting our optins to the front-end.
 *
 * @since 1..0
 */
final class Constant_Contact_Settings {

	/**
	 * Option key, and option page slug.
	 *
	 * @since 1.6.0
	 * @var string
	 */
	private static $key = 'ctct_options_settings';

	/**
	 * Get all of the plugin's setting definitions.
	 *
	 * @since 1.6.0
	 * @return array
	 */
	public static function get_settings_definitions() {

		$before_recaptcha = sprintf(
			'<hr/><h2>%s</h2>%s',
			esc_html__( 'Google reCAPTCHA', 'constant-contact-forms' ),
			'<div class="discover-recaptcha">' . __( 'Learn more and get an <a href="https://www.google.com/recaptcha/intro/" target="_blank">API site key</a>', 'constant-contact-forms' ) . '</div>'
		);

		$before_global_css = sprintf(
			'<hr /><h2>%s</h2>',
			esc_html__( 'Global Form CSS Settings', 'constant-contact-forms' )
		);

		$before_debugging = sprintf(
			'<hr/><h2>%s</h2>',
			esc_html__( 'Support', 'constant-contact-forms' )
		);

		$settings = [
			[
				'name' => esc_html__( 'Google Analytics&trade; tracking opt-in.', 'constant-contact-forms' ),
				'id'   => '_ctct_data_tracking',
				'type' => 'checkbox',
				'desc' => __( 'Allow Constant Contact to use Google Analytics&trade; to track your usage across the Constant Contact Forms plugin.<br/> NOTE &mdash; Your website and users will not be tracked. See our <a href="https://www.endurance.com/privacy"> Privacy Statement</a> information about what is and is not tracked.', 'constant-contact-forms' ),
			],
			[
				'name'       => esc_html__( 'Site Key', 'constant-contact-forms' ),
				'id'         => '_ctct_recaptcha_site_key',
				'type'       => 'text',
				'before_row' => $before_recaptcha,
			],
			[
				'name' => esc_html__( 'Secret Key', 'constant-contact-forms' ),
				'id'   => '_ctct_recaptcha_secret_key',
				'type' => 'text',
			],
			[
				'name'        => esc_html__( 'CSS Classes', 'constant-contact_forms' ),
				'id'          => '_ctct_form_custom_classes',
				'type'        => 'text',
				'description' => esc_html__(
						'Provide custom classes for the form separated by a single space.',
						'constant-contact-forms'
				),
				'before_row'  => $before_global_css,
			],
			[
				'name'             => esc_html__( 'Label Placement', 'constant-contact-forms' ),
				'id'               => '_ctct_form_label_placement',
				'type'             => 'select',
				'default'          => 'top',
				'show_option_none' => false,
				'options'          => [
					'top'    => esc_html__( 'Top', 'constant-contact-forms' ),
					'left'   => esc_html__( 'Left', 'constant-contact-forms' ),
					'right'  => esc_html__( 'Right', 'constant-contact-forms' ),
					'bottom' => esc_html__( 'Bottom', 'constant-contact-forms' ),
					'hidden' => esc_html__( 'Hidden', 'constant-contact-forms' ),
				],
				'description'      => esc_html__(
					'Choose the position for the labels of the form elements.',
					'constant-contact-forms'
				),
			],
			[
				'name'       => esc_html__( 'Enable logging for debugging purposes.', 'constant-contact-forms' ),
				'desc'       => esc_html__( 'This option will turn on some logging functionality that can be used to deduce sources of issues with the use of Constant Contact Forms plugin.', 'constant-contact-forms' ),
				'id'         => '_ctct_logging',
				'type'       => 'checkbox',
				'before_row' => $before_debugging,
			],
		];

		if ( ! constant_contact()->api->is_connected() ) {
			return $settings;
		}


		$settings[] = [
				'name'       => esc_html__( 'Disable E-mail Notifications', 'constant-contact-forms' ),
				'desc'       => sprintf( esc_html__( 'This option will disable e-mail notifications for forms with a selected list and successfully submit to Constant Contact.%s Notifications are sent to the email address listed under Wordpress "General Settings".', 'constant-contact-forms' ), '<br/>' ),
				'id'         => '_ctct_disable_email_notifications',
				'type'       => 'checkbox',
				'before_row' => '<hr/>',
		];

		$settings[] = [
				'name'       => esc_html__( 'Bypass Constant Contact cron scheduling', 'constant-contact-forms' ),
				'desc'       => esc_html__( 'This option will send form entries to Constant Contact right away instead of holding for one minute delay.', 'constant-contact-forms' ),
				'id'         => '_ctct_bypass_cron',
				'type'       => 'checkbox',
				'before_row' => '<hr/>',
		];

		$lists = constant_contact()->builder->get_lists();

		if ( empty( $lists ) || ! is_array( $lists ) ) {
			return $settings;
		}


		$before_optin = sprintf(
			'<hr/><h2>%s</h2>',
			esc_html__( 'Advanced Opt-in', 'constant-contact-forms' )
		);

		$settings[] => [
			'name'       => esc_html__( 'Opt-in Location', 'constant-contact-forms' ),
			'id'         => '_ctct_optin_forms',
			'type'       => 'multicheck',
			'options'    => $this->get_optin_show_options(),
			'before_row' => $before_optin,
		];

		$lists[0] = esc_html__( 'Select a list', 'constant-contact-forms' );

		$settings[] = [
			'name'             => esc_html__( 'Add subscribers to', 'constant-contact-forms' ),
			'id'               => '_ctct_optin_list',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => esc_html__( 'Select a list', 'constant-contact-forms' ),
			'options'          => $lists,
		];

		$business_name = get_bloginfo( 'name' ) ?: esc_html__( 'Business Name', 'constant-contact-forms' );
		$business_addr = '';

		$disclosure_info = $this->plugin->api->get_disclosure_info( true );

		if ( ! empty( $disclosure_info ) ) {
			$business_name = $disclosure_info['name'] ?: $business_name;
			$business_addr = isset( $disclosure_info['address'] ) ?: '';
		}

		$settings[] = [
			'name'    => esc_html__( 'Opt-in Affirmation', 'constant-contact-forms' ),
			'id'      => '_ctct_optin_label',
			'type'    => 'text',
			// translators: placeholder will hold site owner's business name.
			'default' => sprintf( esc_html__( 'Yes, I would like to receive emails from %s. Sign me up!', 'constant-contact-forms' ), $business_name ),
		];

		if ( empty( $disclosure_info ) ) {
			$settings[] = [
				'name'       => esc_html__( 'Disclosure Name', 'constant-contact-forms' ),
				'id'         => '_ctct_disclose_name',
				'type'       => 'text',
				'default'    => $business_name,
				'attributes' => ! empty( $business_name ) ? [ 'readonly' => 'readonly' ] : [],
			];

			$settings[] = [
				'name'       => esc_html__( 'Disclosure Address', 'constant-contact-forms' ),
				'id'         => '_ctct_disclose_address',
				'type'       => 'text',
				'default'    => $business_addr,
				'attributes' => ! empty( $business_addr ) ? [ 'readonly' => 'readonly' ] : [],
			];
		}

		$this->add_spam_error_fields( $cmb );

		return $settings;
	}

	/*
	 * Adds a fieldset for controlling the spam error.
	 *
	 * @since 1.5.0
	 * @param object $cmb An instance of the CMB2 object.
	 */
	private static function add_spam_error_fields( $cmb ) {
		$description  = '<div class="description">';
		$description .= esc_html__( 'This message displays when the plugin detects spam data.', 'constant-contact-forms' );
		$description .= esc_html__( 'Note that this message may be overriden on a per-post basis.', 'constant-contact-forms' );
		$description .= '</div>';

		$before_message = sprintf(
			'<hr/><h2>%s</h2>%s',
			__( 'Suspected Bot Error Message', 'constant-contact-forms' ),
			$description
		);

		$cmb->add_field(
			[
				'name'       => esc_html__( 'Error Message', 'constant-contact-forms' ),
				'id'         => '_ctct_spam_error',
				'type'       => 'text',
				'before_row' => $before_message,
				'default'    => $this->get_default_spam_error(),
			]
		);
	}

	/**
	 * Get the error message displayed to suspected spam input.
	 *
	 * @since 1.5.0
	 * @param string $message The error message to filter.
	 * @param mixed  $post_id The post ID of the current post, if any.
	 * @return string
	 */
	public static function get_spam_error_message( $message, $post_id ) {
		$post_error = get_post_meta( $post_id, '_ctct_spam_error', true );

		if ( ! empty( $post_error ) ) {
			return $post_error;
		}

		$option_error = cmb2_get_option( '_ctct_spam_error' );

		if ( ! empty( $option_error ) ) {
			return $option_error;
		}

		return $this->get_default_spam_error();
	}

	/**
	 * Get the default spam error message.
	 *
	 * @since 1.5.0
	 * @return string
	 */
	private static function get_default_spam_error() {
		return __( 'We do not think you are human', 'constant-contact-forms' );
	}


}

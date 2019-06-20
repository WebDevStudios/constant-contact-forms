<?php
/**
 * Constant Contact plugin settings.
 *
 * @package ConstantContact
 * @subpackage Settings
 * @author Constant Contact
 * @since 1.6.0
 *
 * @var boolean $ctct_api_is_connected Whether the Constant Contact API is connected.
 * @var array $ctct_api_lists A list of Mailing Lists from the Constant Contact API.
 * @var string $ctct_api_disclosure_info A string of disclosure information received from the Constant Contact API.
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

$settings = [];

/**
 * General plugin settings.
 *
 * @since 1.6.0
 */
$settings['general'] = [
	'_ctct_data_tracking'        => [
		'name'    => esc_html__( 'Allow Google Analytics&trade; tracking?', 'constant-contact-forms' ),
		'id'      => '_ctct_data_tracking',
		'type'    => 'checkbox',
		'desc'    => esc_html__( 'Allow Constant Contact to use Google Analytics&trade; to track your usage across the Constant Contact Forms plugin.', 'constact-contact-forms' ),
		'tooltip' => sprintf(
			/* Translators: Placeholders are for HTML <a> tags. */
			esc_html__( 'Your website and users will not be tracked. See our %1$sPrivacy Statement%2$s information about what is and is not tracked.', 'constant-contact-forms' ),
			'<a target="_blank" href="https://www.endurance.com/privacy">',
			'</a>'
		),
	],
	'_ctct_recaptcha_site_key'   => [
		'name'    => esc_html__( 'Google ReCAPTCHA Site Key', 'constant-contact-forms' ),
		'id'      => '_ctct_recaptcha_site_key',
		'type'    => 'text',
		'classes' => 'regular-text',
		'tooltip' => sprintf(
			/* Translators: Placeholders are for HTML <a> tags. */
			esc_html__( 'Learn more and get an API site key at %1$sGoogle ReCAPTCHA%2$s.', 'constant-contact-forms' ),
			'<a href="https://www.google.com/recaptcha/intro/" target="_blank">',
			'</a>'
		),
	],
	'_ctct_recaptcha_secret_key' => [
		'name'    => esc_html__( 'Google ReCAPTCHA Secret Key', 'constant-contact-forms' ),
		'id'      => '_ctct_recaptcha_secret_key',
		'type'    => 'text',
		'classes' => 'regular-text',
		'tooltip' => sprintf(
			/* Translators: Placeholders are for HTML <a> tags. */
			esc_html__( 'Learn more and get an API secret key at %1$sGoogle ReCAPTCHA%2$s.', 'constant-contact-forms' ),
			'<a href="https://www.google.com/recaptcha/intro/" target="_blank">',
			'</a>'
		),
	],
];

if ( $ctct_api_is_connected ) {

	$settings['general']['_ctct_disable_email_notifications'] = [
		'id'   => '_ctct_disable_email_notifications',
		'name' => esc_html__( 'Disable E-mail Notifications', 'constant-contact-forms' ),
		'type' => 'checkbox',
		'desc' => esc_html__( 'Notifications are sent to the email address listed under Wordpress "General Settings".', 'constant-contact-forms' ),
	];

	$settings['general']['_ctct_bypass_cron'] = [
		'id'   => '_ctct_bypass_cron',
		'name' => esc_html__( 'Bypass Constant Contact cron scheduling', 'constant-contact-forms' ),
		'type' => 'checkbox',
		'desc' => esc_html__( 'This option will send form entries to Constant Contact right away instead of holding for one minute delay.', 'constant-contact-forms' ),
	];
}

/**
 * Form-related plugin settings.
 *
 * @since 1.6.0
 */
$settings['form'] = [
	'_ctct_form_custom_classes'  => [
		'name'    => esc_html__( 'CSS Classes', 'constant-contact_forms' ),
		'id'      => '_ctct_form_custom_classes',
		'type'    => 'text',
		'classes' => 'regular-text',
		'desc'    => esc_html__( 'Provide custom classes for the form separated by a single space.', 'constant-contact-forms' ),
	],
	'_ctct_form_label_placement' => [
		'name'    => esc_html__( 'Label Placement', 'constant-contact-forms' ),
		'id'      => '_ctct_form_label_placement',
		'type'    => 'select',
		'default' => 'top',
		'desc'    => esc_html__( 'Choose the position for the labels of the form elements.', 'constant-contact-forms' ),
		'options' => [
			'top'    => esc_html__( 'Top', 'constant-contact-forms' ),
			'left'   => esc_html__( 'Left', 'constant-contact-forms' ),
			'right'  => esc_html__( 'Right', 'constant-contact-forms' ),
			'bottom' => esc_html__( 'Bottom', 'constant-contact-forms' ),
			'hidden' => esc_html__( 'Hidden', 'constant-contact-forms' ),
		],
	],
	'_ctct_spam_error'           => [
		'name'    => esc_html__( 'Suspected Bot Error Message', 'constant-contact-forms' ),
		'id'      => '_ctct_spam_error',
		'type'    => 'text',
		'classes' => 'regular-text',
		'default' => esc_html__( 'We do not think you are human', 'constant-contact-forms' ),
		'desc'    => esc_html__( 'This message displays when the plugin detects spam data.', 'constant-contact-forms' ),
		'tooltip' => esc_html__( 'This message can be overridden on a per-post basis.', 'constant-contact-forms' ),
	],
];

if ( $ctct_api_is_connected && ! empty( $ctct_api_lists ) ) {

	$optin_options = [
		'comment_form' => esc_html__( 'Add a checkbox to the comment field in your posts', 'constant-contact-forms' ),
		'login_form'   => esc_html__( 'Add a checkbox to the main WordPress login page', 'constant-contact-forms' ),
	];

	if ( get_option( 'users_can_register' ) ) {
		$optin_options['reg_form'] = esc_html__( 'Add a checkbox to the WordPress user registration page', 'constant-contact-forms' );
	}

	$settings['form']['_ctct_optin_forms'] = [
		'name'    => esc_html__( 'Opt-in Location', 'constant-contact-forms' ),
		'id'      => '_ctct_optin_forms',
		'type'    => 'multicheck',
		'options' => $optin_options,
	];

	$ctct_api_lists[0] = esc_html__( 'Select a list', 'constant-contact-forms' );

	$settings['form']['_ctct_optin_list'] = [
		'name'             => esc_html__( 'Add subscribers to', 'constant-contact-forms' ),
		'id'               => '_ctct_optin_list',
		'type'             => 'select',
		'show_option_none' => false,
		'default'          => esc_html__( 'Select a list', 'constant-contact-forms' ),
		'options'          => $ctct_api_lists,
	];

	$business_name = get_bloginfo( 'name' ) ?: esc_html__( 'Business Name', 'constant-contact-forms' );
	$business_addr = '';

	if ( ! empty( $ctct_api_disclosure_info ) ) {
		$business_name = $ctct_api_disclosure_info['name'] ?: $business_name;
		$business_addr = isset( $ctct_api_disclosure_info['address'] ) ?: '';
	}

	$settings['form']['_ctct_optin_label'] = [
		'name'    => esc_html__( 'Opt-in Affirmation', 'constant-contact-forms' ),
		'id'      => '_ctct_optin_label',
		'type'    => 'text',
		'default' => sprintf(
			/* Translators: Placeholder will hold site owner's business name. */
			esc_html__( 'Yes, I would like to receive emails from %1$s. Sign me up!', 'constant-contact-forms' ),
			$business_name
		),
	];

	if ( empty( $ctct_api_disclosure_info ) ) {
		$settings['form']['_ctct_disclose_name'] = [
			'name'       => esc_html__( 'Disclosure Name', 'constant-contact-forms' ),
			'id'         => '_ctct_disclose_name',
			'type'       => 'text',
			'default'    => $business_name,
			'attributes' => ! empty( $business_name ) ? [ 'readonly' => 'readonly' ] : [],
		];

		$settings['form']['_ctct_disclose_address'] = [
			'name'       => esc_html__( 'Disclosure Address', 'constant-contact-forms' ),
			'id'         => '_ctct_disclose_address',
			'type'       => 'text',
			'default'    => $business_addr,
			'attributes' => ! empty( $business_addr ) ? [ 'readonly' => 'readonly' ] : [],
		];
	}
}

/**
 * Support-related plugin settings.
 *
 * @since 1.6.0
 */
$settings['support'] = [
	'_ctct_logging' => [
		'name' => esc_html__( 'Enable logging for debugging purposes.', 'constant-contact-forms' ),
		'desc' => esc_html__( 'This option will turn on some logging functionality that can be used to deduce sources of issues with the use of Constant Contact Forms plugin.', 'constant-contact-forms' ),
		'id'   => '_ctct_logging',
		'type' => 'checkbox',
	],
];

return $settings;

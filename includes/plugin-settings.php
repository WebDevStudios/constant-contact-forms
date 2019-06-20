<?php
/**
 * Constant Contact plugin settings.
 *
 * @package ConstantContact
 * @subpackage Settings
 * @author Constant Contact
 * @since 1.6.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

$settings = [];

$before_global_css = '@todo global css placeholder';
$before_debugging  = '@todo debugging placeholder';

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

/**
 * Form-related plugin settings.
 *
 * @since 1.6.0
 */
$settings['form'] = [
	'_ctct_form_custom_classes'  => [
		'name'        => esc_html__( 'CSS Classes', 'constant-contact_forms' ),
		'id'          => '_ctct_form_custom_classes',
		'type'        => 'text',
		'description' => esc_html__( 'Provide custom classes for the form separated by a single space.', 'constant-contact-forms' ),
	],
	'_ctct_form_label_placement' => [
		'name'             => esc_html__( 'Label Placement', 'constant-contact-forms' ),
		'id'               => '_ctct_form_label_placement',
		'type'             => 'select',
		'default'          => 'top',
		'show_option_none' => false,
		'description'      => esc_html__( 'Choose the position for the labels of the form elements.', 'constant-contact-forms' ),
		'options'          => [
			'top'    => esc_html__( 'Top', 'constant-contact-forms' ),
			'left'   => esc_html__( 'Left', 'constant-contact-forms' ),
			'right'  => esc_html__( 'Right', 'constant-contact-forms' ),
			'bottom' => esc_html__( 'Bottom', 'constant-contact-forms' ),
			'hidden' => esc_html__( 'Hidden', 'constant-contact-forms' ),
		],
	],
];

/**
 * Support-related plugin settings.
 *
 * @since 1.6.0
 */
$settings['support'] = [
	'_ctct_logging' => [
		'name'       => esc_html__( 'Enable logging for debugging purposes.', 'constant-contact-forms' ),
		'desc'       => esc_html__( 'This option will turn on some logging functionality that can be used to deduce sources of issues with the use of Constant Contact Forms plugin.', 'constant-contact-forms' ),
		'id'         => '_ctct_logging',
		'type'       => 'checkbox',
		'before_row' => $before_debugging, // @todo Maybe don't need this.
	],
];

return $settings;

<?php
/**
 * Constant Contact plugin options.
 *
 * @package ConstantContact
 * @subpackage Settings
 * @author Constant Contact
 * @since 1.6.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

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

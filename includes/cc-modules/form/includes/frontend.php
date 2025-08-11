<?php
/**
 * Beaver Builder Module to Display Form
 *
 * @since 1.10.0
 */

if ( empty( $settings->cc_form ) ) {
	echo esc_html__( 'Please select a form.', 'constant-contact-forms' );
	return;
}

echo do_shortcode( "[ctct form='$settings->cc_form' show_title='$settings->cc_display_title']" );



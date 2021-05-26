<?php
/**
 * Beaver Builder Module to Display Form
 *
 * @since  NEXT
 */

if ( empty( $settings->cc_form ) ) {
	echo __( 'Please select a form.', 'constant-contact-forms' );
	return;
}

echo do_shortcode( "[ctct form='{$settings->cc_form}' show_title='{$settings->cc_display_title}']" );

?>

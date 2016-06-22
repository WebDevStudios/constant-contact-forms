<?php
/**
 * Constant Contact form shortcode template
 *
 * @package ConstantContact
 * @author Pluginize
 * @license GPLV2
 * @since 1.0.0
 */
	//error_log( print_r( $_POST, true ) );

	//error_log( print_r( $form_data, true ) );

?>

<form id="ctct-form" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" method="post">

	<?php ctct_form_submit_message(); ?>

	<?php ctct_build_form_fields( $form_data ); ?>

	<p><input type="submit" name="ctct-submitted" value="Send"/></p>
	<?php wp_nonce_field( 'ctct_submit_form', 'ctct_form' ) ?>
</form>

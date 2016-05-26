<?php
/**
 * Constant Contact form shortcode template
 *
 * @package ConstantContact
 * @author Pluginize
 * @license GPLV2
 * @since 1.0.0
 */

?>

<form id="myForm" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ) . '?sssss='; ?>" method="post">
<?php
	foreach ( $form_data as $key => $value ) {
		echo '<div><p><label>' . esc_attr( $form_data[ $key ]['name'] ) . '</label></br>';
		echo '<input type="text" name="' . esc_attr( $key ) . '" value="" tabindex="1" size="40"></p></div>';
	}
?>
	<p><input type="submit" name="ctct-submitted" value="Send"/></p>
    <?php wp_nonce_field( 'ctct_submit_form', 'ctct_form' ) ?>
</form>

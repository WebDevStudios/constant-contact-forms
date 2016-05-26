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

<form id="myForm" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" method="post">
<?php
	foreach ( $form_data as $key => $value ) {
		echo '<div><p><label>' . esc_attr( $form_data[ $key ]['name'] ) . '</label></br>';
		echo '<input type="text" pattern="[a-zA-Z0-9 ]+" name="' . esc_attr( $key ) . '" value="' . ( isset( $_POST[ $key ] ) ? esc_attr( $_POST[ $key ] ) : '' ) .'" tabindex="1" size="40"></p></div>';
	}
?>
    <div><p><label><?php _e( 'Email * required', constant_contact()->text_domain ); ?></label></br>
    <input type="email" name="ctct-email" value="<?php echo isset( $_POST['ctct-email'] ) ? esc_attr( $_POST['ctct-email'] ) : ''; ?>" size="40" /></div>

	<p><input type="submit" name="ctct-submitted" value="Send"/></p>
    <?php wp_nonce_field( 'ctct_submit_form', 'ctct_form' ) ?>
</form>

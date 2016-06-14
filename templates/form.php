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
	foreach ( $form_data['fields'] as $key => $value ) {
		$required = isset( $form_data['fields'][ $key ]['required'] ) ? ' * required' : '';
		echo '<div><p><label>' . esc_attr( $form_data['fields'][ $key ]['name'] ) .  $required . '</label></br>';
		echo '<input type="text" pattern="[a-zA-Z0-9 ]+" name="' . esc_attr( $key ) . '" value="' . ( isset( $_POST[ $key ] ) ? esc_attr( $_POST[ $key ] ) : '' ) .'" tabindex="1" size="40"></p></div>';
	}
?>
	<?php if ( isset( $form_data['options']['opt_in'] ) ) : ?>
		<div><p>
			<input type="checkbox" name="ctct-opti-in" value="<?php echo isset( $form_data['options']['ctct_list'] ) ? esc_attr( $form_data['options']['ctct_list'] ) : ''; ?>"/>
			<?php echo $form_data['options']['opt_in']; ?>
		</p></div>
	<?php endif ; ?>

	<p><input type="submit" name="ctct-submitted" value="Send"/></p>
    <?php wp_nonce_field( 'ctct_submit_form', 'ctct_form' ) ?>
</form>

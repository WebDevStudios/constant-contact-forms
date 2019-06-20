<?php
/**
 * Renders a checkbox field in the admin settings pages.
 *
 * @package ConstantContact
 * @subpackage Settings
 * @since 1.6.0
 *
 * @var string $option_key The option key for this specific field.
 * @var array $args The whole array of args for the option.
 */

$checked = ! empty( ctct_get_option( $args['id'] ) ) ? checked( 1, ctct_get_option( $args['id'] ), false ) : '';

?>
<td>
	<?php if ( ! empty( $args['tooltip'] ) ) : ?>
		<span class="ctct-options-tooltip dashicons dashicons-editor-help" title="<?php echo esc_attr( $args['tooltip'] ); ?>"></span>
	<?php endif; ?>

	<input type="hidden" name="<?php echo esc_attr( $option_key ); ?>" value="-1" />
	<input
		class="<?php echo empty( $args['classes'] ) ?: esc_html( $args['classes'] ); ?>"
		type="checkbox"
		id="<?php echo esc_attr( $option_key ); ?>"
		name="<?php echo esc_attr( $option_key ); ?>"
		value="1"
		<?php echo $checked; // XSS ok. ?>
	/>

	<?php if ( ! empty( $args['desc'] ) ) : ?>
		<label for="<?php echo esc_attr( $option_key ); ?>" class="description"><?php echo wp_kses_post( $args['desc'] ); ?></label>
	<?php endif; ?>
</td>

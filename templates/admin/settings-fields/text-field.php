<?php
/**
 * Renders a text field in the admin settings pages.
 *
 * @package ConstantContact
 * @subpackage Settings
 * @since 1.6.0
 *
 * @var string $option_key The option key for this specific field.
 * @var array $args The whole array of args for the option.
 */

?>

<td>
	<?php if ( ! empty( $args['tooltip'] ) ) : ?>
		<span class="ctct-options-tooltip dashicons dashicons-editor-help" title="<?php echo esc_attr( $args['tooltip'] ); ?>"></span>
	<?php endif; ?>

	<input
		type="text"
		id="<?php echo esc_attr( $option_key ); ?>"
		name="<?php echo esc_attr( $option_key ); ?>"
		value="<?php echo esc_attr( ctct_get_option( $args['id'], '' ) ); ?>"
		class="<?php echo empty( $args['classes'] ) ?: esc_attr( $args['classes'] ); ?>"
	/>

	<?php if ( ! empty( $args['desc'] ) ) : ?>
		<label for="<?php echo esc_attr( $option_key ); ?>">
			<p class="description"?><?php echo wp_kses_post( $args['desc'] ); ?></p>
		</label>
	<?php endif; ?>
</td>

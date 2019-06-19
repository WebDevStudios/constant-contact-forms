<?php
/**
 * Render a checkbox admin settings field.
 *
 * @package ConstantContact
 * @subpackage Settings
 * @since 1.6.0
 *
 * @var string $option_key The option key for this specific field.
 * @var array $args {.
 *     @type string $field_type The type of field to render.
 *     @type string $id The option key for the option whose field is being rendered'.
 *     @type string $option_args Array of option args, like title, desc, before_row, etc.
 * }
 */

$checked  = ! empty( ctct_get_option( $args['id'] ) ) ? checked( 1, ctct_get_option( $args['id'] ), false ) : '';

?>

<td>
	<input type="hidden" name="<?php echo esc_attr( $option_key ); ?>" value="-1" />
	<input
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

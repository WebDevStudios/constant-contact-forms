<?php
/**
 * Render a text admin settings field.
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

?>

<td>
	<input
		type="text"
		id="<?php echo esc_attr( $option_key ); ?>"
		name="<?php echo esc_attr( $option_key ); ?>"
		value="<?php echo esc_attr( ctct_get_option( $args['id'], '' ) ); ?>"
	/>

	<?php if ( ! empty( $args['desc'] ) ) : ?>
		<p class="description"><?php echo esc_html( $args['desc'] ); ?></p>
	<?php endif; ?>
</td>

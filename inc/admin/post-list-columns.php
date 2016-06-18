<?php
/**
 * Constant Contact forms Post List Columns
 *
 * @package Constant Contact
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Filter custom colums to each cpt.
 *
 * @internal
 */
function ctct_add_post_columns_filter() {

	$post_type = 'ctct_forms';

	add_filter( 'manage_' . $post_type . '_posts_columns', 'ctct_set_custom_columns' );

}
add_action( 'admin_init', 'ctct_add_post_columns_filter' );


/**
 * Add shortcode columns to each cpt.
 *
 * @internal
 *
 * @since 1.0.0
 * @param array $columns post list columns.
 * @return array $columns Array of columns to add.
 */
function ctct_set_custom_columns( $columns ) {

	$columns['description'] = __( 'Description', 'cptuiext' );
	$columns['shortcodes'] = __( 'Shortcode', 'cptuiext' );

	return $columns;
}

/**
 * Content of custom  post columns.
 *
 * @internal
 *
 * @since 1.0.0
 * @param string  $column  Column title.
 * @param integer $post_id Post id of post item.
 */
function ctct_custom_columns( $column, $post_id ) {

	switch ( $column ) {
		case 'shortcodes':
			echo esc_attr( '[ctct form="'. $post_id .'"]' );
		break;
		case 'description':
			if ( $meta = get_post_meta( $post_id, '_ctct_description' ) ) {
				echo esc_attr( $meta[0] );
			}

		break;
	}
}
add_action( 'manage_ctct_forms_posts_custom_column' , 'ctct_custom_columns', 10, 2 );

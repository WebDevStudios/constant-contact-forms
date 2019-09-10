<?php
/**
 * Constant Contact Form Widget.
 *
 * @package ConstantContactForms
 * @author Constant Contact
 * @since 1.1.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Constant Contact Form Display Widget.
 *
 * @since 1.1.0
 */
class ConstantContactWidget extends WP_Widget {

	/**
	 * ConstantContactWidget constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		$widget_ops = [
			'classname'   => '',
			'description' => esc_html__( 'Display a Constant Contact form.', 'constant-contact-forms' ),
		];

		parent::__construct(
			'ctct_form',
			esc_html__( 'Constant Contact Form', 'constant-contact-forms' ),
			$widget_ops
		);
	}

	/**
	 * Form method.
	 *
	 * @since 1.1.0
	 *
	 * @param array $instance Widget instance.
	 */
	public function form( $instance ) {
		$defaults = [
			'ctct_title'      => '',
			'ctct_form_id'    => 0,
			'ctct_form_title' => '',
		];

		$instance = wp_parse_args( (array) $instance, $defaults );

		$title           = wp_strip_all_tags( $instance['ctct_title'] );
		$form_id         = absint( $instance['ctct_form_id'] );
		$show_form_title = ( 'on' === $instance['ctct_form_title'] ) ? $instance['ctct_form_title'] : '';

		$this->form_input_text( [
			'label_text' => esc_html__( 'Title', 'constant-contact-forms' ),
			'name'       => $this->get_field_name( 'ctct_title' ),
			'id'         => $this->get_field_id( 'ctct_title' ),
			'value'      => $title,
		] );

		$this->form_input_select( [
			'label_text' => esc_html__( 'Form', 'constant-contact-forms' ),
			'name'       => $this->get_field_name( 'ctct_form_id' ),
			'id'         => $this->get_field_id( 'ctct_form_id' ),
			'options'    => $this->get_forms(),
			'value'      => $form_id,
		] );

		$this->form_input_checkbox( [
			'label_text' => esc_html__( 'Display form title', 'constant-contact-forms' ),
			'name'       => $this->get_field_name( 'ctct_form_title' ),
			'id'         => $this->get_field_id( 'ctct_form_title' ),
			'value'      => $show_form_title,
		] );
	}

	/**
	 * Update method.
	 *
	 * @since 1.1.0
	 *
	 * @param array $new_instance New data.
	 * @param array $old_instance Original data.
	 * @return array Updated data.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                 = $old_instance;
		$instance['ctct_title']   = trim( wp_strip_all_tags( $new_instance['ctct_title'] ) );
		$instance['ctct_form_id'] = trim( wp_strip_all_tags( $new_instance['ctct_form_id'] ) );

		if ( empty( $new_instance['ctct_form_title'] ) ) {
			$instance['ctct_form_title'] = '';
		} else {
			$instance['ctct_form_title'] = trim( wp_strip_all_tags( $new_instance['ctct_form_title'] ) );
		}

		return $instance;
	}

	/**
	 * Widget method.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args     Widget args.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		$title           = trim( wp_strip_all_tags( $instance['ctct_title'] ) );
		$form_id         = absint( $instance['ctct_form_id'] );
		$show_form_title = ( ! empty( $instance['ctct_form_title'] ) ) ? 'true' : 'false';

		echo $args['before_widget']; // WPCS: XSS Ok.

		if ( $title ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title']; // WPCS: XSS Ok.
		}

		echo do_shortcode( sprintf( '[ctct form="%s" show_title="%s"]', $form_id, $show_form_title ) );

		echo $args['after_widget']; // WPCS: XSS Ok.
	}

	/**
	 * Get all available forms to display.
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public function get_forms() {

		$args = [
			'post_type'      => 'ctct_forms',
			'posts_per_page' => -1,
			'orderby'        => 'title',
		];

		$forms = new WP_Query( $args );

		if ( $forms->have_posts() ) {
			return array_map( [ $this, 'get_form_fields' ], $forms->posts );
		}

		return [];
	}

	/**
	 * Return an array of post ID and post title.
	 *
	 * @since 1.2.2
	 *
	 * @param WP_Post $post Post object.
	 * @return array
	 */
	public function get_form_fields( $post ) {
		return [ $post->ID => $post->post_title ];
	}

	/**
	 * Return a checkbox.
	 *
	 * @since 1.5.0
	 *
	 * @param array $args Arguments for checkbox.
	 */
	public function form_input_checkbox( $args = [] ) {

		if ( ! empty( $args ) ) {
			$label_text = esc_attr( $args['label_text'] );
			$name       = esc_attr( $args['name'] );
			$id         = esc_attr( $args['id'] );
			$value      = esc_attr( $args['value'] );

			printf(
				'<p><input type="checkbox" class="checkbox" name="%1$s" id="%2$s" %3$s /><label for="%4$s">%5$s</label></p>',
				esc_attr( $name ),
				esc_attr( $id ),
				checked( ! empty( $value ), true, false ),
				esc_attr( $name ),
				esc_html( $label_text )
			);
		}
	}

	/**
	 * Return a text input.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args Arguments for text input.
	 */
	public function form_input_text( $args = [] ) {

		if ( ! empty( $args ) ) {
			$label_text = esc_attr( $args['label_text'] );
			$name       = esc_attr( $args['name'] );
			$id         = esc_attr( $args['id'] );
			$value      = esc_attr( $args['value'] );

			printf(
				'<p><label for="%1$s">%2$s</label><input type="text" class="widefat" name="%3$s" id="%4$s" value="%5$s" /></p>',
				esc_attr( $name ),
				esc_html( $label_text ),
				esc_attr( $name ),
				esc_attr( $id ),
				esc_attr( $value )
			);
		}
	}

	/**
	 * Return a select input.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args Arguments for select input.
	 */
	public function form_input_select( $args = [] ) {
		if ( ! empty( $args ) ) {
			$label_text = esc_attr( $args['label_text'] );
			$name       = esc_attr( $args['name'] );
			$id         = esc_attr( $args['id'] );
			$options    = $args['options'];
			$value      = esc_attr( $args['value'] );

			$selects = '';
			foreach ( $options as $option ) {
				foreach ( $option as $key => $title ) {
					$selects .= sprintf(
						'<option value="%s" %s>%s</option>',
						esc_attr( $key ),
						selected( $value, $key, false ),
						esc_html( $title )
					);
				}
			}
			printf(
				'<p><label for="%1$s">%2$s</label><select class="widefat" name="%3$s" id="%4$s">%5$s</select>',
				esc_attr( $name ),
				esc_html( $label_text ),
				esc_attr( $name ),
				esc_attr( $id ),
				$selects
			);
		}
	}
}

<?php
/**
 * Constant Contact Form Widget.
 *
 * @package ConstantContactForms
 * @author Constant Contact
 * @since 1.1.0
 */

/**
 * Constant Contact Form Display Widget.
 *
 * @since 1.1.0
 */
class ConstantContactWidget extends WP_Widget {

	/**
	 * ConstantContactWidget constructor.
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => '',
		    'description' => esc_html__( 'Display a Constant Contact form.', 'constant-contact-forms' ),
		);
		parent::__construct(
			'ctct_form',
			__( 'Constant Contact Form', 'constant-contact-forms' ),
			$widget_ops
		);
	}

	/**
	 * Form method.
	 *
	 * @since 1.1.0
	 *
	 * @param array $instance Widget instance.
	 * @return void
	 */
	public function form( $instance ) {
		$defaults = array(
			'ctct_title'   => '',
			'ctct_form_id' => 0,
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		$title   = strip_tags( $instance['ctct_title'] );
		$form_id = absint( $instance['ctct_form_id'] );

		$this->form_input_text( array(
			'label_text' => __( 'Title', 'constant-contact-forms' ),
			'name'       => $this->get_field_name( 'ctct_title' ),
			'id'         => $this->get_field_id( 'ctct_title' ),
			'value'      => $title,
		) );

		$this->form_input_select( array(
			'label_text' => __( 'Form', 'constant-contact-forms' ),
			'name'       => $this->get_field_name( 'ctct_form_id' ),
			'id'         => $this->get_field_id( 'ctct_form_id' ),
			'options'    => $this->get_forms(),
			'value'      => $form_id,
		) );
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
		$instance['ctct_title']   = trim( strip_tags( $new_instance['ctct_title'] ) );
		$instance['ctct_form_id'] = trim( strip_tags( $new_instance['ctct_form_id'] ) );

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
		$title   = trim( strip_tags( $instance['ctct_title'] ) );
		$form_id = absint( $instance['ctct_form_id'] );

		echo $args['before_widget'];

		if ( $title ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
		}

		echo do_shortcode( sprintf( '[ctct form="%s"]', $form_id ) );

		echo $args['after_widget'];
	}

	/**
	 * Get all available forms to display.
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public function get_forms() {
		$args = array(
			'post_type'      => 'ctct_forms',
			'posts_per_page' => -1,
			'orderby'        => 'title',
		);
		$forms = new WP_Query( $args );
		if ( $forms->have_posts() ) {
			return array_map( array( $this, 'get_form_fields' ), $forms->posts );
		}

		return array();
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
		return array( $post->ID => $post->post_title );
	}

	/**
	 * Return a text input.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args Arguments for text input.
	 */
	public function form_input_text( $args = array() ) {

		if ( ! empty( $args ) ) {
			$label_text = esc_attr( $args['label_text'] );
			$name       = esc_attr( $args['name'] );
			$id         = esc_attr( $args['id'] );
			$value      = esc_attr( $args['value'] );

			printf(
				'<p><label for="%s">%s</label><input type="text" class="widefat" name="%s" id="%s" value="%s" /></p>',
				$id,
				$label_text,
				$name,
				$id,
				$value
			);
		}
	}

	/**
	 * Return a select input.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args Arguments for select input.
	 * @return void
	 */
	public function form_input_select( $args = array() ) {
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
						$key,
						selected( $value, $key, false ),
						$title
					);
				}
			}
			printf(
				'<p><label for="%s">%s</label><select class="widefat" name="%s" id="%s">%s</select>',
				$id,
				$label_text,
				$name,
				$id,
				$selects
			);
		}
	}
}

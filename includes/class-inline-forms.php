<?php
/**
 * Sign-up Forms
 *
 * @package ConstantContact
 * @subpackage Inline Forms
 * @author Constant Contact
 * @since NEXT
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * This class get's everything up an running for Inline Forms.
 *
 * @since NEXT
 */
class ConstantContact_Inline_Forms {

	/**
	 * Parent plugin class.
	 *
	 * @since NEXT
	 * @var object
	 */
	protected $plugin;

	/**
	 * Inline Forms Slug.
	 *
	 * @since NEXT
	 * @var string
	 */
	private $slug = 'ctct_inline_forms';

	/**
	 * Constructor.
	 *
	 * @since NEXT
	 *
	 * @param object $plugin Parent plugin.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'wp_head', [ $this, 'inject_universal_code' ] );
		add_action( 'cmb2_admin_init', [ $this, 'register_metaboxes' ] );
		add_action( 'cmb2_admin_init', [ $this, 'generated_shortcode' ] );
		add_shortcode('ctct-inline-form', [$this, 'render_inline_form']);
		add_filter( 'manage_ctct_inline_forms_posts_columns', [ $this, 'set_custom_columns' ] );
		add_action( 'manage_ctct_inline_forms_posts_custom_column', [ $this, 'custom_columns' ], 10, 2 );
	}

	/**
	 * Attempt to inject Universal Code of All Sign-up Forms.
	 *
	 * @author Scott Anderson <scott.anderson@webdevstudios.com>
	 * @since  NEXT
	 *
	 * @return void
	 */
	public function inject_universal_code() : void {
		$universal_code         = constant_contact_get_option( '_ctct_signup_universal_code', '' );
		$disable_universal_code = constant_contact_get_option( '_ctct_signup_uc_disable', 'off' );

		if ( '' === $universal_code || 'on' === $disable_universal_code ) {
			return;
		}
		echo $universal_code;
	}

	/**
	 * Register Metaboxes for Inline Forms.
	 *
	 * @author Scott Anderson <scott.anderson@webdevstudios.com>
	 * @since  NEXT
	 *
	 * @return void
	 */
	public function register_metaboxes() {
		$inline_details = new_cmb2_box(array(
			'id'            => 'ctct_inline_metabox',
			'title'         => esc_html__('Inline Forms Details', 'constant-contact-forms'),
			'object_types'  => array('ctct_inline_forms'),
		));

		$inline_details->add_field(array(
			'name'       => esc_html__('Date Received', 'constant-contact-forms'),
			'id'         => 'ctct_inline_code',
			'type'       => 'textarea_code',
		));
	}

	/**
	 * Render Inline Form.
	 *
	 * @author Scott Anderson <scott.anderson@webdevstudios.com>
	 * @since  NEXT
	 * @param  array $args Shortcode Args
	 *
	 * @return string
	 */
	public function render_inline_form( $args ) {

		if ( ! array_key_exists( 'form', $args ) ) {
			return;
		}

		$post_id = absint( sanitize_text_field( $args['form'] ) );

		$inline_code = get_post_meta( $post_id, 'ctct_inline_code', true );

		if ( '' === $inline_code ) {
			return;
		}

		return $inline_code;
	}

	/**
	 * Show a metabox rendering inline forms shortcode.
	 *
	 * @author Scott Anderson <scott.anderson@webdevstudios.com>
	 * @since  NEXT
	 *
	 * @return void
	 */
	public function generated_shortcode() {
		$generated = new_cmb2_box( [
			'id'           => 'ctct_inline_generated_metabox',
			'title'        => esc_html__( 'Shortcode', 'constant-contact-forms' ),
			'object_types' => [ $this->slug ],
			'context'      => 'side',
			'priority'     => 'low',
			'show_names'   => true,
		] );

		$generated->add_field( [
			'name'       => esc_html__( 'Shortcode to use', 'constant-contact-forms' ),
			'id'         => 'ctct_' . 'generated_shortcode',
			'type'       => 'text_medium',
			'desc'       => sprintf(
				/* Translators: Placeholders here represent `<em>` and `<strong>` HTML tags. */
				esc_html__( 'Shortcode to embed — %1$s%2$sYou can copy and paste this in a post to display your form.%3$s%4$s', 'constant-contact-forms' ),
				'<small>',
				'<em>',
				'</em>',
				'</small>'
			),
			'default'    => ( $generated->object_id > 0 ) ? '[ctct-inline-form form="' . $generated->object_id . '"]' : '',
			'attributes' => [
				'readonly' => 'readonly',
			],
		] );
	}

	/**
	 * Add columns to Inline Forms post type.
	 *
	 * @internal
	 *
	 * @since NEXT
	 *
	 * @param array $columns post list columns.
	 * @return array $columns Array of columns to add.
	 */
	public function set_custom_columns( $columns ) {

		$columns['shortcodes']  = esc_html__( 'Shortcode', 'constant-contact-forms' );

		return $columns;
	}

	/**
	 * Content of custom post columns.
	 *
	 * @internal
	 *
	 * @since NEXT
	 *
	 * @param string  $column  Column title.
	 * @param integer $post_id Post id of post item.
	 *
	 * @return void
	 */
	public function custom_columns( $column, $post_id ) {
		$post_id = absint( $post_id );

		if ( ! $post_id ) {
			return;
		}

		$table_list_ids = get_post_meta( $post_id, '_ctct_list', true );
		$table_list_ids = is_array( $table_list_ids ) ? $table_list_ids : [ $table_list_ids ];

		switch ( $column ) {
			case 'shortcodes':
				echo '<div class="ctct-shortcode-wrap"><input class="ctct-shortcode" type="text" value="';
				echo esc_html( '[ctct-inline-form form="' . $post_id . '"]' );
				echo '" readonly="readonly">';
				echo '<button type="button" class="button" data-copied="' . esc_html( 'Copied!', 'constant-contact-forms' ) . '">';
				echo esc_html__( 'Copy', 'constant-contact-forms' );
				echo '</button>';
				echo '</div>';
				break;
			case 'description':
				echo wp_kses_post( wpautop( get_post_meta( $post_id, '_ctct_description', true ) ) );
				break;
		}
	}

}

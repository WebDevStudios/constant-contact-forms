<?php
/**
 * Sign-up Forms
 *
 * @package ConstantContact
 * @subpackage Beaver Builder
 * @author Constant Contact
 * @since NEXT
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * This class get's everything up an running for Sign-up Forms.
 *
 * @since NEXT
 */
class ConstantContact_Signup_Forms {

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
	}

    /**
	 * Attempt to inject Universal Code of Sign-up Form.
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

		$post_id = absint( $args['id'] );

		$inline_code = get_post_meta( $post_id, 'ctct_inline_code', true );
		if ( '' === $inline_code ) {
            return;
        }

		ob_start();
		echo $inline_code;
		return ob_get_clean();
	}

	/**
	 * Show a metabox rendering inline shortcode.
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
			'default'    => ( $generated->object_id > 0 ) ? '[ctct-inline-form id="' . $generated->object_id . '"]' : '',
			'attributes' => [
				'readonly' => 'readonly',
			],
		] );
	}

}

<?php
/**
 * Registers the [ctct] shortcode.
 *
 * @package ConstantContact
 * @subpackage Shortcode
 * @author Constant Contact
 * @since 1.0.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Registers the [ctct] shortcode.
 *
 * @since 1.0.0
 */
class ConstantContact_Shortcode {

	/**
	 * Transient where forms are stored after listed from Constant Contact API.
	 *
	 * @since 1.6.0
	 * @var string
	 */
	const FORMS_LIST_TRANSIENT = 'constant_contact_shortcode_form_list';

	/**
	 * The Shortcode Tag.
	 *
	 * @since 1.6.0
	 * @var string
	 */
	public $tag = 'ctct';

	/**
	 * Plugin object.
	 *
	 * @since 1.6.0
	 * @var Constant_Contact
	 */
	public $plugin;

	/**
	 * Constructor.
	 *
	 * @since 1.6.0
	 *
	 * @param Constant_Contact $plugin Parent plugin class.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Gets the shortcode atts supported by this shortcode. Filterable via shortcode_atts_{$shortcode} filter.
	 *
	 * @since 1.6.0
	 *
	 * @see shortcode_atts()
	 * @return array
	 */
	public function get_atts() {
		return [
			'form'       => '0',
			'show_title' => 'false',
		];
	}

	/**
	 * Registers the [ctct] shortcode.
	 *
	 * @since 1.6.0
	 */
	public function register_shortcode() {
		add_shortcode( $this->tag, [ $this->plugin->get_display_shortcode(), 'render_shortcode' ] );
	}

	/**
	 * Additional cache invalidation for form list transient; runs on save_post.
	 *
	 * @since 1.6.0
	 * @since 2.11.0 added method parameters.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 */
	public function clear_forms_list_transient( int $post_id, WP_Post $post ) {
		if ( ! in_array( $post->post_type, [ 'ctct_forms', 'ctct_lists' ], true ) ) {
			return;
		}
		delete_transient( self::FORMS_LIST_TRANSIENT );
	}
}

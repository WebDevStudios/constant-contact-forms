<?php
/**
 * ConstantContact_Lists class
 *
 * @package ConstantContactLists
 * @subpackage ConstantContact
 * @author Pluginize
 * @since 1.0.0
 */

require_once constant_contact()->dir() . 'vendor/constantcontact/constantcontact/constantcontact/src/Ctct/autoload.php';

use Ctct\ConstantContact;
use Ctct\Exceptions\CtctException;

/**
 * Class ConstantContact_Lists
 */
class ConstantContact_Lists {

	/**
	 * Holds an instance of the object.
	 *
	 * @since 1.0.0
	 * @var ConstantContact_Lists
	 */
	private static $instance = null;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
	}

	/**
	 * Returns the running object
	 *
	 * @since 1.0.0
	 *
	 * @return ConstantContact_Lists
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new ConstantContact_Lists();
			self::$instance->hooks();
		}
		return self::$instance;
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'init', array( $this, 'sync_lists' ) );
	}


	/**
	 * Hooked to WP init.
	 *
	 * @since 1.0.0
	 */
	public function init() {
	}

	/**
	 * Hooked to WP init.
	 *
	 * @since 1.0.0
	 */
	public function sync_lists() {
		global $pagenow;

		if ( ! $token = constantcontact_api()->get_api_token() ) { return; }

		if ( in_array( $pagenow, array( 'edit.php' ), true ) && isset( $_GET['post_type'] ) && 'ctct_lists' === $_GET['post_type'] ) {

			if ( $lists = constantcontact_api()->get_lists() ) {

				foreach ( $lists as $list ) {

					$value = array(
						'value' => $list->id,
						'compare' => '=',
					);
					$args = array(
						'post_type'	=>	'ctct_lists',
						'meta_query'=>	array( $value ),
					);
					$my_query = new WP_Query( $args );

					if ( empty( $my_query->posts ) ) {

						$new_post = array(
							  'post_title'    => wp_strip_all_tags( $list->name ),
							  'post_status'   => 'publish',
							  'post_type' => 'ctct_lists',
						);
						$post = wp_insert_post( $new_post );
						update_post_meta( $post, '_ctct_list_id', $list->id );

					} else {

						$update_post = array(
							'ID' => $my_query->posts[0]->ID,
							'post_title'    => wp_strip_all_tags( $list->name ),
							'post_type' => 'ctct_lists',
						);
						$post = wp_update_post( $update_post );

					}
				}
			}
		}
	}

}

/**
 * Helper function to get/return the ConstantContact_Lists object.
 *
 * @since 1.0.0
 *
 * @return ConstantContact_Lists object.
 */
function constantcontact_lists() {
	return ConstantContact_Lists::get_instance();
}

// Get it started.
constantcontact_lists();

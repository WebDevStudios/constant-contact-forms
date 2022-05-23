<?php
/**
 * Constant Contact v3 + WP Client
 *
 * @package ConstantContact
 * @subpackage Client
 * @author WDS
 * @since 1.0.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Interfaces with necessary Constant Contact V3 Endpoints, utlizing WordPress HTTP APIs as needed.
 *
 * @since 1.14.0
 */
class ConstantContact_Client {

	/**
	 * Base URL for V3.
	 *
	 * @since 1.14.0
	 * @var string
	 */
	private string $base_url = 'https://api.cc.email/v3/';

	/**
	 * Base args for V3 requests.
	 *
	 * @since 1.14.0
	 * @var array
	 */
	private array $base_args = [
		'cache-control' => 'no-cache',
		'content-type'  => 'application/json',
		'accept'        => 'application/json',
	];

	/**
	 * Constructor.
	 *
	 * @since 1.14.0
	 *
	 * @param object $plugin Parent class.
	 */
	public function __construct( $access_token ) {
		$this->base_args['authorization'] = 'Bearer ' . $access_token;
	}

	public function get_account_info() {
		return $this->get( 'account/summary' );
	}

	public function get_contacts( $args = [] ) {

		if ( empty( $args ) ) {
			$args = [ 'status' => 'all' ];
		}

		return $this->get( 'contacts', $args );
	}

	public function add_contact( $args = [] ) {

		return $this->post( 'contacts', $args );
	}

	public function update_contact( $args = [] ) {

		return $this->post( 'contacts/sign_up_form', $args );
	}

	public function get_lists() {
		// Note: probably want to support pulling all the lists, e.g. set limit to 1000, rather than default of 50. Marketers gonna market.
		return $this->get( 'contact_lists', $this->base_args );
	}

	public function get_list( $list_id ) {
		// Note: Major change in V3 is resource IDs are now all UUIDs. Investigate docs. Will need to use the xhref API to get this list prior to attempting to access this resource.
		return $this->get( "contact_lists/$list_id", $this->base_args );
	}

	public function add_list( $list ) {
		// Note: Major change in V3 is resource IDs are now all UUIDs. Investigate docs. Will need to use the xhref API to get this list prior to attempting to access this resource.
		return $this->post( 'contact_lists', $this->base_args );
	}

	public function update_list( $list ) {

		// Note: Major change in V3 is resource IDs are now all UUIDs. Investigate docs. Will need to use the xhref API to get this list prior to attempting to access this resource.
		return $this->put( "contact_lists/$list->id", array_merge( $this->base_args, $list ) );
	}

	public function delete_list( $list_id ) {
		// Note: Major change in V3 is resource IDs are now all UUIDs. Investigate docs. Will need to use the xhref API to get this list prior to attempting to access this resource.
		return $this->delete( "contact_lists/$list_id", $this->base_args );
	}

	private function get( string $endpoint, $args = [] ) : array {

		return wp_safe_remote_get( $this->base_url . $endpoint, array_merge( $args, $this->base_args ) );
	}

	private function post( string $endpoint, $args = [] ) : array {

		return wp_safe_remote_post( $this->base_url . $endpoint, array_merge( $args, $this->base_args ) );
	}

	private function put( string $endpoint, $args = [] ) : array {
		$args['method'] = 'PUT';
		return wp_safe_remote_request( $this->base_url . $endpoint, array_merge( $args, $this->base_args ) );
	}

	private function delete( string $endpoint, $args = [] ) : array {
		$args['method'] = 'DELETE';
		return wp_safe_remote_request( $this->base_url . $endpoint, array_merge( $args, $this->base_args ) );
	}

}

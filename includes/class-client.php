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
 *
 * @todo Support contactService->updateContact in v3, replacing usage in API class.
 *
 * @todo Support listService->getLists in v3, replacing usage in API class.
 * @todo Support listService->getList in v3, replacing usage in API class.
 * @todo Support listService->addList in v3, replacing usage in API class.
 * @todo Support listService->updateList in v3, replacing usage in API class.
 * @todo Support listService->deleteList in v3, replacing usage in API class.
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

		if ( empty( $args ) ) {
			$args = [ 'status' => 'all' ];
		}

		return $this->get( 'contacts', $args );
	}

	public function update_contact( $args = [] ) {

		return $this->get( 'contacts/sign_up_form', $args );
	}

	private function get( string $endpoint, $args = [] ) : array {

		return wp_safe_remote_get( $this->base_url . $endpoint, $args );
	}

	private function post( string $endpoint, $args = [] ) : array {

		return wp_safe_remote_post( $this->base_url . $endpoint, $args );
	}

}

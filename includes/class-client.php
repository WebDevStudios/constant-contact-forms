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
 * @todo Support accountService->getAccountInfo in v3, replacing usage in API class.
 * @todo Support contactService->getContacts in v3, replacing usage in API class.
 * @todo Support contactService->addContact in v3, replacing usage in API class.
 * @todo Support contactService->updateContact in v3, replacing usage in API class.
 * @todo Support listService->getLists in v3, replacing usage in API class.
 * @todo Support listService->getList in v3, replacing usage in API class.
 * @todo Support listService->addList in v3, replacing usage in API class.
 * @todo Support listService->updateList in v3, replacing usage in API class.
 * @todo Support listService->deleteList in v3, replacing usage in API class.
 */
class ConstantContact_Client {

	/**
	 * API Key.
	 *
	 * @since 1.14.0
	 * @var string
	 */
	private string $api_key = '';

	/**
	 * Base URL for V3.
	 *
	 * @since 1.14.0
	 * @var string
	 */
	private string $base_url = 'https://api.cc.email/v3';

	/**
	 * Constructor.
	 *
	 * @since 1.14.0
	 *
	 * @param object $plugin Parent class.
	 */
	public function __construct( $api_key ) {
		$this->api_key = $api_key;
	}

}

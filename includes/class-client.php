<?php
/**
 * Constant Contact v3 + WP Client
 *
 * @package ConstantContact
 * @subpackage Client
 * @author WDS
 * @since 2.0.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Interfaces with necessary Constant Contact V3 Endpoints, utlizing WordPress HTTP APIs as needed.
 *
 * @since 2.0.0
 */
class ConstantContact_Client {

	/**
	 * Base URL for V3.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private string $base_url = 'https://api.cc.email/v3/';

	/**
	 * Base args for V3 requests.
	 *
	 * @since 2.0.0
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
	 * @since 2.0.0
	 *
	 * @param string $access_token Access token.
	 */
	public function __construct( string $access_token ) {
		$this->base_args['authorization'] = 'Bearer ' . $access_token;
	}

	/**
	 * Get account information from Constant Contact for connected account.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_account_info() {
		return $this->get( 'account/summary', $this->base_args );
	}

	/**
	 * Get a list of contacts based on arguments.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args Array of arguments for contacts request
	 * @return array
	 */
	public function get_contacts( array $args = [] ) {

		if ( empty( $args ) ) {
			$args = [ 'status' => 'all' ];
		}
		$args = http_build_query( $args );

		return $this->get( "contacts?$args", $this->base_args );
	}

	/**
	 * Get a specific contact.
	 *
	 * @since 2.0.0
	 *
	 * @param string $contact_id Contact ID
	 * @param array  $args       Array of arguments for the contact request.
	 * @return array
	 */
	public function get_contact( string $contact_id, array $args = [] ) {
		$args = http_build_query( $args );
		return $this->get( "contacts/{$contact_id}?$args", $this->base_args );
	}

	/**
	 * Create or update a contact in Constant Contact.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args Array of arguments for the contact.
	 * @return array
	 */
	public function create_update_contact( array $args = [] ) {
		return $this->post( 'contacts/sign_up_form', $this->base_args, $args );
	}

	/**
	 * Get configured custom fields for contacts.
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public function get_custom_fields() {
		return $this->get( 'contact_custom_fields', $this->base_args );
	}

	/**
	 * Get specific custom field for a contact.
	 *
	 * @since 2.0.0
	 *
	 * @param string $field_id Custom Field ID.
	 * @return array
	 */
	public function get_custom_field( string $field_id ) {
		return $this->get( "contact_custom_fields/{$field_id}", $this->base_args );
	}

	/**
	 * Check if a specific custom field exists.
	 *
	 * @since 2.0.0
	 *
	 * @param string $field_name The name of the field to check for.
	 * @return bool
	 */
	public function custom_field_exists( string $field_name ) {
		$fields = $this->get_custom_fields();
		if ( ! empty( $fields ) && array_key_exists( 'custom_fields', $fields ) ) {
			$field_keys = wp_list_pluck( $fields['custom_fields'], 'label' );
			return in_array( $field_name, $field_keys, true );
		}
		return false;
	}

	/**
	 * Get a custom field value by custom field name.
	 *
	 * @since 2.0.0
	 *
	 * @param string $field_name Field name to get value for.
	 * @return mixed|string
	 */
	public function get_custom_field_by_name( string $field_name ) {
		$fields = $this->get_custom_fields();
		if ( ! empty( $fields ) && array_key_exists( 'custom_fields', $fields ) ) {
			foreach ( $fields['custom_fields'] as $field ) {
				if ( $field['label'] === $field_name ) {
					return $field;
				}
			}
		}
		return '';
	}

	/**
	 * Add a custom field.
	 *
	 * @since 2.0.0
	 *
	 * @param array $field_data Array of custom field data.
	 * @return array
	 */
	public function add_custom_field( array $field_data ) {
		return $this->post( 'contact_custom_fields', $this->base_args, $field_data );
	}

	/**
	 * Add a note to a specific contact.
	 *
	 * @since 2.0.0
	 *
	 * @param array $updated_contact_data Contact data.
	 * @return array
	 */
	public function add_note( array $updated_contact_data ) {
		$contact_id = $updated_contact_data['contact_id'];
		return $this->put( "contacts/{$contact_id}", $this->base_args, $updated_contact_data );
	}

	/**
	 * Get lists associated with the connected account.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_lists() {
		// Note: probably want to support pulling all the lists, e.g. set limit to 1000, rather than default of 50. Marketers gonna market.
		return $this->get( 'contact_lists?include_membership_count=all', $this->base_args );
	}

	/**
	 * Get a specific list by list ID.
	 *
	 * @since 2.0.0
	 *
	 * @param string $list_id ID of the list to retrieve.
	 * @return array
	 */
	public function get_list( string $list_id ) {
		return $this->get( "contact_lists/$list_id?include_membership_count=all", $this->base_args );
	}

	/**
	 * Create a new list.
	 *
	 * @since 2.0.0
	 *
	 * @param object $list List data.
	 * @return array
	 */
	public function add_list( array $list ) {
		return $this->post( 'contact_lists', $this->base_args, $list );
	}

	/**
	 * Update an existing list.
	 *
	 * @since 2.0.0
	 *
	 * @param object $list List data.
	 * @return array
	 */
	public function update_list( array $list ) {
		return $this->put( "contact_lists/{$list['id']}", $this->base_args, $list );
	}

	/**
	 * Delete an existing list.
	 *
	 * @since 2.0.0
	 *
	 * @param string $list_id ID of the list to delete.
	 * @return array
	 */
	public function delete_list( string $list_id ) {
		return $this->delete( "contact_lists/$list_id", $this->base_args );
	}

	/**
	 * Get a list of updated list IDs.
	 *
	 * @since 2.0.0
	 *
	 * @param string $old_ids_string Comma separated string of version2 list IDs
	 * @return array Version3 list IDs for provided lists.
	 */
	public function get_updated_lists_ids( $old_ids_string ) {
		return $this->get( "contact_lists/list_id_xrefs?sequence_ids={$old_ids_string}", $this->base_args );
	}

	/**
	 * GET method for API requests.
	 *
	 * @since 2.0.0
	 *
	 * @param string $endpoint Endpoint to query.
	 * @param array  $args     Arguments to use with query.
	 *
	 * @return array
	 */
	private function get( string $endpoint, array $args = [] ) : array {

		$options = [
			'headers' => $args,
		];

		$url = $this->base_url . $endpoint;

		$response = wp_safe_remote_get( $url, $options );
		if ( is_wp_error( $response ) ) {
			// todo: handle exception
			return (array) $response;
		}

		return json_decode( $response['body'], true );
	}

	/**
	 * POST method for API requests.
	 *
	 * @since 2.0.0
	 *
	 * @param string $endpoint Endpoint to query.
	 * @param array  $args     Arguments to use with query.
	 * @param array  $body     POST body content to send.
	 *
	 * @return array
	 */
	private function post( string $endpoint, array $args = [], array $body = [] ) : array {

		$options = [
			'headers' => $args,
		];

		if ( isset( $body ) ) {
			$options['body'] = wp_json_encode( $body );
		}

		$url = $this->base_url . $endpoint;

		$response = wp_safe_remote_post( $url, $options );

		if ( is_wp_error( $response ) ) {
			// todo: handle exception
			return (array) $response;
		}

		return json_decode( $response['body'], true );
	}

	/**
	 * PUT method for API requests.
	 *
	 * @since 2.0.0
	 *
	 * @param string $endpoint Endpoint to query.
	 * @param array  $args     Arguments to use with query.
	 * @param array  $body     PUT body content to send.
	 *
	 * @return array
	 */
	private function put( string $endpoint, array $args = [], array $body = [] ) : array {

		$options = [
			'headers' => $args,
		];

		if ( isset( $body ) ) {
			$options['body'] = wp_json_encode( $body );
		}

		$url = $this->base_url . $endpoint;

		$options['method'] = 'PUT';

		$response = wp_safe_remote_request( $url, $options );

		if ( is_wp_error( $response ) ) {
			// todo: handle exception
			return (array) $response;
		}

		return json_decode( $response['body'], true );
	}

	/**
	 * DELETE method for API requests.
	 *
	 * @since 2.0.0
	 *
	 * @param string $endpoint Endpoint to query.
	 * @param array  $args     Arguments to use with query.
	 *
	 * @return array
	 */
	private function delete( string $endpoint, array $args = [] ) : array {
		$options = [
			'headers' => $args,
		];

		$url = $this->base_url . $endpoint;

		$options['method'] = 'DELETE';

		$response = wp_safe_remote_request( $url, $options );

		if ( is_wp_error( $response ) ) {
			// todo: handle exception
			return (array) $response;
		}

		return json_decode( $response['body'], true );
	}
}

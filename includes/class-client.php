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
		return $this->get( 'account/summary', $this->base_args );
	}

	public function get_contacts( $args = [] ) {

		if ( empty( $args ) ) {
			$args = [ 'status' => 'all' ];
		}
		$args = http_build_query( $args );

		return $this->get( "contacts?$args", $this->base_args );
	}

	public function get_contact( $contact_id, $args = [] ) {
		$args = http_build_query( $args );
		return $this->get( "contacts/{$contact_id}?$args", $this->base_args );
	}

	public function create_update_contact( $args = [] ) {
		return $this->post( 'contacts/sign_up_form', $this->base_args, $args );
	}

	public function get_custom_fields() {
		return $this->get( 'contact_custom_fields', $this->base_args );
	}

	public function get_custom_field( $field_id ) {
		return $this->get( "contact_custom_fields/{$field_id}", $this->base_args );
	}

	public function custom_field_exists( $field_name ) {
		$fields = $this->get_custom_fields();
		if ( ! empty( $fields ) && array_key_exists( 'custom_fields', $fields ) ) {
			$field_keys = wp_list_pluck( $fields['custom_fields'], 'label' );
			return in_array( $field_name, $field_keys );
		}
		return false;
	}

	public function get_custom_field_by_name( $field_name ) {
		$fields = $this->get_custom_fields();
		if ( ! empty( $fields ) && array_key_exists( 'custom_fields', $fields ) ) {
			foreach( $fields['custom_fields'] as $field ) {
				if ( $field['label'] === $field_name ) {
					return $field;
				}
			}
		}
		return '';
	}

	public function add_custom_field( $field_data ) {
		return $this->post( 'contact_custom_fields', $this->base_args, $field_data );
	}

	public function add_note( $updated_contact_data ) {
		$contact_id = $updated_contact_data['contact_id'];
		return $this->put( "contacts/{$contact_id}", $this->base_args, $updated_contact_data  );
	}

	public function get_lists() {
		// Note: probably want to support pulling all the lists, e.g. set limit to 1000, rather than default of 50. Marketers gonna market.
		return $this->get( 'contact_lists?include_membership_count=all', $this->base_args );
	}

	public function get_list( $list_id ) {
		return $this->get( "contact_lists/$list_id?include_membership_count=all", $this->base_args );
	}

	public function add_list( $list ) {
		return $this->post( 'contact_lists', $this->base_args, $list );
	}

	public function update_list( $list ) {
		return $this->put( "contact_lists/$list->id", $this->base_args, $list );
	}

	public function delete_list( $list_id ) {
		return $this->delete( "contact_lists/$list_id", $this->base_args );
	}

	public function get_updated_lists_ids( $old_ids_string ) {
		return $this->get( "contact_lists/list_id_xrefs?sequence_ids={$old_ids_string}", $this->base_args );
	}

	private function get( string $endpoint, $args = [] ) : array {

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

	private function post( string $endpoint, $args = [], $body = [] ) : array {

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

	private function put( string $endpoint, $args = [], $body = [] ) : array {

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

	private function delete( string $endpoint, $args = [] ) : array {
		$options = [
			'headers' => $this->base_args,
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

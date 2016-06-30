<?php
/**
 * ConstantContact_Mail class
 *
 * @package ConstantContact_Mail
 * @subpackage ConstantContact
 * @author Pluginize
 * @since 1.0.0
 */


/**
 * Class ConstantContact_Mail
 */
class ConstantContact_Mail {

	/**
	 * Parent plugin class
	 *
	 * @var   class
	 * @since 0.0.1
	 */
	protected $plugin = null;

	/**
	 * Constructor
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Returns the running object
	 *
	 * @since 1.0.0
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

	}

	/**
	 * Process our form values
	 *
	 * @since  1.0.0
	 * @param  array $values submitted form values
	 */
	public function submit_form_values( $values = array(), $add_to_opt_in = false ) {

		// Sanity check
		if ( ! is_array( $values ) ) {
			return;
		}

		// Clean our values
		$values = constant_contact()->process_form->clean_values( $values );
		if ( $add_to_opt_in ) {
			$this->opt_in_user( $values );
		}

		// pretty our values
		$values = constant_contact()->process_form->pretty_values( $values );

		/* Potentially send to constant contact
		if ( constant_contact()->api->is_connected() ) {
			$this->send_to_constant_contact( $values );
		} */

		// Format them
		$email_values = $this->format_values_for_email( $values );

		// Send the mail
		return $this->mail( $this->get_email(), $email_values );
	}

	/**
	 * Opts in a user, if requested
	 *
	 * @since  1.0.0
	 * @param  array $values submitted values
	 * @return object         response from API
	 */
	public function opt_in_user( $values ) {

		// Set our default vars
		$email      = '';
		$first_name = '';
		$last_name  = '';

		// go through all our fields
		foreach ( $values as $val ) {

			// Make sure we have our data to check set
			$key = isset( $val['key'] ) ? $val['key'] : '';
			$val = isset( $val['value'] ) ? $val['value'] : '';

			// @TODO here we should probably do this for every possible field type
			// or maybe grab the fields that exist inthe list and push them up

			// Loop through our form and pluck out our email and names
			switch ( $key ) {
				case 'email':
					$email = $val;
					continue;
					break;
				case 'first_name':
					$first_name = $val;
					continue;
					break;
				case 'last_name':
					$last_name = $val;
					continue;
					break;
			}
		}

		// Make sure we have an email set
		if ( $email ) {
			$args = array(
				'email'      => sanitize_email( $email ),
				'list'       => sanitize_text_field( $_POST['ctct-opti-in'] ),
				'first_name' => sanitize_text_field( $first_name ),
				'last_name'  => sanitize_text_field( $last_name ),
			);

			$contact = constantcontact_api()->add_contact( $args );
		}
	}

	/**
	 * Formats values for email
	 *
	 * @since  1.0.0
	 * @param  array $values values to format
	 * @return string         html content for email
	 */
	public function format_values_for_email( $pretty_vals ) {

		$return = '';
		foreach ( $pretty_vals as $val ) {

			// force vars to exist
			$label = isset( $val['orig_key'] ) ? $val['orig_key'] : false;

			// If we have a label
			if ( $label ) {
				// break out our unique key
				$label = explode( '___', $label );

				// Uppercase and format to be human readable
				$label = ucwords( str_replace( '_', ' ', $label[0] ) );
			}
			$value = isset( $val['post'] ) ? $val['post'] : '&nbsp;';

			$return .= '<p>' . sanitize_text_field( $label ) . ': ' . sanitize_text_field( $value ) . '</p>';
		}

		return $return;
	}

	/**
	 * Get the email address to send to
	 *
	 * @since  1.0.0
	 * @return string email address to send to
	 */
	public function get_email() {

		// Eventually we'll make this configurable
		return get_option( 'admin_email' );
	}

	/**
	 * Sends our mail out
	 *
	 * @since  1.0.0
	 * @param  string $destination_email email address
	 * @param  array  $data              data from clean values
	 * @return bool                    if sent
	 */
	public function mail( $destination_email, $content ) {

		// If we didn't get passed in a sanitized email, we know something is
		// wonky here, so bail out
		if ( sanitize_email( $destination_email ) != $destination_email ) {
			return false;
		}

		// Filter to allow sending HTML for our message body
		add_filter( 'wp_mail_content_type', array( $this, 'set_email_type' ) );

		// Send that mail
		$mail_status = wp_mail(
			$destination_email,
			__( 'New submission' ),
			$content
		);

		// Clean up, remove the filter we had set
		remove_filter( 'wp_mail_content_type', array( $this, 'set_email_type' ) );

		// Return the mail status
		return $mail_status;
	}

	/**
	 * Helper method to return 'text/html' string for actions
	 *
	 * @since  1.0.0
	 */
	public function set_email_type() {
		return 'text/html';
	}
}

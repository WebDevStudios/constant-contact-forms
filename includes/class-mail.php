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

		// If a user opted-in, push their data to CC
		if ( $add_to_opt_in ) {
			$this->opt_in_user( $values );
		}

		// pretty our values
		$values = constant_contact()->process_form->pretty_values( $values );

		/*
		Potentially send to constant contact
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

		// go through all our fields
		foreach ( $values as $key => $val ) {

			// Clean up our data that we'll be using
			$key  = sanitize_text_field( isset( $val['key'] ) ? $val['key'] : '' );
			$orig = sanitize_text_field( isset( $val['orig_key'] ) ? $val['orig_key'] : '' );
			$val  = sanitize_text_field( isset( $val['value'] ) ? $val['value'] : '' );

			// Make sure we have a key that we can use
			if ( $key && 'ctct-opt-in' != $key && 'ctct-id' != $key ) {

				// Set our args that we'll pass to our API
				$args[ $orig ] = array(
					'key' => $key,
					'val' => $val,
				);

				// If we have an email, make sure we keep it safe
				if ( 'email' == $key ) {
					$args['email'] = $val;
				}
			}
		}

		// Make sure we have an email set
		if ( isset( $values['ctct-opt-in'] ) && isset( $values['ctct-opt-in']['value'] ) ) {

			// Make sure that our list is a top level
			$args['list'] = sanitize_text_field( $values['ctct-opt-in']['value'] );

			// Send that to our API
			return constantcontact_api()->add_contact( $args );
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

		// Before text for content
		$content_before = __( 'Congratulations! Your Constant Contact Forms plugin has successfully captured new information:', 'constantcontact' );

		// After text for content
		$content_after = __( "Don't forget: Email marketing is a great way to stay connected with visitors after they’ve left your site. To make the most of the information you collect, connect the plugin to an active Constant Contact account. Just go to the Connect page from the Plugin console view.", 'constantcontact' );

		// Tie our contents together
		$content = $content_before . $content . $content_after;

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

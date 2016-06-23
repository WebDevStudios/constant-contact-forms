<?php
/**
 * ConstantContact_Process_Form class
 *
 * @package ConstantContactProcessForm
 * @subpackage ConstantContact
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Class ConstantContact_Process_Form
 */
class ConstantContact_Process_Form {

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
	 * @since  0.0.1
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function hooks() {
		add_action( 'cmb2_init', array( $this, 'process_form' ) );
	}

	/**
	 * Process submitted form data
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function process_form() {

		$notice = array();

	    // If the submit button is clicked, send the email.
	    if ( isset( $_POST['ctct-submitted'] ) ) {

			foreach ( $_POST as $key => $value ) {
			    if ( isset( $key ) ) {
					if ( 'ctct-email' === $key ) {
						$email = sanitize_email( $key );
					} else {
						${$key} = sanitize_text_field( $value );
					}
				}
			}

			if ( isset( $_POST['ctct-opti-in'] ) ) {

				$args = array(
					'email' => sanitize_email( $_POST['ctct-email'] ),
					'list' => sanitize_text_field( $_POST['ctct-opti-in'] ),
					'first_name' => 'test name',
					'last_name' => '',
				);

				$contact = constantcontact_api()->add_contact( $args );

				if ( $contact ) {
					set_transient( 'ctct_form_submit_message', 'success' );
				} else {
					set_transient( 'ctct_form_submit_message', 'error' );
				}
			} else {

		        // // sanitize form values
		        // $name   = isset( $_ctct_first_name ) ? $_ctct_first_name : '';
				// $subject = '';
				// $message = '';
				//
		        // // get the blog administrator's email address
		        // $to = get_option( 'admin_email' );
		        // $headers = "From: $name <$email>" . "\r\n";

		        // // If email has been process for sending, display a success message
		        // if ( wp_mail( $to, $subject, $message, $headers ) ) {
		        //     echo '<div>';
		        //     echo '<p>Thanks for contacting me, expect a response soon.</p>';
		        //     echo '</div>';
		        // } else {
		        //     echo 'An unexpected error occurred';
		        // }

			}
		}
	}

	/**
	 * Form submit success/error messages.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function submit_message( $mode = 'echo ') {

		if ( $message = get_transient( 'ctct_form_submit_message' ) ) {

			switch ( $message ) {
				case 'success':
					$message_text = __( 'Your message has been sent!', 'constantcontact' );
				break;
				case 'error':
					$message_text = __( 'Your message failed to send!', 'constantcontact' );
				break;
			}

			$return = sprintf( '<p class="message ' . esc_attr( $message ) . '"> %s </p>', esc_attr( $message_text ) );

			delete_transient( 'ctct_form_submit_message' );

			if ( 'echo' == $mode ) {
				echo $return;
			} else {
				return $return;
			}
		}
	}
}

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
	 * Holds an instance of the object.
	 *
	 * @since 1.0.0
	 * @var ConstantContact_Process_Form
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
	 * @return ConstantContact_Process_Form
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new ConstantContact_Process_Form();
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
		add_action( 'init', array( $this, 'process_form' ) );
	}


	public function process_form() {

	    // if the submit button is clicked, send the email
	    if ( isset( $_POST['ctct-submitted'] ) ) {

			error_log( print_r( $_POST, true ) );

			foreach ($_POST as $key => $value) {
			    if ( isset( $key ) ) {
					if ( 'ctct-email' === $key ) {
						$email = sanitize_email( $key );
					} else {
						${$key} = sanitize_text_field( $value );
					}
				}
			}

	        // sanitize form values
	        $name   = $_ctct_first_name;
			$subject = '';
			$message = '';

	        // get the blog administrator's email address
	        $to = get_option( 'admin_email' );

	        $headers = "From: $name <$email>" . "\r\n";

	        // If email has been process for sending, display a success message
	        if ( wp_mail( $to, $subject, $message, $headers ) ) {
	            echo '<div>';
	            echo '<p>Thanks for contacting me, expect a response soon.</p>';
	            echo '</div>';
	        } else {
	            echo 'An unexpected error occurred';
	        }
	    }
	}
}

/**
 * Helper function to get/return the constantcontact_process_form object.
 *
 * @since 1.0.0
 *
 * @return constantcontact_process_form object.
 */
function constantcontact_process_form() {
	return ConstantContact_Process_Form::get_instance();
}

// Get it started.
constantcontact_process_form();

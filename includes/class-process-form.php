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
}

/**
 * Helper function to get/return the constantcontact_process_form object.
 *
 * @since 1.0.0
 * @return object constantcontact_process_form.
 */
function constantcontact_process_form() {
	return ConstantContact_Process_Form::get_instance();
}

// Get it started.
constantcontact_process_form();

/**
 * Form submit success/error messages.
 *
 * @since 1.0.0
 * @return void
 */
function ctct_form_submit_message() {

	if ( $message = get_transient( 'ctct_form_submit_message' ) ) {

		switch ( $message ) {
			case 'success':
				$message_text = 'Your message has been sent!';
			break;
			case 'error':
				$message_text = 'Your message failed to send!';
			break;

		}

		echo sprintf( '<p class="message ' . esc_attr( $message ) . '"> %s </p>', esc_attr( $message_text ) );

		delete_transient( 'ctct_form_submit_message' );

	}
}

/**
 * Build form fields for shortcode
 *
 * @since 1.0.0
 * @param  array $form_data formulated cmb2 data for form.
 * @return void
 */
function ctct_build_form_fields( $form_data ) {

	foreach ( $form_data['fields'] as $key => $value ) {

		$required = isset( $form_data['fields'][ $key ]['required'] ) ? ' * required' : '';

		echo '<div><p><label>' . esc_attr( $form_data['fields'][ $key ]['name'] ) . esc_attr( $required ) . '</label></br>';

		$field_name = esc_attr( $form_data['fields'][ $key ]['map_to'] );
		$field_value = ( isset( $_POST[ 'ctct-' . $form_data['fields'][ $key ]['map_to'] ] ) ? esc_attr( $_POST[ 'ctct-' . $form_data['fields'][ $key ]['map_to'] ] ) : '' );

		switch ( $form_data['fields'][ $key ]['map_to'] ) {

			case 'email':
					echo '<input type="email" required name="ctct-' . esc_attr( $field_name ) . '" value="' . esc_attr( $field_value ) . '" tabindex="1" size="40"></p></div>';
			break;
			default:
					echo '<input type="text" pattern="[a-zA-Z0-9 ]+" name="ctct-' . esc_attr( $field_name ) . '" value="' . esc_attr( $field_value ) . '" tabindex="1" size="40"></p></div>';
			break;

		}
	}

	if ( isset( $form_data['options']['opt_in'] ) && isset( $form_data['options']['list'] ) ) {
		?>
			<div><p>
				<input type="checkbox" name="ctct-opti-in" value="<?php echo esc_attr( $form_data['options']['list'] ); ?>"/>
				<?php echo esc_attr( $form_data['options']['opt_in'] ); ?>
			</p></div>
		<?php
	}

}


function ctct_validate_form_fields( $form_data ) {
	return true;
}

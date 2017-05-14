<?php
/**
 * Constant Contact Mail
 *
 * @package ConstantContact
 * @subpackage Mail
 * @author Constant Contact
 * @since 1.0.2
 */

/**
 * Wrapper functions for mailing successful contact forms to the user.
 */
class ConstantContact_Mail {

	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $plugin Parent plugin class.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Fire hoosk for actions.
	 *
	 * @since 1.0.0
	 */
	protected function hooks() {
		add_action( 'ctct_schedule_form_opt_in', array( $this, 'opt_in_user' ) );
	}

	/**
	 * Process our form values.
	 *
	 * @since 1.0.0
	 *
	 * @param array $values        Submitted form values.
	 * @param bool  $add_to_opt_in Whether or not to add to opt in.
	 * @return bool
	 */
	public function submit_form_values( $values = array(), $add_to_opt_in = false ) {

		// Sanity check.
		if ( ! is_array( $values ) ) {
			return false;
		}

		// Clean our values.
		$values = constant_contact()->process_form->clean_values( $values );

		// If a user opted-in and we're still connected, push their data to CC.
		if ( $add_to_opt_in && constant_contact()->api->is_connected() ) {

			$maybe_bypass = ctct_get_settings_option( '_ctct_bypass_cron', '' );

			if ( 'on' !== $maybe_bypass ) {
				/**
				 * Filters the delay between scheduling of the opt-in e-mail event.
				 * @since 1.0.2
				 *
				 * @param int $schedule_delay The time to add to `time()` for the event.
				 */
				$schedule_delay = apply_filters( 'constant_contact_opt_in_delay', MINUTE_IN_SECONDS );
				wp_schedule_single_event( time() + absint( $schedule_delay ), 'ctct_schedule_form_opt_in', array( $values ) );
			}
		}

		// Preserve form ID for mail() method. Lost in pretty_values() pass.
		$submission_details                    = array();
		$submission_details['form_id']         = $values['ctct-id']['value'];
		$submission_details['submitted_email'] = $this->get_user_email_from_submission( $values );

		// Pretty our values.
		$values = constant_contact()->process_form->pretty_values( $values );

		// Format them.
		$email_values = $this->format_values_for_email( $values );

		// Increment our counter for processed form entries.
		constant_contact()->process_form->increment_processed_form_count();

		// Skip sending e-mail if we're connected and the user has opted out of notification emails.
		if ( constant_contact()->api->is_connected() && ( 'on' === ctct_get_settings_option( '_ctct_disable_email_notifications' ) ) ) {
			return true;
		}

		// Send the mail.
		return $this->mail( $this->get_email(), $email_values, $submission_details );
	}

	/**
	 * Opts in a user, if requested.
	 *
	 * @since 1.0.0
	 *
	 * @param array $values Submitted values.
	 * @return object Response from API.
	 */
	public function opt_in_user( $values ) {

		// Go through all our fields.
		foreach ( $values as $key => $val ) {

			// Clean up our data that we'll be using.
			$key  = sanitize_text_field( isset( $val['key'] ) ? $val['key'] : '' );
			$orig = sanitize_text_field( isset( $val['orig_key'] ) ? $val['orig_key'] : '' );
			$val  = sanitize_text_field( isset( $val['value'] ) ? $val['value'] : '' );

			// Make sure we have a key that we can use.
			if ( $key && ( 'ctct-opt-in' !== $key ) && ( 'ctct-id' !== $key ) ) {

				// Set our args that we'll pass to our API.
				$args[ $orig ] = array(
					'key' => $key,
					'val' => $val,
				);

				// If we have an email, make sure we keep it safe.
				if ( 'email' === $key ) {
					$args['email'] = $val;
				}
			}
		}

		// Make sure we have an email set.
		if ( isset( $values['ctct-opt-in'] ) && isset( $values['ctct-opt-in']['value'] ) ) {

			// Make sure that our list is a top level.
			$args['list'] = sanitize_text_field( $values['ctct-opt-in']['value'] );

			// Send that to our API.
			return constantcontact_api()->add_contact( $args, $values['ctct-id']['value'] );
		}
	}

	/**
	 * Formats values for email.
	 *
	 * @since 1.0.0
	 *
	 * @param array $pretty_vals Values to format.
	 * @return string HTML content for email.
	 */
	public function format_values_for_email( $pretty_vals ) {

		$return = '';
		foreach ( $pretty_vals as $val ) {

			$label = isset( $val['orig_key'] ) ? $val['orig_key'] : false;

			if ( $label ) {
				// Break out our unique key.
				$label = explode( '___', $label );

				// Uppercase and format to be human readable.
				$label = ucwords( str_replace( '_', ' ', $label[0] ) );
			}
			$value = isset( $val['post'] ) ? $val['post'] : '&nbsp;';

			$return .= '<p>' . sanitize_text_field( $label ) . ': ' . sanitize_text_field( $value ) . '</p>';
		}

		return $return;
	}

	/**
	 * Get the email address to send to.
	 *
	 * @since 1.0.0
	 *
	 * @return string Email address to send to.
	 */
	public function get_email() {

		$email = get_option( 'admin_email' );

		/**
		 * Filters the email to send Constant Contact Forms admin emails to.
		 *
		 * @since 1.3.0
		 *
		 * @param string $email Email address to send to. Default admin_email option.
		 */
		return apply_filters( 'constant_contact_destination_email', $email );
	}

	/**
	 * Sends our mail out.
	 *
	 * @since 1.0.0
	 *
	 * @param string $destination_email  Intended mail address.
	 * @param string $content            Data from clean values.
	 * @param array  $submission_details Details for submission to process.
	 * @return bool Whether or not sent.
	 */
	public function mail( $destination_email, $content, $submission_details ) {

		// Define a mail key for the cache.
		static $last_sent = false;

		$screen = '';
		// Sanity check for get_current_screen, as we may run too early.
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
		}

		$mail_key = md5( "{$destination_email}:{$content}:" . ( isset( $screen->id ) ? $screen->id : '' ) );

		// If we already have sent this e-mail, don't send it again.
		if ( $last_sent === $mail_key ) {
			$this->maybe_log_mail_status(
				vsprintf(
					/* translators: this is only used when some debugging is enabled */
					__( 'Duplicate send mail for: %1$s and: %2$s', 'constant-contact-forms' ),
					array(
						$destination_email,
						$mail_key,
					)
				),
				$destination_email,
				$mail_key
			);
			return true;
		}

		// If we didn't get passed in a sanitized email, we know something is
		// wonky here, so bail out.
		if ( sanitize_email( $destination_email ) !== $destination_email ) {
			return false;
		}

		// Filter to allow sending HTML for our message body.
		add_filter( 'wp_mail_content_type', array( $this, 'set_email_type' ) );

		$content_before = __( 'Congratulations! Your Constant Contact Forms plugin has successfully captured new information:', 'constant-contact-forms' );

		$content_after = __( "Don't forget: Email marketing is a great way to stay connected and engage with visitors after they've left your site. When you connect to a Constant Contact account, all new subscribers are automatically synced so you can keep the interaction going through emails and more. Sign up for a Free Trial on the Connect page in the Plugin console view.", 'constant-contact-forms' );

		/**
		 * Filters the final constructed email content to be sent to an admin.
		 *
		 * @since 1.3.0
		 *
		 * @param string $value   Constructed email content.
		 * @param string $form_id Current form ID being processed.
		 */
		$content = apply_filters( 'constant_contact_email_content', $content_before . $content . $content_after, $submission_details['form_id'] );

		/**
		 * Fires before the queuing of the email to be sent.
		 *
		 * @since 1.3.0
		 *
		 * @param string $value             Current form ID being processed.
		 * @param string $value             Submitted email address.
		 * @param string $destination_email Current destination for the email.
		 * @param string $content           Constructed email content.
		 */
		do_action( 'constant_contact_before_email_send', $submission_details['form_id'], $submission_details['submitted_email'], $destination_email, $content );

		$mail_status = wp_mail(
			$destination_email,
			/**
			 * Filters the email subject to be sent to an admin.
			 *
			 * @since 1.3.0
			 *
			 * @param string $value Constructed email subject.
			 */
			apply_filters( 'constant_contact_email_subject', __( 'Great News: You just captured a new visitor submission', 'constant-contact-forms' ) ),
			$content
		);

		/**
		 * Fires after the queuing of the email to be sent.
		 *
		 * @since 1.3.0
		 *
		 * @param string $value             Current form ID being processed.
		 * @param string $value             Submitted email address.
		 * @param string $destination_email Current destination for the email.
		 * @param string $content           Constructed email content.
		 */
		do_action( 'constant_contact_after_email_send', $submission_details['form_id'], $submission_details['submitted_email'], $destination_email, $content );

		$this->maybe_log_mail_status( $mail_status, $destination_email, $content );

		// Clean up, remove the filter we had set.
		remove_filter( 'wp_mail_content_type', array( $this, 'set_email_type' ) );

		// Store this for later.
		if ( $mail_status ) {
			$last_sent = $mail_key;
		}

		return $mail_status;
	}

	/**
	 * Helper method to return 'text/html' string for actions.
	 *
	 * @since 1.0.0
	 */
	public function set_email_type() {
		return 'text/html';
	}

	/**
	 * If our mail debugging is set, then log mail statuses to the error log.
	 *
	 * @since 1.0.0
	 *
	 * @param string $status     Status from wp_mail.
	 * @param string $dest_email Destination email.
	 * @param string $content    Content of email.
	 */
	public function maybe_log_mail_status( $status, $dest_email, $content ) {

		if ( defined( 'CONSTANT_CONTACT_DEBUG_MAIL' ) && CONSTANT_CONTACT_DEBUG_MAIL ) {

			// Log status of mail.
			error_log( 'mail attempted for ' . $dest_email . ': ' . $status );

			// Log content too just in case.
			error_log( print_r( $content, true ) );
		}

	}

	/**
	 * Retrieve submitted email address from form values.
	 *
	 * @since 1.3.0
	 *
	 * @param array $values Values submitted to form.
	 * @return mixed
	 */
	public function get_user_email_from_submission( $values = array() ) {
		foreach ( $values as $key => $value ) {
			if ( false === strpos( $key, 'email___' ) ) {
				continue;
			}
			return $value['value'];
		}
	}
}

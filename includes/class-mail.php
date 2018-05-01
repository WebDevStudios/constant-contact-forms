<?php
/**
 * Constant Contact Mail
 *
 * @package ConstantContact
 * @subpackage Mail
 * @author Constant Contact
 * @since 1.0.2
 */

#require_once constant_contact()->path . '/vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

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
				 *
				 * @since 1.0.2
				 *
				 * @param int $schedule_delay The time to add to `time()` for the event.
				 */
				$schedule_delay = apply_filters( 'constant_contact_opt_in_delay', MINUTE_IN_SECONDS );
				wp_schedule_single_event( time() + absint( $schedule_delay ), 'ctct_schedule_form_opt_in', array( $values ) );
			}
		}

		$opt_in_details = ( isset( $values['ctct-opt-in'] ) ) ? $values['ctct-opt-in'] : array();

		// Preserve form ID for mail() method. Lost in pretty_values() pass.
		$submission_details                    = array();
		$submission_details['form_id']         = $values['ctct-id']['value'];
		$submission_details['submitted_email'] = $this->get_user_email_from_submission( $values );

		// Pretty our values.
		$values = constant_contact()->process_form->pretty_values( $values );

		// Format them.
		$email_values = $this->format_values_for_email( $values );
		$was_forced = false; // Set a value regardless of status.

		// Increment our counter for processed form entries.
		constant_contact()->process_form->increment_processed_form_count();

		// Skip sending e-mail if we're connected, the site owner has opted out of notification emails, and the user has opted in
		if ( constant_contact()->api->is_connected() && 'on' === ctct_get_settings_option( '_ctct_disable_email_notifications' ) ) {
			if ( $add_to_opt_in ) {
				return true;
			}

			if ( empty( $_POST['ctct_must_opt_in'] ) || ! empty( $_POST['ctct-opt-in'] ) ) {
				return true;
			}
		}

		// This would allow for setting each sections error and also allow for returning early again for cases
		// like having a list, but not needing to opt in.
		$has_list = get_post_meta( $submission_details['form_id'], '_ctct_list', true );

		// Checks if we have a list
		if (
			( ! constant_contact()->api->is_connected() || empty( $has_list ) ) &&
			( 'on' === ctct_get_settings_option( '_ctct_disable_email_notifications' ) )
		) { // If we're not connected or have no list set AND we've disabled. Override.

			$submission_details['list-available'] = 'no';
			$was_forced                           = true;
		}

		if (
			! empty( $_POST['ctct_must_opt_in'] ) &&
			empty( $opt_in_details ) &&
			( 'on' === ctct_get_settings_option( '_ctct_disable_email_notifications' ) )
		) {
			$submission_details['opted-in'] = 'no';
			$was_forced                     = true;
		}

		// Send the mail.
		return $this->mail( $this->get_email(), $email_values, $submission_details, $was_forced );
	}

	/**
	 * Opts in a user, if requested.
	 *
	 * @since 1.0.0
	 *
	 * @param array $values Submitted values.
	 * @return object|null Response from API.
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
	 * @since 1.3.6 Added $was_forced.
	 *
	 * @param string $destination_email  Intended mail address.
	 * @param string $content            Data from clean values.
	 * @param array  $submission_details Details for submission to process.
	 * @param bool   $was_forced         Whether or not we are force sending. Default false.
	 * @return bool Whether or not sent.
	 */
	public function mail( $destination_email, $content, $submission_details, $was_forced = false ) {

		// Define a mail key for the cache.
		static $last_sent = false;

		$screen = '';
		// Sanity check for get_current_screen, as we may run too early.
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
		}

		$mail_key = md5( "{$destination_email}:{$content}:" . ( isset( $screen->id ) ? $screen->id : '' ) );
		$partial_email = $this->get_email_part( $destination_email );

		// If we already have sent this e-mail, don't send it again.
		if ( $last_sent === $mail_key ) {
			$this->maybe_log_mail_status(
				vsprintf(
					/* translators: this is only used when some debugging is enabled */
					__( 'Duplicate send mail for: %1$s and: %2$s', 'constant-contact-forms' ),
					array(
						$partial_email,
						$mail_key,
					)
				),
				$partial_email,
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

		$content_notice_note = $this->maybe_append_forced_email_notice_note( $was_forced );
		$content_notice_reasons = $this->maybe_append_forced_email_notice_reasons( $was_forced, $submission_details );

		$content_before = esc_html__( 'Your Constant Contact Forms plugin has captured new information.', 'constant-contact-forms' );

		$content_before = $content_notice_note . $content_before . $content_notice_reasons;

		$content_title = '<p><strong>' . esc_html__( 'Form title: ', 'constant-contact-forms' ) . '</strong>' . get_the_title( $submission_details['form_id'] ) . '<br/>';
		$content_title .= '<strong>' . esc_html__( 'Form information: ', 'constant-contact-forms' ) . '</strong></p>';

		$content = $content_title . $content;


		$content_after = sprintf(
			esc_html__( "Email marketing is a great way to stay connected and engage with visitors after they've left your site. Visit %shttps://www.constantcontact.com/index?pn=miwordpress%s to sign up for a Free Trial.", 'constant-contact-forms' ),
				'<a href="https://www.constantcontact.com/index?pn=miwordpress">',
				'</a>'
			);

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
			 * @param string $value Constant Contact Form ID.
			 */
			apply_filters( 'constant_contact_email_subject', __( 'Constant Contact Forms Notification', 'constant-contact-forms' ), $submission_details['form_id'] ),
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
	 * @return void
	 */
	public function maybe_log_mail_status( $status, $dest_email, $content ) {

		constant_contact_maybe_log_it(
			'Mail',
			'mail attempted for ' . $dest_email . ': ' . $status,
			$content
		);

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

	/**
	 * Potentially add initial note for why we are emailing the site owner.
	 *
	 * @since 1.3.6
	 *
	 * @param  bool   $was_forced Whether or not we have to force send an email.
	 * @return string $value      Message to explain why an email was received.
	 */
	public function maybe_append_forced_email_notice_note( $was_forced = false ) {

		if ( ! $was_forced ) {
			return '';
		}

		return sprintf(
			'<p>' . esc_html__( '%sNote:%s You have disabled admin email notifications under the plugin settings, but are receiving this email because of the following reason.', 'constant-contact-forms' ) . '</p>',
			'<strong>*',
			'</strong>'
		);

	}

	/**
	 * Potentially add email content regarding reason we're emailing the site owner.
	 *
	 * @since 1.3.6
	 *
	 * @param bool  $was_forced         Whether or not we have to force send an email.
	 * @param array $submission_details Array of submission details that we tack reasons to send email in.
	 * @return string
	 */
	public function maybe_append_forced_email_notice_reasons( $was_forced = false, $submission_details = array() ) {

		if ( ! $was_forced ) {
			return '';
		}

		$content_notice = '';
		$template = '<p><strong>' . esc_html__( 'Submitted to Constant Contact:', 'constant-contact-forms' ) . '</strong> %s</p>';

		if ( isset( $submission_details['list-available'] ) || isset( $submission_details['opted-in'] ) ) {
			if ( isset( $submission_details['list-available'] ) && 'no' === $submission_details['list-available'] ) {
				$content_notice .= sprintf(
					$template,
					esc_html__( 'NO (Constant Contact list not selected for this form)', 'constant-contact-forms' )
				);
			}
			if ( isset( $submission_details['opted-in'] ) && 'no' === $submission_details['opted-in'] ) {
				$content_notice .= sprintf(
					$template,
					esc_html__( 'NO (User did not select the Email Opt-in checkbox)', 'constant-contact-forms' ) . '<br/>' . esc_html__( "You can disable this under Form options. Email Opt-in isn't required to add subscribers into your account", 'constant-contact-forms' )

				);
			}
		}

		return $content_notice;
	}

	/**
	 * Parse out just the first part of an email address.
	 *
	 * This method is meant to protect privacy with potential logging of email addresses.
	 * Instead of logging ALL of a given email address, we will just log everything before the `@`
	 *
	 * @since 1.3.7
	 *
	 * @param string $email Email address to parse.
	 * @return mixed Part of a provided email.
	 */
	public function get_email_part( $email ) {
		if ( ! is_email( $email ) ) {
			return $email;
		}

		$new_email = explode( '@', sanitize_email( $email ) );
		if ( ! empty( $new_email ) ) {
			return $new_email[0];
		}

		return $email;
	}
}

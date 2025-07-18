<?php
/**
 * Constant Contact Mail
 *
 * @package ConstantContact
 * @subpackage Mail
 * @author Constant Contact
 * @since 1.0.2
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Wrapper functions for mailing successful contact forms to the user.
 *
 * @since 1.0.0
 */
class ConstantContact_Mail {

	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	protected object $plugin;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $plugin Parent plugin class.
	 */
	public function __construct( object $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Fire hooks for actions.
	 *
	 * @since 1.0.0
	 */
	protected function hooks() {}

	/**
	 * Process our form values.
	 *
	 * @since 1.0.0
	 *
	 * @param array $values        Submitted form values.
	 * @param bool  $add_to_opt_in Whether or not to add to opt in.
	 * @return bool
	 */
	public function submit_form_values( array $values = [], bool $add_to_opt_in = false ) : bool {

		if ( ! is_array( $values ) ) {
			return false;
		}

		$values         = constant_contact()->get_process_form()->clean_values( $values );
		$opt_in_details = ( isset( $values['ctct-opt-in'] ) ) ? $values['ctct-opt-in'] : [];

		// Preserve form ID for mail() method. Lost in pretty_values() pass.
		$submission_details                    = [];
		$submission_details['form_id']         = $values['ctct-id']['value'];
		$submission_details['submitted_email'] = $this->get_user_email_from_submission( $values );

		$lists  = $values['ctct-lists'] ?? [];
		$values = constant_contact()->get_process_form()->pretty_values( $values );

		$email_values = $this->format_values_for_email( $values, $submission_details['form_id'] );
		$was_forced   = false; // Set a value regardless of status.

		constant_contact()->get_process_form()->increment_processed_form_count();

		// Check if no list is selected, if so skip to force email logic regardless of connection.
		if ( ! empty( $lists['value'][0] ) ) {

			// Skip sending e-mail if we're connected, the site owner has opted out of notification emails, and the user has opted in.
			if ( constant_contact()->get_api()->is_connected() && constant_contact_emails_disabled( $submission_details['form_id'] ) ) {
				if ( $add_to_opt_in ) {
					return true;
				}

				// phpcs:disable WordPress.Security.NonceVerification -- OK checking of $_POST values.
				if ( empty( $_POST['ctct_must_opt_in'] ) || ! empty( $_POST['ctct-opt-in'] ) ) {
					return true;
				}
				// phpcs:enable WordPress.Security.NonceVerification
			}
		}

		$emails_disabled = constant_contact_emails_disabled( $submission_details['form_id'] );

		if ( ( ! constant_contact()->get_api()->is_connected() || empty( $lists ) ) && $emails_disabled ) {

			// If we're not connected or have no list set AND we've disabled. Override.
			$submission_details['list-available'] = 'no';
			$was_forced                           = true;
		}

		// phpcs:disable WordPress.Security.NonceVerification -- OK checking of $_POST values.
		if ( ! empty( $_POST['ctct_must_opt_in'] ) && empty( $opt_in_details ) && $emails_disabled ) {
			$submission_details['opted-in'] = 'no';
			$was_forced                     = true;
		}
		// phpcs:enable WordPress.Security.NonceVerification

		return $this->mail( $this->get_email( $submission_details['form_id'] ), $email_values, $submission_details, $was_forced );
	}

	/**
	 * Opts in a user, if requested.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $values Submitted values.
	 * @return array|void    Response from API.
	 */
	public function opt_in_user( array $values ) {

		foreach ( $values as $key => $val ) {
			$key  = sanitize_text_field( $val['key'] ?? '' );
			$orig = sanitize_text_field( $val['orig_key'] ?? '' );
			$val  = sanitize_text_field( $val['value'] ?? '' );

			if ( empty( $key ) || in_array( $key, [ 'ctct-opt-in', 'ctct-id', 'ctct-lists' ], true ) ) {
				continue;
			}

			$args[ $orig ] = [
				'key' => $key,
				'val' => $val,
			];

			if ( 'email' === $key ) {
				$args['email'] = $val;
			}
		}

		if ( ! isset( $values['ctct-opt-in'] ) || empty( $values['ctct-lists'] ) ) {
			return;
		}

		$lists        = $values['ctct-lists'] ?? [];
		$lists        = $lists['value'] ?? [];
		$args['list'] = is_array( $lists ) ? array_map( 'sanitize_text_field', $lists ) : sanitize_text_field( $lists );

		return constant_contact()->get_api()->add_contact( $args, $values['ctct-id']['value'] );
	}

	/**
	 * Formats values for email.
	 *
	 * @since 1.0.0
	 * @since 1.4.0 Added form_id parameter.
	 *
	 * @param array  $pretty_vals Values to format.
	 * @param string $form_id Form ID being submitted to.
	 * @return string HTML content for email.
	 */
	public function format_values_for_email( array $pretty_vals, string $form_id ) : string {

		$return = '';

		$original_field_data = $this->plugin->get_process_form()->get_original_fields( $form_id );
		foreach ( $pretty_vals as $val ) {

			$label = $val['orig_key'] ?? false;

			$custom_field_name = '';
			if ( false !== strpos( $label, 'custom___' ) ) {
				$custom_field       = ( $original_field_data[ $val['orig_key'] ] );
				$custom_field_name .= $custom_field['name'];
			}

			if ( $label && empty( $custom_field_name ) ) {
				$label = explode( '___', $label );
				$label = ucwords( str_replace( '_', ' ', $label[0] ) );
			} else {
				$label = $custom_field_name;
			}
			$value = $val['post'] ?? '&nbsp;';

			$return .= '<p>' . sanitize_text_field( $label ) . ': ' . sanitize_text_field( $value ) . '</p>';
		}

		return $return;
	}

	/**
	 * Get the email address to send to.
	 *
	 * @since 1.0.0
	 * @since 1.4.0 Added form ID parameter.
	 *
	 * @param string $form_id Current form ID being submitted to.
	 *
	 * @return string Email address to send to.
	 */
	public function get_email( string $form_id ) : string {

		$email = get_option( 'admin_email' );

		/**
		 * Filters the email to send Constant Contact Forms admin emails to.
		 *
		 * @since 1.3.0
		 * @since 1.4.0 Added form ID parameter.
		 *
		 * @param string $email Email address to send to. Default admin_email option.
		 * @param string $form_id Current form ID being submitted to.
		 */
		return apply_filters( 'constant_contact_destination_email', $email, $form_id );
	}

	/**
	 * Sends our mail out.
	 *
	 * @since 1.0.0
	 * @since 1.3.6 Added $was_forced.
	 *
	 * @throws Exception Throws Exception if encountered while attempting to send email.
	 *
	 * @param mixed $destination_email  Intended mail address.
	 * @param string $content            Data from clean values.
	 * @param array  $submission_details Details for submission to process.
	 * @param bool   $was_forced         Whether or not we are force sending. Default false.
	 * @return bool Whether or not sent.
	 */
	public function mail( $destination_email, string $content, array $submission_details, bool $was_forced = false ) : bool {

		static $last_sent = false;
		$screen           = '';

		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
		}

		if ( is_array( $destination_email ) ) {
			$temp_destination_email = implode( ',', $destination_email );
		} else {
			$temp_destination_email = $destination_email;
		}
		$mail_key = md5( "$temp_destination_email:$content:" . ( $screen->id ?? '' ) );

		if ( is_array( $destination_email ) ) {
			$partial_email = array_map( [ $this, 'get_email_part' ], $destination_email );
			$partial_email = implode( ',', $partial_email );
		} else {
			if ( false !== strpos( $destination_email, ',' ) ) {
				// Use trim to handle cases of ", ".
				$partials      = array_map( 'trim', explode( ',', $destination_email ) );
				$partial_email = array_map( [ $this, 'get_email_part' ], $partials );
				$partial_email = implode( ',', $partial_email );
			} else {
				$partial_email = $this->get_email_part( $destination_email );
			}
		}

		if ( $last_sent === $mail_key ) {
			$this->maybe_log_mail_status(
				vsprintf(
					/* translators: this is only used when some debugging is enabled */
					esc_html__( 'Duplicate send mail for: %1$s and: %2$s', 'constant-contact-forms' ),
					[
						$partial_email,
						$mail_key,
					]
				),
				$partial_email,
				$mail_key
			);
			return true;
		}

		if ( is_array( $destination_email ) ) {
			$destination_email = array_map( 'sanitize_email', $destination_email );
			$destination_email = implode( ',', $destination_email );
		} else {
			if ( false !== strpos( $destination_email, ',' ) ) {
				// Use trim to handle cases of ", ".
				$partials          = array_map( 'trim', explode( ',', $destination_email ) );
				$partials          = array_map( 'sanitize_email', $partials );
				$destination_email = implode( ',', $partials );
			} else {
				$destination_email = sanitize_email( $destination_email );
			}
		}

		add_filter( 'wp_mail_content_type', [ $this, 'set_email_type' ] );

		$content_notice_note    = $this->maybe_append_forced_email_notice_note( $was_forced );
		$content_notice_reasons = $this->maybe_append_forced_email_notice_reasons( $was_forced, $submission_details );

		$content_before = esc_html__( 'Your Constant Contact Forms plugin has captured new information.', 'constant-contact-forms' );

		$content_before = $content_notice_note . $content_before . $content_notice_reasons;

		$content_title = '<p><strong>' . esc_html__( 'Form title: ', 'constant-contact-forms' ) . '</strong>' . get_the_title( $submission_details['form_id'] ) . '<br/>';

		$list_ids = [];
		if ( ! empty( $_POST ) && is_array( $_POST ) ) { //phpcs:ignore
			foreach( $_POST as $key => $value ) { //phpcs:ignore
				if ( false !== strpos( $key, 'lists' ) ) {
					$list_ids = array_map( 'sanitize_text_field', array_values( $value ) );
					break;
				}
			}
		}

		foreach ( $list_ids as $list_id ) {
			$list_info = constant_contact()->get_api()->cc()->get_list( $list_id );
			if ( ! empty( $list_info ) && isset( $list_info['name'] ) ) {
				$content_title .= '<strong>' . esc_html__( 'List name: ', 'constant-contact-forms' ) . '</strong>' . esc_html( $list_info['name'] ) . '<br/>';
			}
		}

		$content_title .= '<strong>' . esc_html__( 'Form information: ', 'constant-contact-forms' ) . '</strong></p>';

		$content = $content_title . $content;

		$content_after = '';
		if ( ! constant_contact()->get_api()->is_connected() ) {
			$content_after = sprintf(
				/* Translators: placeholders provide Constant Contact link information. */
				esc_html__( "Email marketing is a great way to stay connected and engage with visitors after they've left your site. Visit %1\$shttps://www.constantcontact.com/index?pn=miwordpress%2\$s to sign up for a Free Trial.", 'constant-contact-forms' ),
				'<a href="https://www.constantcontact.com/?pn=miwordpress">',
				'</a>'
			);
		}

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

		remove_filter( 'wp_mail_content_type', [ $this, 'set_email_type' ] );

		if ( $mail_status ) {
			$last_sent = $mail_key;
		}

		return $mail_status;
	}

	/**
	 * Helper method to return 'text/html' string for actions.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function set_email_type() : string {
		return 'text/html';
	}

	/**
	 * If our mail debugging is set, then log mail statuses to the error log.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception Exception.
	 *
	 * @param string $status     Status from wp_mail.
	 * @param string $dest_email Destination email.
	 * @param string $content    Content of email.
	 */
	public function maybe_log_mail_status( string $status, string $dest_email, string $content ) {

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
	public function get_user_email_from_submission( array $values = [] ) {
		foreach ( $values as $key => $value ) {
			if ( false === strpos( $key, 'email___' ) ) {
				continue;
			}
			return $value['value'];
		}
		return '';
	}

	/**
	 * Potentially add initial note for why we are emailing the site owner.
	 *
	 * @since 1.3.6
	 *
	 * @param  bool $was_forced Whether or not we have to force send an email.
	 * @return string $value      Message to explain why an email was received.
	 */
	public function maybe_append_forced_email_notice_note( bool $was_forced = false ) : string {

		if ( ! $was_forced ) {
			return '';
		}

		return sprintf(
			/* Translators: placeholders simply meant for `<strong>` html tags */
			'<p>' . esc_html__( '%1$sNote:%2$s You have disabled admin email notifications under the plugin settings, but are receiving this email because of the following reason.', 'constant-contact-forms' ) . '</p>',
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
	public function maybe_append_forced_email_notice_reasons( bool $was_forced = false, array $submission_details = [] ) : string {

		if ( ! $was_forced ) {
			return '';
		}

		$content_notice = '';
		$template       = '<p><strong>' . esc_html__( 'Submitted to Constant Contact:', 'constant-contact-forms' ) . '</strong> %s</p>';

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
		} elseif ( ! empty( $submission_details['custom-reason'] ) ) {
			$content_notice .= sprintf(
				$template,
				esc_html( $submission_details['custom-reason'] )
			);
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
	 * @return string Part of a provided email.
	 */
	public function get_email_part( string $email ) : string {
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

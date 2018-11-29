<?php
/**
 * Helper Functions for end-users to leverage when building themes or plugins.
 *
 * @package ConstantContact
 * @author Constant Contact
 * @since 1.0.0
 */

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Checks to see if a user is connected to Constant Contact or not.
 *
 * @since 1.0.0
 *
 * @return boolean Whether or not they are connected.
 */
function constant_contact_is_connected() {
	return ( constant_contact()->api->is_connected() );
}

/**
 * Checks to see if a user is not connected to Constant Contact or not.
 *
 * @since 1.0.0
 *
 * @return boolean Whether or not they are NOT connected.
 */
function constant_contact_is_not_connected() {
	return ! ( constant_contact()->api->is_connected() );
}

/**
 * Get a form's markup without using a shortcode.
 *
 * @since 1.0.0
 *
 * @param int $form_id Form post ID to grab.
 * @return string HTML markup
 */
function constant_contact_get_form( $form_id ) {
	return constant_contact()->display_shortcode->get_form( $form_id );
}

/**
 * Get a form and display it without using a shortcode.
 *
 * @since 1.0.0
 *
 * @param int $form_id Form post ID to grab.
 */
function constant_contact_display_form( $form_id ) {
	constant_contact()->display_shortcode->display_form( $form_id );
}

/**
 * Get an array of forms.
 *
 * @since 1.0.0
 *
 * @return array WP_Query results of forms.
 */
function constant_contact_get_forms() {
	return constant_contact()->cpts->get_forms( false, true );
}

/**
 * Render a shortcode for display, not for parsing.
 *
 * @since 1.2.0
 *
 * @param string $form_id Form ID to provide in the output.
 * @return string Non-parsed shortcode.
 */
function constant_contact_display_shortcode( $form_id ) {
	return sprintf( '[ctct form="%s"]', $form_id );
}

/**
 * Maybe display the opt-in notification on the dashboard.
 *
 * @since 1.2.0
 *
 * @return bool
 */
function constant_contact_maybe_display_optin_notification() {

	if ( ! function_exists( 'get_current_screen' ) ) {
		return false;
	}

	$current_screen = get_current_screen();
	if ( ! is_object( $current_screen ) || 'dashboard' !== $current_screen->base ) {
		return false;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}

	$privacy = get_option( 'ctct_privacy_policy_status', '' );

	if ( '' !== $privacy ) {
		return false;
	}

	return true;
}

/**
 * Maybe display the review request notification in the Constant Contact areas.
 *
 * @since 1.2.2
 *
 * @return bool
 */
function constant_contact_maybe_display_review_notification() {

	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}

	if ( ! constant_contact()->is_constant_contact() ) {
		return false;
	}

	if ( 'true' === get_option( 'ctct-reviewed', 'false' ) ) {
		return false;
	}

	// @todo date_diff() comparisons.
	//
	$dismissed = get_option( 'ctct-review-dismissed', array() );
	if ( isset( $dismissed['count'] ) && '1' === $dismissed['count'] ) {
		$fourteen_days = strtotime( '-14 days' );
		if ( isset( $dismissed['time'] ) && $dismissed['time'] < $fourteen_days
		) {
			return true;
		} else {
			return false;
		}
	}

	if ( isset( $dismissed['count'] ) && '2' === $dismissed['count'] ) {
		$thirty_days = strtotime( '-30 days' );
		if ( isset( $dismissed['time'] ) && $dismissed['time'] < $thirty_days
		) {
			return true;
		} else {
			return false;
		}
	}

	if ( isset( $dismissed['count'] ) && '3' === $dismissed['count'] ) {
		return false;
	}

	if ( absint( get_option( 'ctct-processed-forms' ) ) >= 10 ) {
		return true;
	}

	return true;
}

/**
 * Whether or not to show our reCAPTCHA info notice. Should only show
 *
 * @since 1.2.4
 *
 * @return bool
 */
function constant_contact_maybe_display_reCAPTCHA_notification() {
	return true;
}

/**
 * Handle the optin checkbox for the admin notice.
 *
 * @since 1.2.0
 */
function constant_contact_optin_ajax_handler() {

	$response = $_REQUEST;

	if ( ! isset( $response['optin'] ) || 'on' !== $response['optin'] ) {
		wp_send_json_success( array( 'opted-in' => 'off' ) );
	}

	$options                        = get_option( constant_contact()->settings->key );
	$options['_ctct_data_tracking'] = $response['optin'];
	update_option( constant_contact()->settings->key, $options );

	wp_send_json_success( array( 'opted-in' => 'on' ) );
	exit();
}
add_action( 'wp_ajax_constant_contact_optin_ajax_handler', 'constant_contact_optin_ajax_handler' );

/**
 * Handle the privacy policy agreement or disagreement selection.
 *
 * @since 1.2.0
 */
function constant_contact_privacy_ajax_handler() {

	$response = $_REQUEST;
	$agreed   = sanitize_text_field( $response['privacy_agree'] );
	update_option( 'ctct_privacy_policy_status', $agreed );

	wp_send_json_success( array( 'updated' => 'true' ) );
	exit();
}
add_action( 'wp_ajax_constant_contact_privacy_ajax_handler', 'constant_contact_privacy_ajax_handler' );

/**
 * Handle the ajax for the review admin notice.
 *
 * @since 1.2.2
 */
function constant_contact_review_ajax_handler() {

	if ( isset( $_REQUEST['ctct-review-action'] ) ) {
		$action = strtolower( sanitize_text_field( $_REQUEST['ctct-review-action'] ) );

		switch ( $action ) {
			case 'dismissed':
				$dismissed         = get_option( 'ctct-review-dismissed', array() );
				$dismissed['time'] = current_time( 'timestamp' );
				if ( empty( $dismissed['count'] ) ) {
					$dismissed['count'] = '1';
				} elseif ( isset( $dismissed['count'] ) && '2' === $dismissed['count'] ) {
					$dismissed['count'] = '3';
				} else {
					$dismissed['count'] = '2';
				}
				update_option( 'ctct-review-dismissed', $dismissed );

				break;

			case 'reviewed':
				update_option( 'ctct-reviewed', 'true' );
				break;

			default:
				break;
		}
	}

	wp_send_json_success( array( 'review-action' => 'processed' ) );
	exit();
}
add_action( 'wp_ajax_constant_contact_review_ajax_handler', 'constant_contact_review_ajax_handler' );

/**
 * Process potential custom Constant Contact Forms action urls.
 *
 * @since 1.2.3
 *
 * @return bool|array
 */
function ctct_custom_form_action_processing() {
	if ( empty( $_POST ) || ! isset( $_POST['ctct-id'] ) ) {
		return false;
	}
	// Only run this if we have a custom action being filtered in.
	if ( ! constant_contact_has_redirect_uri( absint( $_POST['ctct-id'] ) ) ) {
		return false;
	}

	return constant_contact()->process_form->process_form();
}
add_action( 'wp_head', 'ctct_custom_form_action_processing' );

/**
 * Determine if we have any Constant Contact Forms published.
 *
 * @since 1.2.5
 *
 * @return bool
 */
function ctct_has_forms() {
	$args  = array(
		'post_type'      => 'ctct_forms',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
	);
	$forms = new WP_Query( $args );
	return ( $forms->have_posts() );
}

/**
 * Whether or not there is a redirect URI meta value set for a form.
 *
 * @since 1.3.0
 *
 * @param int $form_id Form ID to check.
 * @return bool
 */
function constant_contact_has_redirect_uri( $form_id = 0 ) {
	$maybe_redirect_uri = get_post_meta( $form_id, '_ctct_redirect_uri', true );

	return empty( $maybe_redirect_uri ) ? false : true;
}

/**
 * Compare timestamps for rendered time vs current time.
 *
 * @since 1.3.2
 *
 * @param bool  $maybe_spam Whether or not an entry has been determined as spam.
 * @param array $data       Submitted form data.
 * @return bool
 */
function constant_contact_check_timestamps( $maybe_spam, $data ) {
	$current    = current_time( 'timestamp' );
	$difference = $current - $data['ctct_time'];
	if ( $difference <= 5 ) {
		return true;
	}
	return $maybe_spam;
}
add_filter( 'constant_contact_maybe_spam', 'constant_contact_check_timestamps', 10, 2 );

/**
 * Clean and correctly protocol an given URL.
 *
 * @since 1.3.6
 *
 * @param string $url URL to tidy.
 * @return string
 */
function constant_contact_clean_url( $url = '' ) {
	// Reject and return untouched if not provided a string.
	if ( ! is_string( $url ) ) {
		return $url;
	}

	$clean_url = esc_url( $url );
	if ( is_ssl() && 'http' === parse_url( $clean_url, PHP_URL_SCHEME ) ) {
		$clean_url = str_replace( 'http', 'https', $clean_url );
	}
	return $clean_url;
}

/**
 * Checks if we have our new debugging option enabled.
 *
 * @since 1.3.7
 *
 * @return bool
 */
function constant_contact_debugging_enabled() {
	$debugging_enabled = ctct_get_settings_option( '_ctct_logging', '' );

	return (
		( defined( 'CONSTANT_CONTACT_DEBUG_MAIL' ) && CONSTANT_CONTACT_DEBUG_MAIL ) ||
		'on' === $debugging_enabled
	);
}

/**
 * Potentially add an item to our custom error log.
 *
 * @since 1.3.7
 *
 * @throws Exception Exception.
 *
 * @param string       $log_name   Component that the log item is for.
 * @param string       $error      The error to log.
 * @param mixed|string $extra_data Any extra data to add to the log.
 * @return null
 */
function constant_contact_maybe_log_it( $log_name, $error, $extra_data = '' ) {
	if ( ! constant_contact_debugging_enabled() ) {
		return;
	}

	if ( ! is_writable( constant_contact()->logger_location ) ) {
		return;
	}

	$logger = new Logger( $log_name );
	$logger->pushHandler( new StreamHandler( constant_contact()->logger_location ) );
	$extra = [];

	if ( $extra_data ) {
		$extra = [ 'Extra information', [ $extra_data ] ];
	}
	// Log status of error.
	$logger->addInfo( $error, $extra );
}

/**
 * Check spam through Akismet.
 * It will build Akismet query string and call Akismet API.
 * Akismet response return 'true' for spam submission.
 *
 * Akismet integration props to GiveWP. We appreciate the initial work.
 *
 * @since 1.4.0
 *
 * @param bool  $is_spam Current status of the submission.
 * @param array $data    Array of submission data.
 * @return bool
 */
function constant_contact_akismet( $is_spam, $data ) {

	// Bail out, If spam.
	if ( $is_spam ) {
		return $is_spam;
	}

	$email = false;
	$fname = $lname = $name = '';
	foreach ( $data as $key => $value ) {
		if ( 'email' === substr( $key, 0, 5 ) ) {
			$email = $value;
		}
		if ( 'first_name' === substr( $key, 0, 10 ) ) {
			$fname = $value;
		}
		if ( 'last_name' === substr( $key, 0, 9 ) ) {
			$lname = $value;
		}
	}

	if ( $fname ) {
		$name = $fname;
	}
	if ( $lname ) {
		$name .= ' ' . $lname;
	}

	// Bail out, if Akismet key not exist.
	if ( ! constant_contact_check_akismet_key() ) {
		return $is_spam;
	}

	// Build args array.
	$args = array();

	$args['comment_author']       = $name;
	$args['comment_author_email'] = $email;
	$args['blog']                 = get_option( 'home' );
	$args['blog_lang']            = get_locale();
	$args['blog_charset']         = get_option( 'blog_charset' );
	$args['user_ip']              = $_SERVER['REMOTE_ADDR'];
	$args['user_agent']           = $_SERVER['HTTP_USER_AGENT'];
	$args['referrer']             = $_SERVER['HTTP_REFERER'];
	$args['comment_type']         = 'contact-form';

	$ignore = array( 'HTTP_COOKIE', 'HTTP_COOKIE2', 'PHP_AUTH_PW' );

	foreach ( $_SERVER as $key => $value ) {
		if ( ! in_array( $key, (array) $ignore ) ) {
			$args[ "{$key}" ] = $value;
		}
	}

	// It will return Akismet spam detect API response.
	$is_spam = constant_contact_akismet_spam_check( $args );

	return $is_spam;
}
add_filter( 'constant_contact_maybe_spam', 'constant_contact_akismet', 10, 2 );

/**
 * Check Akismet API Key.
 *
 * @since 1.4.0
 *
 * @return bool
 */
function constant_contact_check_akismet_key() {
	if ( is_callable( array( 'Akismet', 'get_api_key' ) ) ) { // Akismet v3.0.
		return (bool) Akismet::get_api_key();
	}

	if ( function_exists( 'akismet_get_key' ) ) {
		return (bool) akismet_get_key();
	}

	return false;
}

/**
 * Detect spam through Akismet Comment API.
 *
 * @since 1.4.0
 *
 * @param array $args Array of arguments.
 * @return bool|mixed
 */
function constant_contact_akismet_spam_check( $args ) {
	global $akismet_api_host, $akismet_api_port;

	$spam         = false;
	$query_string = http_build_query( $args );

	if ( is_callable( array( 'Akismet', 'http_post' ) ) ) { // Akismet v3.0.
		$response = Akismet::http_post( $query_string, 'comment-check' );
	} else {
		$response = akismet_http_post( $query_string, $akismet_api_host,
			'/1.1/comment-check', $akismet_api_port );
	}

	// It's spam if response status is true.
	if ( 'true' === $response[1] ) {
		$spam = true;
	}

	return $spam;
}

/**
 * Check whether or not emails should be disabled.
 *
 * @since 1.4.0
 *
 * @param int $form_id Current form ID being submitted to.
 *
 * @return mixed
 */
function constant_contact_emails_disabled( $form_id = 0 ) {

	// Assume we can.
	$disabled = false;

	// Check for a setting for the form itself.
	$form_disabled = get_post_meta( $form_id, '_ctct_disable_emails_for_form', true );
	if ( 'on' === $form_disabled ) {
		$disabled = true;
	}

	// Check for our global setting.
	$global_form_disabled = ctct_get_settings_option( '_ctct_disable_email_notifications', '' );
	if ( 'on' === $global_form_disabled ) {
		$disabled = true;
	}

	/**
	 * Filters whether or not emails should be disabled.
	 *
	 * @since 1.4.0
	 *
	 * @param bool $disabled Whether or not emails are disabled.
	 * @param int  $form_id  Form ID being submitted to.
	 */
	return apply_filters( 'constant_contact_emails_disabled', $disabled, $form_id );
}

/**
 * Get a list of font sizes to use in a dropdown menu for user customization.
 *
 * @since 1.4.0
 *
 * @return array The font sizes to use in a dropdown.
 */
function constant_contact_get_font_dropdown_sizes() {
	return array(
		'12px' => '12 pixels',
		'13px' => '13 pixels',
		'14px' => '14 pixels',
		'15px' => '15 pixels',
		'16px' => '16 pixels',
		'17px' => '17 pixels',
		'18px' => '18 pixels',
		'19px' => '19 pixels',
		'20px' => '20 pixels',
	);
}

/**
 * Retrieve a CSS customization setting for a given form.
 *
 * Provide the post meta key or global setting key to retrieve.
 *
 * @since 1.4.0
 *
 * @param int    $form_id           Form ID to fetch data for.
 * @param string $customization_key Key to fetch value for.
 * @return string.
 */
function constant_contact_get_css_customization( $form_id, $customization_key = '' ) {

	$form_id  = absint( $form_id );
	$form_css = get_post_meta( $form_id );

	if ( is_array( $form_css ) && array_key_exists( $customization_key, $form_css ) ) {
		if ( ! empty( $form_css[ $customization_key ][0] ) ) {
			return $form_css[ $customization_key ][0];
		}
	}

	$global_setting = ctct_get_settings_option( $customization_key );

	return ( ! empty( $global_setting ) ) ? $global_setting : '';
}

function constant_contact_privacy_policy_content() {
	$policy_output = wp_remote_get( 'https://www.endurance.com/privacy' );
	if ( ! is_wp_error( $policy_output ) && 200 === wp_remote_retrieve_response_code( $policy_output ) ) {
		$content = wp_remote_retrieve_body( $policy_output );
		preg_match( '/<body[^>]*>(.*?)<\/body>/si', $content, $match );
		$output = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $match[1] );
		preg_match_all( '@<section class="container privacy-center-container">.*?</section>@si', $output, $final );

		return $final[0][0] . $final[0][2];
	}

	return '';
}

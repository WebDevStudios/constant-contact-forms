<?php
/**
 * Helper Functions for end-users to leverage when building themes or plugins.
 *
 * @package ConstantContact
 * @author Constant Contact
 * @since 1.0.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
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
	return constant_contact()->api->is_connected();
}

/**
 * Checks to see if a user is not connected to Constant Contact or not.
 *
 * @since 1.0.0
 *
 * @return boolean Whether or not they are NOT connected.
 */
function constant_contact_is_not_connected() {
	return ! constant_contact()->api->is_connected();
}

/**
 * Get a form's markup without using a shortcode.
 *
 * @since 1.0.0
 *
 * @param int  $form_id Form post ID to grab.
 * @param bool $show_title If true, show the title.
 * @return string HTML markup
 */
function constant_contact_get_form( $form_id, $show_title = false ) {
	return constant_contact()->display_shortcode->get_form( $form_id, $show_title );
}

/**
 * Get a form and display it without using a shortcode.
 *
 * @since 1.0.0
 *
 * @param int  $form_id Form post ID to grab.
 * @param bool $show_title If true, show the title.
 */
function constant_contact_display_form( $form_id, $show_title = false ) {
	constant_contact()->display_shortcode->display_form( $form_id, $show_title );
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

	$privacy       = get_option( 'ctct_privacy_policy_status', '' );
	$ctct_settings = get_option( 'ctct_options_settings', [] );

	if ( isset( $ctct_settings['_ctct_data_tracking'] ) && 'on' === $ctct_settings['_ctct_data_tracking'] ) {
		return false;
	}

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

	if ( 'true' === get_option( ConstantContact_Notifications::$reviewed_option, 'false' ) ) {
		return false;
	}

	$activated_time = get_option( Constant_Contact::$activated_date_option );

	if ( ! $activated_time || time() < strtotime( '+14 days', $activated_time ) ) {
		return false;
	}

	$dismissed = get_option( ConstantContact_Notifications::$review_dismissed_option, [] );

	if ( isset( $dismissed['count'] ) && '1' === $dismissed['count'] ) {
		$fourteen_days = strtotime( '-14 days' );

		if ( isset( $dismissed['time'] ) && $dismissed['time'] < $fourteen_days ) {
			return true;
		}
		return false;
	}

	if ( isset( $dismissed['count'] ) && '2' === $dismissed['count'] ) {
		$thirty_days = strtotime( '-30 days' );
		if ( isset( $dismissed['time'] ) && $dismissed['time'] < $thirty_days
		) {
			return true;
		}
		return false;
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
 * Handles the notice of if we have exceptions existing.
 *
 * @since 1.6.0
 *
 * @return bool
 */
function constant_contact_maybe_display_exceptions_notice() {
	$maybe_has_error = get_option( 'ctct_exceptions_exist' );

	return ( 'true' === $maybe_has_error );
}

/**
 * Handle the optin checkbox for the admin notice.
 *
 * @since 1.2.0
 */
function constant_contact_optin_ajax_handler() {

	$optin = filter_var( $_REQUEST['optin'], FILTER_SANITIZE_STRING );

	if ( 'on' !== $optin ) {
		wp_send_json_success( [ 'opted-in' => 'off' ] );
	}

	$options                        = get_option( constant_contact()->settings->key );
	$options['_ctct_data_tracking'] = $optin;
	update_option( constant_contact()->settings->key, $options );

	wp_send_json_success( [ 'opted-in' => 'on' ] );
	exit();
}
add_action( 'wp_ajax_constant_contact_optin_ajax_handler', 'constant_contact_optin_ajax_handler' );

/**
 * Handle the privacy policy agreement or disagreement selection.
 *
 * @since 1.2.0
 */
function constant_contact_privacy_ajax_handler() {

	$agreed = filter_var( $_REQUEST['privacy_agree'], FILTER_SANITIZE_STRING );
	update_option( 'ctct_privacy_policy_status', $agreed );

	wp_send_json_success( [ 'updated' => 'true' ] );
	exit();
}
add_action( 'wp_ajax_constant_contact_privacy_ajax_handler', 'constant_contact_privacy_ajax_handler' );

/**
 * Handle the ajax for the review admin notice.
 *
 * @since 1.2.2
 */
function constant_contact_review_ajax_handler() {

	//  phpcs:disable WordPress.Security.NonceVerification -- OK accessing of $_REQUEST.
	if ( isset( $_REQUEST['ctct-review-action'] ) ) {
		$action = strtolower( sanitize_text_field( $_REQUEST['ctct-review-action'] ) );
		// phpcs:enable WordPress.Security.NonceVerification

		switch ( $action ) {
			case 'dismissed':
				$dismissed         = get_option( ConstantContact_Notifications::$review_dismissed_option, [] );
				$dismissed['time'] = current_time( 'timestamp' );
				if ( empty( $dismissed['count'] ) ) {
					$dismissed['count'] = '1';
				} elseif ( isset( $dismissed['count'] ) && '2' === $dismissed['count'] ) {
					$dismissed['count'] = '3';
				} else {
					$dismissed['count'] = '2';
				}
				update_option( ConstantContact_Notifications::$review_dismissed_option, $dismissed );

				break;

			case 'reviewed':
				update_option( ConstantContact_Notifications::$reviewed_option, 'true' );
				break;

			default:
				break;
		}
	}

	wp_send_json_success( [ 'review-action' => 'processed' ] );
	exit();
}
add_action( 'wp_ajax_constant_contact_review_ajax_handler', 'constant_contact_review_ajax_handler' );

/**
 * Process potential custom Constant Contact Forms action urls.
 *
 * @since 1.2.3
 *
 * @throws Exception
 *
 * @return bool|array
 */
function ctct_custom_form_action_processing() {

	$ctct_id = filter_input( INPUT_POST, 'ctct-id', FILTER_VALIDATE_INT );

	if ( false === $ctct_id ) {
		return false;
	}

	if ( ! constant_contact_has_redirect_uri( $ctct_id ) ) {
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
	$args  = [
		'post_type'      => 'ctct_forms',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
	];
	$forms = new WP_Query( $args );
	return $forms->have_posts();
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

	return constant_contact_is_valid_url( $maybe_redirect_uri ) ? true : false;
}

/**
 * Check if a string is a valid URL.
 *
 * @since 1.5.0
 *
 * @param string $url The string URL to validate.
 * @return bool Whether or not the provided value is a valid URL.
 */
function constant_contact_is_valid_url( $url = '' ) {
	return ( ! empty( $url ) && filter_var( $url, FILTER_VALIDATE_URL ) );
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
	if ( ! is_string( $url ) ) {
		return $url;
	}

	/* @todo Consideration: non-ssl based external websites. Just cause the user's site may be SSL, doesn't mean redirect url will for sure be. Perhaps add check for home_url as part of consideration. */
	$clean_url = esc_url( $url );
	if ( is_ssl() && 'http' === wp_parse_url( $clean_url, PHP_URL_SCHEME ) ) {
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

	if ( apply_filters( 'constant_contact_force_logging', false ) ) {
		$debugging_enabled = 'on';
	}
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

	if ( ! constant_contact_check_akismet_key() ) {
		return $is_spam;
	}

	$args = [];

	$args['comment_author']       = $name;
	$args['comment_author_email'] = $email;
	$args['blog']                 = get_option( 'home' );
	$args['blog_lang']            = get_locale();
	$args['blog_charset']         = get_option( 'blog_charset' );
	$args['user_ip']              = $_SERVER['REMOTE_ADDR'];
	$args['user_agent']           = $_SERVER['HTTP_USER_AGENT'];
	$args['referrer']             = $_SERVER['HTTP_REFERER'];
	$args['comment_type']         = 'contact-form';

	$ignore = [ 'HTTP_COOKIE', 'HTTP_COOKIE2', 'PHP_AUTH_PW' ];

	foreach ( $_SERVER as $key => $value ) {
		if ( ! in_array( $key, (array) $ignore, true ) ) {
			$args[ "{$key}" ] = $value;
		}
	}

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
	if ( is_callable( [ 'Akismet', 'get_api_key' ] ) ) { // Akismet v3.0.
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

	if ( is_callable( [ 'Akismet', 'http_post' ] ) ) { // Akismet v3.0.
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

	$disabled = false;

	$form_disabled = get_post_meta( $form_id, '_ctct_disable_emails_for_form', true );
	if ( 'on' === $form_disabled ) {
		$disabled = true;
	}

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
	return [
		'12px' => '12 pixels',
		'13px' => '13 pixels',
		'14px' => '14 pixels',
		'15px' => '15 pixels',
		'16px' => '16 pixels',
		'17px' => '17 pixels',
		'18px' => '18 pixels',
		'19px' => '19 pixels',
		'20px' => '20 pixels',
	];
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

	return ! empty( $global_setting ) ? $global_setting : '';
}

/**
 * Fetch and return the content of our Endurance privacy policy.
 *
 * @since 1.4.3
 *
 * @return string
 */
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

/**
 * Set if we have an exception to deal with.
 *
 * @since 1.6.0
 *
 * @param string $status Status value to set
 */
function constant_contact_set_has_exceptions( $status = 'true' ) {
	update_option( 'ctct_exceptions_exist', $status );
}

/**
 * Contactenate passed in log location and line number.
 *
 * Line number may not be 100% accurate, depending on how data is combined.
 * Will be close to actual location in cases of multiple log calls in same function.
 *
 * @since 1.7.0
 *
 * @param string $location Location of the log data being added.
 * @param string $line     Line approximation of where the error originates.
 * @return string
 */
function constant_contact_location_and_line( $location = '', $line = '' ) {
	return sprintf(
		'%s:%s ',
		$location,
		$line
	);
}

/**
 * Get posts containing specified form ID.
 *
 * @since NEXT
 *
 * @param  int $form_id Form ID.
 * @return array        Array of posts containing the form ID.
 */
function constant_contact_get_posts_by_form( $form_id ) {
	global $wpdb;

	$shortcode_like      = $wpdb->esc_like( '[ctct' );
	$post_id_like_single = $wpdb->esc_like( "form='{$form_id}'" );
	$post_id_like_double = $wpdb->esc_like( "form=\"{$form_id}\"" );
	$posts               = $wpdb->get_results( $wpdb->prepare(
		"SELECT ID, post_title, post_type FROM {$wpdb->posts} WHERE (`post_content` LIKE %s OR `post_content` LIKE %s) AND `post_status` = %s ORDER BY post_type ASC",
		"%{$shortcode_like}%{$post_id_like_single}%",
		"%{$shortcode_like}%{$post_id_like_double}%",
		'publish'
	), ARRAY_A );

	array_walk( $posts, function( &$value, $key ) {
		$value = [
			'type'  => 'post',
			'url'   => get_edit_post_link( $value['ID'] ),
			'label' => get_post_type_object( $value['post_type'] )->labels->singular_name,
			'id'    => $value['ID'],
		];
	} );

	return $posts;
}

/**
 * Get links and info on widgets containing specified form ID.
 *
 * @since  NEXT
 *
 * @param  int $form_id Form ID.
 * @return array        Array of widgets containing the form ID.
 */
function constant_contact_get_widgets_by_form( $form_id ) {
	$return = [];

	foreach ( [ 'ctct_form', 'text' ] as $widget_type ) {
		$data    = [
			'form_id' => $form_id,
			'type'    => $widget_type,
		];
		$widgets = array_filter( get_option( "widget_{$widget_type}", [] ), function( $value ) use ( $data ) {
			if ( 'ctct_form' === $data['type'] ) {
				return absint( $value['ctct_form_id'] ) === $data['form_id'];
			} else if ( 'text' === $data['type'] ) {
				if ( ! isset( $value['text'] ) || false === strpos( $value['text'], '[ctct' ) ) {
					return false;
				}
				return ( false !== strpos( $value['text'], "form=\"{$data['form_id']}\"" ) || false !== strpos( $value['text'], "form='{$data['form_id']}'" ) );
			}
			return false;
		} );
		array_walk( $widgets, 'constant_contact_walk_widget_references', $widget_type );
		$return  = array_merge( $return, $widgets );
	}

	return $return;
}

/**
 * Walker callback for widget references of deleted forms.
 *
 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @since  NEXT
 *
 * @param  array  $value Array of current widget settings.
 * @param  string $key   Current widget key.
 * @param  string $type  Type of widget.
 */
function constant_contact_walk_widget_references( array &$value, $key, $type ) {
	global $wp_registered_sidebars, $wp_registered_widgets;

	$widget_id  = "{$type}-{$key}";
	$sidebars   = array_keys( array_filter( get_option( 'sidebars_widgets', [] ), function( $sidebar ) use ( $widget_id ) {
		return is_array( $sidebar ) && in_array( $widget_id, $sidebar );
	} ) );
	$value = [
		'type'    => 'widget',
		'widget'  => $type,
		'url'     => admin_url( 'widgets.php' ),
		'name'    => $wp_registered_widgets[ $widget_id ]['name'],
		'title'   => 'ctct_form' === $type ? $value['ctct_title'] : $value['title'],
		'sidebar' => $wp_registered_sidebars[ array_shift( $sidebars ) ]['name'],
	];
}

/**
 * Check for affected posts and widgets for the newly trashed form post type.
 *
 * @since NEXT
 *
 * @param int $form_id Form ID being trashed.
 * @return void
 */
function constant_contact_check_for_affected_forms_on_trash( $form_id ) {
	$option             = get_option( ConstantContact_Notifications::$deleted_forms, [] );
	$option[ $form_id ] = array_filter( array_merge(
		constant_contact_get_posts_by_form( $form_id ),
		constant_contact_get_widgets_by_form( $form_id )
	) );

	if ( empty( $option[ $form_id ] ) ) {
		return;
	}

	update_option( ConstantContact_Notifications::$deleted_forms, $option );
}
add_action( 'trash_ctct_forms', 'constant_contact_check_for_affected_forms_on_trash' );

/**
 * Remove saved references to deleted form if restored from trash.
 *
 * @since  NEXT
 *
 * @param  int $post_id Post ID being restored.
 * @return void
 */
function constant_contact_remove_form_references_on_restore( $post_id ) {
	if ( 'ctct_forms' !== get_post_type( $post_id ) ) {
		return;
	}

	$option = get_option( ConstantContact_Notifications::$deleted_forms, [] );

	unset( $option[ $post_id ] );

	update_option( ConstantContact_Notifications::$deleted_forms, $option );
}
add_action( 'untrashed_post', 'constant_contact_remove_form_references_on_restore' );

/**
 * Determine whether to display the deleted forms notice in admin.
 *
 * @since  NEXT
 *
 * @return bool Whether to display the deleted forms notice.
 */
function constant_contact_maybe_display_deleted_forms_notice() {
	return ! empty( get_option( ConstantContact_Notifications::$deleted_forms, [] ) );
}

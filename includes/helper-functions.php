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
	return constant_contact()->get_api()->is_connected();
}

/**
 * Checks to see if a user is not connected to Constant Contact or not.
 *
 * @since 1.0.0
 *
 * @return boolean Whether or not they are NOT connected.
 */
function constant_contact_is_not_connected() {
	return ! constant_contact()->get_api()->is_connected();
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
	return constant_contact()->get_display_shortcode()->get_form( $form_id, $show_title );
}

/**
 * Get a form and display it without using a shortcode.
 *
 * @since 1.0.0
 *
 * @param int  $form_id Form post ID to grab.
 * @param bool $show_title If true, show the title.
 */
function constant_contact_display_form( int $form_id, bool $show_title = false ) {
	constant_contact()->get_display_shortcode()->display_form( $form_id, $show_title );
}

/**
 * Get an array of forms.
 *
 * @since 1.0.0
 *
 * @return array WP_Query results of forms.
 */
function constant_contact_get_forms() {
	return constant_contact()->get_cpts()->get_forms( false, true );
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
 * Handle the ajax for the review admin notice.
 *
 * @since 1.2.2
 */
function constant_contact_review_ajax_handler() {

	if ( ! wp_verify_nonce( $_REQUEST['ctct_nonce'], 'ctct-user-is-dismissing' ) ) {
		wp_send_json_error( [ 'nonce-result' => 'failed' ] );
		exit();
	}

	$review_action = 'nothing processed';

	//  phpcs:disable WordPress.Security.NonceVerification -- OK accessing of $_REQUEST.
	if ( isset( $_REQUEST['ctct_review_action'] ) ) {
		$action = strtolower( sanitize_text_field( $_REQUEST['ctct_review_action'] ) );
		// phpcs:enable WordPress.Security.NonceVerification

		switch ( $action ) {
			case 'dismissed':
				$dismissed         = get_option( ConstantContact_Notifications::$review_dismissed_option, [] );
				$dismissed['time'] = time();
				if ( empty( $dismissed['count'] ) ) {
					$dismissed['count'] = '1';
				} elseif ( isset( $dismissed['count'] ) && '4' === $dismissed['count'] ) {
					$dismissed['count'] = '5';
				} elseif ( isset( $dismissed['count'] ) && '3' === $dismissed['count'] ) {
					$dismissed['count'] = '4';
				} elseif ( isset( $dismissed['count'] ) && '2' === $dismissed['count'] ) {
					$dismissed['count'] = '3';
				} else {
					$dismissed['count'] = '2';
				}
				update_option( ConstantContact_Notifications::$review_dismissed_option, $dismissed );

				$review_action = 'processed dismiss success';
				break;

			case 'reviewed':
				update_option( ConstantContact_Notifications::$reviewed_option, 'true' );

				$review_action = 'processed reviewed success';
				break;
		}
	}

	wp_send_json_success( [ 'review-action' => $review_action ] );
	exit();
}
add_action( 'wp_ajax_constant_contact_review_ajax_handler', 'constant_contact_review_ajax_handler' );

/**
 * Perform custom form processing.
 *
 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @since  1.9.0
 *
 * @return mixed Results of form processing, false if no processing performed.
 */
function constant_contact_process_form_custom() {
	$ctct_id = filter_input( INPUT_POST, 'ctct-id', FILTER_VALIDATE_INT );

	if ( false === $ctct_id ) {
		return false;
	}

	if ( ! constant_contact_has_redirect_uri( $ctct_id ) ) {
		return false;
	}

	return constant_contact()->get_process_form()->process_form();
}
add_action( 'wp_head', 'constant_contact_process_form_custom' );

/**
 * Check if any published Constant Contact forms exist.
 *
 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @since  1.9.0
 *
 * @return bool Whether published forms exist.
 */
function constant_contact_has_forms() {
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

	return constant_contact_is_valid_url( $maybe_redirect_uri );
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
	$current    = current_time( 'timestamp' ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
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
	$debugging_enabled = constant_contact_get_option( '_ctct_logging', '' );

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

	$logging_file = constant_contact()->logger_location;

	// Create logging file and directory if they don't exist.
	constant_contact()->get_logging()->initialize_logging();

	if ( ! is_writable( $logging_file ) ) {
		return;
	}

	$logger = new Logger( $log_name );
	$logger->pushHandler( new StreamHandler( $logging_file ) );
	$extra = [];

	if ( $extra_data ) {
		$extra = [ 'Extra information', [ $extra_data ] ];
	}

	$error = constant_contact()->get_logging()->mask_api_key( $error );

	$logger->info( $error, $extra );
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
	$fname = '';
	$lname = '';
	$name  = '';
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

	return constant_contact_akismet_spam_check( $args );
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
		$response = akismet_http_post(
			$query_string,
			$akismet_api_host,
			'/1.1/comment-check',
			$akismet_api_port
		);
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

	$global_form_disabled = constant_contact_get_option( '_ctct_disable_email_notifications', '' );
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

	$form_css = get_post_meta( absint( $form_id ) );

	if ( is_array( $form_css ) && array_key_exists( $customization_key, $form_css ) ) {
		if ( ! empty( $form_css[ $customization_key ][0] ) ) {
			return $form_css[ $customization_key ][0];
		}
	}

	$global_setting = constant_contact_get_option( $customization_key );

	return ! empty( $global_setting ) ? $global_setting : '';
}

/**
 * Set if we have an exception to deal with.
 *
 * @since 1.6.0
 *
 * @param string $status Status value to set.
 */
function constant_contact_set_has_exceptions( $status = 'true' ) {
	update_option( 'ctct_exceptions_exist', $status );
}

/**
 * Check whether or not we have an exception to handle.
 *
 * @since 2.10.0
 *
 * @return bool
 */
function constant_contact_get_has_exceptions(): bool {
	// force string true to be the only way to return true
	return 'true' === get_option( 'ctct_exceptions_exist', 'false' );
}


/**
 * Set if we need to manually reconnect.
 *
 * @param bool $status Status value to set.
 *
 * @since 2.10.0
 */
function constant_contact_set_needs_manual_reconnect( $status = 'true' ) {
	update_option( 'ctct_maybe_needs_reconnected', $status );
}

/**
 * Whether or not we need to manually reconnect.
 *
 * @since 2.10.0
 *
 * @return bool
 */
function constant_contact_get_needs_manual_reconnect(): bool {
	// force string true to be the only way to return true
	return 'true' === get_option( 'ctct_maybe_needs_reconnected', 'false' );
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
 * @since 1.8.0
 *
 * @param  int $form_id Form ID.
 * @return array        Array of posts containing the form ID.
 */
function constant_contact_get_posts_by_form( $form_id ) {
	global $wpdb;

	$shortcode_like      = $wpdb->esc_like( '[ctct' );
	$post_id_like_single = $wpdb->esc_like( "form='{$form_id}'" );
	$post_id_like_double = $wpdb->esc_like( "form=\"{$form_id}\"" );
	$posts               = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT ID, post_title, post_type FROM {$wpdb->posts} WHERE (`post_content` LIKE %s OR `post_content` LIKE %s) AND `post_status` = %s ORDER BY post_type ASC",
			"%{$shortcode_like}%{$post_id_like_single}%",
			"%{$shortcode_like}%{$post_id_like_double}%",
			'publish'
		),
		ARRAY_A
	);

	array_walk(
		$posts,
		function( &$value, $key ) {
			$value = [
				'type'  => 'post',
				'url'   => get_edit_post_link( $value['ID'] ),
				'label' => get_post_type_object( $value['post_type'] )->labels->singular_name,
				'id'    => $value['ID'],
			];
		}
	);

	return $posts;
}

/**
 * Get links and info on widgets containing specified form ID.
 *
 * @since  1.8.0
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
		$widgets = array_filter(
			get_option( "widget_{$widget_type}", [] ),
			function( $value ) use ( $data ) {
				if ( 'ctct_form' === $data['type'] ) {
					return absint( $value['ctct_form_id'] ) === $data['form_id'];
				} elseif ( 'text' === $data['type'] ) {
					if ( ! isset( $value['text'] ) || false === strpos( $value['text'], '[ctct' ) ) {
						return false;
					}
					return ( false !== strpos( $value['text'], "form=\"{$data['form_id']}\"" ) || false !== strpos( $value['text'], "form='{$data['form_id']}'" ) );
				}
				return false;
			}
		);
		array_walk( $widgets, 'constant_contact_walk_widget_references', $widget_type );
		$return = array_merge( $return, $widgets );
	}

	return $return;
}

/**
 * Walker callback for widget references of deleted forms.
 *
 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @since  1.8.0
 *
 * @param  array  $value Array of current widget settings.
 * @param  string $key   Current widget key.
 * @param  string $type  Type of widget.
 */
function constant_contact_walk_widget_references( array &$value, $key, $type ) {
	global $wp_registered_sidebars, $wp_registered_widgets;

	$widget_id = "{$type}-{$key}";
	$sidebars  = array_keys(
		array_filter(
			get_option( 'sidebars_widgets', [] ),
			function( $sidebar ) use ( $widget_id ) {
				return is_array( $sidebar ) && in_array( $widget_id, $sidebar, true );
			}
		)
	);
	$value     = [
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
 * @since 1.8.0
 *
 * @param int $form_id Form ID being trashed.
 * @return void
 */
function constant_contact_check_for_affected_forms_on_trash( $form_id ) {
	$option             = get_option( ConstantContact_Notifications::$deleted_forms, [] );
	$option[ $form_id ] = array_filter(
		array_merge(
			constant_contact_get_posts_by_form( $form_id ),
			constant_contact_get_widgets_by_form( $form_id )
		)
	);

	if ( empty( $option[ $form_id ] ) ) {
		return;
	}

	update_option( ConstantContact_Notifications::$deleted_forms, $option );
}
add_action( 'trash_ctct_forms', 'constant_contact_check_for_affected_forms_on_trash' );

/**
 * Remove saved references to deleted form if restored from trash.
 *
 * @since  1.8.0
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
 * Return an array of countries.
 *
 * US and UK listed first and second, the rest are alphabetical.
 *
 * @since 2.3.0
 *
 * @return string[]
 */
function constant_contact_countries_array() {
	return [
		esc_html__( 'United States', 'constant-contact-forms' ), esc_html__( 'Canada', 'constant-contact-forms' ), esc_html__( 'Afghanistan', 'constant-contact-forms' ), esc_html__( 'Albania', 'constant-contact-forms' ), esc_html__( 'Algeria', 'constant-contact-forms' ), esc_html__( 'Andorra', 'constant-contact-forms' ), esc_html__( 'Angola', 'constant-contact-forms' ), esc_html__( 'Antigua and Barbuda', 'constant-contact-forms' ), esc_html__( 'Argentina', 'constant-contact-forms' ), esc_html__( 'Armenia', 'constant-contact-forms' ), esc_html__( 'Australia', 'constant-contact-forms' ), esc_html__( 'Austria', 'constant-contact-forms' ), esc_html__( 'Azerbaijan', 'constant-contact-forms' ), esc_html__( 'The Bahamas', 'constant-contact-forms' ), esc_html__( 'Bahrain', 'constant-contact-forms' ), esc_html__( 'Bangladesh', 'constant-contact-forms' ), esc_html__( 'Barbados', 'constant-contact-forms' ), esc_html__( 'Belarus', 'constant-contact-forms' ), esc_html__( 'Belgium', 'constant-contact-forms' ), esc_html__( 'Belize', 'constant-contact-forms' ), esc_html__( 'Benin', 'constant-contact-forms' ), esc_html__( 'Bhutan', 'constant-contact-forms' ), esc_html__( 'Bolivia', 'constant-contact-forms' ), esc_html__( 'Bosnia and Herzegovina', 'constant-contact-forms' ), esc_html__( 'Botswana', 'constant-contact-forms' ), esc_html__( 'Brazil', 'constant-contact-forms' ), esc_html__( 'Brunei', 'constant-contact-forms' ), esc_html__( 'Bulgaria', 'constant-contact-forms' ), esc_html__( 'Burkina Faso', 'constant-contact-forms' ), esc_html__( 'Burundi', 'constant-contact-forms' ), esc_html__( 'Cabo Verde', 'constant-contact-forms' ), esc_html__( 'Cambodia', 'constant-contact-forms' ), esc_html__( 'Cameroon', 'constant-contact-forms' ), esc_html__( 'Central African Republic', 'constant-contact-forms' ), esc_html__( 'Chad', 'constant-contact-forms' ), esc_html__( 'Chile', 'constant-contact-forms' ), esc_html__( 'China', 'constant-contact-forms' ), esc_html__( 'Colombia', 'constant-contact-forms' ), esc_html__( 'Comoros', 'constant-contact-forms' ), esc_html__( 'Congo, Democratic Republic of the', 'constant-contact-forms' ), esc_html__( 'Congo, Republic of the', 'constant-contact-forms' ), esc_html__( 'Costa Rica', 'constant-contact-forms' ), esc_html__( 'Côte d’Ivoire', 'constant-contact-forms' ), esc_html__( 'Croatia', 'constant-contact-forms' ), esc_html__( 'Cuba', 'constant-contact-forms' ), esc_html__( 'Cyprus', 'constant-contact-forms' ), esc_html__( 'Czech Republic', 'constant-contact-forms' ), esc_html__( 'Denmark', 'constant-contact-forms' ), esc_html__( 'Djibouti', 'constant-contact-forms' ), esc_html__( 'Dominica', 'constant-contact-forms' ), esc_html__( 'Dominican Republic', 'constant-contact-forms' ), esc_html__( 'East Timor (Timor-Leste)', 'constant-contact-forms' ), esc_html__( 'Ecuador', 'constant-contact-forms' ), esc_html__( 'Egypt', 'constant-contact-forms' ), esc_html__( 'El Salvador', 'constant-contact-forms' ), esc_html__( 'Equatorial Guinea', 'constant-contact-forms' ), esc_html__( 'Eritrea', 'constant-contact-forms' ), esc_html__( 'Estonia', 'constant-contact-forms' ), esc_html__( 'Eswatini', 'constant-contact-forms' ), esc_html__( 'Ethiopia', 'constant-contact-forms' ), esc_html__( 'Fiji', 'constant-contact-forms' ), esc_html__( 'Finland', 'constant-contact-forms' ), esc_html__( 'France', 'constant-contact-forms' ), esc_html__( 'Gabon', 'constant-contact-forms' ), esc_html__( 'The Gambia', 'constant-contact-forms' ), esc_html__( 'Georgia', 'constant-contact-forms' ), esc_html__( 'Germany', 'constant-contact-forms' ), esc_html__( 'Ghana', 'constant-contact-forms' ), esc_html__( 'Greece', 'constant-contact-forms' ), esc_html__( 'Grenada', 'constant-contact-forms' ), esc_html__( 'Guatemala', 'constant-contact-forms' ), esc_html__( 'Guinea', 'constant-contact-forms' ), esc_html__( 'Guinea-Bissau', 'constant-contact-forms' ), esc_html__( 'Guyana', 'constant-contact-forms' ), esc_html__( 'Haiti', 'constant-contact-forms' ), esc_html__( 'Honduras', 'constant-contact-forms' ), esc_html__( 'Hungary', 'constant-contact-forms' ), esc_html__( 'Iceland', 'constant-contact-forms' ), esc_html__( 'India', 'constant-contact-forms' ), esc_html__( 'Indonesia', 'constant-contact-forms' ), esc_html__( 'Iran', 'constant-contact-forms' ), esc_html__( 'Iraq', 'constant-contact-forms' ), esc_html__( 'Ireland', 'constant-contact-forms' ), esc_html__( 'Israel', 'constant-contact-forms' ), esc_html__( 'Italy', 'constant-contact-forms' ), esc_html__( 'Jamaica', 'constant-contact-forms' ), esc_html__( 'Japan', 'constant-contact-forms' ), esc_html__( 'Jordan', 'constant-contact-forms' ), esc_html__( 'Kazakhstan', 'constant-contact-forms' ), esc_html__( 'Kenya', 'constant-contact-forms' ), esc_html__( 'Kiribati', 'constant-contact-forms' ), esc_html__( 'Korea, North', 'constant-contact-forms' ), esc_html__( 'Korea, South', 'constant-contact-forms' ), esc_html__( 'Kosovo', 'constant-contact-forms' ), esc_html__( 'Kuwait', 'constant-contact-forms' ), esc_html__( 'Kyrgyzstan', 'constant-contact-forms' ), esc_html__( 'Laos', 'constant-contact-forms' ), esc_html__( 'Latvia', 'constant-contact-forms' ), esc_html__( 'Lebanon', 'constant-contact-forms' ), esc_html__( 'Lesotho', 'constant-contact-forms' ), esc_html__( 'Liberia', 'constant-contact-forms' ), esc_html__( 'Libya', 'constant-contact-forms' ), esc_html__( 'Liechtenstein', 'constant-contact-forms' ), esc_html__( 'Lithuania', 'constant-contact-forms' ), esc_html__( 'Luxembourg', 'constant-contact-forms' ), esc_html__( 'Madagascar', 'constant-contact-forms' ), esc_html__( 'Malawi', 'constant-contact-forms' ), esc_html__( 'Malaysia', 'constant-contact-forms' ), esc_html__( 'Maldives', 'constant-contact-forms' ), esc_html__( 'Mali', 'constant-contact-forms' ), esc_html__( 'Malta', 'constant-contact-forms' ), esc_html__( 'Marshall Islands', 'constant-contact-forms' ), esc_html__( 'Mauritania', 'constant-contact-forms' ), esc_html__( 'Mauritius', 'constant-contact-forms' ), esc_html__( 'Mexico', 'constant-contact-forms' ), esc_html__( 'Micronesia, Federated States of', 'constant-contact-forms' ), esc_html__( 'Moldova', 'constant-contact-forms' ), esc_html__( 'Monaco', 'constant-contact-forms' ), esc_html__( 'Mongolia', 'constant-contact-forms' ), esc_html__( 'Montenegro', 'constant-contact-forms' ), esc_html__( 'Morocco', 'constant-contact-forms' ), esc_html__( 'Mozambique', 'constant-contact-forms' ), esc_html__( 'Myanmar (Burma)', 'constant-contact-forms' ), esc_html__( 'Namibia', 'constant-contact-forms' ), esc_html__( 'Nauru', 'constant-contact-forms' ), esc_html__( 'Nepal', 'constant-contact-forms' ), esc_html__( 'Netherlands', 'constant-contact-forms' ), esc_html__( 'New Zealand', 'constant-contact-forms' ), esc_html__( 'Nicaragua', 'constant-contact-forms' ), esc_html__( 'Niger', 'constant-contact-forms' ), esc_html__( 'Nigeria', 'constant-contact-forms' ), esc_html__( 'North Macedonia', 'constant-contact-forms' ), esc_html__( 'Norway', 'constant-contact-forms' ), esc_html__( 'Oman', 'constant-contact-forms' ), esc_html__( 'Pakistan', 'constant-contact-forms' ), esc_html__( 'Palau', 'constant-contact-forms' ), esc_html__( 'Panama', 'constant-contact-forms' ), esc_html__( 'Papua New Guinea', 'constant-contact-forms' ), esc_html__( 'Paraguay', 'constant-contact-forms' ), esc_html__( 'Peru', 'constant-contact-forms' ), esc_html__( 'Philippines', 'constant-contact-forms' ), esc_html__( 'Poland', 'constant-contact-forms' ), esc_html__( 'Portugal', 'constant-contact-forms' ), esc_html__( 'Qatar', 'constant-contact-forms' ), esc_html__( 'Romania', 'constant-contact-forms' ), esc_html__( 'Russia', 'constant-contact-forms' ), esc_html__( 'Rwanda', 'constant-contact-forms' ), esc_html__( 'Saint Kitts and Nevis', 'constant-contact-forms' ), esc_html__( 'Saint Lucia', 'constant-contact-forms' ), esc_html__( 'Saint Vincent and the Grenadines', 'constant-contact-forms' ), esc_html__( 'Samoa', 'constant-contact-forms' ), esc_html__( 'San Marino', 'constant-contact-forms' ), esc_html__( 'Sao Tome and Principe', 'constant-contact-forms' ), esc_html__( 'Saudi Arabia', 'constant-contact-forms' ), esc_html__( 'Senegal', 'constant-contact-forms' ), esc_html__( 'Serbia', 'constant-contact-forms' ), esc_html__( 'Seychelles', 'constant-contact-forms' ), esc_html__( 'Sierra Leone', 'constant-contact-forms' ), esc_html__( 'Singapore', 'constant-contact-forms' ), esc_html__( 'Slovakia', 'constant-contact-forms' ), esc_html__( 'Slovenia', 'constant-contact-forms' ), esc_html__( 'Solomon Islands', 'constant-contact-forms' ), esc_html__( 'Somalia', 'constant-contact-forms' ), esc_html__( 'South Africa', 'constant-contact-forms' ), esc_html__( 'Spain', 'constant-contact-forms' ), esc_html__( 'Sri Lanka', 'constant-contact-forms' ), esc_html__( 'Sudan', 'constant-contact-forms' ), esc_html__( 'Sudan, South', 'constant-contact-forms' ), esc_html__( 'Suriname', 'constant-contact-forms' ), esc_html__( 'Sweden', 'constant-contact-forms' ), esc_html__( 'Switzerland', 'constant-contact-forms' ), esc_html__( 'Syria', 'constant-contact-forms' ), esc_html__( 'Taiwan', 'constant-contact-forms' ), esc_html__( 'Tajikistan', 'constant-contact-forms' ), esc_html__( 'Tanzania', 'constant-contact-forms' ), esc_html__( 'Thailand', 'constant-contact-forms' ), esc_html__( 'Togo', 'constant-contact-forms' ), esc_html__( 'Tonga', 'constant-contact-forms' ), esc_html__( 'Trinidad and Tobago', 'constant-contact-forms' ), esc_html__( 'Tunisia', 'constant-contact-forms' ), esc_html__( 'Turkey', 'constant-contact-forms' ), esc_html__( 'Turkmenistan', 'constant-contact-forms' ), esc_html__( 'Tuvalu', 'constant-contact-forms' ), esc_html__( 'Uganda', 'constant-contact-forms' ), esc_html__( 'Ukraine', 'constant-contact-forms' ), esc_html__( 'United Arab Emirates', 'constant-contact-forms' ), esc_html__( 'United Kingdom', 'constant-contact-forms' ), esc_html__( 'Uruguay', 'constant-contact-forms' ), esc_html__( 'Uzbekistan', 'constant-contact-forms' ), esc_html__( 'Vanuatu', 'constant-contact-forms' ), esc_html__( 'Vatican City', 'constant-contact-forms' ), esc_html__( 'Venezuela', 'constant-contact-forms' ), esc_html__( 'Vietnam', 'constant-contact-forms' ), esc_html__( 'Yemen', 'constant-contact-forms' ), esc_html__( 'Zambia', 'constant-contact-forms' ), esc_html__( 'Zimbabwe', 'constant-contact-forms' ), // phpcs:ignore WordPress.Arrays.ArrayDeclarationSpacing.ArrayItemNoNewLine -- This is REALLY long list. Keeping it condensed.
	];
}

/**
 * CMB2 callback function to hide the "Disable email" setting if not connected.
 *
 * @since 2.11.0
 *
 * @return bool
 */
function constant_contact_should_hide_disable_admin_email() : bool {
	$show = true;

	/**
	 * We're not connected, don't allow.
	 */
	if ( empty( constant_contact()->get_api()->is_connected() ) ) {
		$show = false;
	}

	/**
	 * We were connected at some point, show after all.
	 */
	if ( constant_contact_get_needs_manual_reconnect() ) {
		$show = true;
	}

	return $show;
}

function ctct_modal_script_styles() {
	$current_screen = get_current_screen();

	if ( 'plugins' !== $current_screen->base ) {
		return;
	}
?>
<style>
	.ctct-feedback-modal {
		display: none;
		position: fixed;
		z-index: 1;
		left: 0;
		top: 0;
		width: 100%;
		height: 100%;
		overflow: auto;
		background-color: rgb(0, 0, 0);
		background-color: rgba(0, 0, 0, 0.4);
	}

	.ctct-feedback-modal-content {
		border-left: solid #144bee 5px;
		background-color: #fefefe;
		margin: 15% auto;
		padding: 20px;
		width: 50%;
		position: relative;
	}
	.ctct-feedback-modal-content a {
		color: #144bee;
	}

	.ctct-feedback-modal-content .modal-footer {
		font-size: 16px;
	}

	.ctct-feedback-modal-content img {
		width: 35%;
	}

	.ctct-feedback-close {
		float: right;
		font-size: 28px;
		font-weight: bold;
		text-decoration: none;
	}
	.ctct-feedback-modal-footer {
		display: flex;
		justify-content: space-between;
		margin-top: 30px;
	}
	.ctct-feedback-modal-footer a {
		margin: 0 10px;
		padding: 8px 10px;
	}
	.ctct-feedback-modal-footer .ctct-privacy a {
		font-size: 14px;
	}
	.ctct-feedback-modal-footer #ctct-feedback-modal-skip-deactivate {
		background-color: #144bee;
		color: #fff;
		text-decoration: none;
	}
</style>

<script>
	window.addEventListener('load', function () {
		let modal = document.querySelector("#ctct-feedback-modal");
		let closeBtn = document.querySelector('#ctct-feedback-close-btn');
		let cancelLink = document.querySelector('#ctct-feedback-cancel');
		let deactivateLink = document.querySelector('#deactivate-constant-contact-forms');
		let skipdeactivate = document.querySelector('#ctct-feedback-modal-skip-deactivate');

		if (deactivateLink) {
			deactivateLink.addEventListener('click', (e) => {
				e.preventDefault();
				window.ctctDeactivationLink = e.target.href;
				skipdeactivate.setAttribute('href', window.ctctDeactivationLink);
				modal.style.display = 'block';
			});
		}
		if (closeBtn) {
			closeBtn.addEventListener('click', (e) => {
				e.preventDefault();
				modal.style.display = 'none';
			});
		}

		if (cancelLink) {
			cancelLink.addEventListener('click', (e) => {
				e.preventDefault();
				modal.style.display = 'none';
			});
		}

		window.addEventListener('click', (e) => {
			if (e.target === modal) {
				modal.style.display = "none";
			}
		});
	});
</script>
<?php
}
add_action( 'admin_head', 'ctct_modal_script_styles' );

function ctct_modal_feedback() {
	$current_screen = get_current_screen();

	if ( 'plugins' !== $current_screen->base ) {
		return;
	}

	ob_start();
	?>
	<div id="ctct-feedback-modal" class="ctct-feedback-modal">
		<div class="ctct-feedback-modal-content">
			<div class="ctct-feedback-modal-title">
				<img src="<?php echo esc_url( constant_contact()->url ); ?>/assets/images/CTCT_Logo_H_FC_RGB.svg" alt="<?php echo esc_attr_x( 'Constant Contact logo', 'img alt text', 'constant-contact-forms' ); ?>" />
				<a id="ctct-feedback-close-btn" href="#" class="ctct-feedback-close">&times;</a>
			</div>
			<p>
				<?php esc_html_e( "We noticed you're thinking about deactivating the Constant Contact Forms plugin. Please consider offering your feedback using the link below. Understanding your experience helps us improve our services for you and others. We appreciate your input!", 'constant-contact-forms' ); ?>
			</p>

			<p>
				<a href="#" target="_blank"><?php esc_html_e( 'INSERT LINK HERE.', 'constant-contact-forms' ); ?></a>
			</p>

			<div class="ctct-feedback-modal-footer">
				<div class="ctct-privacy">
					<a href="https://www.constantcontact.com/legal/privacy-center" target="_blank"><?php esc_html_e( 'Privacy Center', 'constant-contact-forms' ); ?></a>
				</div>
				<div class="ctct-cancel-skip">
					<a id="ctct-feedback-cancel" href="#"><?php esc_html_e( 'Cancel', 'constant-contact-forms' ); ?></a>
					<a id="ctct-feedback-modal-skip-deactivate" href="#">
						<?php esc_html_e( 'Skip and deactivate', 'constant-contact-forms' ); ?>
					</a>
				</div>
			</div>
		</div>

	</div>
<?php
}
add_action( 'admin_footer', 'ctct_modal_feedback' );

<?php
/**
 * Constant Contact Settings class.
 *
 * @package ConstantContact
 * @subpackage Settings
 * @author Constant Contact
 * @since 1.0.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Powers our settings and options page, as well as injecting our optins to the front-end.
 *
 * @since 1.0.0
 */
class ConstantContact_Settings_Old {

	/**
	 * Option key, and option page slug.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $key = 'ctct_options_settings';

	/**
	 * Settings page metabox id.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $metabox_id = 'ctct_option_metabox_settings';

	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	protected $plugin;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $plugin Parent plugin instance.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		//$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		$this->inject_optin_form_hooks();

		add_filter( 'preprocess_comment', [ $this, 'process_optin_comment_form' ] );
		add_filter( 'authenticate', [ $this, 'process_optin_login_form' ], 10, 3 );

		add_action( 'cmb2_save_field__ctct_logging', [ $this, 'maybe_init_logs' ], 10, 2 );
		add_filter( 'ctct_custom_spam_message', [ $this, 'get_spam_error_message' ], 10, 2 );
	}

	/**
	 * Hook in all our form opt-in injects, decide to show or not when we are at the display point.
	 *
	 * @since 1.0.0
	 */
	public function inject_optin_form_hooks() {
		add_action( 'login_form', [ $this, 'optin_form_field_login' ] );
		add_action( 'comment_form', [ $this, 'optin_form_field_comment' ] );

		add_action( 'register_form', [ $this, 'optin_form_field_registration' ] );
		add_action( 'signup_extra_fields', [ $this, 'optin_form_field_registration' ] );
		add_action( 'login_head', [ $this, 'optin_form_field_login_css' ] );

		if ( ! $this->privacy_policy_status() ) {
			add_action( 'admin_footer', [ $this, 'privacy_notice_markup' ] );
		}
	}

	/**
	 * Based on a type of form we pass in, check if the saved option
	 * for that form is checked or not in the admin.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Allowed values: 'login_form', 'comment_form', 'reg_form'.
	 * @return boolean If should show or not.
	 */
	public function check_if_optin_should_show( $type ) {

		$available_areas = ctct_get_option( '_ctct_optin_forms', [] );

		if ( ! is_array( $available_areas ) ) {
			return false;
		}

		// Otherwise, check to see if our check is in the array.
		return in_array( $type, $available_areas, true );
	}

	/**
	 * Potentially add our opt-in form to the login form. We have three almost
	 * identical functions here. This allows us to hook them all in by themselves
	 * and determine whether or not they should have been hooked in when we get
	 * to displaying them, rather than on potentially pages we dont care about.
	 *
	 * @since 1.0.0
	 */
	public function optin_form_field_login() {
		if ( $this->check_if_optin_should_show( 'login_form' ) ) {
			$this->optin_form_field();
		}
	}

	/**
	 * Potentially add our opt-in form to comment forms.
	 *
	 * @since 1.0.0
	 */
	public function optin_form_field_comment() {
		if ( $this->check_if_optin_should_show( 'comment_form' ) ) {
			$this->optin_form_field();
		}
	}

	/**
	 * Potentially add our opt-in form to the registration form.
	 *
	 * @since 1.0.0
	 */
	public function optin_form_field_registration() {
		if ( $this->check_if_optin_should_show( 'reg_form' ) ) {
			$this->optin_form_field();
		}
	}

	/**
	 * Opt in field checkbox.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function optin_form_field() {
		if ( ! constant_contact()->api->is_connected() ) {
			return;
		}

		$saved_label = ctct_get_option( '_ctct_optin_label', '' );
		$list        = ctct_get_option( '_ctct_optin_list', '' );


		$label = $saved_label ?: esc_html__( 'Sign up to our newsletter.', 'constant-contact-forms' );

		?>
		<p class="ctct-optin-wrapper" style="padding: 0 0 1em 0;">
			<label for="ctct_optin">
				<input type="checkbox" value="<?php echo esc_attr( $list ); ?>" class="checkbox" id="ctct_optin" name="ctct_optin_list" />
				<?php echo esc_attr( $label ); ?>
			</label>
			<?php echo constant_contact()->display->get_disclose_text(); ?>
			<?php wp_nonce_field( 'ct_ct_add_to_optin', 'ct_ct_optin' ); ?>
		</p>
		<?php

	}

	/**
	 * Sends contact to CTCT if optin checked.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception
	 *
	 * @param array $comment_data Comment form data.
	 * @return array Comment form data.
	 */
	public function process_optin_comment_form( $comment_data ) {
		if ( ! isset( $_POST['ctct_optin_list'] ) ) {
			return $comment_data;
		}

		if ( ! isset( $_POST['ct_ct_optin'] ) ) {
			return $comment_data;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ct_ct_optin'] ) ), 'ct_ct_add_to_optin' ) ) {
			constant_contact_maybe_log_it( 'Nonces', 'process_optin_comment_form() nonce failed to verify.' );
			return $comment_data;
		}

		return $this->process_comment_data_for_optin( $comment_data );
	}

	/**
	 * Process our comment data and send to CC.
	 *
	 * @since 1.0.0
	 *
	 * @param array $comment_data Array of comment data.
	 * @return array Passed in comment data
	 */
	public function process_comment_data_for_optin( $comment_data ) {
		if ( isset( $comment_data['comment_author_email'] ) && $comment_data['comment_author_email'] ) {

			$name    = isset( $comment_data['comment_author'] ) ? $comment_data['comment_author'] : '';
			$website = isset( $comment_data['comment_author_url'] ) ? $comment_data['comment_author_url'] : '';

			if ( ! isset( $_POST['ctct_optin_list'] ) ) { // phpcs:ignore
				return $comment_data;
			}

			$list = sanitize_text_field( wp_unslash( $_POST['ctct_optin_list'] ) ); // phpcs:ignore
			$args = [
				'list'       => $list,
				'email'      => sanitize_email( $comment_data['comment_author_email'] ),
				'first_name' => sanitize_text_field( $name ),
				'last_name'  => '',
				'website'    => sanitize_text_field( $website ),
			];

			constantcontact_api()->add_contact( $args );
		}

		return $comment_data;
	}

	/**
	 * Sends contact to CTCT if optin checked.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception
	 *
	 * @param array  $user User.
	 * @param string $username Login name.
	 * @param string $password User password.
	 * @return object|array CTCT return API for contact or original $user array.
	 */
	public function process_optin_login_form( $user, $username, $password ) {
		if ( ! isset( $_POST['ctct_optin_list'] ) ) {
			return $user;
		}

		if ( ! isset( $_POST['ct_ct_optin'] ) ) {
			return $user;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ct_ct_optin'] ) ), 'ct_ct_add_to_optin' ) ) {
			constant_contact_maybe_log_it( 'Nonces', 'process_optin_login_form() nonce failed to verify.' );
			return $user;
		}

		if ( empty( $username ) ) {
			return $user;
		}

		return $this->process_user_data_for_optin( $user, $username );
	}

	/**
	 * Sends user data to CTCT.
	 *
	 * @since 1.0.0
	 *
	 * @param object $user     WP user object.
	 * @param string $username Username.
	 * @return object Passed in $user object.
	 */
	public function process_user_data_for_optin( $user, $username ) {
		$user_data = get_user_by( 'login', $username );
		$email     = '';
		$name      = '';

		if ( $user_data && isset( $user_data->data ) && isset( $user_data->data->user_email ) ) {
			$email = sanitize_email( $user_data->data->user_email );
		}

		if ( $user_data && isset( $user_data->data ) && isset( $user_data->data->display_name ) ) {
			$name = sanitize_text_field( $user_data->data->display_name );
		}

		if ( ! isset( $_POST['ctct_optin_list'] ) ) { // phpcs:ignore
			return $user;
		}

		$list = sanitize_text_field( wp_unslash( $_POST['ctct_optin_list'] ) ); // phpcs:ignore

		if ( $email ) {
			$args = [
				'email'      => $email,
				'list'       => $list,
				'first_name' => $name,
				'last_name'  => '',
			];

			constantcontact_api()->add_contact( $args );
		}

		return $user;
	}

	/**
	 * Returns the status of our privacy policy acceptance.
	 *
	 * @since 1.2.0
	 *
	 * @return bool
	 */
	public function privacy_policy_status() {
		$status = get_option( 'ctct_privacy_policy_status', '' );
		if ( '' === $status || 'false' === $status ) {
			return false;
		}

		return true;
	}

	/**
	 * Outputs the markup for the privacy policy modal popup.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function privacy_notice_markup() {
		if ( $this->privacy_policy_status() || ! constant_contact()->is_constant_contact() ) {
			return;
		}
		?>
		<div id="ctct-privacy-modal" class="ctct-modal">
			<div class="ctct-modal-dialog" role="document">
				<div class="ctct-modal-content">
					<div class="ctct-modal-header">
						<a href="#" class="ctct-modal-close" aria-hidden="true">&times;</a>
						<h2 class="ctct-logo"><img src="<?php echo constant_contact()->url . '/assets/images/constant-contact-logo.png' ?>" alt="<?php echo esc_attr_x( 'Constant Contact logo', 'img alt text', 'constant-contact-forms' ); ?>" /></h2>
					</div>
					<div class="ctct-modal-body ctct-privacy-modal-body">
						<?php
						echo constant_contact_privacy_policy_content();
						?>
					</div><!-- modal body -->
					<div id="ctct-modal-footer-privacy" class="ctct-modal-footer ctct-modal-footer-privacy">
						<a class="button button-blue ctct-connect" data-agree="true"><?php esc_html_e( 'Agree', 'constant-contact-forms' ); ?></a>
						<a class="button no-bg" data-agree="false"><?php esc_html_e( 'Disagree', 'constant-contact-forms' ); ?></a>
					</div>
				</div><!-- .modal-content -->
			</div><!-- .modal-dialog -->
		</div>
		<?php
	}

	/**
	 * Check if we have reCAPTCHA settings available to use with Google reCAPTCHA.
	 *
	 * @since 1.2.4
	 * @return bool
	 */
	public function has_recaptcha() {
		$site_key   = ctct_get_option( '_ctct_recaptcha_site_key', '' );
		$secret_key = ctct_get_option( '_ctct_recaptcha_secret_key', '' );

		if ( $site_key && $secret_key ) {
			return true;
		}
		return false;
	}

	/**
	 * Attempts to add the index file for protecting the log directory.
	 *
	 * @since 1.5.0
	 * @return void
	 */
	public function maybe_init_logs( $updated, $action ) {
		if ( 'updated' !== $action ) {
			return;
		}

		$this->plugin->logging->create_log_folder();
		$this->plugin->logging->create_log_index_file();
		$this->plugin->logging->create_log_file();
	}

	/**
	 * Get the error message displayed to suspected spam input.
	 *
	 * @since 1.5.0
	 * @param string $message The error message to filter.
	 * @param mixed  $post_id The post ID of the current post, if any.
	 * @return string
	 */
	public function get_spam_error_message( $message, $post_id ) {
		$post_error = get_post_meta( $post_id, '_ctct_spam_error', true );

		if ( ! empty( $post_error ) ) {
			return $post_error;
		}

		$option_error = cmb2_get_option( '_ctct_spam_error' );

		if ( ! empty( $option_error ) ) {
			return $option_error;
		}

		return $this->get_default_spam_error();
	}

}

<?php
/**
 * Logging
 *
 * @package ConstantContact
 * @subpackage Logging
 * @author Constant Contact
 * @since 1.3.7
 */


class ConstantContact_Logging {

	/**
	 * Option key, and option page slug.
	 *
	 * @since 1.3.7
	 * @var string
	 */
	private $key = 'ctct_options_logging';

	/**
	 * Parent plugin class.
	 *
	 * @since 1.3.7
	 * @var object
	 */
	protected $plugin = null;

	/**
	 * Logging admin page URL.
	 *
	 * @since 1.3.7
	 * @var string
	 */
	public $options_url = '';

	/**
	 * Options page.
	 *
	 * @since 1.3.7
	 * @var string
	 */
	public $options_page = '';

	/**
	 * Constructor.
	 *
	 * @since 1.3.7
	 *
	 * @param object $plugin Parent class.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->options_url = admin_url( 'edit.php?post_type=ctct_forms&page=ctct_options_logging' );
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.3.7
	 */
	public function hooks() {
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'admin_init', array( $this, 'delete_log_file' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'admin_footer', array( $this, 'dialog' ) );
	}

	/**
	 * Add our jQuery UI elements for dialog confirmation.
	 *
	 * @since 1.3.7
	 */
	public function scripts() {
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
	}

	public function dialog() {
	?>
		<div id="confirmdelete" style="display:none;">
			<?php esc_html_e( 'Are you sure you want to delete current logs?', 'constant-contact-forms' ); ?>
		</div>
	<?php
	}

	/**
	 * Add menu options page.
	 *
	 * @since 1.3.7
	 */
	public function add_options_page() {

		$debugging_enabled = ctct_get_settings_option( '_ctct_logging' );

		if ( 'on' !== $debugging_enabled ) {
			return;
		}

		$connect_title = esc_html__( 'Debug logs', 'constant-contact-forms' );
		$connect_link  = 'edit.php?post_type=ctct_forms';

		// Set up our page.
		$this->options_page = add_submenu_page(
			$connect_link,
			$connect_title,
			$connect_title,
			'manage_options',
			$this->key,
			array( $this, 'admin_page_display' )
		);
	}

	/**
	 * Admin page markup.
	 *
	 * @since 1.3.7
	 *
	 * @return mixed page markup or false if not admin.
	 */
	public function admin_page_display() {

		wp_enqueue_style( 'constant-contact-oath', constant_contact()->url() . 'assets/css/oath.css' );

		?>
		<div class="wrap <?php echo esc_attr( $this->key ); ?>">
			<img class="ctct-logo" src="<?php echo esc_url( constant_contact()->url . 'assets/images/constant-contact-logo.png' ); ?>" alt="<?php esc_attr_e( 'Constant Contact logo', 'constant-contact-forms' ); ?>">
			<div class="ctct-body">
				<?php
				$contents     = '';
				$log_location = '#';

				if ( ! file_exists( constant_contact()->logger_location ) ) {
					$contents .= esc_html__( 'No error log exists', 'constant-contact-forms' );
				} else {
					// logger location from primary class is server path and not URL path. Thus we go this route for moment.
					$log_location = content_url() . '/ctct-logs/constant-contact-errors.log';
					$log_content  = wp_remote_get( $log_location );
					if ( is_wp_error( $log_content ) ) {
						$contents .= sprintf(
							// translators: placeholder wil have error message.
							esc_html__(
								'Log display error: %s',
								'constant-contact-forms'
							),
							$log_content->get_error_message()
						);
					} else {
						$contents .= wp_remote_retrieve_body( $log_content );
					}
				}
				?>
				<p><?php esc_html_e( 'Error log below can be used with support requests to help identify issues with Constant Contact Forms.', 'constant-contact-forms' ); ?></p>
				<p><?php esc_html_e( 'When available, you can share information by copying and pasting the content in the textarea, or by using the "Download logs" link at the end. Logs can be cleared by using the "Delete logs" link.', 'constant-contact-forms' ); ?></p>
				<label><textarea name="ctct_error_logs" id="ctct_error_logs" cols="80" rows="40" onclick="this.focus();this.select();" onfocus="this.focus();this.select();" readonly="readonly" aria-readonly="true"><?php echo esc_html( $contents ); ?></textarea></label>
				<?php

				if ( file_exists( constant_contact()->logger_location ) ) {
					if ( ! empty( $log_content ) && is_wp_error( $log_content ) ) {
						?>
						<p><?php esc_html_e( 'Error log may still have content, even if an error is shown above. Please use the download link below.', 'constant-contact-forms' ); ?></p>
						<?php
					}
					?>
					<p>
						<?php
							printf(
								'<p><a href="%s" download>%s</a></p><p><a href="%s" id="deletelog">%s</a></p>',
								esc_attr( $log_location ),
								esc_html__( 'Download logs', 'constant-contact-forms' ),
								esc_attr(
									wp_nonce_url(
										$this->options_url,
										'ctct_delete_log',
										'ctct_delete_log'
									)
								),
								esc_html__( 'Delete logs', 'constant-contact-forms' )
							);
						?>
					</p>
					<?php
				}
				// @TODO Remind to turn off debugging setting when not needed.
				?>
			</div>
		</div>
		<?php
		return true;
	}

	/**
	 * Delete existing log files.
	 *
	 * @since 1.3.7
	 */
	public function delete_log_file() {
		if ( ! constant_contact()->is_constant_contact() ) {
			return;
		}

		if ( empty( $_GET['page'] ) || 'ctct_options_logging' !== $_GET['page'] ) {
			return;
		}

		if ( ! isset( $_GET['ctct_delete_log'] ) ) {
			return;
		}

		check_admin_referer( 'ctct_delete_log', 'ctct_delete_log' );

		$log_file = constant_contact()->logger_location;
		if ( file_exists( $log_file ) ) {
			unlink( $log_file );
		}

		wp_redirect( $this->options_url );
		exit();
	}
}

<?php
/**
 * Logging
 *
 * @package ConstantContact
 * @subpackage Logging
 * @author Constant Contact
 * @since 1.3.7
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Class ConstantContact_Logging
 *
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
	protected $plugin;

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
	 * Log location, URL path.
	 *
	 * @since 1.4.5
	 * @var string
	 */
	protected $log_location_url = '';

	/**
	 * Log location, server path.
	 *
	 * Prior to 1.5.0 this was `$log_location_dir`.
	 *
	 * @since 1.5.0
	 * @var string
	 */
	protected $log_location_file = '';

	/**
	 * Log directory location, server path.
	 *
	 * @since 1.4.5
	 * @var string
	 */
	protected $log_location_dir = '';

	/**
	 * The location of the log folder's index file.
	 *
	 * @since 1.5.0
	 * @var string
	 */
	protected $log_index_file = '';

	/**
	 * WP_Filesystem
	 *
	 * @since 1.4.5
	 * @var null
	 */
	protected $file_system;

	/**
	 * Constructor.
	 *
	 * @since 1.3.7
	 *
	 * @param object $plugin Parent class.
	 */
	public function __construct( $plugin ) {
		$this->plugin            = $plugin;
		$this->options_url       = admin_url( 'edit.php?post_type=ctct_forms&page=ctct_options_logging' );
		$this->log_location_url  = content_url() . '/ctct-logs/constant-contact-errors.log';
		$this->log_location_dir  = WP_CONTENT_DIR . '/ctct-logs';
		$this->log_location_file = "{$this->log_location_dir}/constant-contact-errors.log";
		$this->log_index_file    = "{$this->log_location_dir}/index.php";

		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.3.7
	 */
	public function hooks() {
		add_action( 'admin_menu', [ $this, 'add_options_page' ] );
		add_action( 'admin_init', [ $this, 'delete_log_file' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'scripts' ] );
		add_action( 'admin_footer', [ $this, 'dialog' ] );
		add_action( 'admin_init', [ $this, 'set_file_system' ] );
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

	/**
	 * Add our dialog message to confirm deletion of error logs.
	 *
	 * @since 1.3.7
	 */
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
	 *
	 * @return null
	 */
	public function add_options_page() {

		$debugging_enabled = ctct_get_settings_option( '_ctct_logging', '' );

		if ( 'on' !== $debugging_enabled ) {
			return;
		}

		$connect_title = esc_html__( 'Debug logs', 'constant-contact-forms' );
		$connect_link  = 'edit.php?post_type=ctct_forms';

		$this->options_page = add_submenu_page(
			$connect_link,
			$connect_title,
			$connect_title,
			'manage_options',
			$this->key,
			[ $this, 'admin_page_display' ]
		);
	}

	public function set_file_system() {
		global $wp_filesystem;
		WP_Filesystem();
		$this->file_system = $wp_filesystem;
	}

	/**
	 * Admin page markup.
	 *
	 * @since 1.3.7
	 *
	 * @return mixed page markup or false if not admin.
	 */
	public function admin_page_display() {

		// We will be nice and remove the exception/error status once they visit the logging page.
		constant_contact_set_has_exceptions( 'false' );

		wp_enqueue_style( 'constant-contact-forms-admin' );

		?>
		<div class="wrap <?php echo esc_attr( $this->key ); ?>">
			<img class="ctct-logo" src="<?php echo esc_url( constant_contact()->url . 'assets/images/constant-contact-logo.png' ); ?>" alt="<?php echo esc_attr_x( 'Constant Contact logo', 'img alt text', 'constant-contact-forms' ); ?>">
			<div class="ctct-body">
				<?php
				$contents     = '';
				$log_location = $this->log_location_url;

				if ( ! file_exists( constant_contact()->logger_location ) ) {

					if ( ! is_writable( constant_contact()->logger_location ) ) {
						$contents .= sprintf(
						/* Translators: placeholder holds the log location. */
							esc_html__( 'We are not able to write to the %s file.', 'constant-contact-forms' ),
							constant_contact()->logger_location
						);
					} else {
						$contents .= esc_html__( 'No error log exists', 'constant-contact-forms' );
					}
				} elseif ( ! is_writable( constant_contact()->logger_location ) ) {
					$contents .= sprintf(
						/* Translators: placeholder holds the log location. */
						esc_html__( 'We are not able to write to the %s file.', 'constant-contact-forms' ),
						constant_contact()->logger_location
					);
				} else {
					$contents .= $this->get_log_contents();
				}
				?>
				<p><?php esc_html_e( 'Error log below can be used with support requests to help identify issues with Constant Contact Forms.', 'constant-contact-forms' ); ?></p>
				<p><?php esc_html_e( 'When available, you can share information by copying and pasting the content in the textarea, or by using the "Download logs" link at the end. Logs can be cleared by using the "Delete logs" link.', 'constant-contact-forms' ); ?></p>
				<textarea name="ctct_error_logs" id="ctct_error_logs" cols="80" rows="40" onclick="this.focus();this.select();" onfocus="this.focus();this.select();" readonly="readonly" aria-readonly="true"><?php echo esc_html( $contents ); ?></textarea>
				<?php

				if ( file_exists( constant_contact()->logger_location ) ) {
					if ( ! empty( $contents ) && is_wp_error( $contents ) ) {
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
	 *
	 * @return null
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

		$log_file = $this->log_location_file;
		if ( file_exists( $log_file ) ) {
			unlink( $log_file );
		}

		$this->create_log_file();

		wp_redirect( $this->options_url );
		exit();
	}

	/**
	 * Get our log content.
	 *
	 * @since 1.4.5
	 *
	 * @return string
	 */
	protected function get_log_contents() {
		$log_content_url = wp_remote_get( $this->log_location_url );
		if ( is_wp_error( $log_content_url ) ) {
			return sprintf(
			// translators: placeholder wil have error message.
				esc_html__(
					'Log display error: %s',
					'constant-contact-forms'
				),
				$log_content_url->get_error_message()
			);
		}

		if ( 200 === wp_remote_retrieve_response_code( $log_content_url ) ) {
			return wp_remote_retrieve_body( $log_content_url );
		}

		$log_content_dir = $this->file_system->get_contents( $this->log_location_file );
		if ( ! empty( $log_content_dir ) && is_string( $log_content_dir ) ) {
			return $log_content_dir;
		}
	}

	/**
	 * Delete the log index protection file when logging is disabled.
	 *
	 * @since 1.5.0
	 * @return void
	 */
	public function delete_log_index_file() {
		if ( constant_contact_debugging_enabled() ) {
			return;
		}

		if ( file_exists( $this->log_index_file ) ) {
			unlink( $this->log_index_file );
		}
	}

	/**
	 * Create the log folder.
	 *
	 * @since 1.5.0
	 * @return void
	 */
	public function create_log_folder() {
		if ( ! constant_contact_debugging_enabled() ) {
			return;
		}

		mkdir( $this->log_location_dir );
		chmod( $this->log_location_dir, 0755 );
	}

	/**
	 * Create the log folder with an `index.php` file.
	 *
	 * @since 1.5.0
	 * @return void
	 */
	public function create_log_index_file() {
		if ( ! constant_contact_debugging_enabled() ) {
			return;
		}

		if ( ! is_writable( $this->log_location_dir ) ) {
			return;
		}

		if ( file_exists( $this->log_index_file ) ) {
			return;
		}

		touch( $this->log_index_file );
	}

	/**
	 * Create the log file itself.
	 *
	 * @since 1.5.0
	 * @return void
	 */
	public function create_log_file() {
		if ( ! constant_contact_debugging_enabled() ) {
			return;
		}

		if ( ! is_writable( $this->log_location_dir ) ) {
			return;
		}

		if ( file_exists( $this->log_location_file ) ) {
			return;
		}

		touch( $this->log_location_file );
	}
}

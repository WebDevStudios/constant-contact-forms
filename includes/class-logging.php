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
	private string $key = 'ctct_options_logging';

	/**
	 * Parent plugin class.
	 *
	 * @since 1.3.7
	 * @var object
	 */
	protected object $plugin;

	/**
	 * Logging admin page URL.
	 *
	 * @since 1.3.7
	 * @var string
	 */
	public string $options_url = '';

	/**
	 * Options page.
	 *
	 * @since 1.3.7
	 * @var string
	 */
	public string $options_page = '';

	/**
	 * Log location, URL path.
	 *
	 * @since 1.4.5
	 * @var string
	 */
	protected string $log_location_url = '';

	/**
	 * Log location, server path.
	 *
	 * Prior to 1.5.0 this was `$log_location_dir`.
	 *
	 * @since 1.5.0
	 * @var string
	 */
	protected string $log_location_file = '';

	/**
	 * Log directory location, server path.
	 *
	 * @since 1.4.5
	 * @var string
	 */
	protected string $log_location_dir = '';

	/**
	 * The location of the log folder's index file.
	 *
	 * @since 1.5.0
	 * @var string
	 */
	protected string $log_index_file = '';

	/**
	 * The location of the log folder's htaccess file.
	 *
	 * @since 2.4.3
	 * @var string
	 */
	protected string $log_htaccess_file = '';

	/**
	 * The logging directory name.
	 *
	 * @since 1.8.2
	 * @var   string
	 */
	protected string $log_file_dir = 'ctct-logs';

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
		$this->plugin      = $plugin;
		$this->options_url = admin_url( 'edit.php?post_type=ctct_forms&page=ctct_options_logging' );
		$uploads_dir       = wp_upload_dir();
		$new_suffix        = $this->generate_random_string();
		$suffix            = get_option( 'ctct_log_suffix', '' );
		if ( empty( $suffix ) ) {
			$suffix = $new_suffix;
			update_option( 'ctct_log_suffix', $suffix );
		}
		$log_file_name           = "constant-contact-errors-$suffix.log";
		$this->log_location_url  = "{$uploads_dir['baseurl']}/$this->log_file_dir/$log_file_name";
		$this->log_location_dir  = "{$uploads_dir['basedir']}/$this->log_file_dir";
		$this->log_location_file = "$this->log_location_dir/$log_file_name";
		$this->log_index_file    = "$this->log_location_dir/index.php";
		$this->log_htaccess_file = "$this->log_location_dir/.htaccess";

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
		add_action( 'admin_init', [ $this, 'maybe_delete_old_log_dir' ] );
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
	 */
	public function add_options_page() {

		$debugging_enabled = constant_contact_get_option( '_ctct_logging', '' );

		if ( 'on' !== $debugging_enabled ) {
			return;
		}

		$connect_title = esc_html__( 'Debug Logs', 'constant-contact-forms' );
		$this->options_page = add_submenu_page(
			'edit.php?post_type=ctct_forms',
			$connect_title,
			$connect_title,
			'manage_options',
			$this->key,
			[ $this, 'admin_page_display' ]
		);
	}

	/**
	 * Set file system.
	 *
	 * @author Michael Beckwith <michael@webdevstudios.com>
	 * @since  1.4.5
	 */
	public function set_file_system() {
		global $wp_filesystem;
		WP_Filesystem();
		$this->file_system = $wp_filesystem;
	}

	/**
	 * Admin page markup.
	 *
	 * @since 1.3.7
	 */
	public function admin_page_display() {

		// We will be nice and remove the exception/error status once they visit the logging page.
		constant_contact_set_has_exceptions( 'false' );

		wp_enqueue_style( 'constant-contact-forms-admin' );

		?>
		<div class="wrap ctct-page-wrap <?php echo esc_attr( $this->key ); ?>">
			<h1><?php echo get_admin_page_title(); ?></h1>

			<?php

			$contents = esc_html__( 'No errors exists.', 'constant-contact-forms' );

			if ( ! file_exists( $this->log_location_file ) ) {
				$contents = esc_html__( 'No error log exists.', 'constant-contact-forms' );
			}

			if ( ! is_writable( $this->log_location_file ) ) {
				$contents = sprintf(
					/* Translators: placeholder holds the log location. */
					esc_html__( 'We are not able to write to the %s file.', 'constant-contact-forms' ),
					constant_contact()->logger_location
				);
			}

			if ( is_file( $this->log_location_file ) && is_readable( $this->log_location_file ) ) {
				$contents = file_get_contents( $this->log_location_file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Not reading over network, it's on the filesystem.
			}

			?>
			<p class="large-text"><?php esc_html_e( 'The error log below can be used with support requests to help identify issues with Constant Contact Forms.', 'constant-contact-forms' ); ?></p>
			<p><?php esc_html_e( 'When available, you can share information by copying and pasting the content in the textarea, or by using the "Download logs" link below. Logs can be cleared by using the "Delete logs" link.', 'constant-contact-forms' ); ?></p>
			<textarea name="ctct_error_logs" id="ctct_error_logs" cols="80" rows="40" onclick="this.focus();this.select();" onfocus="this.focus();this.select();" readonly="readonly" aria-readonly="true"><?php echo esc_html( $contents ); ?></textarea>
			<?php

			if ( is_file( $this->log_location_file ) && ! is_readable( $this->log_location_file ) ) {
				?>
				<p><?php esc_html_e( 'Error log may still have content, even if an error is shown above. Please use the download link below.', 'constant-contact-forms' ); ?></p>
				<?php
			}

			if ( file_exists( $this->log_location_file ) ) {
				?>
				<div class="ctct-button-actions">
					<?php
						printf(
							'<a class="button button-primary" href="%s" download>%s</a> <a class="button" href="%s" id="deletelog">%s</a>',
							esc_attr( $this->log_location_url ),
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
				</div>
				<?php
			}
			// @TODO Remind to turn off debugging setting when not needed.
			?>
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

		if ( constant_contact_debugging_enabled() ) {
			$this->create_log_file();
		}

		wp_safe_redirect( $this->options_url );
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
	 * Delete the log htaccess protection file when logging is disabled.
	 *
	 * @since 2.4.3
	 * @return void
	 */
	public function delete_log_htaccess_file() {
		if ( constant_contact_debugging_enabled() ) {
			return;
		}

		if ( file_exists( $this->log_htaccess_file ) ) {
			unlink( $this->log_htaccess_file );
		}
	}

	/**
	 * Create the log folder.
	 *
	 * @since 1.5.0
	 */
	public function create_log_folder() {
		wp_mkdir_p( $this->log_location_dir );
	}

	/**
	 * Create the log folder with an `index.php` file.
	 *
	 * @since 1.5.0
	 * @return void
	 */
	public function create_log_index_file() {
		if ( ! is_writable( $this->log_location_dir ) ) {
			return;
		}

		if ( file_exists( $this->log_index_file ) ) {
			return;
		}

		touch( $this->log_index_file );
	}

	/**
	 * Create the log folder with a .htaccess` file.
	 *
	 * @since 2.4.3
	 * @return void
	 */
	public function create_log_htaccess_file() {
		if ( ! is_writable( $this->log_location_dir ) ) {
			return;
		}

		if ( file_exists( $this->log_htaccess_file ) ) {
			return;
		}

		touch( $this->log_htaccess_file );
		file_put_contents( // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
			$this->log_htaccess_file,
			'<FilesMatch ".log">
				Order Allow,Deny
				Deny from All
			</FilesMatch>'
		);
	}

	/**
	 * Create the log file itself.
	 *
	 * @since 1.5.0
	 * @return void
	 */
	public function create_log_file() {
		if ( ! is_writable( $this->log_location_dir ) ) {
			return;
		}

		if ( file_exists( $this->log_location_file ) ) {
			return;
		}

		touch( $this->log_location_file );
	}

	/**
	 * Retrieve logging file location.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.8.2
	 *
	 * @return string Logging file location.
	 */
	public function get_logging_location() {
		return $this->log_location_file;
	}

	/**
	 * Remove old logging directory and files for older plugin versions (<= 1.8.1).
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.8.2
	 *
	 * @return void
	 */
	public function maybe_delete_old_log_dir() {
		if ( Constant_Contact::VERSION <= '1.8.1' ) {
			return;
		}

		$this->delete_log_dir( WP_CONTENT_DIR . '/ctct-logs' );
	}

	/**
	 * Remove current logging directory and files.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.8.2
	 */
	public function delete_current_log_dir() {
		$this->delete_log_dir( $this->log_location_dir );
	}

	/**
	 * Helper function to remove logging directory.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.8.2
	 *
	 * @param  string $dir Directory path.
	 * @return void
	 */
	protected function delete_log_dir( $dir = '' ) {
		if ( empty( $dir ) || ! is_dir( $dir ) ) {
			return;
		}

		// Don't allow removing non-logging directories.
		if ( empty( stristr( $dir, $this->log_file_dir ) ) ) {
			return;
		}

		array_map( 'unlink', glob( "{$dir}/*" ) );
		rmdir( $dir );
	}

	/**
	 * Helper function to mask API Keys present in error messages returned by API.
	 * The last 3 characters will be revealed, but the others will be dropped/masked.
	 *
	 * Example input:  Server error response [url] https://api.constantcontact.com/v2/lists?api_key=1234567890abcdefghijklmn [status code] 596 ...
	 * Example output: Server error response [url] https://api.constantcontact.com/v2/lists?api_key=**********lmn [status code] 596 ...
	 *
	 * @since 1.13.0
	 *
	 * @param string $message Text that may contain the api_key value.
	 *
	 * @return string $message with masked api_key value.
	 */
	public function mask_api_key( $message ) {
		if ( empty( $message ) ) {
			return $message;
		}

		if ( strpos( $message, 'api_key' ) === false ) {
			return $message;
		}

		$key_pattern = '/(?<=api_key=)([^\s]+)/m';
		preg_match( $key_pattern, $message, $matches );
		$key = ! empty( $matches[0] ) ? $matches[0] : false;

		if ( ! empty( $key ) ) {
			$key_length = strlen( $key );
			$message    = preg_replace(
				$key_pattern,
				str_repeat( '*', 10 ) . // We won't reveal the actual key length.
				$key[ $key_length - 3 ] .
				$key[ $key_length - 2 ] .
				$key[ $key_length - 1 ],
				$message
			);
		}

		return $message;
	}

	/**
	 * Initialize Logging directories and files.
	 *
	 * @author Richard Aber <richard.aber@webdevstudios.com>
	 * @since  1.8.5
	 */
	public function initialize_logging() {
		$this->create_log_folder();
		$this->create_log_index_file();
		$this->create_log_htaccess_file();
		$this->create_log_file();
	}

	public function get_log_locations() {
		return [
			'directory' => $this->log_location_dir,
			'file'      => $this->log_location_file,
		];
	}

	/**
	 * Generate a random string that we are NOT using for crypto security purposes.
	 *
	 * @since 2.4.3
	 *
	 * @param int $length How many characters to generate for our string.
	 *
	 * @return string Generated string of characters.
	 * @throws \Random\RandomException
	 */
	public function generate_random_string( int $length = 10 ) {
		$characters        = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$characters_length = strlen( $characters );
		$random_string     = '';
		for ( $i = 0; $i < $length; $i ++ ) {
			$random_string .= $characters[ random_int( 0, $characters_length - 1 ) ];
		}

		return $random_string;
	}
}

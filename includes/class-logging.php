<?php
/**
 * Logging
 *
 * @package ConstantContact
 * @subpackage Logging
 * @author Constant Contact
 * @since NEXT
 */


class ConstantContact_Logging {

	/**
	 * Option key, and option page slug.
	 *
	 * @since NEXT
	 * @var string
	 */
	private $key = 'ctct_options_logging';

	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	protected $plugin = null;


	/**
	 * Options page.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $options_page = '';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $plugin Parent class.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
	}

	/**
	 * Add menu options page.
	 *
	 * @since 1.0.0
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
	 * Admin page markup. Mostly handled by CMB2.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed page markup or false if not admin.
	 */
	public function admin_page_display() {

		wp_enqueue_style( 'constant-contact-oath', constant_contact()->url() . 'assets/css/oath.css' );

		?>
		<div class="wrap cmb2-options-page <?php echo esc_attr( $this->key ); ?>">
			<img class="ctct-logo" src="<?php echo esc_url( constant_contact()->url . 'assets/images/constant-contact-logo.png' ); ?>">
			<div class="ctct-body">
                <?php
                $log_location = content_url() . '/ctct-logs/constant-contact-errors.txt';
                $log_content  = wp_remote_get( $log_location );
                $contents     = '';

                if ( ! file_exists( $log_location ) ) {
                    $contents .= esc_html__( 'No error log exists', 'constant-contact-forms' );
                } elseif ( is_wp_error( $log_content ) ) {
                    $contents .= sprintf(
                        esc_html__(
                            'Log display error: %s'
                        ),
                        $log_content->get_error_message()
                    );
                } else {
                    $contents .= wp_remote_retrieve_body( $log_content );
                }
                ?>
                <textarea style="height: 400px; width: 100%; resize: vertical;" name="ctct_error_logs" id="ctct_error_logs" cols="80" rows="40" onclick="this.focus();this.select();" onfocus="this.focus();this.select();" readonly="readonly" aria-readonly="true"><?php echo esc_html( $contents ); ?></textarea>
                <?php
                if ( is_wp_error( $log_content ) ) {
                ?>
                <p><?php esc_html_e( 'Error log may still have content, even if an error is shown above. Please use the link below.', 'constant-contact-forms' ); ?></p>
                <?php
                }

                if ( file_exists( $log_location ) ) {
	                ?>
	                <p>
		                <a href="<?php echo esc_attr( $log_location ); ?>" download>
			                <?php esc_html_e( 'Download error log to use as attachment', 'constant-contact-forms' ); ?>
		                </a>
	                </p>
	                <?php
                }
                // @TODO create log deletion methods.
                // @TODO Remind to turn off debugging setting when not needed.
                // @TODO hide menu item if debugging not enabled.
                // @TODO phpcs linting.
                ?>
            </div>
		</div>
		<?php
		return true;
	}
}

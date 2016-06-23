<?php
/**
 * ConstantContact_Admin_About Class File
 *
 * @package ConstantContact
 * @subpackage Admin
 * @author WebDevStudios
 * @since 1.0.0
 */

/**
 * Main initiation class
 *
 * @internal
 *
 * @since  1.0.0
 */
class ConstantContact_Admin_About {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @param class $plugin this class.
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Initiate our hooks
	 *
	 * @internal
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
	}




	/**
	 * Output our tab navigation.
	 *
	 * @internal
	 * @since 1.0.0
	 * @param string $tab Active tab.
	 */
	public static function tab_navigation( $tab = 'whats_new' ) {
	?>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php if ( 'ConstantContact_Admin_About::about_screen' === $tab ) : ?>nav-tab-active<?php endif; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'cptui_about' ), 'index.php' ) ) ); ?>">
				<?php esc_html_e( 'What&#8217;s New', 'constantcontact' ); ?>
			</a>
		</h2>

	<?php
	}
}

/**
 * About page class setup
 *
 * @internal
 * @since 1.0.0
 * @param array $args data to populate the about page.
 * @return void
 */
function constantcontact_about( $args = array() ) {
	$about = new ConstantContact_Admin_About;
	$about->init( $args );
}

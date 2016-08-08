<?php
/**
 * ConstantContact_Admin_Pages class
 *
 * @package ConstantContact_Admin_Pages
 * @subpackage ConstantContact
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Class ConstantContact_Admin_Pages
 */
class ConstantContact_Admin_Pages {

	/**
	 * Parent plugin class
	 *
	 * @var   class
	 * @since 0.0.1
	 */
	protected $plugin = null;

	/**
	 * Constructor
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'styles' ) );
	}

	/**
	 * Global admin style enqueue stuff
	 *
	 * @since  1.0.0
	 */
	public function styles() {

		// Register our admin form styles
		wp_register_style(
			'constant-contact-forms',
			constant_contact()->url() . 'assets/css/admin-forms.css',
			array(),
			constant_contact()->version
		);

		// Register our admin pages styles
		wp_register_style(
			'constant_contact_admin_pages',
			constant_contact()->url() . 'assets/css/admin-pages.css',
			array(),
			constant_contact()->version
		);
	}

	/**
	 * Gets the help text for help page
	 *
	 * @since  1.0.0
	 * @return array array of all the help text
	 */
	public function get_help_texts() {

		// Grab our help text. This can be filtered elsewhere, as well.
		return apply_filters( 'constant_contact_help_texts', array(
			array(
				'title' => __( 'This is a sample help header', 'constantcontact' ),
				'content' => __( 'This is some sample help text.', 'constantcontact' ),
			),
			array(
				'title' => __( 'This is another sample header', 'constantcontact' ),
				'content' => __( 'This is also some sample help text.', 'constantcontact' ),
			),
		) );
	}

	/**
	 * Get faq text for help page
	 *
	 * @since  1.0.0
	 * @return array array of all the text
	 */
	public function get_faq_texts() {

		// Get our FAQ texts. This can be filtered
		return apply_filters( 'constant_contact_faq_texts', array(
			array(
				'title' => __( 'Is this a sample question?', 'constantcontact' ),
				'content' => __( 'This is a sample answer', 'constantcontact' ),
			),
			array(
				'title' => __( 'This is also a sample question', 'constantcontact' ),
				'content' => __( 'This is another sample answer', 'constantcontact' ),
			),
		) );
	}

	/**
	 * Display our help page
	 *
	 * @since  1.0.0
	 */
	public function help_page() {

		// Enqueue our JS and styles
		wp_enqueue_script( 'ctct_form' );
		wp_enqueue_style( 'constant_contact_admin_pages' );

		?>
		<h1>
			<?php esc_attr_e( 'Help / FAQ', 'constantcontact' ); ?>
		</h1>
		<div class="ctct-wrap wrap">
			<table id="ctct-support" class="ctct-form-table">
			<tr>
				<td class="outer outer-first">
					<h2>
						<?php esc_html_e( 'Help', 'constantcontact' ); ?>
					</h2>
					<ol id="help_ctct">
					<?php
					// Grab our FAQs
					$helps = $this->get_help_texts();

					// Make sure we have some
					if ( is_array( $helps ) ) {

						// Loop through each$help
						foreach ( $helps as $help ) {

							// Make sure we have the right data
							if ( ! isset( $help['title'] ) || ! isset( $help['content'] ) ) {
								continue;
							}
							?>
							<li>
								<span class="question" aria-controls="q1" aria-expanded="false">
									<?php echo esc_html( $help['title'] ); ?>
								</span>
								<div class="answer">
									<?php echo esc_html( $help['content'] ); ?>
								</div>
							</li>
							<?php
						}
					}
					?>
					</ol>
				</td>
				<td class="outter">
					<h2>
						<?php esc_html_e( 'FAQ', 'constantcontact' ); ?>
					</h2>
					<ol id="faq_ctct">
					<?php
					// Grab our FAQs
					$faqs = $this->get_faq_texts();

					// Make sure we have some
					if ( is_array( $faqs ) ) {

						// Loop through each faq
						foreach ( $faqs as $faq ) {

							// Make sure we have the right data
							if ( ! isset( $faq['title'] ) || ! isset( $faq['content'] ) ) {
								continue;
							}
						?>
						<li>
							<span class="question" aria-controls="q1" aria-expanded="false">
								<?php echo esc_html( $faq['title'] ); ?>
							</span>
							<div class="answer">
								<?php echo esc_html( $faq['content'] ); ?>
							</div>
						</li>
						<?php
						}
					}
					?>
					</ol>
				</td>
			</tr>
			</table>
					<?php
					// Lets you add 'ctct-debug-server-check' to the query args of the help
					// page to load a server requirements check
					// http://mysite.com/wp-admin/edit.php?post_type=ctct_forms&page=ctct_options_help&ctct-debug-server-check
					if ( isset( $_GET['ctct-debug-server-check'] ) ) {
						?>
						<hr>
						<div class="ctct-server-requirements">
							<p>
								<h4><?php esc_attr_e( 'Server Check', 'constantcontact' ); ?></h4>
								<?php
									constant_contact()->check->display_server_checks();
								?>
							</p>
						</div>
						<?php
					}
		?>
		</div>
		<?php
	}

	/**
	 * Display our about page
	 *
	 * @since  1.0.0
	 */
	public function about_page() {

		// make it so pretty
		wp_enqueue_style( 'constant_contact_admin_pages' );

		?>
		<div class="wrap about-wrap constant-contact-about">
			<div class="hide-overflow">
				<div class="left-side">
					<h1 class="about-header"><?php esc_attr_e( 'Constant Contact for WordPress', 'constantcontact' ); ?></h1>
					<div class="about-text">
						<p>
							<?php esc_attr_e( 'Want to connect with visitors even after they’ve left your WordPress site? Constant Contact’s email marketing tools make it easy. And with an average of $44 back for every dollar spent, nothing beats email marketing for driving real business results. With the Constant Contact for WordPress plugin and an active Constant Contact account, you can easily add forms (sign-up, comment, opt-ins) to your site so every visitor can be easily added to your Constant Contact email list.', 'constantcontact' ); ?>
						</p>
						<a href="https://www.constantcontact.com/" target="_blank" class="button button-orange" title="Try Us Free">Try Us Free</a>
					</div>
				</div>
				<span class="plugin-badge">
					<img src="<?php echo $this->plugin->url . 'assets/images/icon.jpg'; ?>">
				</span>
				<div class="clear"></div>
				<hr>
				<?php
					// Get our middleware link
					$proof = constant_contact()->authserver->set_verification_option();
					$auth_link = constant_contact()->authserver->do_connect_url( $proof );
				?>
				<?php if ( $auth_link ) { // If we have a link, then display the connect button ?>
					<h2><?php esc_attr_e( 'Already a Constant Contact Member?', 'constantcontact' ); ?></h2>
					<a href="<?php echo $auth_link ?>" class="button button-blue ctct-connect">
						<?php esc_html_e( 'Connect Plugin', 'constantcontact' ); ?>
					</a>
				<?php } ?>
			</div>
			<div class="headline-feature">
				<h3></h3>
				<div class="featured-image">
					<img src="<?php echo $this->plugin->url . 'assets/images/coffee-hero.jpg'; ?>">
					<p class="featured-title c-text">
						<?php esc_attr_e( 'Powerful Email Marketing, Made Simple.', 'constantcontact' ); ?>
					</p>
					<p class="featured-introduction c-text">
						<?php esc_attr_e( 'Create professional emails that bring customers to your door', 'constantcontact' ) ?>
					</p>
				</div>
				<p class="introduction c-text">
					<?php echo wp_kses_post( __( 'Email marketing is good for your business.  $44-back-for-every-$1-spent kind of good.*  And with the Constant Contact for WordPress plugin, you can easily add sign-up forms to your site so you can stay connected with visitors long after they’ve left. <h5>*Direct Marketing Association 2013 Statistical Fact Book</h5>', 'constantcontact' ) ); ?>
				</p>
				<div class="clear"></div>
			</div>
				<hr>

			<div class="advert-block">
				<div class="left">
					<div class="ad-1">
						<h3><?php esc_html_e( 'Easily Add Forms', 'constantcontact' ); ?></h3>
						<img
							src="<?php echo $this->plugin->url . 'assets/images/add-forms.png'; ?>"
							alt="<?php echo esc_attr_x( 'add forms screenshot', 'add forms alt text', 'constantcontact' ); ?>"
						/>
						<p>
							<?php esc_html_e( 'Create forms that automatically select the theme and styling of your WordPress site for a perfect match. ', 'constantcontact' ); ?>
						</p>
					</div>
				</div>
				<div class="right">
					<div class="ad-2">
						<h3><?php esc_html_e( 'Stay Connected With Your WordPress Visitors', 'constantcontact' ); ?></h3>
						<img
							src="<?php echo $this->plugin->url . 'assets/images/stay-connected.png'; ?>"
							alt="<?php echo esc_attr_x( 'stay connected screenshot', 'stay connected alt text', 'constantcontact' ); ?>"
						/>
						<p>
							<?php esc_html_e( 'Form completions from site visitors are conveniently added to your Constant Contact email list.', 'constantcontact' ); ?>
						</p>
					</div>
				</div>
			</div>
		</div>

		<?php
	}
}


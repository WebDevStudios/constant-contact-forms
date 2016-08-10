<?php
/**
 * @package ConstantContact
 * @subpackage AdminPages
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * Powers admin pages and activation message.
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

		// Add our styles to the site
		add_action( 'admin_init', array( $this, 'styles' ) );

		// Add activation message
		add_action( 'admin_notices', array( $this, 'maybe_show_activation_message' ) );

	}

	/**
	 * Depending on if we should or shouldn't show our activation message, queue it up
	 *
	 * @since   1.0.0
	 */
	public function maybe_show_activation_message() {

		// If we have our query args where we're attempting to dismiss the notice
		if ( $this->should_message_be_dismissed_and_saved() ) {
			// Then save that we dismissed it
			$this->save_dismissed_activation_message();
		}

		// Only show if not connected & it wasn't dismissed
		if ( ! $this->was_activation_message_dismissed() && ! constant_contact()->api->is_connected() ) {
			$this->activation_message();
		}
	}

	/**
	 * Checks our query args and nonce to make sure we should save the dismissal of the notice
	 *
	 * @since   1.0.0
	 * @return  boolean  should we dismiss and save?
	 */
	public function should_message_be_dismissed_and_saved() {

		// If we don't have our nonce action, bail
		if ( ! isset( $_GET['ctct-dismiss'] ) ) { // Input var okay.
			return false;
		}

		// if we don't have our dismiss query arg, bail
		if ( ! isset( $_GET['ctct-activation-action'] ) ) { // Input var okay.
			return false;
		}

		// If we don't have an action set for our dismiss action, bail
		if ( ! sanitize_text_field( wp_unslash( $_GET['ctct-activation-action'] ) ) ) { // Input var okay.
			return false;
		}

		// If our nonce fails, then bail
		if ( ! ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['ctct-dismiss'] ) ), 'ctct-user-is-dismissing' ) ) ) { // Input var okay.
			return false;
		}

		return true;
	}

	/**
	 * Was our activation message dismissed
	 *
	 * @since   1.0.0
	 * @return  boolean
	 */
	public function was_activation_message_dismissed() {
		return get_option( 'ctct_notices_dismissed' );
	}

	/**
	 * Save the fact that the user dismissed our message, and don't show again
	 *
	 * @since   1.0.0
	 * @return  boolean  if we updated correctly
	 */
	public function save_dismissed_activation_message() {
		return update_option( 'ctct_notices_dismissed', true, true );
	}

	/**
	 * Displays a fancy activation message to the user
	 *
	 * @since   1.0.0
	 */
	public function activation_message() {

		wp_enqueue_style(
			'constant-contact-admin-notices',
			constant_contact()->url() . 'assets/css/admin-notices.css',
			array(),
			constant_contact()->version
		);

		?>
		<div id="ctct-activated-admin-notice" class="ctct-activated-admin-notice updated notice">
				<p class="ctct-activated-intro">
				<?php
					printf(
						esc_attr__( 'To take full advatage of the %s plugin, we recommend having an active Constant Contact account or an active free trial with Constant Contact.', 'constantcontact' ),
						'<strong>' . esc_attr__( 'Constant Contact Forms' ) . '</strong>'
					);
				?>
				</p>
				<p>
					<a href="<?php echo esc_url_raw( constant_contact()->api->get_connect_link() ); ?>" target="_blank" class="ctct-activated-button button-primary"><?php esc_attr_e( 'Connect your account', 'constantcontact' ); ?></a>
					<a href="https://www.constantcontact.com/" target="_blank" class="ctct-activated-button button-secondary"><?php esc_attr_e( 'Try Us Free', 'constantcontact' ); ?></a>

					<sub  class='ctct-activated-dismiss'>
						<em>
							<a href="<?php echo esc_url_raw( $this->get_activation_dismiss_url() ); ?>">
								<?php esc_attr_e( 'Dismiss this notice.', 'constantcontact' ); ?>
							</a>
						</em>
					</sub>
				</p>
			</p>
		</div>

		<?php
	}

	/**
	 * Helper method to get our dimiss activation message url
	 *
	 * @since   1.0.0
	 * @return  string  url to dismiss prompt
	 */
	public function get_activation_dismiss_url() {

		// Set a link with our current url and desired action
		$link = add_query_arg( array( 'ctct-activation-action' => 'dismiss' ) );

		// Also nonce it and return it
		return wp_nonce_url( $link, 'ctct-user-is-dismissing', 'ctct-dismiss' );
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
			</table
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
					<h1 class="about-header"><?php esc_attr_e( 'Constant Contact Forms', 'constantcontact' ); ?></h1>
					<div class="about-text">
						<p>
						<?php echo wp_kses_post( __( 'The <strong>Constant Contact Forms</strong> plugin makes it fast and easy to capture all kinds of visitor information right from your WordPress site—even if you don’t have a Constant Contact account.', 'constantcontact' ) ); ?>
						</p>
						<p>
						<?php esc_attr_e( 'Whether you’re looking to collect email addresses, contact info, event sign-ups, or visitor feedback, you can customize your forms with data fields that work best for you. You can:', 'constantcontact' ); ?>
						<ul class="ctct-bonus-points">
							<li> <?php esc_attr_e( 'Quickly create different types of forms that are clear, simple, and mobile-optimized.', 'constantcontact' ); ?></li>
							<li> <?php esc_attr_e( 'Choose forms that automatically select the theme and style of your WordPress site.', 'constantcontact' ); ?></li>
							<li> <?php esc_attr_e( 'Customize the form data fields, so you can tailor the type of information you collect', 'constantcontact' ); ?></li>
						</ul>
						</p>
						<p>
						<?php esc_attr_e( 'Using your sign-up forms to collect email addresses? Email marketing is a great way to stay connected with visitors after they’ve left your site. And with an active Constant Contact account, every new subscriber you capture will be automatically added to your selected email lists.  ', 'constantcontact' ); ?>
						</p>
						<a href="https://www.constantcontact.com/" target="_blank" class="button button-orange" title="Try Us Free">Try Us Free</a>
					</div>
				</div>
				<span class="plugin-badge">
					<img src="<?php echo esc_url_raw( $this->plugin->url . 'assets/images/icon.jpg' ); ?>">
				</span>
				<div class="clear"></div>
				<hr>
				<?php
					// Get our connected state
					$connected = constant_contact()->api->is_connected();

					// @codingStandardsIgnoreStart
					if ( ! $connected ) {
					// @codingStandardsIgnoreEnd

						// Get our middleware link
						$proof = constant_contact()->authserver->set_verification_option();
						$auth_link = constant_contact()->authserver->do_connect_url( $proof );

					// @codingStandardsIgnoreStart
					}
					// @codingStandardsIgnoreEnd

				?>
				<?php if ( ! $connected && $auth_link ) { // If we have a link, then display the connect button ?>
					<h2><?php esc_attr_e( 'Already a Constant Contact account?', 'constantcontact' ); ?></h2>
					<a href="<?php echo esc_url_raw( $auth_link ); ?>" class="button button-blue ctct-connect">
						<?php esc_html_e( 'Connect the plugin', 'constantcontact' ); ?>
					</a>
				<?php } ?>
			</div>
			<div class="headline-feature">
				<h3></h3>
				<div class="featured-image">
					<img src="<?php echo esc_url_raw( $this->plugin->url . 'assets/images/coffee-hero.jpg' ); ?>">
					<p class="featured-title c-text">
						<?php esc_attr_e( 'Powerful Email Marketing, Made Simple.', 'constantcontact' ); ?>
					</p>
					<p class="featured-introduction c-text">
						<?php esc_attr_e( 'Create professional emails that bring customers to your door', 'constantcontact' ) ?>
					</p>
				</div>
				<p class="introduction c-text">
				<?php esc_attr_e( 'Email marketing is good for your business.  $44-back-for-every-$1-spent kind of good.*  And with the Constant Contact Forms plugin, you can easily add sign-up forms to your site so you can stay connected with visitors long after they’ve left.', 'constantcontact' ); ?>
					<h5>
						<?php esc_attr_e( '*Direct Marketing Association 2013 Statistical Fact Book', 'constantcontact' ); ?>
					</h5>
				</p>
				<div class="clear"></div>
			</div>
			<hr>
			<div class="cc-a-block">
				<div class="left">
					<div class="ad-1">
						<h3><?php esc_html_e( 'Easily Add Forms', 'constantcontact' ); ?></h3>
						<img
							src="<?php echo esc_url_raw( $this->plugin->url . 'assets/images/add-forms.png' ); ?>"
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
							src="<?php echo esc_url_raw( $this->plugin->url . 'assets/images/stay-connected.png' ); ?>"
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


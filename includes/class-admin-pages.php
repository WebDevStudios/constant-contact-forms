<?php
/**
 * Admin Pages.
 *
 * @package ConstantContact
 * @subpackage AdminPages
 * @author Constant Contact
 * @since 1.0.1
 */

/**
 * Powers admin pages and activation message.
 */
class ConstantContact_Admin_Pages {

	/**
	 * Parent plugin class.
	 *
	 * @var object
	 * @since 0.0.1
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @param object $plugin Plugin parent.
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

		// Add our styles to the site.
		add_action( 'admin_init', array( $this, 'styles' ) );
	}

	/**
	 * Global admin style enqueue stuff
	 *
	 * @since 1.0.0
	 */
	public function styles() {

		wp_register_style(
			'constant-contact-forms',
			constant_contact()->url() . 'assets/css/admin-forms.css',
			array(),
			constant_contact()->version
		);

		wp_register_style(
			'constant_contact_admin_pages',
			constant_contact()->url() . 'assets/css/admin-pages.css',
			array(),
			constant_contact()->version
		);
	}

	/**
	 * Gets the help text for help page.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of all the help text.
	 */
	public function get_help_texts() {

		/**
		 * Filters our default help texts.
		 *
		 * @since 1.0.0
		 *
		 * @param array $value Array of arrays with title/content values
		 */
		return apply_filters( 'constant_contact_help_texts', array(
			array(
				'title' => __( 'This is a sample help header', 'constant-contact-forms' ),
				'content' => __( 'This is some sample help text.', 'constant-contact-forms' ),
			),
			array(
				'title' => __( 'This is another sample header', 'constant-contact-forms' ),
				'content' => __( 'This is also some sample help text.', 'constant-contact-forms' ),
			),
		) );
	}

	/**
	 * Get faq text for help page.
	 *
	 * @since  1.0.0
	 * @return array Array of all the text.
	 */
	public function get_faq_texts() {

		/**
		 * Filters our FAQ text for the help page.
		 *
		 * @since 1.0.0
		 *
		 * @param array $value Array of arrays for help text.
		 */
		return apply_filters( 'constant_contact_faq_texts', array(
			array(
				'title' => __( 'Is this a sample question?', 'constant-contact-forms' ),
				'content' => __( 'This is a sample answer', 'constant-contact-forms' ),
			),
			array(
				'title' => __( 'This is also a sample question', 'constant-contact-forms' ),
				'content' => __( 'This is another sample answer', 'constant-contact-forms' ),
			),
		) );
	}

	/**
	 * Display our help page
	 *
	 * @since  1.0.0
	 */
	public function help_page() {

		wp_enqueue_script( 'ctct_form' );
		wp_enqueue_style( 'constant_contact_admin_pages' );

		?>
		<h1>
			<?php esc_attr_e( 'Help / FAQ', 'constant-contact-forms' ); ?>
		</h1>
		<div class="ctct-wrap wrap">
			<table id="ctct-support" class="ctct-form-table">
			<tr>
				<td class="outer outer-first">
					<h2>
						<?php esc_html_e( 'Help', 'constant-contact-forms' ); ?>
					</h2>
					<ol id="help_ctct">
					<?php
					// Grab our FAQs.
					$helps = $this->get_help_texts();

					// Make sure we have some.
					if ( is_array( $helps ) ) {

						// Loop through each $help.
						foreach ( $helps as $help ) {

							// Make sure we have the right data.
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
						<?php esc_html_e( 'FAQ', 'constant-contact-forms' ); ?>
					</h2>
					<ol id="faq_ctct">
					<?php
					// Grab our FAQs.
					$faqs = $this->get_faq_texts();

					if ( is_array( $faqs ) ) {

						foreach ( $faqs as $faq ) {

							// Make sure we have the right data.
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
	 * Display our about page.
	 *
	 * @since 1.0.0
	 */
	public function about_page() {

		wp_enqueue_style( 'constant_contact_admin_pages' );

		$proof = $auth_link = $new_link = '';

		// @codingStandardsIgnoreStart
		if ( ! constant_contact()->api->is_connected() ) {
		// @codingStandardsIgnoreEnd

			// Get our middleware link.
			$proof     = constant_contact()->authserver->set_verification_option();
			$auth_link = constant_contact()->authserver->do_connect_url( $proof );
			$new_link  = constant_contact()->authserver->do_signup_url( $proof );

		// @codingStandardsIgnoreStart
		}
		// @codingStandardsIgnoreEnd

		?>
		<div class="wrap about-wrap constant-contact-about">
			<div class="hide-overflow">
				<div class="left-side">
					<h1 class="about-header"><?php esc_attr_e( 'Constant Contact Forms', 'constant-contact-forms' ); ?></h1>
					<div class="about-text">
						<p>
						<?php echo wp_kses_post( __( 'This plugin makes it fast and easy to capture all kinds of visitor information right from your WordPress site—even if you don’t have a Constant Contact account.', 'constant-contact-forms' ) ); ?>
						</p>
						<p>
						<?php esc_attr_e( 'Whether you’re looking to collect email addresses, contact info, event sign-ups, or visitor feedback, you can customize your forms with data fields that work best for you.', 'constant-contact-forms' ); ?>
						<ul class="ctct-bonus-points">
							<li> <?php esc_attr_e( 'Quickly create different types of forms that are clear, simple, and mobile-optimized.', 'constant-contact-forms' ); ?></li>
							<li> <?php esc_attr_e( 'Choose forms that automatically select the theme and style of your WordPress site.', 'constant-contact-forms' ); ?></li>
							<li> <?php esc_attr_e( 'Customize the form data fields, so you can tailor the type of information you collect.', 'constant-contact-forms' ); ?></li>
						</ul>
						</p>
						<p>
						<?php esc_attr_e( 'Using your sign-up forms to collect email addresses? Email marketing is a great way to stay connected with visitors after they’ve left your site. And with an active Constant Contact account, every new subscriber you capture will be automatically added to your selected email lists.  ', 'constant-contact-forms' ); ?>
						</p>
						<?php if ( $new_link ) { // If we have a link, then display the connect button. ?>
						<a href="<?php echo esc_url_raw( $new_link ); ?>" target="_blank" class="button button-orange" title="<?php esc_attr_e( 'Try us Free', 'constant-contact-forms' ); ?>"><?php esc_attr_e( 'Try us Free', 'constant-contact-forms' ); ?></a>
						<?php } ?>
					</div>
				</div>
				<span class="plugin-badge">
					<img src="<?php echo esc_url_raw( $this->plugin->url . 'assets/images/icon.jpg' ); ?>">
				</span>
				<div class="clear"></div>
				<hr>
				<?php if ( $auth_link ) { // If we have a link, then display the connect button. ?>
					<h2><?php esc_attr_e( 'Already have a Constant Contact account?', 'constant-contact-forms' ); ?></h2>
					<a href="<?php echo esc_url_raw( $auth_link ); ?>" class="button button-blue ctct-connect">
						<?php esc_html_e( 'Connect the plugin', 'constant-contact-forms' ); ?>
					</a>
				<?php } ?>
			</div>
			<div class="headline-feature">
				<h3></h3>
				<div class="featured-image">
					<img src="<?php echo esc_url_raw( $this->plugin->url . 'assets/images/coffee-hero.jpg' ); ?>">
					<p class="featured-title c-text">
						<?php esc_attr_e( 'Powerful Email Marketing, Made Simple.', 'constant-contact-forms' ); ?>
					</p>
					<p class="featured-introduction c-text">
						<?php esc_attr_e( 'Create professional emails that bring customers to your door', 'constant-contact-forms' ) ?>
					</p>
				</div>
				<p class="introduction c-text">
				<?php esc_attr_e( 'Email marketing is good for your business.  $44-back-for-every-$1-spent kind of good.*  And with the Constant Contact Forms plugin, you can easily add sign-up forms to your site so you can stay connected with visitors long after they’ve left.', 'constant-contact-forms' ); ?>
				</p>
				<?php
				// Include our license link if we have it.
				if ( $license_link = $this->plugin->admin->get_admin_link( __( 'GPLv3 license', 'constant-contact-forms' ), 'license' ) ) {  ?>
					<p class="c-text">
					<?php
					echo wp_kses_post( sprintf( __( 'This software is released under a modified %s.', 'constant-contact-forms' ), $license_link ) );
					?>
				</p>
				<?php
				}
				?>
					<h5>
						<?php esc_attr_e( '*Direct Marketing Association 2013 Statistical Fact Book', 'constant-contact-forms' ); ?>
					</h5>
				</p>
				<div class="clear"></div>
			</div>
			<hr>
			<div class="cc-a-block">
				<div class="left">
					<div class="ad-1">
						<h3><?php esc_html_e( 'Easily Add Forms', 'constant-contact-forms' ); ?></h3>
						<img
							src="<?php echo esc_url_raw( $this->plugin->url . 'assets/images/add-forms.png' ); ?>"
							alt="<?php echo esc_attr_x( 'add forms screenshot', 'add forms alt text', 'constant-contact-forms' ); ?>"
						/>
						<p>
							<?php esc_html_e( 'Create forms that automatically select the theme and styling of your WordPress site for a perfect match. ', 'constant-contact-forms' ); ?>
						</p>
					</div>
				</div>
				<div class="right">
					<div class="ad-2">
						<h3><?php esc_html_e( 'Stay Connected With Your WordPress Visitors', 'constant-contact-forms' ); ?></h3>
						<img
							src="<?php echo esc_url_raw( $this->plugin->url . 'assets/images/stay-connected.png' ); ?>"
							alt="<?php echo esc_attr_x( 'stay connected screenshot', 'stay connected alt text', 'constant-contact-forms' ); ?>"
						/>
						<p>
							<?php esc_html_e( 'Form completions from site visitors are conveniently added to your Constant Contact email list.', 'constant-contact-forms' ); ?>
						</p>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Display our license page.
	 *
	 * @since  1.0.1
	 */
	public function license_page() {
		$license_text = $this->plugin->get_license_text();
		?>
		<div class="wrap license-wrap constant-contact-license">
			<div class="hide-overflow">
				<div class="left-side">
					<h1 class="license-header"><?php esc_attr_e( 'Constant Contact Forms - License', 'constant-contact-forms' ); ?></h1>
					<div class="license-text">
					<pre><?php echo $license_text; ?></pre>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}


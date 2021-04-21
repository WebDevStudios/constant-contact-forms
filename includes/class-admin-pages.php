<?php
/**
 * Admin Pages.
 *
 * @package ConstantContact
 * @subpackage AdminPages
 * @author Constant Contact
 * @since 1.0.1
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Powers admin pages and activation message.
 *
 * @since 1.0.1
 */
class ConstantContact_Admin_Pages {

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
	 */
	public function hooks() {
		add_action( 'admin_enqueue_scripts', [ $this, 'styles' ] );
	}

	/**
	 * Global admin style enqueue stuff.
	 *
	 * @since 1.0.0
	 */
	public function styles() {
		wp_enqueue_style( 'constant-contact-forms-admin' );
		wp_enqueue_script( 'ctct_form' );
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
		 * @param array $value Array of arrays with title/content values.
		 */
		return apply_filters( 'constant_contact_help_texts', [
			[
				'title'   => esc_html__( 'This is a sample help header', 'constant-contact-forms' ),
				'content' => esc_html__( 'This is some sample help text.', 'constant-contact-forms' ),
			],
			[
				'title'   => esc_html__( 'This is another sample header', 'constant-contact-forms' ),
				'content' => esc_html__( 'This is also some sample help text.', 'constant-contact-forms' ),
			],
		] );
	}

	/**
	 * Get faq text for help page.
	 *
	 * @since 1.0.0
	 *
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
		return apply_filters( 'constant_contact_faq_texts', [
			[
				'title'   => esc_html__( 'Is this a sample question?', 'constant-contact-forms' ),
				'content' => esc_html__( 'This is a sample answer', 'constant-contact-forms' ),
			],
			[
				'title'   => esc_html__( 'This is also a sample question', 'constant-contact-forms' ),
				'content' => esc_html__( 'This is another sample answer', 'constant-contact-forms' ),
			],
		] );
	}

	/**
	 * Display our help page.
	 *
	 * @since 1.0.0
	 */
	public function help_page() {
		?>
		<h2>
			<?php esc_attr_e( 'Help / FAQ', 'constant-contact-forms' ); ?>
		</h2>
		<div class="ctct-wrap wrap">
			<table id="ctct-support" class="ctct-form-table">
				<tr>
					<td class="outer outer-first">
						<h2>
							<?php esc_html_e( 'Help', 'constant-contact-forms' ); ?>
						</h2>
						<ol id="help_ctct">
						<?php
						$helps = $this->get_help_texts();

						if ( is_array( $helps ) ) {

							foreach ( $helps as $help ) {
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
						$faqs = $this->get_faq_texts();

						if ( is_array( $faqs ) ) {

							foreach ( $faqs as $faq ) {
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
		</div>
		<?php
	}

	/**
	 * Display our about page.
	 *
	 * @since 1.0.0
	 */
	public function about_page() {

		$proof = $auth_link = $new_link = '';

		if ( ! constant_contact()->api->is_connected() ) {
			$proof     = constant_contact()->authserver->set_verification_option();
			$auth_link = constant_contact()->authserver->do_connect_url( $proof );
			$new_link  = constant_contact()->authserver->do_signup_url( $proof );

			$new_link  = add_query_arg( [ 'rmc' => 'wp_about_try' ], $new_link );
			$auth_link = add_query_arg( [ 'rmc' => 'wp_about_connect' ], $auth_link );
		}

		?> 

		<h2><?php esc_html_e( 'About Constant Contact Forms', 'constant-contact-forms' ); ?></h2>

		<div class="constant-contact-about">

			<div class="ctct-section section-about">
				<p class="large-text">
					<?php echo wp_kses_post( __( "This plugin makes it fast and easy to capture all kinds of visitor information right from your WordPress site—even if you don't have a Constant Contact account.", 'constant-contact-forms' ) ); ?>
				</p>
			</div>
			<div class="ctct-section ctct-features">
				<div class="ctct-video-cta">
					<div>
						<div class="iframe-wrap">
							<iframe src="https://www.youtube.com/embed/Qqb0_zcRKnM" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
						</div>
					</div>
					<div>
						<p>
							<?php esc_attr_e( "Whether you're looking to collect email addresses, contact info, or visitor feedback, you can customize your forms with data fields that work best for you.", 'constant-contact-forms' ); ?>
						</p>
						<ul class="ctct-bonus-points">
							<li> <?php esc_attr_e( 'Quickly create different types of forms that are clear, simple, and mobile-optimized.', 'constant-contact-forms' ); ?></li>
							<li> <?php esc_attr_e( 'Choose forms that automatically select the theme and style of your WordPress site.', 'constant-contact-forms' ); ?></li>
							<li> <?php esc_attr_e( 'Customize the form data fields, so you can tailor the type of information you collect.', 'constant-contact-forms' ); ?></li>
						</ul>
					</div>
				</div>
			</div>			

			<?php if ( $new_link || $auth_link ) { ?>

			<div class="ctct-section section-try-us">
				<h2>
					<?php esc_html_e( 'Turn Contacts into Customers.', 'constant-contact-forms' ); ?>
				</h2>
				<p>
					<?php esc_html_e( "Nurture your new contacts with a Constant Contact email marketing account even after they've left your website. Sign up for a 60-day trial account* and you can:", 'constant-contact-forms' ); ?>
				</p>
				<ul class="ctct-bonus-points">
					<li><?php esc_html_e( 'Seamlessly add new contacts to mailing lists.', 'constant-contact-forms' ); ?></li>
					<li><?php esc_html_e( 'Create and send professional emails.', 'constant-contact-forms' ); ?></li>
					<li><?php esc_html_e( 'Get expert marketing help and support.', 'constant-contact-forms' ); ?></li>
				</ul>
				<hr />
				<div class="ctct-try-us-cta">
					<?php if ( $new_link ) { ?>
						<div class="item">
							<a href="<?php echo esc_url_raw( $new_link ); ?>" target="_blank" class="button button-orange" title="<?php esc_attr_e( 'Try us Free', 'constant-contact-forms' ); ?>"><?php esc_attr_e( 'Try us Free', 'constant-contact-forms' ); ?></a>
						</div>
					<?php } ?>
					<?php if ( $auth_link ) { ?>
						<div class="item">
							<p><?php esc_attr_e( 'Already have a Constant Contact account?', 'constant-contact-forms' ); ?>
								<strong>
									<a href="<?php echo esc_url_raw( $auth_link ); ?>" class="ctct-connect">
										<?php esc_html_e( 'Connect the Plugin', 'constant-contact-forms' ); ?>
									</a>
								</strong>
							</p>
						</div>
					<?php } ?>
				</div>
			</div>

			<p class="small-text"><strong><?php esc_html_e( 'NOTE:', 'constant-contact-forms' ); ?></strong> <?php esc_html_e( 'You can use the Constant Contact Form plugin without a Constant Contact account. All information collected by the forms will be individually emailed to your site admin.', 'constant-contact-forms' ); ?></p>

			<?php } else { ?>	

				<div class="ctct-section">
					<div class="ctct-button-actions">
						<a href="wp-admin/post-new.php?post_type=ctct_forms" class="button button-primary"><?php esc_html_e( 'Add Contact Form', 'constant-contact-forms' ); ?></a>
						<a href="wp-admin/edit.php?post_type=ctct_lists" class="button"><?php esc_html_e( 'View Lists', 'constant-contact-forms' ); ?></a>
					</div>
				</div>

			<?php } ?>	
			
			<?php
				$license_link = $this->plugin->admin->get_admin_link( __( 'GPLv3 license', 'constant-contact-forms' ), 'license' );
				if ( $license_link ) :
			?>
				<div class="ctct-license">
					<p class="small-text">
					<?php
						/* Translators: Placholder here is a link to the license. */
						$license_message = sprintf( __( 'This software is released under a modified %s.', 'constant-contact-forms' ), $license_link );
						echo wp_kses_post( $license_message );
					?>
					</p>
				</div>
			<?php endif; ?>

		</div>				
		<?php
	}

	/**
	 * Display our license page.
	 *
	 * @since 1.0.1
	 */
	public function license_page() {
		$license_text = $this->plugin->get_license_text();
		?>
		<div class="wrap license-wrap constant-contact-license">
			<div class="hide-overflow">
				<div class="left-side">
					<h2 class="license-header"><?php esc_attr_e( 'Constant Contact Forms - License', 'constant-contact-forms' ); ?></h2>
					<div class="license-text">
					<pre><?php echo wp_kses_post( $license_text ); ?></pre>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}

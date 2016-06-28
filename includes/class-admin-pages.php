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
	 * @since  1.0.0
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
	 * Gets the help text for help page
	 *
	 * @since  1.0.0
	 * @return array array of all the help text
	 */
	public function get_help_texts() {
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
					<h1 class="about-header"><?php echo esc_attr( $this->get_text( 'welcome_heading' ) ); ?></h1>
					<div class="about-text">
						<?php echo esc_attr( $this->get_text( 'welcome_text' ) ); ?>
					</div>
				</div>
				<div class="plugin-badge">
					<img src="<?php echo esc_url( $this->get_text( 'icon' ) ); ?>">
				</div>
			</div>

			<?php // $this->tab_navigation(); ?>

			<div class="headline-feature">
				<h3 class="headline-title">
					<?php echo esc_html( $this->get_text( 'feat_headline' ) ); ?>
				</h3>

				<div class="featured-image">
					<img src="<?php echo esc_url( $this->get_text( 'feat_image' ) ); ?>">
					<p class="featured-title c-text">
						<?php echo esc_html( $this->get_text( 'feat_title' ) ); ?>
					</p>
					<p class="featured-introduction c-text">
						<?php echo esc_html( $this->get_text( 'feat_sub_title' ) ); ?>
					</p>
				</div>
				<p class="introduction c-text">
					<?php echo esc_html( $this->get_text( 'feat_description' ) ); ?>
				</p>
				<div class="clear"></div>
			</div>
			<hr />
			<div class="features-section">
				<div class="feature-section<?php echo ( count( $this->get_text( 'features' ) ) >= 2 ) ? ' two-col' : ''; ?> ">
					<?php
					// get our features
					$features = $this->get_text( 'features' );

					// make sure its an array
					if ( is_array( $features ) ) {

						// loop through those features
						foreach ( $features as $feature ) {

							// Make sure we have the right though
							if (
								! isset( $feature['title'] ) ||
								! isset( $feature['description'] )
							) {
								continue;
							}
						?>
						<div>
							<h3 class="feature-title c-text">
								<?php echo esc_html( $feature['title'] ); ?>
							</h3>
							<?php if ( ! empty( $feature['image'] ) ) { ?>
							<div class="feature-image">
								<?php
								$alt = isset( $feature['alt'] ) ? $feature['alt'] : '';
								?>
								<img class="fff-img" src="<?php echo esc_url( $feature['image'] );?>" alt="<?php echo esc_attr( $alt ); ?>">
							</div>
							<?php } ?>
							<p class="c-text">
								<?php echo esc_html( $feature['description'] ); ?>
							</p>
						</div>
						<?php
						}
					}
				?>
				</div>
			</div>
		</div>

		<?php
	}

	/**
	 * Holds all the data for the about page
	 *
	 * @since  1.0.0
	 * @param  string $key           array key of text to retrieve
	 * @param  text   $secondary_key   optional second key for nested array
	 * @return mixed                If no params are passed in, all data returned.
	                                passing one key will return that data if it exists,
	                                either as a string or array. The secondary key will grab a nested
	                                array data point.
	 */
	public function get_text( $key = null, $secondary_key = null ) {

		$about_page_data = apply_filters( 'constant_contact_about_page_date', array(

				'plugin'  => constant_contact()->plugin_name,
				'version' => constant_contact()->version,
				'icon'    => constant_contact()->url . 'assets/images/icon.jpg',

				'welcome_heading'  => __( 'Welcome to Constant Contact', 'constantcontact' ),
				'welcome_text'     => __( 'Powerful Email Marketing, Made Simple. For every dollar spent on email marketing, small businesses make an average of $40 back. * Stats like that make email marketing a must for small businesses. And with Constant Contact, you also get the free award-winning coaching, and resources to see results like that, faster.', 'constantcontact' ),
				'feat_headline'    => __( '', 'constantcontact' ),
				'feat_image'       => constant_contact()->url . 'assets/images/coffee-hero.jpg',
				'feat_title'       => __( 'Powerful Email Marketing, Made Simple.', 'constantcontact' ),
				'feat_sub_title'   => __( 'Create professional emails that bring customers to your door', 'constantcontact' ),
				'feat_description' => __( 'Email marketing is good for your business. $44-back-for-every-$1-spent kind of good.* And with a tool as powerful and easy to use as Constant Contact, you don’t need to be a marketing expert to see results. *Direct Marketing Association 2013 Statistical Fact Book', 'constantcontact' ),

				'features' => array(
					array(
						'title'       => __( 'Easy Contact management.', 'constantcontact' ),
						'image'       => constant_contact()->url . 'assets/images/contacts.png',
						'alt'         => __( 'Reporting management screen', 'constantcontact' ),
						'description' => __( 'Create custom forms and add users to contact lists.', 'constantcontact' ),
					),
					array(
						'title'       => __( 'Track Your Success', 'constantcontact' ),
						'image'       => constant_contact()->url . 'assets/images/reporting.png',
						'alt'         => __( 'Graph of increasing bars', 'constantcontact' ),
						'description' => __( "Our email tracking tools tell you who's opening, clicking, and sharing your emails and social posts in real time.", 'constantcontact' ),
					),
				),
		) );

		// If we didn't pass a key, return all the data
		if ( ! $key ) {
			return $about_page_data;
		}

		// if we passed a key, check to see if we can return that key itself
		if ( isset( $about_page_data[ $key ] ) ) {

			// if we also have a secondary key, check for that
			if ( $secondary_key ) {
				if ( isset( $about_page_data[ $key ][ $secondary_key ] ) ) {
					return $about_page_data[ $key ][ $secondary_key ];
				}
			}
			return $about_page_data[ $key ];
		}

		return '';

	}

	/**
	 * Output our tab navigation.
	 *
	 * @internal
	 * @since 1.0.0
	 * @param string $tab Active tab.
	 */
	public function tab_navigation( $tab = 'whats_new' ) {
	?>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php if ( 'ConstantContact_Admin_About::about_screen' === $tab ) : ?>nav-tab-active<?php endif; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'cptui_about' ), 'index.php' ) ) ); ?>">
				<?php esc_html_e( 'What&#8217;s New', 'constantcontact' ); ?>
			</a>
		</h2>

	<?php
	}
}


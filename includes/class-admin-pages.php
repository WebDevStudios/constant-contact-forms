<?php
/**
 * ConstantContact_Admin_Pages class
 *
 * @package ConstantContactProcessForm
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
	 */
	public function styles() {
		wp_register_style(
			'constant-contact-forms',
			constant_contact()->url() . 'assets/css/forms.css',
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
	 * Parse multidemntional args
	 *
	 * Borrowed from: http://mekshq.com/recursive-wp-parse-args-wordpress-function/
	 *
	 * @internal
	 * @since 1.0.0
	 * @param  array $args args to parse.
	 * @param  array $defaults default array.
	 * @return array the parsed array
	 */
	public function parse_multidimensional_array_args( &$args, $defaults ) {

		$args = (array) $args;
		$result = $defaults = (array) $defaults;

		foreach ( $args as $key => &$value ) {
			if ( is_array( $value ) && isset( $result[ $key ] ) ) {
				$result[ $key ] = $this->parse_multidimensional_array_args( $value, $result[ $key ] );
			} else {
				$result[ $key ] = $value;
			}
		}
		return $result;
	}

	/**
	 * Gets the help text for help page
	 *
	 * @author Brad Parbs
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
	 * @author Brad Parbs
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
	 */
	public function help_page() {

		// Enqueue our JS and styles
		wp_enqueue_script( 'ctct_form' );
		wp_enqueue_style( 'constant_contact_admin_pages' );

		// constantcontact_admin()->page_tabs();
		?>
		<h1>
			<?php esc_attr_e( 'Help / FAQ', 'constantcontact' ); ?>
		</h1>
		<div class="wrap">
			<table id="ctct-support" class="form-table">
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
						<?php esc_html_e( 'Faq', 'constantcontact' ); ?>
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
		</div><?php
	}

	/**
	 * Display our about page
	 *
	 */
	public function about_page() {
		echo 'yo about';
	}
}


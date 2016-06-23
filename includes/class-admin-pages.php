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
	 * Display our help page
	 *
	 */
	public function help_page() {

		wp_enqueue_script( 'ctct_form' );
		wp_enqueue_style( 'constant_contact_admin_pages' );

		// constantcontact_admin()->page_tabs();

		$helps = apply_filters( 'constant_contact_help_texts', array(
			array(
				'title' => __( 'Help', 'constantcontact' ),
				'content' => __( 'plugin is a of plugin by WebDevStudios', 'constantcontact' ),
			),
			array(
				'title' => __( 'Help 2', 'constantcontact' ),
				'content' => __( 'plugin is a of plugin by WebDevStudios', 'constantcontact' ),
			),
		) );

		$faqs = apply_filters( 'constant_contact_faq_texts', array(
			array(
				'title' => __( 'FAQ', 'constantcontact' ),
				'content' => __( 'plugin is a of plugin by WebDevStudios', 'constantcontact' ),
			),
			array(
				'title' => __( 'FAQ 2', 'constantcontact' ),
				'content' => __( 'plugin is a of plugin by WebDevStudios', 'constantcontact' ),
			),
		) );
		?>
		<h2><?php esc_attr_e( 'Help / FAQ', 'constantcontact' ); ?></h2>
		<div class="wrap">
			<table id="ctct-support" class="form-table cptui-table">
			<tr>
				<td class="outter" width="50%">
					<h2><?php esc_html_e( 'Help', 'constantcontact' ); ?></h2>
					<ol id="help_ctct">
					<?php foreach ( $helps as $help ) : ?>
						<li>
							<span tabindex="0" class="question" aria-controls="q1" aria-expanded="false"><?php echo esc_html( isset( $help['title'] ) ? $help['title'] : '' ); ?></span>
							<div class="answer"><?php echo esc_html( isset( $help['content'] ) ? $help['content'] : '' ); ?></div>
						</li>
					<?php endforeach; ?>
					</ol>
				</td>
				<td class="outter">
					<h2><?php esc_html_e( 'Faq', 'constantcontact' ); ?></h2>
					<ol id="faq_ctct">
					<?php foreach ( $faqs as $faq ) : ?>
						<li>
							<span tabindex="0" class="question" aria-controls="q1" aria-expanded="false"><?php echo esc_html( isset( $faq['title'] ) ? $faq['title'] : '' ); ?></span>
							<div class="answer"><?php echo esc_html( isset( $faq['content'] ) ? $faq['content'] : '' ); ?></div>
						</li>
					<?php endforeach; ?>
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


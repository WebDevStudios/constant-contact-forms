<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName -- Class name okay, PSR-4.
/**
 * Constant Contact Settings class.
 *
 * @package ConstantContact
 * @subpackage Settings
 * @author Constant Contact
 * @since 1.0.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Powers our settings and options page, as well as injecting our optins to the front-end.
 *
 * @since 1.0.0
 */
class ConstantContact_Settings {

	/**
	 * Option key, and option page slug.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	public string $key = 'ctct_options_settings';

	/**
	 * Settings page metabox id.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	private string $metabox_id = 'ctct_option_metabox_settings';

	/**
	 * Settings page metabox titles by id.
	 *
	 * @since 1.8.0
	 * @var   array|null
	 */
	private array $metabox_titles;

	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	protected object $plugin;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $plugin Parent plugin instance.
	 */
	public function __construct( object $plugin ) {
		$this->plugin = $plugin;

		$this->register_hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_action( 'cmb2_admin_init', [ $this, 'set_metabox_titles' ] );
		add_action( 'cmb2_admin_init', [ $this, 'add_options_page_metaboxes' ] );
		add_action( 'cmb2_admin_init', [ $this, 'register_metabox_override_hooks' ] );

		add_action( 'admin_menu', [ $this, 'remove_extra_menu_items' ], 999 );
		add_filter( 'parent_file', [ $this, 'select_primary_menu_item' ] );

		$this->inject_optin_form_hooks();

		add_filter( 'preprocess_comment', [ $this, 'process_optin_comment_form' ] );
		add_filter( 'authenticate', [ $this, 'process_optin_login_form' ], 10, 3 );
		add_action( 'user_register', [ $this, 'process_optin_register_form' ], 10, 2 );
		add_action( 'cmb2_save_field__ctct_logging', [ $this, 'maybe_init_logs' ], 10, 3 );
		add_filter( 'constant_contact_custom_spam_message', [ $this, 'get_spam_error_message' ], 10, 2 );
	}

	/**
	 * Set metabox tab titles.
	 *
	 * @since 2.10.0
	 */
	public function set_metabox_titles() {
		// Init CMB2 metabox titles, used as tab titles on settings page.
		$this->metabox_titles = [
			'general' => esc_html__( 'General', 'constant-contact-forms' ),
			'styles'  => esc_html__( 'Styles', 'constant-contact-forms' ),
			'optin'   => esc_html__( 'Opt-in', 'constant-contact-forms' ),
			'spam'    => esc_html__( 'Spam control', 'constant-contact-forms' ),
			'support' => esc_html__( 'Support', 'constant-contact-forms' ),
			'auth'    => esc_html__( 'Account', 'constant-contact-forms' ),
		];
	}

	/**
	 * Add CMB2 hook overrides specific to individual metaboxes.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.8.0
	 *
	 * @return void
	 */
	public function register_metabox_override_hooks() {
		foreach ( array_keys( $this->metabox_titles ) as $cmb_key ) {
			add_filter( "cmb2_override_option_get_{$this->key}_$cmb_key", [ $this, 'get_override' ], 10, 2 );
			add_filter( "cmb2_override_option_save_{$this->key}_$cmb_key", [ $this, 'update_override' ], 10, 2 );
			add_action( "cmb2_save_options-page_fields_{$this->metabox_id}_$cmb_key", [ $this, 'settings_notices' ], 10, 2 );
		}
	}

	/**
	 * Hook in all our form opt-in injects, decide to show or not when we are at the display point.
	 *
	 * @since 1.0.0
	 */
	public function inject_optin_form_hooks() {
		add_action( 'login_form', [ $this, 'optin_form_field_login' ] );
		add_action( 'comment_form', [ $this, 'optin_form_field_comment' ] );

		add_action( 'register_form', [ $this, 'optin_form_field_registration' ] );
		add_action( 'signup_extra_fields', [ $this, 'optin_form_field_registration' ] );
		add_action( 'login_head', [ $this, 'optin_form_field_login_css' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'scripts' ] );
	}

	/**
	 * Add some login page CSS.
	 *
	 * @since 1.2.0
	 */
	public function optin_form_field_login_css() {
		?>
		<style>
		.login .ctct-disclosure {
			margin: 0 0 15px;
		}
		</style>
		<?php
	}

	/**
	 * Enqueue our styles.
	 *
	 * @since 1.0.0
	 */
	public function scripts() {
		wp_enqueue_style( 'constant-contact-forms-admin' );
	}

	/**
	 * Are we on the settings page?
	 *
	 * @since 1.0.0
	 *
	 * @return boolean If we are on the settings page or not.
	 */
	public function on_settings_page() : bool {
		global $pagenow;

		$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_SPECIAL_CHARS );

		return ( 'edit.php' === $pagenow && ! empty( $page ) && $this->key === $page );
	}

	/**
	 * Add the options metaboxes to the array of metaboxes.
	 *
	 * Call corresponding method for each cmb key listed in $metabox_titles.
	 *
	 * @since 1.0.0
	 */
	public function add_options_page_metaboxes() {
		foreach ( array_keys( $this->metabox_titles ) as $cmb_key ) {
			$method = "register_fields_$cmb_key";

			if ( ! method_exists( $this, $method ) ) {
				continue;
			}

			$this->$method();
		}
	}

	/**
	 * Remove secondary settings page menu items.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.8.0
	 */
	public function remove_extra_menu_items() {
		foreach ( array_keys( $this->metabox_titles ) as $cmb_key ) {
			if ( 'general' === $cmb_key ) {
				continue;
			}

			remove_submenu_page( 'edit.php?post_type=ctct_forms', "{$this->key}_$cmb_key" );
		}
	}

	/**
	 * Ensure primary settings page menu item is highlighted.
	 *
	 * Override $plugin_page global to ensure "general" menu item active for other settings pages.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.8.0
	 *
	 * @param  string $file The parent file.
	 * @return string       The parent file.
	 */
	public function select_primary_menu_item( string $file ) : string {
		global $plugin_page;

		if ( null === $plugin_page ) {
			return $file;
		}

		$plugin_page = false !== strpos( $plugin_page, $this->key ) ? "{$this->key}_general" : $plugin_page; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- OK overriding of WP global.

		return $file;
	}

	/**
	 * Display options page with CMB2 tabs.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.8.0
	 *
	 * @param  CMB2_Options_Hookup $cmb_options The CMB2_Options_Hookup object.
	 */
	public function display_tabs( CMB2_Options_Hookup $cmb_options ) {
		$tabs    = $this->get_option_tabs( $cmb_options );
		$current = $this->get_current_tab();
		?>
		<div class="wrap cmb2-options-page ctct-page-wrap option-<?php echo esc_attr( $cmb_options->option_key ); ?>">
			<?php if ( get_admin_page_title() ) : ?>
				<h2><?php echo wp_kses_post( get_admin_page_title() ); ?></h2>
			<?php endif; ?>
			<h2 class="nav-tab-wrapper">
				<?php foreach ( $tabs as $option_key => $tab_title ) : ?>
					<?php $tab_class = $current === $option_key ? ' nav-tab-active' : ''; ?>
					<a class="nav-tab<?php echo esc_attr( $tab_class ); ?>" href="<?php echo esc_url_raw( $this->get_tab_link( $option_key ) ); ?>"><?php echo wp_kses_post( $tab_title ); ?></a>
				<?php endforeach; ?>
			</h2>
			<form class="cmb-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST" id="<?php echo esc_attr( $cmb_options->cmb->cmb_id ); ?>" enctype="multipart/form-data" encoding="multipart/form-data">
				<input type="hidden" name="action" value="<?php echo esc_attr( $cmb_options->option_key ); ?>">
				<?php $cmb_options->options_page_metabox(); ?>
				<?php submit_button( esc_attr( $cmb_options->cmb->prop( 'save_button' ) ), 'primary', 'submit-cmb' ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Get all option tabs for navigation on CMB2 settings page.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.8.0
	 *
	 * @param  CMB2_Options_Hookup $cmb_options The CMB2_Options_Hookup object.
	 * @return array                            Array of option tabs.
	 */
	protected function get_option_tabs( CMB2_Options_Hookup $cmb_options ) : array {
		$tab_group = $cmb_options->cmb->prop( 'tab_group' );
		$tabs      = [];

		foreach ( CMB2_Boxes::get_all() as $cmb_id => $cmb ) {
			if ( $tab_group !== $cmb->prop( 'tab_group' ) ) {
				continue;
			}

			$cmb_key = array_search( $cmb->prop( 'tab_title' ), $this->metabox_titles, true );

			if ( false === $cmb_key ) {
				continue;
			}

			$tab_title                         = $cmb->prop( 'tab_title' );
			$tabs[ "{$this->key}_$cmb_key" ] = empty( $tab_title ) ? $cmb->prop( 'title' ) : $tab_title;
		}

		return $tabs;
	}

	/**
	 * Get currently selected tab.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.8.0
	 *
	 * @return string Current tab.
	 */
	protected function get_current_tab() : string {
		$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_SPECIAL_CHARS );

		return ( empty( $page ) ? "{$this->key}_general" : $page );
	}

	/**
	 * Get link to CMB tab.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.8.0
	 *
	 * @param  string $option_key CMB tab key.
	 * @return string             URL to CMB tab.
	 */
	protected function get_tab_link( string $option_key ) : string {
		return wp_specialchars_decode( menu_page_url( $option_key, false ) );
	}

	/**
	 * Get args for current CMB.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.8.0
	 *
	 * @param  string $cmb_id Current CMB ID.
	 * @return array          CMB args.
	 */
	protected function get_cmb_args( string $cmb_id ) : array {
		return [
			'id'           => "{$this->metabox_id}_$cmb_id",
			'title'        => esc_html__( 'Settings', 'constant-contact-forms' ),
			'menu_title'   => esc_html__( 'Settings', 'constant-contact-forms' ),
			'object_types' => [ 'options-page' ],
			'option_key'   => "{$this->key}_$cmb_id",
			'parent_slug'  => add_query_arg( 'post_type', 'ctct_forms', 'edit.php' ),
			'tab_group'    => $this->key,
			'tab_title'    => $this->metabox_titles[ $cmb_id ],
			'display_cb'   => [ $this, 'display_tabs' ],
		];
	}

	/**
	 * Register 'General' settings tab fields.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.8.0
	 */
	protected function register_fields_general() {
		$cmb = new_cmb2_box( $this->get_cmb_args( 'general' ) );

		if ( constant_contact()->get_api()->is_connected() ) {
			$cmb->add_field(
				[
					'name'       => esc_html__( 'Disable e-mail notifications', 'constant-contact-forms' ),
					'desc'       => sprintf(
					/* Translators: Placeholder is for a <br /> HTML tag. */
						esc_html__( 'This option will disable e-mail notifications for forms with a selected list and successfully submit to Constant Contact.%s Notifications are sent to the email address listed under Wordpress "General Settings".', 'constant-contact-forms' ),
						'<br/>'
					),
					'id'         => '_ctct_disable_email_notifications',
					'type'       => 'checkbox',
					'before_row' => '<hr/>',
				]
			);
		}

		$cmb->add_field(
			[
				'name' => esc_html__( 'Alternative disclaimer text', 'constant-contact-forms' ),
				'desc' => esc_html__( 'Override default sign-up disclaimer text. (Supports HTML)', 'constant-contact-forms' ),
				'id'   => '_ctct_alternative_legal_text',
				'type' => 'textarea',
			]
		);

	}

	/**
	 * Render a tab for the "Styles" settings.
	 *
	 * @since 2.9.0
	 */
	protected function register_fields_styles() {
		$cmb = new_cmb2_box( $this->get_cmb_args( 'styles' ) );

		$before_global_css = sprintf(
			'<hr><h2>%s</h2>',
			esc_html__( 'Global Form CSS Settings', 'constant-contact-forms' )
		);

		$cmb->add_field(
			[
				'name'        => esc_html__( 'CSS classes', 'constant-contact-forms' ),
				'id'          => '_ctct_form_custom_classes',
				'type'        => 'text',
				'description' => esc_html__(
					'Provide custom classes for the form separated by a single space.',
					'constant-contact-forms'
				),
				'before_row'  => $before_global_css,
			]
		);

		$cmb->add_field(
			[
				'name'             => esc_html__( 'Label placement', 'constant-contact-forms' ),
				'id'               => '_ctct_form_label_placement',
				'type'             => 'select',
				'default'          => 'top',
				'show_option_none' => false,
				'options'          => [
					'top'    => esc_html__( 'Top', 'constant-contact-forms' ),
					'left'   => esc_html__( 'Left', 'constant-contact-forms' ),
					'right'  => esc_html__( 'Right', 'constant-contact-forms' ),
					'bottom' => esc_html__( 'Bottom', 'constant-contact-forms' ),
					'hidden' => esc_html__( 'Hidden', 'constant-contact-forms' ),
				],
				'description'      => esc_html__(
					'Choose the position for the labels of the form elements.',
					'constant-contact-forms'
				),
			]
		);

		$cmb->add_field(
			[
				'name' => esc_html__( 'Disable Constant Contact CSS', 'constant-contact-forms' ),
				'desc' => esc_html__( 'Disables Constant Contact stylesheets from loading on the frontend. Note you may need to clear server and client cache to see changes go into effect.', 'constant-contact-forms' ),
				'id'   => '_ctct_disable_css',
				'type' => 'checkbox',
			]
		);
	}

	/**
	 * Render a tab for the "Advanced Optin" settings.
	 *
	 * @since 2.9.0
	 */
	protected function register_fields_optin() {
		$cmb = new_cmb2_box( $this->get_cmb_args( 'optin' ) );

		if ( constant_contact()->get_api()->is_connected() ) {
			$lists     = constant_contact()->get_builder()->get_lists();
			$woo_lists = [
				'WooCommerce - All Customers',
				'WooCommerce - First time Customers',
				'WooCommerce - Lapsed Customers',
				'WooCommerce - Potential Customers',
				'WooCommerce - Recent Customers',
				'WooCommerce - Repeat Customers',
			];
			foreach( $lists as $list_id => $list_name ) {
				if ( in_array( $list_name, $woo_lists ) ) {
					unset( $lists[ $list_id ] );
				}
			}

			if ( $lists && is_array( $lists ) ) {

				$before_optin = sprintf(
					'<hr><h2>%s</h2>',
					esc_html__( 'Advanced opt-in', 'constant-contact-forms' )
				);

				$business_name = get_bloginfo( 'name' ) ?: esc_html__( 'Business Name', 'constant-contact-forms' );
				$business_addr = '';

				$disclosure_info = $this->plugin->get_api()->get_disclosure_info( true );
				if ( ! empty( $disclosure_info ) ) {
					$business_name = $disclosure_info['name'] ?: $business_name;
					$business_addr = $disclosure_info['address'] ?: '';
				}

				$cmb->add_field(
					[
						'name'    => esc_html__( 'Opt-in affirmation', 'constant-contact-forms' ),
						'id'      => '_ctct_optin_label',
						'type'    => 'text',
						// translators: placeholder will hold site owner's business name.
						'default' => sprintf( esc_html__( 'Yes, I would like to receive emails from %s. Sign me up!', 'constant-contact-forms' ), $business_name ),
						'before_row' => $before_optin,
					]
				);

				if ( empty( $disclosure_info ) ) {
					$cmb->add_field(
						[
							'name'       => esc_html__( 'Disclosure Name', 'constant-contact-forms' ),
							'id'         => '_ctct_disclose_name',
							'type'       => 'text',
							'default'    => $business_name,
							'attributes' => ! empty( $business_name ) ? [ 'readonly' => 'readonly' ] : [],
						]
					);

					$cmb->add_field(
						[
							'name'       => esc_html__( 'Disclosure Address', 'constant-contact-forms' ),
							'id'         => '_ctct_disclose_address',
							'type'       => 'text',
							'default'    => $business_addr,
							'attributes' => ! empty( $business_addr ) ? [ 'readonly' => 'readonly' ] : [],
						]
					);
				}

				$cmb->add_field(
					[
						'name'       => esc_html__( 'Opt-in location', 'constant-contact-forms' ),
						'id'         => '_ctct_optin_forms',
						'type'       => 'multicheck',
						'options'    => $this->get_optin_show_options(),
					]
				);

				$cmb->add_field(
					[
						'name'             => esc_html__( 'Add subscribers to', 'constant-contact-forms' ),
						'id'               => '_ctct_optin_list',
						'type'             => 'multicheck',
						'show_option_none' => false,
						'default'          => esc_html__( 'Select a list', 'constant-contact-forms' ),
						'options'          => $lists,
					]
				);
			}
		}
	}

	/**
	 * Register 'Spam control' (incl. Google reCAPTCHA) settings tab fields.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.8.0
	 */
	protected function register_fields_spam() {
		$cmb = new_cmb2_box( $this->get_cmb_args( 'spam' ) );

		$before_captcha_service = sprintf(
			'<h2>%s</h2>',
			esc_html__( 'Captcha service', 'constant-contact-forms' )
		);

		$before_captcha_service .= '<div class="description"><p>';
		$before_captcha_service .= esc_html__( 'Select the captcha service to use.', 'constant-contact-forms' );
		$before_captcha_service .= '</div></p>';

		$cmb->add_field(
			[
				'name'             => esc_html__( 'Captcha service', 'constant-contact-forms' ),
				'id'               => '_ctct_captcha_service',
				'type'             => 'select',
				'default'          => false,
				'before_row'       => $before_captcha_service,
				//'show_option_none' => true,
				'options'          => [
					'disabled'  => esc_html__( 'None - captcha disabled', 'constant-contact-forms' ),
					'recaptcha' => esc_html__( 'Google reCAPTCHA', 'constant-contact-forms' ),
					'hcaptcha'  => esc_html__( 'hCaptcha', 'constant-contact-forms' ),
				],
			]
		);

		$before_recaptcha = sprintf(
			'<h2>%s</h2>',
			esc_html__( 'Google reCAPTCHA', 'constant-contact-forms' )
		);

		$before_recaptcha .= '<div class="description">';

		$before_recaptcha .= sprintf(
			wp_kses(
				/* translators: %s: recaptcha documentation URL */
				__( 'Learn more and get an <a href="%s" target="_blank">API site key</a>.', 'constant-contact-forms' ),
				[
					'a' => [
						'href'   => [],
						'target' => [],
					],
				]
			),
			esc_url( 'https://www.google.com/recaptcha/intro/' )
		);

		$before_recaptcha .= '</div>';

		$cmb->add_field(
			[
				'name'       => esc_html__( 'Version', 'constant-contact-forms' ),
				'id'         => '_ctct_recaptcha_version',
				'type'       => 'select',
				'default'    => 'v2',
				'before_row' => $before_recaptcha,
				'options'    => [
					'v2' => esc_html__( 'Version 2', 'constant-contact-forms' ),
					'v3' => esc_html__( 'Version 3', 'constant-contact-forms' ),
				],
			]
		);

		$cmb->add_field(
			[
				'name'            => esc_html__( 'Site key', 'constant-contact-forms' ),
				'id'              => '_ctct_recaptcha_site_key',
				'type'            => 'text',
				'sanitization_cb' => [ $this, 'sanitize_recaptcha_api_key_string' ],
				'attributes'      => [
					'maxlength' => 50,
				],
			]
		);

		$cmb->add_field(
			[
				'name'            => esc_html__( 'Secret key', 'constant-contact-forms' ),
				'id'              => '_ctct_recaptcha_secret_key',
				'type'            => 'text',
				'sanitization_cb' => [ $this, 'sanitize_recaptcha_api_key_string' ],
				'attributes'      => [
					'maxlength' => 50,
				],
			]
		);

		$before_hcaptcha = sprintf(
			'<h2>%s</h2>',
			esc_html__( 'hCaptcha', 'constant-contact-forms' )
		);

		$before_hcaptcha .= '<div class="description">';

		$before_hcaptcha .= sprintf(
			wp_kses(
			/* translators: %s: hcaptcha signup URL */
				__( 'Sign up and get your <a href="%s" target="_blank">free API key here</a>.', 'constant-contact-forms' ),
				[
					'a' => [
						'href'   => [],
						'target' => [],
					],
				]
			),
			esc_url( 'https://www.hcaptcha.com/pricing/' )
		);

		$before_hcaptcha .= '</div>';

		$cmb->add_field(
			[
				'name'            => esc_html__( 'Site Key', 'constant-contact-forms' ),
				'id'              => '_ctct_hcaptcha_site_key',
				'type'            => 'text',
				'before_row'      => $before_hcaptcha,
				'sanitization_cb' => [ $this, 'sanitize_hcaptcha_api_key_string' ],
				'attributes'      => [
					'maxlength' => 50,
				],
			]
		);

		$cmb->add_field(
			[
				'name'            => esc_html__( 'Secret Key', 'constant-contact-forms' ),
				'id'              => '_ctct_hcaptcha_secret_key',
				'type'            => 'text',
				'sanitization_cb' => [ $this, 'sanitize_hcaptcha_api_key_string' ],
				'attributes'      => [
					'maxlength' => 50,
				],
			]
		);

		$before_message = sprintf(
			'<hr/><h2>%s</h2><div class="description">%s</div>',
			esc_html__( 'Suspected bot error message', 'constant-contact-forms' ),
			esc_html__( 'This message displays when the plugin detects spam data. Note that this message may be overriden on a per-post basis.', 'constant-contact-forms' )
		);

		$cmb->add_field(
			[
				'name'       => esc_html__( 'Error message', 'constant-contact-forms' ),
				'id'         => '_ctct_spam_error',
				'type'       => 'text',
				'before_row' => $before_message,
				'default'    => $this->get_default_spam_error(),
			]
		);
	}

	/**
	 * Register 'Support' settings tab fields.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.8.0
	 */
	protected function register_fields_support() {
		$cmb = new_cmb2_box( $this->get_cmb_args( 'support' ) );

		$before_debugging = sprintf(
			'<h2>%1$s</h2>',
			esc_html__( 'Support', 'constant-contact-forms' )
		);
		$cmb->add_field(
			[
				'name'       => esc_html__( 'Enable logging for debugging purposes.', 'constant-contact-forms' ),
				'desc'       => esc_html__( 'This option will turn on some logging functionality that can be used to deduce sources of issues with the use of Constant Contact Forms plugin.', 'constant-contact-forms' ),
				'id'         => '_ctct_logging',
				'type'       => 'checkbox',
				'before_row' => $before_debugging,
			]
		);
	}

	/**
	 * Register 'auth' settings tab fields.
	 *
	 * @author Rebekah Van Epps <faisal@zao.is>
	 * @since  1.8.0
	 */
	protected function register_fields_auth() {
		$cmb = new_cmb2_box( $this->get_cmb_args( 'auth' ) );

		$before_api_key = sprintf(
			/* translators: 1: horizontal rule and opening heading tag, 2: global css section heading, 3: closing heading tag */
			'%1$s%2$s%3$s',
			'<h2>',
			esc_html__( 'Account settings', 'constant-contact-forms' ),
			'</h2>'
		);

		$cmb->add_field(
			[
				'name'       => esc_html__( 'Auth code and state', 'constant-contact-forms' ),
				'id'         => '_ctct_form_state_authcode',
				'type'       => 'text',
				'desc'       => 'Paste the string you copied from the app',
				'before_row' => $before_api_key,
			]
		);

	}



	/**
	 * Get array of options for our 'optin show' settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of options.
	 */
	public function get_optin_show_options() : array {

		$optin_options = [
			'comment_form' => esc_html__( 'Add a checkbox to the comment field in your posts', 'constant-contact-forms' ),
			'login_form'   => esc_html__( 'Add a checkbox to the main WordPress login page', 'constant-contact-forms' ),
		];

		if ( get_option( 'users_can_register' ) ) {
			$optin_options['reg_form'] = esc_html__( 'Add a checkbox to the WordPress user registration page', 'constant-contact-forms' );
		}

		return $optin_options;
	}

	/**
	 * Based on a type of form we pass in, check if the saved option
	 * for that form is checked or not in the admin.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Allowed values: 'login_form', 'comment_form', 'reg_form'.
	 * @return boolean If should show or not.
	 */
	public function check_if_optin_should_show( string $type ) : bool {

		$available_areas = constant_contact_get_option( '_ctct_optin_forms', [] );

		if ( ! is_array( $available_areas ) ) {
			return false;
		}

		// Otherwise, check to see if our check is in the array.
		return in_array( $type, $available_areas, true );
	}

	/**
	 * Potentially add our opt-in form to the login form. We have three almost
	 * identical functions here. This allows us to hook them all in by themselves
	 * and determine whether or not they should have been hooked in when we get
	 * to displaying them, rather than on potentially pages we dont care about.
	 *
	 * @since 1.0.0
	 */
	public function optin_form_field_login() {
		if ( $this->check_if_optin_should_show( 'login_form' ) ) {
			$this->optin_form_field();
		}
	}

	/**
	 * Potentially add our opt-in form to comment forms.
	 *
	 * @since 1.0.0
	 */
	public function optin_form_field_comment() {
		if ( $this->check_if_optin_should_show( 'comment_form' ) ) {
			$this->optin_form_field();
		}
	}

	/**
	 * Potentially add our opt-in form to the registration form.
	 *
	 * @since 1.0.0
	 */
	public function optin_form_field_registration() {
		if ( $this->check_if_optin_should_show( 'reg_form' ) ) {
			$this->optin_form_field();
		}
	}

	/**
	 * Opt in field checkbox.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function optin_form_field() {
		if ( ! constant_contact()->get_api()->is_connected() ) {
			return;
		}
		$lists = $this->get_optin_list_options();

		if ( empty( $lists ) ) {
			return;
		}

		$saved_label = constant_contact_get_option( '_ctct_optin_label', '' );
		$label       = $saved_label ?: esc_html__( 'Sign up to our newsletter.', 'constant-contact-forms' );
		?>
		<p class="ctct-optin-wrapper" style="padding: 0 0 1em 0;">
			<p><?php echo esc_attr( $label ); ?></p>
			<?php foreach ( $lists as $key => $list ) { ?>
				<label for="ctct_optin_<?php echo esc_attr( $key ); ?>">
					<input type="checkbox" value="<?php echo esc_attr( $key ); ?>" class="checkbox" id="ctct_optin_<?php echo esc_attr( $key ); ?>" name="ctct_optin_list[]" /> <?php echo esc_attr( $list ); ?>
				</label>
				<br/>
			<?php } ?>
			<?php echo wp_kses_post( constant_contact()->get_display()->get_disclose_text() ); ?>
		</p>
		<?php

	}

	/**
	 * Sends contact to CTCT if optin checked.
	 *
	 * @since 1.0.0
	 *
	 * @param array $comment_data Comment form data.
	 * @return array Comment form data.
	 */
	public function process_optin_comment_form( array $comment_data ) : array {
		$ctct_optin_lists = filter_input( INPUT_POST, 'ctct_optin_list', FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY );

		if ( empty( $ctct_optin_lists ) ) {
			return $comment_data;
		}

		return $this->process_comment_data_for_optin( $comment_data );
	}

	/**
	 * Process our comment data and send to CC.
	 *
	 * @since 1.0.0
	 *
	 * @param array $comment_data Array of comment data.
	 * @return array Passed in comment data
	 */
	public function process_comment_data_for_optin( array $comment_data ) : array {

		if ( isset( $comment_data['comment_author_email'] ) && $comment_data['comment_author_email'] ) {

			$name    = $comment_data['comment_author'] ?? '';
			$website = $comment_data['comment_author_url'] ?? '';
			$lists   = filter_input( INPUT_POST, 'ctct_optin_list', FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY );

			foreach ( $lists as $list ) {
				$args = [
					'list'       => sanitize_text_field( wp_unslash( $list ) ),
					'email'      => sanitize_email( $comment_data['comment_author_email'] ),
					'first_name' => sanitize_text_field( $name ),
					'last_name'  => '',
					'website'    => sanitize_text_field( $website ),
				];
				constant_contact()->get_api()->add_contact( $args );
			}
		}

		return $comment_data;
	}

	/**
	 * Sends contact to CTCT if optin checked.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed  $user User.
	 * @param string $username Login name.
	 * @param string $password User password.
	 * @return object|array CTCT return API for contact or original $user array.
	 */
	public function process_optin_login_form( $user, string $username, string $password ) {
		$ctct_optin_lists = filter_input( INPUT_POST, 'ctct_optin_list', FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY );

		if ( empty( $ctct_optin_lists ) ) {
			return $user;
		}

		if ( empty( $username ) ) {
			return $user;
		}

		return $this->process_user_data_for_optin( $user, $username );
	}


	/**
	 * Sends contact to CTCT if optin checked on register.
	 *
	 * @since 1.0.0
	 *
	 * @param  int $user_id ID of user just registered.
	 * @return int Pass in user ID.
	 */
	public function process_optin_register_form( int $user_id ) : int {

		$ctct_optin_lists = filter_input( INPUT_POST, 'ctct_optin_list', FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY );

		if ( empty( $ctct_optin_lists ) ) {
			return $user_id;
		}

		if ( empty( $user_id ) ) {
			return $user_id;
		}

		return $this->process_user_data_register_for_optin( $user_id );
	}

	/**
	 * Process contact for CTCT on register.
	 *
	 * @author Scott Anderson <scott.anderson@webdevstudios.com>
	 *
	 * @since  1.9.0
	 *
	 * @param  int $user_id ID of user just registered.
	 * @return int Pass in user ID.
	 */
	private function process_user_data_register_for_optin( int $user_id ) : int {
		$this->add_user_to_list( get_user_by( 'ID', $user_id ) );
		return $user_id;
	}

	/**
	 * Sends user data to CTCT.
	 *
	 * Updated form of process_user_data_for_optin to be more re-usable. Old function not refactored due to public visibility setting.
	 *
	 * @author Scott Anderson <scott.anderson@webdevstudios.com>
	 *
	 * @since  1.9.0
	 *
	 * @param object $user WP user object.
	 */
	private function add_user_to_list( object $user ) {

		$email = '';
		$name  = '';

		if ( ! $user ) {
			return;
		}

		if ( isset( $user->data->user_email ) ) {
			$email = sanitize_email( $user->data->user_email );
		}

		if ( isset( $user->data->display_name ) ) {
			$name = sanitize_text_field( $user->data->display_name );
		}

		if ( ! isset( $_POST['ctct_optin_list'] ) ) { // phpcs:ignore -- Okay accessing of $_POST.
			return;
		}

		$lists = filter_input( INPUT_POST, 'ctct_optin_list', FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY );

		if ( $email ) {
			foreach ( $lists as $list ) {
				$args = [
					'email'      => $email,
					'list'       => sanitize_text_field( wp_unslash( $list ) ),
					'first_name' => $name,
					'last_name'  => '',
				];
				constant_contact()->get_api()->add_contact( $args );
			}
		}

	}


	/**
	 * Sends user data to CTCT.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed  $user     WP user object.
	 * @param string $username Username.
	 * @return object Passed in $user object.
	 */
	public function process_user_data_for_optin( object $user, string $username ) : object {
		$this->add_user_to_list( get_user_by( 'login', $username ) );
		return $user;
	}

	/**
	 * Register settings notices for display.
	 *
	 * @since 1.0.0
	 *
	 * @param string $object_id CMB2 Option key.
	 * @param array  $updated   Array of updated fields.
	 * @return void
	 */
	public function settings_notices( string $object_id, array $updated ) {
		if ( $object_id !== $this->key || empty( $updated ) ) {
			return;
		}

		add_settings_error( $this->key . '-notices', '', esc_html__( 'Settings updated.', 'constant-contact-forms' ), 'updated' );
		settings_errors( $this->key . '-notices' );
	}

	/**
	 * Replaces get_option with get_site_option.
	 *
	 * @since 1.0.0
	 *
	 * @param string $deprecated Unused first param passed by CMB2 hook.
	 * @param mixed  $default    Default to return.
	 * @return mixed Site option
	 */
	public function get_override( string $deprecated, $default = false ) {
		$option = get_option( $this->key, $default );
		if ( empty( $option ) ) {
			$option = get_site_option( $this->key, $default );
		}
		return $option;
	}

	/**
	 * Replaces update_option with update_site_option.
	 *
	 * @since 1.0.0
	 *
	 * @param string $deprecated   Unused first param passed by CMB2 hook.
	 * @param mixed  $option_value Value to update to.
	 * @return bool Update success status.
	 */
	public function update_override( string $deprecated, $option_value ) : bool {
		return update_option( $this->key, $option_value );
	}

	/**
	 * Public getter method for retrieving protected/private variables.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception Invalid property.
	 *
	 * @param string $field Field to retrieve.
	 * @return mixed Field value or exception is thrown
	 */
	public function __get( string $field ) {
		if ( in_array( $field, [ 'key', 'metabox_id' ], true ) ) {
			if ( isset( $this->{$field} ) ) {
				return $this->{$field};
			}

			return null;
		}

		throw new Exception( 'Invalid property: ' . $field );
	}

	/**
	 * Attempts to add the index file for protecting the log directory.
	 *
	 * @since 1.5.0
	 *
	 * @param mixed      $updated Whether or not we're updating.
	 * @param string     $action  Current action being performed.
	 * @param CMB2_Field $field   Current field object.
	 * @return void
	 */
	public function maybe_init_logs( $updated, string $action, CMB2_Field $field ) {
		if ( 'updated' !== $action ) {
			return;
		}

		if ( 'on' !== $field->value ) {
			return;
		}

		$this->plugin->get_logging()->initialize_logging();
	}

	/**
	 * Get the error message displayed to suspected spam input.
	 *
	 * @since 1.5.0
	 *
	 * @param string $message The error message to filter.
	 * @param mixed  $post_id The post ID of the current post, if any.
	 * @return string
	 */
	public function get_spam_error_message( string $message, int $post_id ) : string {
		$post_error = get_post_meta( $post_id, '_ctct_spam_error', true );

		if ( ! empty( $post_error ) ) {
			return $post_error;
		}

		$option_error = constant_contact_get_option( '_ctct_spam_error' );

		if ( ! empty( $option_error ) ) {
			return $option_error;
		}

		return $this->get_default_spam_error();
	}

	/**
	 * Sanitize API key strings for Google reCaptcha. Length is enforced
	 *
	 * @since 1.6.0
	 *
	 * @param  mixed      $value      The unsanitized value from the form.
	 * @param  array      $field_args Array of field arguments.
	 * @param  CMB2_Field $field      The field object.
	 * @return string
	 */
	public function sanitize_recaptcha_api_key_string( $value, array $field_args, CMB2_Field $field ) : string {
		$value = trim( $value );

		// Keys need to be under 50 chars long and have no spaces inside them.
		if ( false !== strpos( $value, ' ' ) || 50 <= strlen( $value ) ) {
			return '';
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize API key strings for hCaptcha. Length is enforced
	 *
	 * @since 2.9.0
	 *
	 * @param  mixed      $value      The unsanitized value from the form.
	 * @param  array      $field_args Array of field arguments.
	 * @param  CMB2_Field $field      The field object.
	 * @return string
	 */
	public function sanitize_hcaptcha_api_key_string( $value, array $field_args, CMB2_Field $field ) : string {
		$value = trim( $value );

		// Keys need to be under 50 chars long and have no spaces inside them.
		if ( false !== strpos( $value, ' ' ) || 50 <= strlen( $value ) ) {
			return '';
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Get the default spam error message.
	 *
	 * @since 1.5.0
	 * @return string
	 */
	private function get_default_spam_error() : string {
		return esc_html__( 'We do not think you are human', 'constant-contact-forms' );
	}

	/**
	 * Returns formatted list of available lists during opt-in.
	 *
	 * @author Scott Anderson <scott.anderson@webdevstudios.com>
	 * @since  1.12.0
	 *
	 * @return array
	 */
	private function get_optin_list_options() : array {
		$lists = constant_contact_get_option( '_ctct_optin_list', [] );

		$formatted_lists = [];
		foreach ( $lists as $list_id ) {

			$list_args = [
				'numberposts' => 1,
				'post_type'   => 'ctct_lists',
				'meta_key'    => '_ctct_list_id',
				'meta_value'  => $list_id,
			];
			$list      = get_posts( $list_args );

			if ( ! empty( $list ) ) {
				$formatted_lists[ $list_id ] = $list[0]->post_title;
			}
		}

		return $formatted_lists;
	}
}

/**
 * Retrieve option value.
 *
 * Wrapper for `cmb2_get_option` to provide fallback when that function is not available.
 *
 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @since  1.9.0
 *
 * @param  string $key     Option key.
 * @param  mixed  $default Default option value.
 * @return mixed           Option value.
 */
function constant_contact_get_option( string $key = '', $default = null ) {
	if ( function_exists( 'cmb2_get_option' ) ) {
		return cmb2_get_option( constant_contact()->get_settings()->key, $key, $default );
	}

	$options = get_option( $key, $default );
	$value   = $default;

	if ( 'all' === $key ) {
		$value = $options;
	} elseif ( is_array( $options ) && array_key_exists( $key, $options ) && false !== $options[ $key ] ) {
		$value = $options[ $key ];
	}

	return $value;
}

/**
 * delete option value.
 *
 *
 * @author Faisal
 * @since  1.9.0
 *
 * @param  string $key     Option key.
 * @param  mixed  $default Default option value.
 * @return bool true if success, false if error           .
 */
function constant_contact_delete_option( string $key = '' ) : bool {

	$options = get_option( constant_contact()->get_settings()->key, $key );

	if ( is_array( $options ) && array_key_exists( $key, $options ) && false !== $options[ $key ] ) {

		$options[ $key ] = null;
		update_option( constant_contact()->get_settings()->key, $options );

		return true;
	}

	return false;
}

/**
 * Returns whether frontend css should be disabled or not.
 *
 * @author Scott Anderson <scott.anderson@webdevstudios.com>
 * @since 1.10.0
 * @return bool
 */
function constant_contact_disable_frontend_css() : bool {
	return 'on' === constant_contact_get_option( '_ctct_disable_css' );
}

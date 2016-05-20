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
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
	}


	/**
	 * Register our setting to WP
	 *
	 * @since  1.0.0
	 */
	public function init( $plugin ) {

		$defaults = array(
			'plugin' => 'Pluginize',
			'version' => '1.0.0',
			'domain' => 'pluginize',
			'welcome_text' => __( 'Way to level up with the latest version of Pluginize which makes it simple for developers and non-developers alike to create an unlimited amount of custom post types.', 'pluginize' ),
		);

		$plugin = wp_parse_args( $plugin, $defaults );

		$this->plugin = $plugin;
        $this->about_screen();
	}


	/**
	 * Admin page markup.
	 *
	 * @since  1.0.0
	 */
	public function welcome_text() {

		// Switch welcome text based on whether this is a new installation or not.
		$welcome_text = $this->plugin['welcome_text'];
		var_dump( $this->plugin );
		?>

		<h1><?php printf( esc_html__( 'Welcome to ' . constant_contact()->plugin_name . ' %s', constant_contact()->text_domain ), constant_contact()->version ); ?></h1>

		<div class="about-text">
			<?php printf( $welcome_text, constant_contact()->version ); ?>
		</div>

		<div class="plugin-badge">
			<img src="<?php echo esc_url( constant_contact()->url . 'assets/images/icon.png' ); ?>" alt="<?php esc_html_e( 'insert custom post type shortcodes.', constant_contact()->text_domain ); ?>">
		</div>

		<?php

	}

	public function about_screen() {
	?>

		<div class="wrap about-wrap">

			<?php self::welcome_text(); ?>

			<?php self::tab_navigation( __METHOD__ ); ?>

			<?php if ( isset( $_GET['is_new_install'] ) ) : ?>

				<div id="welcome-panel" class="welcome-panel">
					<div class="welcome-panel-content">
					</div>
				</div>

			<?php endif; ?>

			<div class="headline-feature">
				<h3 class="headline-title"><?php esc_html_e( 'New Shortcode Builder Templates', constant_contact()->text_domain ); ?></h3>

				<div class="featured-image">
					<img src="<?php echo esc_url( cptui_extended()->url . 'assets/images/shortcode.png' ); ?>" alt="<?php esc_html_e( 'insert custom post type shortcodes.', constant_contact()->text_domain ); ?>">
				</div>

				<p class="introduction"><?php _e( 'Easily display WooCommerce and Easy Digtal Downloads content.', constant_contact()->text_domain ); ?>  </p>
				<p><?php _e( 'CPTUI Extended 1.1 adds a slew of improvements. One of the big 1.1 features is new and enhanced templates. CPTUI Extended now includes WooCommerce and Easy Digital Downloads shortcode templates. <br><br>To <a href="https://pluginize.com/cptui-extended-shorcode-builder/">learn how to create shortcodes and templates</a> check out this post.', constant_contact()->text_domain ); ?></p>

				<div class="clear"></div>
			</div>

			<hr />

			<div class="bp-features-section">

				<div class="feature-section two-col">
					<div>
						<h3 class="feature-title"><?php esc_html_e( 'Network Wide Custom Post Types & Taxonomies', constant_contact()->text_domain ); ?></h3>
						<p><?php esc_html_e( 'CPTUI Extended works well with WordPress Multisite. From the network admin menu, we\'ve made it simple to create network-wide Custom Post Types and Taxonomies for use on all your subsites.', constant_contact()->text_domain ); ?></p>
					</div>
					<div class="last-feature">
						<h3 class="feature-title"><?php esc_html_e( 'CPTUI Shortcode Builder', constant_contact()->text_domain ); ?></h3>
						<p><?php _e( 'The CPT shortcode builder helps you display custom content through an intuitive and straightforward process. We\'ve supplied a default template for displaying data and provided documentation on creating your own advanced templates.', constant_contact()->text_domain ); ?> <a href="https://pluginize.com/cptui-extended-shorcode-builder/"><?php esc_html_e( 'Learn more &rarr;', constant_contact()->text_domain ); ?></a></p>
					</div>
				</div>

				<div class="feature-section two-col">
					<div>
						<h3 class="feature-title"><?php esc_html_e( 'Template Hooks', constant_contact()->text_domain ); ?></h3>
						<p><?php _e( 'We wanted to focus on the templates in this release because displaying your post types is the core feature of the plugin. So, we added a ton of templates hooks so you can display custom data.', constant_contact()->text_domain ); ?> <a href="http://codex.pluginize.com"><?php esc_html_e( 'Read the documentation to find out about all the new template hooks.', constant_contact()->text_domain ); ?></a></p>
					</div>
					<div class="last-feature">
					</div>
				</div>

			</div>

			<div class="changelog">
			</div>

			<p><?php _ex( 'Learn more:', 'About screen, website links', constant_contact()->text_domain ); ?> <a href="https://pluginize.com/blog/"><?php _ex( 'News', 'About screen, link to project blog', constant_contact()->text_domain ); ?></a> &bullet; <a href="http://docs.pluginize.com/"><?php _ex( 'Documentation', 'About screen, link to documentation', constant_contact()->text_domain ); ?></a> &bullet; <a href="https://pluginize.com/change-logs/cptui-extended-change-log/"><?php _ex( 'Change log', constant_contact()->text_domain ); ?></a></p>

			<p><?php _ex( 'Twitter:', 'official Twitter accounts:', constant_contact()->text_domain ); ?> <a href="https://twitter.com/pluginize/"><?php _ex( '@Pluginize', '@pluginize twitter account name', constant_contact()->text_domain ); ?></a></p>

		</div>

		<?php
	}

	/**
	 * Output our tab navigation.
	 *
	 * @param string $tab Active tab.
	 */
	public static function tab_navigation( $tab = 'whats_new' ) {
	?>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php if ( 'CPTUIEXT_Admin_About::about_screen' === $tab ) : ?>nav-tab-active<?php endif; ?>" href="<?php echo esc_url( cptui_get_admin_url( add_query_arg( array( 'page' => 'cptui_about' ), 'index.php' ) ) ); ?>">
				<?php esc_html_e( 'What&#8217;s New', constant_contact()->text_domain ); ?>
			</a><a class="nav-tab <?php if ( 'CPTUIEXT_Admin_About::credits_screen' === $tab ) : ?>nav-tab-active<?php endif; ?>" href="<?php echo esc_url( cptui_get_admin_url( add_query_arg( array( 'page' => 'cptui_credits' ), 'index.php' ) ) ); ?>">
				<?php esc_html_e( 'Credits', constant_contact()->text_domain ); ?>
			</a>
		</h2>

	<?php
	}

	/**
	 * Display our credit screen.
	 *
	 * @since 1.0.0
	 */
	public function credits_screen() {
	?>

		<div class="wrap about-wrap">

			<?php self::welcome_text(); ?>

			<?php self::tab_navigation( __METHOD__ ); ?>

			<p class="about-description"><?php esc_html_e( 'Custom Post Types UI is created by a worldwide network of friendly folks like these.', constant_contact()->text_domain ); ?></p>

			<h3 class="wp-people-group"><?php esc_html_e( 'Plugin Contributors', constant_contact()->text_domain ); ?></h3>
			<ul class="wp-people-group " id="wp-people-group-project-contributors">
				<li class="wp-person" id="wp-person-pluginize">
					<a class="web" href="https://profiles.wordpress.org/pluginize/"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/210484f55c0df074f663b2b6d082e063?s=60">
					Pluginize
					</a>
					<span class="title"><?php esc_html_e( 'Contributor', constant_contact()->text_domain ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-webdevstudios">
					<a class="web" href="https://profiles.wordpress.org/webdevstudios/"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/2596fe59ce16cabfe5ddf5c7d734ef8a?s=60">
					WebDevStudios
					</a>
					<span class="title"><?php esc_html_e( 'Contributor', constant_contact()->text_domain ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-tw2113">
					<a class="web" href="https://profiles.wordpress.org/tw2113/"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/a5d7c934621fa1c025b83ee79bc62366?s=60">
					Michael Beckwith
					</a>
					<span class="title"><?php esc_html_e( 'Contributor', constant_contact()->text_domain ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-modemlooper">
					<a class="web" href="https://profiles.wordpress.org/modemlooper/"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/1c07be1016e845de514931477c939307?s=60">
					modemlooper
					</a>
					<span class="title"><?php esc_html_e( 'Contributor', constant_contact()->text_domain ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-modemlooper">
					<a class="web" href="https://profiles.wordpress.org/vegasgeek/"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/6f3c8b1e3930788f8fc676c9f23769ac?s=60">
					John Hawkins
					</a>
					<span class="title"><?php esc_html_e( 'Contributor', constant_contact()->text_domain ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-modemlooper">
					<a class="web" href="https://profiles.wordpress.org/colorful-tones/"><img alt="" class="gravatar" src="//www.gravatar.com/avatar/e3dd9f1bbd70a30a63d3d5cc6090059e?s=60">
					Damon Cook
					</a>
					<span class="title"><?php esc_html_e( 'Contributor', constant_contact()->text_domain ); ?></span>
				</li>
			</ul>

		</div>

		<?php
	}
}

/**
 * About page class setup
 *
 * @return void
 */
function constantcontact_about( $args = array() ) {
	$about = new ConstantContact_Admin_About;
	$about->init( $args );
}


constantcontact_about(
	array(
		'plugin' => constant_contact()->plugin_name,
		'version' => constant_contact()->version,
		'domain' => constant_contact()->text_domain,
		'icon' => '',
		'welcome_text' => __( 'Way to level up with the latest version of ' . constant_contact()->plugin_name . ' which makes it simple for developers and non-developers alike to create an unlimited amount of custom post types.', constant_contact()->text_domain ),
		'featured' => array(
			'title' => '',
			'image' => '',
			'descriptions' => '',
		),
		'features' => array(
			array(
				'title' => '',
				'image' => '',
				'descriptions' => '',
			),
		),
	)
);

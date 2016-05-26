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
			'icon' => '',
			'welcome_text' => __( 'Way to level up with the latest version of Pluginize which makes it simple for developers and non-developers alike to create an unlimited amount of custom post types.', 'pluginize' ),
			'featured' => array(
				'headline' => 'New Amazing Feature',
				'image' => '',
				'description' => 'This new feature rocks the socks and makes cats meow!',
			),
			'features' => array(
				array(
					'title' => 'Have you tried feature X?',
					'image' => '',
					'description' => 'Its the bomb .com and a bag of chips.',
				),
			),
			'links' => array(
				'News' => '',
				'Documentation' => '',
				'Change log' => '',
			),
			'social' => array(
				'Twitter' => '',
				'Facebook' => '',
			),
		);

		$plugin = wds_wp_parse_args( $plugin, $defaults );

		$this->plugin = $plugin;
        $this->about_screen();
	}


	/**
	 * Admin page markup.
	 *
	 * @since  1.0.0
	 */
	public function welcome_text() {

		?>
		<style>
		h1.about-header {
			font-size: 22px;
		}
		.about-wrap h3 {
			padding: 20px 0;
		}
		.about-wrap .about-text {
			margin: 1em 0.5em 1em 0;
		}
		.feature-section.two-col > div {
			width: 49%;
			float: left;
		}
		</style>

		<?php $welcome_text = $this->plugin['welcome_text']; ?>

		<div style="overflow:hidden;">
			<div style="float:left; width:80%;">
				<h1 class="about-header"><?php printf( esc_html__( 'Welcome to ' . $this->plugin['plugin'] . ' %s', $this->plugin['domain'] ), $this->plugin['version'] ); ?></h1>

				<div class="about-text">
					<?php printf( $welcome_text, $this->plugin['version'] ); ?>
				</div>
			</div>

			<div class="plugin-badge" style="height:150px; width:150px; background:grey; float:left; margin: 10px 0 0 0;">
				<img src="<?php echo esc_url( $this->plugin['icon'] ); ?>" alt="<?php esc_html_e( $this->plugin['plugin'] . ' icon.', $this->plugin['domain'] ); ?>">
			</div>
		</div>
		<?php

	}

	public function about_screen() {
	?>

		<div class="wrap about-wrap">

			<?php self::welcome_text(); ?>

			<?php self::tab_navigation( __METHOD__ ); ?>

			<div class="headline-feature">
				<h3 class="headline-title" style="text-align:center;"><?php esc_html_e( $this->plugin['featured']['headline'] ); ?></h3>

				<div class="featured-image" style="min-height:150px; background: grey;">
					<img src="<?php echo esc_url( $this->plugin['featured']['image'] ); ?>" alt="<?php esc_html_e( $this->plugin['plugin'] . ' feature image.', $this->plugin['domain'] ); ?>">
				</div>

				<p class="introduction" style="text-align:center;"><?php echo $this->plugin['featured']['description'];  ?></p>

				<div class="clear"></div>
			</div>

			<hr />

			<div class="features-section">

				<div class="feature-section <?php if ( count( $this->plugin['features'] ) >= 2 ) { echo 'two-col'; } ?>">
					<?php foreach ( $this->plugin['features'] as $feature ) : ?>
					<div>
						<h3 class="feature-title" style="text-align:center;"><?php esc_html_e( $feature['title'] ); ?></h3>
						<?php if ( ! empty( $feature['image'] ) ) : ?>
							<div class="feature-image" style="min-height:150px; max-width: 500px; margin: 0 auto; background: grey;">
								<img src="<?php echo esc_url( $feature['image'] ); ?>" alt="<?php esc_html_e( $this->plugin['plugin'] . ' feature image.', $this->plugin['domain'] ); ?>">
							</div>
						<?php endif; ?>
						<p style="text-align:center;"><?php esc_html_e( $feature['description'] ); ?></p>
					</div>
				<?php endforeach; ?>
				</div>

			</div>

			<p>
				<?php _ex( 'Learn more:', 'About screen, website links', $this->plugin['domain'] ); ?>
				<?php foreach ( $this->plugin['links'] as $link => $value ) : ?>
					<a href="<?php echo $value; ?>"><?php _e( $link, $this->plugin['domain'] ); ?></a> &bullet;
				<?php endforeach; ?>
			</p>

			<p>
				<?php _ex( 'Follow Us!:', 'About screen, social media links', $this->plugin['domain'] ); ?>
				<?php foreach ( $this->plugin['social'] as $link => $value ) : ?>
					<a href="<?php echo $value; ?>"><?php _e( $link, $this->plugin['domain'] ); ?></a> &bullet;
				<?php endforeach; ?>
			</p>

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
			<a class="nav-tab <?php if ( 'ConstantContact_Admin_About::about_screen' === $tab ) : ?>nav-tab-active<?php endif; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'cptui_about' ), 'index.php' ) ) ); ?>">
				<?php esc_html_e( 'What&#8217;s New', constant_contact()->text_domain ); ?>
			</a>
		</h2>

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
		'icon' => constant_contact()->url . 'assets/images/icon.jpg',
		'welcome_text' => __( 'Way to level up with the latest version of ' . constant_contact()->plugin_name . ' which makes it simple for developers and non-developers alike to create an unlimited amount of custom post types.', constant_contact()->text_domain ),
		'featured' => array(
			'headline' => '',
			'image' => cptui_extended()->url . 'assets/images/shortcode.png',
			//'description' => '',
		),
		'features' => array(
			array(
				'title' => 'Create Custom Forms',
				'image' => '',
				'description' => 'Create custom forms to get info about people.',
			),
			array(
				'title' => 'Create Custom Lists',
				'image' => '',
				'description' => 'Create custom lists to group people.',
			),
		),
	)
);



	/**
	 * Parse multidemntional args
	 *
	 * @param  array $a args to parse.
	 * @param  array $b default array.
	 * @return array   the parsed array
	 */
	function wds_wp_parse_args( &$a, $b ) {
		$a = (array) $a;
		$b = (array) $b;
		$result = $b;
		foreach ( $a as $k => &$v ) {
			if ( is_array( $v ) && isset( $result[ $k ] ) ) {
				$result[ $k ] = wds_wp_parse_args( $v, $result[ $k ] );
			} else {
				$result[ $k ] = $v;
			}
		}
		return $result;
	}

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
	 * @internal
	 * @since 1.0.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
	}

	/**
	 * Register our setting to WP
	 *
	 * @internal
	 * @since  1.0.0
	 * @return void
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
				'title' => '',
				'sub_title' => '',
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
	 * @internal
	 * @since  1.0.0
	 * @return void
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
		.about-wrap .headline-feature {
			max-width: 90%;
		}
		p.introduction {
			padding: 1.8em 0;
		}
		p.featured-title {
			text-align: center;
			position: absolute;
			top: 80px;
			margin: 0 auto;
			width: 100%;
			font-size: 2em;
			font-weight: bold;
			color: white;
			text-shadow: 0 0 15px #000;
		}
		p.featured-introduction {
			text-align: center;
			position: absolute;
			top: 120px;
			margin: 0 auto;
			width: 100%;
			font-size: 1.2em;
			color: white;
			text-shadow: 0 0 15px #000;
		}
		</style>

		<?php $welcome_text = $this->plugin['welcome_text']; ?>

		<div style="overflow:hidden;">
			<div style="float:left; width:80%;">
				<h1 class="about-header"><?php printf( esc_html__( 'Welcome to ' . $this->plugin['plugin'] . ' %s', $this->plugin['domain'] ), '' ); ?></h1>

				<div class="about-text">
					<?php printf( $welcome_text, $this->plugin['version'] ); ?>
					<input type="button" class="button-primary ctct-disconnect" value="Connect Now!">
				</div>
			</div>

			<div class="plugin-badge" style="height:180px; width:160px; float:left; margin: 10px 0 0 0;">
				<img src="<?php echo esc_url( $this->plugin['icon'] ); ?>" alt="<?php esc_html_e( $this->plugin['plugin'] . ' icon.', $this->plugin['domain'] ); ?>">
			</div>
		</div>
		<?php

	}

	/**
	 * About screen markup
	 *
	 * @internal
	 * @since 1.0.0
	 * @return void
	 */
	public function about_screen() {
	?>

		<div class="wrap about-wrap">

			<?php self::welcome_text(); ?>

			<?php self::tab_navigation( __METHOD__ ); ?>

			<div class="headline-feature">
				<h3 class="headline-title" style="text-align:center;"><?php esc_html_e( $this->plugin['featured']['headline'] ); ?></h3>

				<div class="featured-image" style="min-height:150px; position: relative;">
					<img src="<?php echo esc_url( $this->plugin['featured']['image'] ); ?>" alt="<?php esc_html_e( $this->plugin['plugin'] . ' feature image.', $this->plugin['domain'] ); ?>">
					<p class="featured-title" style="text-align:center;"><?php echo $this->plugin['featured']['title']; ?></p>
					<p class="featured-introduction" style="text-align:center;"><?php echo $this->plugin['featured']['sub_title']; ?></p>
				</div>

				<p class="introduction" style="text-align:center;"><?php echo $this->plugin['featured']['description']; ?></p>


				<div class="clear"></div>
			</div>

			<hr />

			<div class="features-section">

				<div class="feature-section <?php if ( count( $this->plugin['features'] ) >= 2 ) { echo 'two-col'; } ?>">
					<?php foreach ( $this->plugin['features'] as $feature ) : ?>
					<div>
						<h3 class="feature-title" style="text-align:center;"><?php echo esc_html( $feature['title'] ); ?></h3>
						<?php if ( ! empty( $feature['image'] ) ) : ?>
							<div class="feature-image" style="height:195px; max-width:500px; margin:20px; overflow:hidden;">
								<img src="<?php echo esc_url( $feature['image'] ); ?>" alt="<?php esc_html_e( $this->plugin['plugin'] . ' feature image.', $this->plugin['domain'] ); ?>">
							</div>
						<?php endif; ?>
						<p style="text-align:center;"><?php echo esc_html( $feature['description'] ); ?></p>
					</div>
				<?php endforeach; ?>
				</div>

			</div>

			<p>
				<?php _ex( 'Learn more:', 'About screen, website links', $this->plugin['domain'] ); ?>
				<?php foreach ( $this->plugin['links'] as $link => $value ) : ?>
					<a href="<?php echo esc_url( $value ); ?>"><?php esc_html_e( $link, $this->plugin['domain'] ); ?></a> &bullet;
				<?php endforeach; ?>
			</p>

			<p>
				<?php _ex( 'Follow Us!:', 'About screen, social media links', $this->plugin['domain'] ); ?>
				<?php foreach ( $this->plugin['social'] as $link => $value ) : ?>
					<a href="<?php echo esc_url( $value ); ?>"><?php esc_html_e( $link, $this->plugin['domain'] ); ?></a> &bullet;
				<?php endforeach; ?>
			</p>

		</div>

		<?php
	}

	/**
	 * Output our tab navigation.
	 *
	 * @internal
	 * @since 1.0.0
	 * @param string $tab Active tab.
	 */
	public static function tab_navigation( $tab = 'whats_new' ) {
	?>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php if ( 'ConstantContact_Admin_About::about_screen' === $tab ) : ?>nav-tab-active<?php endif; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'cptui_about' ), 'index.php' ) ) ); ?>">
				<?php esc_html_e( 'What&#8217;s New', 'constantcontact' ); ?>
			</a>
		</h2>

	<?php
	}
}

/**
 * About page class setup
 *
 * @internal
 * @since 1.0.0
 * @param array $args data to populate the about page.
 * @return void
 */
function constantcontact_about( $args = array() ) {
	$about = new ConstantContact_Admin_About;
	$about->init( $args );
}

// Data for the content of the about page.
constantcontact_about(
	array(
		'plugin' => constant_contact()->plugin_name,
		'version' => constant_contact()->version,
		'domain' => 'constantcontact',
		'icon' => constant_contact()->url . 'assets/images/icon.jpg',
		'welcome_text' => __( 'Powerful Email Marketing, Made Simple. For every dollar spent on email marketing, small businesses make an average of $40 back. * Stats like that make email marketing a must for small businesses. And with Constant Contact, you also get the free award-winning coaching, and resources to see results like that, faster.', 'constantcontact' ),
		'featured' => array(
			'headline' => '',
			'image' => constant_contact()->url . 'assets/images/coffee-hero.jpg',
			'title' => 'Powerful Email Marketing, Made Simple.',
			'sub_title' => 'Create professional emails that bring customers to your door.',
			'description' => 'Email marketing is good for your business. $44-back-for-every-$1-spent kind of good.* And with a tool as powerful and easy to use as Constant Contact, you don’t need to be a marketing expert to see results.
*Direct Marketing Association 2013 Statistical Fact Book',
		),
		'features' => array(
			array(
				'title' => 'Easy Contact management.',
				'image' => constant_contact()->url . 'assets/images/contacts.png',
				'description' => 'Create custom forms and add users to contact lists.',
			),
			array(
				'title' => 'Track Your Success',
				'image' => constant_contact()->url . 'assets/images/reporting.png',
				'description' => 'Our email tracking tools tell you who\'s opening, clicking, and sharing your emails and social posts in real time.',
			),
		),
	)
);

if ( ! function_exists( 'wds_wp_parse_args' ) ) {
	/**
	 * Parse multidemntional args
	 *
	 * @internal
	 * @since 1.0.0
	 * @param  array $a args to parse.
	 * @param  array $b default array.
	 * @return array the parsed array
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
}

<?php
/**
 * HelpScout Widget
 *
 * @package HelpScout
 * @author WebDevStudios
 * @since 0.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Helpscout Dashboard main class.
 */
class Helpscout_Customer_Dashboard {

	/**
	 * Contruct widget.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->setup_actions();
	}

	/**
	 * Setup_actions
	 *
	 * @return void
	 */
	private function setup_actions() {
		if ( is_multisite() ) {
			add_action( 'wp_network_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
		}
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
	}

	/**
	 * Register Dashboard widget.
	 */
	public function add_dashboard_widget() {
		add_meta_box( 'Helpscout-dashboard-widget', __( 'Pluginize', 'Helpscout-dashboard-widget' ), array( $this, 'dashboard_widget' ), 'dashboard', 'side', 'high' );

		// Add to network dashboard.
		wp_add_dashboard_widget(
			'Helpscout-dashboard-widget',
			__( 'Pluginize Support' ),
			array( $this, 'dashboard_widget' )
		);
	}

	/**
	 * Render Dashboard widget.
	 */
	public function dashboard_widget() {

		 $header_message = __( 'Pluginize products expand your ability to win the race with plugins used by companies looking for proven results.', 'pluginize-dashboard-widget' );

		 ?>
		 <div class="pluginize-header"><p><img src="<?php echo $this->plugin->url( 'assets/img/pluginize.png' ); ?>" alt="<?php __( 'Pluginize Support', 'Helpscout-dashboard-widget' ); ?>" /></p>
		 <p style="margin-top: 17px;"><?php esc_attr_e( $header_message ); ?></p></div>

		<div class="pluginize-body">
		</div>

		<?php $this->pluginize_rest_data() ?>

		<style text="text/css">
		.pluginize-header {
			background-color: #1d2428;
			overflow: hidden;
			padding: 1em;
			color: #ffffff;
		}
		.pluginize-body {
			background-color: #288edf;
			overflow: hidden;
			padding: 1.5em 1em;
			color: #ffffff;
			margin: 0 0 1em 0;
		}
		.pluginize-body .button {
			margin:  0 .5em 0 0;
			border: none;
		}
		.pluginize-feed {
			padding: 1em;
		}
		h3.feed-title {
			font-size: 1.2em !important;
			font-weight: 800 !important;
		}
		.pluginize-feed li {
			border-bottom: 1px solid #e0e0e0;
			padding: .5em 0;
		}
		.pluginize-shop {
			overflow: hidden;
			padding:  0 1em 1em 1em;
			color: #ffffff;
		}
		.pluginize-shop li {
			float: left;
			width: 100%;
			margin: 0 .5em 0 0;
		}
		.pluginize-shop img {
			max-width: 100%;
		}
		</style>
		<?php
	}

	/**
	 * Product and Post display from plugnize rest api.
	 *
	 * @return void
	 */
	public function pluginize_rest_data() {

		// Get RSS Feed(s)
		include_once( ABSPATH . WPINC . '/feed.php' );
		$rss = fetch_feed( 'https://pluginize.com/feed/' );
		$maxitems = 0;

		if ( ! is_wp_error( $rss ) ) {
		    $maxitems = $rss->get_item_quantity( 3 );
		    $rss_items = $rss->get_items( 0, $maxitems );
		}
	?>
		<div class="pluginize-feed">
			<h3 class="feed-title">Pluginize News</h3>
			<ul>
			<?php
			if( isset( $rss_items ) ) {
				if ( ! empty( $rss_items ) ) {
					foreach ( $rss_items as $item ) {
						echo '<a class="" href="'. esc_url(  $item->get_permalink() ) .'" title="'. esc_attr( $item->get_title()) .'"><li>'. esc_attr( $item->get_title() ) .'</li></a>';
					}
				}
			} else {
				_e( 'Sorry, currently no news.', 'cptuiext' );
			}
			?>
			</ul>
		</div>

		<div class="pluginize-shop">
			<h3 class="feed-title">Pluginize Shop</h3>
			<ul>
			<?php
				echo '<a class="" href="https://pluginize.com/product/buddypages/?utm_source=dashboard-widget&utm_medium=banner&utm_campaign=buddyextender" title="link to BuddyPages product on pluginize.com"><li><img src="'. esc_url( helpscout_customer()->url ) .'assets/img/buddypages.jpg"></li></a>';
			?>
			</ul>
		</div>

		<?php
	}
}

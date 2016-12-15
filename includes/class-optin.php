<?php
/**
 * Opt-in.
 *
 * @package ConstantContact
 * @subpackage Lists
 * @author Constant Contact
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Optin for usage tracking
 */
class ConstantContact_Optin {

	/**
	 * Get things going.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		add_action( 'init', array( $this, 'hooks' ) );
	}

	public function hooks() {
		if ( $this->can_track() && constant_contact()->is_constant_contact() ) {
			add_action( 'admin_footer', array( $this, 'anonymous_tracking' ) );
		}
	}

	public function anonymous_tracking() {
		?>
		<!-- Google Analytics -->
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
			ga('create', 'UA-89027837-1', 'auto');
			ga('send', 'pageview');
		</script>
		<!-- End Google Analytics -->
		<?php
	}

	public function can_track() {
		$options = get_option( constant_contact()->settings->key );
		$optin = ( isset( $options['_ctct_data_tracking'] ) ) ? $options['_ctct_data_tracking'] : '';
		return ( 'on' === $optin );
	}
}

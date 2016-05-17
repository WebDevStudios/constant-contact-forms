<?php
/**
 * Displays WebDevStudios products in a sidebar on the add/edit screens for post types and taxonomies.
 *
 * @package    BuddyExtender
 * @subpackage ProductSidebar
 * @author     WebDevStudios
 * @since      1.0.0
 */

if ( ! function_exists( 'bpextender_products_sidebar' ) ) {

	/**
	 * Displays WebDevStudios products in a sidebar on the add/edit screens for post types and taxonomies.
	 * We hope you don't mind.
	 *
	 * @since 1.0.0
	 *
	 * @internal
	 */
	function bpextender_products_sidebar() {

		$ads = array(
			 array(
				'image' => buddyextender()->url() . 'assets/images/buddypages.jpg',
				'url' => 'https://pluginize.com/product/buddypages/?utm_source=sidebar-buddypages&utm_medium=banner&utm_campaign=buddyextender',
				'text' => 'BuddyPages product ad',
			),
			array(
			   'image' => buddyextender()->url() . 'assets/images/apppresser.png',
			   'url' => 'https://apppresser.com/?utm_source=pluginize&utm_medium=plugin&utm_campaign=buddyextender',
			   'text' => 'AppPresser product ad',
		   ),
		   array(
			  'image' => buddyextender()->url() . 'assets/images/maintainn.png',
			  'url' => 'https://maintainn.com/?utm_source=settings-sidebar&utm_medium=banner&utm_campaign=buddyextender',
			  'text' => 'Maintainn product ad',
		  ),
		);

		if ( ! empty( $ads ) ) {
			echo '<div class="pluginizepromos">';
			foreach ( $ads as $ad ) {
				$the_ad = sprintf(
					'<img src="%s" alt="%s">',
					$ad['image'],
					$ad['text']
				);

				printf(
					'<a href="%s">%s</a>',
					$ad['url'],
					$the_ad
				);
			}
			echo '</div>';

		}
	}
}

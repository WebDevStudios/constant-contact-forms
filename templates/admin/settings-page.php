<?php
/**
 * Renders the admin settings page.
 *
 * @package ConstantContact
 * @subpackage Settings
 * @since 1.6.0
 *
 * @var array $tab_urls Array of URLs for each tab.
 * @var array $tab_classes Array of CSS classes for each tab.
 */

?>

<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<?php settings_errors(); ?>

	<h2 class="nav-tab-wrapper">
		<a href="<?php echo esc_url( $tab_urls['general'] ); ?>" class="<?php echo esc_attr( $tab_classes['general'] ); ?>"><?php esc_html_e( 'General Settings', 'constant-contact-forms' ); ?></a>
		<a href="<?php echo esc_url( $tab_urls['form'] ); ?>" class="<?php echo esc_attr( $tab_classes['form'] ); ?>"><?php esc_html_e( 'Form Settings', 'constant-contact-forms' ); ?></a>
		<a href="<?php echo esc_url( $tab_urls['support'] ); ?>" class="<?php echo esc_attr( $tab_classes['support'] ); ?>"><?php esc_html_e( 'Support', 'constant-contact-forms' ); ?></a>
	</h2>

	<div id="tab_container">
		<form method="post" action="options.php">
			<?php

				settings_fields( ConstantContact_Settings_Tabbed::$options_key );

				switch ( $active_tab ) {
					case 'form':
						do_settings_sections( 'ctct_options_form' );
						break;
					case 'support':
						do_settings_sections( 'ctct_options_support' );
						break;
					case 'general':
					default:
						do_settings_sections( 'ctct_options_general' );
						break;
				}

				submit_button();
			?>
		</form>
	</div>
</div>

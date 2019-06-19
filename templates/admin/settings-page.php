<?php



write_log( $active_tab, 'active tab' );
?>
<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<?php settings_errors(); ?>

	<h2 class="nav-tab-wrapper">
		<a href="<?php echo esc_url( $tab_urls['general'] ); ?>" class="<?php echo esc_attr( $tab_classes['general'] ); ?>">General Settings</a>
		<a href="<?php echo esc_url( $tab_urls['form'] ); ?>" class="<?php echo esc_attr( $tab_classes['form'] ); ?>">Form Settings</a>
		<a href="<?php echo esc_url( $tab_urls['support'] ); ?>" class="<?php echo esc_attr( $tab_classes['support'] ); ?>">Support</a>
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

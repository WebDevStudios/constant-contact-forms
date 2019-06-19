<?php

$active_tab        = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING ) ?: 'general';
$settings_page_url = admin_url( 'edit.php?post_type=ctct_forms&page=ctct_options' );

$tab_urls = [
	'general' => add_query_arg( [ 'tab' => 'general' ], $settings_page_url ),
	'form'    => add_query_arg( [ 'tab' => 'form' ], $settings_page_url ),
	'support' => add_query_arg( [ 'tab' => 'support' ], $settings_page_url ),
];

$tab_classes = [
	'general' => 'general' === $active_tab ? 'nav-tab-active nav-tab' : 'nav-tab',
	'form'    => 'form' === $active_tab ? 'nav-tab-active nav-tab' : 'nav-tab',
	'support' => 'support' === $active_tab ? 'nav-tab-active nav-tab' : 'nav-tab',
];

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

	<form method="post" action="options.php">
		<?php
			switch ( $active_tab ) {
				case 'form':
					settings_fields( ConstantContact_Settings_Tabbed::$options_key );
					do_settings_fields( ConstantContact_Settings_Tabbed::$options_key, 'ctct_options_form' );
					break;
				case 'support':
					settings_fields( ConstantContact_Settings_Tabbed::$options_key );
					do_settings_fields( ConstantContact_Settings_Tabbed::$options_key, 'ctct_options_support' );
					break;
				case 'general':
				default:
					settings_fields( ConstantContact_Settings_Tabbed::$options_key );
					do_settings_fields( ConstantContact_Settings_Tabbed::$options_key, 'ctct_options_general' );
					break;
			}

			submit_button();
		?>
	</form>
</div>

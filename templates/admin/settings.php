<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>

	<?php settings_errors(); ?>

	<h2 class="nav-tab-wrapper">
		<a href="#" class="nav-tab">General Settings</a>
		<a href="#" class="nav-tab">Form Settings</a>
		<a href="#" class="nav-tab">Support</a>
	</h2>

	<form method="post" action="options.php">

		<?php settings_fields( ConstantContact_Settings_Tabbed::$options_key ); ?>
		<?php do_settings_sections( self::$options_key ); ?>


		<?php submit_button(); ?>
	</form>
</div>

<?php
/**
 * Tests the Constant_Contact base class.
 *
 * @package ConstantContact
 * @subpackage Display
 * @since 1.6.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Tests the Constant_Contact base class.
 *
 * @since 1.6.0
 */
class BaseTest extends WP_UnitTestCase {

	/**
	 * Set up.
	 *
	 * @since 1.6.0
	 */
	public function setUp() {
		parent::setUp();
		$this->plugin = constant_contact();
	}

	/**
	 * Should map classes to base class properties.
	 *
	 * @since 1.6.0
	 *
	 * @test
	 */
	public function should_map_classes_to_base_class_properties() {
		$this->assertInstanceOf( ConstantContact_API::class, $this->plugin->api );
		$this->assertInstanceOf( ConstantContact_Builder::class, $this->plugin->builder );
		$this->assertInstanceOf( ConstantContact_Builder_Fields::class, $this->plugin->builder_fields );
		$this->assertInstanceOf( ConstantContact_Check::class, $this->plugin->check );
		$this->assertInstanceOf( ConstantContact_CPTS::class, $this->plugin->cpts );
		$this->assertInstanceOf( ConstantContact_Display::class, $this->plugin->display );
		$this->assertInstanceOf( ConstantContact_Shortcode::class, $this->plugin->shortcode );
		$this->assertInstanceOf( ConstantContact_Display_Shortcode::class, $this->plugin->display_shortcode );
		$this->assertInstanceOf( ConstantContact_Lists::class, $this->plugin->lists );
		$this->assertInstanceOf( ConstantContact_Process_Form::class, $this->plugin->process_form );
		$this->assertInstanceOf( ConstantContact_Settings::class, $this->plugin->settings );
		$this->assertInstanceOf( ConstantContact_Auth_Redirect::class, $this->plugin->auth_redirect );
		$this->assertInstanceOf( ConstantContact_Connect::class, $this->plugin->connect );
		$this->assertInstanceOf( ConstantContact_Mail::class, $this->plugin->mail );
		$this->assertInstanceOf( ConstantContact_Notifications::class, $this->plugin->notifications );
		$this->assertInstanceOf( ConstantContact_Notification_Content::class, $this->plugin->notification_content );
		$this->assertInstanceOf( ConstantContact_Middleware::class, $this->plugin->authserver );
		$this->assertInstanceOf( ConstantContact_Updates::class, $this->plugin->updates );
		$this->assertInstanceOf( ConstantContact_Optin::class, $this->plugin->optin );
		$this->assertInstanceOf( ConstantContact_Logging::class, $this->plugin->logging );
		$this->assertInstanceOf( ConstantContact_User_Customizations::class, $this->plugin->customizations );
		$this->assertInstanceOf( ConstantContact_Admin::class, $this->plugin->admin );
		$this->assertInstanceOf( ConstantContact_Admin_Pages::class, $this->plugin->admin_pages );
		$this->assertInstanceOf( ConstantContact_Gutenberg::class, $this->plugin->gutenberg );
	}

}

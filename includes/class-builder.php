<?php
/**
 * Main class for dealing with our form builder functionality.
 *
 * @package ConstantContact
 * @subpackage Builder
 * @author Constant Contact
 * @since 1.0.0
 *
 * phpcs:disable WebDevStudios.All.RequireAuthor -- Don't require author tag in docblocks.
 */

/**
 * Main class for dealing with our form builder functionality.
 *
 * @since 1.0.0
 */
class ConstantContact_Builder {

	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	protected $plugin;

	/**
	 * Prefix for our meta fields/boxes.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $prefix = '_ctct_';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param object $plugin Parent plugin class.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->init();
	}

	/**
	 * Initiate our init.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'init', [ $this, 'hooks' ] );
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		global $pagenow;

		$form_builder_pages = apply_filters( 'constant_contact_form_builder_pages', [ 'post-new.php', 'post.php' ] );

		if ( in_array( $pagenow, $form_builder_pages, true ) ) {

			add_action( 'cmb2_after_post_form_ctct_0_description_metabox', [ $this, 'add_form_css' ] );

			add_action( 'cmb2_save_field', [ $this, 'override_save' ], 10, 4 );
			add_action( 'admin_notices', [ $this, 'admin_notice' ] );
			add_action( 'save_post', [ $this, 'save_post' ], 10, 2 );
		}

	}

	/**
	 * Get lists for dropdown option.
	 *
	 * @since 1.0.0
	 *
	 * @return array array of lists
	 */
	public function get_lists() {

		$lists     = constant_contact()->lists->get_lists();
		$get_lists = [];

		if ( $lists && is_array( $lists ) ) {

			foreach ( $lists as $list => $value ) {

				if ( ! empty( $list ) && ! empty( $value ) && 'new' !== $list ) {
					$get_lists[ $list ] = $value;
				}
			}
		}

		return $get_lists;
	}

	/**
	 * Custom CMB2 meta box css.
	 *
	 * @since 1.0.0
	 */
	public function add_form_css() {
		wp_enqueue_style( 'constant-contact-forms-admin' );
	}

	/**
	 * Hook into CMB2 save meta to check if email field has been added.
	 *
	 * @since 1.0.0
	 *
	 * @param string $field_id CMB2 Field id.
	 * @param object $updated CMB2 object representation of the updated data.
	 * @param string $action CMB2 action calling this.
	 * @param object $cmbobj CMB2 field object.
	 * @return void
	 */
	public function override_save( $field_id, $updated, $action, $cmbobj ) {

		global $post;

		if (
			isset( $post->ID ) &&
			$post->ID &&
			isset( $post->post_type ) &&
			$post->post_type &&
			'ctct_forms' === $post->post_type &&
			$cmbobj &&
			isset( $cmbobj->data_to_save ) &&
			isset( $cmbobj->data_to_save['custom_fields_group'] ) &&
			is_array( $cmbobj->data_to_save['custom_fields_group'] )
		) {

			update_post_meta( $post->ID, '_ctct_verify_key', wp_generate_password( 25, false ) );

			// We want to set our meta to false, as we'll want to loop through
			// and see if we should set it to true, but we want it to be false most
			// of the time.
			update_post_meta( $post->ID, '_ctct_has_email_field', 'false' );

			foreach ( $cmbobj->data_to_save['custom_fields_group'] as $data ) {

				if ( ( isset( $data['_ctct_map_select'] ) && 'email' === $data['_ctct_map_select'] ) || ! isset( $data['_ctct_map_select'] ) ) {
					update_post_meta( $post->ID, '_ctct_has_email_field', 'true' );
					return;
				}
			}
		}
	}

	/**
	 * Set admin notice if no email field.
	 *
	 * @since 1.0.0
	 */
	public function admin_notice() {

		global $post;

		if (
			$post &&
			isset( $post->ID ) &&
			isset( $post->post_type ) &&
			'ctct_forms' === $post->post_type &&
			isset( $post->post_status ) &&
			'auto-draft' !== $post->post_status
		) {
			$has_email = get_post_meta( $post->ID, '_ctct_has_email_field', true );

			if ( ! $has_email || 'false' === $has_email ) :
				?>
					<div id="ctct-no-email-error" class="notice notice-error ctct-no-email-error">
						<p><?php esc_html_e( 'Please add an email field to continue.', 'constant-contact-forms' ); ?></p>
					</div>
				<?php
			endif;

			$custom_fields          = get_post_meta( $post->ID, 'custom_fields_group', true );
			$custom_textareas_count = 0;

			if ( ! empty( $custom_fields ) && is_array( $custom_fields ) ) {

				foreach ( $custom_fields as $field ) {
					if ( 'custom_text_area' === $field['_ctct_map_select'] ) {
						$custom_textareas_count++;
					}
				}

				if ( $custom_textareas_count > 1 && constant_contact()->api->is_connected() ) :
					?>
						<div id="ctct-too-many-textareas" class="notice notice-warning">
							<p>
								<?php
									printf(
										/* Translators: Placeholders here are for `<strong>` and `<a>` HTML tags. */
										esc_html__( 'You have multiple %1$sCustom Text Area%2$s fields in this form. %1$sOnly the first field%2$s will be sent to Constant Contact. %3$sLearn More%4$s', 'constant-contact-forms' ),
										'<strong>',
										'</strong>',
										'<a id="ctct-open-textarea-info" href="#">',
										'</a>'
									);
								?>
							</p>
						</div>
					<?php
						$this->output_custom_textarea_modal();
				endif;
			}

			// phpcs:disable WordPress.Security.NonceVerification -- OK direct-accessing of $_GET.
			if ( isset( $_GET['ctct_not_connected'] ) && sanitize_text_field( wp_unslash( $_GET['ctct_not_connected'] ) ) ) {
				if ( ! constant_contact()->api->is_connected() ) {

					if ( ! get_option( 'ctct_first_form_modal_dismissed', false ) ) {
						$this->output_not_connected_modal( $post->ID );
					}
				}
			}
			// phpcs:enable WordPress.Security.NonceVerification
		}
	}

	/**
	 * On post save, see if we should trigger the not connected modal.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $post_id Post id.
	 * @param object $post    Post object.
	 */
	public function save_post( $post_id, $post ) {

		// Sanity checks to make sure it only applies to
		// what we want to deal with, which is saving a form
		// and not connected to constant contact.
		if (
			$post &&
			$post_id &&
			isset( $post->post_type ) &&
			'ctct_forms' === $post->post_type &&
			! wp_is_post_revision( $post ) &&
			! constant_contact()->api->is_connected()
		) {
			add_filter( 'redirect_post_location', [ $this, 'add_not_conn_query_arg' ], 99 );
		}
	}

	/**
	 * Return our query arg, and reomve our filter that we added before.
	 *
	 * @since 1.0.0
	 *
	 * @param string $location URL to add query args to.
	 * @return string
	 */
	public function add_not_conn_query_arg( $location ) {
		remove_filter( 'redirect_post_location', [ $this, 'add_notice_query_var' ], 99 );
		return add_query_arg( [ 'ctct_not_connected' => 'true' ], $location );
	}

	/**
	 * Gets our form title for our connect modal window.
	 *
	 * @since 1.0.0
	 *
	 * @return string Markup with form title.
	 */
	public function get_form_name_markup_for_modal() {

		global $post;

		if ( isset( $post->post_title ) ) {
			return esc_attr( $post->post_title );
		}
		return '';
	}

	/**
	 * Displays our not connected modal to the user.
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Added post_id parameter.
	 *
	 * @param int $post_id Post ID.
	 */
	public function output_not_connected_modal( $post_id = 0 ) {
		?>
			<div class="ctct-modal ctct-modal-open">

				<div class="ctct-modal-dialog" role="document">
					<div class="ctct-modal-content">
						<div class="ctct-modal-header">
							<a href="#" class="ctct-modal-close" aria-hidden="true">&times;</a>
							<h2><?php esc_html_e( 'Your first form is ready!', 'constant-contact-forms' ); ?></h2>
							<p>
								<?php
									printf(
										/* Translators: Placeholder will hold an example shortcode for a newly-created form. */
										esc_html__( 'Paste shortcode %1$s into a post or page editor.', 'constant-contact-forms' ),
										'<span class="displayed-shortcode">' . wp_kses_post( constant_contact_display_shortcode( $post_id ) ) . '</span>'
									);
								?>
							</p>
						</div>
						<div class="ctct-modal-body">
							<p class="now-what">
								<?php esc_html_e( 'Now, how would you like to manage the information you collect?', 'constant-contact-forms' ); ?>
							</p>
							<div class="ctct-modal-left">

								<?php // Empty alt tag OK; decorative image. ?>
								<img
									class="ctct-modal-flare"
									src="<?php echo esc_url_raw( $this->plugin->url . 'assets/images/question-mail.png' ); ?>"
									alt=""
								/>
								<h3><?php esc_attr_e( 'Try email marketing.', 'constant-contact-forms' ); ?></h3>
								<p>
									<?php esc_attr_e( 'Import everything into Constant Contact so I can see what email marketing can do for me.', 'constant-contact-forms' ); ?>
								</p>
								<a href="<?php echo esc_url_raw( add_query_arg( [ 'rmc' => 'wp_fmodal_try' ], constant_contact()->api->get_signup_link() ) ); ?>" target="_blank" class="button button-orange" title="<?php esc_attr_e( 'Try Us Free', 'constant-contact-forms' ); ?>"><?php esc_html_e( 'Try Us Free', 'constant-contact-forms' ); ?></a><br/>

								<?php // Empty alt tag OK; decorative image. ?>
								<img
									class="flare"
									src="<?php echo esc_url_raw( $this->plugin->url . 'assets/images/cc-modal-logo.png' ); ?>"
									alt=""
								/>
							</div>
							<div class="ctct-modal-right">

								<?php // Empty alt tag OK; decorative image. ?>
								<img
									class="ctct-modal-flare"
									src="<?php echo esc_url_raw( $this->plugin->url . 'assets/images/cc-login.png' ); ?>"
									alt=""
								/>
								<h3><?php esc_attr_e( 'Connect my account.', 'constant-contact-forms' ); ?></h3>
								<p>
									<?php esc_attr_e( 'Automatically add collected information to contacts in my Constant Contact account.', 'constant-contact-forms' ); ?>
								</p>
								<a href="<?php echo esc_url_raw( add_query_arg( [ 'rmc' => 'wp_fmodal_connect' ], constant_contact()->api->get_connect_link() ) ); ?>" target="_blank" class="button button-blue" title="<?php esc_attr_e( 'Connect Plugin', 'constant-contact-forms' ); ?>">
									<?php esc_attr_e( 'Connect Plugin', 'constant-contact-forms' ); ?>
								</a><br/>
								<p class="small"><small><?php esc_attr_e( 'By connecting, you authorize this plugin to access your account.', 'constant-contact-forms' ); ?></small></p>
							</div>
						</div><!-- modal body -->

						<div class="ctct-modal-footer">
							<p>
								<?php
									printf( '<a class="ctct-modal-close" href="#">%1$s</a>. %2$s',
										esc_attr__( 'I\'m all set', 'constant-contact-forms' ),
										esc_attr__( 'I\'ll manage the information on my own for now.', 'constant-contact-forms' )
									);
								?>
							</p>
						</div>

					</div><!-- .modal-content -->
				</div><!-- .modal-dialog -->
			</div>
		<?php
	}

	/**
	 * Outputs our modal for too many custom textareas information.
	 *
	 * @since 1.2.2
	 */
	public function output_custom_textarea_modal() {
		?>
			<div id="ctct-custom-textarea-modal" class="ctct-modal ctct-custom-textarea-modal">
				<div class="ctct-modal-dialog" role="document">
					<div class="ctct-modal-content">

						<div class="ctct-modal-header">
							<a href="#" class="ctct-modal-close" aria-hidden="true">&times;</a>
							<h2><?php esc_html_e( 'Custom Text Area limitations.', 'constant-contact-forms' ); ?></h2>
						</div>

						<div class="ctct-modal-body ctct-custom-textarea-modal-body ctct-custom-textarea">

							<div class="ctct-modal-left">
								<p>
									<?php
										printf(
											/* Translators: Placeholders here are for `<strong>` and `<em>` HTML tags. */
											esc_html__( 'Apologies&mdash;at this time, we can only upload %1$sone %2$sCustom Text Area%3$s field%4$s to your Constant Contact account per form submission. The uploaded field is placed into your contact\'s %1$sNotes%4$s field.', 'constant-contact-forms' ),
											'<strong>',
											'<em>',
											'</em>',
											'</strong>'
										);
									?>
								</p>

								<p>
									<?php
										printf(
											/* Translators: Placeholders here are for `<strong>` HTML tags. */
											esc_html__( 'The first listed %1$sCustom Text Area%2$s field is sent to Constant Contact.', 'constant-contact-forms' ),
											'<strong>',
											'</strong>'
										);
									?>
								</p>

								<p>
									<?php
										printf(
											/* Translators: Placeholders here are for `<strong>` HTML tags. */
											esc_html__( 'Subsequent %1$sCustom Text Area%2$s fields are only sent with the admin email when the form is submitted, and not to your Constant Contact account.', 'constant-contact-forms' ),
											'<strong>',
											'</strong>'
										);
									?>
								</p>
							</div>

							<div class="ctct-modal-right">
								<?php // Empty alt tag OK; decorative image. ?>
								<img src="<?php echo esc_url_raw( $this->plugin->url . 'assets/images/fields-image.png' ); ?>" alt="" />
							</div>

						</div><!-- modal body -->
					</div><!-- .modal-content -->
				</div><!-- .modal-dialog -->
			</div>
		<?php
	}
}

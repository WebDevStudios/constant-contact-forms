<?php
/**
 * @package ConstantContact
 * @subpackage Builder
 * @author Constant Contact
 * @since 1.0.0
 */

/**
 * Main class for dealing with our form builder functionality.
 */
class ConstantContact_Builder {

	/**
	 * Parent plugin class
	 *
	 * @var   class
	 * @since 0.0.1
	 */
	protected $plugin = null;

	/**
	 * Prefix for our meta fields / boxes
	 * @var  string
	 */
	public $prefix = '_ctct_';

	/**
	 * Constructor
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'init', array( $this, 'hooks' ) );
	}

	/**
	 * Initiate our hooks
	 *
	 * @since 1.0.0
	 */
	public function hooks() {
		global $pagenow;

		/** This filter is documented in includes/class-buider-fields.php */
		$form_builder_pages = apply_filters(
			'constant_contact_form_builder_pages',
			array( 'post-new.php', 'post.php' )
		);

		// Only load the cmb2 fields on our specified pages
		if ( in_array( $pagenow, $form_builder_pages, true ) ) {

			add_action( 'cmb2_after_post_form_ctct_0_description_metabox', array( $this, 'add_form_css' ) );

			add_action( 'cmb2_save_field', array( $this, 'override_save' ), 10, 4 );
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );
			add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
		}

	}

	/**
	 * Get lists for dropdown option
	 *
	 * @since  1.0.0
	 * @return array array of lists
	 */
	public function get_lists() {

		// Grab our lists
		$lists = constant_contact()->lists->get_lists();

		// Data verification
		if ( $lists && is_array( $lists ) ) {

			// Loop though our lists
			foreach ( $lists as $list => $value ) {

				// Make sure we have something to use as a key and a value,
				// and that we don't overwrite our 'new' value we set before
				if ( ! empty( $list ) && ! empty( $value ) && 'new' !== $list ) {
					$get_lists[ $list ] = $value;
				}
			}

			// Return those lists
			return $get_lists;
		}

		// If we got this far, we didn't get any lists
		return array();
	}

	/**
	 * Custom CMB2 meta box css
	 *
	 * @since  1.0.0
	 */
	public function add_form_css() {

		// Let's style this thing
		wp_enqueue_style( 'constant-contact-forms' );
	}

	/**
	 * Hook into CMB2 save meta to check if email field has been added
	 *
	 * @since  1.0.0
	 * @param  string $field_id CMB2 Field id.
	 * @param  object $updated
	 * @param  string $action
	 * @param  object $cmbobj   CMB2 field object
	 * @return void
	 */
	public function override_save( $field_id, $updated, $action, $cmbobj ) {

		// Hey $post nice to see you
		global $post;

		// Do all our existence checks
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

			// Save post meta with a random key that we can verify later
			update_post_meta( $post->ID, '_ctct_verify_key', wp_generate_password( 25, false ) );

			// We want to set our meta to false, as we'll want to loop through
			// and see if we should set it to true, but we want it to be false most
			// of the time
			update_post_meta( $post->ID, '_ctct_has_email_field', 'false' );

			// Loop through all of our custom fields group fields
			foreach ( $cmbobj->data_to_save['custom_fields_group'] as $data ) {

				// If we have a an email field set in our map select:
				if ( ( isset( $data['_ctct_map_select'] ) && 'email' === $data['_ctct_map_select'] ) || ! isset( $data['_ctct_map_select'] ) ) {

					// update our post meta to mark that we have email
					update_post_meta( $post->ID, '_ctct_has_email_field', 'true' );

					// bail out, more than one email fields are fine, but we know we have at least one
					break;
				}
			}
		}
	}

	/**
	 * Set admin notice if no email field
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function admin_notice() {

		global $post;

		// data verification
		if (
			$post &&
			isset( $post->ID ) &&
			isset( $post->post_type ) &&
			'ctct_forms' === $post->post_type &&
			isset( $post->post_status ) &&
			'auto-draft' !== $post->post_status
		) {

			// Check to see if we have an email set on our field
			$has_email = get_post_meta( $post->ID, '_ctct_has_email_field', true );

			// If we don't have an email, then display our admin notice to the user
			if ( ! $has_email || 'false' === $has_email ) {
				echo '<div id="ctct-no-email-error" class="notice notice-error ctct-no-email-error"><p>';
				esc_attr_e( 'Please add an email field to continue.', 'constant-contact-forms' );
				echo '</p></div>';
			}

			// Check for our query arg
			if ( isset( $_GET['ctct_not_connected'] ) && sanitize_text_field( wp_unslash( $_GET['ctct_not_connected'] ) ) ) { //Input var okay.

				// Double check that we're not connected
				if ( ! constant_contact()->api->is_connected() ) {

					// See if we dismissed the modal, if not, show it
					if ( ! get_option( 'ctct_first_form_modal_dismissed', false ) ) {

						// Show our modal
						$this->output_not_connected_modal();
					}
				}
			}
		}
	}

	/**
	 * On post save, see if we should trigger the not connected modal
	 *
	 * @since   1.0.0
	 * @param   int  $post_id  post id
	 * @param   object  $post     post object
	 * @return  void
	 */
	public function save_post( $post_id, $post ) {

		// Sanity checks to make sure it only applies to
		// what we want to deal with, which is saving a form
		// and not connected to constant contact
		if (
			$post &&
			$post_id &&
			isset( $post->post_type ) &&
			'ctct_forms' === $post->post_type &&
			! wp_is_post_revision( $post ) &&
			! constant_contact()->api->is_connected()
		) {
			// Inject in a query arg that we can read later
			add_filter( 'redirect_post_location', array( $this, 'add_not_conn_query_arg' ), 99 );
		}
	}

	/**
	 * Return our query arg, and reomve our filter that we added before
	 *
	 * @since   1.0.0
	 */
	public function add_not_conn_query_arg( $location ) {

		// Remove our filter that we added before
		remove_filter( 'redirect_post_location', array( $this, 'add_notice_query_var' ), 99 );

		// Inject in our query arg
		return add_query_arg( array( 'ctct_not_connected' => 'true' ), $location );
	}

	/**
	 * Gets our form title for our connect modal window
	 *
	 * @since   1.0.0
	 * @return  string  markup with form title
	 */
	public function get_form_name_markup_for_modal() {

		// Get the post object
		global $post;

		// If we have a post title set, use that for our modal
		if ( isset( $post->post_title ) ) {
			return esc_attr( $post->post_title );
		}
	}

	/**
	 * Displays our not connected modal to the user
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function output_not_connected_modal() {

		// output markup of non connected modal here ?>
		<div class="ctct-modal ctct-modal-open">

			<?php // modal header ?>
			<div class="ctct-modal-dialog" role="document">
				<div class="ctct-modal-content">
					<div class="ctct-modal-header">
						<a href="#" class="ctct-modal-close" aria-hidden="true">&times;</a>
						<h2><?php esc_attr_e( 'Your first form is ready!', 'constant-contact-forms' ); ?></h2>
						<p>
						<?php esc_html_e( 'Now, how would you like to manage the information you collect?', 'constant-contact-forms' ); ?>
						</p>
					</div>
					<div class="ctct-modal-body">
						<div class="ctct-modal-left">
							<img
								class="ctct-modal-flare"
								src="<?php echo esc_url_raw( $this->plugin->url . 'assets/images/question-mail.png' ); ?>"
								alt="<?php echo esc_attr_x( '? mail', 'email marketing alt text', 'constant-contact-forms' ); ?>"
							/>
							<h3><?php esc_attr_e( 'Try email marketing.', 'constant-contact-forms' ); ?></h3>
							<p>
								<?php esc_attr_e( 'Import everything into Constant Contact so I can see what email marketing can do for me.', 'constant-contact-forms' ); ?>
							</p>
							<a href="<?php echo esc_url_raw( constant_contact()->api->get_signup_link() ); ?>" target="_blank" class="button button-orange" title="Try Us Free">Try Us Free</a><br/>
							<img
								class="flare"
								src="<?php echo esc_url_raw( $this->plugin->url . 'assets/images/cc-modal-logo.png' ); ?>"
								alt="<?php esc_attr_e( 'Constant Contact', 'constant-contact-forms' ); ?>"
							/>
						</div>
						<div class="ctct-modal-right">
							<img
								class="ctct-modal-flare"
								src="<?php echo esc_url_raw( $this->plugin->url . 'assets/images/cc-login.png' ); ?>"
								alt="<?php echo esc_attr_x( 'hand holding phone', 'connect alt text', 'constant-contact-forms' ); ?>"
							/>
							<h3><?php esc_attr_e( 'Connect my account.', 'constant-contact-forms' ); ?></h3>
							<p>
								<?php esc_attr_e( 'Automatically add collected information to contacts in my Constant Contact account.', 'constant-contact-forms' ); ?>
							</p>
							<a href="<?php echo esc_url_raw( constant_contact()->api->get_connect_link() ); ?>" target="_blank" class="button button-blue" title="Connect Plugin">
								<?php esc_attr_e( 'Connect Plugin', 'constant-contact-forms' ); ?>
							</a><br/>
							<p class="small"><small><?php esc_attr_e( 'By connecting, you authorize this
							plugin to access your account.', 'constant-contact-forms' ); ?></small></p>
						</div>
					</div><!-- modal body -->
					<div class="ctct-modal-footer">
						<p><?php
							printf( '<a class="ctct-modal-close" href="%s">%s</a>. %s',
								'#',
								esc_attr__( "I'm all set", 'constant-contact-forms' ),
								esc_attr__( "I'll manage the information on my own for now.", 'constant-contact-forms' )
							);
						?></p>
					</div>
				</div><!-- .modal-content -->
			</div><!-- .modal-dialog -->
		</div>
	<?php }
}

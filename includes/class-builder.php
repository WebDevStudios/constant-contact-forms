<?php
/**
 * ConstantContact_Builder form Builder Settings
 *
 * @package ConstantContact_Builder
 * @author Pluginize
 * @since 1.0.0
 */

/**
 * ConstantContact_Builder
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

		// Allow filtering the pages to load form build actions
		$form_builder_pages = apply_filters(
			'constant_contact_form_builder_pages',
			array( 'post-new.php', 'post.php' )
		);

		// Only load the cmb2 fields on our specified pages
		if ( in_array( $pagenow, $form_builder_pages, true ) ) {

			add_action( 'cmb2_admin_init', array( $this, 'description_metabox' ) );
			add_action( 'cmb2_admin_init', array( $this, 'fields_metabox' ) );
			add_action( 'cmb2_admin_init', array( $this, 'options_metabox' ) );
			add_action( 'cmb2_after_post_form_ctct_description_metabox', array( $this, 'add_form_css' ) );

			add_action( 'cmb2_save_field', array( $this, 'override_save' ), 10, 4 );
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );
			add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
		}

	}

	/**
	 * Form description CMB2 metabox
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function description_metabox() {

		$prefix = '_ctct_';

		/**
		 * Initiate the $description_metabox
		 */
		$description_metabox = new_cmb2_box( array(
			'id'			=> 'ctct_description_metabox',
			'title'		 	=> __( 'Form Description', 'constantcontact' ),
			'object_types'  => array( 'ctct_forms' ),
			'context'	   	=> 'normal',
			'priority'	  	=> 'high',
			'show_names'	=> true,
		) );

		$description_metabox->add_field( array(
			'description' => __( 'This message will display above the form fields, so use it as an opportunity to pitch your email list. Tell visitors why they should subscribe to your emails, focusing on benefits like insider tips, discounts, subscriber coupons, and more.', 'constantcontact' ),
			'id'   => $prefix . 'description',
			'type' => 'wysiwyg',
			'options' => array(
				'media_buttons' => false,
				'textarea_rows' => '10',
				'teeny'         => false,
			),
		) );
	}


	/**
	 * Fields builder CMB2 metabox
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function fields_metabox() {

		$prefix = '_ctct_';

		/**
		 * Initiate the $fields_metabox
		 */
		$fields_metabox = new_cmb2_box( array(
			'id'			=> 'ctct_fields_metabox',
			'title'		 	=> __( 'Form Fields', 'constantcontact' ),
			'object_types'  => array( 'ctct_forms' ),
			'context'	   	=> 'normal',
			'priority'	  	=> 'high',
			'show_names'	=> true,
		) );

		// Custom CMB2 fields.
		$fields_metabox->add_field( array(
			'name'        => __( 'Add Fields', 'constantcontact' ),
			'description' => __( 'Create a field for each piece of information you want to collect. Good basics include email address, first name, and last name. You can also collect birthday and anniversary dates to use with Constant Contact autoresponders! ', 'constantcontact' ),
			'id'          => $prefix . 'title',
			'type'        => 'title',
		) );

		// Default fields.
		$custom_group = $fields_metabox->add_field( array(
			'id'         => 'custom_fields_group',
			'type'       => 'group',
			'repeatable' => true,
			'options'    => array(
				'group_title'   => __( 'Field {#}', 'constantcontact' ),
				'add_button'    => __( 'Add Another Field', 'constantcontact' ),
				'remove_button' => __( 'Remove Field', 'constantcontact' ),
				'sortable'      => true,
			),
		) );

		$fields_metabox->add_group_field( $custom_group, array(
			'name'    => __( 'Field Label', 'constantcontact' ),
			'id'      => $prefix . 'field_label',
			'type'    => 'text',
			'default' => __( 'Email', 'constantcontact' ),
		) );

		$fields_metabox->add_group_field( $custom_group, array(
			'name'    => __( 'Field Description', 'constantcontact' ),
			'id'      => $prefix . 'field_desc',
			'type'    => 'text',
			'default' => '',
		) );

		$default_fields = apply_filters( 'constant_contact_field_types', array(
			'email'            => __( 'Email (required)', 'constantcontact' ),
			'first_name'       => __( 'First Name', 'constantcontact' ),
			'last_name'        => __( 'Last Name', 'constantcontact' ),
			'phone_number'     => __( 'Phone Number', 'constantcontact' ),
			'address'          => __( 'Address', 'constantcontact' ),
			'job_title'        => __( 'Job Title', 'constantcontact' ),
			'company'          => __( 'Company', 'constantcontact' ),
			'website'          => __( 'Website', 'constantcontact' ),
			'birthday'         => __( 'Birthday', 'constantcontact' ),
			'anniversary'      => __( 'Anniversary', 'constantcontact' ),
			'custom'           => __( 'Custom Text Field', 'constantcontact' ),
			'custom_text_area' => __( 'Custom Text Area', 'constantcontact' ),
		) );

		$fields_metabox->add_group_field( $custom_group, array(
			'name'             => __( 'Select a Field', 'constantcontact' ),
			'id'               => $prefix . 'map_select',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => 'email',
			'row_classes'      => 'map',
			'options'          => $default_fields,
		) );

		$fields_metabox->add_group_field( $custom_group, array(
			'name'        => __( 'Required', 'constantcontact' ),
			'id'          => $prefix . 'required_field',
			'type'        => 'checkbox',
			'row_classes' => 'required',
		) );

	}

	/**
	 * Form options CMB2 metabox
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function options_metabox() {

		$prefix = '_ctct_';

		/**
		 * Initiate the $options_metabox
		 */
		$options_metabox = new_cmb2_box( array(
			'id'			=> 'ctct_options_metabox',
			'title'		 	=> __( 'Form Options', 'constantcontact' ),
			'object_types'  => array( 'ctct_forms' ),
			'context'	   	=> 'normal',
			'priority'	  	=> 'high',
			'show_names'	=> true,
		) );

		$options_metabox->add_field( array(
			'name'        => __( 'Enable Opt-in checkbox', 'constantcontact' ),
			'id'          => $prefix . 'opt_in',
			'description' => __( 'Show opt-in checkbox to allow visitors to opt-in to your email list.', 'constantcontact' ),
			'type'        => 'checkbox',
		) );

		// Add field if conncted to API.
		if ( $lists = $this->get_lists() ) {

			$options_metabox->add_field( array(
				'name'             => __( 'Add subscribers to', 'constantcontact' ),
				'id'               => $prefix . 'list',
				'type'             => 'select',
				'show_option_none' => true,
				'default'          => 'none',
				'options'          => $lists,
			) );

		}

		$options_metabox->add_field( array(
			'name'        => __( 'Default state', 'constantcontact' ),
			'id'          => $prefix . 'opt_in_default',
			'description' => __( 'By default, show opt-in checkbox as checked.', 'constantcontact' ),
			'type'        => 'checkbox',
		) );

		$options_metabox->add_field( array(
			'name'        => __( 'Hide checkbox', 'constantcontact' ),
			'id'          => $prefix . 'opt_in_hide',
			'description' => __( 'Hide the checkbox, and use default value (usually when used with a simple newsletter sign-up form).', 'constantcontact' ),
			'type'        => 'checkbox',
		) );

		// Get our site name, and if we don't have it, then use a placeholder
		$business_name = get_bloginfo( 'name' );
		$business_name ? $business_name : __( 'Your Business Name', 'constantcontact' );

		$options_metabox->add_field( array(
			'name'        => __( 'Opt-in Affirmation', 'constantcontact' ),
			'id'          => $prefix . 'opt_in_instructions',
			'type'        => 'textarea_small',
			'default'     => sprintf( __( 'Yes, I would like to receive emails from %s', 'constantcontact' ), $business_name ),
		) );

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
				echo '<div class="notice notice-error"><p>';
				esc_attr_e( 'Please add an email field to continue.', 'constantcontact' );
				echo '</p></div>';
			}

			// Check for our query arg
			if ( isset( $_GET['ctct_not_connected'] ) && $_GET['ctct_not_connected'] ) {

				// Double check that we're not connected
				if ( ! constant_contact()->api->is_connected() ) {

					// Show our modal
					$this->output_not_connected_modal();
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
			return '<strong>' . esc_attr( $post->post_title ) . '</strong>';
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
		<div class="modal modal-open">

			<?php // modal header ?>
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<a href="#close" class="modal-close" aria-hidden="true">&times;</a>
						<h2><?php esc_attr_e( 'Congratulations on your first form!', 'constantcontact' ); ?></h2>
						<p>
						<?php printf( __( 'Now, how do you want to manage your %s form contacts?', 'constantcontact' ), $this->get_form_name_markup_for_modal() ); ?>
						</p>
					</div>
					<div class="modal-body">
						<div class="left">
							<img class="flare" src="<?php echo esc_url_raw( $this->plugin->url . 'assets/images/question-mail.png' ); ?>" alt="? mail" />
							<h3><?php esc_attr_e( 'Try email marketing.', 'constantcontact' ); ?></h3>
							<p>
								<?php esc_attr_e( 'Import everything into constant Contact so I can see what email marketing can do for me.' , 'constantcontact' ); ?>
							</p>
							<a href="#" class="button button-orange" title="Try Us Free">Try Us Free</a><br/>
							<img src="<?php echo esc_url_raw( $this->plugin->url . 'assets/images/cc-modal-logo.png' ); ?>" alt="Constant Contact" />
						</div>
						<div class="right">
							<img class="flare" src="<?php echo esc_url_raw( $this->plugin->url . 'assets/images/cc-login.png' ); ?>" alt="hand holding phone" />
							<h3><?php esc_attr_e( 'Connect my account.', 'constantcontact' ); ?></h3>
							<p>
								<?php esc_attr_e( 'Automatically add collected information to contacts in my Constant Contact account', 'constantcontact' ); ?>
							</p>
							<a href="#" class="button button-blue" title="Connect Plugin">Connect Plugin</a><br/>
							<p class="small"><small><?php esc_attr_e( 'By connecting you authorize this
							plugin to access your account.', 'constantcontact' ); ?></small></p>
						</div>
					</div><!-- modal body -->
					<div class="modal-footer">
						<p>
							<a href="#">I'm all set.</a> I’ll manage the information on my own for now.
						</p>
					</div>
				</div><!-- .modal-content -->
			</div><!-- .modal-dialog -->
		</div>
	<?php }
}

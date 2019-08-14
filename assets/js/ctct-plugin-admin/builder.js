window.CTCTBuilder = {};

( function( window, $, that ) {

	/**
	 * @constructor
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	that.init = () => {

		// If we do actually have an email field set, then remove our error.
		if ( $( '#cmb2-metabox-ctct_2_fields_metabox option[value="email"]:selected' ).length ) {
			$( '#ctct-no-email-error' ).remove();
		}

		// Cache it all.
		that.cache();

		// Bind our events.
		that.bindEvents();

		// Bind our select dropdown events.
		that.selectBinds();

		// Trigger any field modifications we need to do.
		that.modifyFields();

		// Make description non-draggable, so we don't run into weird cmb2 issues.
		$( '#ctct_0_description_metabox h2.hndle' ).removeClass( 'ui-sortable-handle, hndle' );

		// Inject our new labels for the up/down CMB2 buttons, so they can be properly localized.
		// Because we're using :after, we can't use .css() to do this, we need to inject a style tag.
		$( 'head' ).append( '<style> #cmb2-metabox-ctct_2_fields_metabox a.move-up::after { content: "' + window.ctctTexts.move_up + '" } #cmb2-metabox-ctct_2_fields_metabox a.move-down::after { content: "' + window.ctctTexts.move_down + '" }</style>' );
	};

	/**
	 * Cache DOM elements.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	that.cache = () => {

		that.$c = {
			window: $( window ),
			body: $( 'body' )
		};

		that.isLeaveWarningBound = false;
	};

	// Triggers our leave warning if we modify things in the form.
	that.bindLeaveWarning = () => {

		// Don't double-bind it.
		if ( ! that.isLeaveWarningBound ) {

			// Bind our error that displays before leaving page.
			$( window ).bind( 'beforeunload', () => {
				return window.ctctTexts.leavewarning;
			} );

			// Save our state.
			that.isLeaveWarningBound = true;
		}
	};

	/**
	 * Removes our binding of our leave warning.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	that.unbindLeaveWarning = () => {
		$( window ).unbind( 'beforeunload' );
	};

	/**
	 * Attach callbacks to events.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	that.bindEvents = () => {

		$( '#post' ).submit( () => {

			// Make sure our email dropdown reverts from disbled, as CMB2 doesn't save those values.
			$( '.ctct-email-disabled' ).removeClass( 'disabled' ).prop( 'disabled', false );

			that.unbindLeaveWarning();
		} );

		$( '.cmb2-wrap input, .cmb2-wrap textarea' ).on( 'input', () => {
			if ( 'undefined' !== typeof( tinyMCE ) ) {
				that.bindLeaveWarning();
			}
		} );

		// Disable email options on row change trigger.
		$( document ).on( 'cmb2_shift_rows_complete', () => {
			that.modifyFields();
			that.bindLeaveWarning();
			that.removeDuplicateMappings();
		} );

		// If we get a row added, then do our stuff.
		$( document ).on( 'cmb2_add_row', ( newRow ) => { // eslint-disable-line no-unused-vars

			// Automatically set new rows to be 'custom' field type.
			$( '#custom_fields_group_repeat .postbox' ).last().find( '.map select' ).val( 'none' );

			that.modifyFields();
			that.selectBinds();
			that.removeDuplicateMappings();
		} );

		that.removeDuplicateMappings();

		$( '#ctct-reset-css' ).on( 'click', ( event ) => {
			event.preventDefault();

			var selectFields = [
				'#_ctct_form_description_font_size',
				'#_ctct_form_submit_button_font_size',
				'#_ctct_form_label_placement'
			];

			var textFields = [
				'#_ctct_form_padding_top',
				'#_ctct_form_padding_bottom',
				'#_ctct_form_padding_left',
				'#_ctct_form_padding_right',
				'#_ctct_input_custom_classes'
			];

			// Reset color pickers.
			$( '.wp-picker-clear' ).each( function() {
				$( this ).click();
			} );

			for ( var i = selectFields.length; i--; ) {
				var firstOption = $( selectFields[i] ).children( 'option' ).first();
				$( selectFields[i] ).val( firstOption.val() );
			}

			for ( var i = textFields.length; i--; ) {
				$( textFields[i] ).val( '' );
			}
		} );
	};

	/**
	 * When .cmb2_select <selects> get changed, do some actions.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	that.selectBinds = () => {

		// For each fields select.
		$( '#cmb2-metabox-ctct_2_fields_metabox .cmb2_select' ).change( () => {

			// Modify our fields.
			that.modifyFields();

			// Don't allow duplicate mappings in form.
			that.removeDuplicateMappings();

			// Bind our leave warning.
			that.bindLeaveWarning();
		} );
	};

	/**
	 * We need to manipulate our form builder a bit. We do this here.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	that.modifyFields = () => {

		// Set that we haven't found an email.
		var foundEmail = false;

		// Loop through all fields to modify them.
		$( '#cmb2-metabox-ctct_2_fields_metabox #custom_fields_group_repeat .cmb-repeatable-grouping' ).each( function( key, value ) {

			// Set some of our helper paramaters.
			var $fieldParent = $( this ).find( '.cmb-field-list' );
			var $button       = $( $fieldParent ).find( '.cmb-remove-group-row' );
			var $required     = $( $fieldParent ).find( '.required input[type=checkbox]' );
			var $requiredRow  = $required.closest( '.cmb-row' );
			var $map          = $( $fieldParent ).find( '.map select option:selected' );
			var $mapName      = $map.text();
			var $fieldTitle   = $( this ).find( 'h3' );
			var $labelField   = $( this ).find( 'input[name*="_ctct_field_label"]' );
			var $descField    = $( this ).find( 'input[name*="_ctct_field_desc"]' );

			// Set our field row to be the name of the selected option.
			$fieldTitle.text( $mapName );

			// If we have a blank field label, then use the name of the field to fill it in.
			if ( 0 === $labelField.val().length ) {
				$labelField.val( $mapName ).addClass( 'ctct-label-filled' );
			} else {
				$labelField.addClass( 'ctct-label-filled' );
			}

			// If we haven't yet found an email field, and this is our email field.
			if ( ! foundEmail && ( 'email' === $( $map ).val() ) ) {

				// Set that we found an email field.
				foundEmail = true;

				// Make it required.
				$required.prop( 'checked', true );

				// Set it to be 'disabled'.
				$( value ).find( 'select' ).addClass( 'disabled ctct-email-disabled' ).prop( 'disabled', true );

				// Hide the required row.
				$requiredRow.hide();

				// Hide the remove row button.
				$button.hide();

			} else {

				// Verify its not disabled.
				$( value ).find( 'select' ).removeClass( 'disabled ctct-email-disabled' ).prop( 'disabled', false );

				// If we're not an email field, reshow the required field.
				$requiredRow.show();

				// and the remove button.
				$button.show();
			}

			// Set the placeholder text if there's something to set.
			if ( window.ctct_admin_placeholders ) {
				var placeholder = window.ctct_admin_placeholders[ $( value ).find( 'select' ).val() ];

				// If we have a valid placeholder, display it or try the fallback.
				if ( placeholder && placeholder.length && $descField.length ) {
					$descField.attr( 'placeholder', 'Example: ' + placeholder );
				} else if ( window.ctct_admin_placeholders.default ) {
					$descField.attr( 'placeholder', window.ctct_admin_placeholders.default );
				}
			}
		} );
	};

	/**
	 * Go through all dropdowns, and remove used options.
	 *
	 * @author Constant Contact
	 * @since 1.0.0
	 */
	that.removeDuplicateMappings = () => {

		var usedMappings = [];
		var dropdowns    = '#cmb2-metabox-ctct_2_fields_metabox #custom_fields_group_repeat .cmb-repeatable-grouping select';
		var $dropdowns   = $( dropdowns );

		// For each dropdown, build up our array of used values.
		$dropdowns.each( function( key, value ) {
			usedMappings.push( $( value ).val() );
		} );

		// Re-show all the children options we may have hidden.
		$dropdowns.children().show();

		// For each of our mappings that we already have, remove them from all selects.
		usedMappings.forEach( function( value ) {

			// But only do it if the value isn't one of our custom ones.
			if ( ( 'custom_text_area' !== value ) && ( 'custom' !== value ) ) {

				// Remove all options from our dropdowns with the value.
				$( dropdowns + ' option[value=' + value + ']:not( :selected )' ).hide();
			}
		} );
	};

	$( that.init );

} ( window, jQuery, window.CTCTBuilder ) );

window.CTCTBuilder = {};
( function( window, $, that ) {

	// Constructor.
	that.init = function() {
		that.cache();
		that.bindEvents();
	}

	// Cache all the things.
	that.cache = function() {

		that.$c = {
			window: $( window ),
			body:   $( 'body' ),
			hide:   '.cmb2-id--ctct-new-list',
		};

		that.$c.optinfields = {
			list     : $( '.cmb2-id--ctct-list' ),
			default  : $( '.cmb2-id--ctct-opt-in-default' ),
			hide     : $( '.cmb2-id--ctct-opt-in-hide' ),
			instruct : $( '.cmb2-id--ctct-opt-in-instructions' ),
		}
	}

	// Combine all events.
	that.bindEvents = function() {

		// Disable email options on row change trigger.
		$( document ).on( 'cmb2_shift_rows_complete', function() {
			that.modifyFields();
		});

		// If we get a row added, then do our stuff
		$( document ).on( 'cmb2_add_row', function() {

			// Automatically set new rows to be 'custom' field type
			$( '#custom_fields_group_repeat .postbox' ).last().find( '.map select' ).val( 'custom' );

			// Modfiy the field we need to modify
			that.modifyFields();
		});

		// If we modify the opt in checkbox, then toggle fields if we have to
		$( '#_ctct_opt_in' ).change( function() {
			that.toggleOptInFields();
		});

		// On load, toggle our optin fields and run our new row
		// functionality to apply to all saved values
		that.modifyFields();
		that.toggleOptInFields();
    }

    // Toggle un-needed optin fields if we're not showing the opt-in
    that.toggleOptInFields = function() {

    	// If checked, show them
    	if ( $( '#_ctct_opt_in' ).prop( 'checked' ) ) {
    		that.$c.optinfields.list.show();
    		that.$c.optinfields.default.show();
    		that.$c.optinfields.hide.show();
    		that.$c.optinfields.instruct.show();
    	} else {
    		that.$c.optinfields.list.hide();
    		that.$c.optinfields.default.hide();
    		that.$c.optinfields.hide.hide();
    		that.$c.optinfields.instruct.hide();
    	}
    }

	// Disable required email fields.
	that.modifyFields = function() {

		// Set that we haven't found an email
		var foundEmail = false;

		// Loop through all fields to modify them
		$( '#cmb2-metabox-ctct_fields_metabox #custom_fields_group_repeat .cmb-repeatable-grouping' ).each( function( key, value ) {

			// Set some of our helper paramaters
			var $field_parent = $( this ).find( '.cmb-field-list' );
			var $button       = $( $field_parent ).find( '.cmb-remove-group-row' );
			var $required     = $( $field_parent ).find( '.required input[type=checkbox]' );
			var $requiredRow  = $required.closest( '.cmb-row' );
			var $map          = $( $field_parent ).find( '.map select option:selected' );
			var $mapName      = $map.text();
			var $fieldTitle   = $( this ).find( 'h3' );

			// Set our field row to be the name of the selected option
			$fieldTitle.text( $mapName );

			// If we haven't yet found an email field, and this is our email field
			if ( ! foundEmail && ( 'email' === $( $map ).val() ) ) {

				// Set that we found an email field
				foundEmail = true;

				// Make it required
				$required.prop( 'checked', true );

				// Set it to be 'disabled'
				$( value ).find( 'select' ).addClass( 'disabled' );

				// Hide the required row
				$requiredRow.hide();

				// Hide the remove row button
				$button.hide();

			} else {

				// Verify its not disabled
				$( value ).find( 'select' ).removeClass( 'disabled' );

				// If we're not an email field, reshow the required field
				$requiredRow.show();

				// and the remove button
				$button.show();
			}

		});
	}

	// Engage!
	$( that.init );

})( window, jQuery, window.CTCTBuilder );

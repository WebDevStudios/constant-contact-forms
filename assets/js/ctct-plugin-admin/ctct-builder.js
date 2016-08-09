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

		that.isLeaveWarningBound = false;
	}

	// Triggers our leave warning if we modify things in the form
	that.bindLeaveWarning = function() {

		// Don't double-bind it
		if ( ! that.isLeaveWarningBound ) {

			$( window ).bind( 'beforeunload', function(){
				return ctct_texts.leavewarning;
			});

			// Save our state
			that.isLeaveWarningBound = true;
		}
	}

	// Removes our binding of our leavce warning
	that.unbindLeaveWarning = function() {
		$( window ).unbind( 'beforeunload' );
	}

	// Combine all events.
	that.bindEvents = function() {

		// Trigger before saving post
		$( '#post' ).submit( function () {

			// Make sure our email dropdown reverts from disbled, as CMB2 doesn't save those values
			$( '.ctct-email-disabled' ).removeClass( 'disabled' ).prop( 'disabled', false );

			// Unbind our leave warning, so we don't trigger it when we shouldn't.
			that.unbindLeaveWarning();
		});

		// On cmb2 select chnages, fire our leave warning function
		$( '.cmb2-wrap input, .cmb2-wrap textarea' ).on( 'input', function() {

			// Bind our leave warning
			that.bindLeaveWarning();
		});

		// Disable email options on row change trigger.
		$( document ).on( 'cmb2_shift_rows_complete', function() {

			// Fire our field modifications function
			// functionality to apply to all saved values
			that.modifyFields();

			// Bind our leave warning
			that.bindLeaveWarning();
		});

		// If we get a row added, then do our stuff
		$( document ).on( 'cmb2_add_row', function( newRow ) {

			// Automatically set new rows to be 'custom' field type
			$( '#custom_fields_group_repeat .postbox' ).last().find( '.map select' ).val( 'custom' );

			// Trigger bind events again for our selects, as well as our field changes
			that.modifyFields();
			that.selectBinds();
		});


		// On load, toggle our optin fields and run our new row
		// functionality to apply to all saved values, bind our select
		// functionality, and don't allow duplicate mappings in form
		that.modifyFields();
		that.selectBinds();
		that.removeDuplicateMappings();

		// Make description non-draggable, so we don't run into weird cmb2 issues
		$( '#ctct_description_metabox h2.hndle' ).removeClass( 'ui-sortable-handle, hndle' );

    }

    that.selectBinds = function() {
    	// On cmb2 select chaages, fire our modify fields function
    	$( '.cmb2_select' ).change( function() {

    		// Modify our fields
    		that.modifyFields();

    		// Don't allow duplicate mappings in form
    		that.removeDuplicateMappings();

    		// Bind our leave warning
    		that.bindLeaveWarning();
    	});
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
			var $labelField   = $( this ).find( "input[name*='_ctct_field_label']" )

			// Set our field row to be the name of the selected option
			$fieldTitle.text( $mapName );

			// If we have a blank field label, then use the name of the field to fill it in
			if ( ( $labelField.val().length === 0 ) || $labelField.hasClass( 'ctct-label-filled' ) ) {
				$labelField.val( $mapName ).addClass( 'ctct-label-filled' );
			}

			// If we haven't yet found an email field, and this is our email field
			if ( ! foundEmail && ( 'email' === $( $map ).val() ) ) {

				// Set that we found an email field
				foundEmail = true;

				// Make it required
				$required.prop( 'checked', true );

				// Set it to be 'disabled'
				$( value ).find( 'select' ).addClass( 'disabled ctct-email-disabled' ).prop( 'disabled', true );

				// Hide the required row
				$requiredRow.hide();

				// Hide the remove row button
				$button.hide();

			} else {

				// Verify its not disabled
				$( value ).find( 'select' ).removeClass( 'disabled ctct-email-disabled' ).prop( 'disabled', false );

				// If we're not an email field, reshow the required field
				$requiredRow.show();

				// and the remove button
				$button.show();
			}

		});
	}

	// Go through all dropdowns, and remove used options
	that.removeDuplicateMappings = function() {

		// Set up an array for our mappings
		var usedMappings = [];

		// Get all our dropdowns on the page
		var dropdowns = '#cmb2-metabox-ctct_fields_metabox #custom_fields_group_repeat .cmb-repeatable-grouping select';

		// For each dropdown, build up our array of used values
		$( dropdowns ).each( function( key, value ) {
			usedMappings.push( $( value ).val() );
		});

		// For each of our mappings that we already have, remove them from all selects
		usedMappings.forEach( function( value ) {

			// But only do it if the value isn't one of our custom ones
			if ( 'custom_text_area' != value && 'custom' != value ) {

				// Remove all options from our dropdowns with the value
				$( dropdowns + ' option[value=' + value +']:not( :selected )' ).remove();
			}
		});
	}

	// Engage!
	$( that.init );

})( window, jQuery, window.CTCTBuilder );
